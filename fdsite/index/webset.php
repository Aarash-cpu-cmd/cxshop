<?php
$title='系统设置';
require_once('head.php');
if($userrow['uid']!=1){
	alert("你来错地方了","index.php");
}
?>
     <div class="app-content-body ">
        <div class="wrapper-md control" id="add">
	       <div class="panel panel-default" style="box-shadow: 3px 3px 8px #d1d9e6, -3px -3px 8px #d1d9e6;border-radius: 6px;">
				<div class="panel-body">
					<form class="form-horizontal devform" id="form-web">
					    <div  class="card">
          <ul class="nav nav-tabs" role="tablist">
            <li class="active">
              <a data-toggle="tab" href="#wzpz">网站配置</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#ggpz">公告配置</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#dlpz">代理配置</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#zfpz">支付配置</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#jhdl">聚合登录</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#flpz">分类配置</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#ckpz">查课配置</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#yjpz">邮件配置</a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade active in" id="wzpz">
		            <div class="form-group">
							<label class="col-sm-2 control-label">站点名字</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="sitename" value="<?=$conf['sitename']?>" placeholder="请输入站点名字" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">SEO关键词</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="keywords" value="<?=$conf['keywords']?>" placeholder="请输入站点名字" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">SEO介绍</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="description" value="<?=$conf['description']?>" placeholder="请输入站点名字" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">LOGO地址</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="logo" value="<?=$conf['logo']?>" placeholder="请输入logo地址" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">前页自动跳转</label>
								<div class="col-sm-9">
									<select name="tzkg" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">	
	                            	   <option value="1" <?php if($conf['tzkg']==1){ echo 'selected';}?>>1_已开启</option>
	                            	   <option value="0" <?php if($conf['tzkg']==0){ echo 'selected';}?>>0_未开启</option>
	                                </select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">自动跳转延时</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="tztime" value="<?=$conf['tztime']?>" placeholder="请输入数字(单位：毫秒)" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">是否开启水印</label>
								<div class="col-sm-9">
									<select name="sykg" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">	
	                            	   <option value="1" <?php if($conf['sykg']==1){ echo 'selected';}?>>1_允许</option>   
	                            	   <option value="0" <?php if($conf['sykg']==0){ echo 'selected';}?>>0_拒绝</option>
	                                </select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">是否闭站</label>
								<div class="col-sm-9">
									<select name="bz" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">	
	                            	   <option value="1" <?php if($conf['bz']==1){ echo 'selected';}?>>1_已经闭站</option>
	                            	   <option value="0" <?php if($conf['bz']==0){ echo 'selected';}?>>0_没有闭站</option>
	                                </select>
							</div>
						</div>
            </div>
            <div class="tab-pane fade" id="ggpz">
						<div class="form-group">
							<label class="col-sm-2 control-label">首页公告</label>
								<div class="col-sm-9">
									<textarea type="text" name="notice" class="layui-textarea"  rows="5"><?=$conf['notice']?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">弹窗公告</label>
								<div class="col-sm-9">
									<textarea type="text" name="tcgonggao" class="layui-textarea"  rows="5"><?=$conf['tcgonggao']?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">前页公告</label>
								<div class="col-sm-9">
									<textarea type="text" name="qygg" class="layui-textarea"  rows="5"><?=$conf['qygg']?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">闭站公告</label>
								<div class="col-sm-9">
									<textarea type="text" name="bzgg" class="layui-textarea"  rows="5"><?=$conf['bzgg']?></textarea>
							</div>
						</div>
            </div>
            <div class="tab-pane fade" id="dlpz">
                <div class="form-group">
							<label class="col-sm-2 control-label">是否开启上级迁移功能</label>
								<div class="col-sm-9">
									<select name="sjqykg" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">	
	                            	   <option value="1" <?php if($conf['sjqykg']==1){ echo 'selected';}?>>1_开启</option>   
	                            	   <option value="0" <?php if($conf['sjqykg']==0){ echo 'selected';}?>>0_关闭</option>
	                                </select>
							</div>
						</div>
                <div class="form-group">
							<label class="col-sm-2 control-label">是否允许平开</label>
								<div class="col-sm-9">
									<select name="dl_pkkg" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">	
	                            	   <option value="3" <?php if($conf['dl_pkkg']==3){ echo 'selected';}?>>3_都需持有双倍余额</option>
	                            	   <option value="2" <?php if($conf['dl_pkkg']==2){ echo 'selected';}?>>2_顶级需持有双倍余额</option>
	                            	   <option value="1" <?php if($conf['dl_pkkg']==1){ echo 'selected';}?>>1_除顶级外允许</option>
	                            	   <option value="0" <?php if($conf['dl_pkkg']==0){ echo 'selected';}?>>0_允许</option>
	                                </select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">顶级费率（需要和等级配置内相等）</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="djfl" value="<?=$conf['djfl']?>" placeholder="仅用于控制平开" required>
							</div>
						</div>
                <div class="form-group">
							<label class="col-sm-2 control-label">是否允许邀请码注册</label>
								<div class="col-sm-9">
									<select name="user_yqzc" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">
	                            	    <option value="1" <?php if($conf['user_yqzc']==1){ echo 'selected';}?>>1_允许</option> 
	                            	    <option value="0" <?php if($conf['user_yqzc']==0){ echo 'selected';}?>>0_拒绝</option>
	                                </select>
							   </div>
						</div>	

						<div class="form-group">
							<label class="col-sm-2 control-label">是否允许后台开户</label>
								<div class="col-sm-9">
									<select name="user_htkh" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">	
	                            	   <option value="1" <?php if($conf['user_htkh']==1){ echo 'selected';}?>>1_允许</option>   
	                            	   <option value="0" <?php if($conf['user_htkh']==0){ echo 'selected';}?>>0_拒绝</option>
	                                </select>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">代理开通价格</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="user_ktmoney" value="<?=$conf['user_ktmoney']?>" placeholder="" required>
							</div>
						</div>
            </div>
            <div class="tab-pane fade" id="zfpz">
                <div class="form-group">
							<label class="col-sm-2 control-label">最低充值</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="zdpay" value="<?=$conf['zdpay']?>" placeholder="请输入你的商户KEY" required>
							</div>
						</div>
					    <div class="form-group">
							<label class="col-sm-2 control-label">是否开启QQ支付</label>
								<div class="col-sm-9">
									<select name="is_qqpay" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">
	                            	    <option value="1" <?php if($conf['is_qqpay']==1){ echo 'selected';}?>>1_开启</option> 
	                            	    <option value="0" <?php if($conf['is_qqpay']==0){ echo 'selected';}?>>0_关闭</option>
	                                </select>
							   </div>
						</div>	
						<div class="form-group">
							<label class="col-sm-2 control-label">是否开启微信支付</label>
								<div class="col-sm-9">
									<select name="is_wxpay" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">
	                            	    <option value="1" <?php if($conf['is_wxpay']==1){ echo 'selected';}?>>1_开启</option> 
	                            	    <option value="0" <?php if($conf['is_wxpay']==0){ echo 'selected';}?>>0_关闭</option>
	                                </select>
							   </div>
						</div>	
						<div class="form-group">
							<label class="col-sm-2 control-label">是否开启支付宝支付</label>
								<div class="col-sm-9">
									<select name="is_alipay" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">
	                            	    <option value="1" <?php if($conf['is_alipay']==1){ echo 'selected';}?>>1_开启</option> 
	                            	    <option value="0" <?php if($conf['is_alipay']==0){ echo 'selected';}?>>0_关闭</option>
	                                </select>
							   </div>
						</div>	
						<div class="form-group">
							<label class="col-sm-2 control-label">易支付API</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="epay_api" value="<?=$conf['epay_api']?>" placeholder="格式：http://www.baidu.com/" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">商户ID</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="epay_pid" value="<?=$conf['epay_pid']?>" placeholder="请输入你的商户ID" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">商户KEY</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="epay_key" value="<?=$conf['epay_key']?>" placeholder="请输入你的商户KEY" required>
							</div>
						</div>
            </div>
            <div class="tab-pane fade" id="jhdl">
                <div class="form-group">
							<label class="col-sm-2 control-label">彩虹api聚合登录</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="login_apiurl" value="<?=$conf['login_apiurl']?>" placeholder="请输入聚合登录地址" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">应用ID</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="login_appid" value="<?=$conf['login_appid']?>" placeholder="请输入聚合登录应用ID" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">应用key</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="login_appkey" value="<?=$conf['login_appkey']?>" placeholder="请输入聚合登录应用key" required>
							</div>
						</div>
            </div>
            <div class="tab-pane fade" id="flpz">
                <div class="form-group">
							<label class="col-sm-2 control-label">分类开关</label>
								<div class="col-sm-9">
									<select name="flkg" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">
	                            	    <option value="1" <?php if($conf['flkg']==1){ echo 'selected';}?>>开启</option> 
	                            	    <option value="0" <?php if($conf['flkg']==0){ echo 'selected';}?>>关闭</option>
	                                </select>
							   </div>
						</div>	
						<div class="form-group">
							<label class="col-sm-2 control-label">分类类型</label>
								<div class="col-sm-9">
									<select name="fllx" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">
									    <option value="2022" <?php if($conf['fllx']==0){ echo 'selected';}?>>暗网分类</option>
									    <option value="0" <?php if($conf['fllx']==0){ echo 'selected';}?>>侧边栏分类</option>
	                            	    <option value="1" <?php if($conf['fllx']==1){ echo 'selected';}?>>下单页面选择框分类</option>
	                            	    <option value="2" <?php if($conf['fllx']==2){ echo 'selected';}?>>下单页面单选框分类</option>
	                            	                 	                            	
	                                </select>
							   </div>
						</div>	
            </div>
            <div class="tab-pane fade" id="ckpz">
                        <div class="form-group">
							<label class="col-sm-2 control-label">是否开启API调用功能</label>
								<div class="col-sm-9">
									<select name="settings" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">
	                            	    <option value="1" <?php if($conf['settings']==1){ echo 'selected';}?>>开启API调用</option> 
	                            	    <option value="0" <?php if($conf['settings']==0){ echo 'selected';}?>>关闭API调用</option>
	                                </select>
							   </div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">API调用扣费限制(单位:%)</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="api_proportion" value="<?=$conf['api_proportion']?>" placeholder="请输入API调用比例扣费限制比例（单位：%）" required>
							</div>
						</div>
                        <div class="form-group">
							<label class="col-sm-2 control-label">API查课余额限制(单位:元)</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="api_ck" value="<?=$conf['api_ck']?>" placeholder="请输入API查课余额限制金额" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">API下单余额限制(单位:元)</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="api_xd" value="<?=$conf['api_xd']?>" placeholder="请输入API下单余额限制金额" required>
							</div>
						</div>
            </div>
            <div class="tab-pane fade" id="yjpz">
                    <div class="form-group">
							<label class="col-sm-2 control-label">是否开启邮件功能</label>
								<div class="col-sm-9">
									<select name="mailstatus" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">
	                            	    <option value="1" <?php if($conf['mailstatus']==1){ echo 'selected';}?>>1_开启</option> 
	                            	    <option value="0" <?php if($conf['mailstatus']==0){ echo 'selected';}?>>0_关闭</option>
	                                </select>
							   </div>
						</div>
		            <div class="form-group">
							<label class="col-sm-2 control-label">SMTP服务器地址</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="smtp_server" value="<?=$conf['smtp_server']?>" placeholder="请输入SMTP服务器地址" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">SMTP 端口</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="smtp_port" value="<?=$conf['smtp_port']?>" placeholder="请输入SMTP 端口" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">SMTP 用户名</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="mailusername" value="<?=$conf['mailusername']?>" placeholder="请输入SMTP 用户名" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">SMTP 密码</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="mailpassword" value="<?=$conf['mailpassword']?>" placeholder="请输入SMTP 密码" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">SMTP 验证方式</label>
								<div class="col-sm-9">
									<select name="encryption" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">	
									    <option value="0" <?php if($conf['encryption']==''){ echo 'selected';}?>>无</option>
	                            	   <option value="SSL" <?php if($conf['encryption']=='SSL'){ echo 'selected';}?>>SSL</option>
	                            	   <option value="TLS" <?php if($conf['encryption']=='TLS'){ echo 'selected';}?>>TLS</option>
	                                </select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">SMTP 发件人邮箱</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="from_email" value="<?=$conf['from_email']?>" placeholder="请输入SMTP 发件人邮箱" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">SMTP 发件人名称</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="from_name" value="<?=$conf['from_name']?>" placeholder="请输入SMTP 发件人名称" required>
							</div>
						</div>
            </div>
          </div>
        </div>
																									
				  	    <div class="col-sm-offset-2 col-sm-4">
				  	    	<input type="button" @click="add" value="立即修改" class="layui-btn"/>
				  	    </div>

			        </form>
			      

		        </div>
	     </div>
      </div>
    </div>


<?php require_once("footer.php");?>

<script>
new Vue({
	el:"#add",
	data:{

	},
	methods:{
	    add:function(){
	        var loading=layer.load(2);
	    	this.$http.post("/apisub.php?act=webset",{data:$("#form-web").serialize()},{emulateJSON:true}).then(function(data){
	    		layer.close(loading);
	    		if(data.data.code==1){
	    			layer.alert(data.data.msg,{icon:1,title:"温馨提示"},function(){setTimeout(function(){window.location.href=""});});
	    		}else{
	    			layer.alert(data.data.msg,{icon:2,title:"温馨提示"});
	    		}
	    	});
	    }   
	},
	mounted(){
		//this.getclass();		
	}
	
	
});
</script>