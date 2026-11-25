<?php
function gaimiWk($oid,$newpass) {
    global $DB;
    $d = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
    $b = $DB->get_row("select hid,yid,user from qingka_wangke_order where oid='{$oid}' ");
    $hid = $b["hid"];
    $yid = $b["yid"];
    $user = $b["user"];
    $a = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$hid}' ");
    $type = $a["pt"];
    $cookie = $a["cookie"];
    $token = $a["token"];
    $ip = $a["ip"];
    $cid = $d["cid"];
    $school = $d["school"];
    $user = $d["user"];
    $pass = $d["pass"];
    $kcid = $d["kcid"];
    $kcname = $d["kcname"];
    $noun = $d["noun"];
    $miaoshua = $d["miaoshua"];
    
    //29项目
    if ($type == "29") {
        $data = array("uid" => $a["user"], "key" => $a["pass"], "oid" => $yid,'pwd' =>$newpass);
        $dx_rl = $a["url"];
        $dx_url = "$dx_rl/api.php?act=xgmm";
        $result = get_url($dx_url, $data);
        $result = json_decode($result, true);
        return $result;
        }
    else {
        $b = array("code" => -1, "msg" => "该项目暂不支持修改密码，请联系管理员");
        return $b;
    }
}
?>