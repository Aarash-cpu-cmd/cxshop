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
    if len(cr.password) < 6:
        return JSONResponse({'ok': False, 'msg': '密码至少 6 位'}, 400)
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


@app.post('/api.php')
@limiter.limit('120/minute')
async def upstream_api(request: Request, act: str = ''):
    f = await request.form()
    uid = _cap(f.get('uid'), 20)
    key = _cap(f.get('key'), 64)
    if not act:
        act = _cap(f.get('act'), 32)
    u = api_auth(uid, key)
    if not u:
        return {'code': -2, 'msg': '密匙错误'}

    if act == 'getmoney':
        return {'code': 1, 'msg': '查询成功', 'user': u[1], 'name': u[1], 'money': float(u[2])}

    if act == 'getclass':
        with tx() as con, con.cursor(pymysql.cursors.DictCursor) as c:
            c.execute("SELECT pid,name,price,descr FROM products WHERE enabled=1 ORDER BY pid")
            items = [{'cid': r['pid'], 'kcid': r['pid'], 'name': r['name'],
                      'noun': '', 'price': float(r['price']), 'info': r['descr'],
                      'content': r['descr'], 'status': 1, 'miaoshua': 0,
                      'sort': r['pid']} for r in c.fetchall()]
        return {'code': 1, 'data': items}

    if act == 'add':
        school = _cap(f.get('school'), 255)
        stu_user = _cap(f.get('user'), 255)
        stu_pass = _cap(f.get('pass'), 255)
        platform = _to_int(f.get('platform'))
        kcids = _cap(f.get('kcid'), 4000)
        kcnames = _cap(f.get('kcname'), 4000)
        cookies = _cap(f.get('cookies'), 65536)
        if not school or not platform:
            return {'code': 0, 'msg': '所有项目不能为空'}
        names = kcnames.split(',') if kcnames else []
        ids = kcids.split(',') if kcids else []
        if len(ids) > 20 or len(names) > 20:
            return {'code': -1, 'msg': '单次最多提交 20 门课程'}
        with tx(atomic=True) as con, con.cursor() as c:
            c.execute("SELECT price,name FROM products WHERE pid=%s AND enabled=1", (platform,))
            p = c.fetchone()
            if not p:
                return {'code': -1, 'msg': '项目不存在或已下架'}
            courses = {i: {'clazzid': '', 'name': n_} for i, n_ in zip(ids, names)}
            n = max(len(ids), 1)
            total = float(p[0]) * n
            c.execute("UPDATE users SET balance=balance-%s WHERE uid=%s AND balance>=%s",
                      (total, u[0], total))
            if c.rowcount != 1:
                return {'code': -1, 'msg': f'余额不足{total}元'}
            c.execute("""INSERT INTO orders(uid,pid,school,stu_user,stu_pass,cookies,kcid,status,fees,created_at)
                         VALUES(%s,%s,%s,%s,%s,%s,%s,'queued',%s,%s)""",
                      (u[0], platform, school, stu_user, stu_pass, cookies,
                       json.dumps(courses, ensure_ascii=False), total, int(time.time())))
            return {'code': 1, 'msg': f'成功提交{n}门课程', 'data': {'oid': c.lastrowid}}

    if act == 'budan':
        oid = _to_int(f.get('id') or f.get('oid'))
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

    if act in ('chadanoid', 'get', 'up'):
        oid = _to_int(f.get('yid') or f.get('id') or f.get('oid'))
        if not oid:
            return {'code': -1, 'msg': '订单号无效'}
        with tx() as con, con.cursor() as c:
            c.execute("SELECT uid,status,worker_log FROM orders WHERE oid=%s", (oid,))
            r = c.fetchone()
            if not r or r[0] != u[0]:
                return {'code': -1, 'msg': '订单不存在'}
            return {'code': 1, 'msg': 'ok', 'data': {'oid': oid, 'status': r[1],
                    'dockstatus': {'queued': '0', 'running': 'cli_run', 'done': 'cli_done',
                                   'fail': 'cli_fail'}.get(r[1], '0'), 'log': (r[2] or '')[-500:]}}

    return {'code': -1, 'msg': f'unknown act: {act}'}


# ---------- admin ----------
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
