<?php
include('../confing/common.php');

$today = date('Y-m-d');
$reminderLimit = 1; // 一天最多提醒次数
$keywords = ['考试未完成', '已打开考试', '可能时间耗尽', '已经提交', '低于', '章测未做完' ,'异常'];

// 获取所有 status 不为 "已完成"、"已退款" 和 "已停止" 的订单，并且 addtime 在最近15天内
$orders = $DB->get_results("
    SELECT * 
    FROM qingka_wangke_order 
    WHERE status NOT IN ('已完成', '已退款', '已停止') 
    AND addtime >= DATE_SUB(CURDATE(), INTERVAL 15 DAY)
");

// 初始化一个用于存储每个用户异常订单的数组
$userOrders = [];

if (!empty($orders)) {
    foreach ($orders as $order) {
        // 获取订单相关用户信息
        $user = $DB->get_row("SELECT * FROM qingka_wangke_user WHERE uid='{$order['uid']}'");

        $pattern = '/' . implode('|', $keywords) . '/';

        // 检查订单的 remarks 是否包含设定的关键词
        if (preg_match($pattern, $order['remarks'])) {
            // 查询今天是否已经发送过相同提醒（邮箱或微信），且次数未超过限制
            $logCheck = $DB->get_results("
                SELECT COUNT(*) AS reminder_count 
                FROM qingka_wangke_log 
                WHERE uid='{$order['uid']}' 
                AND (type='邮箱提醒' OR type='WX提醒') 
                AND text LIKE '%{$order['ptname']}%' 
                AND DATE(addtime)='$today'
            ");

            // 如果提醒次数小于限制
            if ($logCheck[0]['reminder_count'] < $reminderLimit) {
                // 将订单信息存储到对应用户的数组中
                if (!isset($userOrders[$user['uid']])) {
                    $userOrders[$user['uid']] = [
                        'userInfo' => $user,  // 用户信息
                        'orders' => []        // 异常订单信息
                    ];
                }
                $userOrders[$user['uid']]['orders'][] = [
                    'ptname' => $order['ptname'],
                    'user' => $order['user'],
                    'remarks' => $order['remarks'],
                    'kcname' => $order['kcname']
                ];
            } else {
                echo "今天已经发送过提醒，订单账号：{$order['user']}\n";
            }
        } 
        else{
            echo "订单不符合条件。\n";
        }
    }

    // 处理并发送每个用户的提醒
    foreach ($userOrders as $uid => $userData) {
        $user = $userData['userInfo'];
        $orders = $userData['orders'];

        // 如果用户有异常订单
        if (!empty($orders)) {
            // 邮件发送
            if (!empty($user['email'])) {
                $toEmail = $user['email'];
                $toName = 'FreeDomQA';
                $templateId = 'xmjg'; // 邮件模板

                // 发送邮件并记录日志
                $emailResult = sendEmail($toEmail, $toName, $templateId, $orders); // 传递多个订单
                if ($emailResult) {
                    foreach ($orders as $order) {
                        wlog($uid, "邮箱提醒", "平台：{$order['ptname']} 订单账号：{$order['user']} 订单课程：{$order['kcname']}", 0);
                    }
                    echo "已发送提醒至 {$toEmail}\n";
                } else {
                    echo "发送邮件失败：{$emailResult}\n";
                }
            }

            // 微信推送提醒
            if (!empty($user['tuisongtoken'])) {
                $message = "注意！以下订单可能存在异常，请尽快查看：";
                foreach ($orders as $order) {
                    $message .= "\n订单账号：" . $order['user'] . "，平台：" . $order['ptname'] ;
                }
                $messageEncoded = urlencode($message);
                $spt = urlencode($user['tuisongtoken']);
                $apiUrl = "https://wxpusher.zjiecode.com/api/send/message/$spt/$messageEncoded";
                $response = file_get_contents($apiUrl);

                foreach ($orders as $order) {
                    wlog($uid, "WX提醒", "平台：{$order['ptname']} 订单账号：{$order['user']} 订单课程：{$order['kcname']}", 0);
                }

                echo "已发送微信推送至用户: {$user['user']}\n";
            }
        }
    }
} else {
    echo "没有符合条件的订单。\n";
}
?>