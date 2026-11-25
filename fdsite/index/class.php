<?php
$title='编辑平台网课';
require_once('head.php');
if($userrow['uid']!=1){exit("<script language='javascript'>window.location.href='login.php';</script>");}
?>
<style>
        /* 设置复选框的宽度和高度为 15px */
.form-check-input[type="checkbox"] {
    width: 15px;
    height: 15px;
    /* 将复选框的位置相对于其正常位置上移 5px */
    position: relative;
    top: 0px;
    /* 将复选框的位置相对于其正常位置左移 0px */
    left: 5px;
    /* 设置复选框右侧与其后面的元素的间距为 5px */
    margin-right: 5px;
}

</style>
     <div class="app-content-body ">
        <div class="wrapper-md control" id="orderlist">
        	
	    <div class="panel panel-default" >
		    平台列表
		    <div class="panel-heading font-bold bg-white" style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 5px;">
                <!-- 按钮组 -->
                <div style=" gap: 5px;">
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal-add">添加</button>
                    <button class="btn btn-sm btn-success" @click="yjsj">一键上架</button>
                    <button class="btn btn-sm btn-success" @click="yjxj">一键下架</button>
                    <button class="btn btn-sm btn-danger" @click="del">删除项目</button>
                    <button class="btn btn-sm btn-danger" @click="upkeyword">修改关键词</button>
                </div>
                
                <!-- 搜索框和按钮 -->
                <div style="flex-grow: 1; display: flex; align-items: center;">
                    <input type="text" class="form-control" v-model="keyword" placeholder="输入关键词搜索" style="max-width: 50%; flex-grow: 1;">
                    <button class="btn btn-sm btn-success" @click="search">搜索</button>
                </div>
            </div>
				 <div class="panel-body">
		      <div class="table-responsive">
		        <table class="table table-striped">
		          <thead><tr><th><input type="checkbox" class="form-check-input" v-model="selectAll" @change="selectAllRows"></th><th>操作</th><th>ID</th><th>排序</th><th>平台名字</th><th>定价</th><th>查询接口</th><th>交单接口</th><th>查询参数</th><th>对接参数</th><th>下单计算</th><th>所在分类</th><th>状态</th><th>添加时间</th></tr></thead>
		          <tbody>
		            <tr v-for="res in row.data">
		                <td><input type="checkbox" class="form-check-input" v-model="selectedRows" :value="res.cid"></td>
		                <td><button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#modal-update" @click="storeInfo=res">编辑</button></td>   
		            	<td>{{res.cid}}</td>
		            	<td>{{res.sort}}</td>
		            	<td>{{res.name}}</td>
		            	<td>{{res.price}}</td>
		            	<td>{{res.cx_name}}</td>
		            	<td>{{res.add_name}}</td>
		            	<td>{{res.getnoun}}</td>
		            	<td>{{res.noun}}</td>
		            	<td>{{res.yunsuan=='*'?"乘法":"加法"}}</td>
		            	<td>
		            	    <!--<span class="btn btn-xs btn-success">{{res.fenlei_name}}</span>-->
		            	    <span class="btn btn-xs btn-success" v-if="res.fenlei_status=='分类一'">{{res.fenlei_name}}</span>
        		            <span class="btn btn-xs btn-info" v-else-if="res.fenlei_status=='分类二'">{{res.fenlei_name}}</span>
        		            <span class="btn btn-xs btn-danger" v-else-if="res.fenlei_status==0">未分类</span>
		            	</td>
		            	<td><span class="btn btn-xs btn-success" v-if="res.status==1">上架中</span><span class="btn btn-xs btn-danger" v-else-if="res.status==0">已下架</span></td>
		            	<td>{{res.addtime}}</td>
		                 	
		            </tr>
		          </tbody>
		        </table>
		      </div>
		      
			     <ul class="pagination" v-if="row.last_page>1"><!--by 青卡 Vue分页 -->
			         <li class="disabled"><a @click="get(1)">首页</a></li>
			         <li class="disabled"><a @click="row.current_page>1?get(row.current_page-1, this.keyword):''">&laquo;</a></li>
		             <li  @click="get(row.current_page-3, this.keyword)" v-if="row.current_page-3>=1"><a>{{ row.current_page-3 }}</a></li>
						    <li  @click="get(row.current_page-2, this.keyword)" v-if="row.current_page-2>=1"><a>{{ row.current_page-2 }}</a></li>
						    <li  @click="get(row.current_page-1, this.keyword)" v-if="row.current_page-1>=1"><a>{{ row.current_page-1 }}</a></li>
						    <li :class="{'active':row.current_page==row.current_page}" @click="get(row.current_page, this.keyword)" v-if="row.current_page"><a>{{ row.current_page }}</a></li>
						    <li  @click="get(row.current_page+1, this.keyword)" v-if="row.current_page+1<=row.last_page"><a>{{ row.current_page+1 }}</a></li>
						    <li  @click="get(row.current_page+2, this.keyword)" v-if="row.current_page+2<=row.last_page"><a>{{ row.current_page+2 }}</a></li>
						    <li  @click="get(row.current_page+3, this.keyword)" v-if="row.current_page+3<=row.last_page"><a>{{ row.current_page+3 }}</a></li>		       			     
			         <li class="disabled"><a @click="row.last_page>row.current_page?get(row.current_page+1, this.keyword):''">&raquo;</a></li>
			         <li class="disabled"><a @click="get(row.last_page, this.keyword)">尾页</a></li>	    
			     </ul>      
		    </div>
		  </div>
        <div class="modal fade primary" id="modal-update">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">平台修改</h4>
                    </div>
           
                    <div class="modal-body">
                        <form class="form-horizontal" id="form-update">
                            <input type="hidden" name="action" value="update"/>
                            <input type="hidden" name="cid" :value="storeInfo.cid"/>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">排序</label>
                                <div class="col-sm-9">             
                                  <input type="text" name="sort" class="layui-input" :value="storeInfo.sort" placeholder="输入数字,商品排序从小到大">
                               </div>
                            </div>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">平台名字</label>
                                <div class="col-sm-9">             
                                  <input type="text" name="name" class="layui-input" :value="storeInfo.name" placeholder="输入平台名字">
                               </div>
                            </div>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">定价</label>
                                <div class="col-sm-9">             
                                  <input type="text" name="price" class="layui-input" :value="storeInfo.price" placeholder="输入价格">
                               </div>
                            </div>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">查询参数</label>
                                <div class="col-sm-9">             
                                  <input type="text" name="getnoun" class="layui-input" :value="storeInfo.getnoun" placeholder="输入标识">
                               </div>
                            </div>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">对接参数</label>
                                <div class="col-sm-9">             
                                  <input type="text" name="noun" class="layui-input" :value="storeInfo.noun" placeholder="输入标识">
                               </div>
                            </div>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">说明</label>
                                <div class="col-sm-9"> 
                                  <textarea name="content" class="layui-textarea" rows="3" :value="storeInfo.content"></textarea>           
                               </div>
                            </div>
                            <div class="form-group">
                             	<label class="col-sm-3 control-label">查询平台</label>
                             	<div class="col-sm-9">   
	                            <select name="queryplat" :value="storeInfo.queryplat" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">
	                            	<option value="0">自营</option>
	                            	<?php
	                            		   	 $a=$DB->query("select * from qingka_wangke_huoyuan");
	                            		   	 while($b=$DB->fetch($a)){	            
	                            		   	 	echo '<option value="'.$b['hid'].'">'.$b['name'].'</option>';	
	                            		   	 }
	 
									?>
	                            </select>
	                            </div>
                            </div>
                           <div class="form-group">
                             	<label class="col-sm-3 control-label">交单平台</label>
                             	<div class="col-sm-9">   
	                            <select name="docking" :value="storeInfo.docking" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">	
	                            	<option value="0">自营</option>                            	                            	
	                            	<?php
	                            		   	 $a=$DB->query("select * from qingka_wangke_huoyuan");
	                            		   	 while($b=$DB->fetch($a)){
	                            		   	 	echo '<option value="'.$b['hid'].'">'.$b['name'].'</option>';	
	                            		   	 }
									?>
	                            </select>
	                            </div>
                            </div>
                            <div class="form-group">
                             	<label class="col-sm-3 control-label">代理下单算法</label>
                             	<div class="col-sm-9">   
	                            <select name="yunsuan" :value="storeInfo.yunsuan" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">	                            	                            	
	                               <option value="*">乘法</option>
	                               <option value="+">加法</option>
	                            </select>
	                            </div>
                            </div>
                            <div class="form-group">
                            	<label class="col-sm-3 control-label">状态</label>
                            	<div class="col-sm-9">   
	                            <select name="status" :value="storeInfo.status" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">
	                                <option value="1">上架</option>
	                                <option value="0">下架</option>
	                            </select>
	                            </div>
                            </div>
                            <div class="form-group">
                             	<label class="col-sm-3 control-label">分类</label>
                             	<div class="col-sm-9">   
	                            <select name="fenlei" :value="storeInfo.fenlei" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">	
	                            	<option value="">无</option>
	                            	<?php
                                        $a = $DB->query("select * from qingka_wangke_fenlei ORDER BY `sort` ASC");
                                        while ($b = $DB->fetch($a)) {
                                            if ($b['status'] < 3) {
                                            // 根据status字段的值添加不同的前缀
                                            $prefix = '';
                                            switch ($b['status']) {
                                                case 1:
                                                    $prefix = '分类一 &';
                                                    break;
                                                case 2:
                                                    $prefix = '分类二 &';
                                                    break;
                                                case 3:
                                                    $prefix = '特殊分类 &';
                                                    break;
                                                case 0:
                                                    $prefix = '未启用 &';
                                                    break;
                                                default:
                                                    // 可以在这里添加其他情况的处理，如果没有匹配则不添加前缀
                                                    break;
                                            }
                                            // 输出option标签，其中包含前缀和名字
                                            echo '<option value="' . $b['id'] . '">' . $prefix . $b['name'] . '</option>';
                                        }
                                        }
                                    ?>
	                            </select>
	                            </div>
                            </div>
                         </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="layui-btn layui-btn-danger" data-dismiss="modal">取消</button>
                        <button type="button" class="layui-btn" @click="form('update')">确定</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade primary" id="modal-add">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">平台添加</h4>
                    </div>
           
                    <div class="modal-body">
                        <form class="form-horizontal" id="form-add">
                            <input type="hidden" name="action" value="add"/>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">排序</label>
                                <div class="col-sm-9">             
                                  <input type="text" name="sort" class="form-control"  placeholder="输入数字,不填默认10,商品排序从小到大">
                               </div>
                            </div>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">平台名字</label>
                                <div class="col-sm-9">             
                                  <input type="text" name="name" class="form-control"  placeholder="输入平台名字">
                               </div>
                            </div>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">定价</label>
                                <div class="col-sm-9">             
                                  <input type="text" name="price" class="form-control"  placeholder="输入价格">
                               </div>
                            </div>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">查询参数</label>
                                <div class="col-sm-9">             
                                  <input type="text" name="getnoun" class="form-control"  placeholder="输入标识">
                               </div>
                            </div>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">对接参数</label>
                                <div class="col-sm-9">             
                                  <input type="text" name="noun" class="form-control" placeholder="输入标识">
                               </div>
                            </div>
                            <div class="form-group">
                               <label class="col-sm-3 control-label">说明</label>
                                <div class="col-sm-9"> 
                                  <textarea name="content" class="form-control" rows="3"></textarea>           
                               </div>
                            </div>
                            <div class="form-group">
                             	<label class="col-sm-3 control-label">查询平台</label>
	                            <div class="col-sm-9">                             	
		                            <select name="queryplat" class="form-control">
		                            	<option value="0">自营</option>
		                            	<?php
		  	                            		$a=$DB->query("select * from qingka_wangke_huoyuan");
		                            		   	 while($b=$DB->fetch($a)){
		                            		   	 	echo '<option value="'.$b['hid'].'">'.$b['name'].'</option>';	
		                            		   	 }
										?>
		                            </select>
		                       </div>
	                       </div>
                            <div class="form-group">
                             	<label class="col-sm-3 control-label">对接平台</label>
                             	<div class="col-sm-9">   
	                            <select name="docking" class="form-control">
	                            	<option value="0">自营</option>
	                            	<?php
	                            		   	 $a=$DB->query("select * from qingka_wangke_huoyuan");
	                            		   	 while($b=$DB->fetch($a)){
	                            		   	 	echo '<option value="'.$b['hid'].'">'.$b['name'].'</option>';	
	                            		   	 }
									?>
	                            </select>
	                            </div>
                            </div>
                            <div class="form-group">
                            	<label class="col-sm-3 control-label">状态</label>
                            	<div class="col-sm-9">   
	                            <select name="status" class="form-control">
	                                <option value="1">上架</option>
	                                <option value="0">下架</option>
	                            </select>
	                            </div>
                            </div>
                            <div class="form-group">
                             	<label class="col-sm-3 control-label">分类</label>
                             	<div class="col-sm-9">   
	                            <select name="fenlei" class="form-control">
	                            	<option value="">无</option>
	                            	<?php
	                            		   	 $a=$DB->query("select * from qingka_wangke_fenlei ORDER BY `sort` ASC");
	                            		   	 while($b=$DB->fetch($a)){
	                            		   	 	echo '<option value="'.$b['id'].'">'.$b['name'].'</option>';	
	                            		   	 }
									?>
	                            </select>
	                            </div>
                            </div>
                        </form>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                            <button type="button" class="btn btn-success" @click="form('add')">确定</button>
                        </div>
                    </div>
                </div>
            </div>
  
        </div>
  
  
    </div>
   </div>
 </div>
 
 
 
 
 
 
<?php require_once("footer.php");?>	
<script>
vm=new Vue({
	el:"#orderlist",
	data:{
		row:[],
		page:0,
		storeInfo:[],
        selectAll: false,// 全选复选框的绑定值
        selectedRows: [],// 用于存储选中行数据的数组
        keyword:'',
        keywordA:'',
        keywordB:''
	},
	methods:{
		get: function(page, keyword) {
            var load = layer.load(2);
            // 使用axios发送POST请求，将搜索关键词传递给后端
            this.$http.post("/apisub.php?act=classlist", { page: page, keyword: keyword }, { emulateJSON: true }).then(function(data) {
                layer.close(load);
                if (data.data.code == 1) {
                    this.row = data.body;
                } else {
                    layer.msg(data.data.msg, { icon: 2 });
                }
            });
        },
        search: function() {
            // 调用get方法进行搜索，传递当前页数和搜索关键词
            this.get(1, this.keyword);
        },
		yjsj: function() {
            var vm = this; // 保存 Vue 实例的引用
            if (vm.selectedRows.length === 0) {
                layer.msg('请选择要上架的项目', {icon: 2});
                return;
            }
            layer.confirm('确定要上架所选项目吗？', { // 弹出确认对话框
                title: '确认上架', // 对话框标题
                icon: 3 // 提示图标为问号
            }, function() { // 点击确认按钮的回调函数
                var load = layer.load(2); // 加载提示
                vm.$http.post("/apisub.php?act=yjsj", {cid: vm.selectedRows}, {emulateJSON: true})
                    .then(function(data) { // 请求成功的处理函数
                        layer.close(load); // 关闭加载提示
                        if (data.data.code == 1) { // 如果操作成功
                            this.get(1, this.keyword); // 重新获取商品列表数据
                            vm.selectedRows = []; // 清空选中行数组
                            vm.selectAll = false; // 取消全选状态
                            layer.msg(data.data.msg, {icon: 1}); // 显示成功提示
                        } else { // 如果操作失败
                            layer.msg(data.data.msg, {icon: 2}); // 显示失败提示
                        }
                    }, function(error) { // 请求失败的处理函数
                        layer.close(load); // 关闭加载提示
                        layer.msg('上架失败', {icon: 2}); // 显示失败提示
                    });
            });
        },
		yjxj: function() {
            var vm = this; // 保存 Vue 实例的引用
            if (vm.selectedRows.length === 0) {
                layer.msg('请选择要下架的项目', {icon: 2});
                return;
            }
            layer.confirm('确定要下架所选项目吗？', { // 弹出确认对话框
                title: '确认下架', // 对话框标题
                icon: 3 // 提示图标为问号
            }, function() { // 点击确认按钮的回调函数
                var load = layer.load(2); // 加载提示
                vm.$http.post("/apisub.php?act=yjxj", {cid: vm.selectedRows}, {emulateJSON: true})
                    .then(function(data) { // 请求成功的处理函数
                        layer.close(load); // 关闭加载提示
                        if (data.data.code == 1) { // 如果操作成功
                            this.get(1, this.keyword); // 重新获取商品列表数据
                            vm.selectedRows = []; // 清空选中行数组
                            vm.selectAll = false; // 取消全选状态
                            layer.msg(data.data.msg, {icon: 1}); // 显示成功提示
                        } else { // 如果操作失败
                            layer.msg(data.data.msg, {icon: 2}); // 显示失败提示
                        }
                    }, function(error) { // 请求失败的处理函数
                        layer.close(load); // 关闭加载提示
                        layer.msg('下架失败', {icon: 2}); // 显示失败提示
                    });
            });
        },
		form:function(form){
		   var load=layer.load(2);
		   
 			this.$http.post("/apisub.php?act=upclass",{data:$("#form-"+form).serialize()},{emulateJSON:true}).then(function(data){	
	          	layer.close(load);
	          	console.log($("#form-"+form).serialize())
	          	if(data.data.code==1){			                     		          		
	          		this.get(this.row.current_page, this.keyword); // 重新获取商品列表数据
	          		$("#modal-" + form).modal('hide');
	          		layer.msg(data.data.msg,{icon:1});	             			                     
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
		},
		bs:function(oid){
			layer.msg(oid);
		},
		del: function() {
            var vm = this; // 保存 Vue 实例的引用
            if (vm.selectedRows.length === 0) {
                layer.msg('请选择要删除的项目', {icon: 2});
                return;
            }
            layer.confirm('确定要删除所选项目吗？', { // 弹出确认对话框
                title: '确认删除', // 对话框标题
                icon: 3 // 提示图标为问号
            }, function() { // 点击确认按钮的回调函数
                var load = layer.load(2); // 加载提示
                vm.$http.post("/apisub.php?act=wk_del", {cid: vm.selectedRows}, {emulateJSON: true})
                    .then(function(data) { // 请求成功的处理函数
                        layer.close(load); // 关闭加载提示
                        if (data.data.code == 1) { // 如果删除操作成功
                            vm.get(1); // 重新获取商品列表数据
                            vm.selectedRows = []; // 清空选中行数组
                            vm.selectAll = false; // 取消全选状态
                            layer.msg(data.data.msg, {icon: 1}); // 显示成功提示
                        } else { // 如果删除操作失败
                            layer.msg(data.data.msg, {icon: 2}); // 显示失败提示
                        }
                    }, function(error) { // 请求失败的处理函数
                        layer.close(load); // 关闭加载提示
                        layer.msg('删除失败', {icon: 2}); // 显示失败提示
                    });
            });
        },
        selectAllRows: function() {
        if (this.selectAll) {
            // 如果全选复选框选中，则将所有行数据添加到选中行数组中
            this.selectedRows = this.row.data.map(res => res.cid);
        } else {
            // 如果取消全选，则清空选中行数组
            this.selectedRows = [];
        }
    },
        upkeyword: function() {
            var vm = this; // 保存 Vue 实例的引用
            if (vm.selectedRows.length === 0) {
                layer.msg('请选择要修改的项目', {icon: 2});
                return;
            }
            // 弹出包含两个输入框的表单
            layer.open({
                type: 1,
                title: '修改关键词',
                content: '<div style="padding:20px;">' +
                         '<form>' +
                         '<div class="form-group">' +
                         '<label for="keywordA">已有关键词（为空则直接在头部添加下方的关键词）:</label>' +
                         '<input type="text" id="keywordA" class="form-control" placeholder="请输入需要替换的关键词">' +
                         '</div>' +
                         '<div class="form-group">' +
                         '<label for="keywordB">替换为（或新增）:</label>' +
                         '<input type="text" id="keywordB" class="form-control" placeholder="请输入将要替换的关键词">' +
                         '</div>' +
                         '</form>' +
                         '</div>',
                btn: ['确认', '取消'],
                yes: function(index, layero) {
                    var keywordA = layero.find('#keywordA').val();
                    var keywordB = layero.find('#keywordB').val();
                    layer.confirm('确定要修改所选项目的关键词吗？', {
                        title: '确认修改',
                        icon: 3
                    }, function(confirmIndex) {
                        var load = layer.load(2);
                        vm.$http.post("/apisub.php?act=wk_update_keywords", {cid: vm.selectedRows, keywordA: keywordA, keywordB: keywordB}, {emulateJSON: true})
                            .then(function(data) {
                                layer.close(load);
                                if (data.data.code == 1) {
                                    vm.get(1);
                                    vm.selectedRows = [];
                                    vm.selectAll = false;
                                    layer.msg(data.data.msg, {icon: 1});
                                } else {
                                    layer.msg(data.data.msg, {icon: 2});
                                }
                                layer.close(index); // 关闭表单对话框
                            }, function(error) {
                                layer.close(load);
                                layer.msg('修改失败', {icon: 2});
                                layer.close(index); // 关闭表单对话框
                            });
                        layer.close(confirmIndex); // 关闭确认对话框
                    });
                },
                btn2: function(index, layero) {
                    // 点击取消按钮时关闭表单对话框
                    return true;
                }
            });
        },
	},
	mounted(){
		this.get(1);
	}
});
</script>