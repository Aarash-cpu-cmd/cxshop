<?php
include('../confing/common.php');
$redis=new Redis();
$redis->connect("127.0.0.1","6379");
while(true){
    $oid=$redis->lpop('lhaddoid');
    $b=$DB->get_row("select * from qingka_wangke_order where oid='$oid' limit 1");
    if ($b) {
        if (($b['dockstatus'] == "0" || ($b['dockstatus'] == "2" && $b['hid'] != "100") || ($b['dockstatus']='1' && $b['status']='未知状态')) && $b['status'] != "已取消"){
            if($b['user']=="1"){
			 $DB->get_row("update qingka_wangke_order set `status`='请检查账号',`dockstatus`=2 where oid='{$b['oid']}' ");//对接成功       
		}elseif($b['school']==""){
			 $DB->get_row("update qingka_wangke_order set `status`='请检查学校名字',`dockstatus`=2 where oid='{$b['oid']}' ");//对接成功       
		}elseif($b['user']==""){
			 $DB->get_row("update qingka_wangke_order set `status`='请检查账号',`dockstatus`=2 where oid='{$b['oid']}' ");//对接成功       
		}else{
	 	     $result=addWk($b['oid']);
	    }
	    $d=$DB->get_row("select * from qingka_wangke_class where cid='{$b['cid']}' ");   	 	           
	  	if($result['code']=='1'){
	  	    $today_day=date("Y-m-d H:i:s");
	  	    $DB->get_row("update qingka_wangke_order set `hid`='{$d['docking']}',`status`='上号中',`dockstatus`=1,`yid`='{$result['yid']}' where oid='{$b['oid']}' ");//对接成功  
       	    echo("对接成功 uid：{$b['oid']},返回：{$result['msg']}，处理时间：{$today_day}\r\n");
        }elseif($b['dockstatus']='1' && $b['status']='未知状态'){
            $today_day=date("Y-m-d H:i:s");
        	$DB->query("update qingka_wangke_order set `dockstatus`=1,`yid`='{$result['yid']}' where oid='{$oid}' ");
        	echo("对接失败 uid：{$b['oid']},失败原因：{$result['msg']}，处理时间：{$today_day}\r\n");
        }else{
            $today_day=date("Y-m-d H:i:s");
        	$DB->query("update qingka_wangke_order set `dockstatus`=2,`yid`='{$result['yid']}',`remarks`='{$result['msg']}' where oid='{$oid}' ");
        	echo("对接失败 uid：{$b['oid']},失败原因：{$result['msg']}，处理时间：{$today_day}\r\n");
        }
        }
    }
    sleep(1);
}
?>