<?php
$mod='blank';
$title='订单列表';
require_once('head.php');
// 假设从数据库或 session 中获取用户信息
$userrow = [
    'uid' => 1, // 示例用户，管理员 uid=1
    'username' => '管理员' // 示例用户名
];

// 假设从数据库中获取当前可领取的活动信息
$availableActivities = [
    [
        'name' => '促销活动1',
        'yaoqiu' => '购买满500元赠礼品',
        'type' => '促销',
        'num' => 100,
        'money' => 500,
        'addtime' => '2024-09-01 10:00:00',
        'endtime' => '2024-09-10 18:00:00',
    ],
    [
        'name' => '促销活动2',
        'yaoqiu' => '购买满300元送优惠券',
        'type' => '促销',
        'num' => 200,
        'money' => 300,
        'addtime' => '2024-09-05 08:00:00',
        'endtime' => '2024-09-15 20:00:00',
    ]
];
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>活动管理与领取</title>
    <style>
    /* 设置全局页面样式 */
    body {
        font-family: Arial, sans-serif; /* 使用 Arial 字体 */
        background-color: #f4f4f9; /* 设置背景颜色为浅灰色 */
        margin: 0; /* 移除页面的默认边距 */
        padding: 0; /* 移除页面的默认填充 */
        display: flex; /* 使用 flexbox 布局，方便垂直和水平对齐 */
        justify-content: center; /* 将内容水平居中 */
        align-items: flex-start; /* 将内容垂直顶对齐 */
        min-height: 100vh; /* 设置页面的最小高度为 100% 的视口高度 */
        position: relative; /* 允许内部使用绝对定位 */
    }

    /* 活动页面的主要容器样式 */
    .container {
        background-color: #fff; /* 设置背景为白色 */
        padding: 20px; /* 内边距为 20px */
        width: 100%; /* 宽度为 100% */
        max-width: 1200px; /* 最大宽度为 1200px */
    }

    /* 标题样式 */
    .title {
        text-align: center; /* 文字居中 */
        font-size: 28px; /* 设置字体大小 */
        color: #333; /* 深灰色字体 */
        margin-bottom: 20px; /* 标题与下方内容的间距 */
    }

    /* 活动卡片的网格布局容器 */
    .grid-container {
        display: flex; /* 使用 flexbox 布局 */
        flex-wrap: wrap; /* 如果一行放不下，允许换行 */
        gap: 20px; /* 卡片之间的间距为 20px */
    }

    /* 单个活动卡片的样式 */
    .activity-card {
        background-color: #fff; /* 白色背景 */
        border-radius: 8px; /* 圆角边框 */
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* 设置阴影，提供立体效果 */
        padding: 20px; /* 内边距为 20px */
        width: calc(33.33% - 20px); /* 宽度为三列布局，减去卡片之间的间距 */
        box-sizing: border-box; /* 包括内边距和边框在内的宽度计算 */
    }

    /* 活动卡片标题样式 */
    .activity-card h3 {
        font-size: 20px; /* 设置字体大小 */
        color: #007BFF; /* 蓝色标题 */
        margin-bottom: 10px; /* 标题与内容之间的间距 */
    }

    /* 活动卡片文字样式 */
    .activity-card p {
        font-size: 14px; /* 字体大小为 14px */
        color: #555; /* 设置文字为中灰色 */
        margin: 5px 0; /* 段落之间的间距 */
    }

    /* 领取奖励按钮的样式 */
    .btn-reward {
        background-color: #28a745; /* 按钮背景颜色为绿色 */
        color: white; /* 按钮文字为白色 */
        padding: 10px 15px; /* 设置内边距 */
        border: none; /* 去掉按钮的边框 */
        border-radius: 5px; /* 圆角按钮 */
        cursor: pointer; /* 鼠标悬停时显示为手形 */
        text-align: center; /* 文字居中 */
        width: 100%; /* 按钮宽度为 100% */
    }

    /* 鼠标悬停时改变按钮颜色 */
    .btn-reward:hover {
        background-color: #218838; /* 深绿色 */
    }

.floating-form {
    position: fixed;
    top: 50%;
    left: 50%;
    width: 400px; /* 减小宽度 */
    height: auto; /* 高度自适应 */
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    padding: 25px; /* 减小内边距 */
    z-index: 1000;
    display: none;
    transform: translate(-50%, -50%);
    overflow-y: auto;
}

    /* 表单激活时显示 */
    .floating-form.active {
        display: block; /* 显示表单 */
    }

    /* 表单内的输入框和标签的样式 */
    .floating-form .form-group {
        margin-bottom: 15px; /* 每个表单组的底部间距 */
    }
    
    /* 表单标签的样式 */
    .floating-form .form-group label {
        display: block; /* 标签为块级元素 */
        margin-bottom: 5px; /* 标签与输入框之间的间距 */
        font-weight: bold; /* 加粗字体 */
    }

    /* 表单输入框和文本区域的样式 */
    .floating-form .form-group input,
    .floating-form .form-group textarea {
        width: 100%; /* 宽度 100% */
        padding: 10px; /* 内边距 */
        border: 1px solid #ddd; /* 边框为浅灰色 */
        border-radius: 5px; /* 圆角边框 */
    }

    /* 文本区域高度调整 */
    .floating-form .form-group textarea {
        resize: none; /* 禁止用户调整文本框大小 */
        height: 50px; /* 设置默认高度为 80px */
    }

    /* 发布按钮的样式 */
    .floating-form .btn-submit {
        background-color: #007BFF; /* 按钮背景为蓝色 */
        color: white; /* 按钮文字为白色 */
        padding: 10px 15px ; /* 内边距 */
        border: none; /* 无边框 */
        border-radius: 5px; /* 圆角按钮 */
        cursor: pointer; /* 鼠标悬停时显示手形 */
        text-align: center; /* 文字居中 */
        width: 100%; /* 按钮宽度为 100% */
    }

    /* 鼠标悬停时改变提交按钮颜色 */
    .floating-form .btn-submit:hover {
        background-color: #0056b3; /* 深蓝色 */
    }

    /* 悬浮按钮样式 */
    .toggle-btn {
        position: fixed; /* 固定在页面 */
        bottom: 20px; /* 距离页面底部 20px */
        right: 20px; /* 距离页面右侧 20px */
        background-color: #007BFF; /* 按钮背景为蓝色 */
        color: white; /* 按钮文字为白色 */
        padding: 15px; /* 内边距 */
        border-radius: 50%; /* 圆形按钮 */
        cursor: pointer; /* 鼠标悬停时显示手形 */
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* 按钮的阴影效果 */
        font-size: 24px; /* 字体大小 */
        z-index: 1001; /* 高于其他元素 */
    }

    /* 媒体查询：针对屏幕宽度小于 768px 的设备调整布局 */
    @media screen and (max-width: 768px) {
        .activity-card {
            width: calc(50% - 20px); /* 在中小屏幕上卡片变为两列布局 */
        }
    }

    /* 媒体查询：针对屏幕宽度小于 480px 的设备调整布局 */
    @media screen and (max-width: 480px) {
        .activity-card {
            width: 100%; /* 在小屏幕上卡片变为单列布局 */
        }
    }
</style>
</head>
<body>
         <!-- 引入样式 -->
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
<!-- 引入组件库 -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<div class="container" id="available">
    <h1 class="title">当前可领取的活动</h1>

    <div class="grid-container">
        <!-- 显示多个活动卡片 -->
        <?php if (!empty($availableActivities)) { ?>
            <?php foreach ($availableActivities as $activity) { ?>
            
                <div v-for="res in row.data" class="activity-card">
                    <h3><?php echo $activity['name']; ?></h3>
                    <p><strong>要求:</strong> {{res.yaoqiu}}</p>
                    <p><strong>类型:</strong> <?php echo $activity['type']; ?></p>
                    <p><strong>参与人数:</strong> <?php echo $activity['num']; ?></p>
                    <p><strong>金额:</strong> <?php echo $activity['money']; ?> 元</p>
                    <p><strong>开始时间:</strong> <?php echo $activity['addtime']; ?></p>
                    <p><strong>结束时间:</strong> <?php echo $activity['endtime']; ?></p>
                    <button class="btn-reward" onclick="alert('成功领取【<?php echo $activity['name']; ?>】奖励！')">领取奖励</button>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>当前没有可领取的活动。</p>
        <?php } ?>
    </div>
</div>

<!-- 悬浮表单：仅管理员可见 -->
<?php if ($userrow['uid'] == 1) { ?>
    <div class="toggle-btn" onclick="toggleForm()">+</div>

    <div class="floating-form" id="adminForm">
        <h2>发布新活动</h2>
        <form action="create_activity.php" method="POST">
            <div class="form-group">
                <label for="name">活动名称</label>
                <input type="text" id="name" name="name" placeholder="请输入活动名称" required>
            </div>
            <div class="form-group">
                <label for="yaoqiu">活动要求</label>
                <textarea id="yaoqiu" name="yaoqiu" placeholder="请输入活动要求" required></textarea>
            </div>
            <div class="form-group">
                <label for="type">活动类型</label>
                <input type="text" id="type" name="type" placeholder="请输入活动类型" required>
            </div>
            <div class="form-group">
                <label for="num">参与人数/要求</label>
                <input type="number" id="num" name="num" placeholder="请输入参与人数或要求" required>
            </div>
            <div class="form-group">
                <label for="money">活动金额</label>
                <input type="number" id="money" name="money" placeholder="请输入活动金额" required>
            </div>
            <div class="form-group">
                <label for="addtime">活动开始时间</label>
                <input type="datetime-local" id="addtime" name="addtime" required>
            </div>
            <div class="form-group">
                <label for="endtime">活动结束时间</label>
                <input type="datetime-local" id="endtime" name="endtime" required>
            </div>
            <button type="submit" class="btn-submit">发布活动</button>
        </form>
    </div>
<?php } ?>

<script>
    function toggleForm() {
        var form = document.getElementById("adminForm");
        if (form.classList.contains("active")) {
            form.classList.remove("active");
        } else {
            form.classList.add("active");
        }
    }
</script>
<script src="assets/js/vue.min.js"></script>
<script src="assets/js/vue-resource.min.js"></script>
<script src="assets/js/axios.min.js"></script>


<script>
vm=new Vue({
	el:"#available",
	data:{
		  row:null,
		  cx:{
		  	name:'',
		  	yaoqiu:'',
		  	type:'',
		  	num:'',
		  	money:'',
		  	addtime:'',
		  	endtime:'',
		  }
	},
	methods:{
		get: function() {
    var load = layer.load(2);
    var self = this; // 保存当前上下文的this
    this.$http.post("/apisub.php?act=hdhq", { emulateJSON: true })
        .then(function(data) {
            layer.close(load);
            if (data.data.code == 1) {
                self.row = data.body;
            } else {
                layer.msg(data.data.msg, { icon: 2 });
            }
        })
        .catch(function(error) {
            layer.close(load);
            layer.msg('请求失败，请稍后再试', { icon: 2 });
        });
},
	},
	mounted(){
		this.get();
	}
});
</script>


</body>
</html>
