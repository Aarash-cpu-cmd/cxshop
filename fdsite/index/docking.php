<?php
$mod='blank';
$title='平台对接';
require_once('head.php');
?>
     <div class="app-content-body ">
        <div class="wrapper-md control">
        	
	      <div class="panel panel-default">
		    <div class="panel-heading font-bold bg-white">彩虹代刷对接/小储云对接（自定义访问URL）<button class="btn btn-xs btn-success" data-toggle="modal" data-target="#modal-platform">查看平台ID</button></div>
				 <div class="panel-body">
                                 自定义访问域名URL：<input type="text" class="layui-input" name="pass" value="https://<?echo($_SERVER['SERVER_NAME']);?>/api.php?act=add" required>
								 
             POST提交参数：<input type="text" class="layui-input" name="pass" value="uid=你的uid&key=你的key密匙&platform=平台ID&school=学校&user=账号&pass=密码&kcname=课程名字"><br/>
       
		    </div>
		  </div>
		  

        <div class="modal fade primary" id="modal-platform">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">平台ID</h4>
                    </div>
                         		      <div class="table-responsive"> 
														         <table class="table table-striped">
														          <thead><tr><th>ID</th><th>平台名称</th></thead>
														          <tbody>
														          	
																						<?php 
											                     	 $a=$DB->query("select * from qingka_wangke_class where status=1 ");
											                     	 while($rs=$DB->fetch($a)){
											                     	 	  echo "<tr><td>".$rs['cid']."</td><td>".$rs['name']."</td></tr>"; 
											                     	 }
											                     	?>

														           
														          </tbody>
														        </table>
														      </div>
   
                    
                    <div class="modal-footer">
                        <button type="button" class="layui-btn layui-btn-danger" data-dismiss="modal">取消</button>
                        <button type="button" class="layui-btn" data-dismiss="modal" @click="layer.msg('好的，靓仔')">知道了</button>
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
new Vue({
	el:"#loglist",
	data:{
		row:null
	},
	methods:{
		get:function(page){

		}
	},
	mounted(){
		this.get(1);
	}
});
</script>