<?php
include('../confing/common.php');
$redis=new Redis();
$redis->connect("127.0.0.1","6379");
$redis->select(7);

echo "连通redis： " . $redis->ping() . "\r\n";
    $lenth=$redis->LLEN('oidsydcl');
    if($lenth<=500){
        $i=0;
        $a=$DB->query("select * from qingka_wangke_order where dockstatus=1 and (status='待处理' or status='上号中' or status='补刷中'  or status='考试中' or status='补刷成功') order by oid asc");
        foreach($a as $b){
            $redis->lPush("oidsydcl",$b['oid']);
            $i++;
        }
        echo "入队成功！本次入队订单共计：".$i."条\r\n";
    }else {
        echo("入队失败！队列池还有：".$redis->LLEN('oidsydcl')."条订单正在执行\r\n");
        $list = $redis->lRange('oidsydcl', 0, -1); // 获取列表中的所有元素
    foreach($list as $id){
        echo $id . "\r\n";
    }
    }
?>