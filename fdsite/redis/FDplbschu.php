<?php
require_once('../confing/common.php');

define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);
define('REDIS_DB', 13);
define('BATCH_SIZE', 100);  // 每批次处理的订单数
define('PROCESSING_TIME_LIMIT', 10);  // 每个订单的处理超时时间
define('REDIS_QUEUE_KEY', 'csoid');

// 初始化Redis连接
$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);
$redis->select(REDIS_DB);

// 批量获取订单ID
function fetchOrdersFromQueue($redis, $batchSize) {
    $oids = [];
    for ($i = 0; $i < $batchSize; $i++) {
        $oid = $redis->lpop(REDIS_QUEUE_KEY);
        if ($oid) {
            $oids[] = $oid;
            echo "获取到订单ID: {$oid}\n";
        } else {
            echo "队列中无更多订单\n";
            break;
        }
    }
    return $oids;
}

// 处理单个订单的逻辑
function processSingleOrder($DB, $oid) {
    echo "开始处理订单: {$oid}\n";
    $order = $DB->get_row("SELECT * FROM qingka_wangke_order WHERE oid='$oid'");
    if (!$order) {
        echo "订单{$oid}不存在\n";
        return ['code' => -1, 'message' => '数据库中未找到该订单！','oid' => $oid];
    }

    $startTime = microtime(true);
    $result = budanWk($oid);  // 假设这是一个业务处理函数
    $processingTime = microtime(true) - $startTime;

    echo "订单{$oid}处理时间: " . number_format($processingTime, 2) . "秒\n";

    // 如果处理时间超过设定的时间限制
    if ($processingTime > PROCESSING_TIME_LIMIT) {
        echo "订单{$oid}处理超时\n";
        return ['code' => -1, 'message' => '处理超时'];
    }

    return $result;
}

// 批量更新订单状态
function batchUpdateOrders($DB, $orders) {
    $today = date("Y-m-d H:i:s");
    foreach ($orders as $order) {
        $oid = $order['oid'];
        $result = $order['result'];
        if ($result['code'] >= 0) {
            // 成功处理订单
            $DB->query("UPDATE qingka_wangke_order SET status='补刷中', remarks='补刷成功，等待进度更新。上次补刷成功时间：$today', bsnum=bsnum+1 WHERE oid='$oid'");
            echo "订单{$oid}补刷成功\n";
            wlog(0, "订单补刷", "后台成功补刷了订单 {$oid}", 0);
        } else {
            // 处理失败
            $DB->query("UPDATE qingka_wangke_order SET status='补刷失败', remarks='补刷失败，原因：{$result['message']}，上次补刷失败时间：$today' WHERE oid='$oid'");
            echo "订单{$oid}补刷失败，原因：{$result['message']}\n";
            wlog(0, "订单补刷", "后台补刷失败，请稍后重试！订单 {$oid}", 0);
        }
    }
}

// 主循环，批量处理订单
function processQueue($DB, $redis) {
    while (true) {
        echo "检查队列中是否有订单...\n";
        // 批量获取订单ID
        $oids = fetchOrdersFromQueue($redis, BATCH_SIZE);
        if (count($oids) > 0) {
            $ordersToProcess = [];
            foreach ($oids as $oid) {
                $result = processSingleOrder($DB, $oid);
                if ($result) {
                    $ordersToProcess[] = ['oid' => $oid, 'result' => $result];
                }
            }

            // 批量更新订单状态
            if (!empty($ordersToProcess)) {
                batchUpdateOrders($DB, $ordersToProcess);
            }
        } else {
            // 如果没有订单需要处理，等待一段时间，但期间不断检查
            adaptiveSleepWithCheck($redis);
        }

        // 避免无限循环导致的资源占用，休眠5秒
        sleep(5);  // 5秒
    }
}

// 自适应等待时间并持续检查
function adaptiveSleepWithCheck($redis) {
    $maxSleepTime = 600;  // 最大休眠时间10秒
    $sleepInterval = 10;  // 每次检查的时间间隔为1秒

    echo "无订单可处理，开始休眠，每{$sleepInterval}秒检查一次...\n";

    for ($i = 0; $i < $maxSleepTime; $i += $sleepInterval) {
        // 每隔1秒检查是否有新订单
        $oid = $redis->lpop(REDIS_QUEUE_KEY);
        if ($oid) {
            echo "休眠中发现新订单ID: {$oid}，立即处理\n";
            // 立即处理新订单
            batchUpdateOrders($DB, $oid);
            return;  // 退出休眠循环，回到主处理逻辑
        }

        // 如果没有新订单，则继续休眠
        echo "第" . ($i + $sleepInterval) . "秒：无新订单，继续休眠...\n";
        sleep($sleepInterval);  // 休眠1秒再检查
    }

    echo "休眠结束，无新订单\n";
}

// 启动主处理程序
processQueue($DB, $redis);

?>
