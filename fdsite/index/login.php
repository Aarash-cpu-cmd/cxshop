<?php
include('../confing/common.php');
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8"/>
        <link rel="icon" href="../favicon.ico" type="image/ico">
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title><?=$conf['sitename']?></title>
        <meta name="keywords" content="<?=$conf['keywords'];?>"/>
        <meta name="description" content="<?=$conf['description'];?>"/>
        <link rel="stylesheet" href="../assets/layui/css/layui.css"/>

    <style>
        body {
            background-image: url("../freedom.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            min-height: 100vh;
        }

        body:before {
            content: "";
            background-color: rgba(0, 0, 0, .2);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .login-wrapper {
            max-width: 420px;
            padding: 20px;
            margin: 0 auto;
            position: relative;
            box-sizing: border-box;
            z-index: 2;
        }

        .login-wrapper>.layui-form {
            padding: 25px 30px;
            background-color: #fff;
            box-shadow: 0 3px 6px -1px rgba(0, 0, 0, 0.19);
            box-sizing: border-box;
            border-radius: 4px;
        }

        .login-wrapper>.layui-form>h2 {
            color: #333;
            font-size: 18px;
            text-align: center;
            margin-bottom: 25px;
        }

        .login-wrapper>.layui-form>.layui-form-item {
            margin-bottom: 25px;
            position: relative;
        }

        .login-wrapper>.layui-form>.layui-form-item:last-child {
            margin-bottom: 0;
        }

        .login-wrapper>.layui-form>.layui-form-item>.layui-input {
            height: 46px;
            line-height: 46px;
            border-radius: 2px !important;
        }

        .login-wrapper .layui-input-icon-group>.layui-input {
            padding-left: 46px;
        }

        .login-wrapper .layui-input-icon-group>.layui-icon {
            width: 46px;
            height: 46px;
            line-height: 46px;
            font-size: 20px;
            color: #909399;
            position: absolute;
            left: 0;
            top: 0;
            text-align: center;
        }

        .login-wrapper>.layui-form>.layui-form-item.login-captcha-group {
            padding-right: 135px;
        }

        .login-wrapper>.layui-form>.layui-form-item.login-captcha-group>.login-captcha {
            height: 46px;
            width: 120px;
            cursor: pointer;
            box-sizing: border-box;
            border: 1px solid #e6e6e6;
            border-radius: 2px !important;
            position: absolute;
            right: 0;
            top: 0;
        }

        .login-wrapper>.layui-form>.layui-form-item>.layui-form-checkbox {
            margin: 0 !important;
            padding-left: 25px;
        }

        .login-wrapper>.layui-form>.layui-form-item>.layui-form-checkbox>.layui-icon {
            width: 15px !important;
            height: 15px !important;
        }

        .login-wrapper>.layui-form .layui-btn-fluid {
            height: 48px;
            line-height: 48px;
            font-size: 16px;
            border-radius: 2px !important;
        }

        .login-wrapper>.layui-form>.layui-form-item.login-oauth-group>a>.layui-icon {
            font-size: 26px;
        }

        .login-copyright {
            color: #eee;
            padding-bottom: 20px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        @media screen and (min-height: 550px) {
            .login-wrapper {
                margin: -250px auto 0;
                position: absolute;
                top: 50%;
                left: 0;
                right: 0;
                width: 100%;
            }

            .login-copyright {
                position: absolute;
                bottom: 0;
                right: 0;
                left: 0;
            }
        }

        .layui-btn {
            background-color: #5FB878;
            border-color: #5FB878;
        }

        .layui-link {
            color: #5FB878 !important;
        }

        .layui-input {
            border-top: none;
            border-left: none;
            border-right: none;
        }

        .layui-input:hover,
        .layui-textarea:hover {
            border-color: #00a65a !important;
        }
    </style>
</head>

<body>
   <div id="login1">
    <div v-if="loginType" class="login-wrapper layui-anim layui-anim-scale " >
        <div class="layui-form" >
           <center> <img class="layui-anim layui-anim-scale" src="<?=$conf['logo']?>" width="80%"/></center>
           <!--center style="margin-top: 20px;"><strong>Sometimes goodbye is the only way.</strong></center--><br>
            <div class="layui-form-item layui-input-icon-group">
                <i class="layui-icon layui-icon-username"></i>
                <input class="layui-input" v-model="dl.user" placeholder="Login account" 
                    lay-verify="required" required />
            </div>
            <div class="layui-form-item layui-input-icon-group">
                <i class="layui-icon layui-icon-password"></i>
                <input class="layui-input" v-model="dl.pass" placeholder="Login password" type="password" 
                    lay-verify="required" required />
            </div>

            <div class="layui-form-item">
                <button class="layui-btn layui-btn-fluid layui-bg-black" @click="login" lay-submit>Log in</button>
            </div>
             <div class="layui-form-item">
                             
                                <a @click="newlogin" class="layui-link pull-right">没有账号？点我注册</a>|  <a  href="mzsm" class="layui-link"style="color: black!important;">免责声明</a>
                                   </div>
           
        </div>
     </div>
     
   <div v-else class="login-wrapper layui-anim layui-anim-scale">
        <div class="layui-form">
            <h2>欢迎来到FreeDom！</h2>
             <div class="layui-form-item layui-input-icon-group">
                <i class="layui-icon layui-icon-release"></i>
                <input class="layui-input" name="text" v-model="reg.name" placeholder="请输入昵称" type="text" lay-verType="tips"
                    lay-verify="equalTo" required />
            </div>
            <div class="layui-form-item layui-input-icon-group">
                <i class="layui-icon layui-icon-link"></i>
                <input class="layui-input" name="Invitation" v-model="reg.yqm" placeholder="请输入邀请码" autocomplete="off" lay-verType="tips"
                    lay-verify="required" required />
            </div>

            <div class="layui-form-item layui-input-icon-group">
                <i class="layui-icon layui-icon-username"></i>
                <input class="layui-input" name="user" v-model="reg.user" placeholder="请输入账号(QQ号)" autocomplete="off"
                    lay-verType="tips" lay-verify="required|email" required />
            </div>

            <div class="layui-form-item layui-input-icon-group">
                <i class="layui-icon layui-icon-password"></i>
                <input class="layui-input" name="password" v-model="reg.pass" placeholder="请输入登录密码" type="text" lay-verType="tips"
                    lay-verify="required|psw" required />
            </div>
           

            <div class="layui-form-item">
                <a @click="newlogin" class="layui-link">返回登录</a>
            </div>
            <div class="layui-form-item" style="margin-bottom: 20px;">
                <button  class="layui-btn layui-btn-fluid layui-bg-black" 
                    lay-submit  @click="register">立即注册</button>
            </div>
        </div>
    </div>
    
    
   </div>
</body>



<script src="assets/js/jquery.min.js"></script>
<script src="layer/3.1.1/layer.js"></script>
<script src="js/login/vue.min.js"></script>
<script src="js/login/vue-resource.min.js"></script>
<script src="js/login/axios.min.js"></script>
<!--script>
    var img = document.querySelector('.layui-fluid');
    var audio = document.createElement('audio');
    audio.src = 'http://api.1o.pw/api/wyjx?id=1406642934';
    audio.autoplay = true;
    document.body.appendChild(audio);
</script-->
<script>
    var vm = new Vue({
        el: "#login1",
        data: {
            loginType: true,
            title: "你在看什么呢？我写的代码好看吗",
            dl: {},
            reg: {}
        },
        methods: {
            newlogin: function() {
                this.loginType = !this.loginType
            },
            login: function() {
                if (!this.dl.user || !this.dl.pass) {
                    layer.msg('账号密码不能为空', {
                        icon: 2
                    });
                    return
                }
                var loading = layer.load(2);
                vm.$http.post("/apisub.php?act=login", {
                    user: this.dl.user,
                    pass: this.dl.pass
                }, {
                    emulateJSON: true
                }).then(function(data) {
                    layer.close(loading);
                    if (data.data.code == 1) {
                        layer.msg(data.data.msg, {
                            icon: 1
                        });
                        setTimeout(function() {
                            window.location.href = "../index/mzsm"
                        }, 1000);
                    } else if (data.data.code == 5) {
                        vm.login2();
                    } else {
                        layer.msg(data.data.msg, {
                            icon: 2
                        });
                    }
                });

            },
            register: function() {
                if (!this.reg.user || !this.reg.pass || !this.reg.name || !this.reg.yqm) {
                    layer.msg('所有项不能为空', {
                        icon: 2
                    });
                    return
                }
                var loading = layer.load(2);
                this.$http.post("/apisub.php?act=register", {
                    name: this.reg.name,
                    user: this.reg.user,
                    pass: this.reg.pass,
                    yqm: this.reg.yqm
                }, {
                    emulateJSON: true
                }).then(function(data) {
                    layer.close(loading);
                    if (data.data.code == 1) {
                        this.loginType = true;
                        this.dl.user = this.reg.user;
                        this.dl.pass = this.reg.pass;
                        layer.msg(data.data.msg, {
                            icon: 1
                        });
                    } else {
                        layer.msg(data.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            login2: function() {
                layer.prompt({
                    title: '管理二次验证',
                    formType: 3
                }, function(pass2, index) {
                    var loading = layer.load(2);
                    vm.$http.post("/apisub.php?act=login", {
                        user: vm.dl.user,
                        pass: vm.dl.pass,
                        pass2: pass2
                    }, {
                        emulateJSON: true
                    }).then(function(data) {
                        layer.close(loading);
                        if (data.data.code == 1) {
                            layer.msg(data.data.msg, {
                                icon: 1
                            });
                            setTimeout(function() {
                                window.location.href = "../index/mzsm"
                            }, 1000);
                        } else {
                            layer.msg(data.data.msg, {
                                icon: 2
                            });
                        }
                    });
                });
            }
        }
    });

    $('#connect_qq').click(function() {
        var ii = layer.load(0, {
            shade: [0.1, '#fff']
        });
        $.ajax({
            type: "POST",
            url: "../qq_login.php",
            data: {"type":'qq'},
            dataType: 'json',
            success: function(data) {
                layer.close(ii);
                if (data.code == 1) {
                    window.location.href = data.url;
                } else {
                    layer.alert(data.msg, {
                        icon: 7
                    });
                }
            }
        });
    });
    
</script>
<script>
const handler = setInterval(function () { console.clear(); const before = new Date(); debugger; const after = new Date(); const cost = after.getTime() - before.getTime(); if (cost > 100) { } }, 1);
        //屏蔽右键菜单
        document.oncontextmenu = function (event) {
            if (window.event) {
                event = window.event;
            }
            try {
                var the = event.srcElement;
                if (!((the.tagName == "INPUT" && the.type.toLowerCase() == "text") || the.tagName == "TEXTAREA")) {
                    return false;
                }
                return true;
            } catch (e) {
                return false;
            }
        }
        //禁止f12
        function fuckyou() {
            window.open("/", "_blank"); //新窗口打开页面
            window.close(); //关闭当前窗口(防抽)
            window.location = "about:blank"; //将当前窗口跳转置空白页
        }
         //禁止Ctrl+U
        var arr = [123, 17, 18];
        document.oncontextmenu = new Function("event.returnValue=false;"), //禁用右键
 
            window.onkeydown = function (e) {
                var keyCode = e.keyCode || e.which || e.charCode;
                var ctrlKey = e.ctrlKey || e.metaKey;
                console.log(keyCode + "--" + keyCode);
                if (ctrlKey && keyCode == 85) {
                    e.preventDefault();
                }
                if (arr.indexOf(keyCode) > -1) {
                    e.preventDefault();
                }
            }
</script>
</html>