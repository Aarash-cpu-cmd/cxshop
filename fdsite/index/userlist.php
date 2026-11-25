<?php
$title='代理列表';
require_once('head.php');
// 在页面渲染时将用户信息传递给JavaScript
echo '<script>';
echo 'var userrow = { uid: ' . $userrow['uid'] . ' };'; // 将用户的uid传递给JavaScript
// 可以将其他用户信息也传递给JavaScript，例如：用户名、角色等
echo '</script>';
if($userrow['uid']<'1'){exit("<script language='javascript'>window.location.href='/login.php';</script>");}
?>
     <div class="app-content-body ">
        <div class="wrapper-md control" id="userlist">
        	
	    <div class="panel panel-default"  >
		    <div class="panel-heading font-bold bg-white" >代理管理&nbsp;&nbsp;<br/><br/>
		    <!--<button class="btn btn-xs btn-info " data-toggle="modal" data-target="#modal-adduser"><label><i class="mdi mdi-account-multiple-plus"></i></label>&nbsp;添加用户</button>&nbsp;
		    <button class="btn btn-xs btn-yellow" data-toggle="modal" data-target="#modal-czgz"><label><i class="mdi mdi-account-alert"></i></label>&nbsp;用户规则</button>-->
		    <el-button type="primary" size="small" data-toggle="modal" data-target="#modal-adduser">添加用户</el-button>
		    <el-button type="primary" size="small" data-toggle="modal" data-target="#modal-czgz">用户规则</el-button>
		    </div>
				 <div class="panel-body" >
				     <div class="el-form layui-row layui-col-space10">
				      <div class="form-inline">	
						  	<div class="form-group">			          
				              <el-select class="select" name="type" v-model="type" style="    background: url('../index/arrow.png') no-repeat scroll 99%;width: 100%">
				                <el-option label="UID" value="1"></el-option>
	 		   				    <el-option label="用户名" value="2"></el-option>
	 		   				    <el-option label="邀请码" value="3"></el-option>
	 		   				    <el-option label="昵称" value="4"></el-option>
	 		   				    <el-option label="费率" value="5"></el-option>
	 		   					<el-option label="余额" value="6"></el-option>
	 		   					<el-option label="最后在线时间" value="7"></el-option>
				              </el-select>
			             </div>  
			              <div class="form-group">
				                <el-input v-model="qq" placeholder="请输入内容"></el-input>
			              </div>
			              <div class="form-group">
				               <el-button type="primary" icon="el-icon-search" @click="get(1)">查询用户</el-button>
				               <el-button  type="success" icon="el-icon-top-up"  @click="khcz">跨户充值</el-button>
			              </div>			              
			          </div>
		      <div class="table-responsive"> 
		        <table class="table table-striped">
		          <thead><tr><th>上级UID</th><th>ID</th><th>昵称</th><th>账号</th><th>余额</th><th>费率</th><th>订单</th><th>总充值</th><th>密匙</th><th  v-if="userrow.uid === 1">跨户充值</th><th>邀请码</th><th>封禁/正常</th><!--<th>添加时间</th>--><th>最后在线时间</th><th>操作</th></tr></thead>
		          <tbody>
		            <tr v-for="res in row.data">
		                <td>{{res.uuid}}</td>
		            	<td>{{res.uid}}</td>		            	
		            	<td>{{res.name}}</td>
		            	<td>{{res.user}}</td>
		            	<td>{{res.money}}</td>
		            	<td>{{res.addprice}}</td>
		            	<td>{{res.dd}}</td>
		            	<!--<td><span class="btn btn-xs btn-success" v-if="res.qq_openid!=''">是</span><span class="btn btn-xs btn-danger" v-else>否</span></td>-->	            	
<!--		            	<td>{{res.dd}}</td>-->
		            	<td>{{res.zcz}}</td>
		            	<td><span class="el-button btn-xs btn-success" v-if="res.key!=0">已开通</span><span class="el-button btn-xs btn-danger" v-else-if="res.key==0" @click="ktapi(res.uid)">未开通</span></td>
		            	<td  v-if="userrow.uid === 1"><span class="btn btn-xs btn-success"  @click="ktkh(res.uid)" v-if="res.khcz!=0">已开通</span><span class="btn btn-xs btn-danger"  @click="ktkh(res.uid)" v-else-if="res.khcz!=1">未开通</span></td>
		            	<td><span class="el-button btn-xs btn-info" @click="yqm(res.uid,res.name)">{{res.yqm==''?'未设置':res.yqm}}</span></td>
		            	<td>
		            	<span @click="ban(res.uid,res.active)" class="btn btn-xs btn-success" v-if="res.active==1">正常</span>
		            	<span @click="ban(res.uid,res.active)" class="btn btn-xs btn-danger" v-else-if="res.active==0">封禁</span>
		            	</td>
		            	<!--<td>{{res.addtime}}</td>-->
		            	<td>{{res.endtime}}</td>
		                <td>
		                    <div style="display: flex; justify-content: space-between;">
                                <button class="el-button btn-sm btn-success" @click="cz(res.uid,res.name)">余额充值</button>
                                <button class="el-button btn-sm btn-info" data-toggle="modal" data-target="#modal-gjuser" @click="ddinfo(res)">修改费率</button>
                                <button class="el-button btn-sm btn-danger" @click="czmm(res.uid)">重置密码</button>
                            </div>
		                </td> 
		                <!--<td><a href="https://wpa.qq.com/msgrd?v=3&uin=<?=$sj['user'];?>&site=qqq&menu=yes" target="_blank" class="btn btn-info btn-sm" role="button"><?php echo $sj['user'];?></a></td>-->
		            </tr>
		          </tbody>
		        </table>
		      </div>
		      
			     <ul class="pagination no-border" v-if="row.last_page>1"><!--by 青卡 Vue分页 -->
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
		    </div>
		  </div>
		  
		  <div class="modal fade primary" id="modal-gjuser">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">费率修改</h4>
                    </div>
                    
                    <div class="modal-body">
                        <form class="form-horizontal"  id="form-gjuser">
                            <input type="hidden" name="uid" :value="ddinfo3.info.uid"/>
							<div class="form-group">
                            	<label class="col-sm-2 control-label">选择等级</label>
                            	<div class="col-sm-9">   
	                            <select class="layui-select" name="addprice" style="background:url('../index/arrow.png')no-repeat scroll 99%;width:100%" >
								  <option v-for="row2 in row1.data" :value="row2.rate">{{row2.name}} [费率:{{row2.rate}}]</option>
							</select>
	                            </div>
                            </div>
                         </form>
                    </div>
                    <div class="modal-footer">
                        <el-button type="danger" data-dismiss="modal">取消</el-button>
                        <el-button type="primary" @click="gj()">确定</el-button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade primary" id="modal-adduser">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">代理添加</h4>
                    </div>
           
                    <div class="modal-body">
                        <form class="form-horizontal" id="form-adduser">
	                        <div class="form-group">
								<label class="col-sm-2 control-label">代理昵称:</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="name" placeholder="昵称" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">代理账号:</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="user" placeholder="必须填代理QQ" required>
								</div>
							</div>
						     <div class="form-group">
								<label class="col-sm-2 control-label">代理密码:</label>
								<div class="col-sm-9">
									<input type="text" class="layui-input" name="pass" placeholder="请填写密码!" required>
								</div>
							</div>
							<div class="form-group">
                            	<label class="col-sm-2 control-label">选择等级</label>
                            	<div class="col-sm-9">   
	                            <select class="layui-select" name="addprice" style="background:url('../index/arrow.png')no-repeat scroll 99%;width:100%" >
								  <option v-for="row2 in row1.data" :value="row2.rate">{{row2.name}} [费率:{{row2.rate}}]</option>
							</select>
	                            </div>
                            </div>
							
                         </form>
                    </div>
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary" @click="adduser()">确定</button>-->
                        <el-button type="danger" data-dismiss="modal">取消</el-button>
                        <el-button type="primary" @click="adduser()">确定</el-button>
                    </div>
                </div>
            </div>
        </div>
  
        <div class="modal fade primary" id="modal-czgz">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">充值规则</h4>
                    </div>
           
                    <div class="modal-body" style="font-weight: bold; font-size: 16px;">
    <b>你旗下代理充值的时候页面将显示你的QQ</b><br>  	
    <b>充值扣费：</b>扣除费用=充值金额*(我的总价格/代理价格)<br>
    <b>改价扣费：</b>改价一次需要1元手续费，且代理余额会按照比例做相应调整<br>
    <b>费率价格：</b>代理费率必须大于或等于你的费率，比如你的费率0.2，则代理费率可以是0.2,0.3，0.4等等                                                                                                                
</div>

                    
                    <div class="modal-footer">
                        <el-button type="danger" class="layui-btn layui-btn-danger" data-dismiss="modal">取消</el-button>
                        <el-button type="primary" class="layui-btn" data-dismiss="modal" >确定</el-button>
                    </div>
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
<script src="assets/layui/layui.js"></script>
<script src="assets/js/element.js"></script>
<link href="assets/FD/css/element.css" rel="stylesheet">
<link href="assets/FD/LightYear/css/style.min.css" rel="stylesheet">

<script>
var vm=new Vue({
	el:"#userlist",
	data:{
		row:null,
		type:'1',
		qq:'',
		userrow: userrow,
		addprice:'',
		ddinfo3:{
		  	status:false,
		  	info:[]
		  },
		row1:'',
		storeInfo:{}
	},
	methods:{
		get: function (page) {
  var load = layer.load(2);
  // 判断当前用户是否为管理员（假设UID为1的用户是管理员）
  if (this.userrow.uid === 1) {
    // 如果是管理员，使用管理员接口
    this.$http.post("/apisub.php?act=FDadmin_userlist", { type: this.type, qq: this.qq, page: page }, { emulateJSON: true }).then(function (data) {
      layer.close(load);
      if (data.data.code === 1) {
        this.row = data.body;
      } else {
        layer.msg(data.data.msg, { icon: 2 });
      }
    });
  } else {
    // 如果不是管理员，使用普通用户接口
    this.$http.post("/apisub.php?act=userlist", { type: this.type, qq: this.qq, page: page }, { emulateJSON: true }).then(function (data) {
      layer.close(load);
      if (data.data.code === 1) {
        this.row = data.body;
      } else {
        layer.msg(data.data.msg, { icon: 2 });
      }
    });
  }
},
		dj:function(){
		  var load=layer.load(2);
 			this.$http.post("/apisub.php?act=adddjlist",{emulateJSON:true}).then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){			                     	
	          		this.row1=data.body;			             			                     
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
		},
		form:function(form){
		   var load=layer.load(2);
 			this.$http.post("/apisub.php?act="+form,{data:$("#form-"+form).serialize()},{emulateJSON:true}).then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){			                     		          		
	          		this.get(this.row.current_page);
	          		$("#modal-" + form).modal('hide');
	          		layer.msg(data.data.msg,{icon:1});	             			                     
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
		},ddinfo: function(a){  
      	    this.ddinfo3.info=a;
      },
		adduser:function(){
	           var load=layer.load(2);
               $.post("/apisub.php?act=adduser",{data:$("#form-adduser").serialize()},function (data) {
		 	     layer.close(load);
	             if (data.code==1){    				
					layer.confirm(data.msg, {
					  btn: ['确定开通','取消'],title:'开通扣费'  //按钮
					}, function(){
					    var load=layer.load(2);
			 			vm.$http.post("/apisub.php?act=adduser",{data:$("#form-adduser").serialize(),type:1},{emulateJSON:true}).then(function(data){	
				          	layer.close(load);
				          	if(data.data.code==1){
				          	    vm.get(this.row.current_page);
	          		            $("#modal-adduser").modal('hide');			                     		          		
				          		layer.alert(data.data.msg,{icon:1});	             			                     
				          	}else{
				                layer.msg(data.data.msg,{icon:2});
				          	}
				        });	   
					}, function(){

					});					  									            
	             }else{
	                layer.msg(data.msg,{icon:2});
	             }	              
            });              
		},
		cz: function(uid, name){
    layer.prompt({
        title: '你将为<font color="red">' + name + '</font>充值，请输入充值金额', 
        formType: 3
    }, function(money, index){
        layer.close(index);

        // 向服务器发送请求以获取预计扣费金额
        var load = layer.load(2);
        $.post("/apisub.php?act=calc_kochu", {uid: uid, money: money}, function(calcData){
            layer.close(load);

            if(calcData.code == 1){
                var kochu = calcData.kochu;
                var confirmMsg = '你将为用户' + name + '充值 ' + money + ' 币。\n' +
                                 '预计扣除金额: ' + kochu + '币。\n' +
                                 '是否继续？';
                layer.confirm(confirmMsg, {icon: 3, title:'确认充值'}, function(confirmIndex){
                    layer.close(confirmIndex);

                    // 用户确认后，执行实际的充值操作
                    var loadConfirm = layer.load(2);
                    $.post("/apisub.php?act=userjk", {uid: uid, money: money}, function(data){
                        layer.close(loadConfirm);
                        if(data.code == 1){
                            vm.get(vm.row.current_page);
                            layer.alert(data.msg, {icon: 1});
                        } else {
                            layer.msg(data.msg, {icon: 2});
                        }
                    }, "json"); // 确保数据以 JSON 格式解析
                });
            } else {
                layer.msg(calcData.msg, {icon: 2});
            }
        }, "json");
    });
},
        khcz: function(){
    var _this = this;
    layer.prompt({
        title: '请输入用户UID', 
        formType: 3
    }, function(uid, index){
        layer.close(index);
        layer.prompt({
            title: '请输入充值金额', 
            formType: 3
        }, function(money, index2){
            layer.close(index2);

            // 向服务器发送请求以获取预计扣费金额
            var load = layer.load(2);
            $.post("/apisub.php?act=calc_kochu", {uid: uid, money: money}, function(calcData){
                layer.close(load);

                if(calcData.code == 1){
                    var kochu = calcData.kochu;
                    var confirmMsg = '你将为用户UID为' + uid + '的用户跨户充值 ' + money + ' 币。\n' +
                                     '预计扣除金额: ' + kochu + '币。\n' +
                                     '是否继续？';
                    layer.confirm(confirmMsg, {icon: 3, title:'确认充值'}, function(confirmIndex){
                        layer.close(confirmIndex);

                        // 用户确认后，执行实际的充值操作
                        var loadConfirm = layer.load(2);
                        $.post("/apisub.php?act=khcz", {uid: uid, money: money}, function(data){
                            layer.close(loadConfirm);
                            if(data.code == 1){
                                _this.get(_this.row.current_page);
                                layer.alert(data.msg, {icon: 1});
                            } else {
                                layer.msg(data.msg, {icon: 2});
                            }
                        }, "json"); // 确保数据以 JSON 格式解析
                    });
                } else {
                    layer.msg(calcData.msg, {icon: 2});
                }
            }, "json");
        });
    });
},

		gj:function(uid,name){
	           var load=layer.load(2);
               $.post("/apisub.php?act=usergj",{data:$("#form-gjuser").serialize()},function (data) {
		 	     layer.close(load);
	             if (data.code==1){    				
					layer.confirm(data.msg, {
					  btn: ['确定改价并充值','取消'],title:'改价扣费'  //按钮
					}, function(){
					    var load=layer.load(2);
			 			vm.$http.post("/apisub.php?act=usergj",{data:$("#form-gjuser").serialize(),type:1},{emulateJSON:true}).then(function(data){	
				          	layer.close(load);
				          	if(data.data.code==1){
				          	    vm.get(vm.row.current_page);		                     		          		
				          		layer.alert(data.data.msg,{icon:1});	             			                     
				          	}else{
				                layer.msg(data.data.msg,{icon:2});
				          	}
				        });	   
					}, function(){

					});					  									            
	             }else{
	                layer.msg(data.msg,{icon:2});
	             }	              
             });              
		},
		czmm:function(uid){
		    var load=layer.load(2);
 			vm.$http.post("/apisub.php?act=user_czmm",{uid:uid},{emulateJSON:true}).then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){			                     		          		
	          		layer.alert(data.data.msg,{icon:1});	             			                     
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	              	 
		},ktapi:function(uid){
			layer.confirm('给下级开通API，将扣除5元余额', {title:'温馨提示',icon:1,
				  btn: ['确定','取消'] //按钮
				}, function(){
				  		var load=layer.load(2);
		     			axios.get("/apisub.php?act=ktapi&type=2&uid="+uid)
				          .then(function(data){	
				          	   	layer.close(load);
				          	if(data.data.code==1){
				          		vm.get(vm.row.current_page);		                     	
		                        layer.alert(data.data.msg,{icon:1,title:"温馨提示"});							          				             			                     
				          	}else{
				                layer.msg(data.data.msg,{icon:2});
				          	}
				        });	
				
			    });
		},
		ktkh:function(uid){
			layer.confirm('确定切换用户的跨户充值权限吗？', {title:'温馨提示',icon:1,
				  btn: ['确定','取消'] //按钮
				}, function(){
				  		var load=layer.load(2);
		     			axios.get("/apisub.php?act=ktkh&uid="+uid)
				          .then(function(data){	
				          	   	layer.close(load);
				          	if(data.data.code==1){
				          		vm.get(vm.row.current_page);		                     	
		                        layer.alert(data.data.msg,{icon:1,title:"温馨提示"});							          				             			                     
				          	}else{
				                layer.msg(data.data.msg,{icon:2});
				          	}
				        });	
				
			    });
		},
		yqm:function(uid,name){
			layer.prompt({title: '你将为<font color="red">'+name+'</font>设置邀请码，邀请码最低4位数', formType: 3}, function(yqm, index){
			  layer.close(index);
	           var load=layer.load(2);
               $.post("/apisub.php?act=szyqm",{uid,yqm},function (data) {
		 	     layer.close(load);
	             if (data.code==1){
	             	vm.get(vm.row.current_page);		             	 
	                layer.alert(data.msg,{icon:1});	                
	             }else{
	                layer.msg(data.msg,{icon:2});
	             }	              
              });            
		  });		
		},ban:function(uid,active){
			var load=layer.load(2);
               $.post("/apisub.php?act=user_ban",{uid,active},function (data) {
		 	     layer.close(load);
	             if (data.code==1){
	             	vm.get(vm.row.current_page);		             	 
	                layer.msg(data.msg,{icon:1});	                
	             }else{
	                layer.msg(data.msg,{icon:2});
	             }	              
              });  
		}  
	},
	mounted(){
		this.get(1);
		this.dj();
	}
});
</script>