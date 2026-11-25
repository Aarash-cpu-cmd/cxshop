<?php
$id =$_GET['id'];
$mod='blank';
$title='订单列表';
require_once('head.php');
// 在页面渲染时将用户信息传递给JavaScript
echo '<script>';
echo 'var userrow = { uid: ' . $userrow['uid'] . ' };'; // 将用户的uid传递给JavaScript
// 可以将其他用户信息也传递给JavaScript，例如：用户名、角色等
echo '</script>';
?>
<html lang="zh">
<head>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/apps.css" type="text/css" />
    <link rel="stylesheet" href="assets/css/app.css" type="text/css" />
    <link rel="stylesheet" href="assets/layui/css/layui.css" type="text/css" />
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/LightYear/js/bootstrap-multitabs/multitabs.min.css">
    <link rel="stylesheet" href="assets/LightYear/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/LightYear/css/style.min.css">
    <link rel="stylesheet" href="assets/LightYear/css/materialdesignicons.min.css">

    <!-- Scripts -->
    <script src="assets/css/vue@2.6.14"></script>
    <script src="assets/css/index.js"></script>
    <script src="../Composer/xlsx.full.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="layer/3.1.1/layer.js"></script>
</head>
</html>
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
<div class="app-content-body ">
         
        <div class="wrapper-md control">
        	
	    <div class="panel panel-default" id="orderlist">
		    <div class="panel-heading font-bold bg-white">任务列表</div>
				 <div class="panel-body">
				 		   <div class="form-horizontal devform">	
				 		   		<div class="form-group">				 		   			
				 		   				<div class="col-sm-1 col-xs-4" style="width: 150px;">		          
					              <select class="layui-select" v-model="cx.cid" style="    background: url('../index/arrow.png') no-repeat scroll 99%;width: 125px;">
					                  
					              	<option value="">平台</option>
					                <?php
					                $a=$DB->query("select * from qingka_wangke_class where status=1 and fenlei={$id} ");
					                while($row=$DB->fetch($a)){
					                    echo '<option value="'.$row['cid'].'">'.$row['name'].'</option>';
					                }
					                ?>   
					              </select>  
				              </div>  	
				 		   			 <div class="col-sm-1 col-xs-4" style="width: 150px;">		          
					              <select class="layui-select"  v-model="cx.status_text" style="    background: url('../index/arrow.png') no-repeat scroll 99%;width: 125px;">
					              	<option value="">任务状态</option>
					               <option value="待处理">待处理</option>
					                <option value="进行中">进行中</option>
					                <option value="失败">失败</option>
					               <option value="撰写中">撰写中</option>
					                <option value="待考试">待考试</option>
					                 <option value="上号中">上号中</option>
					                <option value="已完成">已完成</option>
					                <option value="补刷中">补刷中</option>
					                <option value="重刷中">重刷中</option>
					                <option value="待重刷">待重刷</option>
					                <option value="待更新">待更新</option>
					                <option value="异常">异常中</option>
					                <option value="已取消">已取消</option>
					                <option value="已退款">已退款</option>
					              </select>  
				              </div>  
				              <div class="col-sm-1 col-xs-4" style="width: 150px;" v-if="row.uid==1"> 		          
					              
					       <select class="layui-select"  v-model="cx.dock" style="    background: url('../index/arrow.png') no-repeat scroll 99%;width: 125px;">
					              	<option value="">处理状态</option>
					                <option value="0">待处理</option>
					                <option value="1">处理成功</option>
					                <option value="2">处理失败</option>
					                <option value="3">重复下单</option>
					                <option value="4">已取消</option>
					                <option value="99">我的</option>
					              </select>  
				              </div>     
				               <div class="col-sm-1 col-xs-4" style="width: 150px;"> 		          
					             <select class="layui-select"  v-model="dc2.gs" style="    background: url('../index/arrow.png') no-repeat scroll 99%;width: 200px;">
					              	<option value="">选择导出格式</option>
					                <option value="1">学校+账号+密码+课程名字</option>
					                <option value="2">账号+密码+课程名字</option>
					                <option value="3">学校+账号+密码</option>
					                <option value="4">账号+密码</option>
					              </select>  
				              </div>
			            </div>

				 	      <div class="form-group">
				 	          <div class="layui-row layui-col-space10">	
				 	      	<div class="col-sm-2 col-xs-6" v-if="row.uid==1">
		                 <input type="text"  v-model="cx.oid" value="" class="layui-input"  placeholder="请输入订单ID" required/>			              
			              </div>
			              <div class="col-sm-2 col-xs-6" v-if="row.uid==1">
		                 <input type="text"  v-model="cx.uid" value="" class="layui-input"  placeholder="请输入用户UID" required/>			              
			              </div>
			              <div class="col-sm-2 col-xs-6">
		                 <input type="text"  v-model="cx.school" value="" class="layui-input"  placeholder="请输入学校" required/>
		                  </div>
			              <div class="col-sm-2 col-xs-6">
		                 <input type="text"  v-model="cx.kcname" value="" class="layui-input"  placeholder="请输入课程名" required/>
			              </div>
			              <div class="col-sm-3 col-xs-6">
			              <input type="text"  v-model="cx.qq" value="" class="layui-input"  placeholder="请输入学号或者手机号" required/>			              
			              </div>
			              <div class="col-sm-1 col-xs-4" style="width: 150px;">
			                  <select class="layui-select"  v-model="cx.limit" style="    background: url('../index/arrow.png') no-repeat scroll 99%;width: 125px;">
			                      <option value="">25</option>
			                      <option value="50">50</option>
			                      <option value="100">100</option>
			                      <option value="200">200</option>
			                      <option value="500">500</option>
			                      <option value="1000">1000</option>
			                  </select>
				         </div>
			             <div class="col-sm-8 col-xs-6">
			              <input type="submit" value="查询" @click="get(1)" class="layui-btn layui-btn-primary layui-border-blue layui-btn-radius"/><input type="submit" value="导出" @click="daochu()" class="layui-btn layui-btn-primary layui-btn-radius"/>
			             </div>
			             </div>
			             </div>	
			          </div>
			           <div class="bg-gradient-tron" v-if="row.uid==1">
                            <!--<a class="btn btn-xs btn btn-primary purple" id="checkboxAll" @click="selectAll()">全选</a>
                            任务状态:<br/>
                            <span class="badge badge-dot-lg badge-dot bg-yellow"></span><a @click="status_text('待处理')"><font color="#fcc525">&nbsp;待处理</font></a>&nbsp;&nbsp;
                            <span class="badge badge-dot-lg badge-dot bg-success"></span><a @click="status_text('已完成')"><font color="#15c377">&nbsp;已完成</font></a>&nbsp;&nbsp;
                            <span class="badge badge-dot-lg badge-dot bg-purple"></span><a @click="status_text('进行中')"><font color="#926dde">&nbsp;进行中</font></a>&nbsp;&nbsp;
                            <span class="badge badge-dot-lg badge-dot bg-pink"></span><a @click="status_text('异常')"><font color="#f96197">&nbsp;异常</font></a>&nbsp;&nbsp;
                            <span class="badge badge-dot-lg badge-dot bg-primary"></span><a @click="status_text('已取消')"><font color="#33cabb">&nbsp;已取消</font></a>&nbsp;&nbsp;
                             <span class="badge badge-dot-lg badge-dot bg-primary"></span><a @click="status_text('待更新')"><font color="#33cabb">&nbsp;待更新</font></a>&nbsp;&nbsp;
                            <br/>
                            <div style="height:5px"></div>-->
                              任务状态:<br/>
                            <a class="btn btn-xs btn btn-primary purple" id="checkboxAll" @click="selectAll()">全选</a>
                            <a class="btn btn-xs btn-info purple" @click="status_text('待处理')">1_待处理</a>
                            <a class="btn btn-xs btn-success purple" @click="status_text('已完成')">1_已完成</a>
                            <a class="btn btn-xs btn-warning purple" @click="status_text('进行中')">1_进行中</a>
                            <a class="btn btn-xs btn-danger purple" @click="status_text('异常')">1_异常</a> 
                            <a class="btn btn-xs btn-danger purple" @click="status_text('密码错误')">1_密码错误</a> 
                            <a class="btn btn-xs btn-dark purple" @click="status_text('已退单')">1_已退单</a>  
                            <a class="btn btn-xs btn-default purple" @click="status_text('已取消')">1_已取消</a>
                            <a class="btn btn-xs btn-danger purple" @click="status_text('未授权')">1_未授权</a>  
                            <a class="btn btn-xs btn-danger purple" @click="status_text('待验证')">1_待验证</a>   
                            <br/>
                            <div style="height:5px"></div>
                            处理状态:<br/>
                            <a class="btn btn-xs btn-info purple" @click="dock(0)">2_待处理</a>
                            <a class="btn btn-xs  btn-success purple" @click="dock(1)">2_已完成</a>
                            <a class="btn btn-xs btn-danger purple" @click="dock(2)">2_处理失败</a>
                            <a class="btn btn-xs btn-warning purple" @click="dock(3)">2_重复下单</a> 
                            <a class="btn btn-xs btn-default purple" @click="dock(4)">2_取消</a> 
                            <a class="btn btn-xs btn-default purple"@click="del(sex)">2_删除</a> 
                            <a class="btn btn-xs btn-default purple" @click="dock(99)">2_我的</a> 
                             <a class="btn btn-xs btn-danger purple" @click="tk(sex)">2_退款</a>
                             <br>
                               <span>注：勾选订单后才能修改任务状态或者处理状态，会后端执行</span>
                  </div> <div class="bg-gradient-tron">
                            批量操作:<br/>
                           <a class="btn btn-xs  btn-success purple" @click="plup('待更新')">批量同步</a>
                            <a class="btn btn-xs  btn-success purple" @click="plbs('待重刷')">批量补刷</a>
                             <br>
                               <span>注：勾选订单后才能修改任务状态或者处理状态，会后端执行</span>
                  </div> 
                  
                  

                  
		       <div class="layui-table  table-responsive table-condensed" lay-size="sm" >
		        <table class="table table-striped">
		          <thead style="white-space:nowrap"><tr><!--<th>#</th>-->
		          	<th><label class="lyear-checkbox checkbox-inline checkbox-success">
                           <input type="checkbox" id="checkboxAll"  @click="selectAll()"><span>ID</span>
                       </label>
					</th>
		          	<th>操作</th><th class="tac" width="100">平台</th><th>学校&nbsp;账号&nbsp;密码</th><th>课程名称</th><th>金额</th><th>任务状态</th><th class="tac" width="130">详细订单实时进度</th><th>备注</th><th>提交时间</th><th v-if="row.uid==1">处理状态</th><th v-if="row.uid==1">UID</th><th v-if="row.uid==1">修改备注</th>								</tr>
							</thead>
						
							<tbody>
		              
		            <tr v-for="res in row.data">		            					
								  <!--<td>
								  	<span class="checkbox checkbox-success">
			              <input type="checkbox" id="checkboxAll" :value="res.oid" v-model="sex"><label for="checkbox1"></label></span>
								  </td>-->   
								 
					
         <td style="white-space:nowrap" >
		            		<label class="lyear-checkbox checkbox-inline checkbox-success">
                                <input type="checkbox" id="checkboxAll" :value="res.oid" v-model="sex"><span v-if="row.uid==1">{{res.oid}}</span><span v-else>-</span>
						    </label>
					   </td>	
				       <td style="text-align: center; white-space: nowrap;">
		               							  		<!--清华社>-->   			      
							<div v-if="res.cid=='0'">
							  <span class="btn btn-xs btn-danger" @click="bs(res.oid)">补单</span>&nbsp;</span>
							   <button class="btn btn-xs btn-warning" @click="jietu(res.user)">截图</span>
							   <button @click="up(res.oid)" class="btn btn-xs  btn-default" >更新</span>						
							</div>
													
							<div v-else> 
						<!--暗网>-->   			      
							<div v-if="res.cid=='30'">
							 <span class="btn btn-xs  btn-dark" @click="bs(res.oid)">重刷</span>&nbsp;</span>
							</div>
								
							<div v-else> 
						<!--暗网>-->   			      
							<div v-if="res.cid=='31'">
							 <span class="btn btn-xs  btn-dark" @click="bs(res.oid)">重刷</span>&nbsp;</span>
							</div>
													
							<div v-else>  
								<!--暗网>-->   			      
							<div v-if="res.cid=='3027'">
							 <button @click="up(res.oid)" class="btn btn-xs btn-info">更新</button>
							</div>
							
							<div v-else>  	
									  		<!--截图>-->   			      
							<div v-if="res.cid=='0'">
							 <span class="btn btn-xs  btn-dark" @click="bs(res.oid)">重刷</span>&nbsp;</span>
							   <button @click="jietu(res.user)" class="btn btn-xs  btn-default" >截图</button>&nbsp;
							</div>
													
							<div v-else>  
							    <!--论文通用模板-论文撰写-无视查重率[自定义目录]>-->   			      
                          <div v-if="res.cid=='201'">
                            <div v-if="res.status=='已完成'">
                     <a :href="`https://api.wkhome.homes/arctile2/${res.yid}.docx`" class="btn btn-xs btn-default">保存</a>&nbsp;
            
                             <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                              </div>
                             <div v-else>
                             <span class="btn btn-xs  btn-dark" @click="bs(res.oid)">重刷</span>&nbsp;
                             <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                               </div>
                               </div>
							        <div v-else>  
								<!--论文通用模板-论文撰写-无视查重率[字数1万字]>-->   			      
                          <div v-if="res.cid=='202'">
                       <div v-if="res.status=='已完成'">
                      <a :href="`https://api.wkhome.homes/arctile/${res.yid}.docx`" class="btn btn-xs btn-default">保存</a>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                     </div>
                     <div v-else>
                         <span class="btn btn-xs  btn-dark" @click="bs(res.oid)">重刷</span>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                        </div>
                        </div>
									<div v-else>  
								<!--论文通用模板-论文开题报告[编辑标题]>-->   			      
                          <div v-if="res.cid=='203'">
                       <div v-if="res.status=='已完成'">
                      <a :href="`https://api.wkhome.homes/zhoujiqixi/${res.yid}.docx`" class="btn btn-xs btn-default">保存</a>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                     </div>
                     <div v-else>
                         <span class="btn btn-xs  btn-dark" @click="bs(res.oid)">重刷</span>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                        </div>
                        </div>
									<div v-else>  
								<!--论文论文任务书测试中>-->   			      
                          <div v-if="res.cid=='0'">
                       <div v-if="res.status=='已完成'">
                      <span @click="lunw(res.oid)" class="btn btn-xs btn-default">保存</span>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                     </div>
                     <div v-else>
                         <span class="btn btn-xs  btn-dark" @click="bs(res.oid)">重刷</span>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                        </div>
                        </div>
									<div v-else>  
							
								<!--论文通用模板-周记日记撰写[自定义篇数]>-->   			      
                          <div v-if="res.cid=='357'">
                       <div v-if="res.status=='已完成'">
                      <a :href="`https://api.wkhome.homes/zhoujix/${res.yid}.docx`" class="btn btn-xs btn-default">保存</a>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                     </div>
                     <div v-else>
                         <span class="btn btn-xs  btn-dark" @click="bs(res.oid)">重刷</span>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                        </div>
                        </div>
									<div v-else>  
								<!--论文通用模板-日记撰写[自定义篇数]>-->   			      
                          <div v-if="res.cid=='472'">
                       <div v-if="res.status=='已完成'">
                      <a :href="`https://api.wkhome.homes/riji/${res.yid}.docx`" class="btn btn-xs btn-default">保存</a>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                     </div>
                     <div v-else>
                         <span class="btn btn-xs  btn-dark" @click="bs(res.yid)">重刷</span>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                        </div>
                        </div>
									<div v-else>  
								<!--论文通用模板-月记撰写[自定义篇数]>-->   			      
                          <div v-if="res.cid=='473'">
                       <div v-if="res.status=='已完成'">
                      <a :href="`https://api.wkhome.homes/yueji/${res.yid}.docx`" class="btn btn-xs btn-default">保存</a>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                     </div>
                     <div v-else>
                         <span class="btn btn-xs  btn-dark" @click="bs(res.oid)">重刷</span>&nbsp;
                        <button @click="TgTips()" class="btn btn-xs btn-warning">教程</button>&nbsp;
                        </div>
                        </div>
									<div v-else>  
    <span>
        <span class="btn btn-xs  btn-dark" @click="bs(res.oid)">重刷</span>
        &nbsp;
        <button @click="up(res.oid)" class="btn btn-xs btn-cyan">更新</button>
    </span>
</td>
		            
		            
		            
                        <td style="text-align: center;" v-else>-</td>
		            	
		            	
		            	<td style="text-align: center;">{{res.ptname}}<span v-if="res.miaoshua=='1'" style="color: red;">&nbsp;秒刷</span></td>	            	      	
		            	<td>{{res.school}}
		                     {{res.user}}
		            	     {{res.pass}}
		            	</td>
		            	<td>{{res.kcname}}</td>
		            	<td style="text-align: center;">{{res.fees}}</td>
		            	
		            				<td style="text-align: center;white-space:nowrap;line-height:50px">
										<span v-if="res.status=='待处理'"
											class="el-button el-button--warning   is-plain el-button--mini"
											style="padding: 4px 10px;" @click="ddinfo(res)">{{res.status}}</span>
										<span v-else-if="res.status=='已完成'"
											class="el-button el-button--success   is-plain el-button--mini"
											style="padding: 4px 10px;"  @click="ddinfo(res)">{{res.status}}</span>

										<span v-else-if="res.status=='异常'"
											class="el-button el-button--danger  is-plain el-button--mini"
											style="padding: 4px 10px;" @click="ddinfo(res)">{{res.status}}</span>

										<span v-else-if="res.status=='进行中'"
											class="el-button el-button--primary is-plain el-button--mini"
											style="padding: 4px 10px;" @click="ddinfo(res)">{{res.status}}</span>
										<span v-else-if="res.status=='待考试'"
											class="el-button el-button--primary is-plain el-button--mini"
											style="padding: 4px 10px;" @click="ddinfo(res)">{{res.status}}</span>

										<span v-else class="el-button el-button--info is-plain el-button--mini"
											style="padding: 4px 10px;" @click="ddinfo(res)">{{res.status}}</span>
									</td>
		            
		            	<!--<td>{{res.jindu}}
		            	<div class="progress">
		            	        <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" class="progress-bar progress-bar-success active progress-bar-striped" role="progressbar" v-bind:style="'width:'+(res.jindu)+';' ">
		            	        </div>-->
		            	</td>
		            		 <td>
		              <div class="layui-progress layui-progress-big" lay-showPercent="yes">
                      <div class="layui-progress-bar layui-bg-success" v-bind:style="{width: res.process}">
                      <span class="layui-progress-text">{{res.process}}</span>
		            	        </div>
		            	<!--<div class="progress">
		            	        <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" class="progress-bar progress-bar-success active progress-bar-striped" role="progressbar" v-bind:style="'width:'+(res.process)+';' ">
		            	        </div>--></td>
		            	    
		            	    
		            	<td>{{res.remarks}}</td>
		            	<td style="text-align: center;">{{res.addtime}}</td>
		            	<td nowrap="nowrap" v-if="row.uid==1" style="text-align: center;">
		            		<span @click="duijie(res.oid)" v-if="res.dockstatus==0" class="btn btn-xs btn-info">待处理</span>
		            		<span v-if="res.dockstatus==1" class="btn btn-xs btn-success">处理成功</span>
		            		<span @click="duijie(res.oid)" v-if="res.dockstatus==2" class="btn btn-xs btn-danger">处理失败</span>
		            		<span v-if="res.dockstatus==3" class="">重复下单</span>
		            		<span v-if="res.dockstatus==4" class="">已取消</span>
		            		<span v-if="res.dockstatus==99" class="btn btn-xs btn-warning">自营</span></td>
		            	
		            		<td v-if="row.uid==1" style="text-align: center;">{{res.uid}}</td>
		            		<td v-if="row.uid==1"><button @click="dd_passwd(res.oid)"  class="btn btn-xs btn-warning">修改备注</button></td>
		            <!--td v-if="res.status!='已取消'"><button @click="ms(res.oid)" v-if="res.miaoshua==0" class="btn btn-xs btn-danger">秒刷</button>&nbsp;<button @click="up(res.oid)" class="btn btn-xs btn-success">更新状态</button>&nbsp;<button @click="bs(res.oid)" class="btn btn-xs btn-primary">补刷</button>&nbsp;<button @click="quxiao(res.oid)"  class="btn btn-xs btn-info">取消</button></td--> 
		            </tr>
                        
		          </tbody>
		        </table>
		      </div>
		      
			     <ul class="pagination" v-if="row.last_page>1"><!--by 青卡 Vue分页 -->
			         <li class="disabled"><a @click="get(1)">首页</a></li>
			         <li class="disabled"><a @click="row.current_page>1?get(row.current_page-1):''">&laquo;</a></li>
		            <li  @click="get(row.current_page-3)" v-if="row.current_page-3>=1"><a>{{ row.current_page-3 }}</a></li>
						    <li  @click="get(row.current_page-2)" v-if="row.current_page-2>=1"><a>{{ row.current_page-2 }}</a></li>
						    <li  @click="get(row.current_page-1)" v-if="row.current_page-1>=1"><a>{{ row.current_page-1 }}</a></li>
						    <li :class="{'active':row.current_page==row.current_page}" @click="get(row.current_page)" v-if="row.current_page"><a>{{ row.current_page }}</a></li>
						    <li  @click="get(row.current_page+1)" v-if="row.current_page+1<=row.last_page"><a>{{ row.current_page+1 }}</a></li>
						    <li  @click="get(row.current_page+2)" v-if="row.current_page+2<=row.last_page"><a>{{ row.current_page+2 }}</a></li>
						    <li  @click="get(row.current_page+3)" v-if="row.current_page+3<=row.last_page"><a>{{ row.current_page+3 }}</a></li>		       			     
			         <li class="disabled"><a @click="row.last_page>row.current_page?get(row.current_page+1):''">&raquo;</a></li>
			         <li class="disabled"><a @click="get(row.last_page)">尾页</a></li>	    
			     </ul> 
			     
   
			    <div id="ddinfo2" style="display: none;"><!--订单详情-->                    
			       <li class="list-group-item">
			       	<b>课程类型：</b>{{ddinfo3.info.ptname}}<span v-if="ddinfo3.info.miaoshua=='1'" style="color: red;">&nbsp;秒刷</span></li>
			       	<li class="list-group-item" style="word-break:break-all;"><b>账号信息：</b>{{ddinfo3.info.school}}&nbsp;{{ddinfo3.info.user}}&nbsp;{{ddinfo3.info.pass}}</li>
			       	<li class="list-group-item"><b>课程名字：</b>{{ddinfo3.info.kcname}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.name!='null'"><b>学生姓名：</b>{{ddinfo3.info.name}}</li>
			       	<li class="list-group-item"><b>下单时间：</b>{{ddinfo3.info.addtime}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.courseStartTime"><b>课程开始时间：</b>{{ddinfo3.info.courseStartTime}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.courseEndTime"><b>课程结束时间：</b>{{ddinfo3.info.courseEndTime}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.examStartTime"><b>考试开始时间：</b>{{ddinfo3.info.examStartTime}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.examEndTime"><b>考试结束时间：</b>{{ddinfo3.info.examEndTime}}</li>
			       	<li class="list-group-item"><b>订单状态：</b><span style="color: red;">{{ddinfo3.info.status}}</span>&nbsp;<button v-if="ddinfo3.info.dockstatus!='99'" @click="up(ddinfo3.info.oid)" class="btn btn-xs btn-success">更新</button>&nbsp;</li>
			       	<li class="list-group-item"><b>实时进度：</b>{{ddinfo3.info.process}}</li>
			       	<li class="list-group-item"><b>当前状态：</b>{{ddinfo3.info.remarks}}</li>
			       	<li class="list-group-item"><b>下单金额：</b>{{ddinfo3.info.fees}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.status!='已取消'"><b>操作：</b><span v-if="ddinfo3.info.cid<8"><button @click="bs(ddinfo3.info.oid)" class="btn btn-xs btn-primary">补单</button>&nbsp;<button v-if="ddinfo3.info.dockstatus=='99'" @click="dd_passwd(ddinfo3.info.oid)" class="btn btn-xs btn-info">修改密码</button>&nbsp;<button @click="quxiao(ddinfo3.info.oid)"  class="btn btn-xs btn-default">取消</button></li>		       	  
		      </div>
		      
		    </div>
		  </div>
  
    </div>
   </div>
 </div>
 

<script type="text/javascript" src="assets/LightYear/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>
<script src="./js/vue.min.js"></script>
<script src="./js/vue-resource.min.js"></script>
<script src="./js/axios.min.js"></script>
<script src="./js/eleprocess.js"></script>
<script>
vm=new Vue({
	el:"#orderlist",
	data:{
		  row:null,
		  phone:'',
		  userrow: userrow,
		  sex:[],
		  ddinfo3:{
		  	status:false,
		  	info:[]
		  },
		  dc:[],
		  dc2:{
		  	gs:1
		  },
		  cx:{
		  	status_text:'',
		  	dock:'',
		  	qq:'',
		  	oid:'',
		  	uid:'',
		  	school:'',
		  	kcname:'',
		  	cid:'',
		  	limit:'',
		  	id:<?php echo $id;?>,
		  }
	},
	methods:{
		get:function(page){
        var load = layer.load(2);
        var apiUrl = "/apisub.php?act=";
        // 根据 this.cx.uid 的值选择不同的接口
        apiUrl += this.userrow.uid == 1 ? "FDorderlistfl" : "user_orderlistfl";
        var data = {cx: this.cx, page: page};

        this.$http.post(apiUrl, data, {emulateJSON:true}).then(function(data){    
            layer.close(load);
            if(data.data.code == 1){
                this.row = data.body;
            } else {
                layer.msg(data.data.msg, {icon:2});
            }
        }); 
    },
	TgTips:function(){
	layer.alert('1.可以点编辑目录推荐目录的基础上修改或者新增 [<span style="color:red;">章节/小节标题以及字数</span>] <br>2.成为目标目录，系统将通过目标目录为你撰写文章，注意格式 <br>3.可以自己稍微修改格式也可以让客户自己改<span style="color:red;">模板定制联系上级</span> <br>4.能过98%论文网址查重 <br><span style="color:red;">5.欢迎尝试，显示完成即可点保存下载！</span> <br><span style="color:red;"><strong>6.因编辑目录存在修改性，</strong><strong>已完成后重刷会失效！</strong></span> <br><span style="color:red;">7.如果显示失败点重刷，重新编辑即可，</span><span ><br>一直失败请反馈上级！</span>', {
	
		title: '论文订单教程规则',
		skin: 'layui-layer-molv layui-layer-wxd'
		  , shadeClose: true
	});	
		},
		del: function(sex){
         layer.confirm('此操作会删除选中的订单,金额不会退回,请谨慎操作！', {btn: ['确定','取消'],title:'删除确认',icon:3}, function(index){
             var load=layer.load();
             $.post("/apisub.php?act=delorder",{sex:sex},{emulateJSON:true}).then(function(data){
                 layer.close(load);
                 if(data.code==1){
                     vm.selectAll();   
                     vm.get(vm.row.current_page);
                 layer.msg(data.msg,{icon:1});
                }else{
                     layer.msg(data.msg,{icon:2});
                }
             });
         });
     },
		bs:function(oid){
		 	 		 	layer.confirm('建议漏看或者进度被重置的情况下使用。<br>频繁点击补刷会出现不可预测的结果<br>请问是否补刷所选的任务？', {title:'温馨提示',icon:3,
							  btn: ['确定补刷','取消'] //按钮
							}, function(){
		 			     var load=layer.load(2);
		          $.get("/apisub.php?act=bs&oid="+oid,function (data) {
				 	     layer.close(load);
			             if (data.code==1){
			             	  vm.get(vm.row.current_page);		             	 
			                layer.alert(data.msg,{icon:1});	                
			             }else{
			                layer.msg(data.msg,{icon:2});
			             }	              
		         });
         });
		 },
		 dd_passwd:function(oid){
          console.log(oid)
						layer.prompt({title: '请输入新备注', formType: 3}, function(password, index){
						  var load=layer.load();
			              $.post("/apisub.php?act=dd_passwd",{oid:oid,pwd:password},function (data) {
					 	         layer.close(load);
				             if(data.code==1){
				             	  layer.close(index);
				             	  $("#ddinfo2").hide();
				             	  vm.get(vm.row.current_page);		
				                layer.alert(data.msg,{icon:1});
				             }else{
				                layer.msg(data.msg,{icon:2});
				             }
			              });		    		    
					  });
		 
	},plup:function(a){
             var load=layer.load();
          $.post("/apisub.php?act=plup&a="+a,{sex:this.sex,type:1},{emulateJSON:true}).then(function(data){
         layer.close(load);
              if (data.code==1){
                vm.selectAll();   
                 vm.get(vm.row.current_page);                              
                 layer.msg(data.msg,{icon:1});                 
              }else{
                 layer.msg(data.msg,{icon:2});
              }
         });
		 },plbs:function(a){
				var load=layer.load();
          $.post("/apisub.php?act=plbs&a="+a,{sex:this.sex,type:1},{emulateJSON:true}).then(function(data){
		 	     layer.close(load);
	             if (data.code==1){
	              	vm.selectAll();   
	             	  vm.get(vm.row.current_page);		             	            	 
	                layer.msg(data.msg,{icon:1});	                
	             }else{
	                layer.msg(data.msg,{icon:2});
	             }	              
         });
		 },up:function(oid){
				var load=layer.load(2);
				layer.msg("正在努力获取中....",{icon:3});
          $.get("/apisub.php?act=uporder&oid="+oid,function (data) {
		 	     layer.close(load);
	             if (data.code==1){
	             	  vm.get(vm.row.current_page);  
	             	  setTimeout(function() {
	             	  	for(i=0;i<vm.row.data.length;i++){           	
					            	 if(vm.row.data[i].oid==oid){
					            	 	  vm.ddinfo3.info=vm.row.data[i];
					            	 	  console.log(vm.row.data[i].oid);
					            	 	  console.log(vm.row.data[i].status);
					            	 	  console.log(vm.ddinfo3.info.status);
					            	 	  return true;
					            	 } 
					            } 
	             	  },1800);   	             	  		             	 
	                layer.msg(data.msg,{icon:1});	                               
	             }else{
	              	layer.msg(data.msg,{icon:2});	
//	                layer.alert(data.msg,{icon:2,btn:'立即跳转'},function(){
//	                	window.location.href=data.url
//	                });
	             }	              
         });			
		 },duijie:function(oid){
		 	layer.confirm('确定处理么?', {title:'温馨提示',icon:3,
							  btn: ['确定','取消'] //按钮
							}, function(){
		 			     var load=layer.load(2);
		          $.get("/apisub.php?act=duijie&oid="+oid,function (data) {
				 	     layer.close(load);
			             if (data.code==1){
			             	  vm.get(vm.row.current_page);		             	 
			                layer.alert(data.msg,{icon:1});	                
			             }else{
			                layer.msg(data.msg,{icon:2});
			             }	              
		         });
         });
		 },
		 lunw:function(){
	layer.alert('自动发货到邮箱本地不保存', {
	
		title: '论文订单教程规则',
		skin: 'layui-layer-molv layui-layer-wxd'
		  , shadeClose: true
	});	
		},
	  	bianji:function(){
		   // window.location.href="/jietu/exam.hm86.cn/examination.php?paperid=1000000001&usercodepaperid=M2T0A2w1M-D1d1f-M1T6AhwmMsDocfwtMjc4NV8xMDAwMDAwMDAx&oid="+oid
	  window.open('https://wkhome.homes/content/', '_blank');
	  },
		 getname:function(oid){
				var load=layer.load(2);
          $.get("/apisub.php?act=getname&oid="+oid,function (data) {
		 	     layer.close(load);
	             if (data.code==1){	             		             	 
	                layer.msg(data.msg,{icon:1});	                
	             }else{
	                layer.msg(data.msg,{icon:2});
	             }	              
         });			
		 },ms:function(oid){
			 	layer.confirm('提交秒刷将扣除0.05元服务费', {title:'温馨提示',icon:3,
								  btn: ['确定','取消'] //按钮
								}, function(){
			 			     var load=layer.load(2);
			          $.get("/apisub.php?act=ms_order&oid="+oid,function (data) {
					 	     layer.close(load);
				             if (data.code==1){
				             	  vm.get(vm.row.current_page);		             	 
				                layer.alert(data.msg,{icon:1});	                
				             }else{
				                layer.msg(data.msg,{icon:2});
				             }	              
			         });
	         });		
		 },quxiao:function(oid){
		 	 		 	layer.confirm('取消订单将无法退款，确定取消吗', {title:'温馨提示',icon:3,
							  btn: ['确定','取消'] //按钮
							}, function(){
		 			     var load=layer.load(2);
		          $.get("/apisub.php?act=qx_order&oid="+oid,function (data) {
				 	     layer.close(load);
			             if (data.code==1){
			             	  vm.get(vm.row.current_page);		             	 
			                layer.alert(data.msg,{icon:1});	                
			             }else{
			                layer.msg(data.msg,{icon:2});
			             }	              
		         });
         });
		 },status_text:function(a){
				var load=layer.load(2);
          $.post("/apisub.php?act=status_order&a="+a,{sex:this.sex,type:1},{emulateJSON:true}).then(function(data){
		 	     layer.close(load);
	             if (data.code==1){
	              	vm.selectAll();   
	             	  vm.get(vm.row.current_page);		             	            	 
	                layer.msg(data.msg,{icon:1});	                
	             }else{
	                layer.msg(data.msg,{icon:2});
	             }	              
         });	        
		 },dock:function(a){
				var load=layer.load(2);
          $.post("/apisub.php?act=status_order&a="+a,{sex:this.sex,type:2},{emulateJSON:true}).then(function(data){
		 	     layer.close(load);
	             if (data.code==1){
	              	vm.selectAll();   
	             	  vm.get(vm.row.current_page);		             	            	 
	                layer.msg(data.msg,{icon:1});	                
	             }else{
	                layer.msg(data.msg,{icon:2});
	             }	              
         });	        
		 },selectAll: function () {
            if(this.sex.length==0) {
	          	for(i=0;i<vm.row.data.length;i++){           	
	            	vm.sex.push(this.row.data[i].oid)
	            }    	     	
          	}else{
          		this.sex=[]
          	}                           
      },ddinfo: function(a){  
      	    this.ddinfo3.info=a;
      	    var load=layer.load(2,{time:300});
      	    setTimeout(function() {
	             layer.open({
							  type: 1,
							  title:'订单详情操作',
							  skin: 'layui-layer-demo',
							  closeBtn: 1,
							  anim: 2,
							  shadeClose: true,
							  content: $('#ddinfo2'),
							  end: function(){ 
							    $("#ddinfo2").hide();
							  }
							});  
            }, 100); 
            
      },tk: function(sex) {
			    if(this.sex==''){layer.msg("请先选择订单！");return false;}
			    layer.confirm('确定要退款吗？陛下，三思三思！！！', {title: '温馨提示',icon: 3,btn: ['确定', '取消']}, function() {
    				var load = layer.load();
    				$.post("/apisub.php?act=tk",{sex: sex}, {emulateJSON: true}).then(function(data) {
    					layer.close(load);
    					if (data.code == 1) {
    						vm.selectAll();
    						vm.get(vm.row.current_page);
    						layer.msg(data.msg, {icon: 1});
    					} else {
    						layer.msg(data.msg, {
    							icon: 2
    						});
    					}
    				});
				});
			},
			daochu1:function(){
      	     if(this.dc2.gs==''){
      	     	  layer.msg("请先选择格式",{icon:2});
      	     	  return false;
      	     }     	 
      	     if(!this.sex[0]){
      	     	  layer.msg("请先选择订单",{icon:2});
      	     	  return false;
      	     }     
      	     for(i=0;i<this.sex.length;i++){
      	        	oid=this.sex[i];
      	        	for(x=0;x<this.row.data.length;x++){
	      	        	 if(this.row.data[x].oid==oid){
	      	        	 	  school=this.row.data[x].school;
	      	     	    	  user=this.row.data[x].user;
	      	     	    	  pass=this.row.data[x].pass;
	      	     	    	  kcname=this.row.data[x].kcname;
	      	     	    	  if(this.dc2.gs=='1'){
	      	     	    	  	 a=school+' '+user+' '+pass+' '+kcname; 
	      	     	    	  }else if(this.dc2.gs=='2'){
	      	     	    	  	 a=user+' '+pass+' '+kcname; 
	      	     	    	  }else if(this.dc2.gs=='3'){
	      	     	    	  	 a=school+' '+user+' '+pass;
	      	     	    	  }else if(this.dc2.gs=='4'){
	      	     	    	  	 a=user+' '+pass;
	      	     	    	  }
	      	     	    	  this.dc.push(a)
	      	     	    }
      	        	}  
      	     }   	     
      	     layer.alert(this.dc.join("<br>"));
      	     this.dc=[];        
      },
		 daochu: function () {
    if (this.dc2.gs == '') {
        layer.msg("请先选择格式", { icon: 2 });
        return false;
    }
    if (!this.sex[0]) {
        layer.msg("请先选择订单", { icon: 2 });
        return false;
    }

    // 构建 Excel 数据
    const excelData = [];
    const header = [];

    // 根据用户选择的格式构建表头
    if (this.dc2.gs == '1') {
        header.push('学校', '用户名', '密码', '课程名');
    } else if (this.dc2.gs == '2') {
        header.push('用户名', '密码', '课程名');
    } else if (this.dc2.gs == '3') {
        header.push('学校', '用户名', '密码');
    } else if (this.dc2.gs == '4') {
        header.push('用户名', '密码');
    }

    excelData.push(header);

    // 构建表格数据
    for (let i = 0; i < this.sex.length; i++) {
        const oid = this.sex[i];
        for (let x = 0; x < this.row.data.length; x++) {
            if (this.row.data[x].oid == oid) {
                const rowData = [];

                // 根据用户选择的格式构建表格数据
                if (this.dc2.gs == '1') {
                    rowData.push(this.row.data[x].school, this.row.data[x].user, this.row.data[x].pass, this.row.data[x].kcname);
                } else if (this.dc2.gs == '2') {
                    rowData.push(this.row.data[x].user, this.row.data[x].pass, this.row.data[x].kcname);
                } else if (this.dc2.gs == '3') {
                    rowData.push(this.row.data[x].school, this.row.data[x].user, this.row.data[x].pass);
                } else if (this.dc2.gs == '4') {
                    rowData.push(this.row.data[x].user, this.row.data[x].pass);
                }

                excelData.push(rowData);
            }
        }
    }

    // 设置列宽和行高
    const wscols = [
        { wpx: 100 }, // 学校列宽度
        { wpx: 100 }, // 用户名列宽度
        { wpx: 100 }, // 密码列宽度
        { wpx: 180 }, // 课程名列宽度
    ];

    const wshrows = [{ hpx: 30 }]; // 设置表头行高

    // 设置表格中每一行的行高
    for (let i = 0; i < excelData.length; i++) {
        wshrows.push({ hpx: 30 });
    }

    // 弹出确认导出对话框
    layer.confirm('确认导出为xlsx文件？', {
        btn: ['确认', '取消'],
        icon: 3,
        title: '提示'
    }, (index) => {
        // 用户点击确认按钮时执行导出操作
        const ws = XLSX.utils.aoa_to_sheet(excelData);
        
        // 设置列宽和行高
        ws['!cols'] = wscols;
        ws['!rows'] = wshrows;

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, '订单数据.xlsx');
        
        // 关闭确认对话框
        layer.close(index);
    }, (index) => {
        // 用户点击取消按钮时执行的操作
        // 关闭确认对话框
        layer.close(index);
    });
}
	},
	mounted(){
		this.get(1);
	}
});
</script>