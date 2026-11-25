<?php
$title='批量查询';
require_once('head.php');
$addsalt=md5(mt_rand(0,999).time());
$_SESSION['addsalt']=$addsalt;
?>
<link rel="stylesheet" href="assets/css/element.css">
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<div class="app-content-body ">
   <div class="wrapper-md control" id="add">
      <div class="layui-row layui-col-space10">
         <div class="layui-col-md6">
            <div class="panel panel-default" style="border-radius: 10px;">
               <!--div class="panel-heading font-bold bg-white ">
			    填写信息&nbsp;&nbsp;<a href="#!" class="js-create-tab btn btn-xs btn-primary" data-title="单个提交" data-url="add">返回</a>
		     </div-->
               <div class="panel-body">
                  <form class="form-horizontal devform">
                   
                     <div class="form-group">
                        <label class="col-sm-2 control-label">项目分类</label>
                        <div class="col-sm-9">
                           <select class="layui-select" v-model="id" @change="fenlei(id);" style="    background: url('../index/arrow.png') no-repeat scroll 99%; border-radius: 8px; width:100%">
                              <option value="">全部分类</option>
                              <?php $a=$DB->query("select * from qingka_wangke_fenlei where status=1 OR status = 2 ORDER BY `sort` ASC"); while($rs=$DB->fetch($a)){ ?>
                              <option :value="<?=$rs['id']?>">
                                 <?=$rs[ 'name']?>
                              </option>
                              <?php } ?>
                           </select>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-2 control-label">项目选择</label>
                        <div class="col-sm-9">
                           <form class="form-horizontal" id="form-update">
                              <template>
                                 <el-select id="select" v-model="cid" @change="tips(cid)" filterable placeholder="请先选择平台，输入可进行搜索" style=" background: url('../index/arrow.png') no-repeat scroll 99%; width:100%">
                                    <el-option v-for="class2 in class1" :label="class2.name+'→'+class2.price+'币'" :value="class2.cid">
                                       <span style="float: left">{{ class2.name }}</span>
                                       <span style="float: right; color: #8492a6; font-size: 13px">{{ class2.price }}币</span>
                                    </el-option>
                                 </el-select>
                              </template>
                        </div>
                     </div>
                     <div v-show="show">
                        <div class="form-group">
                           <label class="col-sm-2 control-label">信息填写</label>
                           <div class="col-sm-9">
                              <textarea rows="5" class="layui-textarea" v-model="userinfo" placeholder="下单格式：学校（可为空） 账号 密码（用空格分隔）&#10多账号下单必须换行" style="border-radius: 8px;">
                              </textarea>
                              <span class="help-block m-b-none" style="color:red;" id="warning">
                                 <span v-html="content">
                                 </span>
                              </span>
                           </div>
                        </div>
                        <div class="col-sm-offset-2 col-sm-12">
                           <button type="button" @click="get" style="border-radius: 10px;" class="layui-btn layui-btn-sm" />查询课程</button>
                           <button type="button" @click="add" style="border-radius: 10px;" class="layui-btn layui-btn-sm layui-btn-normal" />立即提交</button>
                           <button type="reset" style="border-radius: 10px;" class="layui-btn layui-btn-sm layui-btn-primary" />重置</button>
                        </div>
                     </div>
                     </form>
               </div>
            </div>
         </div>
         <div class="layui-col-md6" v-show="show1">
            <div class="panel panel-default" style="border-radius: 10px;">
                <p>已勾选对象数量：{{ selectedCount }}</p>
               <div class="panel-heading font-bold bg-white" style="border-radius: 10px;">查询结果</div>
               <div class="panel-body">
                  <form class="form-horizontal devform">
                     <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div v-for="(rs,key) in row">
                           <div class="panel panel-default">
                              <div class="panel-heading" role="tab" id="headingOne">
                                 <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" :href="'#'+key" aria-expanded="true">
                                       <b>{{rs.userName}}</b>{{rs.userinfo}}
                                       <span v-if="rs.msg=='查询成功'">
                                          <b style="color: green;">{{rs.msg}}</b>
                                       </span>
                                       <span v-else-if="rs.msg!='查询成功'">
                                          <b style="color: red;">{{rs.msg}}</b>
                                       </span>
                                    </a>
                                 </h4>
                              </div>
                              <div :id="key" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                 <div class="panel-body">
                                    <div v-for="(res,key) in rs.data" class="checkbox checkbox-success">
                                       <li>
                                          <input type="checkbox" :value="res.name" @click="checkResources(rs.userinfo,rs.userName,rs.data,res.id,res.name)">
                                          <label for="checkbox1">
                                          </label>
                                          <span v-if="!!res.remark" style="color: red;">[{{res.remark}}]</span>
                                          <span>{{res.name}}</span>
                                          <span v-if="res.credit !== null && res.credit !== undefined && res.credit !== ''"> 【{{res.credit}}】</span>
                                          <span v-if="res.id !== null && res.id !== undefined && res.id !== ''">  ◼  [课程ID: {{res.id}}] </span>
                                       </li>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
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
<script src="./assets/js/element.js"></script>


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
var vm=new Vue({
	el:"#add",
	data:{	
	    row:[],
	    check_row:[],
		userinfo:'',
		cid:'',
		id:'',
		miaoshua:'',
		class1:'',
		class3:'',
		show: false,
		show1: false,
		content:'',
		activems:false
	},
	methods:{
	    get:function(){
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
	   	    	var hash=getENC('<?php echo $addsalt;?>');
	   	    	var loading=layer.load(2);
	    	    this.$http.post("/apisub.php?act=get",{cid:this.cid,userinfo:info,hash},{emulateJSON:true}).then(function(data){
	    		     layer.close(loading);	    	
	    		     this.show1 = true;
	    			 this.row.push(data.body);
	    	    });
	   	    }	   	    	    
	    },
	    add:function(){
	    	if(this.cid==''){
	    		layer.msg("请先查课");
	    		return false;
	    	} 	
	    	if(this.check_row.length<1){
	    		layer.msg("请先选择课程");
	    		return false;
	    	} 	
	    	//console.log(this.check_row);
	        var loading=layer.load(2);
	    	this.$http.post("/apisub.php?act=add",{cid:this.cid,data:this.check_row},{emulateJSON:true}).then(function(data){
	    		layer.close(loading);
	    		if(data.data.code==1){
	    			this.row=[];
	    			this.check_row=[]; 
	    			layer.alert(data.data.msg,{icon:1,title:"温馨提示"},function(){setTimeout(function(){window.location.href=""});});
	    		}else{
	    			layer.alert(data.data.msg,{icon:2,title:"温馨提示"});
	    		}
	    	});
	    },
	    checkResources(userinfo, userName, rs, id, name) {
          let found = false;
          for (let i = 0; i < rs.length; i++) {
            if (id === "") {
              if (rs[i].name === name) {
                found = rs[i];
                break;
              }
            } else {
              if (rs[i].id === id && rs[i].name === name) {
                found = rs[i];
                break;
              }
            }
          }
        
          if (found) {
            const data = { userinfo, userName, data: found };
            const index = this.check_row.findIndex(item => item.userinfo === userinfo && item.data.name === found.name);
        
            if (index === -1) {
              // 如果未找到，添加新元素
              this.check_row.push(data);
              this.selectedCount++;
            } else {
              // 如果已存在，删除元素
              this.check_row.splice(index, 1);
              this.selectedCount--;
            }
          }
        },
	    fenlei:function(id){
		  var load=layer.load(2);
 			this.$http.post("/apisub.php?act=getclassfl",{id:id},{emulateJSON:true}).then(function(data){	
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
        tips: function (message) {
        	 for(var i=0;this.class1.length>i;i++){
        	 	if(this.class1[i].cid==message){
        	 	    this.show = true;
        	 	    this.content = this.class1[i].content;
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
        tips2: function () {
        	layer.tips('开启秒刷将额外收0.05的费用', '#miaoshua');      	  
		  
        }    
	},
	mounted(){
		this.getclass();		
	}
	
	
});
</script>