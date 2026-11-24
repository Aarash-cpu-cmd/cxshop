#!/usr/bin/env python3
# cx_api_bench.py — api.php 真实调用 benchmark + bug 挖掘
# 用法: python3 cx_api_bench.py [--base http://162.251.92.59:8082]
import sys, time, json, statistics, threading, urllib.request, urllib.parse, urllib.error, collections

BASE = 'http://162.251.92.59:8082'
UID = '5'
KEY = 'bench-b93a17e4d2c5f801a4e6d7c9'

def call(act, fields=None, uid=UID, key=KEY, timeout=15):
    """真实 POST /api.php?act=X，返回 (耗时, http状态, json|None, 错误)"""
    d = {'uid': uid, 'key': key}
    if fields:
        d.update(fields)
    body = urllib.parse.urlencode(d).encode()
    url = f'{BASE}/api.php?act={act}'
    req = urllib.request.Request(url, data=body)
    t0 = time.time()
    try:
        with urllib.request.urlopen(req, timeout=timeout) as r:
            dt = time.time() - t0
            return dt, r.status, json.loads(r.read()), None
    except urllib.error.HTTPError as e:
        dt = time.time() - t0
        try:
            j = json.loads(e.read())
        except Exception:
            j = None
        return dt, e.code, j, f'http{e.code}'
    except Exception as e:
        dt = time.time() - t0
        return dt, 0, None, str(e)[:60]

def bench(name, act, fields, n=30, workers=8):
    times, codes, errs = [], collections.Counter(), collections.Counter()
    def w():
        dt, st, j, e = call(act, fields)
        times.append(dt); codes[st] += 1
        if j is not None and j.get('code') not in (1,):
            errs[f"code{j.get('code')}:{(j.get('msg') or '')[:30]}"] += 1
        if e: errs[e] += 1
    ths = [threading.Thread(target=w) for _ in range(n)]
    t0 = time.time()
    for t in ths: t.start()
    for t in ths: t.join()
    wall = time.time() - t0
    times.sort()
    p50 = times[len(times)//2]; p95 = times[int(len(times)*.95)]
    print(f'{name:28s} n={n} w={workers}  p50={p50:.3f}s p95={p95:.3f}s '
          f'max={times[-1]:.3f}s  {n/wall:.1f}req/s  http={dict(codes)}  errs={dict(errs)}')
    return times

def race_overdraw(n=8):
    """并发 add 透支测试：余额刚好一单，打 n 个并发下单，看超扣几单"""
    import subprocess
    def sql(q):
        return subprocess.check_output(['docker', 'exec', 'cxshop-db-1', 'mysql',
                                        '-ushop', '-pshoppass', 'cxshop', '-N', '-e', q], text=True).strip()
    sql("UPDATE users SET balance=9.90 WHERE uid=5")
    pre = sql("SELECT COUNT(*) FROM orders WHERE uid=5 AND status='queued'")
    results = []
    def w(i):
        dt, st, j, e = call('add', {'school': 'bench大学', 'user': 'b001', 'pass': 'x',
                                  'platform': '1', 'kcid': f'{1000+i}', 'kcname': f'测试课{i}'})
        results.append((j or {}).get('code'))
    ths = [threading.Thread(target=w, args=(i,)) for i in range(n)]
    for t in ths: t.start()
    for t in ths: t.join()
    bal = sql("SELECT balance FROM users WHERE uid=5")
    post = sql("SELECT COUNT(*) FROM orders WHERE uid=5 AND status='queued'")
    ok = results.count(1)
    print(f'[RACE] balance=9.90 price=9.90 fired={n} -> 成功下单={ok} 余额={bal} '
          f'queued订单 {pre}->{post}  {"!!! BUG: 透支 !!!" if float(bal) < 0 or ok > 1 else "OK"}')
    sql("UPDATE orders SET status='cancelled' WHERE uid=5 AND status='queued'")
    sql("UPDATE users SET balance=200.00 WHERE uid=5")

def malformed():
    """畸形输入鲁棒性"""
    print('[EDGE] 畸形输入:')
    cases = [
        ('bad_key',      'getmoney', None, UID, 'wrong'),
        ('no_key',       'getmoney', None, UID, ''),
        ('no_uid',       'getmoney', None, '', KEY),
        ('unknown_act',  'wtf',      None, UID, KEY),
        ('oid_string',   'chadanoid', {'oid': 'abc'}, UID, KEY),
        ('oid_float',    'chadanoid', {'oid': '1.9'}, UID, KEY),
        ('oid_sqli',     'chadanoid', {'oid': "1' OR '1'='1"}, UID, KEY),
        ('oid_huge',     'chadanoid', {'oid': '9'*30}, UID, KEY),
        ('platform_neg', 'add', {'school': 'x', 'platform': '-1'}, UID, KEY),
        ('platform_abc', 'add', {'school': 'x', 'platform': 'abc'}, UID, KEY),
        ('platform_0',   'add', {'school': 'x', 'platform': '0'}, UID, KEY),
        ('budan_others', 'budan', {'oid': '1'}, UID, KEY),   # 越权碰 uid=2 的单
        ('chad_others',  'chadanoid', {'oid': '1'}, UID, KEY),
        ('huge_cookie',  'add', {'school': 'x', 'platform': '1', 'cookies': 'A'*200000}, UID, KEY),
    ]
    for name, act, f, u, k in cases:
        dt, st, j, e = call(act, f, u, k)
        resp = json.dumps(j, ensure_ascii=False)[:90] if j else f'ERR:{e} http:{st}'
        print(f'  {name:14s} act={act:10s} {dt:.2f}s -> {resp}')

def main():
    global BASE
    if '--base' in sys.argv:
        BASE = sys.argv[sys.argv.index('--base') + 1]
    print(f'== 基准: {BASE} uid={UID} ==')
    # 健康检查
    dt, st, j, e = call('getmoney')
    print(f'health: {dt:.3f}s {j}\n')
    if not j or j.get('code') != 1:
        print('健康检查失败，先造 bench 账号:')
        print("docker exec cxshop-db-1 mysql -ushop -pshoppass cxshop -e \"INSERT INTO users(username,pass_hash,balance,api_key,role,created_at) VALUES('benchtest','',200,'bench-b93a17e4d2c5f801a4e6d7c9','partner',unix_timestamp())\"")
        return
    bench('getmoney', 'getmoney', None, 40)
    bench('getclass', 'getclass', None, 40)
    bench('chadanoid (miss)', 'chadanoid', {'oid': '999999'}, 30)
    malformed()
    race_overdraw(8)
    print('\n== done ==')

if __name__ == '__main__':
    main()
