const listVm = new Vue({
  el: '#orderlist',
  data: {
    row: {},
    phone: '',
    sex: [],
    ddinfo3: {
      status: false,
      info: [],
    },
    dc: [],
    dc2: {
      gs: 1,
    },
    cx: {
      status_text: '',
      dock: '',
      qq: '',
      oid: '',
      uid: '',
      cid: '',
      pagesize: '20',
    },
  },
  methods: {
    get: function (page) {
      data = { cx: this.cx, page }
      this.$http
        .post('/apisub.php?act=orderlist', data, { emulateJSON: true })
        .then(function (data) {
          if (data.data.code == 1) {
            this.row = data.body
            this.$nextTick(function () {
              $('.preloader').fadeOut('slow')
            })
          } else {
            iziToast.error({
              title: data.data.msg,
              position: 'topRight',
            })
          }
        })
    },
    handleMenu: function (command) {
      const { res, type } = command
      if (type == 'refresh') {
        this.up(res.oid)
      } else if (type == 'restart') {
        this.bs(res.oid)
      } else if (type == 'quit') {
        this.quxiao(res.oid)
      } else if (type == 'check') {
        this.ddinfo(res)
      }
    },
    bs: function (oid) {
      layer.confirm(
        '建议漏看或者进度被重置的情况下使用。<br>频繁点击补刷会出现不可预测的结果<br>请问是否补刷所选的任务？',
        {
          title: '温馨提示',
          icon: 3,
          btn: ['确定补刷', '取消'], //按钮
        },
        function (index) {
          var load = layer.load(2)
          $.get('/apisub.php?act=bs&oid=' + oid, function (data) {
            layer.close(load)
            layer.close(index)
            if (data.code == 1) {
              listVm.get(listVm.row.current_page)
              iziToast.success({
                title: data.data.msg,
                position: 'topRight',
              })
            } else {
              iziToast.error({
                title: data.data.msg,
                position: 'topRight',
              })
            }
          })
        }
      )
    },
    up: function (oid) {
      var load = layer.load(2)
      iziToast.warning({
        title: '正在努力获取中....',
        position: 'topRight',
      })
      $.get('/apisub.php?act=uporder&oid=' + oid, function (data) {
        layer.close(load)
        if (data.code == 1) {
          listVm.get(listVm.row.current_page)
          setTimeout(function () {
            for (i = 0; i < listVm.row.data.length; i++) {
              if (listVm.row.data[i].oid == oid) {
                listVm.ddinfo3.info = listVm.row.data[i]
                console.log(listVm.row.data[i].oid)
                console.log(listVm.row.data[i].status)
                console.log(listVm.ddinfo3.info.status)
                return true
              }
            }
          }, 1800)
          iziToast.success({
            title: data.msg,
            position: 'topRight',
          })
        } else {
          iziToast.error({
            title: data.msg,
            position: 'topRight',
          })
          //	                layer.alert(data.msg,{icon:2,btn:'立即跳转'},function(){
          //	                	window.location.href=data.url
          //	                });
        }
      })
    },
    plzt: function (sex) {
      if (this.sex == '') {
        layer.msg('请先选择订单！')
        return false
      }
      layer.confirm(
        '是否确认入队，入队后等待线程执行即可，禁止一直重复入队！20分钟内订单禁止入队，切记',
        { title: '温馨提示', icon: 3, btn: ['确认', '取消'] },
        function (index) {
          var load = layer.load()
          $.post(
            '/apisub.php?act=plzt',
            { sex: sex },
            { emulateJSON: true }
          ).then(function (data) {
            layer.close(load)
            layer.close(index)
            if (data.code == 1) {
              listVm.selectAll()
              listVm.get(listVm.row.current_page)
              iziToast.success({
                title: data.msg,
                position: 'topRight',
              })
            } else {
              iziToast.error({
                title: data.msg,
                position: 'topRight',
              })
            }
          })
        }
      )
    },
    plbs: function (sex) {
      if (this.sex == '') {
        layer.msg('请先选择订单！')
        return false
      }
      layer.confirm(
        '是否确认入队补刷，入队后等待线程执行即可，禁止一直重复入队！20分钟内订单禁止入队，切记',
        { title: '温馨提示', icon: 3, btn: ['确认', '取消'] },
        function (index) {
          var load = layer.load()
          $.post(
            '/apisub.php?act=plbs',
            { sex: sex },
            { emulateJSON: true }
          ).then(function (data) {
            layer.close(load)
            layer.close(index)
            if (data.code == 1) {
              listVm.selectAll()
              listVm.get(listVm.row.current_page)
              iziToast.success({
                title: data.msg,
                position: 'topRight',
              })
            } else {
              iziToast.error({
                title: data.msg,
                position: 'topRight',
              })
            }
          })
        }
      )
    },
    duijie: function (oid) {
      layer.confirm(
        '确定处理么?',
        {
          title: '温馨提示',
          icon: 3,
          btn: ['确定', '取消'], //按钮
        },
        function (index) {
          var load = layer.load(2)
          $.get('/apisub.php?act=duijie&oid=' + oid, function (data) {
            layer.close(load)
            layer.close(index)
            if (data.code == 1) {
              listVm.get(listVm.row.current_page)
              iziToast.success({
                title: data.msg,
                position: 'topRight',
              })
            } else {
              iziToast.error({
                title: data.msg,
                position: 'topRight',
              })
            }
          })
        }
      )
    },
    getname: function (oid) {
      var load = layer.load(2)
      $.get('/apisub.php?act=getname&oid=' + oid, function (data) {
        layer.close(load)
        if (data.code == 1) {
          iziToast.success({
            title: data.msg,
            position: 'topRight',
          })
        } else {
          iziToast.error({
            title: data.msg,
            position: 'topRight',
          })
        }
      })
    },
    ms: function (oid) {
      layer.confirm(
        '提交秒刷将扣除0.05元服务费',
        {
          title: '温馨提示',
          icon: 3,
          btn: ['确定', '取消'], //按钮
        },
        function (index) {
          var load = layer.load(2)
          $.get('/apisub.php?act=ms_order&oid=' + oid, function (data) {
            layer.close(load)
            layer.close(index)
            if (data.code == 1) {
              listVm.get(listVm.row.current_page)
              iziToast.success({
                title: data.msg,
                position: 'topRight',
              })
            } else {
              iziToast.error({
                title: data.msg,
                position: 'topRight',
              })
            }
          })
        }
      )
    },
    quxiao: function (oid) {
      layer.confirm(
        '取消订单将无法退款，确定取消吗',
        {
          title: '温馨提示',
          icon: 3,
          btn: ['确定', '取消'], //按钮
        },
        function (index) {
          var load = layer.load(2)
          $.get('/apisub.php?act=qx_order&oid=' + oid, function (data) {
            layer.close(load)
            layer.close(index)
            if (data.code == 1) {
              listVm.get(listVm.row.current_page)
              iziToast.success({
                title: data.msg,
                position: 'topRight',
              })
            } else {
              iziToast.error({
                title: data.msg,
                position: 'topRight',
              })
            }
          })
        }
      )
    },
    status_text: function (a) {
      var load = layer.load(2)
      $.post(
        '/apisub.php?act=status_order&a=' + a,
        { sex: this.sex, type: 1 },
        { emulateJSON: true }
      ).then(function (data) {
        layer.close(load)
        if (data.code == 1) {
          listVm.selectAll()
          listVm.get(listVm.row.current_page)
          iziToast.success({
            title: data.msg,
            position: 'topRight',
          })
        } else {
          iziToast.error({
            title: data.msg,
            position: 'topRight',
          })
        }
      })
    },
    dock: function (a) {
      var load = layer.load(2)
      $.post(
        '/apisub.php?act=status_order&a=' + a,
        { sex: this.sex, type: 2 },
        { emulateJSON: true }
      ).then(function (data) {
        layer.close(load)
        if (data.code == 1) {
          listVm.selectAll()
          listVm.get(listVm.row.current_page)
          iziToast.success({
            title: data.msg,
            position: 'topRight',
          })
        } else {
          iziToast.error({
            title: data.msg,
            position: 'topRight',
          })
        }
      })
    },
    selectAll: function () {
      if (this.sex.length == 0) {
        for (i = 0; i < listVm.row.data.length; i++) {
          listVm.sex.push(this.row.data[i].oid)
        }
      } else {
        this.sex = []
      }
    },
    ddinfo: function (a) {
      this.ddinfo3.info = a
      $('#modal-ddinfo2').modal('show')
    },
    tk: function (sex) {
      if (this.sex == '') {
        iziToast.error({
          title: '请先选择订单！',
          position: 'topRight',
        })
        return false
      }
      layer.confirm(
        '确定要退款吗？陛下，三思三思！！！',
        { title: '温馨提示', icon: 3, btn: ['确定', '取消'] },
        function (index) {
          var load = layer.load()
          $.post(
            '/apisub.php?act=tk',
            { sex: sex },
            { emulateJSON: true }
          ).then(function (data) {
            layer.close(load)
            layer.close(index)
            if (data.code == 1) {
              listVm.selectAll()
              listVm.get(listVm.row.current_page)
              iziToast.success({
                title: data.msg,
                position: 'topRight',
              })
            } else {
              iziToast.error({
                title: data.msg,
                position: 'topRight',
              })
            }
          })
        }
      )
    },
    daochu: function () {
      if (this.dc2.gs == '') {
        iziToast.error({
          title: '请先选择格式',
          position: 'topRight',
        })
        return false
      }
      if (!this.sex[0]) {
        iziToast.error({
          title: '请先选择订单',
          position: 'topRight',
        })
        return false
      }
      for (i = 0; i < this.sex.length; i++) {
        oid = this.sex[i]
        for (x = 0; x < this.row.data.length; x++) {
          if (this.row.data[x].oid == oid) {
            school = this.row.data[x].school
            user = this.row.data[x].user
            pass = this.row.data[x].pass
            kcname = this.row.data[x].kcname
            if (this.dc2.gs == '1') {
              a = school + ' ' + user + ' ' + pass + ' ' + kcname
            } else if (this.dc2.gs == '2') {
              a = user + ' ' + pass + ' ' + kcname
            } else if (this.dc2.gs == '3') {
              a = school + ' ' + user + ' ' + pass
            } else if (this.dc2.gs == '4') {
              a = user + ' ' + pass
            }
            this.dc.push(a)
          }
        }
      }
      layer.alert(this.dc.join('<br>'))
      this.dc = []
    },
  },
  created() {
    this.get(1)
  },
  filters: {
    formatCount: function (value) {
      if (!value) return 0
      return value.replace('%', '')
    },
  },
})
