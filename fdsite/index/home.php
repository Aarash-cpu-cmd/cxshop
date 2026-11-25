<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<link rel="icon" href="./assets/LightYear/favicon.ico" type="image/ico">
<meta name="author" content=" ">
<script src="../assets/js/sweetalert2.min.js"></script>
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="./assets/css/apps.css" type="text/css" />
<link rel="stylesheet" href="./assets/css/app.css" type="text/css" />
<link rel="stylesheet" href="../assets/layui/css/layui.css" type="text/css" />
<link href="../assets/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="./assets/LightYear/js/bootstrap-multitabs/multitabs.min.css">
<link href="./assets/LightYear/css/bootstrap.min.css" rel="stylesheet">
<link href="./assets/LightYear/css/style.min.css" rel="stylesheet">
<link href="./assets/LightYear/css/materialdesignicons.min.css" rel="stylesheet">
<script src="../assets/js/jquery.min.js"></script>
<script src="./layer/3.1.1/layer.js"></script>
</head>
<body>
<style>
  .card-body {
    padding: 20px;
    background-color: #fff;
    border-radius: 4px;
    box-shadow: 0 2px 12px 0 rgba(0,0,0,.1);
  }
  .card-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 20px;
  }
  .el-timeline {
    margin: 0;
    padding: 0;
    list-style: none;
  }
  .el-timeline-item {
    position: relative;
    padding-bottom: 20px;
  }
  .el-timeline-item__tail {
    position: absolute;
    left: 4px;
    height: 100%;
    border-left: 2px solid #e4e7ed;
  }
  .el-timeline-item__node {
    position: absolute;
    background-color: #e4e7ed;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .el-timeline-item__node--normal {
    left: -1px;
    width: 12px;
    height: 12px;
  }
  .el-timeline-item__wrapper {
    padding-left: 28px;
  }
  .el-timeline-item__timestamp {
    color: #909399;
    line-height: 1;
    font-size: 13px;
    margin-bottom: 8px;
  }
  .el-card {
    border: 1px solid #ebeef5;
    background-color: #fff;
    color: #303133;
    transition: .3s;
    margin-bottom: 20px;
  }
  .el-card__body {
    padding: 10px;
  }
  .notice-card h4 {
    margin: 0 0 10px 0;
    font-size: 16px;
  }
  .notice-card p {
    margin: 0;
    font-size: 14px;
    color: #606266;
  }
  .load-more {
    text-align: center;
    margin-top: 20px;
  }
  .btn-load-more {
    padding: 10px 20px;
    background-color: #409eff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  .btn-load-more:hover {
    background-color: #66b1ff;
  }
</style>
<style>
.custom-popup {
  border-radius: 10px; /* 弹窗圆角 */
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* 弹窗阴影 */
  padding: 20px; /* 弹窗内边距 */
  width: 50%; /* 弹窗宽度 */
  height: auto; /* 弹窗高度自适应 */
}

.custom-title {
  font-size: 34px; /* 标题字体大小 */
  font-weight: bold; /* 标题加粗 */
  margin-bottom: 10px; /* 标题与内容间距 */
  text-align: center; /* 标题居中 */
}

.custom-content {
  font-size: 16px; /* 内容字体大小 */
  color: #333; /* 内容颜色 */
  text-align: left; /* 文本左对齐 */
}

.custom-icon {
  margin: 0 auto; /* 图标居中 */
  display: block; /* 图标块级元素 */
}

.custom-confirm-button,
.custom-cancel-button {
  margin: 10px; /* 按钮间隔 */
  padding: 10px 20px; /* 按钮内边距 */
  font-size: 12px; /* 按钮字体大小 */
  border-radius: 5px; /* 按钮圆角 */
}

.custom-confirm-button {
  background-color: #4CAF50; /* 确定按钮背景色 */
  color: white; /* 确定按钮字体颜色 */
}

.custom-cancel-button {
  background-color: #f44336; /* 取消按钮背景色 */
  color: white; /* 取消按钮字体颜色 */
}

/* 媒体查询：针对手机屏幕 */
@media (max-width: 768px) {
  .custom-popup {
    width: 90%; /* 弹窗宽度调整为90% */
  }

  .custom-title {
    font-size: 24px; /* 标题字体大小调整 */
  }

  .custom-content {
    font-size: 14px; /* 内容字体大小调整 */
  }

  .custom-confirm-button,
  .custom-cancel-button {
    font-size: 14px; /* 按钮字体大小调整 */
  }
}
</style>
<body>
  <div class="container-fluid p-t-15">
    <div class="row">
      <div class="col-lg-12">
        <section class="content" id="gg">
          <div class="card-body">
            <div class="card-title">最新公告</div>
            <div class="card-content">
              <div class="notice-block">
                <ul class="el-timeline">
                  <li v-for="res in row.data" :key="res.Aid" class="el-timeline-item">
                    <div class="el-timeline-item__tail"></div>
                    <div class="el-timeline-item__node el-timeline-item__node--normal"></div>
                    <div class="el-timeline-item__wrapper">
                      <div class="el-timeline-item__timestamp is-top">
                        {{formatDate(res.PublishDate)}}
                      </div>
                      <div class="el-timeline-item__content">
                        <div class="el-card notice-card is-always-shadow">
                          <div class="el-card__body">
                            <h4>{{res.Title}}</h4>
                            <p v-html="res.Content"></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="load-more" v-if="hasMore">
            <button @click="loadMore" class="btn-load-more">加载更多</button>
          </div>
        </section>
      </div>
    </div>
  </div>

<script type="text/javascript" src="../assets/LightYear/js/jquery.min.js"></script>
<script type="text/javascript" src="../assets/LightYear/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../assets/LightYear/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="../assets/LightYear/js/main.min.js"></script>
<script src="../assets/js/aes.js"></script>
<script src="./js/vue.min.js"></script>
<script src="./js/vue-resource.min.js"></script>
<script src="./assets/js/axios.min.js"></script>
<script src="./js/sweetalert.min.js"></script>
<script type="text/javascript">
 var vm = new Vue({
    el: "#gg",
    data: {
      row: {
        data: []
      },
      page: 1,
      pageSize: 10,
      hasMore: true,
      total: 0
    },
    methods: {
        fetchAnnouncements: function(options) {
          var load = layer.load(2);
          this.$http.post("/apisub.php?act=getgonggaolist", {
            type: options.type || "全站公告",
            page: options.page || this.page,
            pageSize: options.pageSize || this.pageSize
          }, {emulateJSON: true}).then(function(response) {
            layer.close(load);
            var data = response.data;
            if (data.code == 1) {
              if (options.callback) {
                options.callback(data);
              } else {
                this.row.data = this.row.data.concat(data.data);
                this.hasMore = data.hasMore;
                this.total = data.total;
                this.page++;
              }
            } else {
              layer.msg(data.msg, {icon: 2});
            }
          }, function(error) {
            layer.close(load);
            layer.msg('加载失败，请稍后重试', {icon: 2});
          });
        },
        
        // 加载公告
        loadAnnouncements: function() {
          this.fetchAnnouncements({
            type: "全站公告",
            page: this.page,
            pageSize: this.pageSize,
            callback: (data) => {
              this.row.data = this.row.data.concat(data.data);
              this.hasMore = data.hasMore;
              this.total = data.total;
              this.page++;
            }
          });
        },
        
        // 加载更多公告
        loadMore: function() {
          this.loadAnnouncements();
        },
        
        // 格式化日期
        formatDate: function(dateString) {
          var date = new Date(dateString);
          return date.toLocaleDateString('zh-CN', { 
            year: 'numeric', 
            month: '2-digit', 
            day: '2-digit'
          });
        },
        
        // 获取首页弹窗公告
        fetchGonggaoList: function() {
          this.fetchAnnouncements({
            type: "首页弹窗",
            page: 1,
            pageSize: 10,
            callback: (data) => {
              this.gonggaoList = data.data;
              this.showPopup();
            }
          });
        },
        
        // 显示弹窗
        showPopup: function() {
          const popupData = this.gonggaoList.find(gonggao => gonggao.Type === '首页弹窗');
          const popupTitle = popupData?.Title || '没有新公告';
          const popupContent1 = popupData?.Content || '';
        
          const storedContent = localStorage.getItem('popupContent1');
        
          if (storedContent !== popupContent1) {
            Swal.fire({
              title: popupTitle,
              html: popupContent1,
              icon: 'info',
              showCancelButton: true,
              confirmButtonText: '确定',
              cancelButtonText: '不再显示',
              customClass: {
                popup: 'custom-popup',
                title: 'custom-title',
                content: 'custom-content',
                icon: 'custom-icon',
                confirmButton: 'custom-confirm-button',
                cancelButton: 'custom-cancel-button'
              },
              buttonsStyling: false // 禁用默认按钮样式
            }).then((result) => {
              if (result.isConfirmed) {
                // 用户点击了确定按钮，可以在这里执行确定按钮的逻辑
              } else if (result.dismiss === Swal.DismissReason.cancel) {
                localStorage.setItem('popupContent1', popupContent1);
              }
            });
          }
        },
    },
    mounted() {
      this.loadAnnouncements();
      this.fetchGonggaoList();
    }
  });
  </script>
</body>