<?php
include('head.php');
$dd=$DB->count("select count(oid) from qingka_wangke_order where uid='{$userrow['uid']}' ");

?>
	<div class="container-fluid" id="userindex">
     	<div class="wrapper-md control" >
			<div class="row">
			    <div class="layui-row layui-col-space10">
          <div class="tab-content">
            <div class="tab-pane fade active in" id="wdxx">
		            <li class="list-group-item">						
			              <div class="clearfix">
			                <a class="pull-left thumb-md avatar b-3x m-r">
			                  <img src="http://q2.qlogo.cn/headimg_dl?dst_uin=<?=$userrow['user'];?>&spec=100" class="img-circle" alt="User Image" />
			                </a>							
			                <div class="clear">
			                  <div v-if="row.nickname==''" class="h5 m-t-xs"><?=$conf['sitename'];?></div>
			                  <div v-else class="h5 m-t-xs">{{row.nickname}}</div>							  
			                  <div class="text-muted">
							    <span style="color:red;">UID: {{row.uid}}</span>（{{row.user}}）<br> 	
							    <span style="color:green">KEY:</span>&nbsp;<span v-if="row.key==0">未开通API接口&nbsp;&nbsp;<button @click="ktapi" class="btn btn-xs btn-success" style="border-radius: 5px;">开通KEY</button></span><span v-else="">{{row.key}}&nbsp;&nbsp;<button @click="ghapi" class="btn btn-xs btn-success" style="border-radius: 5px;">更换KEY</button></span>
							  </div>						  
			                </div>			              
			        </div>
			        </li>
			         <li class="list-group-item"> 账户余额<a href="#!" class="js-create-tab badge btn-primary" data-title="在线充值" data-url="pay">充值</a></span><span class="badge bg-success">&yen;{{row.money}}</span></li>
 			         <li class="list-group-item"> 我的订单<span class="badge bg-primary">{{row.dd}}</span>  </li>
   			         <li class="list-group-item"> 我的费率<a href="#!" class="js-create-tab badge bg-info" data-title="价格列表" data-url="myprice">查看</a>  <span class="badge bg-info">{{row.addprice}}</span></span></li>
   			         <li class="list-group-item"> 总充值<span class="badge bg-danger">&yen;{{row.zcz==null?'0':row.zcz}}</span></li>
   			         <li class="list-group-item"  > 我的费率<span class="badge bg-warning" @click="szyqprice">设置</span><span class="badge bg-warning">邀请费率：{{row.yqprice==''?'无':row.yqprice}} </li>
   			         <li class="list-group-item"  > 我的邀请码<span class="badge bg-warning">邀请码：{{row.yqm==''?'无':row.yqm}}</span> </li>
		             
		             <!--<li class="list-group-item" href="">
				               	<span v-if="row.qq_openid==''" class="badge bg-success" @click="connect_qq">立即绑定</span>
				             	<span v-else class="badge bg-danger" @click="layer.alert('为了账号安全，一经绑定不允许解绑，需要解绑请联系管理员',{icon:3})">解绑</span>
			                    <img src="https://qzonestyle.gtimg.cn/qzone/vas/opensns/res/img/bt_blue_24X24.png">QQ绑定
			         </li>-->
			         <br />
            </div>
            <div class="tab-pane fade" id="sjtj">
                <li class="list-group-item"  >今日订单：<span class="badge bg-success">{{row.dailitongji.jrjd}}单</span> </li>
                <li class="list-group-item"  >全部订单：<span class="badge bg-success">{{row.dd}}单</span> </li>
                <li class="list-group-item"  >新增代理：<span class="badge bg-success">{{row.dailitongji.dlzc}}人</span> </li>
                <li class="list-group-item"  >代理总数：<span class="badge bg-success">{{row.dailitongji.dlzs}}人</span> </li>
                <li class="list-group-item"  >今日上线：<span class="badge bg-success">{{row.dailitongji.dldl}}人</span> </li>
                <li class="list-group-item"  >进行中订单：<span class="badge bg-success"><?php echo $a=$DB->count("select count(*) from qingka_wangke_order where status!='已完成' and uid='{$userrow['uid']}'  ");?>单</span> </li>
                <br />
            </div>
            <div class="tab-pane fade" id="ptsj">
                <li class="list-group-item"  >今日订单：<span class="badge bg-success"><?php echo $a=$DB->count("select count(*) from qingka_wangke_order where addtime>'$jtdate'  ");?>单</span> </li>
                <li class="list-group-item"  >全部订单：<span class="badge bg-success"><?php echo $a=$DB->count("select count(*) from qingka_wangke_order");?>单</span> </li>
                <li class="list-group-item"  >新增代理：<span class="badge bg-success"><?php echo $a=$DB->count("select count(*) from qingka_wangke_user where addtime>'$jtdate'  ");?>人</span> </li>
                <li class="list-group-item"  >代理总数：<span class="badge bg-success"><?php echo $a=$DB->count("select count(*) from qingka_wangke_user ");?>人</span> </li>
                <li class="list-group-item"  >今日上线：<span class="badge bg-success"><?php echo $a=$DB->count("select count(*) from qingka_wangke_user where endtime>'$jtdate'");?>人</span> </li>
                <br />
            </div>
            <div class="tab-pane fade" id="jglb">
                <div class="layui-table  table-responsive table-condensed" lay-size="sm" >	
                <table class="table">
				          <thead><tr><th>ID</th><th>平台名称</th><th>0.2</th><th>0.3</th><th>0.4</th><th>0.5</th><th>0.6</th><th>0.8</th><th>我的</th></thead>
				          <tbody>				          	
							<?php 
	                     	 $a=$DB->query("select * from qingka_wangke_class where status=1 ");
	                     	 while($rs=$DB->fetch($a)){
	                     	 	  echo "<tr><td>".$rs['cid']."</td>
	                     	 	  	<td>".$rs['name']."</td>
	                     	 	  	<td>".round($rs['price']*0.267,2)."</td>
	                     	 	  	<td>".round($rs['price']*0.3,2)."</td>
	                     	 	  	<td>".round($rs['price']*0.4,2)."</td>
	                     	 	  	<td>".round($rs['price']*0.5,2)."</td>
	                     	 	  	<td>".round($rs['price']*0.6,2)."</td>
	                     	 	  	<td>".round($rs['price']*0.8,2)."</td>
	                     	 	  	<td>".($rs['price']*$userrow['addprice'])."</td>
	                     	 	  	</tr>"; 
	                     	 }
	                     	?>		           
				          </tbody>
				        </table>
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
<script type="text/javascript" src="assets/LightYear/js/Chart.js"></script>
<script src="assets/js/aes.js"></script>
<script src="https://cdn.staticfile.org/vue/2.6.11/vue.min.js"></script>
<script src="https://cdn.staticfile.org/vue-resource/1.5.1/vue-resource.min.js"></script>
<script src="https://cdn.staticfile.org/axios/0.18.0/axios.min.js"></script>
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
		    			layer.confirm('后台余额满300元可免费开通，反之需花费10元开通', {title:'温馨提示',icon:1,
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
		    	    },
		    	    ghapi:function(){
		    			layer.confirm('确定更换key吗，更换之后之前的就不能用了', {title:'温馨提示',icon:1,
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
		    	  }
		    	
		     	},
		     	mounted(){
		     		this.userinfo();
		     		
		     	}
		      });
       </script>

