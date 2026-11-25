<?php
include('head.php');
 $a=$DB->get_row("select uid,user,yqm,endtime from qingka_wangke_user where uid='{$userrow['uuid']}' ");
?>
     <div class="app-content-body ">
        <div class="wrapper-md control" id="charge">
        	
				          <div class="card" class="">
				             <div class="card-header">	
				             	充值方法
				             </div>	
				             
				            <li class="list-group-item">				            	
				            	非直属用户请联系您的上级进行充值！<br />
								上级联系方式（QQ/微信）：<b style="color: red;"><?php echo $a['user']?></b> <br />
								上级邀请码：<b style="color: blue;"><?php echo $a['yqm']?></b> <br />
								上级最后在线时间：<b style="color: blue;"><?php echo $a['endtime']?></b> <br />
				            </li>   		          
				          </div>
				          
				          
	<div class="col-sm-12" id="pay2" style="display: none;">
    	<form action="/epay/epay.php" method="post">
    		<div><center style="margin-top:15px;"><h3>￥{{money}}</h3></center></div>
    		<input type="hidden" name="out_trade_no" v-model="out_trade_no"/><br>
			<button type="radio" name="type" value="alipay" class="btn btn-primary btn-block" >支付宝</button><br>
			<button type="radio" name="type" value="qqpay" class="btn btn-danger btn-block">QQ</button><br>
			<button type="radio" name="type" value="wxpay" class="btn btn-info btn-block">微信</button><br>
		</form>	
    </div>   
				        <div style="margin-top: 20px;">
				          <div class="card" class="">
				             <div class="card-header">	
				             	我设置的公告&nbsp;<button class="btn btn-xs btn-success" @click="szgg">添加</button>
				             </div>			             
				            <li class="list-group-item">				            	
                                 <span> <?php echo $userrow['notice'];?> </span>
				            </li>   
				          
				          </div>
			             </div>	
				         
				         
				         <div style="margin-top: 20px;">
				          <div class="card" class="">
				             <div class="card-header">	
				             	上级公告
				             </div>			             
				            <li class="list-group-item">				            	
                                 <span><?php		                        	
				                        	$a=$DB->get_row("select notice from qingka_wangke_user where uid='{$userrow['uuid']}' ");
				                        	echo $a['notice'];?>    </span>
				            </li>   
				          
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


 <script type="text/javascript">
    /* 鼠标特效 */
    var a_idx = 0;
    jQuery(document).ready(function($) {
        $("body").click(function(e) {
            var a = new Array("富强","民主","文明","和谐","自由","平等","公正","法治","爱国","敬业","诚信","友善");
            var $i = $("<span />").text(a[a_idx]);
            a_idx = (a_idx + 1) % a.length;
            var x = e.pageX
              , y = e.pageY;
            $i.css({
                "z-index": 999999999999999999999999999999999999999999999999999999999999999999999,
                "top": y - 20,
                "left": x,
                "position": "absolute",
                "font-weight": "bold",
                "color": "#ff6651"
            });
            $("body").append($i);
            $i.animate({
                "top": y - 180,
                "opacity": 0
            }, 1500, function() {
                $i.remove();
            });
        });
    });
</script>


<script>
$("pay2").hide();
new Vue({
	el:"#charge",
	data:{
       money:'',
       out_trade_no:''
	},
	methods:{
		pay:function(page){
		    var load=layer.load(2);
 			this.$http.post("/apisub.php?act=pay",{money:this.money},{emulateJSON:true}).then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){	
	          		$("pay2").show();
	          		this.out_trade_no=data.data.out_trade_no;	
	          		layer.msg(data.data.msg,{icon:1});		  
 					layer.open({
					  type: 1,
					  title: '请选择支付方式',
					  closeBtn: 0,
					  area: ['250px', '300px'],
					  skin: 'layui-bg-gray', //没有背景色
					  shadeClose: true,
					  content: $('#pay2'),
			  		  end: function(){ 
					    $("#pay2").hide();
					  }
					});          		                   	          			             			                     
	          	}else{	          		
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
		},szgg:function(){
		    	  		layer.prompt({title: '设置代理公告，您的代理可看到', formType: 2}, function(notice, index){
						  layer.close(index);
						  var load=layer.load(2);
			              $.post("/apisub.php?act=user_notice",{notice},function (data) {
					 	     layer.close(load);
				             if (data.code==1){  
				                layer.msg(data.msg,{icon:1});
				                window.location.href=""
				             }else{
				                layer.msg(data.msg,{icon:2});
				             }
			              });		    		    
					  });   	  	
		    	  }
	},
	mounted(){
		
	}
});
</script>
<script>
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
</script>

