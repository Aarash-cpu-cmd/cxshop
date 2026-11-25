<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>商品热销榜</title>
    <link rel="stylesheet" type="text/css" href="layui/css/layui.css">
    <?php require_once('head.php'); // 将head.php包含到页面的头部 ?>
</head>
<body>
    <div class="layui-container">
        <div class="layui-row">
            <div class="layui-col-md-offset-1 layui-col-md-10">
                <div class="panel panel-default" style="box-shadow: 3px 3px 8px #d1d9e6; border-radius: 7px;">
                    <div class="panel-body" style="box-shadow: 3px 3px 8px #d1d9e6; border-radius: 7px;">
                        <div class="panel-heading font-bold layui-bg-white">热销榜(最近一周)</div>
                        <table class="layui-table">
                            <thead>
                                <tr>
                                    <th>畅销排名</th>
                                    <th>商品</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // 查询数据库获取数据
                                $query = "SELECT ptname, COUNT(*) AS order_count 
                                          FROM qingka_wangke_order 
                                          WHERE addtime >= DATE_SUB(NOW(), INTERVAL 1 WEEK)
                                          GROUP BY ptname 
                                          ORDER BY order_count DESC";
                                $result = $DB->query($query);
                                $rank = 1;
                                while ($row = $result->fetch_assoc()) {
                                    $ptname = $row['ptname'];
                                    echo "<tr><td>$rank</td><td>$ptname</td></tr>";
                                    $rank++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="layui/layui.js"></script>
</body>
</html>
