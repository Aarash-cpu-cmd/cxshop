<?php

@header('Content-Type: text/html; charset=UTF-8');
include("../confing/common.php");
require_once("epay/notify.class.php");
//得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
//查询
if($verify_result) {
	$out_trade_no=trim(strip_tags(daddslashes($_GET['out_trade_no'])));//商户订单号
	$trade_no=trim(strip_tags(daddslashes($_GET['trade_no'])));//支付宝交易号
	$trade_status=trim(strip_tags(daddslashes($_GET['trade_status'])));//交易状态
	$name=trim(strip_tags(daddslashes($_GET['name'])));
	$money=trim(strip_tags(daddslashes($_GET['money'])));
	$type=trim(strip_tags(daddslashes($_GET['type'])));
	//$mjyh = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_mijia WHERE uid='{$userrow['uid']}' ORDER BY mid DESC LIMIT 0,1");
	//$mjyh = $DB->count("select count(*) from qingka_wangke_mijia WHERE uid='{$userrow['uid']}' ");
	$srow=$DB->get_row("SELECT * FROM qingka_wangke_pay WHERE out_trade_no='{$out_trade_no}' limit 1 for update");
	$userrow=$DB->get_row("SELECT * FROM qingka_wangke_user WHERE uid='{$srow['uid']}'");
	$mjyh = $DB->get_row("SELECT COUNT(*) as count FROM qingka_wangke_mijia WHERE uid='{$userrow['uid']}' ");
	if ($money<0) {
	    $money3=0;
	}
	if ($money>=50) {
	    $money3=$money*0.1;
	}
	if ($money>=100) {
	    $money3=$money*0.15;
	}
	if ($money>=200) {
	    $money3=$money*0.2;
	}
	if ($money>=300) {
	    $money3=$money*0.25;
	}
	if ($money>=500) {
	    $money3=$money*0.3;
	}
	if ($mjyh['count'] > 0) {
	    $money3=$money*0;
	}
	

    $money1= $userrow['money'];
	$money2 = $money1+$money+$money3;
	if ($money3!=0) {
	    $cg="用户[{$userrow['user']}]成功充值".$money."币；本次赠送".$money3."币! ";
	}elseif($mjyh['count'] > 0) {
	    $cg="用户[{$userrow['user']}]成功充值".$money."币! 您为尊贵的密接用户，专属赠送请联系上级！";
	}else {
	    $cg="用户[{$userrow['user']}]成功充值".$money."币!";
	}
    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
		if($srow['status']==0 && $srow['money']==$money){
			$DB->query("update `qingka_wangke_pay` set `status` ='1',`endtime` ='$date',`trade_no`='{$trade_no}' where `out_trade_no`='{$out_trade_no}'");
            $DB->query("update `qingka_wangke_user` set `money`='{$money2}',`zcz`=zcz+'$money' where uid='{$userrow['uid']}'");   	
			wlog($userrow['uid'],"在线充值","用户[{$userrow['user']}]在线充值了{$money}币",$money);
			if ($money3!=0) {
           	    wlog($userrow['uid'],"充值赠送","用户[{$userrow['user']}]充值金额达标赠送{$money3}币",$money3);
           	}
			exit("<script language='javascript'>alert('$cg');history.go(-1);window.close();</script>");
			//exit("<script language='javascript'>alert('$cg');window.location.href='../index/pay';</script>");
		}else{
			$DB->query("update `qingka_wangke_pay` set `status` ='1',`endtime` ='$date',`trade_no`='{$trade_no}' where `out_trade_no`='{$out_trade_no}'"); 
           	wlog($userrow['uid'],"在线充值重复刷新","重复刷新--用户[{$userrow['user']}]在线充值了{$money}币",$money);
			exit("<script language='javascript'>alert('已充值，请勿重复刷新');window.location.href='../index/pay';</script>");
		}
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
}
 else {
	   exit("<script language='javascript'>alert('充值失败!');window.location.href='../index/index';</script>");
}

?>