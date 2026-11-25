<?php
$id =$_GET['id'];
$title='新版查询';
require_once('head.php');
$addsalt=md5(mt_rand(0,999).time());
$_SESSION['addsalt']=$addsalt;
?>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>FreeDom</title>
    <meta name="keywords" content="FreeDom" />
    <meta name="description" content="FreeDom" />
    
    <link rel="icon" href="assets/LightYear/favicon.ico" type="image/ico" />
    <link rel="stylesheet" href="assets/css/apps.css" type="text/css" />
    <link rel="stylesheet" href="assets/css/app.css" type="text/css" />
    <link rel="stylesheet" href="assets/layui/css/layui.css" type="text/css" />
    <link rel="stylesheet" href="assets/LightYear/js/bootstrap-multitabs/multitabs.min.css" />
    <link rel="stylesheet" href="assets/LightYear/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/LightYear/css/style.min.css" />
    <link rel="stylesheet" href="assets/LightYear/css/materialdesignicons.min.css" />
    <link rel="stylesheet" href="assets/js/font-awesome.min.css" />
    <link rel="stylesheet" href="assets/css/element.css" />
    <link rel="stylesheet" href="../assets/css/bootstrap.css" type="text/css" />
    <link rel="stylesheet" href="../assets/css/app.css" type="text/css" />
    <link rel="stylesheet" href="../assets/layui/css/layui.css" type="text/css" />
    <link rel="stylesheet" href="assets/css/index.css" />

    <script src="layer/3.1.1/layer.js"></script>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="assets/index.js"></script>


<script>
    $(document).ready(function(){
        // 初始化 Collapse 插件
        $('[data-toggle="collapse"]').collapse();
    });
</script>
</head>
<style type="text/css">
    .susuicon {
        position: absolute;
    left: 21px;
    top: 14px;
       width: 1.3em; height: 1.3em;
       vertical-align: -0.15em;
       fill: currentColor;
       overflow: hidden;
    }
     .susuicon2 {
     position: absolute;
    top: 50%;
    right: 20px;
    margin-top: -7px;
    transition: transform .3s;
       width: 1.1em; height: 1.1em;
       vertical-align: -0.15em;
       fill: currentColor;
       overflow: hidden;
    }
    .flex{height: 2em; width: 2em;   vertical-align: -0.15em;
       fill: currentColor;float:left; position: absolute;
    bottom: 7px;
    left: 5px;
       overflow: hidden;}
       
       .flex2{height: 1.4em; width: 1.4em;   vertical-align: -0.15em;
       fill: currentColor;float:left;margin-top:2px;margin-right: 5px;
       overflow: hidden;}
       .malet{padding-left:20px;font-size:11px;}
    .nav>li:hover{
        background-color: #f8f8ff;
    }
    hr {
    height: 1px;
    margin: 4px;
        
    }
    
    
    .frosss{    height: 38px;
    border-radius: 8px !important; border: 2px solid rgb(236, 236, 236);
    border-color: #ebebeb;
    -webkit-border-radius: 2px;
    border-radius: 2px;
    padding: 5px 12px;
    line-height: inherit;
    -webkit-transition: 0.2s linear;
    transition: 0.2s linear;
    -webkit-box-shadow: none;
    box-shadow: none;}
      .frosss2{ 
           border-radius: 8px !important; border: 2px solid rgb(236, 236, 236);
           display: block;
    width: 100%;
          height: 38px;
    border-color: #ebebeb;
    -webkit-border-radius: 2px;
    border-radius: 2px;
    padding: 5px 12px;
    line-height: inherit;
    -webkit-transition: 0.2s linear;
    transition: 0.2s linear;
    -webkit-box-shadow: none;
    box-shadow: none;}
    .table>thead>tr>th {
    padding: 20px;
</style>
<style>
.lioverhide {
     width: 300px
  }
.el-select-dropdown .el-select-dropdown__item {
  white-space: nowrap !important;
  overflow-x: auto !important;
  text-overflow: clip;
}
</style>
<style>
    /* 设置单选按钮及其标签的样式 */
    .category-container {
        display: flex; /* 使用 Flexbox 布局 */
        flex-wrap: wrap; /* 在空间不足时换行 */
    }

    .category-container label {
        margin-right: 10px; /* 添加一些右边距，用于分隔按钮 */
        margin-bottom: 5px; /* 设置下边距，使按钮间有一定间隔 */
        cursor: pointer; /* 当鼠标悬停在标签上时显示光标 */
    }

    /* 隐藏单选按钮 */
    .category-container input[type="radio"] {
        display: none; /* 隐藏单选按钮 */
    }

    /* 定义单选按钮的自定义样式 */
    .category-container input[type="radio"] + span {
        display: inline-block; /* 内联块级元素，使标签水平排列 */
        padding: 8px 15px; /* 设置按钮内边距 */
        border: 1px solid #ccc; /* 设置按钮边框 */
        border-radius: 6px; /* 设置按钮边框圆角 */
    }

    /* 定义选中状态下单选按钮的样式 */
    .category-container input[type="radio"]:checked + span {
        border-color: #0e78eF; /* 设置选中状态下的边框颜色为蓝色 */
        color: #333; /* 设置选中状态下的文字颜色为深灰色 */
    }
</style>
</body>
</html>
    <div class="app-content-body ">
        <div class="wrapper-md control" id="add">
	       <div class="panel panel-default" style="box-shadow: 8px 8px 15px #d1d9e6, -18px -18px 30px #fff; border-radius:8px;">
		    <div class="panel-heading font-bold " style="border-top-left-radius: 8px; border-top-right-radius: 8px;background-color:#fff;">
		    <div style="float:right;margin-right:20px"><el-link type="info"></el-link></div>
			    批量提交
		     </div>
				<div class="panel-body">
					<form class="form-horizontal devform">
					    <?php if ($conf['flkg']=="1" && $conf['fllx']=="1") {?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">项目分类</label>
                                <div class="col-sm-9">
                                    <div class="category-container">
                                        <label>
                                            <input type="radio" name="category" value="" @click="fenlei('')" <?=($selectedCategoryId === '') ? 'checked' : ''?>>
                                            <span class="category-name">全部分类</span>
                                        </label>
                                        <?php 
                                        $a = $DB->query("SELECT * FROM qingka_wangke_fenlei WHERE status=1 ORDER BY `sort` ASC");
                                        while ($rs = $DB->fetch($a)) {
                                        ?>
                                        <label>
                                            <input type="radio" name="category" value="<?=$rs['id']?>" @click="fenlei('<?=$rs['id']?>')" <?=($selectedCategoryId === $rs['id']) ? 'checked' : ''?>>
                                            <span class="category-name"><?=$rs['name']?></span>
                                        </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } else if ($conf['flkg']=="1" && $conf['fllx']=="2022") {?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">项目分类</label>
                                <div class="col-sm-9">
                                    <div class="category-container">
                                        <label>
                                            <input type="radio" name="category" value="" @click="fenlei('')" <?=($selectedCategoryId === '') ? 'checked' : ''?>>
                                            <span class="category-name">全部项目</span>
                                        </label>
                                        <?php 
                                        $a = $DB->query("SELECT * FROM qingka_wangke_fenlei WHERE status=3 ORDER BY `sort` ASC");
                                        while ($rs = $DB->fetch($a)) {
                                        ?>
                                        <label>
                                            <input type="radio" name="category" value="<?=$rs['id']?>" @click="fenlei('<?=$rs['id']?>')" <?=($selectedCategoryId === $rs['id']) ? 'checked' : ''?>>
                                            <span class="category-name"><?=$rs['name']?></span>
                                        </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php }?>

						<div class="form-group">
							<label class="col-sm-2 control-label">选择平台</label>
						<div class="col-sm-9">
							<!--<select class="form-control" v-model="cid" @change="tips(cid);">-->
							<el-select id="select"  v-model="cid"  @change="tips(cid)" popper-class="lioverhide" :popper-append-to-body ="false" filterable placeholder="点击选择下单平台" style="    background: url('../index/arrow.png') no-repeat scroll 99%;width:100%">
                                    <el-option
                                      v-for="class2 in class1"
                                      :key="class2.cid"
                                      :label="class2.name+'('+class2.price+'币)'"
                                      :value="class2.cid">
                                    </el-option>
                                  </el-select>					
							</div>
						</div>
						
						<div class="form-group" v-if="activems==true">
							<label class="col-sm-2 control-label" for="checkbox1">是否秒刷</label>
							<div class="col-sm-9">
								<div class="checkbox checkbox-success"  @change="tips2">
        				            <input style="    margin-left: 0px;" type="checkbox" v-model="miaoshua">
        				            	<label for="checkbox1" id="miaoshua"></label>
							    </div>
							</div>							
						</div>
                        <!--单学时备注定位点  在下面的v-if里面按格式添加cid即可-->
                        <div class="form-group " v-if="nochake==1">
							<div class="col-sm-2 col-xs-3 control-label" style="margin-top:9px">份数</div>
							<div class="col-sm-9 col-xs-8">
							    <el-input-number v-model="shu" :min="1" :max="100"></el-input-number>
							</div>
							<div class="col-sm-2 col-xs-3 control-label" style="margin-top:9px">课程名称</div>
							<div class="col-sm-9 col-xs-8">
							    <el-input v-model="bei" placeholder="请输入要刷的课程" style="margin-top:9px"></el-input>
							</div>
						</div>
                        <!--单学时备注定位点-->
                        
						<div class="form-group">
							<label class="col-sm-2 control-label">信息填写</label>
							<div class="col-sm-9">
						    <textarea rows="5" class="frosss2" style="height:120px;" v-model="userinfo"></textarea>	
						    <!--<span class="help-block m-b-none" style="color:red;">
						        <span v-html="content"></span>
						    </span>-->
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">网课说明</label>
							<div class="col-sm-9">
						    <!--<span class="help-block m-b-none" style="color:red;"><span v-html="content"></span></span>-->
						    <el-input placeholder="请选择商品查看说明" type="textarea":rows="3" v-model="content">
						    </el-input>
							</div>
						</div>
	
				  	    <div class="col-sm-offset-2" v-if="nochake==1">
				  	    	<button style="margin-left: 6px; font-size: 13px;" type="button" @click="add" value="立即提交" class="el-button el-button--primary is-plain"/><i class="glyphicon glyphicon-ok"></i>  立即提交</button>
				  	    </div>
				  	    <div class="col-sm-offset-2" v-else>
				  	    	<button style="margin-left: 6px; font-size: 13px;" type="button" @click="get" value="立即查询" class="el-button el-button--primary is-round"/><i class="	glyphicon glyphicon-zoom-in"></i>  立即查询</button>
				  	    	<button style="margin-left: 6px; font-size: 13px;" type="button" @click="add" value="立即提交" class="el-button el-button--primary is-round"/><i class="glyphicon glyphicon-ok"></i>  立即提交</button>
				  	    	<!--<button class="btn btn-label btn-round btn-warning" type="reset"  value="清空数据"><label><i class="mdi mdi-delete-empty"></i></label> 清空数据</button>-->
				  	    </div>

			        </form>
		        </div>
	     </div>
	     


	    <div class="panel panel-default" style="box-shadow: 8px 8px 15px #d1d9e6, -18px -18px 30px #fff; border-radius:8px;">
		   
		    <div class="panel-heading font-bold " style="border-top-left-radius: 8px; border-top-right-radius: 8px;background-color:#fff;">
			    查询结果 &nbsp;
			    <a class="el-button el-button--primary is-plain el-button--mini" style="padding: 4px 10px;" @click="selectAll()">全选</a>
		     </div>
				<div class="panel-body">
  <form class="form-horizontal devform">
    <!-- 循环遍历每个用户资源 -->
    <div class="container" v-for="(rs, key) in row" :key="key">
      <!-- 头部显示用户名和用户信息以及查询状态 -->
      <div class="row mb-3">
        <div class="col-12">
          <b>{{ rs.userName }}</b> {{ rs.userinfo }}
          <!-- 显示查询状态信息 -->
          <span v-if="rs.msg === '查询成功'" class="text-success"><b>{{ rs.msg }}</b></span>
          <span v-else class="text-danger"><b>{{ rs.msg }}</b></span>
        </div>
      </div>
      <!-- 卡片行 -->
      <div class="row">
        <!-- 每行2个卡片 -->
        <div class="col-md-4 mb-4" v-for="(res, index) in rs.data" :key="index">
          <!-- 使用Bootstrap的card组件创建卡片，并增加边框样式 -->
          <div class="card h-200 border border-primary" style="border-width: 3px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div class="card-body d-flex flex-column"> <!-- 使用 flex-column 使内容垂直排列 -->
              <!-- 左侧放置课程图片 -->
              <div class="course-image">
                <!-- 限制图片大小 -->
                <img :src="res.imageUrl" class="img-fluid" alt="课程图片" style="max-height: 200px; object-fit: cover;">
              </div>
              <!-- 右侧放置课程名称、课程ID等信息 -->
              <div class="flex-grow-1 px-3"> <!-- 添加间距 -->
                <h5 class="card-title">{{ res.name }}</h5>
                <p class="card-text">
                  <!-- 根据需要添加其他信息 -->
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
       </div>
       <div class="panel panel-default" style="box-shadow: 8px 8px 15px #d1d9e6, -18px -18px 30px #fff; border-radius:8px;">
               <div class="panel-heading font-bold bg-white" style="border-radius: 10px;">下单前请确保已知晓下列注意事项</div>
               <div class="panel-body">
                  <ul class="layui-timeline">
                      <li class="layui-timeline-item">
                        <i class="layui-icon layui-timeline-axis"></i>
                        <div class="layui-timeline-content layui-text">
                           <p>请务必查看项目下单须知和说明，防止出现错误！</p>
                        </div>
                     </li>
                     <li class="layui-timeline-item">
                        <i class="layui-icon layui-timeline-axis"></i>
                        <div class="layui-timeline-content layui-text">
                           <p>多账号下单必须换行，务必保证一行一条信息！</p>
                        </div>
                     </li>
                     <li class="layui-timeline-item">
                        <i class="layui-icon layui-timeline-axis"></i>
                        <div class="layui-timeline-content layui-text">
                           <p>同商品重复下单，请修改密码后再下！</p>
                        </div>
                     </li>
                     <li class="layui-timeline-item">
                        <i class="layui-icon layui-timeline-axis"></i>
                        <div class="layui-timeline-content layui-text">
                           <p>默认下单格式为学校、账号、密码(空格分开)！</p>
                        </div>
                     </li>
                     <li class="layui-timeline-item">
                        <i class="layui-icon layui-timeline-axis"></i>
                        <div class="layui-timeline-content layui-text">
                           <p>本站所有项目均来源于互联网，非平台自营，下单后概不退款！</p>
                        </div>
                     </li>
                  </ul>
               </div>
            </div>
            
    </div>
    </div>
<script>
    
$(document).ready(function(){
 $("#btn4").click(function(){ 
$("input[name='checkbox']").each(function(){ 
if($(this).attr("checked")) 
{ 
$(this).removeAttr("checked"); 
} 
else
{ 
$(this).attr("checked","true"); 
} 
}) 
}) 




}); 
</script>


<script type="text/javascript" src="assets/LightYear/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>
<script src="../assets/js/vue.min.js"></script>
<script src="assets/vue-resource.min.js"></script>
<script src="assets/axios.min.js"></script>
<script src="./assets/js/element.js"></script>




<script>
var vm=new Vue({
	el:"#add",
	data:{	
	    row:[],
	    shu:'',
	    bei:'',
	    nochake:0,
	    check_row:[],
		userinfo:'',
		cid:'',
		miaoshua:'',
		class1:'',
		class3:'',
		activems:false,
		checked:false,
		content:''
	},
	methods:{
	    get:function(salt){
	    	if(this.cid=='' || this.userinfo==''){
	    		layer.msg("所有项目不能为空");
	    		return false;
	    	}
		    userinfo=this.userinfo.replace(/\r\n/g, "[br]").replace(/\n/g, "[br]").replace(/\r/g, "[br]");      	           	    
	   	    userinfo=userinfo.split('[br]');//分割
	   	    this.row=[];
	   	    this.check_row=[];    	
	   	    for(var i=0;i<userinfo.length;i++){	
	   	    	info=userinfo[i]
	   	    	if(info==''){continue;}
	   	    	var hash=getENC('<?php echo $addsalt;?>');
	   	    	var loading=layer.load();
	    	    this.$http.post("/apisub.php?act=get",{cid:this.cid,userinfo:info,hash},{emulateJSON:true}).then(function(data){
	    		     layer.close(loading);	    	
	    		     if(data.body.code==-7){
	    	            salt=getENC(data.body.msg);
	    	            vm.get(salt);
	    	        }else{
	    	            this.row.push(data.body);
	    	        }
	    			    
	    	    });
	   	    }	   	    	    
	    },
	    add:function(){
	    	if(this.cid==''){
	    	    if(this.nochake!=1){
    	    		layer.msg("请先查课");
    	    		return false;
	    	    }
	    	} 	
	    	if(this.check_row.length<1){
	    	    if(this.nochake!=1){
    	    		layer.msg("请先选择课程");
    	    		return false;
	    	    }
	    	} 	
	    	//console.log(this.check_row);
	        var loading=layer.load();
	    	this.$http.post("/apisub.php?act=add",{cid:this.cid,data:this.check_row,shu:this.shu,bei:this.bei,userinfo:this.userinfo,nochake:this.nochake},{emulateJSON:true}).then(function(data){
	    		layer.close(loading);
	    		if(data.data.code==1){
	    			this.row=[];
	    			this.check_row=[]; 
	    			this.$message({type: 'success', showClose: true,message: data.data.msg});
	    		}else{
	    			this.$message({type: 'error', showClose: true,message: data.data.msg});
	    		}
	    	});
	    },
	    check888:function(userinfo,userName,rs,name){
	        var btns=document.getElementById("btns");
	        var  zk= document.getElementById("s1");
	        var x= zk.getElementsByTagName("input");
        	if(btns.checked==true) {
        		for(var i=0   ; i < x.length; ++i) {
                    data={userinfo,userName,data:rs[i]};
        			x[i].checked=true;
        		    vm.check_row.push(data);
        		}
        	}else {
        		for(var i=0; i < x.length; ++i) {
        			x[i].checked=false; 
        		}
        		 this.check_row = []
        	}
	    },
	    selectAll:function () {            
            if(this.cid==''){
	    		layer.msg("请先查课");
	    		return false;
	    	} 	
	    	this.checked=!this.checked;  
	    	if(this.check_row.length<1){
		    	for(i=0;i<vm.row.length;i++){
		    		console.log(i);
		    		userinfo=vm.row[i].userinfo
		    		userName=vm.row[i].userName
		    		rs=vm.row[i].data
		            for(a=0;a<rs.length;a++){
			    		aa=rs[a]
			    		data={userinfo,userName,data:aa}
			    		vm.check_row.push(data);
			        } 				    	
				}     	          
            }else{
            	vm.check_row=[]
            }   	    
	    	console.log(vm.check_row);                            
        },
	    checkResources:function(userinfo,userName,rs,id,name){
          for(i=0;i<rs.length;i++){
              if(id==""){
                  if(rs[i].name==name){
                      aa=rs[i]
                  }
              }else{
                  if(rs[i].id==id && rs[i].name==name){
                      aa=rs[i]
                  }
              }
        }
        data={userinfo,userName,data:aa}
        if(this.check_row.length<1){
          vm.check_row.push(data); 
        }else{
            var a=0;
          for(i=0;i<this.check_row.length;i++){            
            if(vm.check_row[i].userinfo==data.userinfo && vm.check_row[i].data.name==data.data.name){              
                  var a=1;
                  vm.check_row.splice(i,1);  
            }          
          }                                  
               if(a==0){
                    vm.check_row.push(data);
               }
        } 
      },
	    fenlei:function(id){
		  var load=layer.load(2);
 			this.$http.post("/apisub.php?act=getclassfl1",{id:id},{emulateJSON:true}).then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){			                     	
	          		this.class1=data.body.data;			             			                     
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
	    	
	    },
	    getclass:function(){
		  var load=layer.load(2);
 			this.$http.post("/apisub.php?act=getclass").then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){			                     	
	          		this.class1=data.body.data;			             			                     
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
	    	
	    },
	    getnock:function(cid){
 			this.$http.post("/apisub.php?act=getnock").then(function(data){	
	          	if(data.data.code==1){			                     	
	          		this.nock=data.body.data;	
	          		for(i=0;this.nock.length>i;i++){
	          		    if(cid==this.nock[i].cid){
	          		        this.nochake=1;
	          		        break;
	          		    }else{
	          		        this.nochake=0;
	          		    }
	          		}
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
	    	
	    },
	    tips: function (message) {
        	 for(var i=0;this.class1.length>i;i++){
        	 	if(this.class1[i].cid==message){
        	 	    this.show = true;
        	 	    this.content = this.class1[i].price+'币 '+'说明：'+this.class1[i].content;
                    /*layer.open({
                	 		    type: 0 
                                ,title: '商品说明'
                                ,content: this.class1[i].content
                                ,time: 5000
                                ,shade: 1  //不显示遮罩
                                ,anim: 1
                                ,maxmin: true
                                ,
                            });*/
	    		return false;	
        	 		if(this.class1[i].miaoshua==1){
					   	 this.activems=true;
					   }else{
					   	 this.activems=false;
					   }
        	 		return false;
        	 		
        	 	}
        	 	
        	 }
	
        },
	    /*tips: function (message) {
        	for(var i=0;this.class1.length>i;i++){
        	 	if(this.class1[i].cid==message){
                    this.$notify({
                        title: this.class1[i].name+'说明：',
                        dangerouslyUseHTMLString: true,
                        duration: 6000,
                        // showClose: false,
                        message:'<span style="font-size:14px;">'+this.class1[i].content+'</font>',
                    });
                    if(this.class1[i].miaoshua==1){
					   	 this.activems=true;
				   }else{
				   	 this.activems=false;
				   }
        	 		return false;
        	 	}
        	}
        },*/
        tips2: function () {
        	layer.tips('开启秒刷将额外收0.05的费用', '#miaoshua');      	  
		  
        }    
	},
	mounted(){
		this.getclass();		
	}
	
	
});
</script>