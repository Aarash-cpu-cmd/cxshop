const add1Vm = new Vue({
  el: '#add',
  data: {
    row: [],
    check_row: [],
    userinfo: '',
    cid: '',
    miaoshua: '',
    class1: '',
    class3: '',
    activems: false,
  },
  methods: {
    add: function () {
      if (!this.cid) {
        iziToast.error({
          title: '请先选择平台',
          position: 'topRight',
        })
        return false
      }
      if (!this.userinfo) {
        iziToast.error({
          title: '请填写信息',
          position: 'topRight',
        })
        return false
      }
      userinfo = this.userinfo
        .replace(/\r\n/g, '[br]')
        .replace(/\n/g, '[br]')
        .replace(/\r/g, '[br]')
      userinfo = userinfo.split('[br]') //分割

      for (var i = 0; this.class1.length > i; i++) {
        if (this.class1[i].cid == this.cid) {
          var price = this.class1[i].price
        }
      }

      kofei = price * userinfo.length

      num = userinfo.length

      layer.confirm(
        "检测到有<b style='color:red'>" +
          userinfo.length +
          '</b>条账号信息，具体扣费以提交结果为准',
        { title: '温馨提示', icon: 3, btn: ['确定交单', '取消'] },
        function () {
          var loading = layer.load(2)
          add1Vm.$http
            .post(
              '/apisub.php?act=add_pl',
              { cid: add1Vm.cid, userinfo: userinfo, num: num },
              { emulateJSON: true }
            )
            .then(
              function (data) {
                layer.close(loading)
                if (data.data.code == 1) {
                  //layer.alert(data.data.msg,{icon:1,title:"提交结果"});
                  iziToast.success({
                    title: data.data.msg,
                    position: 'topRight',
                  })
                  setTimeout(function () {
                    window.location.href = ''
                  }, 2000)
                } else {
                  iziToast.error({
                    title: data.data.msg,
                    position: 'topRight',
                  })
                }
              },
              function () {
                layer.close(loading)
                iziToast.error({
                  title: '服务器错误',
                  position: 'topRight',
                })
              }
            )
        }
      )
    },
    getclass: function () {
      this.$http.post('/apisub.php?act=getclass_pl').then(function (data) {
        if (data.data.code == 1) {
          this.class1 = data.body.data
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
    tips: function (message) {
      for (var i = 0; this.class1.length > i; i++) {
        if (this.class1[i].cid == message) {
          layer.tips(this.class1[i].content, 'select', {
            tips: [1, '#3595CC'],
            time: 4000,
          })
          if (this.class1[i].miaoshua == 1) {
            this.activems = true
          } else {
            this.activems = false
          }
          return false
        }
      }
    },
    tips2: function () {
      layer.tips('秒刷几分钟干完，但不包平时分等内容，慎重选择', '#miaoshua', {
        tips: 1,
      })
    },
  },
  created() {
    this.getclass()
  },
})
