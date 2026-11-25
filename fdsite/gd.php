<?php
include('confing/common.php');
include('ayconfig.php');

$php_Self = substr($_SERVER['PHP_SELF'], strripos($_SERVER['PHP_SELF'], "/") + 1);
if ($php_Self != "gd.php") {
    $msg = '%E6%96%87%E4%BB%B6%E9%94%99%E8%AF%AF';
    $msg = urldecode($msg);
    exit(json_encode(['code' => -1, 'msg' => $msg]));
}
$supertoken = $DB->get_row("SELECT `tuisongtoken` FROM `qingka_wangke_user` WHERE `uid`='1'");
switch ($act) {
    
    case 'addTicket':
        $content = trim(strip_tags(daddslashes($_GET['content'])));
        if (empty($content)) {
            exit('{"code":-1,"msg":"问题内容不能为空"}');
        }
        $title = "无";
        $region = "其他问题";
        $date = date('Y-m-d H:i:s');
        $insertQuery = "INSERT INTO `qingka_wangke_gongdan` (`title`, `region`, `content`, `uid`, `state`, `addtime`) VALUES ('$title', '$region', '$content', '{$userrow['uid']}', '待回复', '$date')";
        if ($DB->query($insertQuery)) {
            $message = "用户ID:{$userrow['uid']}反馈了{$region}，请尽快处理。";
            $apiUrl = 'https://wxpusher.zjiecode.com/api/send/message/' . urlencode($supertoken['tuisongtoken']) . '/' . urlencode($message);
            $response = file_get_contents($apiUrl);
            exit('{"code":1,"msg":"工单新增成功"}');
        } else {
            exit('{"code":-1,"msg":"工单新增失败"}');
        }
    break;

    case 'feedback':
        $oid = intval($_GET['oid']);
        $feedback = trim(strip_tags(daddslashes($_GET['feedback'])));
        if (empty($oid) || empty($feedback)) {
            echo json_encode(['code' => 0, 'msg' => '订单ID或反馈内容不能为空']);
            exit;
        }
        if (strlen($feedback) > 50 || preg_match('/[0-9a-zA-Z]/', $feedback)) {
            echo json_encode(['code' => 0, 'msg' => '反馈内容不能超过50个字，且不能包含数字和字母']);
            exit;
        }
        $result = $DB->query("SELECT * FROM `qingka_wangke_order` WHERE `oid` = '$oid'");
    
        if ($result && $row = $result->fetch_assoc()) {
            $title = $row['ptname'] . ' ' . $row['school'] . ' ' . $row['user'] . ' ' . $row['pass'] . ' ' . $row['kcname'];
            $region = $oid;
            $content = "反馈问题: ".$feedback."\n该订单下单时间: " . $row['addtime'] . "\n当前状态: " . $row['status'] . "\n进度: " . $row['process'] . "\n详细信息: " . $row['remarks'] . "\n开考信息: " . $row['examStartTime'] . " - " . $row['examEndTime'];
    
            $date = date('Y-m-d H:i:s');
            $checkQuery = "SELECT * FROM `qingka_wangke_gongdan` WHERE `title` = '$title'";
            $checkResult = $DB->query($checkQuery);
    
            if ($checkResult && $checkResult->num_rows > 0) {
                echo json_encode(['code' => 0, 'msg' => '该工单已存在']);
                exit;
            }
            $insertQuery = "INSERT INTO `qingka_wangke_gongdan` (`title`, `region`, `content`, `uid`, `state`, `addtime`) VALUES ('$title', '$region', '$content', '{$userrow['uid']}', '待回复', '$date')";
            if ($DB->query($insertQuery)) {
                $message = "用户ID:{$userrow['uid']}反馈了{$oid}，请尽快处理。";
                $apiUrl = 'https://wxpusher.zjiecode.com/api/send/message/' . urlencode($supertoken['tuisongtoken']) . '/' . urlencode($message);
                $response = file_get_contents($apiUrl);
                echo json_encode(['code' => 1, 'msg' => '反馈成功']);
            } else {
                echo json_encode(['code' => 0, 'msg' => '反馈失败，请重试']);
            }
        } else {
            echo json_encode(['code' => 0, 'msg' => '订单不存在']);
        }
    break;

    case 'gdlist':
        $searchQuery = trim(strip_tags($_POST['searchQuery']));
        $statusFilter = trim(strip_tags($_POST['statusFilter'])); // 新增状态筛选参数
    
        if ($userrow['uid'] != '1') {
            $sql1 = "where uid='{$userrow['uid']}'";
        } else {
            $sql1 = "where 1=1";
        }
    
        if (!empty($searchQuery)) {
            $sql1 .= " AND (title LIKE '%$searchQuery%' OR region LIKE '%$searchQuery%')";
       }
    
        if (!empty($statusFilter)) {
            $sql1 .= " AND state='$statusFilter'"; // 新增状态筛选条件
        }
    
        $a = $DB->query("select * from qingka_wangke_gongdan {$sql1} order by gid desc");
        while ($row = $DB->fetch($a)) {
            $data[] = $row;
        }
    
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));
    break;
    
    case 'savePushToken':
        $token = trim(strip_tags(daddslashes($_GET['token'])));
        $updateQuery = "UPDATE `qingka_wangke_user` SET `tuisongtoken`='$token' WHERE `uid`='{$userrow['uid']}'";
        if ($DB->query($updateQuery)) {
            exit('{"code":1,"msg":"推送Token保存成功"}');
        } else {
            exit('{"code":-1,"msg":"推送Token保存失败"}');
        }
        break;
        case 'getPushToken':
        $token = $DB->get_row("SELECT `tuisongtoken` FROM `qingka_wangke_user` WHERE `uid`='{$userrow['uid']}'");
        if ($token) {
            exit(json_encode(['code' => 1, 'token' => $token['tuisongtoken']]));
        } else {
            exit(json_encode(['code' => -1, 'msg' => '暂未设置推送token']));
        }
    break;
    
    case 'shan':
        $gid = trim(strip_tags(daddslashes($_POST['gid'])));
        $b = $DB->get_row("select * from qingka_wangke_gongdan where gid='{$gid}'");
        if ($userrow['uid'] != $b['uid'] && $userrow['uid'] != '1') {
            exit('{"code":-1,"msg":"该工单不是你的！无法删除！"}');
        }
        $DB->query("delete from qingka_wangke_gongdan where gid='{$gid}'");
        exit('{"code":1,"msg":"删除成功！"}');
    break;

    case 'answer':
        $gid = trim(strip_tags(daddslashes($_POST['gid'])));
        $answer = trim(strip_tags(daddslashes($_POST['answer'])));
        $b = $DB->get_row("select * from qingka_wangke_gongdan where gid='{$gid}'");
        if ($userrow['uid'] != '1') {
            exit('{"code":-1,"msg":"无权限！"}');
        }
        $DB->query("update qingka_wangke_gongdan set `answer`='$answer',`state`='已回复' where gid='$gid'");
        $token = $DB->get_row("SELECT `tuisongtoken` FROM `qingka_wangke_user` WHERE `uid`='{$b['uid']}'");
        $message = "您反馈的{$b['region']}有新变动，最新回复内容为：【{$answer}】请及时前往查看";
        $apiUrl = 'https://wxpusher.zjiecode.com/api/send/message/' . urlencode($token['tuisongtoken']) . '/' . urlencode($message);
        $response = file_get_contents($apiUrl);
        exit('{"code":1,"msg":"回复成功！"}');
        break;

    case 'bohui':
        $gid = trim(strip_tags(daddslashes($_POST['gid'])));
        $answer = trim(strip_tags(daddslashes($_POST['answer'])));
        $b = $DB->get_row("select * from qingka_wangke_gongdan where gid='{$gid}'");
        if ($userrow['uid'] != '1') {
            exit('{"code":-1,"msg":"无权限！"}');
        }
        $DB->query("update qingka_wangke_gongdan set `answer`='$answer',`state`='已完成' where gid='$gid'");
        $token = $DB->get_row("SELECT `tuisongtoken` FROM `qingka_wangke_user` WHERE `uid`='{$b['uid']}'");
        $message = "您反馈的{$b['region']}已经完成，最新回复内容为：【{$answer}】请及时前往查看";
        $apiUrl = 'https://wxpusher.zjiecode.com/api/send/message/' . urlencode($token['tuisongtoken']) . '/' . urlencode($message);
        $response = file_get_contents($apiUrl);
        exit('{"code":1,"msg":"该订单已处理完成"}');
        break;
        
     case 'toanswer':
     $gid = trim(strip_tags(daddslashes($_POST['gid'])));
     $toanswer = trim(strip_tags(daddslashes($_POST['toanswer'])));
     $b = $DB->get_row("select * from qingka_wangke_gongdan where gid='{$gid}'");
    
    if ($b['state'] == '已完成') {
        exit('{"code":-1,"msg":"工单已完成，无法再追问"}');
    } else {
        $message = "用户ID:{$userrow['uid']}追问订单{$b['region']}处理进展，请尽快回复。";
        $apiUrl = 'https://wxpusher.zjiecode.com/api/send/message/' . urlencode($supertoken['tuisongtoken']) . '/' . urlencode($message);
        $response = file_get_contents($apiUrl);
        $DB->query("update qingka_wangke_gongdan set `content`='$toanswer',`state`='待回复' where gid='$gid'");
        exit('{"code":1,"msg":"二次提问完成"}');
    }
    break;
    
    
}
?>