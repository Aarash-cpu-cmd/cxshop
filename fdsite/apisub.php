<?php
include ('confing/common.php');
include ('ayconfig.php');
$php_Self = substr($_SERVER['PHP_SELF'], strripos($_SERVER['PHP_SELF'], "/") + 1);
if ($php_Self != "apisub.php") {
    $msg = '%E6%96%87%E4%BB%B6%E9%94%99%E8%AF%AF';
    $msg = urldecode($msg);
    exit(json_encode(['code' => - 1, 'msg' => $msg]));
}
$today_day=date("Y-m-d H:i:s");
switch ($act) {
    case 'yjflxj':
	    $a=$DB->query("select * from qingka_wangke_fenlei where status=0");
	    $count1=$DB->count("select count(*) from qingka_wangke_fenlei where status=0");
	    while($row=$DB->fetch($a)){
	        $b=$DB->query("select * from qingka_wangke_class where fenlei='{$row['id']}' ");
	        while($res=$DB->fetch($b)){
	            $DB->query("update qingka_wangke_class set status='0' where cid={$res['cid']} ");
	        }
        }
        jsonReturn(1,"已下架{$count1}个分类内的全部项目");
        //exit('{"code":1,"msg":"已下架$count1个分类内的全部项目"}');
	break;
	case 'yjflsj':
	    $a=$DB->query("select * from qingka_wangke_fenlei where status=1");
	    $count1=$DB->count("select count(*) from qingka_wangke_fenlei where status=1");
	    while($row=$DB->fetch($a)){
	        $b=$DB->query("select * from qingka_wangke_class where fenlei='{$row['id']}' ");
	        while($res=$DB->fetch($b)){
	            $DB->query("update qingka_wangke_class set status='1' where cid={$res['cid']} ");
	        }
        }
        jsonReturn(1,"已上架{$count1}个分类内的全部项目");
        //exit('{"code":1,"msg":"已下架$count1个分类内的全部项目"}');
	break;
	
case 'yjsj':
    $ids = isset($_POST['cid']) ? $_POST['cid'] : array();
    if ($userrow['uid'] != '1') {
        jsonReturn(-1, "滚");
    }
    // 如果 $ids 是数组，则循环上架选中的项目
    if (is_array($ids)) {
        foreach ($ids as $id) {
            $DB->query("update qingka_wangke_class set status='1' where cid='$id' ");
        }
    } else {
        // 如果 $ids 不是数组，则直接上架单个项目
        $id = daddslashes($ids);
        $DB->query("update qingka_wangke_class set status='1' where cid='$id' ");
    }
    jsonReturn(1, "上架成功");
    break;
	
case 'yjxj':
    $ids = isset($_POST['cid']) ? $_POST['cid'] : array();
    if ($userrow['uid'] != '1') {
        jsonReturn(-1, "滚");
    }
    // 如果 $ids 是数组，则循环下架选中的项目
    if (is_array($ids)) {
        foreach ($ids as $id) {
            $DB->query("update qingka_wangke_class set status='0' where cid='$id' ");
        }
    } else {
        // 如果 $ids 不是数组，则直接下架单个项目
        $id = daddslashes($ids);
        $DB->query("update qingka_wangke_class set status='0' where cid='$id' ");
    }
    jsonReturn(1, "下架成功");
    break;
	
    case 'yjdj':
    if ($userrow['uid'] == 1) {
        // 获取请求参数并进行清理
        $hid = trim(strip_tags(daddslashes($_GET['hid'])));
        $pricee = trim(strip_tags(daddslashes($_GET['pricee'])));
        $category = trim(strip_tags(daddslashes($_GET['category'])));
        $name = trim(strip_tags(daddslashes($_GET['name']))); // 获取name参数
        $fd = trim(strip_tags(daddslashes($_GET['fd']))); // 判断是否只更新

        // 获取货源信息
        $a = $DB->get_row("SELECT * FROM qingka_wangke_huoyuan WHERE hid='{$hid}'");

        // 获取分类信息
        $data = array("uid" => $a["user"], "key" => $a["pass"]);
        $er_url = "{$a["url"]}/api.php?act=getclass";
        $result = get_url($er_url, $data);
        $result1 = json_decode($result, true);
        $addtime = date('Y-m-d H:i:s');

        // 检查分类名称是否存在
        $namereselt = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_fenlei WHERE name='{$name}' and status != 3 ORDER BY id DESC LIMIT 0,1");
        if ($namereselt['count'] > 0) {
            $b = $DB->get_row("SELECT * FROM qingka_wangke_fenlei WHERE name='{$name}' and status != 3 ORDER BY id DESC LIMIT 0,1");
        } elseif ($fd == 0 && !empty($name)) {
            // 如果fd不等于1，则插入新的分类
            $DB->query("INSERT INTO qingka_wangke_fenlei (sort, name, status, time) VALUES ('10', '{$name}', '1', NOW())");
            $b = $DB->get_row("SELECT * FROM qingka_wangke_fenlei WHERE name='{$name}' and status != 3 ORDER BY id DESC LIMIT 0,1");
        } elseif (empty($name) && $category != '999999') {
            $DB->query("INSERT INTO qingka_wangke_fenlei (sort, name, status, time) VALUES ('10', '{$a['name']}', '1', NOW())");
            $b = $DB->get_row("SELECT * FROM qingka_wangke_fenlei WHERE name='{$a['name']}' and status != 3 ORDER BY id DESC LIMIT 0,1");
        } 

        // 处理获取到的分类数据
        $data = $result1["data"];
        $numItems = count($data);
        $i = 0; // 新插入的记录数
        $g = 0; // 更新的记录数

        foreach ($data as $k => $value) {
            if ($value['fenlei'] == $category) { // 对比用户输入的分类ID
                $price = $value['price'] * $pricee;
                $maxSortResult = $DB->get_row("SELECT MAX(sort) as max_sort FROM qingka_wangke_class");
                $maxSortValue = $maxSortResult['max_sort'] ? $maxSortResult['max_sort'] : 10; // 如果没有记录，max_sort为10
                $sort = $maxSortValue + 1; // 排序字段，可以根据需要进行调整

                $existsResult = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_class WHERE docking = '{$hid}' AND noun = '{$value['cid']}'");
                if ($existsResult['count'] > 0) {
                    // 更新现有记录
                    $DB->query("UPDATE qingka_wangke_class SET price='{$price}', content='{$value['content']}', status='1' WHERE docking = '{$hid}' AND noun = '{$value['cid']}'");
                    $g++;
                } elseif ($fd == 0) {
                    // 插入新记录
                    $DB->query("INSERT INTO qingka_wangke_class (name, getnoun, noun, fenlei, queryplat, docking, price, sort, content, addtime) VALUES ('{$value['name']}', '{$value['cid']}', '{$value['cid']}', '{$b['id']}', '$hid', '$hid', '{$price}', '{$sort}', '{$value['content']}', NOW())");
                    $i++;
                }
            } elseif ($category == '999999' && $fd == 1) {
                $price = $value['price'] * $pricee;
                $existsResult = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_class WHERE docking = '{$hid}' AND noun = '{$value['cid']}'");
                if ($existsResult['count'] > 0) {
                    // 更新现有记录
                    $DB->query("UPDATE qingka_wangke_class SET price='{$price}', content='{$value['content']}', status='1' WHERE docking = '{$hid}' AND noun = '{$value['cid']}'");
                    $g++;
                }
            } elseif ($category == '999999' && $fd == 0) {
                $price = $value['price'] * $pricee;
                $maxSortResult = $DB->get_row("SELECT MAX(sort) as max_sort FROM qingka_wangke_class");
                $maxSortValue = $maxSortResult['max_sort'] ? $maxSortResult['max_sort'] : 10; // 如果没有记录，max_sort为10
                $sort = $maxSortValue + 1; // 排序字段，可以根据需要进行调整

                $existsResult = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_class WHERE docking = '{$hid}' AND noun = '{$value['cid']}'");
                if ($existsResult['count'] > 0) {
                    // 更新现有记录
                    $DB->query("UPDATE qingka_wangke_class SET price='{$price}', content='{$value['content']}', status='1' WHERE docking = '{$hid}' AND noun = '{$value['cid']}'");
                    $g++;
                } else {
                    // 检查category_name是否存在
                    if (!empty($value['category_name'])) {
                        $categoryNameExists = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_fenlei WHERE name='{$value['category_name']}' and status != 3");
                        if ($categoryNameExists['count'] > 0) {
                            $b = $DB->get_row("SELECT * FROM qingka_wangke_fenlei WHERE name='{$value['category_name']}' and status != 3 ORDER BY id DESC LIMIT 0,1");
                        }else{
                            // 插入新的分类
                            $DB->query("INSERT INTO qingka_wangke_fenlei (sort, name, status, time) VALUES ('10', '{$value['category_name']}', '1', NOW())");
                            $b = $DB->get_row("SELECT * FROM qingka_wangke_fenlei WHERE name='{$value['category_name']}' and status != 3 ORDER BY id DESC LIMIT 0,1");
                        }
                    } else {
                        // 插入新的分类
                        $DB->query("INSERT INTO qingka_wangke_fenlei (sort, name, status, time) VALUES ('10', '{$a['name']}', '1', NOW())");
                        $b = $DB->get_row("SELECT * FROM qingka_wangke_fenlei WHERE name='{$a['name']}' and status != 3 ORDER BY id DESC LIMIT 0,1");
                    }

                    // 插入新记录
                    $DB->query("INSERT INTO qingka_wangke_class (name, getnoun, noun, fenlei, queryplat, docking, price, sort, content, addtime) VALUES ('{$value['name']}', '{$value['cid']}', '{$value['cid']}', '{$b['id']}', '$hid', '$hid', '{$price}', '{$sort}', '{$value['content']}', NOW())");
                    $i++;
                }
            }
        }

        $total = $i + $g;
        if ($total > 0 && $fd != 1) {
            wlog($userrow['uid'], "更新上架", "已上架{$name}的全部分类的项目，共计{$total}个，新上架{$i}个, 更新{$g}个，并自动新建分类到【{$b["name"]}】中，价格、排序和内容已更新", 0);
            jsonReturn(1, "已上架{$name}的全部分类的项目，共计{$total}个，新上架{$i}个, 更新{$g}个，并自动新建分类到【{$b["name"]}】中，价格、排序和内容已更新");
        } elseif ($total > 0 && $fd == 1) {
            wlog($userrow['uid'], "更新上架", "已上架{$name}的全部分类的项目，共计{$total}个，新上架{$i}个, 更新{$g}个，价格、排序和内容已更新", 0);
            jsonReturn(1, "已上架{$name}的全部分类的项目，共计{$total}个，新上架{$i}个, 更新{$g}个，价格、排序和内容已更新");
        } else {
            jsonReturn(-1, "无可更新内容！请检查是否存在对应商品！");
        }
    }
break;
    
case 'updateStatus':

    $hid = trim(strip_tags(daddslashes($_GET['hid'])));
    $a = $DB->get_row("SELECT * FROM qingka_wangke_huoyuan WHERE hid = '{$hid}'");

    if (!$a) {
        exit(json_encode(array("code" => -1, "msg" => "未找到对应的接口信息")));
    }

    $data = array("uid" => $a["user"], "key" => $a["pass"]);
    $er_url = "{$a["url"]}/api.php?act=getclass";
    $result = get_url($er_url, $data);
    $result1 = json_decode($result, true);

    if (!isset($result1['data']) || empty($result1['data'])) {
        exit(json_encode(array("code" => -1, "msg" => "接口返回的数据格式不正确")));
    }
    $apiCids = array_map(function ($item) {
        return $item['cid'];
    }, $result1['data']);
    $existingItems = $DB->query("SELECT cid, noun, status FROM qingka_wangke_class WHERE docking = '{$hid}'");
    $updateIds = [];
    $downCids = [];  // 用于存储下架商品的 cid
    if ($existingItems) {
        while ($item = $DB->fetch($existingItems)) {
            if (!in_array($item['noun'], $apiCids) && $item['status'] != '0') {
                $updateIds[] = $item['cid'];
                $downCids[] = $item['cid'];
            }
        }

        if (!empty($updateIds)) {
            $ids = implode(',', $updateIds);
            $DB->query("UPDATE qingka_wangke_class SET status = '0' WHERE cid IN ({$ids})");

            $updateCount = count($updateIds);
            $cidList = implode(', ', $downCids);  
            wlog($userrow['uid'], "状态更新", "共下架{$updateCount}个商品，商品CID: {$cidList}", 0);
            exit(json_encode(array("code" => 1, "msg" => "更新{$updateCount}个商品的状态为0，商品CID: {$cidList}")));
        } else {
            exit(json_encode(array("code" => 1, "msg" => "没有需要更新的商品状态")));
        }
    } else {
        exit(json_encode(array("code" => -1, "msg" => "数据库中没有找到相关商品")));
    }

    break;


    
    case 'plup':
        $a = trim(strip_tags(daddslashes($_GET['a'])));
        $sex = daddslashes($_POST['sex']);
        $type = trim(strip_tags(daddslashes($_POST['type'])));
        if ($a == " " or empty($sex)) {
            jsonReturn(-1, "请先选择订单");
        }
        if ($userrow['uid'] != 1 && $a != "待更新") {
            jsonReturn(-1, "老铁，求您别干我");
        }
        if ($type == 1) {
            $sql = "`status`='$a'";
        } elseif ($type == 2) {
            $sql = "`dockstatus`='$a'";
        }
        if ($a == "待更新") {
            $count = count($sex);
            for ($i = 0;$i < count($sex);$i++) {
                $oid = $sex[$i];
                $b = $DB->query("update qingka_wangke_order set {$sql} where oid='{$oid}' ");
            }
            wlog($userrow['uid'], "批量更新", "批量更新了 $count 条订单", 0);
            if ($b) {
                jsonReturn(1, "更新成功已加入后端执行");
            } else {
                jsonReturn(-1, "未知异常");
            }
        } else {
            exit('{"code":-1,"msg":"无权限"}');
        }
    break;
    case 'plbs':
        $a = trim(strip_tags(daddslashes($_GET['a'])));
        $sex = daddslashes($_POST['sex']);
        $type = trim(strip_tags(daddslashes($_POST['type'])));
        if ($a == " " or empty($sex)) {
            jsonReturn(-1, "请先选择订单");
        }
        if ($userrow['uid'] != 1 && $a != "待重刷") {
            jsonReturn(-1, "请耐心等待补刷！");
        }
        if ($type == 1) {
            $sql = "`status`='$a'";
        } elseif ($type == 2) {
            $sql = "`dockstatus`='$a'";
        }
        if ($a == "待重刷") {
            $count = count($sex);
            for ($i = 0;$i < count($sex);$i++) {
                $oid = $sex[$i];
                $b = $DB->query("update qingka_wangke_order set {$sql} where oid='{$oid}' ");
            }
            wlog($userrow['uid'], "批量重刷", "批量重刷了 $count 条订单", 0);
            if ($b) {
                jsonReturn(1, "更新成功已加入后端执行");
            } else {
                jsonReturn(-1, "未知异常");
            }
        } else {
            exit('{"code":-1,"msg":"无权限"}');
        }
    break;
    case 'wlogin_1':
        jsonReturn(1, "不写也罢");
        if ($userrow['uid'] == 1) {
            $uid = daddslashes($_POST['uid']);
            $row = $DB->get_row("SELECT * FROM qingka_wangke_user WHERE uid='$uid' limit 1");
            $session = md5($user . $pass . $password_hash);
            $token = authcode("{$user}\t{$session}", 'ENCODE', SYS_KEY);
            setcookie("admin_token", $token, time() + 3000);
            exit('{"code":1,"msg":"登录成功2"}');
        } else {
            jsonReturn(-1, "你在干啥？");
        }
    break;
    case 'userinfo_1':
        if ($islogin != 1) {
            exit('{"code":-10,"msg":"请先登录"}');
        }
        $a = $DB->get_row("select uid,user,notice from qingka_wangke_user where uid='{$userrow['uuid']}' ");
        $dd = $DB->count("select count(oid) from qingka_wangke_order where uid='{$userrow['uid']}' ");
        //$zcz=$DB->count("select sum(money) as money from qingka_wangke_log where type='上级充值' and uid='{$userrow['uid']}' ");
        //安全验证1
        if ($userrow['addprice'] < 0.1) {
            $DB->query("update qingka_wangke_user set addprice='1' where uid='{$userrow['uid']}' ");
            jsonReturn(-9, "大佬，我得罪不起您啊，有什么做的不好的地方尽管提出来，我小本生意，经不起折腾，还望多多包涵");
        }
        //安全验证2
        if ($userrow['uid'] != 1) {
            if ((int)$userrow['money'] - (int)'0.1' > (int)$userrow['zcz']) {
                $DB->query("update qingka_wangke_user set money='$zcz',active='0' where uid='{$userrow['uid']}' ");
                jsonReturn(-9, "账号异常，请联系你老大");
            }
        }
        //代理数据统计
        $dlzs = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' ");
        $dldl = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and endtime>'$jtdate' ");
        $dlzc = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and addtime>'$jtdate' ");
        $jrjd = $DB->count("select count(uid) from qingka_wangke_order where uid='{$userrow['uid']}' and addtime>'$jtdate' ");
        //       while($dllist2=$DB->fetch($DB->query("select uid from qingka_wangke_user where uuid='{$userrow['uid']}'"))){
        //       	  $dlxd+=$DB->count("select count(oid) from qingka_wangke_order where uid='{$ddlist2['uid']}' and addtime>'$jtdate' ");
        //       }
        //$dlxd="emmmmmm";
        $dailitongji = array('dlzc' => $dlzc, 'dldl' => $dldl, 'dlxd' => $dlxd, 'dlzs' => $dlzs, 'jrjd' => $jrjd);
        $data = array('code' => 1, 'msg' => '查询成功', 'uid' => $userrow['uid'], 'user' => $userrow['user'], 'qq_openid' => $userrow['qq_openid'], 'nickname' => $userrow['nickname'], 'faceimg' => $userrow['faceimg'], 'money' => round($userrow['money'], 2), 'addprice' => $userrow['addprice'], 'key' => $userrow['key'], 'sjuser' => $a['user'], 'dd' => $dd, 'zcz' => $userrow['zcz'], 'yqm' => $userrow['yqm'], 'yqprice' => $userrow['yqprice'], 'notice' => $conf['notice'], 'sjnotice' => $a['notice'], 'dailitongji' => $dailitongji);
        exit(json_encode($data));
    break;
}
switch ($act) {
    case 'status_order':
        $a = trim(strip_tags(daddslashes($_GET['a'])));
        $sex = daddslashes($_POST['sex']);
        $type = trim(strip_tags(daddslashes($_POST['type'])));
        if ($a == " " or empty($sex)) {
            jsonReturn(-1, "请先选择订单");
        }
        if ($userrow['uid'] != 1) {
            jsonReturn(-1, "老铁，求您别干我");
        }
        if ($type == 1) {
            $sql = "`status`='$a'";
        } elseif ($type == 2) {
            $sql = "`dockstatus`='$a'";
        }
        if ($userrow['uid'] == 1) {
            for ($i = 0;$i < count($sex);$i++) {
                $oid = $sex[$i];
                $b = $DB->query("update qingka_wangke_order set {$sql} where oid='{$oid}' ");
            }
            if ($b) {
                jsonReturn(1, "修改成功");
            } else {
                jsonReturn(-1, "未知异常");
            }
        } else {
            exit('{"code":-1,"msg":"无权限"}');
        }
    break;
    case 'passwd':
        $oldpass = trim(strip_tags(daddslashes($_POST['oldpass'])));
        $newpass = trim(strip_tags(daddslashes($_POST['newpass'])));
        if ($oldpass != $userrow['pass']) {
            exit('{"code":-1,"msg":"原密码错误"}');
        }
        if ($newpass == '') {
            exit('{"code":-1,"msg":"新密码不能为空"}');
        }
        $sql = "update `qingka_wangke_user` set `pass` ='{$newpass}' where `uid`='{$userrow['uid']}'";
        if ($DB->query($sql)) {
            exit('{"code":1,"msg":"修改成功,请牢记密码"}');
        } else {
            exit('{"code":-1,"msg":"修改失败"}');
        }
    break;
    case 'shaixuan':
        // 获取筛选选项
        $sortOption = trim(strip_tags(daddslashes($_POST['option'])));
        // 初始化结果数组
        $result = array();
        // 根据筛选选项执行不同的查询
        if ($sortOption == 'total') {
            // 查询总单量排序
            $query = "SELECT ptname, cid, COUNT(*) AS total_orders FROM qingka_wangke_order GROUP BY ptname, cid ORDER BY total_orders DESC LIMIT 5";
        } else if ($sortOption == 'today') {
            // 查询今日单量排序
            $query = "SELECT ptname, cid, COUNT(*) AS total_orders FROM qingka_wangke_order WHERE addtime > '$jtdate' GROUP BY ptname, cid ORDER BY total_orders DESC LIMIT 5";
        }
        // 执行查询
        $result_query = $DB->query($query);
        // 将结果添加到结果数组中
        while ($row = $DB->fetch($result_query)) {
            $today_order_count = 0;
            if ($sortOption == 'today') {
                // 查询今日销量
                $today_order_query = "SELECT COUNT(*) AS today_orders FROM qingka_wangke_order WHERE ptname='{$row['ptname']}' AND cid='{$row['cid']}' AND addtime>'$jtdate'";
                $today_order_result = $DB->query($today_order_query);
                $today_order_row = $DB->fetch($today_order_result);
                $today_order_count = $today_order_row['today_orders'];
            }
            // 添加项目信息到结果数组中
            $result[] = array(
                'cid' => $row['cid'],
                'ptname' => $row['ptname'],
                'today_orders' => $today_order_count,
                'total_orders' => $row['total_orders']
            );
        }
        // 返回 JSON 格式的结果
        exit(json_encode($result));
    break;
    case 'xgbz':
        if($userrow['uid'] == 1)
        {
            // 获取前端传递的参数，这里使用explode函数将字符串转换为数组
            $oids = isset($_POST['oid']) ? explode(',', $_POST['oid']) : [];
            $newpass = trim(strip_tags(daddslashes($_POST['pwd'])));
            // 遍历处理每个订单ID
            foreach ($oids as $oid) {
                $oid = trim(strip_tags(daddslashes($oid)));
                $sql = "UPDATE `qingka_wangke_order` SET `remarks` = '{$newpass}' WHERE `oid` = '{$oid}'";
                if (!$DB->query($sql)) {
                    exit('{"code":-1,"msg":"部分订单修改失败"}');
                }
            }
            // 如果所有订单都成功修改，则返回成功消息
            exit('{"code":1,"msg":"修改成功"}');
        }else{
            exit('{"code":-1,"msg":"无权限！"}');
        }
break;
    // 修改密码
case 'dd_passwd':
        // 获取前端传递的参数，这里使用explode函数将字符串转换为数组
        $oids = isset($_POST['oid']) ? explode(',', $_POST['oid']) : [];
        $newpass = trim(strip_tags(daddslashes($_POST['pwd'])));
        $xgkf = 0.05;//改密扣费
        // 检查新密码是否为空
        if (empty($newpass)) {
            exit(json_encode(array("code" => -1, "msg" => "新密码不能为空")));
        }
        // 遍历处理每个订单ID
        foreach ($oids as $oid) {
            $oid = trim(strip_tags(daddslashes($oid)));
            $row = $DB->get_row("SELECT * FROM qingka_wangke_order WHERE oid = '$oid' ");
            if ($userrow['uid'] != $row['uid'] && $userrow['uid'] != 1) {
                jsonReturn(-1, "该订单不是你的，无法修改！");
            }elseif($newpass==$row['pass']){
                jsonReturn(-1, "新密码不能和旧密码相同！");
            }else{
                $result = gaimiWk($oid, $newpass);
                $result1 = json_decode($result, true);
                // 检查$result['code'] == '1'
                if ($result['code'] == '1') {
                    $sql = "UPDATE qingka_wangke_order SET pass = '{$newpass}' WHERE oid = '{$oid}' ";
                    if (!$DB->query($sql)) {
                        exit(json_encode(array("code" => -1, "msg" => "订单ID: {$oid} 修改失败，无权限或数据库错误",$result)));
                        wlog($userrow['uid'], "修改密码", "订单：【{$row['oid']}】修改密码失败！信息：项目：【{$row['ptname']}】- 账号：【{$row['user']}】-课程：【{$row['kcname']}】-原密码：【{$row['pass']}】-新密码： 【{$newpass}】", 0); // 添加日志记录
                    }
                    $DB->query("update qingka_wangke_user set `money`=`money`-'$xgkf' where uid='{$userrow['uid']}' ");
                    wlog($userrow['uid'], "修改密码", "订单：【{$row['oid']}】修改密码成功！信息：项目：【{$row['ptname']}】- 账号：【{$row['user']}】-课程：【{$row['kcname']}】-原密码：【{$row['pass']}】-新密码： 【{$newpass}】", -$xgkf); // 添加日志记录
                } else {
                    exit(json_encode(array("code" => -1, "msg" =>$result['msg'],$result)));
                }
            }
    }
        // 如果所有订单都成功修改，则返回成功消息
        exit(json_encode(array("code" => 1, "msg" => "修改成功".$result['msg'] ?? $result['message'],$result)));
    break;
    case 'webset':
        parse_str(daddslashes($_POST['data']), $row);
        if ($userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"滚，傻逼！你没妈了？"}');
        } else if ($userrow['uid'] == 1) {
            //var_dump($row);
            foreach ($row as $k => $value) {
                if ($k == 'dklcookie' || $k == 'nanatoken' || $k == 'akcookie' || $k == 'vpercookie') {
                    $value = authcode($value, 'ENCODE', 'qingka');
                }
                $DB->query("UPDATE `qingka_wangke_config` SET k='{$value}' WHERE v='{$k}'");
            }
            exit('{"code":1,"msg":"修改成功"}');
        }
        break;
    case 'szyqm':
        //jsonReturn(-1,"邀请码暂停设置");
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        $yqm = trim(strip_tags(daddslashes($_POST['yqm'])));
        if (strlen($yqm) < 4) {
            jsonReturn(-1, "邀请码最少4位，且必须为数字");
        }
        if (!is_numeric($yqm)) {
            jsonReturn(-1, "请正确输入邀请码，必须为数字");
        }
        if ($DB->get_row("select * from qingka_wangke_user where yqm='$yqm' ")) {
            jsonReturn(-1, "该邀请码已被使用，请换一个");
        }
        $a = $DB->get_row("select * from qingka_wangke_user where uid='$uid' ");
        if ($userrow['uid'] == '1') {
            $DB->query("update qingka_wangke_user set yqm='{$yqm}' where uid='$uid' ");
            wlog($userrow['uid'], "设置邀请码", "给下级设置邀请码{$yqm}成功", '0');
            jsonReturn(1, "设置成功");
            //}elseif($userrow['uid']==$a['uuid'] && $userrow['addprice']=='1'){
            
        } elseif ($userrow['uid'] == $a['uuid']) {
            $DB->query("update qingka_wangke_user set yqm='{$yqm}' where uid='$uid' ");
            wlog($userrow['uid'], "设置邀请码", "给下级设置邀请码{$yqm}成功", '0');
            jsonReturn(1, "设置成功");
        } else {
            jsonReturn(-1, "无权限");
        }
        break;
    case 'yqprice':
        $yqprice = trim(strip_tags(daddslashes($_POST['yqprice'])));
        if (!is_numeric($yqprice)) {
            jsonReturn(-1, "请正确输入费率，必须为数字");
        }
        if ($yqprice < $userrow['addprice']) {
            jsonReturn(-1, "下级默认费率不能比你低哦");
        }
        if ($yqprice > 0.8) {
            jsonReturn(-1, "邀请费率最低设置为0.8");
        }
        if ($yqprice * 100 % 5 != 0) {
            jsonReturn(-1, "邀请费率必须为0.05的倍数");
        }
        if ($userrow['yqm'] == "") {
            $yqm = random(5, 5);
            if ($DB->get_row("select uid from qingka_wangke_user where yqm='$yqm' ")) {
                $yqm = random(6, 5);
            }
            $sql = "yqm='{$yqm}',yqprice='$yqprice'";
        } else {
            $sql = "yqprice='$yqprice'";
        }
        $DB->query("update qingka_wangke_user set {$sql} where uid='{$userrow['uid']}' ");
        jsonReturn(1, "设置成功");
        break;
    case 'wlogin':
        jsonReturn(1, "不写也罢");
        if ($userrow['uid'] == 1) {
            $uid = daddslashes($_POST['uid']);
            $row = $DB->get_row("SELECT * FROM qingka_wangke_user WHERE uid='$uid' limit 1");
            $session = md5($user . $pass . $password_hash);
            $token = authcode("{$user}\t{$session}", 'ENCODE', SYS_KEY);
            setcookie("admin_token", $token, time() + 3000);
            exit('{"code":1,"msg":"登录成功1"}');
        } else {
            jsonReturn(-1, "你在干啥？");
        }
        break;
    case 'userinfo':
        if ($islogin != 1) {
            exit('{"code":-10,"msg":"请先登录"}');
        }
        $a = $DB->get_row("select uid,user,notice from qingka_wangke_user where uid='{$userrow['uuid']}' ");
        $dd = $DB->count("select count(oid) from qingka_wangke_order where uid='{$userrow['uid']}' ");
        //$zcz=$DB->count("select sum(money) as money from qingka_wangke_log where type='上级充值' and uid='{$userrow['uid']}' ");
        //安全验证1
        if ($userrow['addprice'] < 0.1) {
            $DB->query("update qingka_wangke_user set addprice='1' where uid='{$userrow['uid']}' ");
            jsonReturn(-9, "大佬，我得罪不起您啊，有什么做的不好的地方尽管提出来，我小本生意，经不起折腾，还望多多包涵");
        }
        //安全验证2
        if ($userrow['uid'] != 1) {
            if ((int)$userrow['money'] - (int)'0.1' > (int)$userrow['zcz']) {
                $DB->query("update qingka_wangke_user set money='$zcz',active='0' where uid='{$userrow['uid']}' ");
                jsonReturn(-9, "账号异常，请联系你老大");
            }
        }
        //代理数据统计
        $dlzs = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' ");
        $dldl = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and endtime>'$jtdate' ");
        $dlzc = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and addtime>'$jtdate' ");
        $jrjd = $DB->count("select count(uid) from qingka_wangke_order where uid='{$userrow['uid']}' and addtime>'$jtdate' ");
        //       while($dllist2=$DB->fetch($DB->query("select uid from qingka_wangke_user where uuid='{$userrow['uid']}'"))){
        //       	  $dlxd+=$DB->count("select count(oid) from qingka_wangke_order where uid='{$ddlist2['uid']}' and addtime>'$jtdate' ");
        //       }
        //$dlxd="emmmmmm";
        $dailitongji = array('dlzc' => $dlzc, 'dldl' => $dldl, 'dlxd' => $dlxd, 'dlzs' => $dlzs, 'jrjd' => $jrjd);
        $data = array('code' => 1, 'msg' => '查询成功', 'uid' => $userrow['uid'], 'name' => $userrow['name'], 'user' => $userrow['user'], 'money' => round($userrow['money'], 2), 'cdmoney' => round($userrow['cdmoney'], 2),'addprice' => $userrow['addprice'], 'key' => $userrow['key'], 'sjuser' => $a['user'], 'dd' => $dd, 'zcz' => $userrow['zcz'], 'yqm' => $userrow['yqm'], 'yqprice' => $userrow['yqprice'], 'notice' => $conf['notice'], 'sjnotice' => $a['notice'],'email' => $userrow['email'], 'wxpushertoken' => $userrow['tuisongtoken'],'dailitongji' => $dailitongji);
        exit(json_encode($data));
        break;
    case 'ktkh':
	  $uid=trim(strip_tags(daddslashes($_GET['uid'])));
	  if($userrow['uid']==1){
	  		if($uid==""){
	  			jsonReturn(-2,"uid不能为空");
	  		}
	  		$u = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1 ");
	  		if($u['khcz']==1){
	  		    $DB->query("update qingka_wangke_user set `khcz`='0' where uid='{$uid}' ");
    			wlog($userrow['uid'],"关闭跨户","给下级代理UID{$uid}关闭跨户充值权限成功!",'0');
    			wlog($uid,"关闭跨户","已关闭您的跨户充值权限!",'0');
    			exit('{"code":1,"msg":"关闭成功"}');
	  		}else{
	  		    $DB->query("update qingka_wangke_user set `khcz`='1' where uid='{$uid}' ");
    			wlog($userrow['uid'],"关闭跨户","给下级代理UID{$uid}开通跨户充值权限成功!",'0');
    			wlog($uid,"开启跨户","已开启您的跨户充值权限!",'0');
		        exit('{"code":1,"msg":"开通成功"}');
	  		}
	  }else{
      jsonReturn(-2,"无权限！");}
	break;
	case 'ktapi':
	  $type=trim(strip_tags(daddslashes($_GET['type'])));
	  $uid=trim(strip_tags(daddslashes($_GET['uid'])));
	  $key=random(16);
	  if($type==1){//自我开通		  
		   if($userrow['money']<300){
		   	 if($userrow['money']>=10){
		   	  $DB->query("update qingka_wangke_user set `key`='$key',`money`=`money`-10 where uid='{$userrow['uid']}' ");
		   	  wlog($userrow['uid'],"开通接口","开通接口成功!扣费10积分",'-10');
		   	  exit('{"code":1,"msg":"花费10积分开通接口成功","key":"'.$key.'"}');
		   	 }else{
		   	 	exit('{"code":-1,"msg":"余额不足"}');
		   	  }
		   }else{	   	
			   	  $DB->query("update qingka_wangke_user set `key`='$key' where uid='{$userrow['uid']}' ");
			   	  wlog($userrow['uid'],"开通接口","免费开通接口成功!",'0');
			   	  exit('{"code":1,"msg":"免费开通成功","key":"'.$key.'"}');
		   }
	  }elseif($type==2){
	  	if($userrow['money']<5){
	  		wlog($userrow['uid'],"开通接口","尝试给下级UID{$uid}开通接口失败! 原因：余额不足",'0');
	  		jsonReturn(-2,"余额不足以开通");	  		
	  	}else{
	  		if($uid==""){
	  			jsonReturn(-2,"uid不能为空");
	  		}
	  		$DB->query("update qingka_wangke_user set `key`='$key' where uid='{$uid}' ");
	  		$DB->query("update qingka_wangke_user set `money`=`money`-5 where uid='{$userrow['uid']}' ");		   	 
			wlog($userrow['uid'],"开通接口","给下级代理UID{$uid}开通接口成功!扣费5积分",'-5');
			wlog($uid,"开通接口","你上级给你开通API接口成功!",'0');
		    exit('{"code":1,"msg":"花费5积分开通成功"}');
	  	}  	
	  }elseif($type==3){
	      if ($userrow['key']=="0") {
	           exit('{"code":-1,"msg":"请先开通key""}');
	      }elseif ($userrow['key']!="") {
	      $DB->query("update qingka_wangke_user set `key`='$key' where uid='{$userrow['uid']}' ");
		   	  wlog($userrow['uid'],"开通接口","更换接口{$key}成功",'0');
		   	  exit('{"code":1,"msg":"更换成功","key":"'.$key.'"}');
	      }
	 
	  }elseif($type==4){
	      if ($userrow['key']=="0") {
	           exit('{"code":-1,"msg":"请先开通key""}');
	      }elseif ($userrow['key']!="") {
	      $DB->query("update qingka_wangke_user set `key`='0' where uid='{$userrow['uid']}' ");
		   	  wlog($userrow['uid'],"关闭接口","关闭接口{$key}成功",'0');
		   	  exit('{"code":1,"msg":"关闭成功","key":"'.$key.'"}');
	      }
	 
	  }
      jsonReturn(-2,"未知异常");
	break;
    case 'get':
        $cid = trim(strip_tags(daddslashes($_POST['cid'])));
        $userinfo = daddslashes($_POST['userinfo']);
        $hash = daddslashes($_POST['hash']);
        $rs = $DB->get_row("select * from qingka_wangke_class where cid='$cid' limit 1 ");
        $kms = str_replace(array("\r\n", "\r", "\n"), "[br]", $userinfo);
        $info = explode("[br]", $kms);
        $key = 'AES_Encryptwords';
        $iv = '0123456789abcdef';
        $hash = openssl_decrypt($hash, 'aes-128-cbc', $key, 0, $iv);
        if ((empty($_SESSION['addsalt']) || $hash != $_SESSION['addsalt'])) {
            exit('{"code":-1,"msg":"验证失败，请刷新页面重试"}');
        }
        for ($i = 0;$i < count($info);$i++) {
            $str = merge_spaces(trim($info[$i]));
            $userinfo2 = explode(" ", $str);
            //分割
            if (count($userinfo2) > 2) {
                $result = getWk($rs['queryplat'], $rs['getnoun'], trim($userinfo2[0]), trim($userinfo2[1]), trim($userinfo2[2]), $rs['name']);
            } else {
                $result = getWk($rs['queryplat'], $rs['getnoun'], "自动识别", trim($userinfo2[0]), trim($userinfo2[1]), $rs['name']);
            }
            $userinfo3 = trim($userinfo2[0] . " " . $userinfo2[1] . " " . $userinfo2[2]);
            $result['userinfo'] = $userinfo3;
            $DB->query("update qingka_wangke_user set `todayck`=todayck+1 where uid='{$userrow['uid']}' limit 1 ");
            wlog($userrow['uid'], "查课", "{$rs['name']}-查课信息：{$userinfo3}", 0);
        }
        exit(json_encode($result));
        break;
    case 'pay':
        $zdpay = $conf['zdpay'];
        $money = trim(strip_tags(daddslashes($_POST['money'])));
        $name = "零食购买-" . $money . "";
        if (!preg_match('/^[0-9.]+$/', $money)) exit('{"code":-1,"msg":"订单金额不合法"}');
        if ($money < $zdpay) {
            jsonReturn(-1, "在线充值最低{$zdpay}元");
        }
        $row = $DB->get_row("select * from qingka_wangke_user where uid='{$userrow['uuid']}' ");
        if ($row['uid'] == '1') {
            $out_trade_no = date("YmdHis") . rand(111, 999);
            //生成本地订单号
            $wz = $_SERVER['HTTP_HOST'];
            $sql = "insert into `qingka_wangke_pay` (`out_trade_no`,`uid`,`num`,`name`,`money`,`ip`,`addtime`,`domain`,`status`) values ('" . $out_trade_no . "','" . $userrow['uid'] . "','" . $money . "','" . $name . "','" . $money . "','" . $clientip . "','" . $date . "','" . $wz . "','0')";
            if ($DB->query($sql)) {
                exit('{"code":1,"msg":"生成订单成功！","out_trade_no":"' . $out_trade_no . '","need":"' . $money . '"}');
            } else {
                exit('{"code":-1,"msg":"生成订单失败！' . $DB->error() . '"}');
            }
        } else {
            jsonReturn(-1, "请您根据上面的信息联系上家充值。");
        }
        break;
    case 'getclass_pl':
        $a=$DB->query("select * from qingka_wangke_class where  wck=1 order by sort desc");
        while ($row = $DB->fetch($a)) {
            if ($row['yunsuan'] == "*") {
                $price = round($row['price'] * $userrow['addprice'], 2);
                $price1 = $price;
            } elseif ($row['yunsuan'] == "+") {
                $price = round($row['price'] + $userrow['addprice'], 2);
                $price1 = $price;
            } else {
                $price = round($row['price'] * $userrow['addprice'], 2);
                $price1 = $price;
            }
            //密价
            $mijia = $DB->get_row("select * from qingka_wangke_mijia where uid='{$userrow['uid']}' and cid='{$row['cid']}' ");
            if ($mijia) {
                if ($mijia['mode'] == 0) {
                    $price = round($price - $mijia['price'], 2);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 1) {
                    $price = round(($row['price'] - $mijia['price']) * $userrow['addprice'], 2);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 2) {
                    $price = $mijia['price'];
                    if ($price <= 0) {
                        $price = 0;
                    }
                }
                $row['name'] = "【密价】{$row['name']}";
            }
            if ($price >= $price1) {
                //密价价格大于原价，恢复原价
                $price = $price1;
            }
            $data[] = array('sort' => $row['sort'], 'cid' => $row['cid'], 'name' => $row['name'], 'noun' => $row['noun'], 'price' => $price, 'content' => $row['content'], 'status' => $row['status'], 'miaoshua' => $miaoshua);
        }
        foreach ($data as $key => $row) {
            $sort[$key] = $row['sort'];
            $cid[$key] = $row['cid'];
            $name[$key] = $row['name'];
            $noun[$key] = $row['noun'];
            $price[$key] = $row['price'];
            $info[$key] = $row['info'];
            $content[$key] = $row['content'];
            $status[$key] = $row['status'];
            $miaoshua[$key] = $row['miaoshua'];
        }
        array_multisort($sort, SORT_ASC, $cid, SORT_DESC, $data);
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));
        break;
    case 'add':
        $cid = trim(strip_tags(daddslashes($_POST['cid'])));
        $data = daddslashes($_POST['data']);
        $clientip = real_ip();
        $rs = $DB->get_row("select * from qingka_wangke_class where cid='$cid' limit 1 ");
        if ($cid == '' || $data == '') {
            exit('{"code":-1,"msg":"请选择课程"}');
        }
        if ($rs['yunsuan'] == "*") {
            $danjia = round($rs['price'] * $userrow['addprice'], 2);
        } elseif ($rs['yunsuan'] == "+") {
            $danjia = round($rs['price'] + $userrow['addprice'], 2);
        } else {
            $danjia = round($rs['price'] * $userrow['addprice'], 2);
        }
        //密价
        $mijia = $DB->get_row("select * from qingka_wangke_mijia where uid='{$userrow['uid']}' and cid='$cid' ");
        if ($mijia) {
            if ($mijia['mode'] == 0) {
                $danjia = round($danjia - $mijia['price'], 2);
                if ($danjia <= 0) {
                    $danjia = 0;
                }
            } elseif ($mijia['mode'] == 1) {
                $danjia = round(($rs['price'] - $mijia['price']) * $userrow['addprice'], 2);
                if ($danjia <= 0) {
                    $danjia = 0;
                }
            } elseif ($mijia['mode'] == 2) {
                // 如果密价大于等于原单价，则保持原单价不变
                if ($mijia['price'] >= $danjia) {
                    // 不修改单价
                } else {
                    $danjia = $mijia['price'];
                    if ($danjia <= 0) {
                        $danjia = 0;
                    }
                }
            }
        }
        if ($danjia == 0 || $userrow['addprice'] < 0.2) {
            exit('{"code":-1,"msg":"大佬，我得罪不起您，我小本生意，有哪里得罪之处，还望多多包涵"}');
        }
        $money = count($data) * $danjia;
        if ($userrow['money'] < $money) {
            exit('{"code":-1,"msg":"余额不足"}');
        }
        foreach ($data as $row) {
            $userinfo = $row['userinfo'];
            $userName = $row['userName'];
            $userinfo = explode(" ", $userinfo);
            //分割账号密码
            if (count($userinfo) > 2) {
                $school = $userinfo[0];
                $user = $userinfo[1];
                $pass = $userinfo[2];
            } else {
                $school = "自动识别";
                $user = $userinfo[0];
                $pass = $userinfo[1];
            }
            $kcid = $row['data']['id'];
            $kcname = $row['data']['name'];
            $kcjs = $row['data']['kcjs'];
            //90天内的重复订单
            $duplicateOrder = $DB->get_row("SELECT * FROM qingka_wangke_order WHERE cid='{$rs['cid']}' AND ptname='{$rs['name']}' AND school='$school' AND user='$user' AND pass='$pass' AND kcid='$kcid' AND kcname='$kcname' and addtime >= CURDATE() - INTERVAL 100 DAY");
            $TIME = (new DateTime($duplicateOrder['addtime']))->modify('+90 days')->format('Y-m-d H:i:s');
            if ($duplicateOrder) {
                // 重复下单
                $errMsg = '存在重复订单，请检查后重试！ 重复账号：' . $duplicateOrder['user'] .'  '. $duplicateOrder['pass']. '  重复课程：' . $duplicateOrder['kcname'] .'，过期时间：'.$TIME;
                exit('{"code": -1, "msg": "' . $errMsg . '"}');
            }elseif ($rs['docking'] == 0) {
                $dockstatus = '99';
            } else {
                $dockstatus = '0';
            }
            $is = $DB->query("insert into qingka_wangke_order (uid,cid,fenlei,hid,ptname,school,name,user,pass,kcid,kcname,courseEndTime,fees,noun,miaoshua,addtime,ip,dockstatus) values ('{$userrow['uid']}','{$rs['cid']}','{$rs['fenlei']}','{$rs['docking']}','{$rs['name']}','{$school}','$userName','$user','$pass','$kcid','$kcname','{$kcjs}','{$danjia}','{$rs['noun']}','$miaoshua','$date','$clientip','$dockstatus') ");
            //将对应课程写入数据库
            if ($is) {
                $DB->query("update qingka_wangke_user set money=money-'{$danjia}',`todayadd`=todayadd+1 where uid='{$userrow['uid']}' limit 1 ");
                wlog($userrow['uid'], "添加任务", "  {$rs['name']} {$user} {$pass} {$kcname} 扣除{$danjia}元！", -$danjia);
            }
        }
        if ($is) {
            // 获取订单数量和扣除的金额
            $orderCount = count($data);
            $deductedAmount = $money;
            $msg = '提交成功，共' . $orderCount . '条任务，扣除' . $deductedAmount . '币';
            exit('{"code":1,"msg":"' . $msg . '","orderCount":'.$orderCount.',"deductedAmount":'.$deductedAmount.'}');}
            else {
            exit('{"code":-1,"msg":"提交失败"}');
        }
        break;
    case 'bs':
        $oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $b = $DB->get_row("select hid,cid,dockstatus from qingka_wangke_order where oid='{$oid}' ");
        $a = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
        $info = "{$a['school']} {$a['user']} {$a['pass']} {$a['kcname']}";
        wlog($userrow['uid'], "订单补刷", "用户补刷了订单 {$info}", 0);
        if ($b['dockstatus'] == '99') {
            jsonReturn(1, "成功加入线程，排队补刷中");
        }/*/elseif ($a['bsnum'] >= '10') {
            jsonReturn(-1, "该订单补刷超过10次，已限制补刷");
        }/*/ else {
            $b = budanWk($oid);
            if ($b['code'] >= 0) {
                $DB->query("UPDATE qingka_wangke_order SET status='补刷中', remarks='补刷成功，等待进度更新。上次补刷成功时间：$today_day', bsnum=bsnum+1 WHERE oid='{$oid}' ");
                jsonReturn(1, $b['msg'] ?? $b['message']);
            } else {
                $DB->query("UPDATE qingka_wangke_order SET status='补刷失败', remarks='补刷失败，请重试！上次补刷失败时间：$today_day' WHERE oid='{$oid}' ");
                jsonReturn(-1, $b['msg'] ?? $b['message']);
            }
        }
        break;
    case 'uporder45'://进度刷新
           $oid=trim(strip_tags(daddslashes($_GET['oid'])));
           $row=$DB->get_row("select * from qingka_wangke_order where oid='$oid'");
           if($row['hid']=='ximeng'){
             	exit('{"code":-2,"msg":"当前订单接口异常，请去查询补单","url":""}');
           }elseif($row['dockstatus']=='99'){
           	    //$result=pre_zy($oid);
           	    //exit(json_encode($result));
           	    jsonReturn(1,'实时进度无需更新');
           }       	     
    	       $result=processCx($oid);
    	       for($i=0;$i<count($result);$i++){
    	        	$DB->query("update qingka_wangke_order set `name`='{$result[$i]['name']}',`yid`='{$result[$i]['yid']}',`status`='{$result[$i]['status_text']}',`courseStartTime`='{$result[$i]['kcks']}',`courseEndTime`='{$result[$i]['kcjs']}',`examStartTime`='{$result[$i]['ksks']}',`examEndTime`='{$result[$i]['ksjs']}',`process`='{$result[$i]['process']}',`remarks`='{$result[$i]['remarks']}' where `user`='{$result[$i]['user']}' and `kcname`='{$result[$i]['kcname']}' and `oid`='{$oid}'");
    	       }
    	       exit('{"code":1,"msg":"同步成功"}');
        break;
    case 'uporder'://进度刷新
           $oid=trim(strip_tags(daddslashes($_GET['oid'])));
           $row=$DB->get_row("select * from qingka_wangke_order where oid='$oid'");
           $rows=$DB->get_row("select * from qingka_wangke_user where uid='{$row['uid']}'");
           if($row['dockstatus']=='99'){
           	    //$result=pre_zy($oid);
           	    //exit(json_encode($result));
           	    jsonReturn(1,'实时进度无需更新');
           }       	     
           if($row['status']=='已退款' || $row['status']=='已退单' || $row['dockstatus']=='4'){
           	    //$result=pre_zy($oid);
           	    //exit(json_encode($result));
           	    jsonReturn(-1,'订单已退款，无法更新');
           }
    	       $result=processCx($oid);
            // 遍历 $result 数组，执行更新操作
            for ($i = 0; $i < count($result); $i++) {
                // 获取需要更新的字段值并进行转义
                $name = $DB->escape($result[$i]['name']);
                $yid = $DB->escape($result[$i]['yid']);
                $status = $DB->escape($result[$i]['status_text']);
                $courseStartTime = $DB->escape($result[$i]['kcks']);
                $courseEndTime = $DB->escape($result[$i]['kcjs']);
                $examStartTime = $DB->escape($result[$i]['ksks']);
                $examEndTime = $DB->escape($result[$i]['ksjs']);
                $process = $DB->escape($result[$i]['process']);
                $remarks = $DB->escape($result[$i]['remarks']);
                $user = $DB->escape($result[$i]['user']);
                $kcname = $DB->escape($result[$i]['kcname']);
                $oid = $DB->escape($oid); // Assuming $oid is defined and should also be escaped
                
                /*/// 设定需要匹配的关键词列表
                $keywords = ['考试未完成', '已打开考试', '可能时间耗尽', '已经提交','低于', '章测未做完'];
                
                // 构建正则表达式，用来匹配任意关键词（去掉 preg_quote）
                $pattern = '/' . implode('|', $keywords) . '/';

                // 使用 preg_match 进行关键词匹配
                if (preg_match($pattern, $remarks)) {
                    if (!empty($rows['email'])) {
                        $toEmail = $rows['email'];
                        $toName = 'FreeDomQA';
                        $templateId = 'xmjg'; 
                        $data = [
                            'ptname' => $row['ptname'], 
                            'user' => $row['user'], 
                            'remarks' => $remarks
                        ];
                        $result1 = sendEmail($toEmail, $toName, $templateId, $data);
                        if ($result1) {
                            wlog($row['uid'], "邮箱提醒", "平台：{$row['ptname']} 订单账号：{$row['user']} 订单课程：{$row['kcname']} ", 0);
                        } else {
                            wlog($row['uid'], "邮箱提醒", "{$result1}", 0);
                        }
                    }
                    if(!empty($rows['tuisongtoken'])){
                        $message = "注意！订单可能存在异常！请尽快查看！订单账号：".$row['user'];
                        $messageEncoded = urlencode($message);
                        $spt = urlencode($rows['tuisongtoken']);
                        $apiUrl = "https://wxpusher.zjiecode.com/api/send/message/$spt/$messageEncoded";
                        $response = file_get_contents($apiUrl);
                        wlog($row['uid'], "WX提醒", "平台：{$row['ptname']} 订单账号：{$row['user']} 订单课程：{$row['kcname']} ", 0);
                        }
                }/*/
                // 构建 SQL 查询语句
                $sql = "UPDATE qingka_wangke_order 
                        SET `name` = '{$name}', 
                            `yid` = '{$yid}', 
                            `status` = '{$status}', 
                            `courseStartTime` = '{$courseStartTime}', 
                            `courseEndTime` = '{$courseEndTime}', 
                            `examStartTime` = '{$examStartTime}', 
                            `examEndTime` = '{$examEndTime}', 
                            `process` = '{$process}', 
                            `remarks` = '{$remarks}' 
                            WHERE `user` = '{$user}' 
                            AND `kcname` = '{$kcname}' 
                            AND `oid` = '{$oid}'";
            
                // 执行 SQL 查询
                $dbresult = $DB->query($sql);
            
                // 检查更新操作是否成功
                if ($dbresult === false) {
                    // 更新操作失败，输出错误信息并终止程序
                    $error = $DB->error();
                    exit(json_encode(array("code" => 0, "msg" => "数据库更新失败：" . $error)));
                }
            }if($userrow['uid']==1){
                exit(json_encode(array("code" => 1, "msg" => "更新成功！",$result)));
            }else{
                exit(json_encode(array("code" => 1, "msg" => "更新成功！")));
            }
        break;
    case 'uporderkc': //kc进度刷新
        $oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $row = $DB->get_row("select * from qingka_wangke_order where oid='$oid'");
        if ($row['dockstatus'] == '99') {
            $result = pre_zy($oid);
            exit(json_encode($result));
        }
        $result = processCx($oid);
        $newResult = array();
        for ($i = 0; $i < count($result); $i++) {
            $currentKcname = $result[$i]['kcname'];
            // 如果新结果数组中已经包含相同课程名称的数据，跳过当前循环
            if (in_array($currentKcname, array_column($newResult, 'kcname'))) {
                continue;
            }
            // 将当前数据添加到新结果数组中
            $newResult[] = $result[$i];
            // 更新数据库中对应的数据
            $DB->query("update qingka_wangke_order set `name`='{$result[$i]['name']}',`jindu`='{$result[$i]['jindu']}',`yid`='{$result[$i]['yid']}',`status`='{$result[$i]['status_text']}',`courseStartTime`='{$result[$i]['kcks']}',`courseEndTime`='{$result[$i]['kcjs']}',`examStartTime`='{$result[$i]['ksks']}',`examEndTime`='{$result[$i]['ksjs']}',`process`='{$result[$i]['process']}',`remarks`='{$result[$i]['remarks']}' where `user`='{$result[$i]['user']}' and `kcname`='{$result[$i]['kcname']}' and `oid`='{$oid}'");
        }
    
        exit(json_encode(array("code" => 1, "msg" => "更新成功",$result)));
        break;
    case 'ms_order': //列表提交秒刷
        $oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $msg = $row['ptname'] . "不支持提交秒刷";
        exit('{"code":-1,"msg":"' . $msg . '"}');
        break;
    case 'qx_order': //取消订单
        $oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $row = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
        if ($row['uid'] != $userrow['uid'] && $userrow['uid'] != 1) {
            jsonReturn(-1, "无权限");
        } else {
            $DB->query("update qingka_wangke_order set `status`='已取消',`dockstatus`=4 where oid='$oid' ");
            jsonReturn(1, "取消成功");
        }
        break;
    case 'FDorderlist':
        $cx = daddslashes($_POST['cx']);
        $limit = $cx['limit'];
        if ($limit == "") {
            $pagesize = 25;
        } else {
            $pagesize = $limit;
        }
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $pageu = ($page - 1) * $pagesize;
        //当前界面
        $qq = trim(strip_tags(daddslashes($cx['qq'])));
        $status_text = trim(strip_tags(daddslashes($cx['status_text'])));
        $dock = trim(strip_tags(daddslashes($cx['dock'])));
        $cid = trim(strip_tags(daddslashes($cx['cid'])));
        $oid = trim(strip_tags(daddslashes($cx['oid'])));
        $uid = trim(strip_tags(daddslashes($cx['uid'])));
        $school = trim(strip_tags(daddslashes($cx['school'])));
        $kcname = trim(strip_tags(daddslashes($cx['kcname'])));
        $mh = trim(strip_tags(daddslashes($cx['mh'])));
        $search = trim(strip_tags(daddslashes($cx['search'])));
        if ($userrow['uid'] != '1') {
            $sql1 = "where uid='{$userrow['uid']}'";
        } else {
            $sql1 = "where 1=1";
        }
        if ($search === 'process' && $mh !== '') {
            //$sql2 = " and process='".$mh."'";
            $sql2 = " AND CAST(SUBSTRING(process, 1, LENGTH(process) - 1) AS DECIMAL) < '".$mh."'";
        }
        if ($search === 'user' && $mh !== '') {
            $sql3 = " and user='".$mh."'";
        }
        if ($cid != '') {
            $sql4 = " and cid='{$cid}'";
        }
        if ($search === 'uid' && $mh !== '') {
            $sql5 = " and uid='".$mh."'";
        }
        if ($status_text != '') {
            $sql6 = " and status='{$status_text}'";
        }
        if ($dock != '') {
            $sql7 = " and dockstatus='{$dock}'";
        }
          if ($search === 'school'  && $mh !== '') {
            $sql8 = " and school='".$mh."'";
        }
          if ($search === 'kcname' && $mh !== '') {
            $sql9 = " and kcname='".$mh."'";
        }
          if ($search === 'remarks' && $mh !== '') {
            $sql9 = " and remarks='".$mh."'";
        }
        if ($search === 'oid' && $mh !== '') {
            $sql10 = " and oid='".$mh."'";
        }
            $sql11 = "";
            if ($search === '' && $mh !== '') {
                $sql11 = " and (uid LIKE '%" . $mh . "%' OR ptname LIKE '%" . $mh . "%' OR school LIKE '%" . $mh . "%' OR user LIKE '%" . $mh . "%' OR pass LIKE '%" . $mh . "%' OR kcname LIKE '%" . $mh . "%' OR process LIKE '%" . $mh . "%' OR remarks LIKE '%" . $mh . "%' OR fees LIKE '%" . $mh . "%' OR oid LIKE '%" . $mh . "%' OR status LIKE '%" . $mh . "%')";
        }
        $sql = $sql1 . $sql2 . $sql3 . $sql4 . $sql5 . $sql6 . $sql7 . $sql8 . $sql9 . $sql10 . $sql11;
        $a = $DB->query("select * from qingka_wangke_order {$sql} order by oid desc limit $pageu,$pagesize ");
        $count1 = $DB->count("select count(*) from qingka_wangke_order {$sql} ");
        while ($row = $DB->fetch($a)) {
            if ($row['name'] == '' || $row['name'] == 'undefined') {
                $row['name'] = 'null';
            }
            $data[] = $row;
        }
        $last_page = ceil($count1 / $pagesize);
        //取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page, "uid" => (int)$userrow['uid']);
        exit(json_encode($data));
        break;
    case 'FDclasslist':
        $cx = daddslashes($_POST['cx']);
        $limit = $cx['limit'];
        if ($limit == "") {
            $pagesize = 25;
        } else {
            $pagesize = $limit;
        }
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $pageu = ($page - 1) * $pagesize;
                $pagesize = 50;
        $pageu = ($page - 1) * $pagesize;
        $qq = trim(strip_tags($cx['qq']));
        $status_text = trim(strip_tags($cx['status_text']));
        $dock = trim(strip_tags($cx['dock']));
        $cid = trim(strip_tags($cx['cid']));
        $oid = trim(strip_tags($cx['oid']));
        $uid = trim(strip_tags($cx['uid']));
        $school = trim(strip_tags($cx['school']));
        $kcname = trim(strip_tags($cx['kcname']));
        $mh = trim(strip_tags(daddslashes($cx['mh'])));
        $search = trim(strip_tags(daddslashes($cx['search'])));

        //当前界面
        $count1 = $DB->count("select count(*) from qingka_wangke_class");
        $last_page = ceil($count1 / $pagesize);
        //取最大页数
        if ($userrow['uid'] == '1') {
            $a = $DB->query("select * from qingka_wangke_class limit $pageu,$pagesize ");
            while ($row = $DB->fetch($a)) {
                $c = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$row['queryplat']}' ");
                $d = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$row['docking']}' ");
                $f = $DB->get_row("select * from qingka_wangke_fenlei where id='{$row['fenlei']}' ");
                $row['cx_name'] = $c['name'];
                $row['add_name'] = $d['name'];
                $row['fenlei'] = $f['name'];
                if ($row['queryplat'] == '0') {
                    $row['cx_name'] = '自营';
                }
                if ($row['docking'] == '0') {
                    $row['add_name'] = '自营';
                }
                $data[] = $row;
            }
            foreach ($data as $key => $rows) {
                $sort[$key] = $rows['sort'];
                $cid[$key] = $rows['cid'];
                $name[$key] = $rows['name'];
                $getnoun[$key] = $rows['getnoun'];
                $noun[$key] = $rows['noun'];
                $price[$key] = $rows['price'];
                $queryplat[$key] = $rows['queryplat'];
                $yunsuan[$key] = $rows['yunsuan'];
                $content[$key] = $rows['content'];
                $addtime[$key] = $rows['addtime'];
                $status[$key] = $rows['status'];
                $cx_names[$key] = $rows['cx_names'];
                $add_name[$key] = $rows['add_name'];
            }
            array_multisort($sort, SORT_ASC, $cid, SORT_DESC, $data);
            $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page);
            exit(json_encode($data));
        } else {
            exit('{"code":-2,"msg":"你在干啥"}');
        }
        break;
        case 'user_orderlist':
            $cx = daddslashes($_POST['cx']);
            $limit = $cx['limit'];
            if ($limit == "") {
                $pagesize = 25;
            } else {
                $pagesize = $limit;
            }
            $page = trim(strip_tags(daddslashes($_POST['page'])));
            $pageu = ($page - 1) * $pagesize;
            // 当前界面
            $qq = trim(strip_tags(daddslashes($cx['qq'])));
            $status_text = trim(strip_tags(daddslashes($cx['status_text'])));
            $dock = trim(strip_tags(daddslashes($cx['dock'])));
            $cid = trim(strip_tags(daddslashes($cx['cid'])));
            $oid = trim(strip_tags(daddslashes($cx['oid'])));
            $uid = trim(strip_tags(daddslashes($cx['uid'])));
            $school = trim(strip_tags(daddslashes($cx['school'])));
            $kcname = trim(strip_tags(daddslashes($cx['kcname'])));
            $mh = trim(strip_tags(daddslashes($cx['mh'])));
            $search = trim(strip_tags(daddslashes($cx['search'])));
            // SQL查询条件构建
            $sql1 = "where uid='{$userrow['uid']}'";
            if ($search === 'process' && $mh !== '') {
                    $sql2 = " and process='".$mh."'";
                }
                if ($search === 'user' && $mh !== '') {
                    $sql3 = " and user='".$mh."'";
                }
                if ($cid != '') {
                    $sql4 = " and cid='{$cid}'";
                }
                if ($search === 'uid' && $mh !== '') {
                    $sql5 = " and uid='".$mh."'";
                }
                if ($status_text != '') {
                    $sql6 = " and status='{$status_text}'";
                }
                if ($dock != '') {
                    $sql7 = " and dockstatus='{$dock}'";
                }
                  if ($search === 'school'  && $mh !== '') {
                    $sql8 = " and school='".$mh."'";
                }
                  if ($search === 'kcname' && $mh !== '') {
                    $sql9 = " and kcname='".$mh."'";
                }
                  if ($search === 'remarks' && $mh !== '') {
                    $sql9 = " and remarks='".$mh."'";
                }
                    $sql10 = "";
                    if ($search === '' && $mh !== '') {
                        $sql10 = " and (uid LIKE '%" . $mh . "%' OR ptname LIKE '%" . $mh . "%' OR school LIKE '%" . $mh . "%' OR user LIKE '%" . $mh . "%' OR pass LIKE '%" . $mh . "%' OR kcname LIKE '%" . $mh . "%' OR process LIKE '%" . $mh . "%' OR remarks LIKE '%" . $mh . "%' OR fees LIKE '%" . $mh . "%' )";
                }
                $sql12="and addtime >= CURDATE() - INTERVAL 100 DAY";
                $sql = $sql1 . $sql2 . $sql3 . $sql4 . $sql5 . $sql6 . $sql7 . $sql8 . $sql9 . $sql10 . $sql12;
            
            // 执行数据库查询
            $a = $DB->query("select * from qingka_wangke_order {$sql} order by oid desc limit $pageu,$pagesize ");
            $count1 = $DB->count("select count(*) from qingka_wangke_order {$sql} ");
            
            // 检查查询结果的uid是否等于当前用户的uid
            while ($row = $DB->fetch($a)) {
                if ($row['uid'] != $userrow['uid']) {
                    exit('{"code":-1,"msg":"你在干啥？""}');
                }
                if ($row['name'] == '' || $row['name'] == 'undefined') {
                    $row['name'] = 'null';
                }
                $data[] = $row;
            }
            
            $last_page = ceil($count1 / $pagesize);
            
            // 取最大页数
            $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page, "uid" => (int)$userrow['uid']);
            exit(json_encode($data));
            break;
        case 'user_orderlist1':
            $cx = daddslashes($_POST['cx']);
            $limit = $cx['limit'];
            if ($limit == "") {
                $pagesize = 25;
            } else {
                $pagesize = $limit;
            }
            $page = trim(strip_tags(daddslashes($_POST['page'])));
            $pageu = ($page - 1) * $pagesize;
            // 当前界面
            $qq = trim(strip_tags(daddslashes($cx['qq'])));
            $cid = trim(strip_tags(daddslashes($cx['cid'])));
            $mh = trim(strip_tags(daddslashes($cx['mh'])));
            $search = trim(strip_tags(daddslashes($cx['search'])));
            // SQL查询条件构建
            $sql1 = "where 1=1 ";
                if ($cid != '') {
                    $sql2 = " and cid='{$cid}'";
                }
                  if ($search === 'kcname' && $mh !== '') {
                    $sql4 = " and kcname='".$mh."'";
                }
                    $sql5 = "";
                if ($search === '' && $mh !== '') {
                    $sql5 = " and (ptname LIKE '%" . $mh . "%' OR school LIKE '%" . $mh . "%' OR kcname LIKE '%" . $mh . "%' ) and status = '已完成'";
                }
                if ($cx['kcname'] != '') {
                    $sql7 = " and kcname='{$cx['kcname']}'";
                }
                $sql6 = " and status = '已完成'";
                $sql12="and addtime >= CURDATE() - INTERVAL 1000 DAY";
                $sql = $sql1 . $sql2  . $sql4 . $sql5 . $sql6 . $sql7 . $sql12;
            
            // 执行数据库查询
            $a = $DB->query("SELECT ptname, kcname, status, process, remarks, addtime FROM qingka_wangke_order {$sql} ORDER BY oid DESC LIMIT $pageu, $pagesize");
            $count1 = $DB->count("select count(*) from qingka_wangke_order {$sql} ");
            
            // 检查查询结果的uid是否等于当前用户的uid
            while ($row = $DB->fetch($a)) {
                if ($row['name'] == '' || $row['name'] == 'undefined') {
                    $row['name'] = 'null';
                }
                $data[] = $row;
            }
            
            $last_page = ceil($count1 / $pagesize);
            
            // 取最大页数
            $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page, "uid" => (int)$userrow['uid']);
            exit(json_encode($data));
            break;
    case 'FDorderlistfl':
        $cx = daddslashes($_POST['cx']);
        $limit = $cx['limit'];
        if ($limit == "") {
            $pagesize = 25;
        } else {
            $pagesize = $limit;
        }
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $pageu = ($page - 1) * $pagesize;
        //当前界面
        $qq = trim(strip_tags($cx['qq']));
        $status_text = trim(strip_tags($cx['status_text']));
        $dock = trim(strip_tags($cx['dock']));
        $cid = trim(strip_tags($cx['cid']));
        $oid = trim(strip_tags($cx['oid']));
        $uid = trim(strip_tags($cx['uid']));
        $school = trim(strip_tags($cx['school']));
        $kcname = trim(strip_tags($cx['kcname']));
        if ($userrow['uid'] != '1') {
             $sql1 = "where fenlei='{$cx['id']}' and uid='{$userrow['uid']}'";
        } else {
            $sql1 = "where 1=1 and fenlei='{$cx['id']}'";
        }
        if ($cid != '') {
            $sql2 = " and cid='{$cid}'";
        }
        if ($qq != '') {
            $sql3 = " and user='{$qq}'";
        }
        if ($oid != '') {
            $sql4 = " and oid='{$oid}'";
        }
        if ($uid != '') {
            $sql5 = " and uid='{$uid}'";
        }
        if ($status_text != '') {
            $sql6 = " and status='{$status_text}'";
        }
        if ($dock != '') {
            $sql7 = " and dockstatus='{$dock}'";
        }
          if ($school != '') {
            $sql8 = " and school='{$school}'";
        }
          if ($kcname != '') {
            $sql9 = " and kcname LIKE '%{$kcname}%'";
        }
        $sql = $sql1 . $sql2 . $sql3 . $sql4 . $sql5 . $sql6 . $sql7 . $sql8 . $sql9 . $sql10;
        $a = $DB->query("select * from qingka_wangke_order {$sql} order by oid desc limit $pageu,$pagesize ");
        $count1 = $DB->count("select count(*) from qingka_wangke_order {$sql} ");
        while ($row = $DB->fetch($a)) {
            if ($row['name'] == '' || $row['name'] == 'undefined') {
                $row['name'] = 'null';
            }
            $data[] = $row;
        }
        $last_page = ceil($count1 / $pagesize);
        //取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page, "uid" => (int)$userrow['uid']);
        exit(json_encode($data));
        break;
    case 'user_orderlistfl':
        $cx = daddslashes($_POST['cx']);
        $limit = $cx['limit'];
        if ($limit == "") {
            $pagesize = 25;
        } else {
            $pagesize = $limit;
        }
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $pageu = ($page - 1) * $pagesize;
        // 当前界面
        $qq = trim(strip_tags(daddslashes($cx['qq'])));
        $status_text = trim(strip_tags(daddslashes($cx['status_text'])));
        $dock = trim(strip_tags(daddslashes($cx['dock'])));
        $cid = trim(strip_tags(daddslashes($cx['cid'])));
        $oid = trim(strip_tags(daddslashes($cx['oid'])));
        $uid = trim(strip_tags(daddslashes($cx['uid'])));
        $school = trim(strip_tags(daddslashes($cx['school'])));
        $kcname = trim(strip_tags(daddslashes($cx['kcname'])));
        $fenlei = trim(strip_tags(daddslashes($cx['id'])));
        $mh = trim(strip_tags(daddslashes($cx['mh'])));
        // SQL查询条件构建
        $sql1 = "where fenlei='{$fenlei}' and uid='{$userrow['uid']}'";
        if ($cid != '') {
            $sql2 = " and cid='" . $cid . "'";
        }
        if ($qq != '') {
            $sql3 = " and user='" . $qq . "'";
        }
        if ($oid != '') {
            $sql4 = " and oid='" . $oid . "'";
        }
        if ($uid != '') {
            $sql5 = " and uid='" . $uid . "'";
        }
        if ($status_text != '') {
            $sql6 = " and status='" . $status_text . "'";
        }
        if ($dock != '') {
            $sql7 = " and dockstatus='" . $dock . "'";
        }
        if ($school != '') {
            $sql8 = " and school='" . $school . "'";
        }
        if ($kcname != '') {
            $sql9 = " and kcname='" . $kcname . "'";
        }
        $sql12="and addtime >= CURDATE() - INTERVAL 100 DAY";
        // 构建SQL查询语句
        $sql = $sql1 . $sql2 . $sql3 . $sql4 . $sql5 . $sql6 . $sql7 . $sql8 . $sql9 .$sql12 ;
        
        // 执行数据库查询
        $a = $DB->query("select * from qingka_wangke_order {$sql} order by oid desc limit $pageu,$pagesize ");
        $count1 = $DB->count("select count(*) from qingka_wangke_order {$sql} ");
        
        // 检查查询结果的uid是否等于当前用户的uid
        while ($row = $DB->fetch($a)) {
            if ($row['uid'] != $userrow['uid']) {
                exit('{"code":-1,"msg":"你在干啥？""}');
            }
            if ($row['name'] == '' || $row['name'] == 'undefined') {
                $row['name'] = 'null';
            }
            $data[] = $row;
        }
        
        $last_page = ceil($count1 / $pagesize);
        
        // 取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page, "uid" => (int)$userrow['uid']);
        exit(json_encode($data));
        break;
    case 'duijie':
        $oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $b = $DB->get_row("select * from qingka_wangke_order where oid='$oid' limit 1 ");
        if ($userrow['uid'] != 1) {
            exit('{"code":-2,"msg":"无权限"}');
        }
        $d = $DB->get_row("select * from qingka_wangke_class where cid='{$b['cid']}' ");
        $result = addWk($oid);
        if ($result['code'] == '1') {
            $DB->query("update qingka_wangke_order set `hid`='{$d['docking']}',`status`='已提交',`dockstatus`=1,`yid`='{$result['yid']}' where oid='{$oid}' ");
            //对接成功
            
        } else {
            $DB->query("update qingka_wangke_order set `dockstatus`=2,`yid`='{$result['yid']}',`remarks`='{$result['msg']}' where oid='{$oid}' ");
        }
        exit(json_encode($result, true));
        break;
    
    case 'kcidlist':
    $page = trim(daddslashes($_POST['page']));
$limit = trim(daddslashes($_POST['limit']));
$searchKeyword = trim(daddslashes($_POST['keyword']));
$pageu = ($page - 1) * $limit;


    if ($userrow['uid'] == 1) {
        $sql = "select * from qingka_wangke_order where user LIKE '%$searchKeyword%' order by oid desc limit $pageu, $limit";
        $count = $DB->count("select count(*) from qingka_wangke_order where user LIKE '%$searchKeyword%'");
    } else {
        $sql = "select * from qingka_wangke_order where uid='{$userrow['uid']}' and user LIKE '%$searchKeyword%' order by oid desc limit $pageu, $limit";
        $count = $DB->count("select count(*) from qingka_wangke_order where uid='{$userrow['uid']}' and user LIKE '%$searchKeyword%'");
    }

    $result = $DB->query($sql);

    while ($row = $DB->fetch($result)) {
        $data[] = array(
            'oid' => $row['oid'],
            'ptname' => $row['ptname'],
            'user' => $row['user'],
            'kcname' => $row['kcname'],
            'kcid' => $row['kcid'],
            'addtime' => $row['addtime'],
            'status' => $row['status'],
        );
    }

    $data = array('code' => 1, 'data' => $data, 'count' => $count);
    // 打印调试信息
error_log(json_encode($data));
// 返回数据
exit(json_encode($data));

    break;
    
    case 'getclass':
        $a = $DB->query("select * from qingka_wangke_class where status=1 order by sort desc");
        while ($row = $DB->fetch($a)) {
            if ($row['docking'] == 'nana') {
                $miaoshua = 1;
            } else {
                $miaoshua = 0;
            }
            if ($row['yunsuan'] == "*") {
                $price = round($row['price'] * $userrow['addprice'], 2);
                $price1 = $price;
            } elseif ($row['yunsuan'] == "+") {
                $price = round($row['price'] + $userrow['addprice'], 2);
                $price1 = $price;
            } else {
                $price = round($row['price'] * $userrow['addprice'], 2);
                $price1 = $price;
            }
            //密价
            $mijia = $DB->get_row("select * from qingka_wangke_mijia where uid='{$userrow['uid']}' and cid='{$row['cid']}' ");
            if ($mijia) {
                if ($mijia['mode'] == 0) {
                    $price = round($price - $mijia['price'], 2);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 1) {
                    $price = round(($row['price'] - $mijia['price']) * $userrow['addprice'], 2);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 2) {
                    $price = $mijia['price'];
                    if ($price <= 0) {
                        $price = 0;
                    }
                }
                $row['name'] = "【密价】{$row['name']}";
            }
            if ($price >= $price1) {
                //密价价格大于原价，恢复原价
                $price = $price1;
            }
            $data[] = array('sort' => $row['sort'], 'cid' => $row['cid'], 'name' => $row['name'], 'noun' => $row['noun'], 'price' => $price, 'content' => $row['content'], 'status' => $row['status'], 'miaoshua' => $miaoshua);
        }
        foreach ($data as $key => $row) {
            $sort[$key] = $row['sort'];
            $cid[$key] = $row['cid'];
            $name[$key] = $row['name'];
            $noun[$key] = $row['noun'];
            $price[$key] = $row['price'];
            $info[$key] = $row['info'];
            $content[$key] = $row['content'];
            $status[$key] = $row['status'];
            $miaoshua[$key] = $row['miaoshua'];
        }
        array_multisort($sort, SORT_ASC, $cid, SORT_DESC, $data);
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));
        break;
    case 'getclassfl':
        $fenlei = trim(strip_tags(daddslashes($_POST['id'])));
        if ($fenlei == "") {
            $a = $DB->query("select * from qingka_wangke_class where status=1 order by sort desc");
        } else {
            $a = $DB->query("select * from qingka_wangke_class where status=1 and fenlei='$fenlei' order by sort desc");
        }
        while ($row = $DB->fetch($a)) {
            if ($row['docking'] == 'nana') {
                $miaoshua = 1;
            } else {
                $miaoshua = 0;
            }
            if ($row['yunsuan'] == "*") {
                $price = round($row['price'] * $userrow['addprice'], 2);
                $price1 = $price;
            } elseif ($row['yunsuan'] == "+") {
                $price = round($row['price'] + $userrow['addprice'], 2);
                $price1 = $price;
            } else {
                $price = round($row['price'] * $userrow['addprice'], 2);
                $price1 = $price;
            }
            //密价
            $mijia = $DB->get_row("select * from qingka_wangke_mijia where uid='{$userrow['uid']}' and cid='{$row['cid']}' ");
            if ($mijia) {
                if ($mijia['mode'] == 0) {
                    $price = round($price - $mijia['price'], 2);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 1) {
                    $price = round(($row['price'] - $mijia['price']) * $userrow['addprice'], 2);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 2) {
                    $price = $mijia['price'];
                    if ($price <= 0) {
                        $price = 0;
                    }
                }
                $row['name'] = "【密价】{$row['name']}";
            }
            if ($price >= $price1) {
                //密价价格大于原价，恢复原价
                $price = $price1;
            }
            //全站一个价
            if ($row['suo'] != 0) {
                $price = $row['suo'];
            }
            $data[] = array('sort' => $row['sort'], 'cid' => $row['cid'], 'name' => $row['name'], 'noun' => $row['noun'], 'price' => $price, 'content' => $row['content'], 'status' => $row['status'], 'miaoshua' => $miaoshua);
        }
        foreach ($data as $key => $row) {
            $sort[$key] = $row['sort'];
            $cid[$key] = $row['cid'];
            $name[$key] = $row['name'];
            $noun[$key] = $row['noun'];
            $price[$key] = $row['price'];
            $info[$key] = $row['info'];
            $content[$key] = $row['content'];
            $status[$key] = $row['status'];
            $miaoshua[$key] = $row['miaoshua'];
        }
        array_multisort($sort, SORT_ASC, $cid, SORT_DESC, $data);
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));
        break;
    case 'classlist':
    $page = trim(strip_tags(daddslashes($_POST['page'])));
    $keyword = trim(strip_tags(daddslashes($_POST['keyword'])));
    $pagesize = 50;
    $pageu = ($page - 1) * $pagesize;
    // 当前界面
    $count1 = $DB->count("SELECT count(*) FROM qingka_wangke_class");
    $last_page = ceil($count1 / $pagesize);
    // 取最大页数
    if ($userrow['uid'] == '1') {
        if ($keyword === '' || $keyword === null || $keyword === undefined) {
                $a = $DB->query("SELECT * FROM qingka_wangke_class limit $pageu,$pagesize ");
            } else {
                $a = $DB->query("SELECT * FROM qingka_wangke_class");
                }
        while ($row = $DB->fetch($a)) {
            $c = $DB->get_row("SELECT * FROM qingka_wangke_huoyuan WHERE hid='{$row['queryplat']}' ");
            $d = $DB->get_row("SELECT * FROM qingka_wangke_huoyuan WHERE hid='{$row['docking']}' ");
            $f = $DB->get_row("SELECT * FROM qingka_wangke_fenlei WHERE id='{$row['fenlei']}' ");
            $row['cx_name'] = $c['name'];
            $row['add_name'] = $d['name'];
            $row['fenlei_name'] = $f['name'];
            if($f['status']==1){
                $row['fenlei_status'] = "分类一";
            }elseif($f['status']==2){
                $row['fenlei_status'] = "分类二";
            }elseif($f['status']==3){
                $row['fenlei_status'] = "特殊分类";
            }elseif($f['status']==0){
                $row['fenlei_status'] = 0;
            }
            if ($row['queryplat'] == '0') {
                $row['cx_name'] = '自营';
            }
            if ($row['docking'] == '0') {
                $row['add_name'] = '自营';
            }
            // 检查关键词是否为空，如果为空直接添加到结果中，否则检查所有字段是否包含关键词
            if ($keyword === '' || $keyword === null || $keyword === undefined) {
                $data[] = $row;
            } else {
                $match = false;
                foreach ($row as $field) {
                    if (stripos($field, $keyword) !== false) {
                        $match = true;
                        break;
                    }
                }
                if ($match) {
                    $data[] = $row;
                }
            }
        }
        foreach ($data as $key => $rows) {
            $sort[$key] = $rows['sort'];
            $cid[$key] = $rows['cid'];
            $name[$key] = $rows['name'];
            $getnoun[$key] = $rows['getnoun'];
            $noun[$key] = $rows['noun'];
            $price[$key] = $rows['price'];
            $queryplat[$key] = $rows['queryplat'];
            $yunsuan[$key] = $rows['yunsuan'];
            $content[$key] = $rows['content'];
            $addtime[$key] = $rows['addtime'];
            $status[$key] = $rows['status'];
            $cx_names[$key] = $rows['cx_names'];
            $add_name[$key] = $rows['add_name'];
        }
        array_multisort($sort, SORT_ASC, $cid, SORT_DESC, $data);
        $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page);
        exit(json_encode($data));
    } else {
        exit('{"code":-2,"msg":"你在干啥"}');
    }
    break;
    
    case 'classlist1':
            $page = trim(strip_tags(daddslashes($_POST['page'])));
            $pagesize = 50;
            $pageu = ($page - 1) * $pagesize;
            //当前界面
            $count1 = $DB->count("select count(*) from qingka_wangke_class");
            $last_page = ceil($count1 / $pagesize);
            //取最大页数
            if ($userrow['uid'] == '1') {
                $a = $DB->query("select * from qingka_wangke_class limit $pageu,$pagesize ");
                while ($row = $DB->fetch($a)) {
                    $c = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$row['queryplat']}' ");
                    $d = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$row['docking']}' ");
                    $f = $DB->get_row("select * from qingka_wangke_fenlei where id='{$row['fenlei']}' ");
                    $row['cx_name'] = $c['name'];
                    $row['add_name'] = $d['name'];
                    $row['fenlei'] = $f['name'];
                    if ($row['queryplat'] == '0') {
                        $row['cx_name'] = '自营';
                    }
                    if ($row['docking'] == '0') {
                        $row['add_name'] = '自营';
                    }
                    $data[] = $row;
                }
                foreach ($data as $key => $rows) {
                    $sort[$key] = $rows['sort'];
                    $cid[$key] = $rows['cid'];
                    $name[$key] = $rows['name'];
                    $getnoun[$key] = $rows['getnoun'];
                    $noun[$key] = $rows['noun'];
                    $price[$key] = $rows['price'];
                    $queryplat[$key] = $rows['queryplat'];
                    $yunsuan[$key] = $rows['yunsuan'];
                    $content[$key] = $rows['content'];
                    $addtime[$key] = $rows['addtime'];
                    $status[$key] = $rows['status'];
                    $cx_names[$key] = $rows['cx_names'];
                    $add_name[$key] = $rows['add_name'];
                }
                array_multisort($sort, SORT_ASC, $cid, SORT_DESC, $data);
                $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page);
                exit(json_encode($data));
            } else {
                exit('{"code":-2,"msg":"你在干啥"}');
            }
            break;
    case 'upclass':
        parse_str(daddslashes($_POST['data']), $row);
        /*/$ids = isset($_POST['cid']) ? $_POST['cid'] : array();
    if ($userrow['uid'] != '1') {
        jsonReturn(-1, "滚");
    }
    // 如果 $ids 是数组，则循环删除选中的项目
    if (is_array($ids)) {
        foreach ($ids as $id) {
            $DB->query("delete from qingka_wangke_class where cid='$id' ");
        }
    } else {
        // 如果 $ids 不是数组，则直接删除单个项目
        $id = daddslashes($ids);
        $DB->query("delete from qingka_wangke_class where cid='$id' ");
    }/*/
        //将字符串解析成多个变量
        if ($userrow['uid'] == 1) {
            if ($row['sort']=='') {
                    // 获取qingka_wangke_class表中最大的sort值
                    $maxSortResult = $DB->get_row("SELECT MAX(sort) as max_sort FROM qingka_wangke_class");
                    $maxSortValue = $maxSortResult['max_sort'] ? $maxSortResult['max_sort'] : 10; // 如果没有记录，max_sort为10
            
                    // 将最大的sort值+1赋值给$row['sort']
                    $row['sort'] = $maxSortValue + 1;
                }
            if ($row['action'] == 'add') {
                // 检查是否已经存在相同的 docking 和 noun
                $existsResult = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_class WHERE docking = '{$row['docking']}' AND noun = '{$row['noun']}'");
                if ($existsResult['count'] > 0) {
                    $DB->query("update `qingka_wangke_class` set `price`='{$row['price']}',`content`='{$row['content']}',`status`='{$row['status']}' WHERE docking = '{$row['docking']}' AND noun = '{$row['noun']}' ");
                    exit('{"code":1,"msg":"记录已存在，跳过添加,自动更新项目信息"}');
                }else{
                    $DB->query("insert into qingka_wangke_class (sort,name,getnoun,noun,price,queryplat,docking,content,addtime,status,fenlei) values ('{$row['sort']}','{$row['name']}','{$row['getnoun']}','{$row['noun']}','{$row['price']}','{$row['queryplat']}','{$row['docking']}','{$row['content']}','{$date}','{$row['status']}','{$row['fenlei']}')");
                    exit('{"code":1,"msg":"添加成功"}');
                }
            } else {
                $DB->query("update `qingka_wangke_class` set `sort`='{$row['sort']}',`name`='{$row['name']}',`getnoun`='{$row['getnoun']}',`noun`='{$row['noun']}',`price`='{$row['price']}',`queryplat`='{$row['queryplat']}',`docking`='{$row['docking']}',`yunsuan`='{$row['yunsuan']}',`content`='{$row['content']}',`status`='{$row['status']}',`fenlei`='{$row['fenlei']}' where cid='{$row['cid']}' ");
                exit('{"code":1,"msg":"更新成功"}');
            }
        } else {
            exit('{"code":-2,"msg":"无权限"}');
        }
        break;
    case 'huoyuanlist':
        $page = daddslashes($_POST['page']);
        $pagesize = 50;
        $pageu = ($page - 1) * $pagesize;
        //当前界面
        $count1 = $DB->count("select count(*) from qingka_wangke_huoyuan");
        $last_page = ceil($count1 / $pagesize);
        //取最大页数
        if ($userrow['uid'] == '1') {
            $a = $DB->query("select * from qingka_wangke_huoyuan limit $pageu,$pagesize ");
            while ($row = $DB->fetch($a)) {
                $data[] = $row;
            }
            $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page);
            exit(json_encode($data));
        } else {
            exit('{"code":-2,"msg":"你在干啥"}');
        }
        break;
    case 'gethymoney':
        if ($userrow['uid'] != 1) {
            exit('{"code":-2,"msg":"无权限"}');
        }
        // 准备查询语句以获取所有 hid
        $result = $DB->query("SELECT hid FROM qingka_wangke_huoyuan");
        
        // 遍历每个 hid
        while ($hid_row = $result->fetch_assoc()) {
            $hid = $hid_row['hid'];
        
            // 获取当前 hid 的用户信息
            $a = $DB->get_row("SELECT * FROM qingka_wangke_huoyuan WHERE hid='{$hid}'");
            $user = $a["user"];
            $pass = $a["pass"];
            $dx_rl = $a["url"];
            $dx_url = "$dx_rl/api.php?act=getmoney";
        
            // 准备请求数据
            $data = array("uid" => $user, "key" => $pass);
        
            // 发送请求并获取结果
            $result_api = get_url($dx_url, $data);
            $result_api = json_decode($result_api, true);
        
            // 获取 money
            $money = $result_api["money"];
        
            // 更新数据库中的 money 字段
            $DB->query("UPDATE qingka_wangke_huoyuan SET money='{$money}' WHERE hid='{$hid}'");
        }
        break;
    case 'uphuoyuan':
        parse_str(daddslashes($_POST['data']), $row);
        //将字符串解析成多个变量
        if ($userrow['uid'] == 1) {
            if ($row['action'] == 'add') {
                $DB->query("insert into qingka_wangke_huoyuan (pt,name,url,user,pass,token,ip,cookie,addtime) values ('{$row['pt']}','{$row['name']}','{$row['url']}','{$row['user']}','{$row['pass']}','{$row['token']}','{$row['ip']}','{$row['cookie']}',NOW())");
                exit('{"code":1,"msg":"操作成功"}');
            } else {
                $DB->query("update `qingka_wangke_huoyuan` set `pt`='{$row['pt']}',`name`='{$row['name']}',`url`='{$row['url']}',`user`='{$row['user']}',`pass`='{$row['pass']}',`token`='{$row['token']}',`ip`='{$row['ip']}',`cookie`='{$row['cookie']}',`endtime`=NOW() where hid='{$row['hid']}' ");
                exit('{"code":1,"msg":"操作成功"}');
            }
        } else {
            exit('{"code":-2,"msg":"无权限"}');
        }
        break;
    case 'tk':
        $sex = daddslashes($_POST['sex']);
        if ($userrow['uid'] == 1) {
            for ($i = 0;$i < count($sex);$i++) {
                $oid = $sex[$i];
                $order = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
                $user = $DB->get_row("select * from qingka_wangke_user where uid='{$order['uid']}' ");
                $DB->query("update qingka_wangke_user set money=money+'{$order['fees']}' where uid='{$user['uid']}'");
                $DB->query("update qingka_wangke_order set status='已退款',dockstatus='4' where oid='{$oid}'");
                wlog($user['uid'], "订单退款", "订单ID：{$order['oid']} 订单信息：{$order['user']} {$order['pass']} {$order['kcname']}被管理员退款", "+{$order['fees']}");
            }
            exit('{"code":1,"msg":"选择的订单已批量退款！可在日志中查看！"}');
        } else {
            exit('{"code":-1,"msg":"无权限"}');
        }
        break;
    case 'delorder':
    // 验证用户权限，只有 uid=1 的用户可以执行该操作
    if ($userrow['uid'] !== '1') {
        exit('{"code":-1,"msg":"无权执行该操作"}');
    }

    $sex = daddslashes($_POST['sex']);
    
    // 这里可以添加一些其他的验证或处理逻辑

    // 执行删除订单的操作
    $count = count($sex);
    for ($i = 0; $i < $count; $i++) {
        $oid = $sex[$i];
        $b = $DB->query("delete from qingka_wangke_order where oid='{$oid}' ");
    }

    // 记录日志
    wlog($userrow['uid'], "批量删除订单", "批量删除了 $count 条订单", 0);

    // 返回相应的JSON响应，表示删除成功
    if ($b) {
        exit('{"code":1,"msg":"选择的订单已批量删除！可在日志中查看！"}');
    } else {
        exit('{"code":-1,"msg":"未知异常"}');
    }
    break;

    case 'adduser':
        if ($conf['user_htkh'] == '0') {
            jsonReturn(-1, "暂停开户，具体开放时间等通知");
        }
        parse_str(daddslashes($_POST['data']), $row);
        //将字符串解析成多个变量
        $type = daddslashes($_POST['type']);
        if ($row['name'] == '' || $row['user'] == '' || $row['pass'] == '' || $row['addprice'] == '') {
            exit('{"code":-2,"msg":"所有项目不能为空"}');
        }
        if (!preg_match('/[1-9]([0-9]{4,10})/', $row['user'])) exit('{"code":-1,"msg":"账号必须为QQ号"}');
        if ($DB->get_row("select * from qingka_wangke_user where user='{$row['user']}' ")) {
            exit('{"code":-1,"msg":"该账号已存在"}');
        }
        if ($DB->get_row("select * from qingka_wangke_user where name='{$row['name']}' ")) {
            exit('{"code":-1,"msg":"该昵称已存在"}');
        }
        if ($row['addprice'] < $userrow['addprice']) {
            exit('{"code":-1,"msg":"费率不能比自己低哦"}');
        }
        if ($row['addprice'] * 100 % 2 != 0 && $row['addprice'] != 0.267) {
            jsonReturn(-1, "请输入单价为0.02的倍数");
        }
        // 			if($row['addprice']>=0.2 && $row['addprice']<0.3){
        // 	            $cz=2000;
        // 			}elseif($row['addprice']>=0.3 && $row['addprice']<0.4){
        // 				$cz=1000;
        // 			}elseif($row['addprice']>=0.4 && $row['addprice']<0.5){
        // 				$cz=300;
        // 			}elseif($row['addprice']>=0.5 && $row['addprice']<0.6){
        // 				$cz=100;
        // 			}else{
        // 				$cz=0;
        // 			}
        $cz = 0;
        $h = $DB->query("select * from qingka_wangke_dengji");
        while ($row1 = $DB->fetch($h)) {
            if ($row['addprice'] == $row1['rate']) {
                if ($row1['addkf'] == 1) {
                    $cz = $row1['money'];
                }
            }
        }
        $kochu = round($cz * ($userrow['addprice'] / $row['addprice']), 2);
        //充值
        $kochu2 = $kochu + $conf['user_ktmoney'];
        if ($conf['dl_pkkg'] == 1) {
            //顶级不允许平开
            if ($row['addprice'] == $conf['djfl']) {
                exit('{"code":-1,"msg":"禁止顶级用户平开"}');
                break;
            }
        } else if ($conf['dl_pkkg'] == 2) {
            //顶级平开需持有双倍开户价格的余额及开户费
            if ($row['addprice'] == $conf['djfl']) {
                $kochu2 = $kochu + $kochu + $conf['user_ktmoney'];
            }
        } else if ($conf['dl_pkkg'] == 3) {
            //所有等级平开需持有双倍开户价格的余额及开户费
            if ($row['addprice'] == $userrow['addprice']) {
                $kochu2 = $kochu + $kochu + $conf['user_ktmoney'];
            }
        }
        if ($type != 1) {
            jsonReturn(1, "开通扣{$conf['user_ktmoney']}元开户费，并自动给下级充值{$cz}元，将扣除{$kochu}余额");
        }
        if ($userrow['money'] >= $kochu2) {
            $DB->query("insert into qingka_wangke_user (uuid,user,pass,name,addprice,addtime) values ('{$userrow['uid']}','{$row['user']}','{$row['pass']}','{$row['name']}','{$row['addprice']}','$date') ");
            $DB->query("update qingka_wangke_user set `money`=`money`-'{$conf['user_ktmoney']}' where uid='{$userrow['uid']}' ");
            wlog($userrow['uid'], "添加商户", "添加商户{$row['user']}成功!扣费{$conf['user_ktmoney']}元!", "-{$conf['user_ktmoney']}");
            if ($cz != 0) {
                $DB->query("update qingka_wangke_user set money='$cz',zcz=zcz+'$cz' where user='{$row['user']}' ");
                $DB->query("update qingka_wangke_user set `money`=`money`-'$kochu' where uid='{$userrow['uid']}' ");
                wlog($userrow['uid'], "代理充值", "成功给账号为[{$row['user']}]的靓仔充值{$cz}元,扣除{$kochu}元", -$kochu);
                $is = $DB->get_row("select uid from qingka_wangke_user where user='{$row['user']}' limit 1");
                wlog($is['uid'], "上级充值", "你上面的靓仔[{$userrow['name']}]成功给你充值{$cz}元", +$cz);
            }
            exit('{"code":1,"msg":"添加成功"}');
        } else {
            if ($conf['dl_pkkg'] == 2 && $userrow['money'] >= $kochu) {
                jsonReturn(-1, "余额不足开户，顶级平开需持有双倍开户价格的余额及开户费共计{$kochu2}元");
            } else if ($conf['dl_pkkg'] == 2 && $userrow['money'] >= $kochu) {
                jsonReturn(-1, "余额不足开户，所有等级平开需持有双倍开户价格的余额及开户费共计{$kochu2}元");
            } else {
                jsonReturn(-1, "余额不足开户，开户需扣除开户费{$conf['user_ktmoney']}元，及余额{$kochu}元");
            }
        }
        break;
    case 'FDadmin_userlist':
        $type = trim(strip_tags(daddslashes($_POST['type'])));
        $qq = trim(strip_tags(daddslashes($_POST['qq'])));
        $page = trim(daddslashes($_POST['page']));
        $pagesize = 20;
        $pageu = ($page - 1) * $pagesize;
        //当前界面
        if ($userrow['uid'] == '1') {
            if ($qq != "" and $type == 1) {
                $sql = "where uid=" . $qq;
            } elseif ($qq != "" and $type == 2) {
                $sql = "where user='" . $qq . "'";
            } elseif ($qq != "" and $type == 3) {
                $sql = "where yqm='" . $qq . "'";
            } elseif ($qq != "" and $type == 4) {
                $sql = "where name='" . $qq . "'";
            } elseif ($qq != "" and $type == 5) {
                $sql = "where addprice='" . $qq . "'";
            } elseif ($qq != "" and $type == 6) {
                $sql = "where money='" . $qq . "'";
            } elseif ($qq != "" and $type == 7) {
                $sql = "where endtime>'" . $qq . "'";
            }
        } else {
            if ($qq != "" and $type == 1) {
                $sql = "where uuid='{$userrow['uid']}' and uid='" . $qq . "'";
            } elseif ($qq != "" and $type == 2) {
                $sql = "where uuid='{$userrow['uid']}' and user='" . $qq . "'";
            } elseif ($qq != "" and $type == 3) {
                $sql = "where uuid='{$userrow['uid']}' and yqm='" . $qq . "'";
            } elseif ($qq != "" and $type == 4) {
                $sql = "where uuid='{$userrow['uid']}' and name='" . $qq . "'";
            } elseif ($qq != "" and $type == 5) {
                $sql = "where uuid='{$userrow['uid']}' and addprice='" . $qq . "'";
            } elseif ($qq != "" and $type == 6) {
                $sql = "where uuid='{$userrow['uid']}' and money='" . $qq . "'";
            } elseif ($qq != "" and $type == 7) {
                $sql = "where endtime>'" . $qq . "' and uuid='{$userrow['uid']}'";
            } else {
                $sql = "where uuid='{$userrow['uid']}'";
            }
        }
        $a = $DB->query("select * from qingka_wangke_user {$sql} order by uid desc limit $pageu,$pagesize ");
        $count1 = $DB->count("select count(*) from qingka_wangke_user {$sql}");
        while ($row = $DB->fetch($a)) {
            $zcz = 0;
            $row['pass'] = "这还能让你知道？";
            if ($row['key'] != '0') {
                $row['key'] = '1';
            }
            $dd = $DB->count("select count(oid) from qingka_wangke_order where uid='{$row['uid']}' ");
            //$zcz=$DB->count("select sum(money) as money from qingka_wangke_log where type='上级充值' and uid='{$row['uid']}' ");
            $row['dd'] = $dd;
            //$row['zcz']=round($zcz,2);
            $data[] = $row;
        }
        $last_page = ceil($count1 / $pagesize);
        //取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page);
        exit(json_encode($data));
        break;
    case 'userlist':
        $type = trim(strip_tags(daddslashes($_POST['type'])));
        $qq = trim(strip_tags(daddslashes($_POST['qq'])));
        $page = trim(daddslashes($_POST['page']));
        $pagesize = 10;
        $pageu = ($page - 1) * $pagesize; // 当前界面
        if ($qq != "") {
            if ($type == 1) {
                $sql = "where uuid='{$userrow['uid']}' and uid='" . $qq . "'";
            } elseif ($type == 2) {
                $sql = "where uuid='{$userrow['uid']}' and user='" . $qq . "'";
            } elseif ($type == 3) {
                $sql = "where uuid='{$userrow['uid']}' and yqm='" . $qq . "'";
            } elseif ($type == 4) {
                $sql = "where uuid='{$userrow['uid']}' and name='" . $qq . "'";
            } elseif ($type == 5) {
                $sql = "where uuid='{$userrow['uid']}' and addprice='" . $qq . "'";
            } elseif ($type == 6) {
                $sql = "where uuid='{$userrow['uid']}' and money='" . $qq . "'";
            } elseif ($type == 7) {
                $sql = "where endtime>'" . $qq . "' and uuid='{$userrow['uid']}'";
            }
        } else {
            $sql = "where uuid='{$userrow['uid']}'";
        }
        $a = $DB->query("select * from qingka_wangke_user {$sql} order by uid desc limit $pageu,$pagesize ");
        $count1 = $DB->count("select count(*) from qingka_wangke_user {$sql}");
        $data = array();
        while ($row = $DB->fetch($a)) {
            if ($row['uuid'] != $userrow['uid']) {
                exit('{"code":-1,"msg":"你在干啥？""}');
            }
            $row['uid'] = $row['uid'];
            $row['user'] = $row['user'];
            $row['yqm'] = $row['yqm'];
            $row['name'] = $row['name'];
            $row['addprice'] = $row['addprice'];
            $row['money'] = $row['money'];
            $row['endtime'] = $row['endtime'];
            $row['pass'] = "这还能让你知道？";
            $dd = $DB->count("select count(oid) from qingka_wangke_order where uid='{$row['uid']}' ");
            $row['dd'] = $dd;
            $data[] = $row;
        }
        $last_page = ceil($count1 / $pagesize); // 取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int) $page, "last_page" => $last_page);
        exit(json_encode($data));
    break;

    case 'adddjlist':
        $a = $DB->query("select * from qingka_wangke_dengji where status=1 and rate>='{$userrow['addprice']}' order by sort desc");
        while ($row = $DB->fetch($a)) {
            $data[] = array('sort' => $row['sort'], 'name' => $row['name'], 'rate' => $row['rate'],);
        }
        foreach ($data as $key => $row) {
            $sort[$key] = $row['sort'];
            $name[$key] = $row['name'];
            $rate[$key] = $row['rate'];
        }
        array_multisort($sort, SORT_ASC, $rate, SORT_ASC, $data);
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));
        break;
    case 'user_notice':
        $notice = trim(strip_tags(daddslashes($_POST['notice'])));
        if ($DB->query("update qingka_wangke_user set notice='{$notice}' where uid='{$userrow['uid']}' ")) {
            wlog($userrow['uid'], "设置公告", "设置公告: {$notice}", 0);
            jsonReturn(1, "设置成功");
        } else {
            jsonReturn(-1, "未知异常");
        }
        break;
    case 'user_email':
        // 从 POST 和 GET 请求中获取邮箱和 token，去除多余空白和特殊字符
        $email = trim(strip_tags(daddslashes($_POST['email'])));
        $token = trim(strip_tags(daddslashes($_POST['tuisongtoken'])));
    
        // 如果提供的邮箱格式不正确且不为空，则返回错误信息
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonReturn(-1, "邮箱格式不正确");
            break;
        }
        $updateQuery = "UPDATE `qingka_wangke_user` SET `email` = '{$email}', `tuisongtoken` = '{$token}' WHERE `uid` = '{$userrow['uid']}'";
        
        // 执行更新操作
        if ($DB->query($updateQuery)) {
            wlog($userrow['uid'], "设置邮箱和token", "设置邮箱: {$email}, 推送token: {$token}", 0);
            jsonReturn(1, "设置成功");
        } else {
            jsonReturn(-1, "未知异常");
        }
    break;



    case 'calc_kochu':
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        $money = trim(strip_tags(daddslashes($_POST['money'])));
        
        // 动态选择正则：管理员允许负号
        if (!preg_match($userrow['uid'] == 1 ? '/^-?[0-9.]+$/' : '/^[0-9.]+$/', $money)) {
            exit(json_encode(["code" => -1, "msg" => "充值金额不合法"]));
        }
        if ($money < 20 && $userrow['uid'] != 1) {
            exit(json_encode(["code" => -1, "msg" => "最低充值20元"]));
        }
        
        $row = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
        if (!$row) {
            exit(json_encode(["code" => -1, "msg" => "用户不存在"]));
        }
        
        if ($userrow['uid'] == $uid) {
            exit(json_encode(["code" => -1, "msg" => "自己不能给自己充值哦"]));
        }
        
        $kochu = round($money * ($userrow['addprice'] / $row['addprice']), 5);
        exit(json_encode(["code" => 1, "msg" => "计算成功", "kochu" => $kochu]));
    break;
    
    case 'userjk':
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        $money = trim(strip_tags(daddslashes($_POST['money'])));
        // 动态正则检查
        if (!preg_match($userrow['uid'] == 1 ? '/^-?[0-9.]+$/' : '/^[0-9.]+$/', $money)) {
            exit('{"code":-1,"msg":"充值金额不合法"}');
        }
        if ($money < 20 && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"最低充值20元"}');
        }
        $row = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
        if ($row['uuid'] != $userrow['uid'] && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"该用户不是你的下级,无法充值"}');
        }
        if ($userrow['uid'] == $uid) {
            exit('{"code":-1,"msg":"自己不能给自己充值哦"}');
        }
        $kochu = round($money * ($userrow['addprice'] / $row['addprice']), 5);
        // 调整余额检查逻辑：仅非管理员需要检查余额
        if ($userrow['uid'] != 1 && $userrow['money'] < $kochu) {
            exit('{"code":-1,"msg":"您当前余额不足,无法充值"}');
        }
        $wdkf = round($userrow['money'] - $kochu, 5);
        $xjkf = round($row['money'] + $money, 5);
        $DB->query("update qingka_wangke_user set money='$wdkf' where uid='{$userrow['uid']}' ");
        $DB->query("update qingka_wangke_user set money='$xjkf',zcz=zcz+'$money' where uid='$uid' ");
        // 根据金额正负调整日志描述
        $action = $money >=0 ? "充值" : "扣除";
        $absMoney = abs($money);
        wlog($userrow['uid'], "代理操作", "成功给账号为[{$row['user']}]的靓仔{$action}{$absMoney}元,实际{$action}{$kochu}元", -$kochu);
        wlog($row['uid'], "上级操作", "{$userrow['name']}已{$action}你{$absMoney}元", +$money);
        exit('{"code":1,"msg":"'.$action.'' . $absMoney . '元成功,实际扣费' . $kochu . '元"}');
    break;
    
    case 'khcz':
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        $money = trim(strip_tags(daddslashes($_POST['money'])));
        if (!preg_match('/^[0-9.]+$/', $money)) exit('{"code":-1,"msg":"充值金额不合法"}');
        if ($money < 20 && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"最低充值20元"}');
        }
        $row = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
        if ($userrow['khcz'] != 1 && $userrow['zcz']<5000) {
            exit('{"code":-1,"msg":"无权限！累计充值达到5000自动开通！"}');
        }
        if ($userrow['uid'] == $uid) {
            exit('{"code":-1,"msg":"自己不能给自己充值哦"}');
        }
        $kochu = round($money * ($userrow['addprice'] / $row['addprice']), 2);
        //充值
        if ($userrow['money'] < $kochu) {
            exit('{"code":-1,"msg":"您当前余额不足,无法充值"}');
        }
        $wdkf = round($userrow['money'] - $kochu, 2);
        $xjkf = round($row['money'] + $money, 2);
        $DB->query("update qingka_wangke_user set money='$wdkf' where uid='{$userrow['uid']}' ");
        //我的扣费
        $DB->query("update qingka_wangke_user set money='$xjkf',zcz=zcz+'$money' where uid='$uid' ");
        //下级增加
        wlog($userrow['uid'], "跨户充值", "成功给UID为[{$row['uid']}]的用户充值{$money}元,扣除{$kochu}元", -$kochu);
        wlog($row['uid'], "跨户充值", "UID为{$userrow['uid']}的用户成功给你跨户充值{$money}元", +$money);
        exit('{"code":1,"msg":"跨户充值' . $money . '元成功,实际扣费' . $kochu . '元"}');
        break;
        

    case 'usergj':
        parse_str(daddslashes($_POST['data']), $row);
        $uid = trim(strip_tags(daddslashes(trim($row['uid']))));
        $addprice = trim(strip_tags(daddslashes($row['addprice'])));
        $type = trim(strip_tags(daddslashes($_POST['type'])));
        if (!preg_match('/^[0-9.]+$/', $addprice)) exit('{"code":-1,"msg":"费率不合法"}');
        $row = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
        if ($row['uuid'] != $userrow['uid'] && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"该用户你的不是你的下级,无法修改价格"}');
        }
        if ($userrow['uid'] == $uid) {
            exit('{"code":-1,"msg":"自己不能给自己改价哦"}');
        }
        if ($userrow['addprice'] > $addprice) {
            exit('{"code":-1,"msg":"你下级的费率不能低于你哦"}');
        }
        if ($addprice * 100 % 5 != 0 && $addprice != 0.267) {
            jsonReturn(-1, "请输入单价为0.05的倍数");
        }
        if ($addprice == $row['addprice']) {
            jsonReturn(-1, "该商户已经是{$addprice}费率了，你还修改啥");
        }
        if ($addprice > $row['addprice']) {
            jsonReturn(-1, "下调费率，请联系管理员");
        }
        if ($addprice < '0.2' && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"你在干什么？"}');
        }
        //降价扣费计算：下级余额 /当前费率 *修改费率 ；
        $money = round($row['money'] / $row['addprice'] * $addprice, 2);
        //涨降价余额变动,,自动调费
        $money1 = $money - $row['money'];
        //日志显示变动余额
        // 		if($addprice>=0.2 && $addprice<0.3){
        //             $cz=2000;
        // 		}elseif($addprice>=0.3 && $addprice<0.4){
        // 			$cz=1000;
        // 		}elseif($addprice>=0.4 && $addprice<0.5){
        // 			$cz=300;
        // 		}elseif($addprice>=0.5 && $addprice<0.6){
        // 			$cz=100;
        // 		}else{
        // 			$cz=0;
        // 		}
        $cz = 0;
        $h = $DB->query("select * from qingka_wangke_dengji");
        while ($row1 = $DB->fetch($h)) {
            if ($addprice == $row1['rate']) {
                if ($row1['gjkf'] == 1) {
                    $cz = $row1['money'];
                }
            }
        }
        $kochu = round($cz * ($userrow['addprice'] / $addprice), 2);
        //充值
        $kochu2 = $kochu + $money + 3;
        if ($type != 1) {
            jsonReturn(1, "改价手续费3元，并自动给下级[UID:{$uid}]充值{$cz}元，将扣除{$kochu}余额");
        }
        if ($userrow['money'] < $kochu2) {
            jsonReturn(-1, "余额不足,改价需扣3元手续费,及余额{$kochu}元");
        } else {
            $DB->query("update qingka_wangke_user set money=money-3 where uid='{$userrow['uid']}' ");
            $DB->query("update qingka_wangke_user set money='$money',addprice='$addprice' where uid='$uid' ");
            //调费
            wlog($userrow['uid'], "修改费率", "修改代理{$row['name']},费率：{$addprice},扣除手续费3元", "-3");
            wlog($uid, "修改费率", "{$userrow['name']}修改你的费率为：{$addprice},系统根据比例自动调整价格", $money1);
            if ($cz != 0) {
                $DB->query("update qingka_wangke_user set money=money-'{$kochu}' where uid='{$userrow['uid']}' ");
                //我的扣费
                $DB->query("update qingka_wangke_user set money=money+'{$cz}',zcz=zcz+'$cz' where uid='$uid' ");
                //下级增加
                wlog($userrow['uid'], "代理充值", "成功给账号为[{$row['user']}]的靓仔充值{$cz}元,扣除{$kochu}元", -$kochu);
                wlog($uid, "上级充值", "{$userrow['name']}成功给你充值{$cz}元", +$cz);
            }
            exit('{"code":1,"msg":"改价成功"}');
        }
        break;
    case 'user_czmm':
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        if ($userrow['uid'] == $uid) {
            jsonReturn(-1, "自己不能给自己重置哦");
        }
        $row = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
        if ($row['uuid'] != $userrow['uid'] && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"该用户你的不是你的下级,无法修改价格"}');
        } else {
            $DB->query("update qingka_wangke_user set pass='123456' where uid='{$uid}' ");
            wlog($row['uid'], "重置密码", "成功重置UID为{$uid}的密码为123456", 0);
            jsonReturn(1, "成功重置密码为123456");
        }
        break;
    case 'user_ban':
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        $active = trim(strip_tags(daddslashes($_POST['active'])));
        if ($userrow['uid'] != 1) {
            jsonReturn(-1, "无权限");
        }
        if ($userrow['uid'] == $uid) {
            jsonReturn(-1, "你TM有病是吧？");
        }
        if ($active == 1) {
            $a = 0;
            $b = "封禁商户";
        } else {
            $a = 1;
            $b = "解封商户";
        }
        $DB->query("update qingka_wangke_user set active='$a' where uid='{$uid}' ");
        wlog($userrow['uid'], $b, "{$b}[UID {$uid}]成功", 0);
        jsonReturn(1, "操作成功");
        break;
    case 'loglist':
        $page = trim(strip_tags(daddslashes(trim($_POST['page']))));
        $type = trim(strip_tags(daddslashes(trim($_POST['type']))));
        $types = trim(strip_tags(daddslashes(trim($_POST['types']))));
        $qq = trim(strip_tags(daddslashes(trim($_POST['qq']))));
        $pagesize = 20;
        $pageu = ($page - 1) * $pagesize;
        //当前界面
        if ($userrow['uid'] != '1') {
            $sql1 = "where uid='{$userrow['uid']}'";
        } else {
            $sql1 = "where 1=1";
        }
        if($type!=''){
            $sql2=" and type='$type'";
        }
        if($types!='')
        {
            if($types=='1')
            {
                $sql3=" and uid like '".$qq."' ";
            }
            elseif($types=='2')
            {
                $sql3=" and text like '%".$qq."%' ";
            }
            elseif($types=='3')
            {
                $sql3=" and money='$qq' ";
            }
            elseif($types=='4')
            {
                $sql3=" and addtime like '%".$qq."%' ";
            }
        }
        if($qq != ''){
            $sql4 = " and (id LIKE '%" . $qq . "%' OR uid LIKE '%" . $qq . "%' OR type LIKE '%" . $qq . "%' OR text LIKE '%" . $qq . "%' OR money LIKE '%" . $qq . "%' OR smoney LIKE '%" . $qq . "%' OR ip LIKE '%" . $qq . "%' OR addtime LIKE '%" . $qq . "%')";
        }
        $sql=$sql1.$sql2.$sql3.$sql4;
        $a = $DB->query("select * from qingka_wangke_log {$sql} order by id desc limit  $pageu,$pagesize ");
        $count1 = $DB->count("select count(*) from qingka_wangke_log {$sql} ");
        while ($row = $DB->fetch($a)) {
            $data[] = $row;
        }
        $last_page = ceil($count1 / $pagesize);
        //取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page);
        exit(json_encode($data));
        break;
    case 'getclassfl1':
        $fenlei = trim(strip_tags(daddslashes($_POST['id'])));
        if ($fenlei == 0) {
            $a = $DB->query("select * from qingka_wangke_class where status=1 order by sort desc");
        } else {
            $a = $DB->query("select * from qingka_wangke_class where status=1 and fenlei1='$fenlei' order by sort desc");
        }
        while ($row = $DB->fetch($a)) {
            if ($row['docking'] == 'nana') {
                $miaoshua = 1;
            } else {
                $miaoshua = 0;
            }
            if ($row['yunsuan'] == "*") {
                $price = round($row['price'] * $userrow['addprice'], 2);
                $price1 = $price;
            } elseif ($row['yunsuan'] == "+") {
                $price = round($row['price'] + $userrow['addprice'], 2);
                $price1 = $price;
            } else {
                $price = round($row['price'] * $userrow['addprice'], 2);
                $price1 = $price;
            }
            //密价
            $mijia = $DB->get_row("select * from qingka_wangke_mijia where uid='{$userrow['uid']}' and cid='{$row['cid']}' ");
            if ($mijia) {
                if ($mijia['mode'] == 0) {
                    $price = round($price - $mijia['price'], 2);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 1) {
                    $price = round(($row['price'] - $mijia['price']) * $userrow['addprice'], 2);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 2) {
                    $price = $mijia['price'];
                    if ($price <= 0) {
                        $price = 0;
                    }
                }
                $row['name'] = "【密价】{$row['name']}";
            }
            if ($price >= $price1) {
                //密价价格大于原价，恢复原价
                $price = $price1;
            }
            //全站一个价
            if ($row['suo'] != 0) {
                $price = $row['suo'];
            }
            $data[] = array('sort' => $row['sort'], 'cid' => $row['cid'], 'name' => $row['name'], 'noun' => $row['noun'], 'price' => $price, 'content' => $row['content'], 'status' => $row['status'], 'miaoshua' => $miaoshua);
        }
        foreach ($data as $key => $row) {
            $sort[$key] = $row['sort'];
            $cid[$key] = $row['cid'];
            $name[$key] = $row['name'];
            $noun[$key] = $row['noun'];
            $price[$key] = $row['price'];
            $info[$key] = $row['info'];
            $content[$key] = $row['content'];
            $status[$key] = $row['status'];
            $miaoshua[$key] = $row['miaoshua'];
        }
        array_multisort($sort, SORT_ASC, $cid, SORT_DESC, $data);
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));
        break;
    case 'djlist':
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $pagesize = 500;
        $pageu = ($page - 1) * $pagesize;
        //当前界面
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "滚");
        }
        $a = $DB->query("select * from qingka_wangke_dengji");
        $count1 = $DB->count("select count(*) from qingka_wangke_dengji");
        while ($row = $DB->fetch($a)) {
            $data[] = array('id' => $row['id'], 'sort' => $row['sort'], 'name' => $row['name'], 'rate' => $row['rate'], 'money' => $row['money'], 'addkf' => $row['addkf'], 'gjkf' => $row['gjkf'], 'status' => $row['status'], 'time' => $row['time'],);
        }
        foreach ($data as $key => $row) {
            $id[$key] = $row['id'];
            $sort[$key] = $row['sort'];
            $name[$key] = $row['name'];
            $rate[$key] = $row['rate'];
            $money[$key] = $row['money'];
            $addkf[$key] = $row['addkf'];
            $gjkf[$key] = $row['gjkf'];
            $status[$key] = $row['status'];
            $time[$key] = $row['time'];
        }
        array_multisort($sort, SORT_ASC, $rate, SORT_ASC, $data);
        $last_page = ceil($count1 / $pagesize);
        //取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page);
        exit(json_encode($data));
        break;
    case 'dj':
        $data = daddslashes($_POST['data']);
        $active = trim(strip_tags(daddslashes(trim($_POST['active']))));
        $id = trim(strip_tags(daddslashes(trim($data['id']))));
        $sort = trim(strip_tags(daddslashes(trim($data['sort']))));
        $name = trim(strip_tags(daddslashes(trim($data['name']))));
        $rate = trim(strip_tags(daddslashes(trim($data['rate']))));
        $money = trim(strip_tags(daddslashes(trim($data['money']))));
        $status = trim(strip_tags(daddslashes(trim($data['status']))));
        $addkf = trim(strip_tags(daddslashes(trim($data['addkf']))));
        $gjkf = trim(strip_tags(daddslashes(trim($data['gjkf']))));
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "滚！");
        }
        if ($active == '1') {
            //添加
            $DB->query("insert into qingka_wangke_dengji (sort,name,rate,money,addkf,gjkf,status,time) values ('$sort','$name','$rate','$money','$addkf','$gjkf','1',NOW())");
            jsonReturn(1, "添加成功");
        } elseif ($active == '2') {
            //修改
            $DB->query("update qingka_wangke_dengji set `sort`='$sort',`name`='$name',`rate`='$rate',`money`='$money',`addkf`='$addkf',`gjkf`='$gjkf',`status`='$status' where id='$id'");
            jsonReturn(1, "修改成功");
        } else {
            jsonReturn(-1, "不知道你在干什么");
        }
        break;
    case 'dj_del':
        $id = daddslashes($_POST['id']);
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "滚");
        }
        $DB->query("delete from qingka_wangke_dengji where id='$id' ");
        jsonReturn(1, "删除成功");
        break;
    case 'fllist':
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $pagesize = 500;
        $pageu = ($page - 1) * $pagesize;
        //当前界面
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "滚");
        }
        $a = $DB->query("select * from qingka_wangke_fenlei");
        $count1 = $DB->count("select count(*) from qingka_wangke_fenlei");
        while ($row = $DB->fetch($a)) {
            $data[] = array('id' => $row['id'], 'sort' => $row['sort'], 'name' => $row['name'], 'rate' => $row['rate'], 'money' => $row['money'], 'addkf' => $row['addkf'], 'gjkf' => $row['gjkf'], 'status' => $row['status'], 'time' => $row['time'],);
        }
        foreach ($data as $key => $row) {
            $id[$key] = $row['id'];
            $sort[$key] = $row['sort'];
            $name[$key] = $row['name'];
            $rate[$key] = $row['rate'];
            $money[$key] = $row['money'];
            $addkf[$key] = $row['addkf'];
            $gjkf[$key] = $row['gjkf'];
            $status[$key] = $row['status'];
            $time[$key] = $row['time'];
        }
        array_multisort($sort, SORT_ASC, $rate, SORT_ASC, $data);
        $last_page = ceil($count1 / $pagesize);
        //取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int)$page, "last_page" => $last_page);
        exit(json_encode($data));
        break;
    case 'fl':
        $data = daddslashes($_POST['data']);
        $active = trim(strip_tags(daddslashes(trim($_POST['active']))));
        $id = trim(strip_tags(daddslashes(trim($data['id']))));
        $sort = trim(strip_tags(daddslashes(trim($data['sort']))));
        $name = trim(strip_tags(daddslashes(trim($data['name']))));
        $status = trim(strip_tags(daddslashes(trim($data['status']))));
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "滚！");
        }
        if ($active == '1') {
            //添加
            $DB->query("insert into qingka_wangke_fenlei (sort,name,status,time) values ('$sort','$name','1',NOW())");
            jsonReturn(1, "添加成功");
        } elseif ($active == '2') {
            //修改
            $DB->query("update qingka_wangke_fenlei set `sort`='$sort',`name`='$name',`status`='$status' where id='$id'");
            jsonReturn(1, "修改成功");
        } else {
            jsonReturn(-1, "不知道你在干什么");
        }
        break;
     case 'fl2':
    $data = daddslashes($_POST['data']);
    $active = trim(strip_tags(daddslashes(trim($_POST['active']))));
    $id = trim(strip_tags(daddslashes(trim($data['id']))));
    $sort = trim(strip_tags(daddslashes(trim($data['sort']))));
     $name = trim(strip_tags(daddslashes(trim($data['name']))));
    if ($userrow['uid'] != '1') {
        jsonReturn(-1, "滚！");
    }
    if ($active == '1') {
        // 修改data[sort]: 强盛 data[name]: 12
        $DB->query("UPDATE `qingka_wangke_class` SET `fenlei` = '$name' WHERE `name` LIKE '%$sort%'");
        jsonReturn(1, "修改成功");
    } else {
        jsonReturn(-1, "不知道你在干什么");
    }
    break;
    case 'fl3':
        $data = daddslashes($_POST['data']);
        $active = trim(strip_tags(daddslashes(trim($_POST['active']))));
        $id = trim(strip_tags(daddslashes(trim($data['id']))));
        $sort = trim(strip_tags(daddslashes(trim($data['sort']))));//关键词
        $name = trim(strip_tags(daddslashes(trim($data['name']))));//特殊分类id
        $flid = trim(strip_tags(daddslashes(trim($data['fenleiid']))));//普通分类id
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "滚！");
        }
        if ($active == '1') {
            if (empty($sort) && empty($flid)) {
				exit('{"code":-1,"msg":"关键词和普通分类不能都为空"}');
			}
			if(!empty($sort) && empty($flid)){
                // 修改data[sort]: 强盛 data[name]: 12
                $DB->query("UPDATE `qingka_wangke_class` SET `fenlei1` = '$name' WHERE `name` LIKE '%$sort%' ");
                jsonReturn(1, "修改成功");
			}elseif(!empty($flid) && empty($sort)){
			    if($flid==999)
			    {
			        $DB->query("UPDATE `qingka_wangke_class` SET `fenlei1` = '$flid' WHERE `fenlei1` = '$name' ");
                    jsonReturn(1, "清空成功");
			    }else{
                    $DB->query("UPDATE `qingka_wangke_class` SET `fenlei1` = '$name' WHERE `fenlei` = '$flid' ");
                    jsonReturn(1, "修改成功");
			    }
			}else{
			    $DB->query("UPDATE `qingka_wangke_class` SET `fenlei1` = '$name' WHERE `fenlei` = '$flid' and `name` LIKE '%$sort%'");
                jsonReturn(1, "修改成功");
			}
        } else {
            jsonReturn(-1, "不知道你在干什么");
        }
    break;
    case 'fl_del':
        $id = daddslashes($_POST['id']);
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "滚");
        }
        $DB->query("delete from qingka_wangke_fenlei where id='$id' ");
        jsonReturn(1, "删除成功");
        break;
    case 'wk_del':
        $ids = isset($_POST['cid']) ? $_POST['cid'] : array();
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "滚");
        }
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $DB->query("delete from qingka_wangke_class where cid='$id' ");
            }
        } else {
            $id = daddslashes($ids);
            $DB->query("delete from qingka_wangke_class where cid='$id' ");
        }
        jsonReturn(1, "删除成功");
        break;
    
    case 'wk_update_keywords':
        $ids = isset($_POST['cid']) ? $_POST['cid'] : array();
        $keywordA = isset($_POST['keywordA']) ? $_POST['keywordA'] : '';
        $keywordB = isset($_POST['keywordB']) ? $_POST['keywordB'] : '';
    
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "滚");
        }
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $id = daddslashes($id); 
    
                if (!empty($keywordA) && !empty($keywordB)) {
                    // 如果关键词 A 和 B 都不为空，且 name 包含关键词 A，则用 B 替换 A
                    $DB->query("UPDATE qingka_wangke_class SET name = REPLACE(name, '$keywordA', '$keywordB') WHERE cid='$id' AND name LIKE '%$keywordA%'");
                } elseif (empty($keywordA) && !empty($keywordB)) {
                    // 如果关键词 A 为空，B 不为空，则在 name 前面加上 B
                    $DB->query("UPDATE qingka_wangke_class SET name = CONCAT('$keywordB', name) WHERE cid='$id'");
                }
            }
        } else {
            $id = daddslashes($ids); 
    
            if (!empty($keywordA) && !empty($keywordB)) {
                // 如果关键词 A 和 B 都不为空，且 name 包含关键词 A，则用 B 替换 A
                $DB->query("UPDATE qingka_wangke_class SET name = REPLACE(name, '$keywordA', '$keywordB') WHERE cid='$id' AND name LIKE '%$keywordA%'");
            } elseif (empty($keywordA) && !empty($keywordB)) {
                // 如果关键词 A 为空，B 不为空，则在 name 前面加上 B
                $DB->query("UPDATE qingka_wangke_class SET name = CONCAT('$keywordB', name) WHERE cid='$id'");
            }
        }
        jsonReturn(1, "修改成功");
        break;
    case 'mijialist':
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $uid = trim(strip_tags(daddslashes($_POST['selectedUid'])));
        $keyword = trim(strip_tags(daddslashes($_POST['keyword'])));
        $pagesize = isset($_POST['pagesize']) ? (int)trim(strip_tags(daddslashes($_POST['pagesize']))) : 10; // 默认为10条
        $pageu = ($page - 1) * $pagesize;
    
        // 当前界面
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "滚");
        }
        $sql = "";
        if ($uid != '') {
            $sql = " AND uid='$uid'";
        }
    
        // 构建模糊匹配的SQL条件
        $likeSql = "";
        if ($keyword != '') {
            $likeSql = " AND (qingka_wangke_mijia.uid LIKE '%{$keyword}%' OR qingka_wangke_class.name LIKE '%{$keyword}%')";
        }
    
        // 计算总数
        $totalSql = "SELECT COUNT(*) FROM qingka_wangke_mijia LEFT JOIN qingka_wangke_class ON qingka_wangke_mijia.cid = qingka_wangke_class.cid WHERE 1=1 {$sql} {$likeSql}";
        $count1 = $DB->count($totalSql);
        $last_page = ceil($count1 / $pagesize); // 计算最后一页的页码
    
        // 添加ORDER BY子句按照mid排序
        $orderSql = "ORDER BY qingka_wangke_mijia.mid ASC";
        $selectSql = "SELECT qingka_wangke_mijia.*, qingka_wangke_class.name FROM qingka_wangke_mijia LEFT JOIN qingka_wangke_class ON qingka_wangke_mijia.cid = qingka_wangke_class.cid WHERE 1=1 {$sql} {$likeSql} {$orderSql} LIMIT {$pageu}, {$pagesize}";
    
        $a = $DB->query($selectSql);
        $data = [];
        while ($row = $DB->fetch($a)) {
            $data[] = $row;
        }
    
        // 查询所有唯一的uid，并按照uid排序
        $uidSql = "SELECT DISTINCT uid FROM qingka_wangke_mijia ORDER BY uid ASC";
        $uidQuery = $DB->query($uidSql);
        $uids = [];
        while ($uidRow = $DB->fetch($uidQuery)) {
            $uids[] = $uidRow['uid'];
        }
    
        $response = array(
            'code' => 1,
            'data' => $data,
            "current_page" => (int)$page,
            "last_page" => $last_page,
            "total" => $count1, // 返回总数
            "uids" => $uids, // 返回所有唯一的uids
            "pagesize" => $pagesize
        );
        exit(json_encode($response));
        break;
    case 'mijia':
        $data = daddslashes($_POST['data']);
        $active = trim(strip_tags(daddslashes(trim($_POST['active']))));
        $uid = trim(strip_tags(daddslashes(trim($data['uid']))));
        $mid = trim(strip_tags(daddslashes(trim($data['mid']))));
        $mode = trim(strip_tags(daddslashes(trim($data['mode']))));
        $cid = trim(strip_tags(daddslashes(trim($data['cid']))));
        $price = trim(strip_tags(daddslashes(trim($data['price']))));
        $fenlei = trim(strip_tags(daddslashes(trim($data['fenlei']))));
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "不知道你在干什么");
        }
    
        // 如果 fenlei 参数存在，则查询对应分类下的所有商品
        if ($fenlei) {
            $classfl = $DB->query("SELECT cid FROM qingka_wangke_class WHERE fenlei = '$fenlei'");
            $cids = [];
            while ($row = $DB->fetch($classfl)) {
                $cids[] = $row['cid'];
            }
    
            if ($active == '1') {
                foreach ($cids as $cid) {
                    // 检查是否存在该uid和cid的密接
                    $uidmijia = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_mijia WHERE uid='{$uid}' and cid = '{$cid}'");
                    
                    if ($uidmijia['count'] > 0) {
                        // 如果存在，则更新已有数据
                        $DB->query("UPDATE qingka_wangke_mijia SET price = '$price', mode = '$mode' WHERE uid = '$uid' AND cid = '$cid'");
                    } else {
                        // 如果不存在，则插入新数据
                        $DB->query("INSERT INTO qingka_wangke_mijia (uid, cid, mode, price, addtime) VALUES ('$uid', '$cid', '$mode', '$price', NOW())");
                    }
                }
                jsonReturn(1, "批量操作成功");
            } elseif ($active == '2') {
                // 为每个商品修改密价
                foreach ($cids as $cid) {
                    $DB->query("UPDATE qingka_wangke_mijia SET price = '$price', mode = '$mode', uid = '$uid', cid = '$cid' WHERE uid = '$uid' AND cid = '$cid' AND mid <> '$mid'");
                }
                jsonReturn(1, "批量修改成功");
            }
        } else {
            if ($active == '1') {
                $uidmijia = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_mijia WHERE uid='{$uid}' and cid = '{$cid}' ORDER BY mid DESC LIMIT 0,1");
                if($uidmijia['count'] > 0){
                    $DB->query("UPDATE qingka_wangke_mijia SET price = '$price', mode = '$mode', cid = '$cid' WHERE uid = '$uid'");
                    jsonReturn(1, "该密接已存在，已更新！");
                }
                // 添加单个商品的密价
                $DB->query("INSERT INTO qingka_wangke_mijia (uid, cid, mode, price, addtime) VALUES ('$uid', '$cid', '$mode', '$price', NOW())");
                jsonReturn(1, "添加成功");
            } elseif ($active == '2') {
                // 修改单个商品的密价
                $DB->query("UPDATE qingka_wangke_mijia SET price = '$price', mode = '$mode', uid = '$uid', cid = '$cid' WHERE mid = '$mid'");
                jsonReturn(1, "修改成功");
            }
        }
        break;
    case 'mijia_del':
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "滚");
        }
        
        $mids = isset($_POST['mids']) ? $_POST['mids'] : '';
        if (empty($mids)) {
            jsonReturn(-1, "没有提供要删除的ID");
        }
    
        // 将字符串转换为数组
        $midsArray = explode(',', $mids);
        if (empty($midsArray)) {
            jsonReturn(-1, "无效的ID列表");
        }
    
        // 构建IN条件的SQL语句
        $inSql = "'" . implode("','", array_map('trim', $midsArray)) . "'";
        $deleteSql = "DELETE FROM qingka_wangke_mijia WHERE mid IN ($inSql)";
        $result = $DB->query($deleteSql);
    
        if ($result) {
            exit(json_encode(["code" => 1, "msg" => "删除成功", "mids" => $mids]));
        } else {
            jsonReturn(-1, "删除失败");
        }
        break;
    case 'sjqy':
        $uuid = daddslashes($_POST['uid']);
        $yqm = daddslashes($_POST['yqm']);
        if ($uuid == '' || $yqm == '') {
            exit('{"code":0,"msg":"所有项目不能为空"}');
        }
        if ($conf['sjqykg'] == 0) {
            exit('{"code":0,"msg":"管理员未打开迁移功能"}');
        } elseif ($conf['sjqykg'] == 1) {
            $row = $DB->get_row("select * from qingka_wangke_user where uid='$uuid' limit 1");
            if ($row) {
                if ($yqm == $row['yqm']) {
                    $row1 = $DB->get_row("select * from qingka_wangke_user where uid='{$userrow['uid']}' limit 1");
                    if ($row1['uuid'] != $uuid) {
                        if ($row1['uid'] != $uuid) {
                            $ztdate = date("Y-m-d", strtotime("-7 day"));
                            $row11 = $DB->get_row("select * from qingka_wangke_user where uid='{$userrow['uuid']}' limit 1");
                            if ($row11['endtime'] < $zhdl) {
                                $DB->query("update qingka_wangke_user set `uuid`='$uuid' where uid='{$userrow['uid']}' ");
                                if ($DB) {
                                    jsonReturn(1, "迁移成功,您已迁移至[UID$uuid]的名下");
                                } else {
                                    jsonReturn(-1, "迁移失败,未知错误");
                                }
                            } else {
                                jsonReturn(-1, "上级在七天内有登陆记录，禁止转移");
                            }
                        } else {
                            jsonReturn(-1, "禁止填写自己的UID");
                        }
                    } else {
                        jsonReturn(-1, "该用户已经是你的上级了");
                    }
                } else {
                    jsonReturn(-1, "非该用户邀请码，请重新输入");
                }
            } else {
                jsonReturn(-1, "UID不存在，请重新输入");
            }
        }
        break;
    case 'getclassdata':
        if($userrow['uid']!=1){
		        exit('{"code":-1,"msg":"你在干嘛？？？"}');
		   }
        $uid = isset($_POST['uid']) ? $_POST['uid'] : '';
        $key = isset($_POST['key']) ? $_POST['key'] : '';
        $token = isset($_POST['token']) ? $_POST['token'] : '';
        $apiUrl = isset($_POST['url']) ? $_POST['url'] : '';
        $hid = isset($_POST['hid']) ? $_POST['hid'] : '';
        // 确保所有必要的参数都已提供
        if (empty($uid) || empty($key) || empty($apiUrl) || empty($token)) {
            echo json_encode(['code' => -1, 'msg' => '缺少必要的参数']);
            exit;
        }

        $apiUrl = rtrim($apiUrl, '/') . '/api.php?act=getclass'; // 确保URL格式正确

        $data = ['uid' => $uid, 'key' => $key,'token' => $token];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 如果是HTTPS请求，并且证书有问题，需要这行

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo json_encode(['code' => -2, 'msg' => 'cURL Error: ' . curl_error($ch)]);
            curl_close($ch);
            exit;
        }

        curl_close($ch);
        $responseData = json_decode($response, true);
        // 假设 $hid 是已经定义的变量，$responseData['data'] 是您提供的数组数据
        foreach ($responseData['data'] as $index => $item) {
            // 构造查询语句
            $existsResult = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_class WHERE docking = '$hid' AND noun = '{$item['cid']}'");
            
            // 根据查询结果设置 states 字段
            if ($existsResult && $existsResult->count > 0) {
                // 如果存在记录，states 设置为 1
                $responseData['data'][$index]['states'] = 1;
            } else {
                // 如果不存在记录，states 设置为 0
                $responseData['data'][$index]['states'] = 0;
            }
        }
        
        // 现在 $responseData['data'] 数组中的每个元素都根据数据库查询结果添加了 states 字段
        // 您可以输出或以其他方式使用更新后的数组
        if (!isset($responseData['data'])) {
            echo json_encode(['code' => -3, 'msg' => $response['msg'], 'rawResponse' => $response]);
            exit;
        }

        echo json_encode(['code' => 1, 'msg' => '操作成功', 'data' => $responseData['data']]);
        break;
		
	case 'iktz':
    	$oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $row=$DB->get_row("select * from qingka_wangke_order where oid='$oid'");
    	$info = "{$row['school']} {$row['user']} {$row['pass']} {$row['kcname']}";
    	$a = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$row["hid"]}' ");
        $ikun_surl = $a["url"];
        $ikun_url =$ikun_surl."/uporder/?token=".$row["yid"]."&state=".urlencode("已停止");
        $result =get_url($ikun_url); 
        $result = json_decode($result, true);
        if ($result["code"] >= 0) {
            $DB->query("UPDATE qingka_wangke_order SET status='已停止', remarks='停止成功，上次停止时间：$today_day' WHERE oid='{$oid}' ");
            wlog($userrow['uid'], "订单停止", "用户停止了订单 {$info}", 0);
            jsonReturn(1, $result['msg']);
            } else {
                jsonReturn(-1, $result['msg']);
            }
            break;
        
    case "addgonggao":
        // 获取并处理表单数据
        $form = daddslashes($_POST['form']);
        if ($userrow['uid'] != 1) {
            jsonReturn(-1, "无权限操作！");
        }
    
        // 校验必填字段
        if (empty($form['Title'])) jsonReturn(-1, "公告标题不能为空！");
        if (empty($form['Content'])) jsonReturn(-1, "公告内容不能为空！");
        if (empty($form['Type'])) jsonReturn(-1, "公告类型未选择！");
        if (empty($form['PublishDate'])) {
            $form['PublishDate']=$today_day;
        }
        // 插入公告数据
        $is = $DB->query("INSERT INTO qingka_wangke_gonggao (Type, Title, Content, PublishDate, Importance, ExpiryDate, UserID)
                          VALUES ('{$form['Type']}', '{$form['Title']}', '{$form['Content']}', '{$form['PublishDate']}', '{$form['Importance']}', '{$form['ExpiryDate']}', '{$userrow['uid']}')");
        if ($is) {
            jsonReturn(1, "公告发布成功！");
        } else {
            jsonReturn(-1, "公告发布失败！");
        }
        break;
    
    case "updategonggao":
        // 获取并处理表单数据
        $form = daddslashes($_POST['form']); // 这里直接获取form数据
        $AID = trim(strip_tags(daddslashes($_POST['AID']))); // 公告ID
        if ($userrow['uid'] != 1) {
            jsonReturn(-1, "无权限");
        }
        // 校验公告ID
        if (empty($AID)) jsonReturn(-1, "公告ID无效！");
    
        // 校验必填字段
        if (empty($form['Title'])) jsonReturn(-1, "公告标题不能为空！");
        if (empty($form['Content'])) jsonReturn(-1, "公告内容不能为空！");
        if (empty($form['ExpiryDate'])) jsonReturn(-1, "ExpiryDate不能为空！");
        if (empty($form['PublishDate'])) jsonReturn(-1, "PublishDate不能为空！");
        // 更新公告数据
        $is = $DB->query("UPDATE qingka_wangke_gonggao 
                          SET Title = '{$form['Title']}', Content = '{$form['Content']}', Type = '{$form['Type']}', Importance = '{$form['Importance']}', ExpiryDate = '{$form['ExpiryDate']}', PublishDate = '{$form['PublishDate']}'
                          WHERE AID = '{$AID}'");
        if ($is) {
            jsonReturn(1, "公告更新成功！");
        } else {
            jsonReturn(-1, "公告更新失败！");
        }
        break;

    case "deletegonggao":
        // 获取公告ID
        $AID = trim(strip_tags(daddslashes($_POST['AID'])));
        if ($userrow['uid'] != 1) {
            jsonReturn(-1, "无权限");
        }
        // 校验公告ID
        if (!$AID) jsonReturn(-1, "公告ID无效！");
        
        // 删除公告
        $is = $DB->query("DELETE FROM qingka_wangke_gonggao WHERE AID = '{$AID}'");
        if ($is) {
            jsonReturn(1, "公告删除成功！");
        } else {
            jsonReturn(-1, "公告删除失败！");
        }
        break;

    case "getgonggaolist":
        // 获取传递的类型参数
        $type = isset($_POST['type']) ? $_POST['type'] : null;
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1; // 将$page转换为整数，并默认为1
        $pageSize = isset($_POST['pageSize']) ? (int)$_POST['pageSize'] : 20; // 将$pageSize转换为整数，并默认为20
    
        // 计算起始行数，用于LIMIT子句
        $offset = ($page - 1) * $pageSize;
    
        // 指定要返回的字段
        $fields = "AID, Type, Title, Content, PublishDate, Importance";
    
        // 如果提供了类型参数，则根据类型进行筛选
        if ($type) {
            $totalQuery = "SELECT COUNT(*) as total FROM qingka_wangke_gonggao WHERE Type = '{$type}'";
            $rowsQuery = "SELECT {$fields} FROM qingka_wangke_gonggao WHERE Type = '{$type}' ORDER BY Importance DESC, AID DESC LIMIT {$pageSize} OFFSET {$offset}";
        } else {
            $totalQuery = "SELECT COUNT(*) as total FROM qingka_wangke_gonggao";
            $rowsQuery = "SELECT {$fields} FROM qingka_wangke_gonggao ORDER BY Importance DESC, AID DESC LIMIT {$pageSize} OFFSET {$offset}";
        }
    
        // 获取总记录数
        $totalRowsResult = $DB->query($totalQuery);
        if ($totalRowsResult) {
            $totalRows = $totalRowsResult->fetch_assoc()['total'];
        }
    
        // 获取当前页的数据
        $rowsResult = $DB->query($rowsQuery);
        $rows = [];
        while ($row = $rowsResult->fetch_assoc()) {
            $rows[] = $row;
        }
    
        // 检查查询结果并返回相应的JSON
        if ($rows) {
            // 计算是否还有更多数据
            $hasMore = ($page * $pageSize) < $totalRows;
    
            exit(json_encode(array(
                'code' => 1,
                'data' => $rows,
                'hasMore' => $hasMore, // 添加hasMore到返回的数据中
                "msg" => "获取公告列表成功",
                "total" => $totalRows // 可以返回总记录数，以便前端使用
            )));
        } else {
            exit(json_encode(array('code' => -1, 'data' => $rows, "msg" => "暂无公告信息")));
        }
        break;
        //FD章节测试插件
    case 'getzjcs':
        $uid = '';
        $key = '';
        $oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $a = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
        // 判断kcid是否包含任何字母
        if (preg_match('/[a-zA-Z]/', $a['kcid'])) {
            $kcid = $a['kcname'];
        } else {
            $kcid = $a['kcid'];
        }
        
        $data = array(
            "uid" => $uid,
            "key" => $key,
            "id" => $yid, // 确保$yid已经被定义，否则这里会有错误
            "school" => $a['school'],
            "user" => $a['user'],
            "pass" => $a['pass'],
            "kcid" => $kcid
        );
        
        $dx_rl = 'https://freedomp.icu/cjapi.php'; // 确保URL是正确的，移除了错误的转义字符
        $dx_url = "$dx_rl?act=getzjcs";
        $result = get_url($dx_url, $data); // 确保get_url函数存在且能正确发送请求
        $result = json_decode($result, true);
        exit(json_encode($result));
        break;
    }
?>