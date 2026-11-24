<div align="center">

# ⚡ CXSHOP

### 网课代刷门面 + 订单调度系统

[![Python](https://img.shields.io/badge/Python-3.11-3776AB?logo=python&logoColor=white)](https://python.org)
[![FastAPI](https://img.shields.io/badge/FastAPI-uvicorn-009688?logo=fastapi&logoColor=white)](https://fastapi.tiangolo.com)
[![Docker](https://img.shields.io/badge/Docker-compose-2496ED?logo=docker&logoColor=white)](https://www.docker.com)
[![License](https://img.shields.io/badge/License-MIT-yellow)](LICENSE)
[![Worker](https://img.shields.io/badge/Engine-chaoxing--grind-8b5cf6)](https://github.com/Aarash-cpu-cmd/chaoxing-grind)

**自研 · 全链路 · 真实播放引擎 — 一条 `docker compose up` 起全栈**

</div>

---

## 🏗 架构

```
买家 ──▶ CXSHOP 门面(FastAPI) ──▶ orders 队列 ──▶ dispatcher
                                                  │
              ┌── fd-worker 容器 ─────────────────┤
              │   headless Chromium               │
              │   + chaoxing-grind 工具包         │
              │   真实播放 · 抗回滚               │
              └───────────────────────────────────┘
                          ▲
              proxypool ──┘ 每单 sticky 出口代理(可选)
```

| 模块 | 说明 |
|---|---|
| 🛍 **shop** | 注册/登录/商城/订单进度/管理后台 — 原生单页前端零构建 |
| 📮 **api.php** | FreeDom/29网课兼容上游接口，下游店铺直接对接 |
| 🔀 **dispatcher** | 轮询队列 → 起 worker 容器 → 回写状态与日志 |
| 🎬 **fd-worker** | headless Chromium + chaoxing-grind，真实播放刷课 |
| 🌐 **proxypool** | vendored [jhao104/proxy_pool](https://github.com/jhao104/proxy_pool)，代理采集+验证+REST |

## 🚀 快速开始

```bash
git clone https://github.com/Aarash-cpu-cmd/cxshop.git && cd cxshop
docker compose up -d
```

- 🛍 商城：`http://localhost:8082`
- 👑 后台：`http://localhost:8082/admin`（admin / admin123，`ADMIN_PASS` 可改）
- worker 镜像 `fd-worker` 用 [chaoxing-grind](https://github.com/Aarash-cpu-cmd/chaoxing-grind) 的 `Dockerfile.worker` 构建

## 📡 对接协议（api.php）

下游店铺按 FreeDom 协议 POST `uid` + `key`：

| act | 用途 |
|---|---|
| `getclass` | 拉商品目录（cid/价格/说明） |
| `getmoney` | 查余额 |
| `add` | 下单（platform/school/user/pass/kcid/kcname/cookies） |
| `budan` | 补刷重投 |
| `chadanoid` | 查订单进度 |

## 📦 订单约定

- `cookies`：课程平台导出的 cookies.json
- `kcid`：`{"课程id": {"clazzid":"xx","name":"xx"}}`
- 状态机：`queued → running → done / fail`

## ⚠️ 备注

- proxypool 采的是免费公开代理，仅演示用；生产换付费住宅代理 API（`PROXY_API` 指过去即可）
- 仅供学习交流 ⚠️

<div align="center">
<sub>Built with Devin · Powered by real browsers, not fake heartbeats</sub>
</div>
