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
		    <div class="panel-heading font-bold bg-white">任务列表 (状态同步不及时，有时需手动更新)</div>
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
                                    <el-select v-model="cx.status_text" placeholder="任务状态" style="background: url('../index/arrow.png') no-repeat scroll 99%;width: 100%;"filterable>
                                          <el-option label="任务状态" value=""></el-option>
                                          
                                          <?php
                                            if($userrow['uid'] != 1){
                                                // 如果不是管理员，按照用户ID分组并计算每个状态的出现次数
                                                $query = "SELECT status, COUNT(*) as status_count FROM qingka_wangke_order WHERE uid = " . intval($userrow['uid']) . " GROUP BY status ORDER BY status_count DESC";
                                            } else {
                                                // 如果是管理员，不按用户ID分组，计算每个状态的出现次数
                                                $query = "SELECT status, COUNT(*) as status_count FROM qingka_wangke_order GROUP BY status ORDER BY status_count DESC";
                                            }
                                            $a = $DB->query($query);
                                            
                                            while ($row = $DB->fetch($a)) {
                                                // 使用htmlspecialchars转义以防止XSS攻击
                                                echo '<el-option label="' . htmlspecialchars($row['status']) . ' (' . $row['status_count'] . ')" value="' . htmlspecialchars($row['status']) . '"></el-option>';
                                            }
                                            ?>
                                    </el-select>
                                </div>
                                <div class="col-sm-1 col-xs-4" style="width: 50%;">
                                    <el-select v-model="dc2.gs" placeholder="选择导出格式" style="background: url('../index/arrow.png') no-repeat scroll 99%;width: 100%;">
                                          <el-option label="选择导出格式" value=""></el-option>
                                          <el-option label="学校+账号+密码+课程名字" value="1"></el-option>
                                          <el-option label="账号+密码+课程名字" value="2"></el-option>
                                          <el-option label="学校+账号+密码" value="3"></el-option>
                                          <el-option label="账号+密码" value="4"></el-option>
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
                                <div class="col-sm-1 col-xs-4" style="width: 50%;" v-if="row.uid==1">
                                        <el-select v-model="cx.dock" placeholder="处理状态" style="background: url('../index/arrow.png') no-repeat scroll 99%;width: 100%;">
                                          <el-option label="选择处理状态" value=""></el-option>
                                          <el-option label="待处理" value="0"></el-option>
                                          <el-option label="处理成功" value="1"></el-option>
                                          <el-option label="处理失败" value="2"></el-option>
                                          <el-option label="重复下单" value="3"></el-option>
                                          <el-option label="已取消" value="4"></el-option>
                                          <el-option label="自营" value="99"></el-option>
                                        </el-select>
                                      </div>
                            </div>
				                <div class="form-group">
				 	                <div class="layui-row">
			                            <div class="col-sm-1 col-xs-4" style="width: 100%;">
                        		            <el-input placeholder="模糊查询" v-model="cx.mh" class="input-with-select">
                                                <el-select v-model="cx.search" style="width:100px" placeholder="条件" slot="prepend" >
                                                    <el-option label="所有" value=""></el-option>
                                                    <el-option label="UID" value="uid" v-if="row.uid === 1"></el-option>
                                                    <el-option label="订单ID" value="oid" v-if="row.uid === 1"></el-option>
                                                    <el-option label="学校" value="school"></el-option>
                                                    <el-option label="账号" value="user"></el-option>
                                                    <el-option label="课程名称" value="kcname"></el-option>
                                                    <el-option label="进度条" value="process"></el-option>
                                                    <el-option label="详细进度" value="remarks"></el-option>
                                                </el-select>
                                                <el-button slot="append" icon="el-icon-search" @click="get(1)">搜索</el-button>
                                            </el-input>
                                            <div style="height:5px"></div>
                        				</div>
				                    </div>
			             
			             </div>
			            </div>
			           <el-collapse v-if="row.uid==1">
                          <el-collapse-item title="任务状态">
                        <div class="button-container">
                            <el-button size="small" type="primary" @click="selectAll()" style="margin-right: 5px; margin-bottom: 5px;">全选</el-button>
                            <el-button size="small" type="info" @click="status_text('待处理')" style="margin-right: 5px; margin-bottom: 5px;">待处理</el-button>
                            <el-button size="small" type="success" @click="status_text('已完成')" style="margin-right: 5px; margin-bottom: 5px;">已完成</el-button>
                            <el-button size="small" type="warning" @click="status_text('进行中')" style="margin-right: 5px; margin-bottom: 5px;">进行中</el-button>
                            <el-button size="small" type="danger" @click="status_text('异常')" style="margin-right: 5px; margin-bottom: 5px;">异常</el-button>
                            <el-button size="small" type="danger" @click="status_text('密码错误')" style="margin-right: 5px; margin-bottom: 5px;">密码错误</el-button>
                            <el-button size="small" type="dark" @click="status_text('已退单')" style="margin-right: 5px; margin-bottom: 5px;">已退单</el-button>
                            <el-button size="small" type="default" @click="status_text('已取消')" style="margin-right: 5px; margin-bottom: 5px;">已取消</el-button>
                            <el-button size="small" type="danger" @click="status_text('未授权')" style="margin-right: 5px; margin-bottom: 5px;">未授权</el-button>
                            <el-button size="small" type="danger" @click="status_text('待验证')" style="margin-right: 5px; margin-bottom: 5px;">待验证</el-button>
                            <span style="margin-top: 10px;">注：勾选订单后才能修改任务状态或处理状态，会后端执行</span>
                            </div>
                          </el-collapse-item>
                        
                          <el-collapse-item title="处理状态">
                        <div class="button-container">
                            <el-button size="small" type="info" @click="dock(0)" style="margin-right: 5px; margin-bottom: 5px;">待处理</el-button>
                            <el-button size="small" type="success" @click="dock(1)" style="margin-right: 5px; margin-bottom: 5px;">已完成</el-button>
                            <el-button size="small" type="danger" @click="dock(2)" style="margin-right: 5px; margin-bottom: 5px;">处理失败</el-button>
                            <el-button size="small" type="warning" @click="dock(3)" style="margin-right: 5px; margin-bottom: 5px;">重复下单</el-button>
                            <el-button size="small" type="default" @click="dock(4)" style="margin-right: 5px; margin-bottom: 5px;">取消</el-button>
                            <el-button size="small" type="default" @click="del(sex)" style="margin-right: 5px; margin-bottom: 5px;">删除</el-button>
                            <el-button size="small" type="default" @click="dock(99)" style="margin-right: 5px; margin-bottom: 5px;">我的</el-button>
                            <el-button size="small" type="danger" @click="tk(sex)" style="margin-right: 5px; margin-bottom: 5px;">退款</el-button>
                            </div>
                          </el-collapse-item>
                        </el-collapse>
                        			            <div class="bg-gradient-tron">
                                                批量操作:<br/>
                                                <template>
                          <div>
                            <!-- 批量更新按钮 -->
                            <el-tooltip content="批量更新订单进度和详细备注" placement="top">
                              <el-button size="small" type="primary" @click="plup('待更新')">批量更新</el-button>
                            </el-tooltip>
                        
                            <!-- 批量补刷按钮 -->
                            <el-tooltip content="批量补刷，漏刷/卡单/异常时使用，勿频繁点击" placement="top">
                              <el-button size="small" type="success" @click="plbs('待重刷')">批量补刷</el-button>
                            </el-tooltip>
                        
                            <!-- 导出订单按钮 -->
                            <el-tooltip content="点击导出订单，支持文本导出和表格导出" placement="top">
                              <el-button size="small" type="warning" @click="showExportDialog">导出订单</el-button>
                            </el-tooltip>
                            
                            <!-- 修改备注按钮 -->
                            <el-tooltip v-if="row.uid==1" content="点击修改备注" placement="top">
                              <el-button size="small" type="danger" @click="xgbz">修改备注</el-button>
                            </el-tooltip>
                            <!-- 修改密码按钮 -->
                            <el-tooltip  content="点击修改密码" placement="top">
                              <el-button size="small" type="danger" @click="xgmm">修改密码</el-button>
                            </el-tooltip>
                          </div>
                        </template>

                        <br>
                        <p style="color: red; font-size: 14px; font-weight: bold;">
                        注：勾选订单后才能进行批量操作，操作后订单会加入后端排队执行，5分钟内点击更新可取消重刷
                        </p>
                        </div>
                        
                  
		           <div class="layui-table  table-responsive table-condensed" lay-size="sm" >
		        <table class="table table-striped">
		          <thead style="white-space:nowrap"><tr><!--<th>#</th>-->
		          	<th><label class="lyear-checkbox checkbox-inline checkbox-success">
                           <input type="checkbox" id="checkboxAll"  @click="selectAll()"><span>ID</span>
                       </label>
					</th>
		          	<th>操作</th>
		          	<th class="tac" width="100">平台</th>
		          	<th>学校&nbsp;账号&nbsp;密码</th>
		          	<th>课程名称</th>
		          	<th>任务状态</th>
		          	<th class="tac" width="130">详细订单实时进度</th>
		          	<th>详细备注</th>
		          	<th>提交时间</th>
		          	<th v-if="row.uid==1">处理状态</th>
		          	<th v-if="row.uid==1">UID</th>
		          	<th>金额</th>
		          	</tr>
							</thead>
						
							<tbody>
		              
		            <tr v-for="res in row.data">		            					
								  <!--<td>
								  	<span class="checkbox checkbox-success">
			              <input type="checkbox" id="checkboxAll" :value="res.oid" v-model="sex"><label for="checkbox1"></label></span>
								  </td>-->   
								 
					
                    <td style="white-space:nowrap">
                        <label class="lyear-checkbox checkbox-inline checkbox-success">
                            <input type="checkbox" id="checkboxAll" :value="res.oid" v-model="sex">
                            <span v-if="row.uid==1">{{res.oid}}</span>
                            <span v-else>-</span>
                        </label>
                    </td>
                    <td style="text-align: center; white-space: nowrap;">
                        <div style="display: grid;grid-template-columns: repeat(2, 1fr);gap: 5px;">
                            <span class="btn btn-xs btn-dark" @click="bs(res.oid)" style="margin-bottom: 5px;">重刷</span>
                            <button @click="up(res.oid)" class="btn btn-xs btn-cyan" style="margin-bottom: 5px;">更新</button>
                            <button @click="feedback(res.oid)" class="btn btn-xs btn-info" style="margin-bottom: 5px;">反馈</button>
                            <div v-if="(res.cid === '32' || res.cid === '33' || res.cid === '38') && res.status === '异常' ">
                                <span class="btn btn-xs btn-danger" @click="bc(res.remarks)" style="margin-bottom: 5px;">补充</span>
                            </div>
                            
                            <div v-else-if="res.cid === '3027'">
                                <button @click="up(res.oid)" class="btn btn-xs btn-info" style="margin-bottom: 5px;">更新</button>
                            </div>
                            <div v-if="res.ptname.includes('学习通') ">
			       	            <button @click="getzjcs(res.oid)" style="margin-bottom: 5px;"class="btn btn-xs btn-primary">章节</button>
                            </div>
                            <div v-if="res.hid=='101' ">
			       	            <button @click="tz(res.oid)" class="btn btn-xs btn-danger" style="margin-bottom: 5px;">停止</button>
                            </div>
                            <div v-else-if="res.hid=='101' || res.hid=='109'">
                                <button @click="tz(res.oid)" class="btn btn-xs btn-danger" style="margin-bottom: 5px;">停止</button>
                            </div>
                            
                            <div v-else-if="res.cid === '201' || res.cid === '202' || res.cid === '203'">
                                <div v-if="res.status === '已完成'">
                                    <a :href="res.href" class="btn btn-xs btn-default" style="margin-bottom: 5px;">保存</a>
                                </div>
                                <button @click="TgTips()" class="btn btn-xs btn-warning" style="margin-bottom: 5px;">教程</button>
                            </div>
                            
                            <div v-else-if="res.cid === '4752' || res.cid === '4753'">
                                <div v-if="res.status === '已完成'">
                                    <a :href="'http://exam.hm86.xyz/jietu/exam.hm86.cn/zhengshu.php?username=' + res.user" class="btn btn-xs btn-default" style="margin-bottom: 5px;">证书</a>
                                </div>
                            </div>
                        </div>
                    </td>


                        <td style="text-align: center;" v-else>-</td>
		            	
		            	
		            	<td style="text-align: center;">{{res.ptname}}<span v-if="res.miaoshua=='1'" style="color: red;">&nbsp;秒刷</span></td>	            	      	
		            	<td>{{res.school}}
		                     {{res.user}}
		            	     {{res.pass}}
		            	</td>
		            	<td>{{res.kcname}}</td>
		            			<td style="text-align: center;white-space:nowrap;line-height:50px">
										<span v-if="res.status=='待处理'"
											class="el-button el-button--warning   is-plain el-button--mini"
											style="padding: 4px 10px;" @click="ddinfo(res)">{{res.status}}</span>
										<span v-else-if="res.status=='已完成'"
											class="el-button el-button--success   is-plain el-button--mini"
											style="padding: 4px 10px;"  @click="ddinfo(res)">{{res.status}}</span>

										<span v-else-if="res.status=='异常'"
											class="el-button el-button--danger  is-plain el-button--mini"
											style="padding: 4px 10px;" @click="ddinfo(res)">{{res.status}}</span>

										<span v-else-if="res.status=='进行中'"
											class="el-button el-button--primary is-plain el-button--mini"
											style="padding: 4px 10px;" @click="ddinfo(res)">{{res.status}}</span>
										<span v-else-if="res.status=='待考试'"
											class="el-button el-button--primary is-plain el-button--mini"
											style="padding: 4px 10px;" @click="ddinfo(res)">{{res.status}}</span>

										<span v-else class="el-button el-button--info is-plain el-button--mini"
											style="padding: 4px 10px;" @click="ddinfo(res)">{{res.status}}</span>
									</td>
		            	</td>
		            		<td>
        		              <div class="layui-progress layui-progress-big" @click="up(res.oid)" lay-showPercent="yes">
                              <div class="layui-progress-bar layui-bg-success"  v-bind:style="{width: res.process}"><span class="layui-progress-text">{{res.process}}</span></div>
		            	    </td>
		            	    
		            	    
		            	<td>{{res.remarks}}</td>
		            	<td style="text-align: center;">{{res.addtime}}</td>
		            	<td nowrap="nowrap" v-if="row.uid==1" style="text-align: center;">
		            		<span @click="duijie(res.oid)" v-if="res.dockstatus==0" class="btn btn-xs btn-info">待处理</span>
		            		<span v-if="res.dockstatus==1" class="btn btn-xs btn-success">处理成功</span>
		            		<span @click="duijie(res.oid)" v-if="res.dockstatus==2" class="btn btn-xs btn-danger">处理失败</span>
		            		<span v-if="res.dockstatus==3" class="">重复下单</span>
		            		<span v-if="res.dockstatus==4" class="">已取消</span>
		            		<span v-if="res.dockstatus==99" class="btn btn-xs btn-warning">自营</span></td>
		            	
		            		<td v-if="row.uid==1" style="text-align: center;">{{res.uid}}</td>
		            		<td style="text-align: center;">{{res.fees}}</td>
		            
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
			     
   
			    <div id="ddinfo2" style="display: none;"><!--订单详情-->                    
			       <li class="list-group-item">
			       	<b>课程类型：</b>{{ddinfo3.info.ptname}}<span v-if="ddinfo3.info.miaoshua=='1'" style="color: red;">&nbsp;秒刷</span></li>
			       	<li class="list-group-item" style="word-break:break-all;"><b>账号信息：</b>{{ddinfo3.info.school}}&nbsp;{{ddinfo3.info.user}}&nbsp;{{ddinfo3.info.pass}}</li>
			       	<li class="list-group-item"><b>课程名字：</b>{{ddinfo3.info.kcname}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.name!='null'"><b>学生姓名：</b>{{ddinfo3.info.name}}</li>
			       	<li class="list-group-item"><b>下单时间：</b>{{ddinfo3.info.addtime}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.courseStartTime"><b>课程开始时间：</b>{{ddinfo3.info.courseStartTime}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.courseEndTime"><b>课程结束时间：</b>{{ddinfo3.info.courseEndTime}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.examStartTime"><b>考试开始时间：</b>{{ddinfo3.info.examStartTime}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.examEndTime"><b>考试结束时间：</b>{{ddinfo3.info.examEndTime}}</li>
			       	<li class="list-group-item"><b>订单状态：</b><span style="color: red;">{{ddinfo3.info.status}}</span>&nbsp;<button v-if="ddinfo3.info.dockstatus!='99'" @click="up(ddinfo3.info.oid)" class="btn btn-xs btn-success">更新</button>&nbsp;</li>
			       	<li class="list-group-item"><b>实时进度：</b>{{ddinfo3.info.process}}</li>
			       	<li class="list-group-item"><b>当前状态：</b>{{ddinfo3.info.remarks}}</li>
			       	<li class="list-group-item"><b>下单金额：</b>{{ddinfo3.info.fees}}</li>
			       	<li class="list-group-item" v-if="ddinfo3.info.status!='已取消'">
			       	    <b>操作：</b>
			       	    <span>
			       	        <button @click="bs(ddinfo3.info.oid)" class="btn btn-xs btn-primary">补单</button>
			       	        <button @click="dd_passwd(ddinfo3.info.oid)" class="btn btn-xs btn-info">修改密码</button>
			       	        <button v-if="ddinfo3.info.cid === '3' || ddinfo3.info.cid === '4' || ddinfo3.info.cid === '5' || ddinfo3.info.cid === '23' || ddinfo3.info.hid === '101' || ddinfo3.info.hid === '109'"@click="tz(ddinfo3.info.oid)" class="btn btn-xs btn-danger">停止</button>
			       	        <button @click="feedback(ddinfo3.info.oid)" class="btn btn-xs btn-info">反馈</button>
			       	        <button @click="quxiao(ddinfo3.info.oid)"  class="btn btn-xs btn-default">取消</button>
			       	    </span>
			       	</li>		       	  
		      </div>

              <el-dialog title="章节测试信息" :visible.sync="dialogVisible" class="custom-dialog" :close-on-click-modal="false" :modal="true" destroy-on-close="true">
                  <div class="dialog-content" style="max-height: 400px; overflow-y: auto; padding: 5px;">
                    <!-- 表格展示章节信息 -->
                    <el-table :data="tableData" style="width: 100%" border>
                      <el-table-column label="章节" prop="chapter.name" min-width="40%">
                        <template slot-scope="scope">
                          <div v-if="scope.row.chapter" class="chapter-info">
                            <strong class="chapter-name">{{ scope.row.chapter.name }}</strong>
                            <div class="avg-score">平均分: <span class="score">{{ scope.row.chapter.avgScore }}</span></div>
                          </div>
                        </template>
                      </el-table-column>
                
                      <el-table-column label="小节" min-width="100%">
                        <template slot-scope="scope">
                          <div v-if="scope.row.sections.length > 0">
                            <div v-for="section in scope.row.sections" :key="section.workId" class="section-info">
                              <span class="section-name">{{ section.chapterName }}</span>
                              <div class="section-details">
                                总分: <span class="detail-value">{{ section.fullScore }}</span> | 得分: <span class="detail-value">{{ section.scoreStr }}</span> | 完成时间: <span class="detail-value">{{ section.createTime }}</span> | 状态: <span class="detail-value">{{ section.statusStr }}</span>
                              </div>
                            </div>
                          </div>
                          <div v-else class="no-section" @click="addSection(scope.row.chapter.id)" style="cursor: pointer;">
                            <el-tag type="info">暂无小节数据，点击添加小节</el-tag>
                          </div>
                        </template>
                      </el-table-column>
                    </el-table>
                  </div>
                
                  <span slot="footer" class="dialog-footer">
                    <el-button type="primary" @click="dialogVisible = false">关闭</el-button>
                  </span>
                </el-dialog>

                    <el-dialog
                      :title="popupTitle"
                      :visible.sync="isPopupVisible"
                      custom-class="custom-popup"
                      @close="handlePopupClose"
                    >
                      <div v-html="popupContent"></div>
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
  var platformOptions = <?php
        $options = [];
        $a = $DB->query("select * from qingka_wangke_class where status=1");
        while($row = $DB->fetch($a)) {
            $options[] = ['value' => $row['cid'], 'label' => $row['name']];
        }
        echo json_encode($options);
    ?>;
    vm = new Vue({
        el: "#orderlist",
        data: {
            row: null,
            phone: '',
            sex: [],
            userrow: userrow,
            platformOptions: platformOptions,
            ddinfo3: {
                status: false,
                info: []
            },
            dc: [],
            dc2: {
                gs: ''
            },
            cx: {
                status_text: '',
                dock: '',
                qq: '',
                mh: '',
                search: '',
                oid: '',
                uid: '',
                school: '',
                kcname: '',
                cid: '',
                limit: ''
            },
            dialogVisible: false, // 控制弹窗是否显示
            tableData: [],
            isPopupVisible: false,
            popupTitle: '',
            popupContent: '',
            gonggaoList: []
        },
        methods: {
            get: function (page) {
                var load = layer.load(2);
                var apiUrl = "/apisub.php?act=";
                // 根据 this.cx.uid 的值选择不同的接口
                apiUrl += this.userrow.uid == 1 ? "FDorderlist" : "user_orderlist";
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
            TgTips: function () {
                layer.alert('1.可以点编辑目录推荐目录的基础上修改或者新增 [<span style="color:red;">章节/小节标题以及字数</span>] <br>2.成为目标目录，系统将通过目标目录为你撰写文章，注意格式 <br>3.可以自己稍微修改格式也可以让客户自己改<span style="color:red;">模板定制联系上级</span> <br>4.修改内容可以找上级要修改地址，能过98%论文网址查重 <br><span style="color:red;">5.欢迎尝试，显示完成即可点保存下载！</span> <br><span style="color:red;"><strong>6.因编辑目录存在修改性，</strong><strong>已完成后重刷会失效！</strong></span> <br><span style="color:red;">7.如果显示失败点重刷，重新编辑即可，</span><span ><br>一直失败请反馈上级！</span>', {

                    title: '论文订单教程规则',
                    skin: 'layui-layer-molv layui-layer-wxd'
                    , shadeClose: true
                });
            },
            jietu: function (user) {
                // window.location.href="/jietu/exam.hm86.cn/examination.php?paperid=1000000001&usercodepaperid=M2T0A2w1M-D1d1f-M1T6AhwmMsDocfwtMjc4NV8xMDAwMDAwMDAx&oid="+oid
                window.open("http://185.248.86.75:9211/jinagsu_aqjy.php?username=" + res.user, "_blank");
            },
            lunw: function () {
                layer.alert('自动发货到邮箱本地不保存', {

                    title: '论文订单教程规则',
                    skin: 'layui-layer-molv layui-layer-wxd'
                    , shadeClose: true
                });
            },
            bianji: function () {
                // window.location.href="/jietu/exam.hm86.cn/examination.php?paperid=1000000001&usercodepaperid=M2T0A2w1M-D1d1f-M1T6AhwmMsDocfwtMjc4NV8xMDAwMDAwMDAx&oid="+oid
                window.open('https://eee.vg/content/', '_blank');
            }, del: function (sex) {
                layer.confirm('此操作会删除选中的订单,金额不会退回,请谨慎操作！', { btn: ['确定', '取消'], title: '删除确认', icon: 3 }, function (index) {
                    var load = layer.load();
                    $.post("/apisub.php?act=delorder", { sex: sex }, { emulateJSON: true }).then(function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.selectAll();
                            vm.get(vm.row.current_page);
                            layer.msg(data.msg, { icon: 1 });
                        } else {
                            layer.msg(data.msg, { icon: 2 });
                        }
                    });
                });
            }, bs: function (oid) {
                layer.confirm('建议漏看或者进度被重置的情况下使用。<br>频繁点击补刷会出现不可预测的结果<br>请问是否补刷所选的任务？', {
                    title: '温馨提示', icon: 3,
                    btn: ['确定补刷', '取消'] //按钮
                }, function () {
                    var load = layer.load(2);
                    $.get("/apisub.php?act=bs&oid=" + oid, function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.get(vm.row.current_page);
                            layer.alert(data.msg, { icon: 1 });
                        } else {
                            layer.msg(data.msg, { icon: 2 });
                        }
                    });
                });
            }, tz: function (oid) {
                layer.confirm('请问是否停止所选的任务？', {
                    title: '温馨提示', icon: 3,
                    btn: ['确定停止', '取消'] //按钮
                }, function () {
                    var load = layer.load(2);
                    $.get("/apisub.php?act=iktz&oid=" + oid, function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.get(vm.row.current_page);
                            layer.alert(data.msg, { icon: 1 });
                        } else {
                            layer.msg(data.msg, { icon: 2 });
                        }
                    });
                });
            },up: function (oid) {
                var load = layer.load(2);
                layer.msg("正在努力获取中....", { icon: 3 });
                $.get("/apisub.php?act=uporder&oid=" + oid, function (data) {
                    layer.close(load);
                    if (data.code == 1) {
                        vm.get(vm.row.current_page);
                        setTimeout(function () {
                            for (i = 0; i < vm.row.data.length; i++) {
                                if (vm.row.data[i].oid == oid) {
                                    vm.ddinfo3.info = vm.row.data[i];
                                    console.log(vm.row.data[i].oid);
                                    console.log(vm.row.data[i].status);
                                    console.log(vm.ddinfo3.info.status);
                                    return true;
                                }
                            }
                        }, 1800);
                        layer.msg(data.msg, { icon: 1 });
                    } else {
                        layer.msg(data.msg, { icon: 2 });
                        //	                layer.alert(data.msg,{icon:2,btn:'立即跳转'},function(){
                        //	                	window.location.href=data.url
                        //	                });
                    }
                });
            }, duijie: function (oid) {
                layer.confirm('确定处理么?', {
                    title: '温馨提示', icon: 3,
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    var load = layer.load(2);
                    $.get("/apisub.php?act=duijie&oid=" + oid, function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.get(vm.row.current_page);
                            layer.alert(data.msg, { icon: 1 });
                        } else {
                            layer.msg(data.msg, { icon: 2 });
                        }
                    });
                });
            }, ms: function (oid) {
                layer.confirm('提交秒刷将扣除0.05元服务费', {
                    title: '温馨提示', icon: 3,
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    var load = layer.load(2);
                    $.get("/apisub.php?act=ms_order&oid=" + oid, function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.get(vm.row.current_page);
                            layer.alert(data.msg, { icon: 1 });
                        } else {
                            layer.msg(data.msg, { icon: 2 });
                        }
                    });
                });
            }, quxiao: function (oid) {
                layer.confirm('取消订单将无法退款，确定取消吗', {
                    title: '温馨提示', icon: 3,
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    var load = layer.load(2);
                    $.get("/apisub.php?act=qx_order&oid=" + oid, function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.get(vm.row.current_page);
                            layer.alert(data.msg, { icon: 1 });
                        } else {
                            layer.msg(data.msg, { icon: 2 });
                        }
                    });
                });
            }, plup: function (a) {
                var load = layer.load();
                $.post("/apisub.php?act=plup&a=" + a, { sex: this.sex, type: 1 }, { emulateJSON: true }).then(function (data) {
                    layer.close(load);
                    if (data.code == 1) {
                        vm.selectAll();
                        vm.get(vm.row.current_page);
                        layer.msg(data.msg, { icon: 1 });
                    } else {
                        layer.msg(data.msg, { icon: 2 });
                    }
                });
            }, plbs: function (a) {
                var load = layer.load();
                $.post("/apisub.php?act=plbs&a=" + a, { sex: this.sex, type: 1 }, { emulateJSON: true }).then(function (data) {
                    layer.close(load);
                    if (data.code == 1) {
                        vm.selectAll();
                        vm.get(vm.row.current_page);
                        layer.msg(data.msg, { icon: 1 });
                    } else {
                        layer.msg(data.msg, { icon: 2 });
                    }
                });
            }, status_text: function (a) {
                var load = layer.load(2);
                $.post("/apisub.php?act=status_order&a=" + a, { sex: this.sex, type: 1 }, { emulateJSON: true }).then(function (data) {
                    layer.close(load);
                    if (data.code == 1) {
                        vm.selectAll();
                        vm.get(vm.row.current_page);
                        layer.msg(data.msg, { icon: 1 });
                    } else {
                        layer.msg(data.msg, { icon: 2 });
                    }
                });
            }, dock: function (a) {
                var load = layer.load(2);
                $.post("/apisub.php?act=status_order&a=" + a, { sex: this.sex, type: 2 }, { emulateJSON: true }).then(function (data) {
                    layer.close(load);
                    if (data.code == 1) {
                        vm.selectAll();
                        vm.get(vm.row.current_page);
                        layer.msg(data.msg, { icon: 1 });
                    } else {
                        layer.msg(data.msg, { icon: 2 });
                    }
                });
            },
            selectAll: function () {
                if (this.sex.length == 0) {
                    for (i = 0; i < vm.row.data.length; i++) {
                        vm.sex.push(this.row.data[i].oid)
                    }
                } else {
                    this.sex = []
                }
            },
            ddinfo: function (a) {
                this.ddinfo3.info = a;
                var load = layer.load(2, { time: 300 });
                setTimeout(function () {
                    layer.open({
                        type: 1,
                        title: '订单详情操作',
                        skin: 'layui-layer-demo',
                        closeBtn: 1,
                        anim: 2,
                        shadeClose: true,
                        content: $('#ddinfo2'),
                        end: function () {
                            $("#ddinfo2").hide();
                        }
                    });
                }, 100);

            },
            tk: function (sex) {
                if (this.sex == '') { layer.msg("请先选择订单！"); return false; }
                layer.confirm('确定要退款吗？陛下，三思三思！！！', { title: '温馨提示', icon: 3, btn: ['确定', '取消'] }, function () {
                    var load = layer.load();
                    $.post("/apisub.php?act=tk", { sex: sex }, { emulateJSON: true }).then(function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.selectAll();
                            vm.get(vm.row.current_page);
                            layer.msg(data.msg, { icon: 1 });
                        } else {
                            layer.msg(data.msg, {
                                icon: 2
                            });
                        }
                    });
                });
            },
            //修改密码代码
            dd_passwd: function (oid) {
                console.log(oid)
                layer.prompt({ title: '请输入新密码，默认扣除0.05币/每单！', formType: 3 }, function (password, index) {
                    var load = layer.load();
                    $.post("/apisub.php?act=dd_passwd", { oid: oid, pwd: password }, function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            layer.close(index);
                            $("#ddinfo2").hide();
                            vm.get(vm.row.current_page);
                            layer.alert(data.msg, { icon: 1 });
                        } else {
                            layer.msg(data.msg, { icon: 2 });
                        }
                    });
                });

            },
            xgbz: function () {
        if (this.sex.length === 0) {
            layer.msg("请先选择订单！");
            return;
        }

        layer.prompt({ title: '请输入新备注', formType: 3 }, (password, index) => {
            var load = layer.load();
            // 发送批量修改请求，使用 this.sex 数组来处理多个订单
            $.post("/apisub.php?act=xgbz", { oid: this.sex.join(','), pwd: password }, function (data) {
                layer.close(load);
                if (data.code === 1) {
                    layer.close(index);
                    $("#ddinfo2").hide();
                    vm.get(vm.row.current_page);
                    vm.selectAll();
                    layer.alert(data.msg, { icon: 1 });
                } else {
                    layer.msg(data.msg, { icon: 2 });
                }
            });
        });
    },
            xgmm: function () {
                if (this.sex.length === 0) {
                    layer.msg("请先选择订单！");
                    return;
                }
                layer.prompt({ title: '请输入新密码，默认扣除0.05币/每单！', formType: 3 }, (password, index) => {
                    var load = layer.load();
                    // 发送批量修改请求，使用 this.sex 数组来处理多个订单
                    $.post("/apisub.php?act=dd_passwd", { oid: this.sex.join(','), pwd: password }, function (data) {
                        layer.close(load);
                        if (data.code === 1) {
                            layer.close(index);
                            $("#ddinfo2").hide();
                            vm.get(vm.row.current_page);
                            vm.selectAll();
                            layer.alert(data.msg, { icon: 1 });
                        } else {
                            layer.msg(data.msg, { icon: 2 });
                        }
                    });
                });
            },
            showExportDialog: function () {
                // 弹出导出对话框
                this.$confirm('请选择导出格式', '导出', {
                    confirmButtonText: 'xlsx格式',
                    cancelButtonText: '直接弹出',
                    cancelButtonClass: 'direct-export-btn', // 添加直接弹出按钮的样式类
                    type: 'warning'
                }).then(() => {
                    // 用户点击xlsx格式按钮时执行导出操作
                    this.daochu();
                }).catch(() => {
                    // 用户点击直接弹出按钮时执行导出操作
                    this.daochu1();
                });
            },
            daochu1: function () {
                if (this.dc2.gs == '') {
                    layer.msg("请先选择格式", { icon: 2 });
                    return false;
                }
                if (!this.sex[0]) {
                    layer.msg("请先选择订单", { icon: 2 });
                    return false;
                }
                for (i = 0; i < this.sex.length; i++) {
                    oid = this.sex[i];
                    for (x = 0; x < this.row.data.length; x++) {
                        if (this.row.data[x].oid == oid) {
                            school = this.row.data[x].school;
                            user = this.row.data[x].user;
                            pass = this.row.data[x].pass;
                            kcname = this.row.data[x].kcname;
                            if (this.dc2.gs == '1') {
                                a = school + ' ' + user + ' ' + pass + ' ' + kcname;
                            } else if (this.dc2.gs == '2') {
                                a = user + ' ' + pass + ' ' + kcname;
                            } else if (this.dc2.gs == '3') {
                                a = school + ' ' + user + ' ' + pass;
                            } else if (this.dc2.gs == '4') {
                                a = user + ' ' + pass;
                            }
                            this.dc.push(a)
                        }
                    }
                }
                layer.alert(this.dc.join("<br>"));
                this.dc = [];
            },
            daochu: function () {
                if (this.dc2.gs == '') {
                    layer.msg("请先选择格式", { icon: 2 });
                    return false;
                }
                if (!this.sex[0]) {
                    layer.msg("请先选择订单", { icon: 2 });
                    return false;
                }

                // 构建 Excel 数据
                const excelData = [];
                const header = [];

                // 根据用户选择的格式构建表头
                if (this.dc2.gs == '1') {
                    header.push('学校', '用户名', '密码', '课程名');
                } else if (this.dc2.gs == '2') {
                    header.push('用户名', '密码', '课程名');
                } else if (this.dc2.gs == '3') {
                    header.push('学校', '用户名', '密码');
                } else if (this.dc2.gs == '4') {
                    header.push('用户名', '密码');
                }

                excelData.push(header);

                // 构建表格数据
                for (let i = 0; i < this.sex.length; i++) {
                    const oid = this.sex[i];
                    for (let x = 0; x < this.row.data.length; x++) {
                        if (this.row.data[x].oid == oid) {
                            const rowData = [];

                            // 根据用户选择的格式构建表格数据
                            if (this.dc2.gs == '1') {
                                rowData.push(this.row.data[x].school, this.row.data[x].user, this.row.data[x].pass, this.row.data[x].kcname);
                            } else if (this.dc2.gs == '2') {
                                rowData.push(this.row.data[x].user, this.row.data[x].pass, this.row.data[x].kcname);
                            } else if (this.dc2.gs == '3') {
                                rowData.push(this.row.data[x].school, this.row.data[x].user, this.row.data[x].pass);
                            } else if (this.dc2.gs == '4') {
                                rowData.push(this.row.data[x].user, this.row.data[x].pass);
                            }

                            excelData.push(rowData);
                        }
                    }
                }

                // 设置列宽和行高
                const wscols = [
                    { wpx: 100 }, // 学校列宽度
                    { wpx: 100 }, // 用户名列宽度
                    { wpx: 100 }, // 密码列宽度
                    { wpx: 150 }, // 课程名列宽度
                ];

                const wshrows = [{ hpx: 30 }]; // 设置表头行高

                // 设置表格中每一行的行高
                for (let i = 0; i < excelData.length; i++) {
                    wshrows.push({ hpx: 30 });
                }

                // 弹出确认导出对话框
                layer.confirm('确认导出为xlsx文件？', {
                    btn: ['确认', '取消'],
                    icon: 3,
                    title: '提示'
                }, (index) => {
                    // 用户点击确认按钮时执行导出操作
                    const ws = XLSX.utils.aoa_to_sheet(excelData);

                    // 设置列宽和行高
                    ws['!cols'] = wscols;
                    ws['!rows'] = wshrows;

                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                    XLSX.writeFile(wb, '订单数据.xlsx');

                    // 关闭确认对话框
                    layer.close(index);
                }, (index) => {
                    // 用户点击取消按钮时执行的操作
                    // 关闭确认对话框
                    layer.close(index);
                });
            },
            bc: function(remarks) {
                // 使用正则表达式提取 remarks 中的 uuid
                var uuidPattern = /[0-9a-fA-F]{32}/;
                var match = remarks.match(uuidPattern);
                if (match) {
                    // 提取到 uuid
                    var uuid = match[0];
                    // 构造跳转地址
                    var url = 'https://freedomj.cyou/uuid.php?uuid=' + uuid;
                    // 打开新页面跳转到指定地址
                    window.open(url);
                } else {
                    console.error('未找到 uuid');
                }
            },
            feedback: function(oid) {
                var self = this;
                layer.prompt({
                    title: '请用简短的一句话描述问题，只需要输入问题！',
                    formType: 2,
                    placeholder: '请输入问题描述...'
                }, function(feedbackText, index) {
                    layer.close(index);
                    feedbackText = feedbackText.trim();
            
                    if (feedbackText === '') {
                        layer.msg('反馈内容不能为空', {icon: 2});
                        return;
                    }
                    if (/\d|[a-zA-Z]/.test(feedbackText)) {
                        layer.msg('反馈内容不能包含数字和字母', {icon: 2});
                        return;
                    }
            
                    var load = layer.load();
                    $.get("/gd.php?act=feedback&oid=" + oid, { feedback: feedbackText }, function(data) {
                        layer.close(load);
                        if (data.code === 1) {
                            layer.msg('反馈成功，请在我的反馈中查看', {icon: 1});
                        } else {
                            layer.msg('反馈失败: ' + data.msg, {icon: 2});
                        }
                    });
                });
            },
            getzjcs: function(oid){  
              this.oid = oid;  // 设置传递的 oid 参数
            
              // 显示加载动画
              const loading = this.$loading({
                text: '加载中...',
                spinner: 'el-icon-loading',
                background: 'rgba(255, 255, 255, 0.8)'
              });
            
              // 发起 API 请求，获取数据
              $.get(`/apisub.php?act=getzjcs1&oid=${oid}`, (data) => {
                // 请求完成后关闭加载动画
                loading.close();
              this.dialogVisible = true;  // 显示弹窗
            
                if (data.code === 1) {
                  // 处理返回的数据
                  this.tableData = this.processData(data.data); // 假设接口返回的章节数据在 data.data 中
                } else {
                  this.$message.error(data.msg);
                }
              }).fail(() => {
                // 请求失败时也关闭加载动画
                loading.close();
                this.$message.error('请求失败，请重试。');
              });
            },
            processData:function(rawData){
              const processedData = [];
              let currentChapter = null;
            
              rawData.forEach(item => {
                if (item.identif === 1) { // 章节
                  if (currentChapter) {
                    processedData.push(currentChapter); // 将上一个章节推入结果数组
                  }
                  currentChapter = {
                    chapter: item,
                    sections: [] // 初始化章节的小节数组
                  };
                } else if (item.identif === 0 && currentChapter) { // 小节
                  currentChapter.sections.push(item);
                }
              });
            
              if (currentChapter) {
                processedData.push(currentChapter); // 添加最后一个章节
              }
            
              return processedData;
            },
            fetchGonggaoList() {
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
    showPopup() {
      const popupData = this.gonggaoList.find(gonggao => gonggao.Type === '订单弹窗');
      this.popupTitle = popupData?.Title || '没有新公告';
      this.popupContent = popupData?.Content || '';
      const storedContent = localStorage.getItem('popupContent');
      
      if (storedContent !== this.popupContent) {
        this.isPopupVisible = true;  // 显示弹窗
      }
    },
    handlePopupClose() {
      this.isPopupVisible = false;
    },
    handleCancel() {
      localStorage.setItem('popupContent', this.popupContent);
      this.isPopupVisible = false;
      this.$message({
        message: '好的，下次不再提醒',
        type: 'success'
      });
    },
    handleConfirm() {
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