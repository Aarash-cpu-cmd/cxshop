<?php
$mod='blank';
$title='订单列表';
require_once('head.php');
// 在页面渲染时将用户信息传递给JavaScript
echo '<script>';
echo 'var userrow = { uid: ' . $userrow['uid'] . ' };'; // 将用户的uid传递给JavaScript
// 可以将其他用户信息也传递给JavaScript，例如：用户名、角色等
echo '</script>';
?>
<html lang="zh">
<head>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/apps.css" type="text/css" />
    <link rel="stylesheet" href="assets/css/app.css" type="text/css" />
    <link rel="stylesheet" href="assets/layui/css/layui.css" type="text/css" />
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/LightYear/js/bootstrap-multitabs/multitabs.min.css">
    <link rel="stylesheet" href="assets/LightYear/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/LightYear/css/style.min.css">
    <link rel="stylesheet" href="assets/LightYear/css/materialdesignicons.min.css">

    <!-- Scripts -->
    <script src="assets/css/vue@2.6.14"></script>
    <script src="assets/css/index.js"></script>
    <script src="../Composer/xlsx.full.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="layer/3.1.1/layer.js"></script>
</head>
<style>
.lioverhide {
     width: 300px
  }
.el-collapse-item .el-button {
    margin-right: 5px; /* 调整按钮之间的右边距 */
}

.el-collapse-item .button-container {
    display: flex; /* 使用 Flex 布局 */
    flex-wrap: wrap; /* 允许按钮换行 */
    align-content: flex-start; /* 按钮在交叉轴上上对齐 */
}
.chapter-info {
  font-size: 14px; /* 调整字体大小 */
  margin-bottom: 5px;
}
.custom-dialog {
  min-width: 60%; /* 电脑上的最小宽度为 50% */
}
.avg-score {
  font-size: 12px; /* 调整平均分字体大小 */
  color: gray; /* 设置颜色 */
}

.section-info {
  margin-top: 5px; /* 增加小节信息之间的间距 */
  font-size: 14px; /* 调整小节信息字体大小 */
}

.section-details {
  font-size: 12px; /* 调整小节详情字体大小 */
  color: gray; /* 设置颜色 */
}

.no-section {
  font-size: 14px; /* 调整暂无小节提示字体大小 */
  color: red; /* 设置颜色 */
}
/* 响应式设计 */
@media (max-width: 1080px) {
  .custom-dialog {
    width: 100%; /* 确保弹窗在手机上全宽显示 */
    margin: 0; /* 去掉外边距以避免溢出 */
    padding: 5px; /* 增加弹窗内边距 */
  }

  .chapter-info,
  .section-info,
  .section-details,
  .no-section {
    font-size: 12px; /* 在小屏幕上减小字体 */
  }

  .el-dialog {
    margin: 0;                  /* 移除外边距 */
    width: 100%;                /* 弹窗宽度占满100% */
    max-width: 100%;            /* 最大宽度也是100%，防止超出屏幕 */
    padding: 1px;               /* 添加内边距，避免内容与屏幕边缘太贴近 */
  }
}


</style>
<style>
.custom-popup {
  border-radius: 8px; /* 更小的圆角，更简约 */
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15); /* 更柔和的阴影效果 */
  padding: 16px; /* 更紧凑的内边距 */
}

.custom-popup .el-dialog__title {
  font-size: 20px; /* 标题字体稍微减小 */
  font-weight: 600; /* 字体加粗但不太突兀 */
  margin-bottom: 8px; /* 标题和内容间的间距减小 */
  color: #1F2937; /* 更现代的深灰色 */
}

.custom-popup .el-dialog__body {
  font-size: 14px; /* 保持简约的内容字体 */
  line-height: 1.5; /* 稍大的行高，提升可读性 */
  color: #4B5563; /* 现代感的中灰色 */
}

.dialog-footer {
  text-align: right; /* 右对齐保持简洁 */
  padding-top: 10px; /* 增加顶部间距 */
}

.dialog-footer .el-button {
  margin-left: 8px; /* 按钮间距减小 */
  border-radius: 4px; /* 按钮的圆角更加简约 */
  font-weight: 500; /* 按钮文字稍微加重 */
}

.dialog-footer .el-button--primary {
  color: white;
  border: none; /* 移除边框，使按钮更简洁 */
}
.custom-dialog {
  font-family: 'Arial', sans-serif;
  color: #333;
}

.dialog-content {
  background-color: #f9f9f9; /* 背景颜色 */
  border-radius: 8px; /* 圆角 */
}

.chapter-info {
  margin-bottom: 15px;
}

.chapter-name {
  font-size: 1.2em; /* 加大章节名称字体 */
  color: #2c3e50; /* 深色字体 */
}

.avg-score {
  color: #7f8c8d; /* 淡色字体 */
}

.section-info {
  margin: 10px 0; /* 小节之间的间距 */
  padding: 10px;
  border: 1px solid #e0e0e0; /* 小节边框 */
  border-radius: 5px; /* 小节圆角 */
  background-color: #fff; /* 小节背景 */
  transition: background-color 0.3s;
}

.section-info:hover {
  background-color: #ecf0f1; /* 悬停时的背景颜色 */
}

.section-name {
  font-weight: bold; /* 小节名称加粗 */
}

.section-details {
  font-size: 0.9em; /* 小节详细信息字体 */
  color: #34495e; /* 小节详细信息颜色 */
}

.no-section {
  color: #e74c3c; /* 暂无小节颜色 */
  text-align: center; /* 居中对齐 */
}

.detail-value {
  font-weight: bold; /* 详细信息加粗 */
}

</style>

</html>
<body>
         
        <div class="wrapper-md control">
        	
	    <div class="panel panel-default" id="orderlist">
		    <div class="panel-heading font-bold bg-white">记录查询 (查询平台已有记录)</div>
				<div class="panel-body">
                        <div class="form-horizontal devform">
				 		   	<div class="form-group layui-col-space15">
                                <div class="col-sm-1 col-xs-4" style="width: 50%;">
                                          <el-select v-model="cx.cid" placeholder="点击可搜索平台" popper-class="lioverhide" style="background: url('../index/arrow.png') no-repeat scroll 99%;width: 100%;" filterable>
                                          <el-option label="点击可搜索平台" value=""></el-option>
                                          <?php
                                          $a = $DB->query("select * from qingka_wangke_class where status=1");
                                          while ($row = $DB->fetch($a)) {
                                          echo '<el-option label="' . $row['name'] . '" value="' . $row['cid'] . '"></el-option>';
                                          }
                                          ?>
                                          </el-select>
                            </div>
                                <div class="col-sm-1 col-xs-4" style="width: 50%;">
    			                    <el-select v-model="cx.limit" placeholder="每页订单数量，默认25条" style="background: url('../index/arrow.png') no-repeat scroll 99%;width: 100%;">
    			                        <el-option label="每页订单数量" value=""></el-option>
                                        <el-option label="25条" value="25"></el-option>
                                        <el-option label="50条" value="50"></el-option>
                                        <el-option label="100条" value="100"></el-option>
                                        <el-option label="200条" value="200"></el-option>
                                        <el-option label="500条" value="500"></el-option>
                                        <el-option v-if="row.uid==1" label="1000条" value="1000"></el-option>
                                    </el-select>
    				         </div>
    				         <div class="col-sm-1 col-xs-4" style="width: 50%;">
                                <el-input v-model="cx.kcname" placeholder="请输入课程名称"></el-input>
                            </div>
                            </div>
				            <div class="form-group">
				 	                <div class="layui-row">
			                            <div class="col-sm-1 col-xs-4" style="width: 100%;">
                        		            <el-input placeholder="模糊查询" v-model="cx.mh" class="input-with-select">
                                                <el-select v-model="cx.search" style="width:100px" placeholder="条件" slot="prepend" >
                                                    <el-option label="所有" value=""></el-option>
                                                    <el-option label="项目名称" value="ptname" ></el-option>
                                                    <el-option label="课程名称" value="kcname"></el-option>
                                                </el-select>
                                                <el-button slot="append" icon="el-icon-search" @click="get(1)">搜索</el-button>
                                            </el-input>
                                            <div style="height:5px"></div>
                        				</div>
				                    </div>
			             
			             </div>
			            </div>

                    </div>
                        
                  
		        <div class="layui-table  table-responsive table-condensed" lay-size="sm" >
		        <table class="table table-striped" style="margin-left: 5px;margin-right: 5px;">
		          <thead style="white-space:nowrap"><tr>
		          	<th>操作</th>
		          	<th class="tac" width="100">平台</th>
		          	<th>账号</th>
		          	<th>课程名称</th>
		          	<th>任务状态</th>
		          	<th class="tac" width="130">详细订单实时进度</th>
		          	<th>详细备注</th>
		          	<th>提交时间</th>
		          	</tr>
				  </thead>
				<tbody>
		              
		            <tr v-for="res in row.data">
                    <td style="text-align: center; white-space: nowrap;">
                        <div style="display: grid;grid-template-columns: repeat(2, 1fr);gap: 5px;">
                            <button @click="up(res.oid)" class="btn btn-xs btn-cyan" style="margin-bottom: 5px;">刷新</button>
                        </div>
                    </td>


                        <td style="text-align: center;" v-else>-</td>
		            	
		            	
		            	<td style="text-align: center;">{{res.ptname}}<span v-if="res.miaoshua=='1'" style="color: red;">&nbsp;秒刷</span></td>	            	      	
		            	<td>**********
		            	</td>
		            	<td>{{res.kcname}}</td>
		            	<td style="text-align: center;white-space:nowrap;line-height:50px">
							<span v-if="res.status=='已完成'"
								class="el-button el-button--success   is-plain el-button--mini"
								style="padding: 4px 10px;"  @click="ddinfo(res)">{{res.status}}</span>
						</td>
		            	</td>
		            		<td>
        		              <div class="layui-progress layui-progress-big" @click="up(res.oid)" lay-showPercent="yes">
                              <div class="layui-progress-bar layui-bg-success"  v-bind:style="{width: res.process}"><span class="layui-progress-text">{{res.process}}</span></div>
		            	    </td>
		            	    
		            	    
		            	<td>{{res.remarks}}</td>
		            	<td style="text-align: center;">{{res.addtime}}</td>
		            
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

                    <el-dialog
                      :title="popupTitle"
                      :visible.sync="isPopupVisible"
                      custom-class="custom-popup"
                      @close="handlePopupClose"
                    >
                      <div v-html="popupContent2"></div>
                      <div slot="footer" class="dialog-footer">
                        <el-button @click="handleCancel">不再显示</el-button>
                        <el-button type="primary" @click="handleConfirm">确定</el-button>
                      </div>
                    </el-dialog>
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
<script type="text/javascript" src="assets/LightYear/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>
<script src="assets/vue.min.js"></script>
<script src="assets/vue-resource.min.js"></script>
<script src="assets/axios.min.js"></script>
<script src="assets/layui/layui.js"></script>
<script src="assets/js/element.js"></script>


<script type="text/javascript">
   var uidFromPHP = <?php echo json_encode($uid); ?>;
    vm = new Vue({
        el: "#orderlist",
        data: {
            row: null,
            userrow: userrow,
            cx: {
                qq: '',
                mh: '',
                search: '',
                kcname: '',
                limit: ''
            },
            isPopupVisible: false,
            popupTitle: '',
            popupContent2: '',
            gonggaoList: []
        },
        methods: {
            get: function (page) {
                var load = layer.load(2);
                var apiUrl = "/apisub.php?act=";
                // 根据 this.cx.uid 的值选择不同的接口
                apiUrl += this.userrow.uid == 1 ? "user_orderlist1" : "user_orderlist1";
                var data = { cx: this.cx, page: page };

                this.$http.post(apiUrl, data, { emulateJSON: true }).then(function (data) {
                    layer.close(load);
                    if (data.data.code == 1) {
                        this.row = data.body;
                    } else {
                        layer.msg(data.data.msg, { icon: 2 });
                    }
                });
            },
            fetchGonggaoList: function () {
              this.$http.post('/apisub.php?act=getgonggaolist', { emulateJSON: true })
                .then((response) => {
                  if (response.data.code === 1) {
                    this.gonggaoList = response.data.data;
                    this.showPopup();
                  } else {
                    this.$message.error(response.data.msg);
                  }
                });
            },
            showPopup: function () {
              const popupData = this.gonggaoList.find(gonggao => gonggao.Type === '查询可用');
              this.popupTitle = popupData?.Title || '没有新公告';
              this.popupContent2 = popupData?.Content || '';
              const storedContent = localStorage.getItem('popupContent2');
              
              if (storedContent !== this.popupContent2) {
                this.isPopupVisible = true;  // 显示弹窗
              }
            },
            handlePopupClose: function () {
              this.isPopupVisible = false;
            },
            handleCancel: function () {
              localStorage.setItem('popupContent2', this.popupContent2);
              this.isPopupVisible = false;
              this.$message({
                message: '好的，下次不再提醒',
                type: 'success'
              });
            },
            handleConfirm: function () {
              this.isPopupVisible = false;
              this.$message({
                message: '好的，祝您自由！',
                type: 'success'
              });
            }
                    }, mounted() {
                        this.get(1);
                        this.fetchGonggaoList();
                    }
                });
</script>