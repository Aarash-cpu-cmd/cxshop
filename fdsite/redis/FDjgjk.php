<?php
include('../confing/common.php');

// 自定义价格更新规则
$price_rules = [
    ['hids' => [10], 'multiplier' => 1.05], // 输入符合情况一的hid。价格加价情况一：类似暗网的平台返回的价格，比如源台定价0.2，但是返回的价格是1，即源台单价*5的情况。
    ['hids' => [1], 'multiplier' => 1.05 * 5], // 输入符合情况二的hid。价格加价情况二：类似FreeDom平台返回的价格，比如源台定价0.2，返回的价格也是0.2的情况。
    ['hids' => [104], 'multiplier' => 1.1 ], // 自定义情况
    // 可以继续添加更多规则
];



// 准备查询语句以获取所有 hid
$result = $DB->query("SELECT hid FROM qingka_wangke_huoyuan");

// 存储所有需要更新的数据
$updates = [];

// 遍历每个 hid
while ($hid_row = $result->fetch_assoc()) {
    $hid = $hid_row['hid'];

    // 获取当前 hid 的用户信息
    $a = $DB->get_row("SELECT * FROM qingka_wangke_huoyuan WHERE hid='{$hid}'");
    // 获取分类信息
    $data = array("uid" => $a["user"], "key" => $a["pass"]);
    $er_url = "{$a["url"]}/api.php?act=getclass";
    $api_result = get_url($er_url, $data);

    // 错误处理
    if ($api_result === false) {
        echo "API 调用失败: {$a['name']}\n";
        continue;
    }

    $result1 = json_decode($api_result, true);

    // 错误处理
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON 解码失败: {$a['name']}\n";
        continue;
    }

    $addtime = date('Y-m-d H:i:s');
    $data = $result1["data"];
    $update_count = 0; // 初始化更新计数器

    // 获取所有需要检查的 hid
    $check_hids = array_column($price_rules, 'hids');
    $check_hids = array_merge(...$check_hids);

    // 如果当前 hid 不在需要检查的列表中，则跳过
    if (!in_array($hid, $check_hids)) {
        echo "{$a['name']} 的 hid 不在更新列表中，跳过更新。\n";
        continue;
    }

    foreach ($data as $k => $value) {
        $price = $value['price'];
        $existsResult = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_class WHERE docking = '{$hid}' AND noun = '{$value['cid']}'");

        if ($existsResult['count'] > 0) {
            // 根据自定义规则计算价格
            $price = apply_price_rule($hid, $price, $price_rules);

            // 存储更新数据
            $updates[] = [
                'price' => $price,
                'content' => $value['content'],
                'addtime' => $addtime,
                'docking' => $hid,
                'noun' => $value['cid']
            ];
            $update_count++; // 更新计数器
        }
    }

    // 日志记录
    if ($update_count > 0) {
        echo "{$a['name']} 更新了 {$update_count} 个商品，更新时间为：{$addtime}\n";
    } else {
        echo "{$a['name']} 无商品需要更新！\n";
    }
}

// 批量更新数据库
if (!empty($updates)) {
    foreach ($updates as $update) {
        $DB->query("UPDATE qingka_wangke_class SET price='{$update['price']}', content='{$update['content']}', addtime='{$update['addtime']}' WHERE docking = '{$update['docking']}' AND noun = '{$update['noun']}'");
    }
}

// 应用价格规则的函数
function apply_price_rule($hid, $price, $price_rules) {
    foreach ($price_rules as $rule) {
        if (in_array($hid, $rule['hids'])) {
            $price *= $rule['multiplier'];
            break;
        }
    }
    return $price;
}
?>