<?php
$title='货源管理';
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

        <div class="panel panel-default">
            <div class="panel-heading font-bold layui-bg-black">商品列表&nbsp;<button class="btn btn-xs btn-light"
                    data-toggle="modal" data-target="#modal-add">货源选择</button>
            </div>
            <div class="panel-body">
                <!-- 搜索框和按钮 -->
                <div class="form-inline">
                    <div class="form-group">
                        <label for="categorySelect">选择分类</label>
                        <select id="categorySelect" class="form-control" v-model="selectedCategory">
                            <option value="">选择分类</option>
                            <option v-for="category in uniqueCategories" :key="category.value" :value="category.value">
                                {{ category.value }}--{{ category.label }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="searchInput">请输入搜索关键词</label>
                        <input id="searchInput" type="text" class="form-control" placeholder="请输入搜索关键词" v-model="searchQuery">
                    </div>
                    
                    <div class="form-group">
                        <button class="btn btn-primary" type="button" @click="searchData">查询</button>
                        <button class="btn btn-danger" type="button" @click="updateStatus">检查失效商品</button>
                        <button id="yjdjBtn" class="btn btn-primary" type="button" @click="openModal()">一键更新对接</button>
                    </div>
                </div>
                
                
                
                <!-- 表格部分 -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="form-check-input" v-model="selectAll" @change="selectAllRows"></th>
                                <th>YCID</th>
                                <th>名称</th>
                                <th>内容</th>
                                <th>分类</th>
                                <th>价格</th>
                                <th>排序</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="res in paginatedRows" :key="res.cid">
                                <td><input type="checkbox" class="form-check-input" v-model="selectedRows" :value="res.cid"></td>
                                <td>{{ res.cid }}</td>
                                <td>{{ res.name }}</td>
                                <td>{{ res.content }}</td>
                                <td>{{ res.fenlei }}</td>
                                <td>{{ res.price }}</td>
                                <td>{{ res.sort }}</td>
                                <td><button class="btn btn-success" data-toggle="modal"
                                        @click="selectItem(res)">添加</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                        <a class="page-link" href="#" @click="changePage(1)">首页</a>
                    </li>
                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                        <a class="page-link" href="#" @click="changePage(currentPage - 1)">&laquo; 上一页</a>
                    </li>

                    <!-- 循环渲染中间的页码按钮 -->
                    <li class="page-item" v-for="number in pageNumbers" :class="{ active: currentPage === number }"
                        :key="number">
                        <a class="page-link" href="#" @click="changePage(number)">{{ number }}</a>
                    </li>

                    <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                        <a class="page-link" href="#" @click="changePage(currentPage + 1)">下一页 &raquo;</a>
                    </li>
                    <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                        <a class="page-link" href="#" @click="changePage(totalPages)">尾页</a>
                    </li>
                </ul>
            </nav>

        </div>
        <div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">货源选择</h4>
                    </div>
                    <div class="modal-body">
                        <select class="form-control" v-model="selectedHy">
                            <option value="" disabled selected>请下拉选择</option>
                            <option v-for="hy in hyList" :value="hy.hid">{{ hy.name }}</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" @click="selectHyAndSearchData">确认选择</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade primary" id="modal-add1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">平台添加</h4>
                    </div>

                    <div class="modal-body">
                        <form class="form-horizontal" id="form-add">
                            <input type="hidden" name="action" value="add" />
                            <div class="form-group">
                                <label class="col-sm-3 control-label">排序</label>
                                <div class="col-sm-9">
                                    <input type="text" name="sort" class="form-control"
                                        placeholder="输入数字,不填默认10,商品排序从小到大">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">平台名字</label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" class="form-control" placeholder="输入平台名字"
                                        v-model="currentSelectedItem.name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">定价</label>
                                <div class="col-sm-9">
                                    <input type="text" name="price" class="form-control" placeholder="输入价格"
                                        v-model="currentSelectedItem.price*1.1">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">查询参数</label>
                                <div class="col-sm-9">
                                    <input type="text" name="getnoun" class="form-control" placeholder="输入标识"
                                        v-model="currentSelectedItem.cid">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">对接参数</label>
                                <div class="col-sm-9">
                                    <input type="text" name="noun" class="form-control" placeholder="输入标识"
                                        v-model="currentSelectedItem.cid">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">说明</label>
                                <div class="col-sm-9">
                                    <textarea name="content" class="form-control" rows="3"
                                        v-model="currentSelectedItem.content"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">查询平台</label>
                                <div class="col-sm-9">
                                    <select name="queryplat" class="form-control" v-model="selectedHy">
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
                                    <select name="docking" class="form-control" v-model="selectedHy">
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
        
        <!-- 模态框 -->
<div class="modal fade" id="yjdjModal" tabindex="-1" role="dialog" aria-labelledby="yjdjModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="yjdjModalLabel">一键对接信息填写</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="categoryId">货源分类/id</label>
                        <select id="categoryId" class="layui-select" v-model="category"style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">
                            <option value="999999">全部分类</option>
                            <option v-for="category in uniqueCategories" :key="category.value" :value="category.value">
                                {{ category.label || category.value }}
                            </option>
                        </select>

                    </div>
                    <div class="form-group">
                        <label for="categoryName">添加到已有分类</label>
                        <select id="categoryName" v-model="name" class="layui-select" style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%">	
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
                                            echo '<option value="' . $b['name'] . '">' . $prefix . $b['name'] . '</option>';
                                        }
                                        }
                                    ?>
	                            </select>
                        <label for="categoryName">自定义新分类</label>
                        <input type="text" class="form-control" id="categoryName" v-model="name">
                    </div>
                    <div class="form-group">
                        <label for="priceRate">价格倍率</label>
                        <input type="text" class="form-control" id="priceRate" v-model="pricee">
                    </div>
                    <div class="form-group">
                        <label>是否只更新</label>
                        <div>
                            <input type="radio" id="fdYes" value="1" v-model="fd">
                            <label for="fdYes">是,只更新已对接项目</label>
                        </div>
                        <div>
                            <input type="radio" id="fdNo" value="0" v-model="fd">
                            <label for="fdNo">否，更新并上架新项目</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" @click="submitYjdj()">确认对接</button>
            </div>
        </div>
    </div>
</div>
    </div>
</div>





<?php require_once("footer.php");?>
<script>
  vm=new Vue({
        el: "#orderlist",
        data: {
            row: [],
            filteredRows: [], // 用于存储过滤后的数据
            hyList: [], // 货源列表
            category: '',    // 存储输入的分类ID
            name: '',        // 存储输入的分类名称
            pricee: '',      // 存储输入的价格倍率
            fd:1,
            selectedHy: null, // 选中的货源
            searchQuery: '', // 搜索关键词
            currentSelectedItem: [], // 用于存储当前选中项目的数据
            currentPage: 1, // 当前页码
            pageSize: 20,   // 每页显示的条目数量
            selectAll: false,// 全选复选框的绑定值
            selectedRows: [], // 添加 selectedRows 变量
            selectedCategory: '', // 绑定下拉框的值
        },
        methods: {
            selectHyAndSearchData: function () {
                if (this.selectedHy && this.hyList.length === 0) {
                    this.gethy(1);
                }
                if (this.selectedHy) {
                    var selectedHyObj = this.hyList.find(hy => hy.hid === this.selectedHy);
                    if (selectedHyObj) {
                        var load = layer.load(2);
                        var postData = {
                            uid: selectedHyObj.user,
                            key: selectedHyObj.pass,
                            url: selectedHyObj.url,
                            token: selectedHyObj.token,
                            hid:selectedHyObj.hid
                        };

                        this.$http.post('/apisub.php?act=getclassdata', postData, { emulateJSON: true }).then(function (response) {
                            layer.close(load);
                            var data = response.body;
                            if (data.code == 1) {
                                this.row = data.data;
                                this.searchData(); 
                            } else {
                                layer.msg(data.msg, { icon: 2 });
                            }
                        }.bind(this), function (response) {
                            layer.close(load);
                            layer.msg('请求失败', { icon: 2 });
                        });
                    }
                }

                $('#modal-add').modal('hide');
            },
            searchData: function () {
                // 过滤数据
                this.filteredRows = this.row.filter((res) => {
                    // 搜索关键词匹配
                    const matchesQuery = this.searchQuery ? 
                        (res.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                         res.content.toLowerCase().includes(this.searchQuery.toLowerCase())) : true;
                    
                    // 分类匹配
                    const matchesCategory = this.selectedCategory ? 
                        (res.category_name && res.category_name.includes(this.selectedCategory)) ||
                        (res.fenlei && res.fenlei.includes(this.selectedCategory)) : true;
                    
                    // 只有当关键词匹配和分类匹配条件都符合时才返回 true
                    return matchesQuery && matchesCategory;
                });
            
                // 将当前页码设置为 1
                this.currentPage = 1;
            },

            gethy: function (page) {
                var load = layer.load(2);
                this.$http.post("/apisub.php?act=huoyuanlist", { page: page }, { emulateJSON: true }).then(function (response) {
                    layer.close(load);
                    if (response.data.code == 1) {
                        this.hyList = response.data.data;
                    } else {
                        layer.msg(response.data.msg, { icon: 2 });
                    }
                }.bind(this));
            },
            selectItem: function (res) {
                this.currentSelectedItem = res;
                // 然后打开模态框，并预填充数据
                $('#modal-add1').modal('show');
            },
            form: function (form) {
                var load = layer.load(2);

                this.$http.post("/apisub.php?act=upclass", { data: $("#form-" + form).serialize() }, { emulateJSON: true }).then(function (data) {
                    layer.close(load);
                    console.log($("#form-" + form).serialize())
                    if (data.data.code == 1) {
                        $("#modal-" + form + 1).modal('hide');
                        layer.msg(data.data.msg, { icon: 1 });
                    } else {
                        layer.msg(data.data.msg, { icon: 2 });
                    }
                });
            },
            form1: function(form) {
            var vm = this; // 保存 Vue 实例的引用
            if (vm.selectedRows.length === 0) {
                layer.msg('请选择要更新上架的项目', {icon: 2});
                return;
            }
            layer.confirm('确定要更新上架所选项目吗？', { // 弹出确认对话框
                title: '确认', // 对话框标题
                icon: 3 // 提示图标为问号
            }, function() { // 点击确认按钮的回调函数
                var load = layer.load(2); // 加载提示
                vm.$http.post("/apisub.php?act=upclass", {cid: vm.selectedRows}, {emulateJSON: true})
                    .then(function(data) { // 请求成功的处理函数
                        layer.close(load); // 关闭加载提示
                        if (data.data.code == 1) { // 如果操作成功
                            vm.selectedRows = []; // 清空选中行数组
                            vm.selectAll = false; // 取消全选状态
                            layer.msg(data.data.msg, {icon: 1}); // 显示成功提示
                        } else { // 如果操作失败
                            layer.msg(data.data.msg, {icon: 2}); // 显示失败提示
                        }
                    }, function(error) { // 请求失败的处理函数
                        layer.close(load); // 关闭加载提示
                        layer.msg('处理失败', {icon: 2}); // 显示失败提示
                    });
            });
        },
            changePage: function(newPage) {
                if (newPage >= 1 && newPage <= this.totalPages) {
                    this.currentPage = newPage;
                }
            },
            cleanPrice: function(price) {
                // 使用正则表达式移除所有非数字和非小数点字符
                const cleanPrice = String(price).replace(/[^\d.]/g, '');
                // 尝试将清理后的字符串转换为数字
                const numericPrice = parseFloat(cleanPrice);
                // 如果转换成功，则除以5并保留两位小数；否则显示 'N/A'
                return !isNaN(numericPrice) ? (numericPrice / 5).toFixed(2) : 'N/A';
            },
            selectAllRows: function() {
            if (this.selectAll) {
                // 如果全选复选框选中，则将所有行数据添加到选中行数组中
                this.selectedRows = this.filteredRows.map(res => res.cid);
            } else {
                // 如果取消全选，则清空选中行数组
                this.selectedRows = [];
            }
        },
            
            updateStatus: function() {
                // 检查是否选择了货源
                if (!this.selectedHy) {
                    layer.msg('请先选择一个货源', { icon: 2 });
                    return; // 防止后续代码执行
                }
            
                var vm = this; // 保存 Vue 实例的引用
                var hid = this.selectedHy; // 获取货源 ID
            
                // 显示确认对话框
                layer.confirm('确定要更新当前货源的商品状态吗？', {
                    title: '确认',
                    icon: 3
                }, function() { // 确认后执行以下操作
                    var load = layer.load(2); // 显示加载动画
            
                    // 发起 GET 请求，进行对接操作
                    $.get("/apisub.php?act=updateStatus&hid=" + hid, function(data) {
                        layer.close(load); // 关闭加载动画
                        if (data.code == 1) {
                            layer.msg(data.msg, { icon: 1 });
                        } else {
                            layer.msg(data.msg, { icon: 2 });
                        }
                    });
                });
            },
            // 打开模态框
            openModal: function() {
                if (!this.selectedHy) {
                    layer.msg('请先选择一个货源', { icon: 2 });
                } else {
                    $('#yjdjModal').modal('show');
                }
            },
            
            // 提交对接请求
            submitYjdj: function() {
                // 确保已经填写了所有必填项
                if (!this.category  || !this.pricee ||!this.fd) {
                    layer.msg('请完整填写表单', { icon: 2 });
                    return;
                }

                var hid = this.selectedHy; // 货源 ID

                var load = layer.load(2); // 显示加载动画

                // 发起 GET 请求，进行对接操作
                $.get("/apisub.php?act=yjdj&hid=" + hid + "&pricee=" + this.pricee +"&fd=" + this.fd + "&category=" + this.category + "&name=" + encodeURIComponent(this.name), function(data) {
                    layer.close(load); // 关闭加载动画
                    if (data.code == 1) {
                        layer.msg(data.msg, { icon: 1 });
                    } else {
                        layer.msg(data.msg, { icon: 2 });
                    }
                });

                $('#yjdjModal').modal('hide'); // 关闭模态框
            },
        },
        computed: {
            filteredRows() {
              if (this.selectedCategory) {
                return this.rows.filter(row => 
                  row.category_name === this.selectedCategory || row.fenlei === this.selectedCategory
                );
              } else {
                return this.rows;
              }
            },
            paginatedRows() {
                let start = (this.currentPage - 1) * this.pageSize;
                let end = start + this.pageSize;
                return this.filteredRows.slice(start, end);
            },
            totalPages() {
                return Math.ceil(this.filteredRows.length / this.pageSize);
            },
            pageNumbers() {
                let pages = [];
                for (let i = this.currentPage - 2; i <= this.currentPage + 2; i++) {
                    if (i > 0 && i <= this.totalPages) {
                        pages.push(i);
                    }
                }
                return pages;
            },
            uniqueCategories() {
              // 创建一个 Map 来存储唯一的分类项
              const categories = new Map();
              this.row.forEach(item => {
                const label = item.category_name || item.fenlei;
                // 使用分类ID作为 Map 的键，保证唯一性
                categories.set(item.fenlei, { value: item.fenlei, label });
              });
              // 将 Map 的值转换为数组返回
              return Array.from(categories.values());
            }
        },
        mounted() {
            this.gethy(1); // 在组件挂载时获取货源列表
        }
    });
</script>