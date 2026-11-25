# -*- coding: utf-8 -*-
"""chaoxing-shop: 代刷门面前端 + 订单系统（自研，可开源）"""
import hashlib, json, os, secrets, time
from contextlib import contextmanager
import pymysql
from fastapi import FastAPI, Request
from fastapi.responses import HTMLResponse, JSONResponse
from fastapi.staticfiles import StaticFiles
from pydantic import BaseModel, Field
from slowapi import Limiter
from slowapi.errors import RateLimitExceeded
from slowapi.util import get_remote_address

DB = dict(host=os.environ.get('DB_HOST', '127.0.0.1'),
          port=int(os.environ.get('DB_PORT', '3306')),
          user=os.environ.get('DB_USER', 'shop'),
          password=os.environ.get('DB_PASS', 'shoppass'),
          database=os.environ.get('DB_NAME', 'cxshop'),
          charset='utf8mb4', autocommit=True)
ADMIN_USER = os.environ.get('ADMIN_USER', 'admin')
ADMIN_PASS = os.environ.get('ADMIN_PASS', 'admin123')

app = FastAPI(title='chaoxing-shop')
STATIC = os.path.join(os.path.dirname(__file__), '..', 'static')
limiter = Limiter(key_func=get_remote_address, default_limits=['300/minute'])
app.state.limiter = limiter


@app.exception_handler(RateLimitExceeded)
def _rate_limit(request, exc):
    return JSONResponse({'ok': False, 'code': -1, 'msg': '请求过于频繁，请稍后再试'}, 429)


@app.middleware('http')
async def _security(request: Request, call_next):
    if int(request.headers.get('content-length') or 0) > 1_000_000:
        return JSONResponse({'ok': False, 'code': -1, 'msg': '请求体过大'}, 413)
    r = await call_next(request)
    r.headers['X-Content-Type-Options'] = 'nosniff'
    r.headers['X-Frame-Options'] = 'DENY'
    return r


def db():
    for _ in range(30):
        try:
            return pymysql.connect(**DB)
        except Exception:
            time.sleep(1)
    return pymysql.connect(**DB)


@contextmanager
def tx(atomic=False):
    """连接上下文：退出时关闭连接；atomic=True 时整段作为事务提交/回滚"""
    conn = db()
    if atomic:
        conn.autocommit(False)
    try:
        yield conn
        if atomic:
            conn.commit()
    except Exception:
        if atomic:
            conn.rollback()
        raise
    finally:
        conn.close()


def _to_int(v, lo=1, hi=2**31 - 1):
    try:
        n = int(str(v).strip())
    except (TypeError, ValueError):
        return None
    return n if lo <= n <= hi else None


def _cap(s, n):
    return (s or '').strip()[:n]


def init_schema():
    with tx() as con, con.cursor() as c:
        c.execute("""CREATE TABLE IF NOT EXISTS users(
            uid INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(64) UNIQUE,
            pass_hash VARCHAR(64), balance DECIMAL(10,2) DEFAULT 0,
            api_key VARCHAR(32), role VARCHAR(16) DEFAULT 'buyer',
            created_at BIGINT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""")
        c.execute("""CREATE TABLE IF NOT EXISTS products(
            pid INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255),
            price DECIMAL(10,2), descr TEXT, enabled TINYINT DEFAULT 1)
            ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""")
        c.execute("""CREATE TABLE IF NOT EXISTS orders(
            oid INT AUTO_INCREMENT PRIMARY KEY, uid INT, pid INT,
            school VARCHAR(255), stu_user VARCHAR(255), stu_pass VARCHAR(255),
            cookies MEDIUMTEXT, kcid TEXT,
            status VARCHAR(32) DEFAULT 'queued', worker_log TEXT,
            fees DECIMAL(10,2) DEFAULT 0, created_at BIGINT,
            INDEX(status), INDEX(uid)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""")
        c.execute("""CREATE TABLE IF NOT EXISTS upstreams(
            hid INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(64),
            url VARCHAR(255), api_uid VARCHAR(64), api_key VARCHAR(128),
            pt VARCHAR(8) DEFAULT '29', enabled TINYINT DEFAULT 1)
            ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""")
        c.execute("""CREATE TABLE IF NOT EXISTS cards(
            code VARCHAR(32) PRIMARY KEY, amount DECIMAL(10,2),
            used_by INT DEFAULT NULL, used_at BIGINT DEFAULT NULL,
            created_at BIGINT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""")
        c.execute("SELECT COUNT(*) n FROM products")
        if c.fetchone()[0] == 0:
            c.execute("""INSERT INTO products(name,price,descr) VALUES
                ('整门课代刷（视频+作业+测验）', 9.90, '真实浏览器播放，干净抗回滚'),
                ('章节测验包过', 2.00, '字体解码自动答题'),
                ('课程扫描报告', 0.50, '任务点清单+完成度核对')""")
        c.execute("SELECT COUNT(*) n FROM users WHERE role='admin'")
        if c.fetchone()[0] == 0:
            c.execute("""INSERT INTO users(username,pass_hash,balance,api_key,role,created_at)
                VALUES(%s,%s,99999,%s,'admin',%s)""",
                (ADMIN_USER, ph(ADMIN_PASS), 'adm-'+secrets.token_hex(8), int(time.time())))


def ph(p):
    return hashlib.sha256(('cxshop$' + p).encode()).hexdigest()


def sess_get(tok):
    if not tok:
        return None
    with tx() as con, con.cursor() as c:
        c.execute("SELECT uid,username,balance,role,api_key FROM users WHERE api_key=%s", (tok,))
        return c.fetchone()


# ---------- auth ----------
class Creds(BaseModel):
    username: str
    password: str


@app.post('/api/register')
@limiter.limit('10/minute')
def register(request: Request, cr: Creds):
    import re
    uname = cr.username.strip()
    if not re.fullmatch(r'[\w\-]{3,32}', uname):
        return JSONResponse({'ok': False, 'msg': '用户名需 3-32 位字母/数字/下划线'}, 400)
    if not 6 <= len(cr.password) <= 128:
        return JSONResponse({'ok': False, 'msg': '密码需 6-128 位'}, 400)
    key = secrets.token_hex(16)
    try:
        with tx() as con, con.cursor() as c:
            c.execute("""INSERT INTO users(username,pass_hash,balance,api_key,created_at)
                         VALUES(%s,%s,0,%s,%s)""",
                      (uname, ph(cr.password), key, int(time.time())))
    except pymysql.err.IntegrityError:
        return JSONResponse({'ok': False, 'msg': '用户名已存在'}, 400)
    return {'ok': True, 'token': key}


@app.post('/api/login')
@limiter.limit('10/minute')
def login(request: Request, cr: Creds):
    with tx() as con, con.cursor() as c:
        c.execute("SELECT api_key FROM users WHERE username=%s AND pass_hash=%s",
                  (cr.username, ph(cr.password)))
        r = c.fetchone()
    if not r:
        return JSONResponse({'ok': False, 'msg': '用户名或密码错误'}, 401)
    return {'ok': True, 'token': r[0]}


# ---------- store ----------
@app.get('/api/me')
def me(request: Request):
    r = sess_get(request.headers.get('X-Token', ''))
    if not r:
        return JSONResponse({'ok': False}, 401)
    return {'ok': True, 'uid': r[0], 'username': r[1], 'balance': float(r[2]), 'role': r[3], 'api_key': r[4]}


@app.get('/api/products')
def products():
    with tx() as con, con.cursor(pymysql.cursors.DictCursor) as c:
        c.execute("SELECT pid,name,price,descr FROM products WHERE enabled=1 ORDER BY pid")
        return {'ok': True, 'items': c.fetchall()}


class OrderIn(BaseModel):
    pid: int
    school: str = Field('', max_length=255)
    stu_user: str = Field('', max_length=255)
    stu_pass: str = Field('', max_length=255)
    cookies: str = Field('', max_length=65536)
    courses: dict = {}


@app.post('/api/order')
def order(o: OrderIn, request: Request):
    u = sess_get(request.headers.get('X-Token', ''))
    if not u:
        return JSONResponse({'ok': False}, 401)
    with tx(atomic=True) as con, con.cursor() as c:
        c.execute("SELECT price FROM products WHERE pid=%s AND enabled=1", (o.pid,))
        p = c.fetchone()
        if not p:
            return JSONResponse({'ok': False, 'msg': '商品不存在'}, 400)
        c.execute("UPDATE users SET balance=balance-%s WHERE uid=%s AND balance>=%s",
                  (p[0], u[0], p[0]))
        if c.rowcount != 1:
            return JSONResponse({'ok': False, 'msg': '余额不足'}, 402)
        c.execute("""INSERT INTO orders(uid,pid,school,stu_user,stu_pass,cookies,kcid,status,fees,created_at)
                     VALUES(%s,%s,%s,%s,%s,%s,%s,'queued',%s,%s)""",
                  (u[0], o.pid, o.school, o.stu_user, o.stu_pass,
                   o.cookies, json.dumps(o.courses, ensure_ascii=False), p[0], int(time.time())))
        return {'ok': True, 'oid': c.lastrowid}


@app.get('/api/orders')
def orders(request: Request):
    u = sess_get(request.headers.get('X-Token', ''))
    if not u:
        return JSONResponse({'ok': False}, 401)
    with tx() as con, con.cursor(pymysql.cursors.DictCursor) as c:
        c.execute("""SELECT o.oid,o.status,o.fees,o.created_at,p.name FROM orders o
                     JOIN products p ON p.pid=o.pid WHERE o.uid=%s ORDER BY oid DESC LIMIT 100""", (u[0],))
        return {'ok': True, 'items': c.fetchall()}


@app.get('/api/order/{oid}')
def order_detail(oid: int, request: Request):
    u = sess_get(request.headers.get('X-Token', ''))
    if not u:
        return JSONResponse({'ok': False}, 401)
    with tx() as con, con.cursor(pymysql.cursors.DictCursor) as c:
        c.execute("SELECT oid,status,worker_log,created_at FROM orders WHERE oid=%s AND uid=%s", (oid, u[0]))
        r = c.fetchone()
        return {'ok': True, 'item': r} if r else JSONResponse({'ok': False}, 404)


# ---------- recharge (卡密充值) ----------
class RechargeIn(BaseModel):
    code: str = Field('', max_length=64)


@app.post('/api/recharge')
@limiter.limit('30/minute')
def recharge(request: Request, r: RechargeIn):
    u = sess_get(request.headers.get('X-Token', ''))
    if not u:
        return JSONResponse({'ok': False}, 401)
    code = r.code.strip().upper()
    if not code:
        return JSONResponse({'ok': False, 'msg': '卡密不能为空'}, 400)
    with tx(atomic=True) as con, con.cursor() as c:
        c.execute("UPDATE cards SET used_by=%s,used_at=%s WHERE code=%s AND used_by IS NULL",
                  (u[0], int(time.time()), code))
        if c.rowcount != 1:
            return JSONResponse({'ok': False, 'msg': '卡密无效或已被使用'}, 400)
        c.execute("SELECT amount FROM cards WHERE code=%s", (code,))
        amt = float(c.fetchone()[0])
        c.execute("UPDATE users SET balance=balance+%s WHERE uid=%s", (amt, u[0]))
        return {'ok': True, 'amount': amt}


# ---------- upstream api (FreeDom/29网课-compatible api.php) ----------
# 下游店铺按 uid+key 调 act=getclass/getmoney/add/budan/chadanoid
def api_auth(uid, key):
    uid = _to_int(uid)
    if not uid or not key:
        return None
    with tx() as con, con.cursor() as c:
        c.execute("SELECT uid,username,balance,api_key FROM users WHERE uid=%s", (uid,))
        r = c.fetchone()
        if r and r[3] and r[3] != '0' and secrets.compare_digest(r[3], key):
            return r
    return None


@app.api_route('/api.php', methods=['GET', 'POST'])
@limiter.limit('120/minute')
async def upstream_api(request: Request, act: str = ''):
    if request.method == 'POST':
        f = await request.form()
    else:
        f = {}
    def _fix(v):
        # starlette decodes urlencoded forms as latin-1; repair utf-8 mojibake
        if isinstance(v, str):
            try:
                return v.encode('latin-1').decode('utf-8')
            except (UnicodeEncodeError, UnicodeDecodeError):
                return v
        return v

    def _p(name):
        v = f.get(name)
        if v is None:
            v = request.query_params.get(name)
        return _fix(v)
    uid = _cap(_p('uid'), 20)
    key = _cap(_p('key'), 64)
    if not act:
        act = _cap(_p('act'), 32)
    u = api_auth(uid, key)
    if not u:
        return {'code': -2, 'msg': '密匙错误'}

    if act == 'getmoney':
        return {'code': 1, 'msg': '查询成功', 'user': u[1], 'name': u[1], 'money': float(u[2])}

    if act == 'getclass':
        only_cid = _to_int(_p('cid'))
        with tx() as con, con.cursor(pymysql.cursors.DictCursor) as c:
            if only_cid:
                c.execute("SELECT pid,name,price,descr FROM products WHERE enabled=1 AND pid=%s ORDER BY pid", (only_cid,))
            else:
                c.execute("SELECT pid,name,price,descr FROM products WHERE enabled=1 ORDER BY pid")
            items = [{'sort': r['pid'], 'cid': r['pid'], 'kcid': r['pid'], 'fenlei': 1,
                      'name': r['name'], 'noun': str(r['pid']), 'price': float(r['price']),
                      'content': r['descr'] or '', 'info': r['descr'] or '',
                      'status': 1, 'miaoshua': 0, 'category_name': '网课'} for r in c.fetchall()]
        return {'code': 1, 'data': items}

    if act == 'get':
        school = _cap(_p('school'), 255)
        stu_user = _cap(_p('user'), 255)
        stu_pass = _cap(_p('pass'), 255)
        if not school or not stu_user or not stu_pass:
            return {'code': 0, 'msg': '请检查项目是否完整'}
        try:
            from app.cxquery import query_courses
        except ImportError:
            from cxquery import query_courses
        result, _err = query_courses(school, stu_user, stu_pass)
        return result

    if act == 'add':
        school = _cap(_p('school'), 255)
        stu_user = _cap(_p('user'), 255)
        stu_pass = _cap(_p('pass'), 255)
        platform = _to_int(_p('platform'))
        kcids = _cap(_p('kcid'), 4000)
        kcnames = _cap(_p('kcname'), 4000)
        cookies = _cap(_p('cookies'), 65536)
        if not school or not stu_user or not stu_pass or not platform or not kcnames:
            return {'code': 0, 'msg': '所有项目不能为空'}
        names = kcnames.split(',') if kcnames else []
        ids = kcids.split(',') if kcids else []
        if len(ids) > 20 or len(names) > 20:
            return {'code': -1, 'msg': '单次最多提交 20 门课程'}
        if len(ids) < len(names):
            ids = ids + [''] * (len(names) - len(ids))
        with tx(atomic=True) as con, con.cursor() as c:
            c.execute("SELECT price,name FROM products WHERE pid=%s AND enabled=1", (platform,))
            p = c.fetchone()
            if not p:
                return {'code': -1, 'msg': '商品已下架，下单失败！'}
            c.execute("""SELECT oid FROM orders WHERE uid=%s AND pid=%s AND school=%s AND stu_user=%s
                         AND created_at>%s LIMIT 1""",
                      (u[0], platform, school, stu_user, int(time.time()) - 100 * 86400))
            if c.fetchone():
                return {'code': -1, 'msg': '存在100天内的重复订单！请勿重复提交！'}
            courses = {i: {'clazzid': '', 'name': n_} for i, n_ in zip(ids, names)}
            n = max(len(names), 1)
            total = float(p[0]) * n
            c.execute("UPDATE users SET balance=balance-%s WHERE uid=%s AND balance>=%s",
                      (total, u[0], total))
            if c.rowcount != 1:
                return {'code': -1, 'msg': '余额不足以本次提交'}
            c.execute("""INSERT INTO orders(uid,pid,school,stu_user,stu_pass,cookies,kcid,status,fees,created_at)
                         VALUES(%s,%s,%s,%s,%s,%s,%s,'queued',%s,%s)""",
                      (u[0], platform, school, stu_user, stu_pass, cookies,
                       json.dumps(courses, ensure_ascii=False), total, int(time.time())))
            oid = c.lastrowid
            return {'code': 0, 'msg': '提交成功', 'status': 0, 'message': '提交成功', 'id': oid}

    if act == 'budan':
        oid = _to_int(_p('id') or _p('oid'))
        if not oid:
            return {'code': -1, 'msg': '订单号无效'}
        with tx() as con, con.cursor() as c:
            c.execute("SELECT uid,status FROM orders WHERE oid=%s", (oid,))
            r = c.fetchone()
            if not r or r[0] != u[0]:
                return {'code': -1, 'msg': '订单不存在'}
            if r[1] == 'running':
                return {'code': -1, 'msg': '订单处理中'}
            c.execute("UPDATE orders SET status='queued', worker_log=%s WHERE oid=%s",
                      ('re-queued by API budan', oid))
            return {'code': 1, 'msg': '已重新下单'}

    if act in ('chadanoid', 'up'):
        oid = _to_int(_p('oid') or _p('yid') or _p('id'))
        username = _cap(_p('username'), 255)
        school_q = _cap(_p('school'), 255)
        if not oid and not username and not school_q:
            return {'code': -1, 'msg': '至少需要一个有效参数'}
        status_map = {'queued': '排队中', 'running': '刷课中', 'done': '已完成', 'fail': '异常'}
        with tx() as con, con.cursor(pymysql.cursors.DictCursor) as c:
            sql = "SELECT * FROM orders WHERE uid=%s"
            args = [u[0]]
            if oid:
                sql += " AND oid=%s"
                args.append(oid)
            if username:
                sql += " AND stu_user=%s"
                args.append(username)
            if school_q:
                sql += " AND school LIKE %s"
                args.append(f'%{school_q}%')
            sql += " ORDER BY oid DESC LIMIT 20"
            c.execute(sql, args)
            rows = c.fetchall()
            if not rows:
                return {'code': -1, 'msg': '未查到订单信息'}
            data = []
            for r in rows:
                kc = r.get('kcid') or ''
                kcname = ''
                try:
                    cc = json.loads(kc)
                    kcname = ','.join(v.get('name', '') for v in cc.values() if isinstance(v, dict))
                except Exception:
                    kcname = kc[:100]
                st = status_map.get(r['status'], r['status'])
                log = (r.get('worker_log') or '')[-200:]
                proc = '100%' if r['status'] == 'done' else ('0%' if r['status'] == 'queued' else '')
                data.append({
                    'id': r['oid'], 'ptname': r.get('pid'), 'school': r['school'],
                    'name': '', 'user': r['stu_user'], 'kcname': kcname,
                    'addtime': time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(r['created_at'] or 0)),
                    'courseStartTime': '', 'courseEndTime': '',
                    'examStartTime': '', 'examEndTime': '',
                    'status': st, 'process': proc, 'remarks': log,
                })
            return {'code': 1, 'data': data}

    return {'code': -1, 'msg': f'unknown act: {act}'}


# ---------- admin ----------
def _admin(request):
    u = sess_get(request.headers.get('X-Token', ''))
    return u if u and u[3] == 'admin' else None


@app.get('/api/admin/orders')
def admin_orders(request: Request):
    u = sess_get(request.headers.get('X-Token', ''))
    if not u or u[3] != 'admin':
        return JSONResponse({'ok': False}, 403)
    with tx() as con, con.cursor(pymysql.cursors.DictCursor) as c:
        c.execute("""SELECT o.oid,o.uid,u.username,o.status,o.fees,o.created_at,p.name
                     FROM orders o JOIN products p ON p.pid=o.pid JOIN users u ON u.uid=o.uid
                     ORDER BY oid DESC LIMIT 200""")
        return {'ok': True, 'items': c.fetchall()}


class CardGen(BaseModel):
    amount: float
    count: int = 1


@app.post('/api/admin/cards')
def admin_cards_gen(request: Request, g: CardGen):
    if not _admin(request):
        return JSONResponse({'ok': False}, 403)
    if not 0 < g.amount <= 100000 or not 1 <= g.count <= 200:
        return JSONResponse({'ok': False, 'msg': '参数无效'}, 400)
    codes = ['CX' + secrets.token_hex(4).upper() + '-' + secrets.token_hex(2).upper()
             for _ in range(g.count)]
    with tx(atomic=True) as con, con.cursor() as c:
        c.executemany("INSERT INTO cards(code,amount,created_at) VALUES(%s,%s,%s)",
                      [(k, g.amount, int(time.time())) for k in codes])
    return {'ok': True, 'codes': codes}


@app.get('/api/admin/cards')
def admin_cards_list(request: Request):
    if not _admin(request):
        return JSONResponse({'ok': False}, 403)
    with tx() as con, con.cursor(pymysql.cursors.DictCursor) as c:
        c.execute("""SELECT c.code,c.amount,c.used_by,c.used_at,c.created_at,u.username
                     FROM cards c LEFT JOIN users u ON u.uid=c.used_by
                     ORDER BY c.created_at DESC LIMIT 200""")
        return {'ok': True, 'items': c.fetchall()}


class CreditIn(BaseModel):
    uid: int
    amount: float


@app.post('/api/admin/credit')
def admin_credit(request: Request, ci: CreditIn):
    if not _admin(request):
        return JSONResponse({'ok': False}, 403)
    if not -100000 < ci.amount < 100000 or ci.amount == 0:
        return JSONResponse({'ok': False, 'msg': '金额无效'}, 400)
    with tx(atomic=True) as con, con.cursor() as c:
        c.execute("UPDATE users SET balance=balance+%s WHERE uid=%s", (ci.amount, ci.uid))
        if c.rowcount != 1:
            return JSONResponse({'ok': False, 'msg': '用户不存在'}, 404)
    return {'ok': True}


# ---------- pages ----------
@app.get('/', response_class=HTMLResponse)
def page_store():
    return open(os.path.join(STATIC, 'index.html'), encoding='utf8').read()


@app.get('/admin', response_class=HTMLResponse)
def page_admin():
    return open(os.path.join(STATIC, 'admin.html'), encoding='utf8').read()


@app.on_event('startup')
def _init():
    init_schema()


app.mount('/static', StaticFiles(directory=STATIC), name='static')
