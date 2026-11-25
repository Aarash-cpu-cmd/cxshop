"""查课: chaoxing password login + course list scrape.

Implements the upstream-supplier `act=get` workflow: given school/user/pass,
log into chaoxing and return the student's course list so downstream stores
can pick exact (kcid, kcname) pairs before ordering.
"""
import re
import requests

UA = ('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
      '(KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')


def cx_login(user: str, password: str):
    """Password login -> requests.Session with chaoxing cookies, or None."""
    s = requests.Session()
    s.headers.update({'User-Agent': UA})
    try:
        r = s.post('https://passport2.chaoxing.com/v11/loginregister',
                   data={'uname': user, 'code': password, 'loginType': '1',
                         'roleSelect': 'true', 'doubleFactorLogin': '0',
                         'independentId': '0', 'independentNameId': '0'},
                   timeout=15, verify=True)
        j = r.json()
        if not j.get('status'):
            return None, j.get('mes') or '登录失败'
    except Exception as e:
        return None, f'登录请求异常: {e}'
    if not s.cookies.get('UID') and not s.cookies.get('_uid'):
        return None, '登录失败: 未获取到会话'
    return s, None


def cx_courses(s: requests.Session):
    """Scrape course list from the interaction page HTML.
    Returns list of {'id': kcid, 'name': kcname, 'courseid', 'clazzid'}."""
    try:
        r = s.get('https://mooc2-ans.chaoxing.com/mooc2-ans/visit/interaction?moocDomain=mooc',
                  timeout=20, headers={'User-Agent': UA})
        html = r.text
    except Exception as e:
        return None, f'课程列表请求异常: {e}'
    if 'interaction' not in r.url and 'login' in r.url.lower():
        return None, '会话失效'
    out = []
    seen = set()
    # course items carry onclick="...courseid=XXX&clazzid=YYY..." plus a title
    for m in re.finditer(
            r'onclick="[^"]*courseid[=:](\d+)[^"]*clazzid[=:](\d+)[^"]*"[^>]*>(.*?)</div>',
            html, re.S):
        courseid, clazzid, blob = m.group(1), m.group(2), m.group(3)
        t = re.search(r'title="([^"]+)"', blob)
        name = t.group(1) if t else re.sub(r'<[^>]+>', '', blob).strip()[:60]
        keyv = f'{courseid}_{clazzid}'
        if name and keyv not in seen:
            seen.add(keyv)
            out.append({'id': keyv, 'name': name, 'courseid': courseid, 'clazzid': clazzid})
    if not out:
        # fallback: looser parse of courseid/clazzid pairs + nearest title attr
        for m in re.finditer(r'courseid[=:](\d+)[^"\']*clazzid[=:](\d+)', html):
            keyv = f'{m.group(1)}_{m.group(2)}'
            if keyv not in seen:
                seen.add(keyv)
                out.append({'id': keyv, 'name': keyv, 'courseid': m.group(1), 'clazzid': m.group(2)})
    return out, None


def query_courses(school: str, user: str, password: str):
    """Full 查课 pipeline. Returns (result_dict, http-friendly error or None)."""
    s, err = cx_login(user, password)
    if err:
        return {'code': -1, 'msg': f'登录失败:{err}'}, err
    courses, err2 = cx_courses(s)
    if courses is None:
        return {'code': -1, 'msg': f'查课失败:{err2}'}, err2
    if not courses:
        return {'code': -1, 'msg': '该账号没有查到课程'}, 'empty'
    data = [{'id': c['id'], 'name': c['name']} for c in courses]
    return {'code': 1, 'msg': '查询成功', 'data': data}, None
