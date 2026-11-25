<?php
include('head.php');
$uid=$userrow['uid'];
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订单页面</title>
    <link rel="stylesheet" href="../sxdk/element/index.css">
    <script src="./dk/jquery.js"></script>
    <script src="./dk/vue.js"></script>
    <script src="./dk/vue-resource.min.js"></script>
    <script src="../sxdk/element/index.js"></script>
    <script src="layer/3.1.1/layer.js"></script>
</head>
     <div class="app-content-body ">
        <div class="wrapper-md control" id="charge">
        	
				          <div class="card" class="">
				             <div class="card-header">	
				             在线充值
				             </div>	
				             
				            <li class="list-group-item">
				           <div class="edit-avatar">
                  
                  <div class="avatar-divider"></div>
                  <div class="edit-avatar-content">
                       <div class="h5 m-t-xs"><?=$conf['sitename'];?> 欢迎您！</div>
                    <span style="color:red;">账号: <?=$userrow['user'];?></span><br> 	
							    <span style="color:green">余额:<?=$userrow['money'];?></span>		
                  </div>
                </div>			              
			              
				            </li>   		          
				          	
				             <div class="card-body">
						      <div class="form-group" style="overflow:hidden">									
				                    <input style="width: 150px; float: left;" type="text" class="layui-input" placeholder="请输入充值金额" v-model="money"/><button style="float: left;margin-left: 5px; background-color: #00CED1; color: #fff;" class="layui-btn" @click="pay">立即充值</button>	
				                    
							  </div>
							  
				                  <span style="color:red;"> 
				                  充值规则（单次充值）：<br />
				                  50以上即送10%金额<br />
				                  100以上送15%金额<br />
				                  200以上送20%金额<br />
				                  300以上送25%金额<br />
				                  500以上赠送30%金额<br />
				                  密接用户可联系上级享受专属优惠！<br />
				                  <br />赠送金额直接到账！<br />
				                  </span><br>
				             </div>		          
				              <!--<div class="card-body">
						      <div class="form-group" style="overflow:hidden">									
				                        <input style="width: 150px; float: left;" type="text" class="layui-input" placeholder="请输入充值卡密" v-model="km"/>
				                    <button style="float: left;margin-left: 5px; background-color: #00CED1; color: #fff;" class="layui-btn" @click="paycard">立即充值</button>						
				                    
							  </div>
							   1.正常情况下请联系上家进行充值。</b><br />
					        	2.充值卡不定时作为福利发放。</b><br />
					        	3.一切安好！ </b><br />
				               </div>		          
			
				             <div class="card-header">充值卡查询</div>	
				             <div class="card-body">
				                 
							  
							  <div class="form-group" style="overflow:hidden">									
				                    <input style="width: 150px; float: left;" type="text" class="layui-input" placeholder="请输入查询卡密" v-model="querykm"/>
				                    <button style="float: left;margin-left: 5px; background-color: #00CED1; color: #fff;" class="layui-btn" @click="querycard">立即查询</button>							
							  </div>
           
            
                  <p>充值卡号：{{kminfo.content}}<p>
                  <p>卡密金额：&yen; {{kminfo.money}}<p>
                  <p>卡密状态：<font :class="{'text-success':kminfo.status == 0,'text-danger':kminfo.status == 1}">{{kminfo.status == 1 ?"已使用":"未使用"}}</font><p>
                </div>-->
				    </div>       
	   <div class="col-sm-12" id="pay2" style="display: none;">
    	<form action="/epay/epay.php" method="post" >
    		<div><center style="margin-top:15px;"><h3>￥{{money}}</h3></center></div>
    		
    		<input type="hidden" name="out_trade_no" v-model="out_trade_no"/><br>
    		 <?php if($conf['is_alipay']==1){ ?>
			<button type="radio" name="type" value="alipay" class="btn btn-primary btn-block" >支付宝</button><br> <?php } ?>
			 <?php if($conf['is_qqpay']==1){ ?>
			<button type="radio" name="type" value="qqpay" class="btn btn-danger btn-block">QQ</button><br> <?php } ?>
			 <?php if($conf['is_wxpay']==1){ ?>
			<button type="radio" name="type" value="wxpay" class="btn btn-info btn-block">微信</button><br> <?php } ?>
		</form>	
    </div>   
				    

 

	
		          <div class="panel panel-default">   
		           <div class="card-header">	
				             成功订单
				             </div>	
         		      <div class="table-responsive"> 
				         <table class="table table-striped">
				          <thead>
				              <tr>
				                  <th>ID</th>
				                  <th>订单号</th>
				                  <th>类型</th>
				                  <th>用户UID</th>
				                  <th>名称</th>
				                  <th>金额</th>
				                  <th>创建时间</th>
				                  <th>支付时间</th>
				                  <th>状态</th>
				              </tr>
				              </thead>
				          <tbody>				          	
							<?php
							if($uid!=1){
	                     	 $a=$DB->query("select * from qingka_wangke_pay where uid='$uid' and status='1' ");
							}else{
	                     	 $a=$DB->query("select * from qingka_wangke_pay where status='1' ");
							}
	                     	 while($rs=$DB->fetch($a)){
	                     	     if ($rs['status'] == 1) {
	                     	         $zt = '<span class="badge bg-success">已支付</span>';
	                     	     }else{
	                     	         $zt = '<span class="badge bg-danger">未支付</span>';
	                     	     }
	                     	 	  echo "<tr><td>".$rs['oid']."</td>
	                     	 	  	<td>".$rs['out_trade_no']."</td>
	                     	 	  	<td>".$rs['type']."</td>
	                     	 	  	<td>".$rs['uid']."</td>
	                     	 	  	<td>".$rs['name']."</td>
	                     	 	  	<td>".$rs['money']."</td>
	                     	 	  	<td>".$rs['addtime']."</td>
	                     	 	  	<td>".$rs['endtime']."</td>
	                     	 	  	<td>".$zt."</td>
	                     	 	  	</tr>"; 
	                     	 }
	                     	?>		           
				          </tbody>
				        </table>
				      </div>
		          </div>		
		        </div>
				
     </div>

<script>layer.alert('赠送最高改为30%，扫码支付时务必支付和显示同样的余额，支付后务必在二维码页面停留等待页面自动跳转！如果未跳转就关闭会导致充值不到账！');</script>
<script type="text/javascript" src="assets/LightYear/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>
<script src="assets/js/vue.min.js"></script>
<script src="assets/js/vue-resource.min.js"></script>
<script src="assets/js/axios.min.js"></script>
<script>
const chargeVm = new Vue({
  el: '#charge',
  data: {
    money: '',
    km: '',
    querykm: '',
    kminfo: {
      content: '请先查询',
      money: '0',
      status: '',
    },
    out_trade_no: '',
  },
  methods: {
  pay: function () {
  var load = layer.load(2);
  this.$http
    .post(
      '/apisub.php?act=pay',
      { money: this.money },
      { emulateJSON: true }
    )
    .then(function (data) {
      layer.close(load);
      if (data.data.code == 1) {
        $('pay2').show();
        this.out_trade_no = data.data.out_trade_no;
        layer.msg(data.data.msg, { icon: 1, time: 2000, anim: 0 });
        layer.open({
          type: 1,
          title: '请选择支付方式',
          closeBtn: 0,
          area: ['250px', '300px'],
          skin: 'layui-bg-gray', //没有背景色
          shadeClose: true,
          content: $('#pay2'),
          end: function () {
            $('#pay2').hide();
          },
        });
      } else {
        layer.msg(data.data.msg, { icon: 2, time: 2000, anim: 6 });
      }
    })
},
 paycard: function () {
  var load = layer.load(2)
  this.$http
    .post('/km.php?act=paykm', { content: this.km }, { emulateJSON: true })
    .then(function (data) {
      layer.close(load)
      if (data.data.code == -1) {  // 修改code为-1
        layer.msg(data.data.msg, { icon: 2, time: 2000, shift: 3 }) // 使用 layun 提示框
      } else {
        layer.msg(data.data.msg, { icon: 1, time: 2000, shift: 1 }) // 使用 layun 提示框
        setTimeout(function () {
          location.reload()
        }, 1000)
      }
    })
},
   querycard: function () {
  var load = layer.load(2)
  this.$http
    .post(
      '/km.php?act=querykm',
      { content: this.querykm },
      { emulateJSON: true }
    )
    .then(function (data) {
      layer.close(load)
      if (data.data.code == 1) {
        this.kminfo = data.data.data[0]
        layer.msg(data.data.msg, { icon: 1, time: 2000, anim: 3 })
      } else {
        layer.msg(data.data.msg, { icon: 2, time: 2000, anim: 1 })
      }
    })
},
    szgg: function () {
      layer.prompt(
        { title: '设置代理公告，您的代理可看到', formType: 2 },
        function (notice, index) {
          layer.close(index)
          var load = layer.load(2)
          $.post('/apisub.php?act=user_notice', { notice }, function (data) {
            layer.close(load)
            if (data.code == 1) {
              iziToast.success({
                title: data.msg,
                position: 'topRight',
              })
              window.location.href = ''
            } else {
              iziToast.error({
                title: data.msg,
                position: 'topRight',
              })
            }
          })
        }
      )
    },
  },
  mounted() {
    $('.preloader').fadeOut('slow')
  },
})

</script>



