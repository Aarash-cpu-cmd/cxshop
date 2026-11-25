<?php
$title = '公告设置';
require_once('head.php');
if ($userrow['uid'] != 1) {
    alert("你来错地方了", "index.php");
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>公告设置</title>
    <link rel="stylesheet" href="./dk/index.css">
    <script src="./dk/jquery.js"></script>
    <script src="./dk/vue.js"></script>
    <script src="./dk/vue-resource.min.js"></script>
    <script src="./dk/index.js"></script>
    <script src="layer/3.1.1/layer.js"></script>
</head>

<div id="gonggao">
  <!-- 公告浏览 -->
  <el-card shadow="always" class="gonggao-card">
    <h2 class="gonggao-title">公告浏览</h2>
    
    <!-- 发布公告按钮 -->
    <el-button type="primary" @click="openDialog" icon="el-icon-edit" class="publish-btn">发布公告</el-button>
    
    <!-- 公告表格 -->
    <el-table :data="gonggaoList" border stripe>
      <el-table-column label="操作" width="160">
        <template slot-scope="scope">
          <el-button @click="editGonggao(scope.row)" type="text" size="small" icon="el-icon-edit">编辑</el-button>
          <el-button @click="deleteGonggao(scope.row.AID)" type="text" size="small" icon="el-icon-delete" class="delete-btn">删除</el-button>
        </template>
      </el-table-column>
      <el-table-column prop="AID" label="AID" width="80"></el-table-column>
      <el-table-column prop="Title" label="标题" width="150" show-overflow-tooltip></el-table-column>
      <el-table-column prop="Type" label="类型" width="120">
        <template slot-scope="scope">
          <el-tag :type="getTypeColor(scope.row.Type)">{{ scope.row.Type }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="Importance" label="重要程度" width="120">
        <template slot-scope="scope">
          <el-tag :type="getTagType(scope.row.Importance)">{{ scope.row.Importance }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="Content" label="内容" width="350" show-overflow-tooltip></el-table-column>
      <el-table-column prop="PublishDate" label="发布日期" width="180"></el-table-column>
    </el-table>
  </el-card>

  <!-- 公告编辑/发布弹窗 -->
  <el-dialog :visible.sync="dialogVisible" title="公告管理" width="70%">
    <el-form :model="gonggaoForm" ref="gonggaoForm" label-width="100px">
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="公告标题" >
            <el-input v-model="gonggaoForm.Title" placeholder="请输入公告标题"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="24">
          <el-form-item label="公告类型">
  <el-select
    v-model="gonggaoForm.Type"
    placeholder="选择公告类型"
    allow-create
    filterable
  >
    <el-option
      v-for="item in options"
      :key="item.value"
      :label="item.label"
      :value="item.value"
    ></el-option>
  </el-select>
</el-form-item>
        </el-col>
      </el-row>
      
      <el-form-item label="公告内容">
        <el-input type="textarea" v-model="gonggaoForm.Content" placeholder="请输入公告内容" :autosize="{ minRows: 3, maxRows: 5 }"></el-input>
      </el-form-item>

      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item label="重要程度">
            <el-slider v-model="gonggaoForm.Importance" :min="1" :max="5" show-tooltip></el-slider>
          </el-form-item>
        </el-col>
        <el-col :span="24">
          <el-form-item label="发布时间">
            <el-date-picker 
              v-model="gonggaoForm.PublishDate" 
              type="datetime" 
              placeholder="选择发布日期" 
              format="yyyy-MM-dd HH:mm:ss"
              value-format="yyyy-MM-dd HH:mm:ss">
            </el-date-picker>
          </el-form-item>
        </el-col>
      </el-row>

      <el-form-item>
        <el-button type="primary" @click="submitForm">确认</el-button>
        <el-button @click="dialogVisible=false">取消</el-button>
      </el-form-item>
    </el-form>
  </el-dialog>
</div>

<style scoped>
/* 卡片样式优化 */
.gonggao-card {
  margin-bottom: 20px;
  width: 100%;
  padding: 20px;
}

/* 公告标题样式 */
.gonggao-title {
  font-size: 1.5em;
  font-weight: bold;
  margin-bottom: 20px;
}

/* 发布公告按钮位置优化 */
.publish-btn {
  position: fixed;
  right: 30px;
  top: 20px;
  z-index: 1000;
}

/* 表格按钮样式 */
.delete-btn {
  color: red;
}

/* 公告标题超出部分显示省略号 */
.el-table .el-table__cell {
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

/* 自定义公告类型颜色 */
.el-tag[title="全站公告"] {
  background-color: #409eff;
}
.el-tag[title="首页弹窗"] {
  background-color: #67c23a;
}
.el-tag[title="下单弹窗"] {
  background-color: #e6a23c;
}
.el-tag[title="订单弹窗"] {
  background-color: #f56c6c;
}

/* 响应式设计：适配手机 */
@media (max-width: 768px) {
  .gonggao-card {
    padding: 10px;
    width: 100%;
  }

  .gonggao-title {
    font-size: 1.2em;
  }

  .publish-btn {
    right: 10px;
    top: 10px;
  }

  .el-table .el-table__cell {
    white-space: normal;
  }

  /* 弹窗样式适配 */
  .el-dialog {
    width: 90% !important;
  }

  /* 调整表单元素的布局 */
  .el-form-item {
    margin-bottom: 15px;
  }

  /* 调整行和列的间距 */
  .el-row {
    margin-bottom: 10px;
  }

  .el-col {
    margin-bottom: 10px;
  }
}
</style>

<script>
var vm = new Vue({
  el: "#gonggao",
  data() {
    return {
      dialogVisible: false,
      gonggaoForm: {
        Title: '',
        Content: '',
        Type: null,
        Importance: 5,
        PublishDate: null,
        UserID: ''
      },
      gonggaoList: [],
      editingAID: null, // 当前编辑的公告ID
      options: [ // 公告类型的选项
      { label: '全站公告', value: '全站公告' },
      { label: '首页弹窗', value: '首页弹窗' },
      { label: '下单弹窗', value: '下单弹窗' },
      { label: '订单弹窗', value: '订单弹窗' },
      { label: 'FD运动世界弹窗', value: 'FD运动世界弹窗' }
    ]
    };
  },
  mounted() {
    this.fetchGonggaoList();
  },
  methods: {
  fetchGonggaoList() {
    // 使用 emulateJSON 方式发送数据
    this.$http.post('/apisub.php?act=getgonggaolist', {}, { emulateJSON: true }).then((response) => {
      if (response.data.code === 1) {
        this.gonggaoList = response.data.data;
      } else {
        this.$message.error(response.data.msg);
      }
    });
  },
  openDialog() {
    this.resetForm; // 打开弹窗时，确保表单是清空的
    this.dialogVisible = true;
  },
  submitForm() {
      this.$refs.gonggaoForm.validate((valid) => {
        if (valid) {
          const action = this.editingAID ? 'updategonggao' : 'addgonggao';

          // 调用 formatDate 函数格式化 PublishDate 和 ExpiryDate
          const formattedPublishDate = this.formatDate(this.gonggaoForm.PublishDate);
          const formattedExpiryDate = this.formatDate(this.gonggaoForm.ExpiryDate);

          const params = this.editingAID
            ? { AID: this.editingAID, form: { ...this.gonggaoForm, PublishDate: formattedPublishDate, ExpiryDate: formattedExpiryDate } }
            : { form: { ...this.gonggaoForm, PublishDate: formattedPublishDate, ExpiryDate: formattedExpiryDate } };

          console.log('发送的数据:', params);

          this.$http.post(`/apisub.php?act=${action}`, params, { emulateJSON: true }).then((response) => {
            if (response.data.code === 1) {
              this.$message.success('公告操作成功');
              this.fetchGonggaoList();
              this.dialogVisible = false;
              this.resetForm();
            } else {
              this.$message.error(response.data.msg);
            }
          }).catch(() => {
            this.$message.error('公告操作失败');
          });
        } else {
          this.$message.error('请填写完整的公告信息');
        }
      });
    },
  resetForm() {
    this.$refs.gonggaoForm.resetFields();
    this.editingAID = null; // 重置编辑状态
  },
editGonggao(gonggao) {
  // 填充表单数据
  this.gonggaoForm = { ...gonggao };
  
  // 确保 Importance 是数字
  this.gonggaoForm.Importance = Number(this.gonggaoForm.Importance);

  // 设置正在编辑的公告ID
  this.editingAID = gonggao.AID; 
  this.dialogVisible = true; // 打开弹窗
},

  deleteGonggao(AID) {
    this.$confirm('此操作将永久删除该公告, 是否继续?', '提示', {
      confirmButtonText: '继续',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      // 使用 emulateJSON 方式发送数据
      this.$http.post('/apisub.php?act=deletegonggao', { AID }, { emulateJSON: true }).then((response) => {
        if (response.data.code === 1) {
          this.$message.success('公告删除成功');
          this.fetchGonggaoList();
        } else {
          this.$message.error(response.data.msg);
        }
      });
    }).catch(() => {
      this.$message.info('已取消删除');
    });
  },
  formatDate(date) {
      const d = new Date(date);
      const year = d.getFullYear();
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      const hours = String(d.getHours()).padStart(2, '0');
      const minutes = String(d.getMinutes()).padStart(2, '0');
      const seconds = String(d.getSeconds()).padStart(2, '0');
      return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    },
    getTagType(importance) {
      if (importance <= 2) return 'danger';
      if (importance <= 4) return 'warning';
      return 'success';
    },
    getTypeColor(type) {
      switch (type) {
        case '全站公告': return 'primary';
        case '首页弹窗': return 'success';
        case '下单弹窗': return 'warning';
        case '订单弹窗': return 'danger';
        default: return 'success';
      }
    }
}

});
</script>

<style scoped>
.el-card {
  margin-bottom: 20px;
}
</style>
