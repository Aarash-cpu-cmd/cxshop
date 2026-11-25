<?php
include('../confing/common.php');
if($islogin!=1){exit("<script language='javascript'>window.location.href='login';</script>");}
if($conf['bz']==1){if($userrow['uid']!="1"){exit("<script language='javascript'>window.location.href='../bz';</script>");}}
$dd=$DB->count("select count(oid) from qingka_wangke_order WHERE uid='{$userrow['uid']}' ");
$ck=$DB->count("SELECT count(id) FROM `qingka_wangke_log` WHERE type='API查课' AND uid='{$userrow['uid']}' ");
$xd=$DB->count("SELECT count(id) FROM `qingka_wangke_log` WHERE type='API添加任务' AND uid='{$userrow['uid']}' ");
$djname=$DB->get_row("select name from qingka_wangke_dengji WHERE rate='{$userrow['addprice']}'");
if($ck==0 || $xd==0){
    $xdb=100;
}else{
    $xdb=round($xd/$ck,4)*100;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title><?=$conf['sitename']?></title>
<meta name="keywords" content="<?=$conf['keywords'];?>" />
<meta name="description" content="<?=$conf['description'];?>" />
<link rel="icon" href="assets/LightYear/favicon.ico" type="image/ico">
<link rel="stylesheet" href="assets/js/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/apps.css" type="text/css" />
<link rel="stylesheet" href="assets/css/app.css" type="text/css" />
<link rel="stylesheet" href="assets/layui/css/layui.css" type="text/css" />
<link href="assets/js/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/LightYear/js/bootstrap-multitabs/multitabs.min.css">
<link href="assets/LightYear/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/LightYear/css/style.min.css" rel="stylesheet">
<link href="assets/LightYear/css/materialdesignicons.min.css" rel="stylesheet">
<script src="assets/js/jquery.min.js"></script>
<script src="layer/3.1.1/layer.js"></script>
<script src="assets/vue.min.js"></script>
</head>
<?php
if($userrow['active']=="0"){
alert('您的账号已被封禁！','login');
}
?>
<body>
    
    
<!--<script>
const handler = setInterval(function () { console.clear(); const before = new Date(); debugger; const after = new Date(); const cost = after.getTime() - before.getTime(); if (cost > 100) { } }, 1);
        //屏蔽右键菜单
        document.oncontextmenu = function (event) {
            if (window.event) {
                event = window.event;
            }
            try {
                var the = event.srcElement;
                if (!((the.tagName == "INPUT" && the.type.toLowerCase() == "text") || the.tagName == "TEXTAREA")) {
                    return false;
                }
                return true;
            } catch (e) {
                return false;
            }
        }
        //禁止f12
        function fuckyou() {
            window.open("/", "_blank"); //新窗口打开页面
            window.close(); //关闭当前窗口(防抽)
            window.location = "about:blank"; //将当前窗口跳转置空白页
        }
         //禁止Ctrl+U
        var arr = [123, 17, 18];
        document.oncontextmenu = new Function("event.returnValue=false;"), //禁用右键
 
            window.onkeydown = function (e) {
                var keyCode = e.keyCode || e.which || e.charCode;
                var ctrlKey = e.ctrlKey || e.metaKey;
                console.log(keyCode + "--" + keyCode);
                if (ctrlKey && keyCode == 85) {
                    e.preventDefault();
                }
                if (arr.indexOf(keyCode) > -1) {
                    e.preventDefault();
                }
            }
</script>-->