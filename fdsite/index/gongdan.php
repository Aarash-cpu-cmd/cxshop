<?php
$title = '工单系统';
require_once('head.php');
?>
<link rel="stylesheet" href="assets/css/element.css">
<style>
    .liclass {
        font-size: 14px;
        text-indent: 2em;
        margin: 5px;
    }

    .null {
        font-size: 18px;
        text-align: center;
    }

    .short-title {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
<div class="app-content-body ">
    <div class="wrapper-md control" id="gdlist">
        <div class="col-sm-12">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <div class="panel-heading font-bold " style="border-top-left-radius: 8px; border-top-right-radius: 8px;background-color:#fff;">
                    <el-button type="info" size="small" @click="showAddTicketForm">新增工单</el-button> 
                    <!-- 新增工单弹出框 -->
                    <el-dialog title="新增工单" :visible.sync="addTicketFormVisible">
                        <el-form :model="newTicketForm">
                            <el-form-item label="请输入你遇到的问题，不要在此反馈订单问题，订单问题请到订单页一键反馈">
                                <el-input type="textarea" v-model="newTicketForm.content"></el-input>
                            </el-form-item>
                        </el-form>
                        <div slot="footer" class="dialog-footer">
                            <el-button @click="addTicketFormVisible = false">取 消</el-button>
                            <el-button type="primary" @click="submitNewTicket">确 定</el-button>
                        </div>
                    </el-dialog>
                    </div>
                </div>
                <div class="text item">
                    
                    <!-- 原有搜索框 -->
                    <div class="search-box">
                        <!-- <div>
                            <el-input v-model="pushToken" placeholder="推送Token,留空关闭" style="width: 160px;"></el-input>
                            <el-button type="danger" size="small" @click="window.open('https://wxpusher.zjiecode.com/api/qrcode/RwjGLMOPTYp35zSYQr0HxbCPrV9eU0wKVBXU1D5VVtya0cQXEJWPjqBdW3gKLifS.jpg', '_blank')">获取</el-button>
                            <el-button type="success" size="small" @click="savePushToken">保存</el-button>
                        </div>-->
                        <div>
                            <el-select v-model="statusFilter" placeholder="筛选状态" style="width: 300px;">
                                <el-option label="选择处理状态" value=""></el-option>
                                <el-option label="待回复" value="待回复"></el-option>
                                <el-option label="已回复" value="已回复"></el-option>
                                <el-option label="已完成" value="已完成"></el-option>
                            </el-select>
                            <el-input v-model="searchQuery" placeholder="可搜索订单ID/账号/密码/商品名" style="width: 300px;"></el-input>
                            <el-button type="primary" @click="get">搜索</el-button>
                        </div>
                    </div>
                    <div class="table-responsive" lay-size="sm" v-if="show == false">
                        <el-divider><span style="color:red">工单列表</span></el-divider>
                        <el-table ref="multipleTable" :data="order" size="small" header-cell-style="text-align:center;font-weight:1500" cell-style="text-align:center" empty-text=暂无反馈内容 highlight-current-row border>
                            <?php if ($userrow['uid'] == 1) { ?>
                                <el-table-column property="uid" label="提交者" width="100" sortable></el-table-column>
                            <?php } ?>
                            <el-table-column property="status" label="工单状态" width="80">
                                <template slot-scope="scope">
                                    <el-tag size="small" v-if="scope.row.state == '待处理'" effect="plain">{{ scope.row.state }}</el-tag>
                                    <el-tag type="success" size="small" v-else-if="scope.row.state == '已完成'" effect="plain">{{ scope.row.state }}</el-tag>
                                    <el-tag type="danger" size="small" v-else-if="scope.row.state == '已回复'" effect="plain">{{ scope.row.state }}</el-tag>
                                    <el-tag type="warning" size="small" v-else="" effect="plain">{{ scope.row.state }}</el-tag>
                                </template>
                            </el-table-column>
                            <?php if ($userrow['uid'] == 1) { ?>
                            <el-table-column label="操作" width="110">
                                <template slot-scope="scope">
                                    <el-dropdown trigger="click" @command="commandvalue">
                                        <el-button type="primary" size="mini" plain>
                                            操作<i class="el-icon-arrow-down el-icon--right"></i>
                                        </el-button>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item :command="{ gid: scope.row.gid, type: 'hf' }">回复</el-dropdown-item>
                                            <el-dropdown-item :command="{ gid: scope.row.gid, type: 'bh' }">完成</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </template>
                            </el-table-column>
                            <?php } ?>
                            <el-table-column label="催促/追问" width="110">
                                <template slot-scope="scope">
                                    <el-popconfirm placement="top-start" confirm-button-text='确定' cancel-button-text='还是算了吧' cancel-button-type="danger" icon="el-icon-info" icon-color="red" title="是否要催促/追问这个工单" @confirm="toanswer(scope.row.gid)">
                                        <el-button type="success" size="mini" slot="reference" plain>追加提问</el-button>
                                    </el-popconfirm>
                                </template>
                            </el-table-column>
                            <el-table-column property="region" label="订单id" width="80" show-overflow-tooltip></el-table-column>
                            <el-table-column label="商品详细信息" width="200">
                                <template slot-scope="scope">
                                    <el-popover placement="top-start" trigger="click" width="400">
                                        <div>{{ scope.row.title }}</div>
                                        <span slot="reference" class="short-content">{{ scope.row.title | truncate(50) }}</span>
                                    </el-popover>
                                </template>
                            </el-table-column>
                            <el-table-column label="问题内容" width="250">
                                <template slot-scope="scope">
                                    <el-popover placement="top-start" trigger="click" width="400">
                                        <div>{{ scope.row.content }}</div>
                                        <span slot="reference" class="short-content">{{ scope.row.content | truncate(50) }}</span>
                                    </el-popover>
                                </template>
                            </el-table-column>
                            <el-table-column label="回复内容" width="250">
                                <template slot-scope="scope">
                                    <el-popover placement="top-start" trigger="click" width="400">
                                        <div>{{ scope.row.answer }}</div>
                                        <span slot="reference" class="short-answer">{{ scope.row.answer | truncate(50) }}</span>
                                    </el-popover>
                                </template>
                            </el-table-column>
                            <el-table-column property="addtime" label="添加时间" width="100" sortable>
                            </el-table-column>
                            <el-table-column label="删除" width="110">
                                <template slot-scope="scope">
                                    <el-popconfirm placement="top-start" confirm-button-text='确定删除' cancel-button-text='我再想想' cancel-button-type="danger" icon="el-icon-info" icon-color="red" title="是否删除该条工单？" @confirm="shan(scope.row.gid)">
                                        <el-button type="danger" size="mini" slot="reference" plain>删除</el-button>
                                    </el-popconfirm>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                    <!--工单列表模块-->
                </div>
            </el-card>
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
<script src="assets/js/element.js"></script>

<script>
    Vue.filter('truncate', function(value, length) {
        if (!value) return '';
        length = length || 20;
        if (value.length <= length) {
            return value;
        }
        return value.substring(0, length) + '...';
    });

    var vm = new Vue({
        el: "#gdlist",
        data: {
            row: null,
            show: false,
            ddsize: 'small',
            order: [],
            list: {
                region: '',
                title: '',
                content: '',
                answer: ''
            },
            form: {
                title: '',
                content: '',
                region: '',
            },
            rules: {
                region: [{
                    required: true,
                    message: '必填！',
                    trigger: 'blur'
                }],
                title: [{
                    required: true,
                    message: '必填！',
                    trigger: 'blur'
                }],
                content: [{
                    required: true,
                    message: '必填！',
                    trigger: 'blur'
                }]
            },
            searchQuery: '',
            statusFilter: '', // 新增搜索查询
            isSearch: false,
            addTicketFormVisible: false, // 新增工单弹出框的显示状态
            newTicketForm: {
            content: ''
        },
        pushToken: '' // 新增推送Token
        },
        methods: {// 显示新增工单弹出框
        showAddTicketForm() {
            this.addTicketFormVisible = true;
        },
        // 提交新增工单
        submitNewTicket() {
    var content = this.newTicketForm.content;
    this.$http.get("/gd.php?act=addTicket&content=" + encodeURIComponent(content)).then(function(response) {
        if (response.data.code == 1) {
            this.addTicketFormVisible = false;
            this.get();
            this.$message({
                message: '工单新增成功',
                type: 'success'
            });
        } else {
            this.$message.error('工单新增失败');
        }
    }).catch(function(error) {
        console.error(error);
        this.$message.error('工单新增失败');
    });
},savePushToken() {
    var token = this.pushToken;
    this.$http.get("/gd.php?act=savePushToken&token=" + encodeURIComponent(token)).then(function(response) {
        if (response.data.code == 1) {
            this.$message({
                message: '推送Token保存成功',
                type: 'success'
            });
        } else {
            this.$message.error('推送Token保存失败');
        }
    }).catch(function(error) {
        console.error(error);
        this.$message.error('推送Token保存失败');
    });
},getPushToken() {
        this.$http.get("/gd.php?act=getPushToken").then(function(response) {
            if (response.data.code == 1) {
                this.pushToken = response.data.token;
            } else {
                this.$message.error('获取推送Token失败');
            }
        }).catch(function(error) {
            console.error(error);
            this.$message.error('获取推送Token失败');
        });
    },commandvalue(command) {
                // 工单回复
                if (command.type == 'hf') {
                    layer.prompt({
                        title: '工单回复',
                        formType: 2,
                        area: ['300px', '200px']
                    }, function(answer, index) {
                        layer.close(index);
                        var load = layer.load();
                        $.post("/gd.php?act=answer", {
                            gid: command.gid,
                            answer: answer
                        }, function(data) {
                            layer.close(load);
                            if (data.code == 1) {
                                layer.msg(data.msg, {
                                    icon: 1
                                });
                                vm.get();
                            } else {
                                layer.msg(data.msg, {
                                    icon: 2
                                });
                            }
                        });
                    });
                }
                // 工单驳回
                if (command.type == 'bh') {
                    layer.prompt({
                        title: '工单完成信息',
                        formType: 2,
                        area: ['300px', '200px']
                    }, function(answer, index) {
                        layer.close(index);
                        var load = layer.load();
                        $.post("/gd.php?act=bohui", {
                            gid: command.gid,
                            answer: answer
                        }, function(data) {
                            layer.close(load);
                            if (data.code == 1) {
                                layer.msg(data.msg, {
                                    icon: 1
                                });
                                vm.get();
                            } else {
                                layer.msg(data.msg, {
                                    icon: 2
                                });
                            }
                        });
                    });
                }
            },
            get: function() {
    var load = layer.load();
    var data = {
        searchQuery: this.searchQuery,
        statusFilter: this.statusFilter // 新增状态筛选参数
    };
    this.$http.post("/gd.php?act=gdlist", data, {
        emulateJSON: true
    }).then(function(data) {
        layer.close(load);
        if (data.data.code == 1) {
            this.row = data.body;
            this.order = [];
            for (var i = 0; data.body.data.length > i; i++) {
                this.order[i] = data.body.data[i];
            }
            this.isSearch = false;
        } else {
            layer.msg(data.data.msg, {
                icon: 2
            });
        }
    });
},shan: function(gid) {
                var loading = layer.load();
                $.post("/gd.php?act=shan", {
                    gid: gid
                }, function(data) {
                    layer.close(loading);
                    if (data.code == 1) {
                        layer.msg(data.msg, {
                            icon: 1
                        });
                        setTimeout(function() {
                            window.location.href = "";
                        }, );
                    } else {
                        layer.msg(data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            toanswer: function(gid) {
                layer.confirm('您正在申请二次处理工单内容<br>本次提交的问题会覆盖你原有的提问内容<br>请尽可能完整的描述你遇到的问题', {
                    title: '温馨提示',
                    icon: 1,
                    btn: ['继续', '取消']
                }, function() {
                    layer.prompt({
                        title: '请仔细描述您遇到的问题，我们会尽快为您处理',
                        formType: 2,
                        area: ['300px', '200px']
                    }, function(toanswer, index) {
                        layer.close(index);
                        var load = layer.load();
                        $.post("/gd.php?act=toanswer", {
                            gid: gid,
                            toanswer: toanswer
                        }, function(data) {
                            layer.close(load);
                            if (data.code == 1) {
                                layer.msg(data.msg, {
                                    icon: 1
                                });
                                setTimeout(function() {
                                    window.location.href = "";
                                }, 2000);
                            } else {
                                layer.msg(data.msg, {
                                    icon: 2
                                });
                            }
                        });
                    });
                });
            },
        },
        mounted() {
    this.get();
    this.getPushToken();
}
    });
</script>