<?php
include('head.php');
?>
<div class="lyear-layout-web">
  <div class="lyear-layout-container">
    <!--左侧导航-->
    <aside class="lyear-layout-sidebar">
      
      <!-- logo -->
      <div id="logo" class="sidebar-header">
        <a href="index"><img src="<?=$conf['logo']?>" title="FreeDom" alt="FreeDom" /></a>
      </div>
      <div class="lyear-layout-sidebar-scroll"> 
        
        <nav class="sidebar-main">
          <ul class="nav nav-drawer">
            <li class="nav-item active"> <a class="multitabs" href="home"><i class="mdi mdi-home"></i> <span>我的首页</span></a> </li>
            <?php if($userrow['uid']==1){ ?>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-palette"></i> <span>控制中心</span></a>
              <ul class="nav nav-subnav">
                <li class="nav-item nav-item-has-subnav"><a href="javascript:void(0)"><span>系统设置</span></a>
                    <ul class="nav nav-subnav">
                        <li> <a class="multitabs" href="webset">系统配置</a> </li>
                        <li> <a class="multitabs" href="gonggao">公告配置</a> </li>
                        <li> <a class="multitabs" href="data">数据统计</a> </li>
                        <li> <a class="multitabs" href="dengji">等级设置</a> </li>
                        <li> <a class="multitabs" href="mijia">密价设置</a> </li>
                        <li> <a class="multitabs" href="guanx">管理卡密</a> </li>
                        <li> <a class="multitabs" href="paylist">支付列表</a> </li>
                </ul>
                </li>
                <li class="nav-item nav-item-has-subnav"><a href="javascript:void(0)"><span>商品配置</span></a>
                    <ul class="nav nav-subnav">
                        <li> <a class="multitabs" href="huoyuan">接口配置</a></li>
                        <li> <a class="multitabs" href="newclass">货源添加</a></li>
                        <li> <a class="multitabs" href="class">网课设置</a></li>
                        <li> <a class="multitabs" href="fenlei">分类添加</a> </li>
                </ul>
                </li>
                <!--<li> <a class="multitabs" href="webset">系统配置</a> </li>
                <li> <a class="multitabs" href="huoyuan">接口配置</a> </li>
                <li> <a class="multitabs" href="newclass">货源添加</a> </li>
                <li> <a class="multitabs" href="class">网课设置</a> </li>
                <li> <a class="multitabs" href="fenlei">分类添加</a> </li>
                <li> <a class="multitabs" href="data">数据统计</a> </li>
                <li> <a class="multitabs" href="dengji">等级设置</a> </li>
                <li> <a class="multitabs" href="paylist">支付列表</a> </li>
                <li> <a class="multitabs" href="mijia">密价设置</a> </li>
                <li> <a class="multitabs" href="guanx">管理卡密</a> </li>-->
              </ul>
            </li>
            <?php } ?>  
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-run-fast"></i> <span>快捷交单</span></a>
              <ul class="nav nav-subnav">
                  <li> <a class="multitabs" href="add2pl">新版交单</a> </li>
                  <li> <a class="multitabs" href="linlang">琳琅交单</a> </li>
                  <li> <a class="multitabs" href="addsj">手机交单</a> </li>
                  <li> <a class="multitabs" href="adds">无查项目</a> </li>
                  <li> <a class="multitabs" href="cs">订单汇总</a> </li>
                  <li> <a class="multitabs" href="sale">热门排行</a> </li>
                  <li> <a class="multitabs" href="cxky">查询可用</a> </li>
              </ul>
            </li>
            <?php if ($conf['flkg']=="1"&&$conf['fllx']=="0") {?>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-plus"></i> <span>经典交单</span></a>
              <ul class="nav nav-subnav">
                  <?php 
                  $a=$DB->query("select * from qingka_wangke_fenlei where status=1  order by sort asc");
                  while($rs=$DB->fetch($a)){
                  ?>
                                    <li> <a class="multitabs" href="addfl?id=<?=$rs['id']?>">
                                            <?=$rs['name']?>
                                        </a></li>
                                    <?php } ?>
                 <li> <a class="multitabs" href="add2">所有项目</a></li>
              </ul>
            </li>
            <?php }else if ($conf['flkg']=="1"&&$conf['fllx']=="2022") {?>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-plus"></i> <span>常用项目</span></a>
              <ul class="nav nav-subnav">
                  <?php 
                  $a=$DB->query("select * from qingka_wangke_fenlei where status=1 order by sort asc");
                  while($rs=$DB->fetch($a)){
                  ?>
                                    <li class="nav-item nav-item-has-subnav"><a href="javascript:void(0)"><span><?=$rs['name']?></span></a>
                                    <ul class="nav nav-subnav">
                                        <li> <a class="multitabs" href="addfl?id=<?=$rs['id']?>">提交</a></li>
                                        <li> <a class="multitabs" href="csfl?id=<?=$rs['id']?>">订单</a></li>
                                    </ul>
                                    </li>
                                    <?php } ?>
              </ul>
            </li>
            <?php }  if ($conf['flkg']=="1"&&$conf['fllx']=="2022") {?>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-plus-box-outline"></i> <span>冷门项目</span></a>
              <ul class="nav nav-subnav">
                  <?php 
                  $a=$DB->query("select * from qingka_wangke_fenlei where status=2 order by sort asc");
                  while($rs=$DB->fetch($a)){
                  ?>
                                    <li class="nav-item nav-item-has-subnav"><a href="javascript:void(0)"><span><?=$rs['name']?></span></a>
                                    <ul class="nav nav-subnav">
                                        <li> <a class="multitabs" href="addfl?id=<?=$rs['id']?>">提交</a></li>
                                        <li> <a class="multitabs" href="csfl?id=<?=$rs['id']?>">订单</a></li>
                                    </ul>
                                    </li>
                                    <?php } ?>
              </ul>
            </li>
            
            <li class="nav-item"><a class="multitabs" href="cs"><i class="mdi mdi-library-books"></i> <span>订单汇总</span></a></li>
            <?php }?>
             <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-file-document-box"></i> <span>用户中心</span></a>
              <ul class="nav nav-subnav">
                <li class="nav-item"><a class="multitabs" href="pay"><span>在线充值</span></a> </li>
                <?php if($userrow['uuid']==1) {?>
                <?php }?>
                <li> <a class="multitabs" href="userinfo">个人中心</a> </li>
                <li> <a class="multitabs" href="userlist">代理管理</a> </li>
                <li> <a class="multitabs" href="dock">对接文档</a> </li>
                <li class="nav-item"><a class="multitabs" href="gongdan"><!--<i class="mdi mdi-wrench">--></i><span>提交工单</span></a></li>
                <!--<li> <a class="multitabs" href="charge">我的上级</a> </li>-->
                <li> <a class="multitabs" href="log">操作日志</a> </li>
              </ul>
            </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
          <p class="copyright">Copyright &copy; 2024. <a target="_blank" href="/"><?=$conf['sitename']?></a> All rights reserved. | <a  href="mzsm" class="layui-link"style="color: black!important;">免责声明</a></p>
        </div>
      </div>
      
    </aside>
    <!--End 左侧导航-->
    
    <!--头部信息-->
    <header class="lyear-layout-header" style="height:60px;" >
      
      <nav class="navbar navbar-default">
        <div class="topbar">
          
          <div class="topbar-left">
    <div class="lyear-aside-toggler">
        <span class="lyear-toggler-bar"></span>
        <span class="lyear-toggler-bar"></span>
        <span class="lyear-toggler-bar"></span>
    </div>
    <!-- 刷新图标 -->
    <a href="javascript:void(0);" onclick="refreshContent();" title="刷新当前页面">
        <i class="mdi mdi-refresh" style="font-size: 20px; margin-left: 15px; color: black;"></i>
    </a>
</div>

          
          <ul class="topbar-right">
            <li class="dropdown dropdown-profile">
              <a href="javascript:void(0)" data-toggle="dropdown">
                <img class="img-avatar img-avatar-48 m-r-10" style="border-radius: 8px;width:30px;height:30px;border:0px;" src="favicon.ico" alt="<?=$userrow['name'];?>" />
                <span><?=$userrow['name'];?> <span class="caret"></span></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-right">
                  <li> <a class="multitabs" id="sjqy"><i class="mdi mdi-lock-outline"></i>上级迁移</a> </li>
                <li> <a class="multitabs" data-url="passwd" href="javascript:void(0)"><i class="mdi mdi-lock-outline"></i> 修改密码</a> </li>
                <!--<li> <a href="javascript:void(0)"><i class="mdi mdi-delete"></i> 清空缓存</a></li>     login.php  退出登录路径   -->
                <li class="divider"></li>
                <li> <a href="../apisub.php?act=logout"><i class="mdi mdi-logout-variant"></i> 退出登录</a> </li>
              </ul>
            </li>
          </ul>
          
        </div>
      </nav>
      
    </header>
    <!--End 头部信息-->
    
    <!--页面主要内容-->
    <main class="lyear-layout-content">
      
      <div id="iframe-content"></div>
      
    </main>
    <!--End 页面主要内容-->
  </div>
</div>
<script>
    function refreshContent() {
        var $iframe = $('#iframe-content iframe');  // 获取页面中的iframe
        if ($iframe.length > 0) {
            // 如果内容在iframe中，重新加载iframe的src
            $iframe.each(function() {
                var src = $(this).attr('src');
                $(this).attr('src', src);
            });
        } else {
            // 如果内容不在iframe中，通过ajax请求重新加载内容
            $.ajax({
                url: window.location.href,  // 使用当前页面的URL重新加载内容
                dataType: "html",
                success: function (callback) {
                    $('body').html(callback);  // 替换页面主体内容
                },
                error: function (callback) {
                    alert('页面加载失败，请稍后重试！');
                }
            });
        }
    }
</script>
<script type="text/javascript" src="assets/LightYear/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/bootstrap-multitabs/multitabs.js"></script>
<script type="text/javascript" src="assets/LightYear/js/index.min.js"></script>
<script type="text/javascript" src="layer/3.1.1/layer.js"></script>
<script src="assets/js/sy.js"></script>
<script src="assets/vue.min.js"></script>
<!--水印开关，其他页面可以复制过去-->
<?php if ($conf['sykg']==1) {?>
<script type="text/javascript">
watermark('<?=$conf['sitename'];?>','昵称 : <?=$userrow['name'];?>','UID:<?=$userrow['uid'];?>');
</script>
<?}?>
       <script type="text/javascript">
        $('#sjqy').click(function() {
            layer.prompt({title: '请输入要转移的上级UID', formType: 3}, function(uid, index){
			  layer.close(index);
	           layer.prompt({title: '请输入要转移的上级邀请码', formType: 3}, function(yqm, index){
			  layer.close(index);
	           var load=layer.load(2);
	           $.ajax({
	               type: "POST",
	               url: "../apisub.php?act=sjqy",
	               data: {"uid":uid,"yqm":yqm},
	               dataType: 'json',
	               success: function(data) {
	                   layer.close(load);
	                   if (data.code == 1) {
	                       layer.msg(data.msg,{icon:1});
	                   } else {
	                       layer.msg(data.msg,{icon:2});
	                   }
	               }
	           });           
		  });           
		  });		
    });
       </script>

</body>
</html>