<?php
$mod='blank';
$title='日志列表';
require_once('head.php');
?>
     <div class="app-content-body ">
        <div class="wrapper-md control">
        	
	    <div class="panel panel-default" id="loglist">
		    <div class="panel-body"  >
		        <div class=" font-bold layui-bg-white">操作日志</div>
				     <div class="el-form layui-row layui-col-space10">
				 			<div class="form-inline">	
						  	<div class="form-group">			          
				              <el-select class="select" placeholder="请选择查询类型" v-model="type" style="scroll 99%;width:100%"filterable>
				                <el-option label="请选择查询类型" value=""></el-option>
				                <?php
				                    if($userrow['uid']!=1){
                                    $a = $DB->query("SELECT DISTINCT type FROM qingka_wangke_log WHERE uid = " . intval($userrow['uid']). " ORDER BY type ASC");
				                    }else{
				                        $a = $DB->query("SELECT DISTINCT type FROM qingka_wangke_log  ORDER BY type ASC");
				                    }
                                    while ($row = $DB->fetch($a)) {
                                        echo '<el-option label="' . htmlspecialchars($row['type']) . '" value="' . htmlspecialchars($row['type']) . '"></el-option>';
                                    }
                                ?>
				                
				              </el-select>              			               
			            </div> 
			            <div class="form-group">			          
				               <el-select class="select" placeholder="请选择查询条件" v-model="types" style="scroll 99%;width:100%">
				                <el-option label="请选择查询条件" value=""></el-option>
				                <el-option label="用户UID" value="1"></el-option>
	 		   				    <el-option label="操作内容" value="2"></el-option>
	 		   				    <el-option label="积分变动" value="3"></el-option>
	 		   				    <el-option label="操作时间" value="4"></el-option>
	 		   				    
				              </el-select>              			               
			             </div>  
			              <div class="form-group">
				                <el-input v-model="qq" placeholder="请输入查询内容"></el-input>
			              </div> 
			              <div class="form-group">				              
				               <!--<input type="submit" @click="get(1,1)" value="查询" class="layui-btn"/>-->
				               <el-button type="primary" icon="el-icon-search" @click="get(1,1)">查询</el-button>
			              </div>			              
			          </div>
		      <div class="table-responsive">
		        <table class="table table-striped">
		          <thead><tr>
		          <th>
                    <label class="lyear-checkbox checkbox-info">
                      <input type="checkbox" id="check-all"><span>ID</span>
                    </label>
                  </th>
		          <th>用户ID</th><th>类型</th><th>积分变动</th><th>积分</th><th>操作内容</th><th>操作时间</th><th>操作IP</th></tr></thead>
		          <tbody>
		            <tr v-for="res in row.data">
		            	<!--<td>{{res.id}}</td>-->
		            	<td>
                            <label class="lyear-checkbox checkbox-info">
                              <input type="checkbox" v-model="sex"><span>{{res.id}}</span>
                            </label>
                        </td>
		            	<td>{{res.uid}}</td>
		            	<td>
		            	    <span class="btn btn-xs" :class="getButtonType(res.type)">
    {{ res.type }}
  </span></td>
		            	<td>{{res.money}}</td>
		            	<td>{{res.smoney}}</td>
		            	<td>{{res.text}}</td>
		            	<td>{{res.addtime}}</td>
		            	<td>{{res.ip}}</td>           	          	
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
		    </div>
		  </div>
     
    </div>
   </div>
 </div>
 
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/apps.css" type="text/css" />
    <link rel="stylesheet" href="assets/css/app.css" type="text/css" />
    <link rel="stylesheet" href="assets/layui/css/layui.css" type="text/css" />
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/LightYear/js/bootstrap-multitabs/multitabs.min.css">
    <link rel="stylesheet" href="assets/LightYear/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/LightYear/css/style.min.css">
    <link rel="stylesheet" href="assets/LightYear/css/materialdesignicons.min.css">
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
<script>
new Vue({
	el:"#loglist",
	data:{
		row:null,
		sex:[],
		type:'',
		types:'',
		qq:'',
        typeToColorMap: {
      // API 相关操作，通常表示信息类或次要操作
      "API改密": "btn-info",
      "API查课": "btn-info",
      "API添加任务": "btn-info",
      "API补刷": "btn-info",
      
      // copilot ai 相关操作，根据成功或失败分配颜色
      "copilot ai-准备添加订单": "btn-info",
      "copilot ai-删除订单失败": "btn-danger",
      "copilot ai-删除订单成功": "btn-success",
      "copilot ai-改变订单状态失败": "btn-danger",
      "copilot ai-改变订单状态成功": "btn-success",
      "copilot ai-查询日志成功": "btn-success",
      "copilot ai-添加订单失败": "btn-danger",
      "copilot ai-添加订单成功": "btn-success",
      "copilot ai-源台下单成功": "btn-success",
      "copilot ai-源台编辑订单失败": "btn-danger",
      "copilot ai-源台编辑订单成功": "btn-success",
      "copilot ai-源台预查订单成功": "btn-info",
      "copilot ai-立即打卡失败": "btn-danger",
      "copilot ai-立即打卡成功": "btn-success",
      "copilot ai-编辑订单失败": "btn-danger",
      "copilot ai-编辑订单成功": "btn-success",
      "copilot ai-自动获取信息": "btn-info",
      "copilot ai-获取推送码成功": "btn-success",
      "copilot ai-获取订单源台信息失败": "btn-danger",
      "copilot ai-获取订单源台信息成功": "btn-success",
      "copilot ai-预删除订单": "btn-warning",
      "copilot ai-预改变订单状态": "btn-warning",
      "copilot ai-预查询日志": "btn-info",
      "copilot ai-预源台编辑订单": "btn-warning",
      "copilot ai-预立即打卡": "btn-warning",
      "copilot ai-预编辑订单": "btn-warning",
      "copilot ai-预获取推送码": "btn-info",
      "copilot ai-预获取订单源台信息": "btn-info",

      // 财务相关操作
      "上级充值": "btn-success",
      "代理充值": "btn-success",
      
      // 系统配置或修改操作
      "修改密码": "btn-warning",
      "修改费率": "btn-warning",
      "关闭接口": "btn-danger",
      "卡密充值": "btn-success",
      
      // 内容创作相关
      "土拨鼠撰写": "btn-primary",
      "撰写文章": "btn-primary",
      "更新上架": "btn-primary",
      
      // 用户交互操作
      "在线充值": "btn-success",
      "在线充值重复刷新": "btn-warning",
      "实习打卡-修改订单": "btn-warning",
      "实习打卡-添加订单": "btn-success",
      "实习打卡-立即打卡": "btn-success",
      
      // 商户管理操作
      "封禁商户": "btn-danger",
      "开通接口": "btn-success",
      "批量删除订单": "btn-danger",
      "批量提交": "btn-success",
      "批量更新": "btn-info",
      "批量重刷": "btn-warning",
      
      // 订单管理操作
      "查课": "btn-info",
      "添加任务": "btn-success",
      "添加商户": "btn-success",
      "登录": "btn-primary",
      "解封商户": "btn-success",
      "订单停止": "btn-danger",
      "订单密码修改": "btn-warning",
      "订单补刷": "btn-warning",
      "订单退款": "btn-danger",
      "设置公告": "btn-info",
      "设置邀请码": "btn-info",
      
      // 其他操作
      "跨户充值": "btn-success",
      "运动世界下单": "btn-success",
      "邀请码注册商户": "btn-success",
      "重置密码": "btn-warning",
      
      // 默认颜色
      "default": "btn-default"
    }
	},
	methods:{
		get:function(page,a){
		  var load=layer.load(2);
		  data={page:page,type:this.type,types:this.types,qq:this.qq}
 			this.$http.post("/apisub.php?act=loglist",data,{emulateJSON:true}).then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){			                     	
	          		this.row=data.body;			             			                     
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
		},
		getButtonType(type) {
    // 根据 type 返回对应的颜色类，如果未找到则返回默认颜色
    return this.typeToColorMap[type] || this.typeToColorMap["default"];
  },
	},
	mounted(){
		this.get(1);
	}
});
</script>