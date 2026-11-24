# CXSHOP · 网课代刷门面系统

自研代刷接单系统：FastAPI 门面 + 订单调度器 + Docker worker 刷课 + 代理 IP 池，一条 `docker compose up` 起全栈。

## 架构

```
买家 → shop(FastAPI+自带前端) → orders表 → dispatcher → fd-worker容器(chromium+chaoxing-grind)
                                          ↘ proxypool → 每单一个出口代理(可选)
```

- **shop**：注册/登录/下单/订单进度/管理后台（原生单页前端，无构建）
- **dispatcher**：轮询 `status='queued'` 订单 → 起 worker 容器 → 回写状态与日志
- **fd-worker**：headless Chromium + [chaoxing-grind](https://github.com/Aarash-cpu-cmd/chaoxing-grind) 工具包
- **proxypool**：[jhao104/proxy_pool](https://github.com/jhao104/proxy_pool)（MIT，vendored）免费代理采集+验证+REST API；worker 启动时取一个 sticky 出口

## 快速开始

```bash
docker compose up -d
# 门面: http://localhost:8082  管理后台: http://localhost:8082/admin
# 默认管理员: admin / admin123 (ADMIN_PASS 环境变量可改)
```

worker 镜像 `fd-worker` 需预先构建（chaoxing-grind 目录的 Dockerfile.worker）。

## 订单约定

- `cookies`：学习通导出的 cookies.json（无 cookie 密码登录暂未实现）
- `kcid`：JSON，如 `{"课程id": {"clazzid":"xxx","name":"xxx"}}`
- 状态机：`queued → running → done/fail`

## 备注

proxy_pool 为免费公开代理源，仅供演示；生产建议替换为付费住宅代理 API（`PROXY_API` 指向你的接口即可，返回 `{"proxy":"ip:port"}`）。
