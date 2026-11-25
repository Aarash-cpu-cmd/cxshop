<?php 
// 查课接口设置
function getWk($type, $noun, $school, $user, $pass, $name = false){
	global $DB;
	global $wk;
	$a = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$type}' ");
	$type = $a["pt"];
	$cookie = $a["cookie"];
	$token = $a["token"];

    //暗网查课
    if ($type == "29") {
       $data = array("uid" => $a["user"], "key" => $a["pass"], "school" => $school, "user" => $user, "pass" => $pass, "platform" => $noun, "kcid" => $kcid);
       $dx_rl = $a["url"];
       $dx_url = "$dx_rl/api.php?act=get";
       $result = get_url($dx_url, $data);
       $result = json_decode($result, true);
       return $result;
    }
    
	else {
    print_r("没有了,文件ckjk.php,可能故障：参数缺少，比如平台名错误！！！");die;
	}
}
 

?>