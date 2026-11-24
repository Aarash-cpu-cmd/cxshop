# -*- coding: utf-8 -*-
"""chaoxing-shop: 代刷门面前端 + 订单系统（自研，可开源）"""
import hashlib, json, os, secrets, time
import pymysql
from fastapi import FastAPI, Request
from fastapi.responses import HTMLResponse, JSONResponse
from fastapi.staticfiles import StaticFiles
from pydantic import BaseModel

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


def db():
    for _ in range(30):
        try:
            return pymysql.connect(**DB)
        except Exception:
            time.sleep(1)
    return pymysql.connect(**DB)


def init_schema():
    with db().cursor() as c:
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
    db().close()


def ph(p):
    return hashlib.sha256(('cxshop$' + p).encode()).hexdigest()


def sess_get(tok):
    with db().cursor() as c:
        c.execute("SELECT uid,username,balance,role,api_key FROM users WHERE api_key=%s", (tok,))
        return c.fetchone()


# ---------- auth ----------
class Creds(BaseModel):
    username: str
    password: str


@app.post('/api/register')
def register(cr: Creds):
    key = secrets.token_hex(16)
    try:
        with db().cursor() as c:
            c.execute("""INSERT INTO users(username,pass_hash,balance,api_key,created_at)
                         VALUES(%s,%s,0,%s,%s)""",
                      (cr.username, ph(cr.password), key, int(time.time())))
    except pymysql.err.IntegrityError:
        return JSONResponse({'ok': False, 'msg': '用户名已存在'}, 400)
    return {'ok': True, 'token': key}


@app.post('/api/login')
def login(cr: Creds):
    with db().cursor() as c:
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
    with db().cursor(pymysql.cursors.DictCursor) as c:
        c.execute("SELECT pid,name,price,descr FROM products WHERE enabled=1 ORDER BY pid")
        return {'ok': True, 'items': c.fetchall()}


class OrderIn(BaseModel):
    pid: int
    school: str = ''
    stu_user: str = ''
    stu_pass: str = ''
    cookies: str = ''
    courses: dict = {}


@app.post('/api/order')
def order(o: OrderIn, request: Request):
    u = sess_get(request.headers.get('X-Token', ''))
    if not u:
        return JSONResponse({'ok': False}, 401)
    with db().cursor() as c:
        c.execute("SELECT price FROM products WHERE pid=%s AND enabled=1", (o.pid,))
        p = c.fetchone()
        if not p:
            return JSONResponse({'ok': False, 'msg': '商品不存在'}, 400)
        if u[2] < p[0]:
            return JSONResponse({'ok': False, 'msg': '余额不足'}, 402)
        c.execute("UPDATE users SET balance=balance-%s WHERE uid=%s", (p[0], u[0]))
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
    with db().cursor(pymysql.cursors.DictCursor) as c:
        c.execute("""SELECT o.oid,o.status,o.fees,o.created_at,p.name FROM orders o
                     JOIN products p ON p.pid=o.pid WHERE o.uid=%s ORDER BY oid DESC LIMIT 100""", (u[0],))
        return {'ok': True, 'items': c.fetchall()}


@app.get('/api/order/{oid}')
def order_detail(oid: int, request: Request):
    u = sess_get(request.headers.get('X-Token', ''))
    if not u:
        return JSONResponse({'ok': False}, 401)
    with db().cursor(pymysql.cursors.DictCursor) as c:
        c.execute("SELECT oid,status,worker_log,created_at FROM orders WHERE oid=%s AND uid=%s", (oid, u[0]))
        r = c.fetchone()
        return {'ok': True, 'item': r} if r else JSONResponse({'ok': False}, 404)


# ---------- upstream api (FreeDom/29网课-compatible api.php) ----------
# 下游店铺按 uid+key 调 act=getclass/getmoney/add/budan/chadanoid
def api_auth(uid, key):
    if not uid or not key:
        return None
    with db().cursor() as c:
        c.execute("SELECT uid,username,balance,api_key FROM users WHERE uid=%s", (uid,))
        r = c.fetchone()
        if r and r[3] and r[3] != '0' and r[3] == key:
            return r
    return None


@app.post('/api.php')
async def upstream_api(request: Request, act: str = ''):
    f = await request.form()
    uid = (f.get('uid') or '').strip()
    key = (f.get('key') or '').strip()
    if not act:
        act = (f.get('act') or '').strip()
    u = api_auth(uid, key)
    if not u:
        return {'code': -2, 'msg': '密匙错误'}

    if act == 'getmoney':
        return {'code': 1, 'msg': '查询成功', 'user': u[1], 'name': u[1], 'money': float(u[2])}

    if act == 'getclass':
        with db().cursor(pymysql.cursors.DictCursor) as c:
            c.execute("SELECT pid,name,price,descr FROM products WHERE enabled=1 ORDER BY pid")
            items = [{'cid': r['pid'], 'kcid': r['pid'], 'name': r['name'],
                      'noun': '', 'price': float(r['price']), 'info': r['descr'],
                      'content': r['descr'], 'status': 1, 'miaoshua': 0,
                      'sort': r['pid']} for r in c.fetchall()]
        return {'code': 1, 'data': items}

    if act == 'add':
        school = (f.get('school') or '').strip()
        stu_user = (f.get('user') or '').strip()
        stu_pass = (f.get('pass') or '').strip()
        platform = (f.get('platform') or '').strip()
        kcids = (f.get('kcid') or '').strip()
        kcnames = (f.get('kcname') or '').strip()
        cookies = (f.get('cookies') or '').strip()
        if not school or not platform:
            return {'code': 0, 'msg': '所有项目不能为空'}
        with db().cursor() as c:
            c.execute("SELECT price,name FROM products WHERE pid=%s AND enabled=1", (platform,))
            p = c.fetchone()
            if not p:
                return {'code': -1, 'msg': '项目不存在或已下架'}
            if u[2] < p[0]:
                return {'code': -1, 'msg': f'你的余额不足{p[0]}元，请尽快充值'}
            names = kcnames.split(',') if kcnames else []
            ids = kcids.split(',') if kcids else []
            courses = {i: {'clazzid': '', 'name': n} for i, n in zip(ids, names)}
            n = max(len(ids), 1)
            total = float(p[0]) * n
            if float(u[2]) < total:
                return {'code': -1, 'msg': f'余额不足{total}元'}
            c.execute("UPDATE users SET balance=balance-%s WHERE uid=%s", (total, u[0]))
            c.execute("""INSERT INTO orders(uid,pid,school,stu_user,stu_pass,cookies,kcid,status,fees,created_at)
                         VALUES(%s,%s,%s,%s,%s,%s,%s,'queued',%s,%s)""",
                      (u[0], platform, school, stu_user, stu_pass, cookies,
                       json.dumps(courses, ensure_ascii=False), total, int(time.time())))
            return {'code': 1, 'msg': f'成功提交{n}门课程', 'data': {'oid': c.lastrowid}}

    if act == 'budan':
        oid = (f.get('id') or f.get('oid') or '').strip()
        with db().cursor() as c:
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
        oid = (f.get('yid') or f.get('id') or f.get('oid') or '').strip()
        with db().cursor() as c:
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
    with db().cursor(pymysql.cursors.DictCursor) as c:
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
