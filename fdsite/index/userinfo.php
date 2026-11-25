<?php
include('head.php');
include('../confing/order.php');
?>
<div id="userindex">
<div class="container-fluid p-t-15">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
           <div class="panel-heading font-bold ">个人资料</div>
        <div class="card-body">
          <div class="edit-avatar">
            <img src="favicon.ico" alt="..." class="img-avatar">
            <div class="avatar-divider"></div>
            <div class="clear">
			                  <div v-if="row.name==''" class="h5 m-t-xs"><?=$conf['sitename'];?></div>								
			                  <div v-else class="h5 m-t-xs">{{row.name}}</div>							  
			                  <div class="text-muted">
							    <span style="color:red;">UID: {{row.uid}}</span>（{{row.user}}）<br> 	
							    <span style="color:green">KEY:</span>&nbsp;<span v-if="row.key==0">未开通API接口&nbsp;<button @click="ktapi" class="btn btn-xs btn-success">开通KEY</button>&nbsp;<button @click="szyqprice" class="btn btn-xs btn-success">设置邀请费率</button></span><span v-else="">{{row.key}}&nbsp;<button @click="ghapi" class="btn btn-xs btn-info">更换KEY</button>&nbsp;<button @click="gbapi" class="btn btn-xs btn-warning">关闭KEY</button>&nbsp;<button @click="szyqprice" class="btn btn-xs btn-primary">设置邀请费率</button></span>&nbsp;<button @click="szgg" class="btn btn-xs btn-primary">设置下级公告</button>&nbsp;<button @click="szyx(row.email,row.wxpushertoken)" class="btn btn-xs btn-primary">设置推送</button>
							  </div>						  
			                </div>
          </div>
          <hr>
          <form class="site-form">
                  <div class="form-group">
                  <label>UID</label>
                  <input type="text" class="form-control" :value="row.uid" disabled="disabled" />
                </div>
                <div class="form-group">
                  <label>用户名</label>
                  <input type="text" class="form-control" :value="row.name" disabled="disabled" />
                </div>
                <div class="form-group">
                  <label>账号</label>
                  <input type="text" class="form-control" :value="row.user" disabled="disabled" />
                </div>
                <div class="form-group">
                  <label>剩余积分</label>
                  <input type="text" class="form-control" :value="row.money" disabled="disabled" />
                </div>
                <div class="form-group">
                  <label>长安专用积分</label>
                  <input type="text" class="form-control" :value="row.cdmoney" disabled="disabled" />
                </div>
                <div class="form-group">
                  <label>总充值</label>
                  <input type="text" class="form-control" :value="row.zcz" disabled="disabled" />
                </div>
                <div class="form-group">
                  <label>费率</label>
                  <input type="text" class="form-control" :value="row.addprice" disabled="disabled" />
                </div>
                <div class="form-group">
                  <label>邀请码</label>
                  <input type="text" class="form-control" :value="row.yqm==''?'无':row.yqm" disabled="disabled" />
                </div>
                <div class="form-group">
                  <label>邀请费率</label>
                  <input type="text" class="form-control" :value="row.yqprice==''?'无':row.yqprice" disabled="disabled" />
                </div>
                <div class="form-group">
                    <label>邮箱</label>
                    <input type="text" class="form-control" :value="row.email" disabled="disabled" />
                </div>
                <div class="form-group">
                    <label>推送Token</label>
                    <input type="text" class="form-control" :value="row.wxpushertoken" disabled="disabled" />
                </div>
                <!--<div class="form-group">
                  <label>QQ登陆</label>
                  <input type="text" class="form-control" :value="row.qq_openid==''?'未绑定':qq_openid" disabled="disabled" />
                </div>-->
          </form>
        </div>
      </div>
    </div>
    
  </div>
  </div>
</div>
<script src="js/vue.min.js"></script>
<script src="js/vue-resource.min.js"></script>
<script src="assets/js/axios.min.js"></script>
<script src="assets/js/aes.js"></script>
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
</body>
</html>
<script type="text/javascript">

		    var vm=new Vue({
		     	el: "#userindex",
		    	data: {
		      		row:null,
		      		inte:'',
		        },
		      	methods:{
		    		userinfo:function(){
		    			var load=layer.load(2);
		     			this.$http.post("/apisub.php?act=userinfo")
				          .then(function(data){	
				          	   	layer.close(load);
				          	if(data.data.code==1){			                     	
				          		this.row=data.data			             			                     
				          	}else{
				                layer.alert(data.data.msg,{icon:2});
				          	}
				          });	
		    		},
		    		yecz:function(){
		    			layer.alert('请联系您的上级QQ：'+this.row.sjuser+'，进行充值。（下级点充值，此处将显示您的QQ）',{icon:1,title:"温馨提示"});
		    		},
		    		ktapi:function(){
		    			layer.confirm('后台剩余积分满300积分可免费开通，反之需花费10积分开通', {title:'温馨提示',icon:1,
							  btn: ['确定','取消'] //按钮
							}, function(){
							  		var load=layer.load(2);
					     			axios.get("/apisub.php?act=ktapi&type=1")
							          .then(function(data){	
							          	   	layer.close(load);
							          	if(data.data.code==1){			                     	
	    			                        layer.alert(data.data.msg,{icon:1,title:"温馨提示"},function(){setTimeout(function(){window.location.href=""});});							          				             			                     
							          	}else{
							                layer.msg(data.data.msg,{icon:2});
							          	}
							        });	
							
						    });
		    	    },ghapi:function(){
		    			layer.confirm('确定更换key吗，更换之后原有的key将失效！', {title:'温馨提示',icon:1,
							  btn: ['确定','取消'] //按钮
							}, function(){
							  		var load=layer.load(2);
					     			axios.get("/apisub.php?act=ktapi&type=3")
							          .then(function(data){	
							          	   	layer.close(load);
							          	if(data.data.code==1){			                     	
	    			                        layer.alert(data.data.msg,{icon:1,title:"温馨提示"},function(){setTimeout(function(){window.location.href=""});});							          				             			                     
							          	}else{
							                layer.msg(data.data.msg,{icon:2});
							          	}
							        });	
							
						    });
		    	    },gbapi:function(){
		    			layer.confirm('确定关闭key吗，关闭之后无法使用对接功能！', {title:'温馨提示',icon:1,
							  btn: ['确定','取消'] //按钮
							}, function(){
							  		var load=layer.load(2);
					     			axios.get("/apisub.php?act=ktapi&type=4")
							          .then(function(data){	
							          	   	layer.close(load);
							          	if(data.data.code==1){			                     	
	    			                        layer.alert(data.data.msg,{icon:1,title:"温馨提示"},function(){setTimeout(function(){window.location.href=""});});							          				             			                     
							          	}else{
							                layer.msg(data.data.msg,{icon:2});
							          	}
							        });	
							
						    });
		    	    },
		    	    szyqprice:function(){		    	    	
						layer.prompt({title: '设置下级默认费率，首次自动生成邀请码', formType: 3}, function(yqprice, index){
						  layer.close(index);
						  var load=layer.load(2);
			              $.post("/apisub.php?act=yqprice",{yqprice},function (data) {
					 	     layer.close(load);
				             if (data.code==1){
				             	vm.userinfo();  
				                layer.alert(data.msg,{icon:1});
				             }else{
				                layer.msg(data.msg,{icon:2});
				             }
			              });		    		    
					  });
		    	    },
		    	    connect_qq:function(){
		    	    	    var ii = layer.load(0, {
		    	    	        shade: [0.1, '#fff']
		    	    	    });
		    	    	    $.ajax({
		    	    	        type: "POST",
		    	    	        url: "../qq_login.php",
		    	    	        data: {"type":'qq'},
		    	    	        dataType: 'json',
		    	    	        success: function(data) {
		    	    	            layer.close(ii);
		    	    	            if (data.code == 1) {
		    	    	                window.location.href = data.url;
		    	    	            } else {
		    	    	                layer.alert(data.msg, {
		    	    	                    icon: 7
		    	    	                });
		    	    	            }
		    	    	        }
		    	    	    });
		    	  },szgg:function(){
		    	  		layer.prompt({title: '设置代理公告，您的代理可看到', formType: 2}, function(notice, index){
						  layer.close(index);
						  var load=layer.load(2);
			              $.post("/apisub.php?act=user_notice",{notice},function (data) {
					 	     layer.close(load);
				             if (data.code==1){
				             	vm.userinfo();  
				                layer.msg(data.msg,{icon:1});
				             }else{
				                layer.msg(data.msg,{icon:2});
				             }
			              });		    		    
					  });   	  	
		    	  },szyx: function(existingEmail, existingToken) {
                        // 创建一个自定义的HTML结构，用于输入邮箱和token，填入已有的值
                        var content = `
                            <div style="font-family: Arial, sans-serif; line-height: 1.5;">
                                <div style="margin-bottom: 15px;">
                                    <label for="email" style="display: block; font-weight: bold;">邮箱:</label>
                                    <input type="email" id="email" placeholder="请输入邮箱" value="${existingEmail}" 
                                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); transition: border 0.3s;">
                                </div>
                                <div>
                                    <label for="token" style="display: block; font-weight: bold;">推送Token:</label>
                                    <input type="text" id="token" placeholder="请输入推送Token" value="${existingToken}" 
                                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); transition: border 0.3s;">
                                </div>
                                <div style="margin-top: 5px;">
                                    <button style="background-color: #f56c6c; color: white; border: none; border-radius: 4px; 
                                                   padding: 5px 8px; cursor: pointer; font-size: 12px; transition: background-color 0.3s;" 
                                            onclick="window.open('https://wxpusher.zjiecode.com/api/qrcode/RwjGLMOPTYp35zSYQr0HxbCPrV9eU0wKVBXU1D5VVtya0cQXEJWPjqBdW3gKLifS.jpg', '_blank')">
                                        获取 Token
                                    </button>
                                </div>

                            </div>
                        `;
                    
                        // 显示自定义输入框
                        layer.open({
                            title: '设置邮箱和Token',
                            content: content,
                            area: ['400px', '320px'], // 调整高度以容纳按钮
                            btn: ['确定'],
                            yes: function(index, layero) {
                                // 获取输入框的值
                                var email = layero.find('#email').val();
                                var token = layero.find('#token').val();
                    
                                layer.close(index);
                                var load = layer.load(2);
                    
                                // 发送POST请求，包含邮箱和token
                                $.post("/apisub.php?act=user_email", { email: email, tuisongtoken: token }, function(data) {
                                    layer.close(load);
                                    if (data.code == 1) {
                                        vm.userinfo();
                                        layer.msg(data.msg, { icon: 1 });
                                    } else {
                                        layer.msg(data.msg, { icon: 2 });
                                    }
                                });
                            }
                        });
                    }

		    	
		     	},
		     	mounted(){
		     		this.userinfo();
		     		
		     	}
		      });
		  
       </script>
