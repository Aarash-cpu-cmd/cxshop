<?php
include('head.php');
include('../confing/order.php');

// 获取最近七天的日期
$dateArray = [];
for ($i = 0; $i < 7; $i++) {
    $dateArray[] = date('Y-m-d', strtotime("-$i days"));
}

// 获取统计数据函数
function getStatistics($DB, $jtdate, $ztdate, $dateArray) {
    $statistics = [];

    // 总用户数
    $statistics['totalUsers'] = $DB->count("SELECT COUNT(*) FROM qingka_wangke_user");

    // 今日新增用户数
    $statistics['todayNewUsers'] = $DB->count("SELECT COUNT(*) FROM qingka_wangke_user WHERE addtime > '$jtdate'");

    // 今日订单数
    $statistics['todayOrders'] = $DB->count("SELECT COUNT(*) FROM qingka_wangke_order WHERE addtime > '$jtdate'");

    // 今日销售额
    $a = $DB->query("SELECT * FROM qingka_wangke_log WHERE addtime > '$jtdate' AND money < 0 AND type != '代理充值'");
    $zcz = 0;
    while ($c = $DB->fetch($a)) {
        $zcz += $c['money'];
    }
    $statistics['todaySales'] = -$zcz;

    // 昨日订单数
    $statistics['yesterdayOrders'] = $DB->count("SELECT COUNT(*) FROM qingka_wangke_order WHERE addtime > '$ztdate' AND addtime < '$jtdate'");

    // 昨日销售额
    $a = $DB->query("SELECT * FROM qingka_wangke_log WHERE addtime > '$ztdate' AND addtime < '$jtdate' AND money < 0 AND type != '代理充值'");
    $zcz = 0;
    while ($c = $DB->fetch($a)) {
        $zcz += $c['money'];
    }
    $statistics['yesterdaySales'] = -$zcz;

    // 最近七天销售额
    $statistics['lastSevenDaysSales'] = [];
foreach (array_reverse($dateArray) as $date) {
    $startOfDay = "$date 00:00:00";
    $endOfDay = "$date 23:59:59";
    $a = $DB->query("SELECT * FROM qingka_wangke_log WHERE addtime BETWEEN '$startOfDay' AND '$endOfDay' AND money < 0 AND type != '代理充值'");
    $daySales = 0;
    while ($c = $DB->fetch($a)) {
        $daySales += $c['money'];
    }
    $statistics['lastSevenDaysSales'][] = -$daySales;
}


    // 总订单数
    $statistics['totalOrders'] = $DB->count("SELECT COUNT(*) FROM qingka_wangke_order");

    return $statistics;
}

// 获取统计数据
$statistics = getStatistics($DB, $jtdate, $ztdate, $dateArray);

if ($userrow['uid'] != 1) {
    exit("<script language='javascript'>window.location.href='login.php';</script>");
}
?>

<script type="text/javascript" src="assets/LightYear/js/Chart.js"></script>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", () => {
        const ctx = document.getElementById('salesStatics').getContext('2d');
        if (ctx) {
            // PHP 数据传递给 JavaScript 对象
            const data = {
                labels: <?php echo json_encode(array_reverse($dateArray)); ?>,
                orderData: [<?php echo $order7; ?>, <?php echo $order6; ?>, <?php echo $order5; ?>, <?php echo $order4; ?>, <?php echo $order3; ?>, <?php echo $order2; ?>, <?php echo $order1; ?>],
                addUserData: [<?php echo $adduser7; ?>, <?php echo $adduser6; ?>, <?php echo $adduser5; ?>, <?php echo $adduser4; ?>, <?php echo $adduser3; ?>, <?php echo $adduser2; ?>, <?php echo $adduser1; ?>],
                salesData: <?php echo json_encode($statistics['lastSevenDaysSales']); ?>
            };

            var charts = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: '订单数量',
                            tension: 0.4,
                            fill: true,
                            backgroundColor: 'rgba(30, 144, 255, 0.2)',
                            borderColor: 'rgb(65, 105, 255)',
                            data: data.orderData,
                        },
                        {
                            label: "新增用户",
                            tension: 0.4,
                            fill: true,
                            backgroundColor: 'rgba(0, 255, 0, 0.2)',
                            borderColor: 'rgb(152, 251, 152)',
                            data: data.addUserData,
                            type: "line",
                        },
                        {
                            label: '销售额',
                            tension: 0.4,
                            fill: true,
                            backgroundColor: 'rgba(255, 69, 0, 0.2)',
                            borderColor: 'rgb(255, 99, 71)',
                            data: data.salesData,
                        }
                    ]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }
    });
</script>

<div class="app-content-body">
    <div class="wrapper-md control" id="userindex">
        <!-- 原有统计数据展示 -->
        <div class="row">
            <div class="col-sm-6">
                                <div class="panel panel-default" style="box-shadow: 3px 3px 8px #d1d9e6, -3px -3px 8px #d1d9e6; border-radius: 7px;">
                    <div class="list-group no-radius alt">
                        <span class="list-group-item" href="">
                            <span class="badge bg-success">
                                <?php echo $statistics['totalUsers']; ?>
                            </span>
                            <i class="glyphicon glyphicon-yen"></i>
                            总用户
                        </span>
                    </div>

                    <div class="list-group no-radius alt">
                        <span class="list-group-item" href="">
                            <span class="badge bg-success">
                                <?php echo $statistics['todayNewUsers']; ?>
                            </span>
                            <i class="glyphicon glyphicon-yen"></i>
                            今日新增用户
                        </span>
                    </div>

                    <div class="list-group no-radius alt">
                        <span class="list-group-item" href="">
                            <span class="badge bg-primary">
                                <?php echo $statistics['todayOrders']; ?>
                            </span>
                            <i class="glyphicon glyphicon-ok"></i>
                            今日订单
                        </span>
                    </div>

                    <div class="list-group no-radius alt">
                        <span class="list-group-item" href="">
                            <span class="badge bg-success">&yen;
                                <?php echo $statistics['todaySales']; ?>
                            </span>
                            <i class="glyphicon glyphicon-yen"></i>
                            今日销售
                        </span>
                    </div>
                    
                    <div class="list-group no-radius alt">
                        <span class="list-group-item" href="">
                            <span class="badge bg-primary">
                                <?php echo $statistics['yesterdayOrders']; ?>
                            </span>
                            <i class="glyphicon glyphicon-ok"></i>
                            昨日订单
                        </span>
                    </div>
                
                    <div class="list-group no-radius alt">
                    <span class="list-group-item" href="">
                        <span class="badge bg-success">&yen;
                            <?php echo $statistics['yesterdaySales']; ?>
                        </span>
                        <i class="glyphicon glyphicon-yen"></i>
                        昨日销售
                    </span>
                </div>
                
                    <div class="list-group no-radius alt">
                        <span class="list-group-item" href="">
                            <span class="badge bg-info">
                                <?php echo $statistics['totalOrders']; ?>
                            </span>
                            <i class="glyphicon glyphicon-shopping-cart"></i>
                            总订单
                        </span>
                    </div>
                    
                    <div class="list-group no-radius alt"> 
                     <span class="list-group-item" href="">
                      <span class="badge bg-success">&yen;                      
                      <?php
                      $zcmoney = 0; // 初始化变量
                        $a=$DB->query("SELECT money FROM qingka_wangke_log WHERE type='上级充值' and addtime >'$jtdate'");                        
                          while($c=$DB->fetch($a)){
                       $zcmoney+=$c['money'];
                  }                        
                        echo $zcmoney;                                          
                       ?>                      
                      </span>
                      <i class="glyphicon glyphicon-yen"></i> 
                                                    今日上级充值
                    </span>  
                  </div>
                  
                    <div class="list-group no-radius alt"> 
                     <span class="list-group-item" href="">
                      <span class="badge bg-success">&yen;                      
                      <?php
                        
                        $zxzcmoney = 0; // 初始化变量
                        $a=$DB->query("SELECT money FROM qingka_wangke_log WHERE type='在线充值' and addtime >'$jtdate'");                        
                          while($c=$DB->fetch($a)){
                       $zxzcmoney+=$c['money'];
                  }                        
                        echo $zxzcmoney;                                          
                       ?>                      
                      </span>
                      <i class="glyphicon glyphicon-yen"></i> 
                                                    今日在线总充值
                    </span>  
                  </div>
                    
                    <div class="list-group no-radius alt"> 
                     <span class="list-group-item" href="">
                      <span class="badge bg-success">&yen;                      
                      <?php
    // 初始化总余额为0
    $zcmoney2 = 0;

    // 查询 uid 为1 的用户的余额
    $result = $DB->query("SELECT money FROM qingka_wangke_user WHERE uid = '1' ");

    // 检查查询是否成功
    if ($result) {
        // 获取 uid 为1 的用户的余额值
        $row = $DB->fetch($result);
        $userBalance = $row['money'];

        // 查询所有用户的余额
        $a = $DB->query("SELECT money FROM qingka_wangke_user");

        // 遍历所有用户的余额，累加总余额
        while ($c = $DB->fetch($a)) {
            $zcmoney2 += $c['money'];
        }
        
        // 输出总余额
        echo $zcmoney2-$userBalance;
    } else {
        // 查询失败时的处理
        echo "查询失败：" . $DB->error;
    }
?>
           
                      </span>
                      <i class="glyphicon glyphicon-yen"></i> 
                                                    总余额
                    </span>  
                  </div>

                    
                </div>

            </div>

            <!-- 新增图表展示 -->
            <div class="col-sm-6">
                <div class="layui-card" style="box-shadow: 3px 3px 8px #d1d9e6, -3px -3px 8px #d1d9e6; border-radius: 7px;">
                    <div>
                        <canvas id="salesStatics"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once("footer.php"); ?>
</html>
