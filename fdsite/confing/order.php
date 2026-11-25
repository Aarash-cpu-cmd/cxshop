<?php
if (!defined('IN_CRONLITE')) exit();
$date1 = date("m-d");
$day1 = date("Y-m-d");
$date2 = date("m-d", strtotime("-1 day"));
$day2 = date("Y-m-d", strtotime("-1 day"));
$date3 = date("m-d", strtotime("-2 day"));
$day3 = date("Y-m-d", strtotime("-2 day"));
$date4 = date("m-d", strtotime("-3 day"));
$day4 = date("Y-m-d", strtotime("-3 day"));
$date5 = date("m-d", strtotime("-4 day"));
$day5 = date("Y-m-d", strtotime("-4 day"));
$date6 = date("m-d", strtotime("-5 day"));
$day6 = date("Y-m-d", strtotime("-5 day"));
$date7 = date("m-d", strtotime("-6 day"));
$day7 = date("Y-m-d", strtotime("-6 day"));
if($userrow['uid'] == 1){
    $order1 = $DB->count("select count(oid) from qingka_wangke_order where addtime>'$day1'");
    $order2 = $DB->count("select count(oid) from qingka_wangke_order where addtime<'$day1' and addtime>'$day2'");
    $order3 = $DB->count("select count(oid) from qingka_wangke_order where addtime<'$day2' and addtime>'$day3'");
    $order4 = $DB->count("select count(oid) from qingka_wangke_order where addtime<'$day3' and addtime>'$day4'");
    $order5 = $DB->count("select count(oid) from qingka_wangke_order where addtime<'$day4' and addtime>'$day5'");
    $order6 = $DB->count("select count(oid) from qingka_wangke_order where addtime<'$day5' and addtime>'$day6'");
    $order7 = $DB->count("select count(oid) from qingka_wangke_order where addtime<'$day6' and addtime>'$day7'");
    
    $adduser1 = $DB->count("select count(uid) from qingka_wangke_user where addtime>'$day1'");
    $adduser2 = $DB->count("select count(uid) from qingka_wangke_user where addtime<'$day1' and addtime>'$day2'");
    $adduser3 = $DB->count("select count(uid) from qingka_wangke_user where addtime<'$day2' and addtime>'$day3'");
    $adduser4 = $DB->count("select count(uid) from qingka_wangke_user where addtime<'$day3' and addtime>'$day4'");
    $adduser5 = $DB->count("select count(uid) from qingka_wangke_user where addtime<'$day4' and addtime>'$day5'");
    $adduser6 = $DB->count("select count(uid) from qingka_wangke_user where addtime<'$day5' and addtime>'$day6'");
    $adduser7 = $DB->count("select count(uid) from qingka_wangke_user where addtime<'$day6' and addtime>'$day7'");
}else{
    //七日订单数量
    $order1 = $DB->count("select count(oid) from qingka_wangke_order where uid='{$userrow['uid']}' and addtime>'$day1'");
    $order2 = $DB->count("select count(oid) from qingka_wangke_order where uid='{$userrow['uid']}' and addtime<'$day1' and addtime>'$day2'");
    $order3 = $DB->count("select count(oid) from qingka_wangke_order where uid='{$userrow['uid']}' and addtime<'$day2' and addtime>'$day3'");
    $order4 = $DB->count("select count(oid) from qingka_wangke_order where uid='{$userrow['uid']}' and addtime<'$day3' and addtime>'$day4'");
    $order5 = $DB->count("select count(oid) from qingka_wangke_order where uid='{$userrow['uid']}' and addtime<'$day4' and addtime>'$day5'");
    $order6 = $DB->count("select count(oid) from qingka_wangke_order where uid='{$userrow['uid']}' and addtime<'$day5' and addtime>'$day6'");
    $order7 = $DB->count("select count(oid) from qingka_wangke_order where uid='{$userrow['uid']}' and addtime<'$day6' and addtime>'$day7'");
    
    $adduser1 = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and addtime>'$day1'");
    $adduser2 = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and addtime<'$day1' and addtime>'$day2'");
    $adduser3 = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and addtime<'$day2' and addtime>'$day3'");
    $adduser4 = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and addtime<'$day3' and addtime>'$day4'");
    $adduser5 = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and addtime<'$day4' and addtime>'$day5'");
    $adduser6 = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and addtime<'$day5' and addtime>'$day6'");
    $adduser7 = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and addtime<'$day6' and addtime>'$day7'");
}


