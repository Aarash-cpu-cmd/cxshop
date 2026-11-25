<?php
include ('confing/common.php');
$xz0 = 5; //API限制金额
$xz1 = $conf['api_ck']; //查课限制金额
$xz2 = $conf['api_xd']; //下课限制金额
$xz3 = $conf['api_proportion'];//下单/查课限制
$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;
@header('Content-Type: application/json; charset=UTF-8');
$uid = trim(strip_tags(daddslashes($_POST['uid'])));
$key = trim(strip_tags(daddslashes($_POST['key'])));
if ($uid == '' || $key == '') {
    exit('{"code":0,"msg":"所有项目不能为空"}');
} else {
    $row = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
    if (!$row) {
        $result = array("code" => - 1, "msg" => "UID错误！");
        exit(json_encode($result));
    }
    elseif ($row['key'] == '0') {
        $result = array("code" => - 1, "msg" => "你还没有开通接口哦");
        exit(json_encode($result));
    } elseif ($row['key'] != $key) {
        $result = array("code" => - 2, "msg" => "密匙错误!");
        exit(json_encode($result));
    } elseif ($row['money'] < $xz0) {
        $result = array("code" => - 2, "msg" => "您的余额不足{$xz0}，不符合API调用协议，请充值后使用API");
        exit(json_encode($result));
    } else {
        switch ($act) {
            
        case 'getclass':
            $uid = trim(strip_tags(daddslashes($_POST['uid'])));
            $key = trim(strip_tags(daddslashes($_POST['key'])));
            if ($uid == '' || $key == '') {
                exit('{"code":0,"msg":"所有项目不能为空"}');
            }
            $userrow = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
            if ($userrow['key'] == '0') {
                $result = array("code" => -1, "msg" => "你还没有开通接口哦");
                exit(json_encode($result));
            } elseif ($userrow['key'] != $key) {
                $result = array("code" => -2, "msg" => "密匙错误");
                exit(json_encode($result));
            }
        
            if ($_REQUEST['cid']) {
                $a = $DB->query("select * from qingka_wangke_class where status=1 and cid = '{$_REQUEST['cid']}' order by sort desc");
            } else {
                $a = $DB->query("select * from qingka_wangke_class where status=1 order by sort desc");
            }
        
            // 获取分类名称
            $b = $DB->query("select * from qingka_wangke_fenlei where status!=3 order by id desc");
            $categories = [];
            while ($row_b = $DB->fetch($b)) {
                $categories[$row_b['id']] = $row_b['name'];
            }
        
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
        
                // 密价
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
        
                if ($price >= $price1) { //密价价格大于原价，恢复原价
                    $price = $price1;
                }
        
                // 获取分类名称
                $category_name = isset($categories[$row['fenlei']]) ? $categories[$row['fenlei']] : "未分类";
        
                $data[] = array(
                    'sort' => $row['sort'],
                    'cid' => $row['cid'],
                    'kcid' => $row['kcid'],
                    'fenlei' => $row['fenlei'],
                    'name' => $row['name'],
                    'noun' => $row['noun'],
                    'price' => $price,
                    'content' => $row['content'],
                    'status' => $row['status'],
                    'miaoshua' => $row['miaoshua'],
                    'category_name' => $category_name // 使用分类名称
                );
            }
        
            foreach ($data as $key => $row) {
                $sort[$key]  = $row['sort'];
                $cid[$key] = $row['cid'];
                $kcid[$key] = $row['kcid'];
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
            
            case 'getmoney': //获取余额
                $result = array('code' => 1, 'msg' => '查询成功', 'user' => $row['user'], 'name' => $row['name'],  'money' => $row['money']);
                exit(json_encode($result));
            break;
            case 'get': //查课
                if ($row['money'] > $xz1) {
                    $platform = daddslashes($_POST['platform']);
                    $school = daddslashes($_POST['school']);
                    $user = daddslashes($_POST['user']);
                    $pass = daddslashes($_POST['pass']);
                    $type = daddslashes($_POST['type']);
                    if ($platform == '' || $school == '' || $user == '' || $pass == '') {
                        exit('{"code":0,"msg":"请检查项目是否完整"}');
                    }
                    $rs = $DB->get_row("select * from qingka_wangke_class where cid='$platform' limit 1 ");
                    if ($rs['status'] == 0) {
                        $result = array("code" => - 2, "msg" => "当前项目已下架！！");
                        exit(json_encode($result));
                    }
                    $ckzs = $DB->count("select count(*) from qingka_wangke_log where uid='$uid' and type='API查课' ");
                    $xdjs = $DB->count("select count(*) from qingka_wangke_log where uid='$uid' and type='API添加任务' ");
                    $xdsl = $DB->count("select count(*) from qingka_wangke_log where uid='$uid' and type='添加任务' ");
                    if($ckzs <=20){
                        $ckzs=100;
                    }
                    if($xdjs <=20){
                        $xdjs=100;
                    }
                    $xdzs = $xdjs + $xdsl;
                    $ckl = round($xdzs / $ckzs * 100);
                    if ($ckl >= $xz3) {
                    } else {
                        $result = array("code" => - 2, "msg" => "查课下单率低于{$xz3}%,当前查课率{$ckl}%");
                        exit(json_encode($result));
                    }
                    $result = getWk($rs['queryplat'], $rs['getnoun'], $school, $user, $pass, $rs['name']);
                    $result['userinfo'] = $school . " " . $user . " " . $pass;
                    $DB->query("update qingka_wangke_user set `todayck`=todayck+1 where uid='{$row['uid']}' limit 1 ");
                    wlog($uid, "API查课", "{$rs['name']}-查课信息：{$school} {$user} {$pass}", 0);
                    if ($type == "xiaochu") {
                        foreach ($result['data'] as $row) {
                            if ($value == '') {
                                $value = $row['name'];
                            } else {
                                $value = $value . ',' . $row['name'];
                            }
                        }
                        $v[0] = $rs['name'];
                        $v[1] = $user;
                        $v[2] = $pass;
                        $v[3] = $school;
                        $v[4] = $value;
                        $data = array('code' => $result['code'], 'msg' => $result['msg'], 'data' => $v, 'js' => '', 'info' => '欢迎使用感谢您的信任与支持');
                        exit(json_encode($data));
                    } else {
                        exit(json_encode($result));
                    }
                } else {
                    $result = array("code" => - 1, "msg" => "你的余额不足{$xz1}元，请尽快充值");
                    exit(json_encode($result));
                }
            break;
            case 'add': //下单
                if ($row['money'] > $xz2) {
                    $platform = daddslashes($_POST['platform']);
                    $school = daddslashes($_POST['school']);
                    $user = daddslashes($_POST['user']);
                    $pass = daddslashes($_POST['pass']);
                    $kcid = daddslashes($_POST['kcid']);
                    $kcname = daddslashes($_POST['kcname']);
                    $clientip = real_ip();
                    if ($platform == '' || $school == '' || $user == '' || $pass == '' || $kcname == '') {
                        exit('{"code":0,"msg":"所有项目不能为空"}');
                    }
                    $rs = $DB->get_row("select * from qingka_wangke_class where cid='$platform' limit 1 ");
                    if ($rs['status'] == 0) {
                        $result = array("code" => - 2, "msg" => "商品已下架，下单失败！");
                        exit(json_encode($result));
                    }
                    $res = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$docking}' limit 1 ");
                    if ($rs['yunsuan'] == "*") {
                        $danjia = round($rs['price'] * $row['addprice'], 2);
                        $danjia1 = $danjia;
                    } elseif ($rs['yunsuan'] == "+") {
                        $danjia = round($rs['price'] + $row['addprice'], 2);
                        $danjia1 = $danjia;
                    } else {
                        $danjia = round($rs['price'] * $row['addprice'], 2);
                        $danjia1 = $danjia;
                    }
                    //密价
                    $mijia = $DB->get_row("select * from qingka_wangke_mijia where uid='{$uid}' and cid='{$platform}' ");
                    if ($mijia) {
                        if ($mijia['mode'] == 0) {
                            $danjia = round($danjia - $mijia['price'], 2);
                            if ($danjia <= 0) {
                                $danjia = 0;
                            }
                        } elseif ($mijia['mode'] == 1) {
                            $danjia = round(($rs['price'] - $mijia['price']) * $row['addprice'], 2);
                            if ($danjia <= 0) {
                                $danjia = 0;
                            }
                        } elseif ($mijia['mode'] == 2) {
                            $danjia = $mijia['price'];
                            if ($danjia <= 0) {
                                $danjia = 0;
                            }
                        }
                    }
                    if ($danjia >= $danjia1) { //密价价格大于原价，恢复原价
                        $danjia = $danjia1;
                    }
                    if ($danjia == 0 || $row['addprice'] < 0.01) {
                        exit('{"code":-1,"msg":"免费商品禁止使用api进行下单"}');
                    }
                    if ($res['pt'] == 'wkm4') {
                        $m4 = getWk($rs['queryplat'], $rs['getnoun'], $school, $user, $pass, $rs['name']);
                        if ($m4['code'] == '1') {
                            for ($i = 0;$i < count($m4['data']);$i++) {
                                $kcid = $m4['data'][$i]['id'];
                                $kcname1 = $m4['data'][$i]['name'];
                                if ($kcname1 == $kcname) {
                                    break;
                                } else {
                                    exit('{"code":-1,"msg":"请完整输入课程名字","status":-1,"message":"请完整输入课程名字"}');
                                }
                            }
                        }
                    }
                    $c = explode(",", $kcname);
                    $d = explode(",", $kcid);
                    for ($i = 0;$i < count($c);$i++) {
                        if ($row['money'] < $danjia * count($c)) {
                            exit('{"code":-1,"msg":"余额不足以本次提交"}');
                        }
                        if ($DB->get_row("select * from qingka_wangke_order where ptname='{$rs['name']}' and school='$school' and user='$user' and pass='$pass' and kcid='$kcid' and kcname='$kcname' and addtime >= CURDATE() - INTERVAL 100 DAY ")) {
                            //$dockstatus = '3'; //重复下单
                            exit('{"code":-1,"msg":"存在100天内的重复订单！请勿重复提交！"}');
                        } elseif ($rs['docking'] == '0') {
                            $dockstatus = '99';
                        } else {
                            $dockstatus = '0';
                        }
                        $is = $DB->query("insert into qingka_wangke_order (uid,cid,fenlei,hid,ptname,school,user,pass,kcid,kcname,fees,noun,miaoshua,addtime,ip,dockstatus) values ('{$uid}','{$rs['cid']}','{$rs['fenlei']}','{$rs['docking']}','{$rs['name']}','{$school}','$user','$pass','$d[$i]','$c[$i]','{$danjia}','{$rs['noun']}','$miaoshua','$date','$clientip','$dockstatus') "); //将对应课程写入数据库
                        if ($is) {
                            $order_id = $DB->insert_id; // 获取新插入订单的ID
                            $DB->query("update qingka_wangke_user set money=money-'{$danjia}',`todayadd`=todayadd+1 where uid='{$row['uid']}' limit 1 ");
                            wlog($row['uid'], "API添加任务", "{$user} {$pass} {$c[$i]} 扣除{$danjia}元！", -$danjia);
                            $ok = 1;
                        }
                    }
                    if ($ok == 1) {
                        exit(json_encode(array("code" => 0,"msg" => "提交成功","status" => 0,"message" => "提交成功","id" => $order_id)));
                    } else {
                        exit('{"code":-1,"msg":"请完整输入课程名字","status":-1,"message":"请完整输入课程名字"}');
                    }
                } else {
                    $result = array("code" => - 1, "msg" => "你的余额不足{$xz2}元，请尽快充值");
                    exit(json_encode($result));
                }
            break;
            case 'budan': //补刷
                $oid = daddslashes($_POST['id']);
                $b = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
                $info = "{$b['ptname']} {$b['school']} {$b['user']} {$b['pass']} {$b['kcname']}";
                /*/if ($b['bsnum'] > 200) {
                    exit('{"code":-1,"msg":"该订单补刷已超过200次，还补呢？我劝你好自为之！"}');
                }/*/
                $c = budanWk($oid);
                if ($c['code'] == 1) {
                    $DB->query("update qingka_wangke_order set status='补刷中',`bsnum`=bsnum+1 where oid='{$oid}' ");
                    wlog($uid, "API补刷", "用户补刷了订单 {$info}", 0);
                    jsonReturn(1, $c['msg'] ?? $c['message']);
                } else {
                    jsonReturn(-1, $c['msg'] ?? $c['message']);
                }
            break;
            case 'up'://进度刷新
                $oid=daddslashes($_POST['id']);
                $row=$DB->get_row("select hid from qingka_wangke_order where oid='{$oid}' ");
                if($row['hid']=='ximeng'){
                 	exit('{"code":-2,"msg":"当前订单接口异常，请去查询补单","url":""}');
                }elseif($row['dockstatus']=='99'){
                       $result=pre_zy($oid);
                       exit(json_encode($result));
                }       	     
                   $result=processCx($oid);
                   for($i=0;$i<count($result);$i++){
                    	$a=$DB->query("update qingka_wangke_order set `yid`='{$result[$i]['yid']}',`status`='{$result[$i]['status_text']}',`courseStartTime`='{$result[$i]['kcks']}',`courseEndTime`='{$result[$i]['kcjs']}',`examStartTime`='{$result[$i]['ksks']}',`examEndTime`='{$result[$i]['ksjs']}',`process`='{$result[$i]['process']}',`remarks`='{$result[$i]['remarks']}' where `user`='{$result[$i]['user']}' and `kcname`='{$result[$i]['kcname']}' ");    	
                   }
                   exit('{"code":1,"msg":"同步成功"}');
                break;
            
            case 'chadanoid': //进度
                $oid = trim(strip_tags(daddslashes($_POST['yid'])));
                $username = trim(strip_tags(daddslashes($_POST['username'])));
                $school = trim(strip_tags(daddslashes($_POST['school'])));
                
                if ($oid == "" && $username == "" && $school == "") {
                    $data = array('code' => -1, 'msg' => "至少需要一个有效参数");
                    exit(json_encode($data));
                }
                
                // 构建查询条件
                $conditions = [];
                if ($oid != "") {
                    $conditions[] = "oid = '$oid'";
                }
                if ($username != "") {
                    $conditions[] = "user = '$username'";
                }
                if ($school != "") {
                    $conditions[] = "school LIKE '%$school%'";
                }
                $conditions[] = "dockstatus = 1 ";
                $conditions[] = "uid = '$uid'";  // 假设$uid始终有效
                // 假设$conditions是一个条件数组
                $conditions[] = "addtime >= DATE_SUB(NOW(), INTERVAL 100 DAY)";
                // 将所有条件连接成SQL查询语句
                $sql = "SELECT oid FROM qingka_wangke_order WHERE " . implode(" AND ", $conditions) . " ORDER BY oid DESC";
                
                // 第一次查询，获取所有符合条件的订单ID（oid）
                $result = $DB->query($sql);
                $oids = array();
                if ($result) {
                    while ($row = $DB->fetch($result)) {
                        $oids[] = $row['oid'];
                    }
                }
                
                // 检查是否查询到了订单ID
                if (!empty($oids)) {
                    // 对所有获取到的 oid 进行同步操作，调用 processCx 函数并更新数据库
                    foreach ($oids as $oid) {
                        $cxResult = processCx($oid);
                        if ($cxResult) {
                            foreach ($cxResult as $item) {
                                $updateSql = "UPDATE qingka_wangke_order 
                                              SET yid = '{$item['yid']}', 
                                                  status = '{$item['status_text']}', 
                                                  courseStartTime = '{$item['kcks']}', 
                                                  courseEndTime = '{$item['kcjs']}', 
                                                  examStartTime = '{$item['ksks']}', 
                                                  name = '{$item['name']}', 
                                                  examEndTime = '{$item['ksjs']}', 
                                                  process = '{$item['process']}', 
                                                  remarks = '{$item['remarks']}' 
                                              WHERE `oid` = '{$oid}' AND `user` = '{$item['user']}' 
                                              AND `kcname` = '{$item['kcname']}' ";
                                $DB->query($updateSql);
                            }
                        }
                    }
                }
                
                // 第二次查询，获取更新后的订单信息
                $sql = "SELECT oid, ptname, school, name, user, kcname, addtime, courseStartTime, courseEndTime, examStartTime, examEndTime, status, process, remarks 
                        FROM qingka_wangke_order 
                        WHERE " . implode(" AND ", $conditions) . " ORDER BY oid DESC";
                
                $result = $DB->query($sql);
                $orders = array();
                if ($result) {
                    while ($row = $DB->fetch($result)) {
                        $orders[] = array(
                            'id' => $row['oid'],
                            'ptname' => $row['ptname'],
                            'school' => $row['school'],
                            'name' => $row['name'],
                            'user' => $row['user'],
                            'kcname' => $row['kcname'],
                            'addtime' => $row['addtime'],
                            'courseStartTime' => $row['courseStartTime'],
                            'courseEndTime' => $row['courseEndTime'],
                            'examStartTime' => $row['examStartTime'],
                            'examEndTime' => $row['examEndTime'],
                            'status' => $row['status'],
                            'process' => $row['process'],
                            'remarks' => $row['remarks']
                        );
                    }
                    $data = array('code' => 1, 'data' => $orders);
                    exit(json_encode($data));
                } else {
                    $data = array('code' => -1, 'msg' => "未查到更新后的订单信息");
                    exit(json_encode($data));
                }
                break;

            
            case 'xgmm':
                // 获取前端传递的参数，这里使用explode函数将字符串转换为数组
                $oids = isset($_POST['oid']) ? explode(',', $_POST['oid']) : [];
                $newpass = trim(strip_tags(daddslashes($_POST['pwd'])));
                $xgkf = 0.01;//改密扣费
                // 检查新密码是否为空
                if (empty($newpass)) {
                    exit(json_encode(array("code" => -1, "msg" => "新密码不能为空")));
                }
                // 遍历处理每个订单ID
                foreach ($oids as $oid) {
                    $oid = trim(strip_tags(daddslashes($oid)));
                    $x = $DB->get_row("SELECT * FROM qingka_wangke_order WHERE oid = '$oid' ");
                    if ($row['uid'] != $x['uid']) {
                        jsonReturn(-1, "该订单不是你的，无法修改！");
                    }elseif($newpass==$x['pass']){
                        jsonReturn(-1, "新密码不能和旧密码相同！");
                    }else{
                        $result = gaimiWk($oid, $newpass);
                        // 检查$result['code'] == '1'
                        if ($result['code'] == '1') {
                            $sql = "UPDATE qingka_wangke_order SET pass = '{$newpass}' WHERE oid = '{$oid}' ";
                            if (!$DB->query($sql)) {
                                exit(json_encode(array("code" => -1, "msg" => "修改失败，无权限或数据库错误")));
                                wlog($userrow['uid'], "API改密", "订单修改密码失败！信息：项目：【{$x['ptname']}】- 账号：【{$x['user']}】-课程：【{$x['kcname']}】-原密码：【{$x['pass']}】-新密码： 【{$newpass}】", 0); // 添加日志记录
                            }
                            $DB->query("update qingka_wangke_user set `money`=`money`-'$xgkf' where uid='{$userrow['uid']}' ");
                            wlog($userrow['uid'], "API改密", "订单修改密码成功！信息：项目：【{$x['ptname']}】- 账号：【{$x['user']}】-课程：【{$x['kcname']}】-原密码：【{$x['pass']}】-新密码： 【{$newpass}】", -$xgkf); // 添加日志记录
                        } else {
                            exit(json_encode(array("code" => -1, "msg" =>$result['msg'])));
                        }
                    }
            }
                // 如果所有订单都成功修改，则返回成功消息
                exit(json_encode(array("code" => 1, "msg" => $result['msg'] ?? $result['message'])));
            break;
            
            case 'fdtz':
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
        }
    }
}
