# -*- coding: utf-8 -*-
"""
-------------------------------------------------
   File Name：     cxhttps.py
   Description :   HTTPS-capable 免费代理源（geonode + proxifly 的 https 协议列表）
   Author :        Devin
   date：          2026/11/25
-------------------------------------------------
"""
__author__ = 'Devin'

from fetcher.baseFetcher import BaseFetcher
from handler.logHandler import LogHandler
from util.webRequest import WebRequest

logger = LogHandler("fetcher")


class CxHttpsFetcher(BaseFetcher):
    """HTTPS (CONNECT-capable) proxies from geonode + proxifly https lists"""

    name = "cxhttps"
    url = "https://geonode.com/"
    enabled = True

    def fetch(self):
        proxies = []
        try:
            for page in (1, 2):
                u = ("https://proxylist.geonode.com/api/proxy-list?protocols=https"
                     "&limit=500&page=%d&sort_by=lastChecked&sort_type=desc" % page)
                r = WebRequest().get(u, timeout=15, retry_time=1)
                for item in r.json.get("data", []):
                    ip, port = item.get("ip"), item.get("port")
                    if ip and port:
                        proxies.append("%s:%s" % (ip, port))
        except Exception as e:
            logger.error("ProxyFetch - cxhttps/geonode: %s" % e)
        try:
            u = "https://cdn.jsdelivr.net/gh/proxifly/free-proxy-list@main/proxies/protocols/https/data.json"
            r = WebRequest().get(u, timeout=15, retry_time=1)
            for each in r.json:
                if each.get("protocol") == "https" and each.get("ip") and each.get("port"):
                    proxies.append("%s:%s" % (each["ip"], each["port"]))
        except Exception as e:
            logger.error("ProxyFetch - cxhttps/proxifly: %s" % e)
        for proxy in self.yieldUniqueProxies(proxies):
            yield proxy


if __name__ == '__main__':
    for proxy in CxHttpsFetcher().fetch():
        print(proxy)
