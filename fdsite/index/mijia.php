<?php
$title='编辑用户密价';
require_once('head.php');
if($userrow['uid']!=1){exit("<script language='javascript'>window.location.href='login.php';</script>");}

$uid=$_GET['uid'];
?>
<style>
.search-and-buttons,
.operation-buttons {
  margin-bottom: 20px; /* 添加一些底部间距 */
}

.input-with-select {
  width: 100%; /* 使输入框和下拉框宽度为100% */
}

.margin-right {
  margin-right: 10px; /* 为添加按钮添加右边距 */
}
.title {
  margin-bottom: 20px;
}

.title-text {
  margin: 0;
  font-size: 18px; /* 调整标题大小 */
}

.search-and-buttons {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.input-with-select {
  margin-right: 10px;
}

.margin-right {
  margin-right: 10px;
}
</style>
<style>
.lioverhide {
     width: 300px
  }
.el-select-dropdown .el-select-dropdown__item {
  white-space: nowrap !important;
  overflow-x: auto !important;
  text-overflow: clip;
  scrollbar-width: none;
}
.el-select {
    width: 100% !important;
  }
/* 响应式样式 */
@media (max-width: 1088px) {
  .el-select {
    width: 100% !important;
  }
  .el-dialog {
    width: 80% !important;
  }
</style>
<body>
<div id="app" class="app-content-body">
    <div class="wrapper-md control" id="mijialist">
        <el-card>
            <!-- 标题 -->
            <div class="title">
              <h3 class="title-text">密价列表</h3>
            </div>
            
             <!-- 搜索框、下拉框 -->
            <el-row class="search-and-buttons" type="flex" justify="start" align="middle">
              <!-- 下拉框：用户ID -->
              <el-col :span="8">
                <el-select v-model="selectedUid" placeholder="用户UID" class="input-with-select" filterable>
                  <el-option label="请选择用户UID" value=""></el-option>
                  <el-option v-for="item in uidOptions" :key="item.value" :label="item.label" :value="item.value"></el-option>
                </el-select>
              </el-col>
              
              <!-- 搜索框 -->
              <el-col :span="16">
                <el-input placeholder="请输入关键词" v-model="keyword" clearable class="input-with-select">
                  <el-button slot="append" icon="el-icon-search" @click="get(1,pagesize)"></el-button>
                </el-input>
              </el-col>
            </el-row>
            <!-- 操作按钮 -->
            <el-row class="operation-buttons" type="flex" justify="start" align="middle">
              <el-col :span="24">
                <el-button type="success" size="mini" class="margin-right" @click="openAddModal">添加密价</el-button>
                <el-button type="danger" size="mini" @click="del()">批量删除</el-button>
              </el-col>
            </el-row>
            <el-table :data="row" @selection-change="handleSelectionChange" stripe>
                <el-table-column type="selection" width="50" :header-align="'center'" :align="'center'"></el-table-column>
                <el-table-column prop="mid" label="ID"></el-table-column>
                <el-table-column prop="uid" label="UID"></el-table-column>
                <el-table-column prop="name" label="课程"></el-table-column>
                <el-table-column :formatter="formatMode" label="类型"></el-table-column>
                <el-table-column prop="price" label="金额/倍数"></el-table-column>
                <el-table-column prop="addtime" label="添加时间"></el-table-column>
                <el-table-column label="操作">
                    <template slot-scope="scope">
                        <el-button type="primary" size="mini" @click="openUpdateModal(scope.row)">编辑</el-button>
                        <el-button type="danger" size="mini" @click="del(scope.row.mid)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <el-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page="currentPage"
                :page-sizes="[10, 20, 50, 100]"
                :page-size="pagesize"
                layout="total, sizes, prev, pager, next, jumper"
                :total="total">
            </el-pagination>
        </el-card>

    <!-- 编辑模态框 -->
    <el-dialog :title="'密价修改'" :visible.sync="updateModalVisible">
      <el-form :model="upm" >
        <el-row :gutter="20">
          <el-col :span="24">
            <el-form-item label="UID">
              <el-input v-model="upm.uid"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="24">
            <el-form-item label="商品">
              <el-select v-model="upm.cid"  popper-class="lioverhide"  placeholder="请选择商品" filterable>
                <el-option v-for="item in classOptions" :key="item.cid" :label="item.name+'('+item.price+'币)'" :value="item.cid"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="24">
            <el-form-item label="类型">
              <el-select v-model="upm.mode">
                <el-option value="0" label="价格的基础上扣除"></el-option>
                <el-option value="1" label="倍数的基础上扣除"></el-option>
                <el-option value="2" label="直接定价"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="24">
            <el-form-item label="金额/倍数">
              <el-input v-model="upm.price"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="updateModalVisible = false">取消</el-button>
        <el-button type="primary" @click="up_m">确定</el-button>
      </span>
    </el-dialog>

    <!-- 添加模态框 -->
    <el-dialog :title="'密价添加'" :visible.sync="addModalVisible">
      <el-form :model="addm" >
        <el-row :gutter="20">
          <el-col :span="24">
            <el-form-item label="UID">
              <el-input v-model="addm.uid"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="24">
            <el-form-item label="选择单个商品">
              <el-select v-model="addm.cid" popper-class="lioverhide"  placeholder="请选择商品" filterable>
                <el-option label="请选择商品" value=""></el-option>
                <el-option v-for="item in classOptions" :key="item.cid" :label="item.name+'('+item.price+'币)'" :value="item.cid"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="24">
            <el-form-item label="按分类批量设置">
              <el-select v-model="addm.fenlei" placeholder="请选择分类" filterable>
                <el-option label="请选择分类" value=""></el-option>
                <el-option v-for="item in fenleiOptions" :key="item.id" :label="item.name" :value="item.id"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="24">
            <el-form-item label="类型">
              <el-select v-model="addm.mode">
                <el-option value="0" label="价格的基础上扣除"></el-option>
                <el-option value="1" label="倍数的基础上扣除"></el-option>
                <el-option value="2" label="直接定价"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="24">
            <el-form-item label="金额/倍数">
              <el-input v-model="addm.price"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="addModalVisible = false">取消</el-button>
        <el-button type="primary" @click="add_m">确定</el-button>
      </span>
    </el-dialog>
    </div>
</div>

<?php require_once("footer.php");?>
<script src="./assets/element/index.js"></script>
<link rel="stylesheet" href="./assets/element/index.css">

<script>
new Vue({
    el: '#mijialist',
    data() {
        return {
            row: null,
            uid: "<?php echo $uid ?>",
            storeInfo: {},
            addm: {
                uid: "<?php echo $uid ?>"
            },
            upm: {},
            keyword:null,
            selectedCid:null,
            selectedFenlei:null,
            updateModalVisible: false,
            addModalVisible: false,
            classOptions: [],
            fenleiOptions:[],
            currentPage: 1,
            total: 10,
            pagesize: 10,
            lastPage: 0,
            selectedUid: '', // 选中的用户UID
            uidOptions: [], // 用户UID下拉框选项
            selectedRows: [], // 用于存储选中的行
        };
    },
    methods: {
        get:function(page, pagesize) {
          const load = this.$loading({ text: '正在加载，请稍候...' });
          this.$http.post("/apisub.php?act=mijialist", {pagesize: pagesize, page: page, selectedUid:this.selectedUid, keyword: this.keyword}, { emulateJSON: true }).then(response => {
            load.close();
            const data = response.data;
            if (data.code == 1) {
              this.row = data.data;
              this.total = parseInt(data.total, 10); // 总条目数
              this.lastPage = data.last_page; // 最后一页的页码
              // 使用后端返回的所有唯一的uids来填充下拉框
              if (data.uids) {
                this.uidOptions = data.uids.map(uid => ({ label: uid, value: uid }));
              }
            } else {
              layer.msg(data.msg, { icon: 2 });
            }
          }).catch(() => {
            layer.close(load);
            layer.msg('加载数据失败，请重试', { icon: 2 });
          });
        },
        formatMode:function(row, column) {
            const modeMap = {
                0: '价格的基础上扣除',
                1: '倍数的基础上扣除',
                2: '直接定价',
                3: '按照赠送比例折扣'
            };
            return modeMap[row.mode];
        },
        openAddModal:function() {
            this.addModalVisible = true;
        },
        openUpdateModal:function(row) {
            this.upm = Object.assign({}, row);
            this.updateModalVisible = true;
        },
        del:function(mid) {
          // 检查是否传入了mid参数，如果没有则表示批量删除
          const mids = mid ? [mid] : this.selectedRows.map(row => row.mid);
          if (mids.length === 0) {
            this.$message({
              message: '请选择要删除的项',
              type: 'warning',
            });
            return;
          }
        
          // 使用Element UI的$confirm方法来显示二次确认对话框
          this.$confirm('确认删除这些记录吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
          }).then(() => {
            // 用户点击了确认按钮，执行删除操作
            var load = layer.load(2);
            this.$http.post("/apisub.php?act=mijia_del", { mids: mids.join(',') }, { emulateJSON: true }).then(function(data) {
              layer.close(load);
              if (data.data.code == 1) {
                this.get(this.currentPage, this.pagesize); // 重新获取数据
                this.currentPage = 1; // 重置为第一页
                this.selectedRows = []; // 清空选中的行
                layer.msg(data.data.msg, { icon: 1 }); // 显示成功消息
              } else {
                layer.msg(data.data.msg, { icon: 2 }); // 显示失败消息
              }
            }).catch(function(error) {
              layer.close(load);
              layer.msg('删除失败，请重试', { icon: 2 }); // 显示失败消息
            });
          }).catch(() => {
            // 用户点击了取消按钮或关闭了对话框，不执行任何操作
            layer.msg('已取消删除', { icon: 0 }); // 可选：显示已取消的消息
          });
        },
        handleSelectionChange:function(val) {
          this.selectedRows = val;
        },
        add_m:function() {
            var load = layer.load(2);
            this.$http.post("/apisub.php?act=mijia", { data: this.addm, active: 1 }, { emulateJSON: true }).then(function(data) {
                layer.close(load);
                if (data.data.code == 1) {
                    this.get(1,10);
                    this.addModalVisible = false;
                    layer.msg(data.data.msg, { icon: 1 });
                } else {
                    layer.msg(data.data.msg, { icon: 2 });
                }
            });
        },
        up_m:function() {
            var load = layer.load(2);
            this.$http.post("/apisub.php?act=mijia", { data: this.upm, active: 2 }, { emulateJSON: true }).then(function(data) {
                layer.close(load);
                if (data.data.code == 1) {
                    this.get(1,10);
                    this.updateModalVisible = false;
                    layer.msg(data.data.msg, { icon: 1 });
                } else {
                    layer.msg(data.data.msg, { icon: 2 });
                }
            });
        },
        handleSizeChange: function(val) {
            console.log(`每页 ${val} 条`);
            this.pagesize = val;
            this.currentPage = 1; // 重置为第一页
            this.get(this.currentPage, this.pagesize);
        },
        handleCurrentChange: function(val) {
            this.currentPage = val;
            this.get(this.currentPage, this.pagesize);
        },
        getclass:function(){
		  var load=layer.load(2);
 			this.$http.post("/apisub.php?act=getclass").then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){			                     	
	          		this.classOptions=data.body.data;			             			                     
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
	    	
	    },
	    getfenlei:function(){
		  var load=layer.load(2);
 			this.$http.post("/apisub.php?act=fllist").then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){			                     	
	          		this.fenleiOptions=data.body.data;			             			                     
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
	    	
	    },
    },
    mounted() {
        this.getclass();
        this.get(1,10);
        this.getfenlei();
    }
});
</script>
</body>
</html>