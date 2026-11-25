<?php
function wkname() {
    $data = array(
        "29" => "29通用",
    );
    return $data;
}
// 这里也要添加相应的接口数据  比如  "shenwei" => "神威",  后他设置里面才能看到
function addWk($oid) {
    global $DB;
    global $wk;
    $d = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
    $cid = $d["cid"];
    $school = $d["school"];
    $user = $d["user"];
    $pass = $d["pass"];
    $kcid = $d["kcid"];
    $status = $d["status"];
    $remark = $d["remarks"];
    $kcname = $d["kcname"];
    $noun = $d["noun"];
    $miaoshua = $d["miaoshua"];
    $b = $DB->get_row("select * from qingka_wangke_class where cid='{$cid}' ");
    $hid = $b["docking"];
    $a = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$hid}' ");
    $type = $a["pt"];
    $cookie = $a["cookie"];
    $token = $a["token"];
    $ip = $a["ip"];

       
    if ($type == "29") {
           $data = array("uid" => $a["user"], "key" => $a["pass"], "platform" => $noun, "school" => $school, "user" => $user, "pass" => $pass, "kcname" => $kcname, "kcid" => $kcid);
           $dx_rl = $a["url"];
           $dx_url = "$dx_rl/api.php?act=add";
           $result = get_url($dx_url, $data);
           $result = json_decode($result, true);
           if ($result["code"] == "0") {
              	return ['code'=> 1,'msg'=>'对接成功，id:'.$result['id'],'yid'=>$result['id']];
           } else {
              $b = array("code" => - 1, "msg" => $result["msg"]);
           }
           return $b;
        }
        //自定义更多
    elseif($type == "XXX"){
        //自定义
    }
    else {
        print_r("没有了,文件xdjk.php,可能故障：参数缺少，比如平台名错误！！！订单id：".$oid);
        die;
    }
}
?>