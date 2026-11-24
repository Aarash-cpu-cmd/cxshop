# -*- coding: utf-8 -*-
"""cxshop dispatcher: 轮询 orders(status='queued') → fd-worker 容器执行 → 回写。
worker 容器从 proxy_pool 取一个 sticky 代理（可选，PROXY_API 为空则直连）。"""
import json, os, subprocess, time
import pymysql
import urllib.request

DB = dict(host=os.environ.get('DB_HOST', 'db'),
          port=int(os.environ.get('DB_PORT', '3306')),
          user=os.environ.get('DB_USER', 'shop'),
          password=os.environ.get('DB_PASS', 'shoppass'),
          database=os.environ.get('DB_NAME', 'cxshop'),
          charset='utf8mb4', autocommit=True)
JOBS_DIR = os.environ.get('JOBS_DIR', '/jobdata')
# Named docker volume shared between dispatcher and spawned workers.
# No host paths: works identically on any server/docker project dir.
JOB_VOLUME = os.environ.get('JOB_VOLUME', 'cxshop_jobs_data')
# Optional host checkout of the toolkit to mount over /app (dev override);
# normally the worker image already has the toolkit baked in.
TOOLKIT_HOST = os.environ.get('TOOLKIT_HOST', '')
IMAGE = os.environ.get('WORKER_IMAGE', 'fd-worker')
PROXY_API = os.environ.get('PROXY_API', 'http://proxypool:5010/pop')
POLL = int(os.environ.get('POLL_SECONDS', '5'))


def db():
    return pymysql.connect(**DB)


def get_proxy():
    if not PROXY_API:
        return None
    try:
        d = json.loads(urllib.request.urlopen(PROXY_API, timeout=5).read())
        p = d.get('proxy')
        return f'http://{p}' if p else None
    except Exception:
        return None


def claim(conn):
    with conn.cursor(pymysql.cursors.DictCursor) as c:
        c.execute("SELECT oid,pid,school,stu_user,stu_pass,cookies,kcid "
                  "FROM orders WHERE status='queued' ORDER BY oid LIMIT 1")
        row = c.fetchone()
        if row:
            c.execute("UPDATE orders SET status='running' WHERE oid=%s", (row['oid'],))
    return row


def _docker_base(oid, proxy):
    # Mount the shared jobs volume at /job; each order uses /job/<oid>.
    cmd = ['docker', 'run', '--rm', '--network', 'host',
           '-v', f'{JOB_VOLUME}:/job', '-w', '/app']
    if TOOLKIT_HOST:
        cmd += ['-v', f'{TOOLKIT_HOST}:/app']
    if proxy:
        cmd += ['-e', f'HTTPS_PROXY={proxy}', '-e', f'HTTP_PROXY={proxy}']
    return cmd + [IMAGE]


def run_cli(oid, proxy, *args):
    r = subprocess.run(_docker_base(oid, proxy) +
                       ['python', '-m', 'chaoxing_grind.cli'] + list(args),
                       capture_output=True, text=True, timeout=1800)
    return r.returncode, (r.stdout or '') + (r.stderr or '')


def run_grind(oid, proxy):
    prox = f' --proxy-server={proxy}' if proxy else ''
    cmd = _docker_base(oid, proxy) + ['sh', '-c',
        f'chromium --headless=new --no-sandbox --remote-debugging-port=9222{prox} '
        '--user-data-dir=/tmp/prof about:blank & sleep 4 && '
        f'python scripts/inject_cookies_cdp.py --cookies /job/{oid}/cookies.json '
        '--port 9222 && python -m chaoxing_grind.cli grind --jobs '
        f'/job/{oid}/jobs.json --port 9222 --tabs 1 --done /job/{oid}/grind_done.json']
    r = subprocess.run(cmd, capture_output=True, text=True, timeout=1800)
    return (r.stdout or '') + (r.stderr or '')


def process_order(conn, o):
    oid = o['oid']
    d = f'{JOBS_DIR}/{oid}'
    os.makedirs(d, exist_ok=True)
    log = []
    ck = (o['cookies'] or '').strip()
    if not ck:
        return 'fail', 'order has no cookies attached\n'
    open(f'{d}/cookies.json', 'w').write(ck)
    try:
        courses = json.loads(o['kcid'] or '{}')
    except Exception:
        courses = {}
    json.dump(courses, open(f'{d}/courses.json', 'w'))

    proxy = get_proxy()
    if proxy:
        log.append(f'proxy: {proxy}\n')
    args = ('scan', '--cookies', f'/job/{oid}/cookies.json',
            '--courses', f'/job/{oid}/courses.json')
    rc, out = run_cli(oid, proxy, *args)
    if rc != 0 and proxy:
        log.append('proxy failed, retrying direct\n')
        rc, out = run_cli(oid, None, *args)
    log.append('$ cx scan\n' + out)
    return ('done' if rc == 0 else 'fail'), ''.join(log)[-6000:]


def main():
    print('cxshop dispatcher up', flush=True)
    while True:
        try:
            conn = db()
            o = claim(conn)
            if o:
                print(f"[order {o['oid']}] claimed", flush=True)
                try:
                    st, log = process_order(conn, o)
                except Exception as e:
                    st, log = 'fail', f'exc: {e}'
                with conn.cursor() as c:
                    c.execute("UPDATE orders SET status=%s, worker_log=%s WHERE oid=%s",
                              (st, log, o['oid']))
                print(f"[order {o['oid']}] -> {st}", flush=True)
            conn.close()
        except Exception as e:
            print('poll err:', e, flush=True)
        time.sleep(POLL)


if __name__ == '__main__':
    main()
