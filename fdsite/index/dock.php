<?php
$mod='blank';
$title='平台对接';
require_once('head.php');
$fl=$DB->count("select addprice from qingka_wangke_user where uid='{$userrow['uid']}'");
$ck=$DB->count("SELECT count(id) FROM `qingka_wangke_log` WHERE type='API查课'AND uid='{$userrow['uid']}' ");
$xd = $DB->count("SELECT count(id) FROM `qingka_wangke_log` WHERE type='API添加任务' AND uid='{$userrow['uid']}' ");
$xdbxz=$conf['api_proportion'];
$xdb=round($xd/$ck,4)*100;
?>
<style>
  .td-wrap {
    white-space: normal; /* 允许内容自动换行 */
    max-width: 200px; /* 最大宽度 */
    word-wrap: break-word; /* 允许在单词中间断行 */
  }
/* 卡片阴影和边框 */
.card {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* 加强阴影效果 */
  border-radius: 12px; /* 更加圆滑的边角 */
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* 添加过渡效果 */
  border: 1px solid rgba(0, 0, 0, 0.1); /* 添加轻微边框 */
  background: linear-gradient(145deg, #ffffff, #f1f1f1); /* 添加渐变背景 */
}

.fdcard:hover {
  transform: scale(1.05); /* 鼠标悬停时轻微放大 */
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3); /* 鼠标悬停时加强阴影 */
}

/* 按钮样式 */
.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
  border-radius: 8px; /* 使按钮有圆角 */
  color: #fff;
  padding: 10px 20px; /* 加大内边距 */
  font-size: 16px; /* 调整字体大小 */
  box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4); /* 添加阴影效果 */
  transition: background-color 0.3s ease, transform 0.2s ease; /* 添加过渡动画 */
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #0056b3;
  transform: translateY(-2px); /* 鼠标悬停时按钮微微上升 */
  box-shadow: 0 6px 12px rgba(0, 123, 255, 0.6); /* 鼠标悬停时加大阴影 */
}

/* 按钮点击时效果 */
.btn-primary:active {
  transform: translateY(0); /* 点击时回到原位置 */
  box-shadow: 0 2px 4px rgba(0, 123, 255, 0.4); /* 点击时阴影变弱 */
  background-color: #004085; /* 点击时颜色略深 */
}

/* 卡片内部文本调整 */
.card-title {
  font-weight: bold;
  font-size: 18px;
  color: #333;
}

.card-text {
  font-size: 14px;
  color: #666;
}

/* 小型屏幕调整布局 */
@media (max-width: 768px) {
  .card {
    margin-bottom: 20px; /* 移动端增加卡片之间的间距 */
  }
  .btn-primary {
    width: 100%; /* 按钮在移动设备上全宽显示 */
  }
}


</style>
     <div class="app-content-body ">
        <div class="wrapper-md control">
        <div class="layui-row layui-col-space8 layui-anim layui-anim-upbit">
        <div  class="card" style="box-shadow: 3px 3px 8px #d1d9e6, -3px -3px 8px #d1d9e6;border-radius: 7px;">
          <ul class="nav nav-tabs" role="tablist">
            <li class="active">
              <a data-toggle="tab" href="#ck">查课接口</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#xd">下单接口</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#bs">补刷接口</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#jd">同步接口</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#gm">改密接口</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#kcid">商品信息</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#flid">分类信息</a>
            </li>
            <li class="nav-item">
              <a data-toggle="tab" href="#xx">我的信息</a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade active in" id="ck">
                <pre><h5>POST网址:</h5>https://<?echo($_SERVER['SERVER_NAME']);?>/api.php?act=get</pre>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width:100px">请求参数</th>
                                        <th>
                                            说明<br>
                                        </th>
                                        <th class="text-center" style="width:100px">
                                            传输类型<br>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <tr>
                                            <th class="text-center" scope="row">uid</th>
                                            <td><code>登录验证</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">key</th>
                                            <td><code>登录验证</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">platform</th>
                                            <td><code>平台ID</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>

                                        <tr>
                                            <th class="text-center" scope="row">user</th>
                                            <td><code>学生账号</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">pass</th>
                                            <td><code>学生密码</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                         <tr>
                                            <th class="text-center" scope="row">school</th>
                                            <td><code>学生学校</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                    </tr>
                                    </tbody>
                                </table>
                <pre><h4>29系统对接网址</h4>https://<?echo($_SERVER['SERVER_NAME']);?></pre>
                <pre><h4>对接参数设置如下：</h4>账号：uid 密码：key token：key</pre>
                <pre class="layui-code code-demo" lay-title="标识" lay-options="{}"><h4>29对接标识：</h4><h5>//FD标识  "FD" => "FreeDom", 放在/Checkorder/xdjk.php 文件</h5></pre>
                <pre class="layui-code code-demo" lay-title="29查课对接代码" lay-options="{}"><h4>29对接代码：</h4><h5>//FD平台查课接口 复制代码放在/Checkorder/ckjk.php 文件
                
                    else if ($type == "FD")
                    {
                        $data = array("uid" => $a["user"], "key" => $a["pass"], "school" => $school, "user" => $user, "pass" => $pass, "platform" => $noun, "kcid" => $kcid);
                        $dx_rl = $a["url"];
                        $dx_url = "$dx_rl/api.php?act=get";
                        $result = get_url($dx_url, $data);
                        $result = json_decode($result, true);
                        return $result;
                    }</h5></pre>
            </div>
            <div class="tab-pane fade" id="xd">
             <pre><h5>POST:</h5>https://<?echo($_SERVER['SERVER_NAME']);?>/api.php?act=add</pre>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th class="text-center" style="width:100px">请求参数</th>
                                            <th>
                                                说明<br>
                                            </th>
                                            <th class="text-center" style="width:100px">
                                                传输类型<br>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <th class="text-center" scope="row">uid</th>
                                            <td><code>登录验证</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">key</th>
                                            <td><code>登录验证</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">platform</th>
                                            <td><code>平台ID</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>

                                        <tr>
                                            <th class="text-center" scope="row">user</th>
                                            <td><code>学生账号</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">pass</th>
                                            <td><code>账号密码</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">kcname</th>
                                            <td><code>课程名字</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">kcid</th>
                                            <td><code>课程ID</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        </tbody>
                                    </table>
             <pre><h4>29系统对接网址</h4>https://<?echo($_SERVER['SERVER_NAME']);?></pre>
             <pre><h4>对接参数设置如下：</h4>账号：uid 密码：key token：key</pre>
                <pre class="layui-code code-demo" lay-title="标识" lay-options="{}"><h4>29对接标识：</h4><h5>//FD标识  "FD" => "FreeDom", 放在/Checkorder/xdjk.php 文件</h5></pre>
                <pre class="layui-code code-demo" lay-title="29下单对接代码" lay-options="{}"><h4>29对接代码：</h4><h5>//FD平台下单接口 复制代码放在/Checkorder/xdjk.php 文件
                
                    else if ($type == "FD") 
                    {
                        $data = array("uid" => $a["user"], "key" => $a["pass"], "platform" => $noun, "school" => $school, "user" => $user, "pass" => $pass, "kcname" => $kcname, "kcid" => $kcid);
                        $dx_rl = $a["url"];
                        $dx_url = "$dx_rl/api.php?act=add";
                        $result = get_url($dx_url, $data);
                        $result = json_decode($result, true);
                        if ($result["code"] == "0") {
                            $b = array("code" => 1, "msg" => "下单成功");
                        } else {
                            $b = array("code" => - 1, "msg" => $result["msg"]);
                    }
                    return $b;
                    }</h5></pre>
            </div>
            <div class="tab-pane fade" id="bs">
                <pre><h5>POST:</h5>https://<?echo($_SERVER['SERVER_NAME']);?>/api.php?act=budan</pre>
                    <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width:100px">请求参数</th>
                                        <th>
                                            说明<br>
                                        </th>
                                        <th class="text-center" style="width:100px">
                                            传输类型<br>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th class="text-center" scope="row">uid</th>
                                        <td><code>登录验证</code></td>
                                        <td class="text-center"><code>必传</code></td>
                                    </tr>
                                    <tr>
                                        <th class="text-center" scope="row">key</th>
                                        <td><code>登录验证</code></td>
                                        <td class="text-center"><code>必传</code></td>
                                        </tr>
                                    <tr>
                                        <th class="text-center" scope="row">id</th>
                                        <td><code>订单账号</code></td>
                                        <td class="text-center"><code>必传</code></td>
                                    </tr>
                                    </tbody>
                                </table>
                <pre><h4>29系统对接网址</h4>https://<?echo($_SERVER['SERVER_NAME']);?></pre>
                <pre><h4>对接参数设置如下：</h4>账号：uid 密码：key token：key</pre>
                <pre class="layui-code code-demo" lay-title="标识" lay-options="{}"><h4>29对接标识：</h4><h5>//FD标识  "FD" => "FreeDom", 放在/Checkorder/xdjk.php 文件</h5></pre>
                <pre class="layui-code code-demo" lay-title="29补刷对接代码" lay-options="{}"><h4>29对接代码：</h4><h5>//FD平台补刷接口 复制代码放在/Checkorder/bsjk.php 文件
                
                    elseif ($type == "FD") 
                    {
                        $data = array("uid" => $a["user"], "key" => $a["pass"], "id" => $yid);
                        $dx_rl = $a["url"];
                        $dx_url = "$dx_rl/api.php?act=budan";
                        $result = get_url($dx_url, $data);
                        $result = json_decode($result, true);
                        return $result;
                    }</h5></pre>
            </div>
            <div class="tab-pane fade" id="jd">
                <pre><h5>POST:</h5>https://<?echo($_SERVER['SERVER_NAME']);?>/api.php?act=chadanoid</pre>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:100px">请求参数</th>
                                <th>
                                    说明<br>
                                </th>
                                <th class="text-center" style="width:100px">
                                     传输类型<br>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                                        <tr>
                                            <th class="text-center" scope="row">uid</th>
                                            <td><code>登录验证</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">key</th>
                                            <td><code>登录验证</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">yid</th>
                                            <td><code>订单id</code></td>
                                            <td class="text-center"><code>可选</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">username</th>
                                            <td><code>订单账号</code></td>
                                            <td class="text-center"><code>可选</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">school</th>
                                            <td><code>订单学校</code></td>
                                            <td class="text-center"><code>可选</code></td>
                                        </tr>
                                        </tbody>
                                    </table>
                <pre><h4>29系统对接网址</h4>https://<?echo($_SERVER['SERVER_NAME']);?></pre>
                <pre><h4>对接参数设置如下：</h4>账号：uid 密码：key token：key</pre>
                <pre class="layui-code code-demo" lay-title="标识" lay-options="{}"><h4>29对接标识：</h4><h5>//FD标识  "FD" => "FreeDom", 放在/Checkorder/xdjk.php 文件</h5></pre>
                <pre class="layui-code code-demo" lay-title="29进度对接代码" lay-options="{}"><h4>29进度对接代码（支持yid和账号查询）：</h4><h5>//FD平台进度接口 复制代码放在/Checkorder/jdjk.php 文件
                
    else if ($type == "FD") {
        $data = array("username" => $user,"yid"=>$d["yid"],"uid" => $a["user"],"key" => $a["pass"]);//$d["yid"]为订单yid，如果没有自行在jdjk.php文件顶部添加该参数！
        $dx_rl = $a["url"];
        $dx_url  = "$dx_rl/api.php?act=chadanoid";
        $result = get_url($dx_url, $data);
        $result = json_decode($result, true);
        if ($result["code"] == "1") {
            foreach ($result["data"] as $res) {
            $yid = $res["id"];
            $kcname = $res["kcname"];
            $status = $res["status"];
            $process = $res["process"];
            $remarks = $res["remarks"];
            $kcks = $res["courseStartTime"];
            $kcjs = $res["courseEndTime"];
            $ksks = $res["examStartTime"];
            $ksjs = $res["examEndTime"];
            $b[] = array("code" => 1, "msg" => "查询成功", "yid" => $yid, "kcname" => $kcname, "user" => $user, "pass" => $pass, "ksks" => $ksks, "ksjs" => $ksjs, "status_text" => $status, "process" => $process, "remarks" => $remarks);
            }
        } else {
            $b[] = array("code" => -1, "msg" => $result);
        }
        return $b;
    }</h5></pre>
            </div>
            <div class="tab-pane fade" id="gm">
                <pre><h5>POST:</h5>https://<?echo($_SERVER['SERVER_NAME']);?>/api.php?act=xgmm</pre>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:100px">请求参数</th>
                                <th>
                                    说明<br>
                                </th>
                                <th class="text-center" style="width:100px">
                                     传输类型<br>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                                        <tr>
                                            <th class="text-center" scope="row">uid</th>
                                            <td><code>登录验证</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">key</th>
                                            <td><code>登录验证</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">yid</th>
                                            <td><code>订单id</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" scope="row">pwd</th>
                                            <td><code>新密码</code></td>
                                            <td class="text-center"><code>必传</code></td>
                                        </tr>
                                        </tbody>
                                    </table>
                <pre><h4>29系统对接网址</h4>https://<?echo($_SERVER['SERVER_NAME']);?></pre>
                <pre><h4>对接参数设置如下：</h4>账号：uid 密码：key token：key</pre>
                <pre class="layui-code code-demo" lay-title="标识" lay-options="{}"><h4>29对接标识：</h4><h5>//FD标识  "FD" => "FreeDom", 放在/Checkorder/gmjk.php 文件</h5></pre>
                <pre class="layui-code code-demo" lay-title="29进度对接代码" lay-options="{}"><h4>29改密对接代码：</h4><h5>//FD平台改密接口 复制代码放在/Checkorder/gmjk.php 文件
                
    elseif ($type == "FD") {
        $data = array("uid" => $a["user"], "key" => $a["pass"], "oid" => $yid,'pwd' =>$newpass);
        $dx_rl = $a["url"];
        $dx_url = "$dx_rl/api.php?act=xgmm";
        $result = get_url($dx_url, $data);
        $result = json_decode($result, true);
        return $result;
        }</h5></pre>
            </div>
            <div class="tab-pane fade" id="kcid">
                <div class="modal-content">
                    <input type="text" class="form-control" id="searchInput" placeholder="搜索商品名称..." onkeyup="searchTable()">
                    <div class="table-responsive"> 
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>对接ID</th>
                                    <th>分类id</th>
                                    <th>商品名称</th>
                                    <th>你的价格（<?php echo $userrow['addprice']?>）</th>
                                    <th>0.2</th>
                                    <th>0.3</th>
                                    <th>0.4</th>
                                    <th>0.5</th>
                                    <th>0.6</th>
                                    <th>0.8</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $query = "select * from qingka_wangke_class where status=1";
                                    $result = $DB->query($query);
                                
                                    while ($row = $DB->fetch($result)) {
                                        echo "<tr>";
                                        echo "<td>".$row['cid']."</td>";
                                        echo "<td>".$row['fenlei']."</td>";
                                        echo "<td class=\"td-wrap\">".htmlspecialchars($row['name'])."</td>";
                                        echo "<td>".$row['price'] * $fl."</td>";
                                        echo "<td>".$row['price']*0.2."</td>";
                                        echo "<td>".$row['price']*0.3."</td>";
                                        echo "<td>".$row['price']*0.4."</td>";
                                        echo "<td>".$row['price']*0.5."</td>";
                                        echo "<td>".$row['price']*0.6."</td>";
                                        echo "<td>".$row['price']*0.8."</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="flid">
            <div class="modal-content">
                <div class="table-responsive"> 
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>分类ID</th>
                                <th>分类名称</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $query = "select * from qingka_wangke_fenlei where status!=0";
                                $result = $DB->query($query);

                                while ($row = $DB->fetch($result)) {
                                    echo "<tr>";
                                    echo "<td>".$row['id']."</td>";
                                    echo "<td>".$row['name']."</td>";
                                    echo "</tr>";
                                }
                            ?>
                    </tbody>
                    </table>
                </div>
            </div>
            </div>
            <div class="tab-pane fade" id="xx">
                <div class="modal-content">
                    <div class="table-responsive"> 
						<table class="table table-striped">
							<thead>
							    <tr>
							        <th>对接ID</th>
							        <th>对接KEY</th>
							        <th>下单比限制</th>
							        <th>当前API下单比</th>
							    </tr>
							</thead>
								<tbody>
									<?php 
										$a=$DB->query("select * from qingka_wangke_user where uid='{$userrow['uid']}'");
										
										while($rs=$DB->fetch($a)){
										    if($rs['key']==0){
										    $rs['key']="未开通KEY";
										    echo "<tr><td>".$rs['uid']."</td><td>".$rs['key']."</td><td>".$xdbxz."%</td><td>".$xdb."%</td></tr>"; 
										    }else{
										        echo "<tr><td>".$rs['uid']."</td><td>".$rs['key']."</td><td>".$xdbxz."%</td><td>".$xdb."%</td></tr>"; 
										    }
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
 

<script type="text/javascript" src="assets/LightYear/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>
<script src="assets/js/vue.min.js"></script>
<script src="assets/js/vue-resource.min.js"></script>
<script src="assets/main/axios.min.js"></script>
<script src="assets/layui/layui.js"></script>  
<script src="assets/js/element.js"></script>

<script>
function searchTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("dataTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[2]; // 假设商品名称在第三列
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        } 
    }
}
</script>
<script>
    //注意：选项卡 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function(){
      var element = layui.element;
      
      //…
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
<!--<script>
             //禁止鼠标右击
      document.oncontextmenu = function() {
        event.returnValue = false;
      };
      //禁用开发者工具F12
      document.onkeydown = document.onkeyup = document.onkeypress = function(event) {
        let e = event || window.event || arguments.callee.caller.arguments[0];
        if (e && e.keyCode == 123) {
          e.returnValue = false;
          return false;
        }
      };
      let userAgent = navigator.userAgent;
      if (userAgent.indexOf("Firefox") > -1) {
        let checkStatus;
        let devtools = /./;
        devtools.toString = function() {
          checkStatus = "on";
        };
        setInterval(function() {
          checkStatus = "off";
          console.log(devtools);
          console.log(checkStatus);
          console.clear();
          if (checkStatus === "on") {
            let target = "";
            try {
              window.open("about:blank", (target = "_self"));
            } catch (err) {
              let a = document.createElement("button");
              a.onclick = function() {
                window.open("about:blank", (target = "_self"));
              };
              a.click();
            }
          }
        }, 200);
      } else {
        //禁用控制台
        let ConsoleManager = {
          onOpen: function() {
            alert("Console is opened");
          },
          onClose: function() {
            alert("Console is closed");
          },
          init: function() {
            let self = this;
            let x = document.createElement("div");
            let isOpening = false,
              isOpened = false;
            Object.defineProperty(x, "id", {
              get: function() {
                if (!isOpening) {
                  self.onOpen();
                  isOpening = true;
                }
                isOpened = true;
                return true;
              }
            });
            setInterval(function() {
              isOpened = false;
              console.info(x);
              console.clear();
              if (!isOpened && isOpening) {
                self.onClose();
                isOpening = false;
              }
            }, 200);
          }
        };
        ConsoleManager.onOpen = function() {
          //打开控制台，跳转
          let target = "";
          try {
            window.open("about:blank", (target = "_self"));
          } catch (err) {
            let a = document.createElement("button");
            a.onclick = function() {
              window.open("about:blank", (target = "_self"));
            };
            a.click();
          }
        };
        ConsoleManager.onClose = function() {
          alert("Console is closed!!!!!");
        };
        ConsoleManager.init();
      }
        </script>-->