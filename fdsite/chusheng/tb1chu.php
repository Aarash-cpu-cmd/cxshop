<?php
include('../confing/common.php');
$redis = new Redis();
$redis->connect("127.0.0.1", "6379");

// 定义多个队列池和对应的 Redis 分区
$queuePools = array(
    'oidblpt000' => 0,
    'oidsjxz' => 8,
    'oidstdks' => 10
);

$yqsl = 500; // 单进程单次最大获取订单数
$processId = getenv('SUPERVISOR_PROCESS_NAME'); // 获取当前进程ID

while (true) {
    $foundOrder = false; // 标记是否找到订单

    foreach ($queuePools as $queue => $partition) {
        $redis->select($partition);

        $oids = array();
        $totalOrders = 0;
        $queueLength = $redis->llen($queue);

        if ($queueLength >= 20) {
            $yqslyqsl = ($queueLength < $yqsl) ? min(intval($queueLength / 3), $yqsl) : $yqsl;
        } else {
            $yqslyqsl = $queueLength;
        }

        for ($i = 0; $i < $yqslyqsl; $i++) {
            $oid = $redis->lpop($queue);
            if ($oid != '') {
                $oids[] = $oid;
                $foundOrder = true; // 找到订单时设置为true
            } else {
                break;
            }
        }

        $oidcount = count($oids);
        $totalOrders += $oidcount;

        if ($oidcount != 0) {
            echo "当前进程: " . $processId . " 成功获取 " . $totalOrders . " 个订单\n";
            echo "----------------------------------------------\n";

            if (!empty($oids)) {
                foreach ($oids as $oid) {
                    $result = processCx($oid);

                    $updateSuccess = true;

                    foreach ($result as $item) {
                        if ($item['code'] == 1) {
                            $updateSuccess = $updateSuccess && $DB->query("UPDATE qingka_wangke_order SET
                                `name`='{$item['name']}',
                                `yid`='{$item['yid']}',
                                `status`='{$item['status_text']}',
                                `courseStartTime`='{$item['kcks']}',
                                `courseEndTime`='{$item['kcjs']}',
                                `examStartTime`='{$item['ksks']}',
                                `examEndTime`='{$item['ksjs']}',
                                `process`='{$item['process']}',
                                `remarks`='{$item['remarks']}'
                                WHERE
                                `user`='{$item['user']}' AND
                                `kcname`='{$item['kcname']}' AND
                                `oid`='$oid'");
                        }
                    }

                    if ($updateSuccess) {
                        $today_day = date("Y-m-d H:i:s");
                        echo "订单 $oid 已更新完成！\n";
                        echo "当前进程: " . $processId . "剩余 " . (--$totalOrders) . " 个订单。\n";
                        echo "更新时间：" . $today_day . "\n";
                        echo "----------------------------------------------\n";
                        sleep(1);
                    } else {
                        echo "订单 $oid 更新失败\n";
                        echo "----------------------------------------------\n";
                        $totalOrders--;
                    }
                }
            }
        }
    }

    if (!$foundOrder) {
        echo "未找到订单，休息10秒\n";
        sleep(10);
    }

    $randomSleepTime = mt_rand(5, 15);
    sleep($randomSleepTime);
}
?>
