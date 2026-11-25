<?php

function processCx($oid)
{
    global $DB;
    $d = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
    $b = $DB->get_row("select hid,user,pass,cid from qingka_wangke_order where oid='{$oid}' ");
    $a = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$b["hid"]}' ");
    $type = $a["pt"];
    $cookie = $a["cookie"];
    $token = $a["token"];
    $ip = $a["ip"];
    $cid = $b["cid"];
    $user = $b["user"];
    $pass = $b["pass"];
    $kcname = $d["kcname"];
    $school = $d["school"];
    $pt = $d["noun"];
    $kcid = $d["kcid"];
    $doyid = $d["yid"];

    //暗网进度
    if ($type == "29") {
        $data = array("oid" => $d['yid'], "uid" => $a["user"], "key" => $a["pass"], "username" => $user,"token" => $token,"noun" => $pt,"course_name"=>$kcname);
        $dx_rl = $a["url"];
        $dx_url = "$dx_rl/api.php?act=chadanoid";
        $result = get_url($dx_url, $data);
        $result = json_decode($result, true);
        if ($result["code"] == "1") {
            foreach ($result["data"] as $res) {
                $yid = $res["id"];
                $kcname = $res["kcname"];
                $status = $res["status"];
                $process = $res["process"];
                $remarks = $res["remarks"];
                $kcks = $res["courseStartTime"];
                $kcjs = $res["courseEndTime"];
                $ksks = $res["examStartTime"];
                $ksjs = $res["examEndTime"];
                $b[] = array("code" => 1, "msg" => "查询成功", "yid" => $yid, "kcname" => $kcname, "user" => $user, "pass" => $pass, "kcks" => $kcks, "kcjs" => $kcjs, "ksks" => $ksks, "ksjs" => $ksjs, "status_text" => $status, "process" => $process, "remarks" => $remarks);
            }
        } else {
            $b[] = array("code" => -1, "msg" => "查询失败,请联系管理员");
        }
        return $b;
    }
    
    else {
        $b[] = array("code" => -1, "msg" => "查询失败,请联系管理员");
    }


}

?>