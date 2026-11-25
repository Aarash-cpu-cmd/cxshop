<?php
$mod='blank';
$title='订单列表';
require_once('head.php');
?>

     <div class="app-content-body ">
         
         <!-- 引入样式 -->
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
<!-- 引入组件库 -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>

         
        <div class="wrapper-md control">
        	
	    <div class="panel panel-default" id="orderlist">
		    <div class="panel-heading font-bold bg-white">任务列表 (状态同步不及时，有时需手动更新)</div>
				 <div class="panel-body">
				 		   <div class="form-horizontal devform">	
				 		   		<div class="form-group">				 		   			
				 		   				<div class="col-sm-1 col-xs-4" style="width: 150px;">		          
					              <select class="layui-select" v-model="cx.cid" style="    background: url('../index/arrow.png') no-repeat scroll 99%;width: 125px;">
					                  
					              	<option value="">平台</option>
					                <?php
					                $a=$DB->query("select * from qingka_wangke_class where status=1 ");
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
					                <option value="已完成">已完成</option>
					                <option value="补刷中">补刷中</option>
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
					             <select class="layui-select"  v-model="dc2.gs" style="    background: url('../index/arrow.png') no-repeat scroll 99%;width: 125px;">
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
				 	      	<div class="col-sm-2 col-xs-6">
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
                            <!--<a class="btn btn-xs btn btn-primary purple" id="checkboxAll" @click="selectAll()">全选</a>-->
                            任务状态:<br/>
                            <span class="badge badge-dot-lg badge-dot bg-yellow"></span><a @click="status_text('待处理')"><font color="#fcc525">&nbsp;待处理</font></a>&nbsp;&nbsp;
                            <span class="badge badge-dot-lg badge-dot bg-success"></span><a @click="status_text('已完成')"><font color="#15c377">&nbsp;已完成</font></a>&nbsp;&nbsp;
                            <span class="badge badge-dot-lg badge-dot bg-purple"></span><a @click="status_text('进行中')"><font color="#926dde">&nbsp;进行中</font></a>&nbsp;&nbsp;
                            <span class="badge badge-dot-lg badge-dot bg-pink"></span><a @click="status_text('异常')"><font color="#f96197">&nbsp;异常</font></a>&nbsp;&nbsp;
                            <span class="badge badge-dot-lg badge-dot bg-primary"></span><a @click="status_text('已取消')"><font color="#33cabb">&nbsp;已取消</font></a>&nbsp;&nbsp;
                            <br/>
                            <div style="height:5px"></div>
                            处理状态:<br/>
                            <a class="btn btn-xs btn-info purple" @click="dock(0)">2_待处理</a>
                            <a class="btn btn-xs  btn-success purple" @click="dock(1)">2_已完成</a>
                            <a class="btn btn-xs btn-danger purple" @click="dock(2)">2_处理失败</a>
                            <a class="btn btn-xs btn-warning purple" @click="dock(3)">2_重复下单</a> 
                            <a class="btn btn-xs btn-default purple" @click="dock(4)">2_取消</a> 
                            <a class="btn btn-xs btn-default purple" @click="没想好写啥">2_删除</a> 
                            <a class="btn btn-xs btn-default purple" @click="dock(99)">2_我的</a> 
                             <a class="btn btn-xs btn-danger purple" @click="tk(sex)">2_退款</a>
                             <br>
                            <span>注：勾选订单后才能修改任务状态或者处理状态</span>
                  </div> 
			           <div class="bg-gradient-tron">
                            批量操作:<br/>
                           <a class="btn btn-xs  btn-success purple" @click="plup('待更新')">批量同步</a>
                            <a class="btn btn-xs  btn-success purple" @click="plbs('待重刷')">批量补刷</a>
                             <br>
                            <span>注：勾选订单后才能修改任务状态或者处理状态。批量补刷后不可立即点更新，否则补刷不成功！！！</span>
                  </div> 
                  
                  

                  
		      <div class="layui-table table-responsive" lay-size="sm" >
		        <table class="table table-bordered">
		          <thead><tr><th style="text-align: center;"><input type="checkbox" id="checkboxAll" @click="selectAll()" /></th><th style="text-align: center;"><b>ID</b></th><th style="text-align: center;"><b>平台</b></th><th style="text-align: center;"><b>学校&nbsp;账号&nbsp;密码</b></th><th style="text-align: center;"><b>课程名</b></th><th nowrap="nowrap" style="text-align: center;"><b>任务状态</b></th><th style="text-align: center;"><b>刷新</b></th><th style="text-align: center;"><b>补刷</b></th><th style="text-align: center;"><b>删除</th><!--<th>进度条</th>--><th style="text-align: center;"><b>详细进度</b></th><th style="text-align: center;"><b>备注</b></th><th style="text-align: center;"><b>提交时间</th><th v-if="row.uid==1" style="text-align: center;"><b>处理状态</b></th><th style="text-align: center;"><b>详情</b></th><th style="text-align: center;"><b>金额</b></th><th v-if="row.uid==1" style="text-align: center;"><b>UID</b></th></tr></thead>
		          <tbody>
		            <tr v-for="res in row.data">
								  <td style="text-align: center;"> 
								  	<span class="checkbox checkbox-success">
								  	    <input type="checkbox" id="checkboxAll" :value="res.oid" v-model="sex"><label for="checkbox1"></label></span>
								  </td>
		            	<td style="text-align: center;"><span v-if="row.uid==1">{{res.oid}}</span><span v-else>-</span></td>
		            	<td style="text-align: center;">{{res.ptname}}<span v-if="res.miaoshua=='1'" style="color: red;">&nbsp;秒刷</span></td>	            	      	
		            	<td>{{res.school}}
		                     {{res.user}}
		            	     {{res.pass}}
		            	</td>
		            	<td>{{res.kcname}}</td>
		            	
		            	
		            	<td nowrap="nowrap" style="text-align: center;">
		            		<div v-if="res.status=='待处理'"><span class="badge badge-dot-lg badge-dot bg-yellow"></span><font color="#fcc525">&nbsp;{{res.status}}</font></div>
		            		<div v-else-if="res.status=='已完成'"><span class="badge badge-dot-lg badge-dot bg-success"></span><font color="#15c377">&nbsp;{{res.status}}</font></div>
		            		<div v-else-if="res.status=='进行中'"><span class="badge badge-dot-lg badge-dot bg-purple"></span><font color="#926dde">&nbsp;{{res.status}}</font></span></div>
		            		<div v-else-if="res.status=='异常'"><span class="badge badge-dot-lg badge-dot bg-pink"></span><font color="#f96197">&nbsp;{{res.status}}</font></div>
		            		<div v-else><span class="badge badge-dot-lg badge-dot bg-primary"></span><font color="#33cabb">&nbsp;{{res.status}}</font></span>
		            	</td> 
		            	<td style="text-align: center;"><span class="btn btn-xs btn-primary" @click="up(res.oid)"><i class="glyphicon glyphicon-repeat"></i> </span></td>
		            	<td style="text-align: center;"><span class="btn btn-xs btn-warning" @click="bs(res.oid)"><i class="glyphicon glyphicon-refresh"></i> </span></td>
		            	<td style="text-align: center;"><span class="btn btn-xs btn-danger" @click="quxiao(res.oid)"><i class="glyphicon glyphicon-remove"></i> </span></td>
		            	<!--<td>{{res.jindu}}
		            	<div class="progress">
		            	        <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" class="progress-bar progress-bar-success active progress-bar-striped" role="progressbar" v-bind:style="'width:'+(res.jindu)+';' ">
		            	        </div>-->
		            	</td>
		            	<td>{{res.process}}
		            	<div class="progress">
		            	        <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" class="progress-bar progress-bar-success active progress-bar-striped" role="progressbar" v-bind:style="'width:'+(res.process)+';' ">
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
		            	<td style="text-align: center;"><span class="btn btn-xs btn-primary" @click="ddinfo(res)"><i class="glyphicon glyphicon-cog"></i> </span>
		            		</td>
		            	<td style="text-align: center;">{{res.fees}}</td>
		            		<td v-if="row.uid==1" style="text-align: center;">{{res.uid}}</td>
		            <!--td v-if="res.status!='已取消'"><button @click="ms(res.oid)" v-if="res.miaoshua==0" class="btn btn-xs btn-danger">秒刷</button>&nbsp;<button @click="up(res.oid)" class="btn btn-xs btn-success">更新状态</button>&nbsp;<button @click="bs(res.oid)" class="btn btn-xs btn-primary">补刷</button>&nbsp;<button @click="quxiao(res.oid)"  class="btn btn-xs btn-info">取消</button></td--> 
		            </tr>
                        
		          </tbody>
		        </table>
		      </div>
		      
			     <ul class="pagination pagination-circle" v-if="row.last_page>1"> 
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
			       	<li class="list-group-item"><b>订单状态：</b><span style="color: red;">{{ddinfo3.info.status}}</span>&nbsp;<button v-if="ddinfo3.info.dockstatus!='99'" @click="up(ddinfo3.info.oid)" class="btn btn-xs btn-success">刷新</button>&nbsp;</li>
			       	<li class="list-group-item"><b>进度：</b>
			       	{{ddinfo3.info.process}}
			       	
			       	<!--进度条-->
			       	<!--<div class="progress">-->
		         <!--   	        <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" class="progress-bar active progress-bar-success progress-bar-striped" role="progressbar" v-bind:style="'width:'+(ddinfo3.info.process)+';' ">-->
		         <!--   	        </div>-->
		         <!--   	    </div></li>-->
			       	<li class="list-group-item" v-if="ddinfo3.info.remarks"><b>备注：</b>{{ddinfo3.info.remarks}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.status!='已取消'"><b>操作：</b><button @click="ms(ddinfo3.info.oid)" v-if="false" class="btn btn-xs btn-danger">秒刷</button>&nbsp;<button v-if="false" @click="layer.msg('更新中，近期开放')" class="btn btn-xs btn-info">修改密码</button>&nbsp;<button @click="bs(ddinfo3.info.oid)" class="btn btn-xs btn-primary">补刷</button>&nbsp;<button @click="quxiao(ddinfo3.info.oid)"  class="btn btn-xs btn-default">删除</button></li>		       	  
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
<script src="assets/js/vue.min.js"></script>
<script src="assets/js/vue-resource.min.js"></script>
<script src="assets/js/axios.min.js"></script>




	
	
<script>
vm=new Vue({
	el:"#orderlist",
	data:{
		  row:null,
		  phone:'',
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
		  	limit:''
		  }
	},
	methods:{
		get:function(page){
		  var load=layer.load(2);
		  data={cx:this.cx,page}
 			this.$http.post("/apisub.php?act=user_orderlist",data,{emulateJSON:true}).then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){
	          		this.row=data.body;
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
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
		 },getname:function(oid){
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
			},daochu:function(){
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
      }
		 
	},
	mounted(){
		this.get(1);
	}
});
</script>
