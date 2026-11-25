<?php
include('../confing/common.php');
$redis=new Redis();
$redis->connect("127.0.0.1","6379");

echo "连通redis： " . $redis->ping() . "\r\n";
$lenth=$redis->LLEN('lhaddoid');

if($lenth==0){
    $i=0;
    $a = $DB->query("SELECT * FROM qingka_wangke_order WHERE (dockstatus='0' OR (dockstatus='2' AND hid!='100') or (dockstatus='1' AND status='未知状态')) AND status!='已取消'  ORDER BY oid ASC");
    foreach($a as $b){
        $redis->lPush("lhaddoid",$b['oid']);
        $i++;
        echo "加入队列的订单ID: " . $b['oid'] . "\r\n";
    }
    echo "入队成功！本次入队订单共计：".$i."条\r\n";
}else {
    // 打印队列中所有订单的ID
    echo "入队失败！队列池还有：" . $lenth . " 条订单正在执行。队列中的订单ID如下：\r\n";
    $list = $redis->lRange('lhaddoid', 0, -1); // 获取列表中的所有元素
    foreach($list as $id){
        echo $id . "\r\n";
    }
}
?>