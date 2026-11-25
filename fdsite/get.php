<?php
$act=isset($_GET['act'])?$_GET['act']:null;
if($act=="chadan"){
	$username=$_POST['username'];
	$data=array(
	   'username'=>$username
	);
    $result=get_url("api/index.php?act=cd",$data);
	exit($result);
	
}elseif($act=='budan'){
	$id=$_POST['id'];
	$data=array(
	   'id'=>$id
	);
    $result=get_url("api/index.php?act=budan",$data);
	exit($result);	
}

	
	
	
function get_url($url,$post=false,$cookie=false,$header=false){//curl
    $ch = curl_init();
    if($header){
        //curl_setopt($ch,CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }else{
        curl_setopt($ch,CURLOPT_HEADER, 0);
    }
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.62 Safari/537.36');//设置UA 否则会被拦截
    if($post){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post));
    }
    if($cookie){
        curl_setopt($ch, CURLOPT_COOKIE,$cookie);
    }
    
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}	
?>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <title>FreeDom自助售后系统</title>
        <link href="assets/imgage/favicon.ico" rel="shortcut icon"/>
        <!-- Bootstrap Core CSS -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="assets/css/index.css" rel="stylesheet"/>
        <link href="assets/LightYear/css/materialdesignicons.min.css" rel="stylesheet">
		<link href="assets/LightYear/js/ion-rangeslider/ion.rangeSlider.min.css" rel="stylesheet"/>
		<link href="assets/LightYear/css/style.min.css" rel="stylesheet"/>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="assets/js/html5shiv.min.js"></script>
            <script src="assets
            	
            	
            	
            	/js/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">
            .cm-kfqy{
          background-color: #fff;
          padding: 1px 3px;
          padding-left: 10px;
          display: block;
          color: #000 !important;
          border-radius: 4px;
        }
        .cm-kfqy:hover{
          background-color: #f0f0f0;
          padding: 1px 3px;
          padding-left: 10px;
          width: 100%;
          display: block;
          color: #000 !important;
          border-radius: 4px;
        }
        </style>
    </head>
    <body id="page-top">
        <div id="app">
            <div class="col-lg-4 col-md-7 col-sm-10" style="float: none;  margin: auto; padding-top: 50px">
                <!--html公告代码 开始-->                                                         
                <!--html公告代码 结束-->
   
                <div class="panel">
                    <div class="panel-heading bg-success">
                        <h3 class="panel-title" >
                            {{title}}
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    {{inputName}}
                                </div>
                                <input class="form-control" type="text" v-model="username"/>
                            </div>
                        </div>
                        <div class="btn-group btn-group-justified form-group">
                            <a class="btn btn-block btn-default" v-bind:html="btnName" v-on:click="query()">
                                {{btnName}}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default" v-show="userInfo.show">
                    <div class="panel-heading bg-info">
                        <h3 class="panel-title">
                            {{userInfo.title}}
                        </h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>学生姓名</th>
                                    <th>网课账号</th>
                                    <th>学校名称</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{userInfo.name}}
                                    </td>
                                    <td>
                                        {{userInfo.user}}
                                    </td>
                                    <td>
                                        {{userInfo.school}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel panel-default" v-show="orderInfo.show">
                    <div class="panel-heading bg-danger">
                        <h3 class="panel-title">
                            {{orderInfo.title}}
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default" v-for="(item,index) in orderInfo.list" v-bind:key="index">
                              
                                  <div style="font-size: 16px; margin-left: 15px; margin-top: 5px;">[课程] &nbsp;<b>{{item.kcname}}</b><span v-if="item.miaoshua=='1'" style="color: red;">&nbsp;秒</span></div>
                              
                                <div class="panel-collapse">
                                    <div class="panel-body"> 
                                    	       <!--    <span><b>平台名字：</b>{{item.ptname}}<br/></span>
		                                     <!--  <span><b>课程名称：</b>{{item.kcname}}<br/></span>-->
			                                   <span> <b>下单时间：</b>{{item.addtime}} <br/></span>
			                                    <span v-if="item.courseStartTime!=''"><b>课程开始时间：</b>{{item.courseStartTime}}<br/></span>
			                                    <span v-if="item.courseEndTime!=''">    <b>课程结束时间：</b>{{item.courseEndTime}}<br/></span>
			                                     <span v-if="item.examStartTime!=''">   <b>考试开始时间：</b>{{item.examStartTime}}<br/></span>
			                                    <span v-if="item.examEndTime!=''">    <b>考试结束时间：</b>{{item.examEndTime}}<br/></span>
			                                    <span><b>订单状态：</b>
                                                <span v-if="item.status=='待处理'" class="btn btn-xs btn-info ">{{item.status}}</span>
                                                <span v-else-if="item.status=='已完成'" class="btn btn-xs btn-success">{{item.status}}</span>
                                                <span v-else-if="item.status=='异常'" class="btn btn-xs btn-danger">{{item.status}}</span>
                                                <span v-else-if="item.status=='密码错误'" class="btn btn-xs btn-danger">{{item.status}}</span>
                                                <span v-else-if="item.status=='进行中'" class="btn btn-xs btn-warning">{{item.status}}</span>
                                                <span v-else-if="item.status=='待考试'" class="btn btn-xs btn-pink">{{item.status}}</span>
                                                <span v-else-if="item.status=='已考试'" class="btn btn-xs btn-pink">{{item.status}}</span>
                                                <span v-else-if="item.status=='未激活'" class="btn btn-xs btn-danger">{{item.status}}</span>
                                                <span v-else-if="item.status=='已过期'" class="btn btn-xs btn-danger">{{item.status}}</span>
                                                <span v-else-if="item.status=='已取消'" class="btn btn-xs btn-dark">{{item.status}}</span>
                                                <span v-else-if="item.status=='补刷中'" class="btn btn-xs btn-cyan">{{item.status}}</span>
                                                <span v-else-if="item.status=='平时分'" class="btn btn-xs btn-purple">{{item.status}}</span>
									            <span v-else style="color: green;">{{item.status}}</span>
									            
			                                    	<br/></span>			                                  
			                                     <span><b>课程进度：</b>{{item.process}}<br/></span>
			                                     <span><b>备注：</b>{{item.remarks}}<br/> </span>            

                                        <div> 
                                        	<b> 操作：</b>
                                        	<button class="btn btn-success btn-xs" v-on:click="fill(item.id)">补单重刷</button> 
                                                                       
                                               <button class="btn btn-danger btn-xs" v-on:click="fill1(item.id)">刷新进度</button> 
                                            </span>   
                                        </div>  
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<script src="assets/js/jquery.min.js"></script>
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
<script src="assets/js/vue.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>


<script>
var vm=new Vue({
    el: '#app',
    data: {
        title: 'FreeDom自助售后系统',
        inputName: '网课账号',
        btnName: '查询信息',
        list: {},
        username: '',
        is_load: false,
        userInfo: {
            show: false,
            title: '学生信息（请核对后是本人再继续操作）',
            name: '',
            user: '',
            school: '',
            type: 0
        },
        orderInfo: {
            show: false,
            title: '订单信息',
            list: {}
        }
    },
    methods: {
        query: function() {
            //console.log(this.username);
            if (this.username == "") {
            	layer.msg("请输入查询账号！");              
                this.btnName = '重新查询信息';
                return false;
            }
            var that = this;
            that.userInfo.show = false;
            that.orderInfo.show = false;
            that.orderInfo.list = {};
            that.load();
            $.ajax({
                type: "POST",
                url: "api/index.php?act=cd",
                data: {username: this.username},
                dataType: 'json',
                success: function(data) {
                    that.load();
                    if (typeof data.data == 'object') {
                        if (data.data.length > 0) that.userInfo.name = data.data[0].name;
                        if (data.data.length > 0) that.userInfo.school = data.data[0].school;
                        that.userInfo.user = that.username;
                        that.userInfo.show = false;
                        if (data.data.length > 0) {
                            that.orderInfo.type = data.type;
                            that.orderInfo.list = data.data;
                            that.orderInfo.show = true;
                        }
                    } else {
                        layer.msg("未查询到相关订单信息！");
                    }
                },
                error: function(e) {
                    console.log(e);
                    layer.alert('服务器错误，请稍后再试！');
                }
            });
        },
        fill: function(id) {
            var wktype = this.orderInfo.type;
            layer.msg("正在努力提交中....",{icon:3});
            $.ajax({
                type: "POST",
                url: "api/index.php?act=bd",
                data: {id:id},
                dataType: 'json',
                success: function(data) {
                    if (data.code == 1) {
                    	vm.query();
                        layer.alert(data.msg,{icon:1});
                    } else {
                        layer.alert(data.msg,{icon:2,title:'注意'});
                    }
                },
                error: function(e) {
                    console.log(e);
                    layer.alert('服务器错误，请稍后再试！');
                }
            });
        },
          fill1: function(id) {
            var wktype = this.orderInfo.type;
            layer.msg("正在努力获取中....",{icon:3});
            $.ajax({
                type: "POST",
                url: "api/index.php?act=up",
                data: {id:id},
                dataType: 'json',
                success: function(data) {
                    if (data.code == 1) {
                    	vm.query();
                        layer.alert(data.msg,{icon:1});
                    } else {
                        layer.alert(data.msg,{icon:2,title:'注意'});
                    }
                },
                error: function(e) {
                    console.log(e);
                    layer.alert('服务器错误，请稍后再试！');
                }
            });
        },
/*        
*/        
        load: function() {
            if (this.is_load === false) {
                this.btnName = '查询中..';
            } else {
                this.btnName = '重新查询信息';
            }
            this.is_load = !this.is_load;
        }
    }
});


	

</script>

<!-----弹出公告-->
<script src="js/sweetalert.min.js"></script>
<script>
swal('查询不到就是下单失败，联系管理员','\n\n每个订单只能补刷10次，请勿频繁操作。','success'); function AddFavorite(title, url) {
  try {
      window.external.addFavorite(url, title);
  }
catch (e) {
     try {
       window.sidebar.addPanel(title, url,);
    }
     catch (e) {
         alert("抱歉，您所使用的浏览器无法完成此操作。");
     }
  }
}
</script>
