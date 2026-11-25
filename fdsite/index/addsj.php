<?php
$title='批量查询';
require_once('head.php');
$addsalt=md5(mt_rand(0,999).time());
$_SESSION['addsalt']=$addsalt;
?>
<link rel="stylesheet" href="assets/css/element.css">
<link rel="stylesheet" href="assets/css/index.css">
<script src="assets/css/index.js"></script>
<div class="app-content-body ">
   <div class="wrapper-md control" id="add">
      <div class="layui-row layui-col-space10">
         <div class="layui-col-md6">
            <div class="panel panel-default" style="border-radius: 10px;">
                            <div class="card-body">
                                
                                <form class="form-horizontal devform">
                                                                        <div class="form-group">
                                        <label>搜索项目</label>
                                        <div>
        <input class="form-control" placeholder="在此输入项目名称可进行搜索"type="text" v-model="searchKeyword">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>网课类别</label>
                                        <div>
                                            <select class="form-control" v-model="cid" @change="tips(cid);">
                                      <option value="">选择搜索平台</option>
                                              <option id="cid2" v-for="(class2,index) in class1_temp":value="class2.cid" :selected="index==0">{{class2.name+'（'+class2.price+'币）'}}</option>		
                                            </select>
                                        </div>
                                    </div>
                                    <div v-show="show">
                                        <div class="form-group">
                                                                                       <label>信息填写</label>
                                            <div>
                          <textarea rows="5" class="form-control" v-model="userinfo"
                                    placeholder="下单格式：学校（可为空） 账号 密码（用空格分隔）&#10多账号下单必须换行"></textarea>
                                                <span class="help-block m-b-none" style="color:blue;" id="warning">
                        说明： <span v-html="content" style="color: red;"></span><br>
    网址：<span v-html="content2" style="color: blue;"></span>
                          </span>
                                            </div>
                                        </div>

                                        <div>
                                            <button type="button" @click="get" class="btn btn-primary">查询课程</button>
                                            <button type="button" @click="add" class="btn btn-success">立即提交</button>
                                                                                     <button type="reset" class="btn btn-secondary">重置</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" v-show="show1">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">查询结果&nbsp;<button type="button" class="btn btn-sm btn-info" @click="selectAll">
                                                        全选
                                                    </button></h4>

                                <form class="form-horizontal devform">
                                    <div id="accordion">
                                        <div v-for="(rs,key) in row" class="card mb-1">
                                            <div class="card-header p-3">
                                                <h6 class="m-0 font-14">
                                                    
                                                    <a :href="'#collapse'+key" class="text-dark" data-toggle="collapse"
                                                       aria-expanded="true" aria-controls="collapse0">
                                                        <b>{{rs.userName}}</b> {{rs.userinfo}} <span
                                                            v-if="rs.msg=='查询成功'"><b
                                                                style="color: green;">{{rs.msg}}</b></span><span
                                                            v-else-if="rs.msg!='查询成功'"><b
                                                                style="color: red;">{{rs.msg}}</b></span>
                                                    </a>
                                                </h6>
                                            </div>

                                            <div :id="'collapse'+key" class="collapse show" aria-labelledby="heading0"
                                                 data-parent="#accordion">
                                                <div class="card-body">
                                                   
                                                  <div v-for="(res,key) in rs.data">
                                          <label class="layui-table lyear-checkbox checkbox-inline checkbox-success">
                                             
                                                        <li><input type="checkbox"
                                                                   :value="res"
                                                                   @click="checkResources(rs.userinfo,rs.userName,rs.data,res.id,res.name)"
                                                                   v-model="sex"
                                                            >
                                                            <label
                                                                for="checkbox1"></label>
                                                                <span v-if="!!res.remark" style="color: red;">[{{res.remark}}]</span>
								      	  	                    <span v-if="res.state=='已结束或者未开始' || res.state=='已结课或者未开始'" style="color: red;">【{{res.state}}】</span>
                                                                <span v-if="!!res.sort">【{{res.sort}}】</span>
								      	  	                    <span>{{res.name}}</span>
                                                                <span v-if="res.credit !== null && res.credit !== undefined && res.credit !== ''"> 【{{res.credit}}】</span>
                                                                <span v-if="res.id !== null && res.id !== undefined && res.id !== ''">  ◼  [课程ID: {{res.id}}] </span>]</span></li>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
<script>
var vm=new Vue({
	el:"#add",
 data: {
            row: [],
            check_row: [],
            userinfo: '',
            cid: '',
            id: '',
            miaoshua: '',
            class1: '',
            class1_temp: '',
            searchKeyword: '',
            class3: '',
            show: false,
            show1: false,
            content: '',
            content2:'',
            activems: false,
            sex: []
        },
        watch: {
            sex(newVal) {
                // console.log(newVal)
            },
            searchKeyword(newVal) {
                if (newVal.length > 0) {
                    this.class1_temp = this.class1.filter(e => {
                        return e.name.indexOf(newVal) >= 0;
                    })
                } else {
                    this.class1_temp = JSON.parse(JSON.stringify(this.class1));
                }
                this.cid = this.class1_temp[0].cid;
            }
        },
        methods: {
            selectAll() {
                this.sex = [];
                let res = {};
                let rs = {};
                for (rs of this.row) {
                    for (res of rs.data) {
                        this.checkResources(rs.userinfo, rs.userName, rs.data, res.id, res.name)
                        this.sex.push(res)
                    }
                }
            },
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
	    	console.log(this.check_row);
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
	   
        getclass:function(){
		  var load=layer.load(2);
 			this.$http.post("/apisub.php?act=getclass").then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){			                     	
	          		this.class1=data.body.data;		
	          		 this.class1_temp = JSON.parse(JSON.stringify(this.class1));
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
	    	
	    },
         tips: function (message) {
                for (var i = 0; this.class1.length > i; i++) {
                    if (this.class1[i].cid == message) {
                        this.show = true;
                        this.content = this.class1[i].content;
                        this.content2 = this.class1[i].content2;
                        return false;
                        if (this.class1[i].miaoshua == 1) {
                            this.activems = true;
                        } else {
                            this.activems = false;
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
