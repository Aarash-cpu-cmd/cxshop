var vm = new Vue({
  el: '#login1',
  data: {
    loginType: true,
    title: '你在看什么呢？我写的代码好看吗',
    dl: {},
    reg: {},
  },
  methods: {
    newlogin: function () {
      this.loginType = !this.loginType
    },
    login: function () {
      if (!this.dl.user || !this.dl.pass) {
        iziToast.error({
          title: '账号密码不能为空',
          position: 'topRight',
        })
        return
      }
      var loading = layer.load(2)
      vm.$http
        .post(
          '/apisub.php?act=login',
          {
            user: this.dl.user,
            pass: this.dl.pass,
          },
          {
            emulateJSON: true,
          }
        )
        .then(function (data) {
          layer.close(loading)
          if (data.data.code == 1) {
            iziToast.success({
              title: data.data.msg,
              position: 'topRight',
            })
            setTimeout(function () {
              window.location.href = '../../../index/qianye'
            }, 1000)
          } else if (data.data.code == 5) {
            vm.login2()
          } else {
            iziToast.error({
              title: data.data.msg,
              position: 'topRight',
            })
          }
        })
    },
    register: function () {
      if (!this.reg.user || !this.reg.pass || !this.reg.name || !this.reg.yqm) {
        iziToast.error({
          title: '所有项不能为空',
          position: 'topRight',
        })
        return
      }
      var loading = layer.load(2)
      this.$http
        .post(
          '/apisub.php?act=register',
          {
            name: this.reg.name,
            user: this.reg.user,
            pass: this.reg.pass,
            yqm: this.reg.yqm,
          },
          {
            emulateJSON: true,
          }
        )
        .then(function (data) {
          layer.close(loading)
          if (data.data.code == 1) {
            this.loginType = true
            this.dl.user = this.reg.user
            this.dl.pass = this.reg.pass
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
    },
    login2: function () {
      layer.prompt(
        {
          title: '管理二次验证',
          formType: 3,
        },
        function (pass2, index) {
          var loading = layer.load(2)
          vm.$http
            .post(
              '/apisub.php?act=login',
              {
                user: vm.dl.user,
                pass: vm.dl.pass,
                pass2: pass2,
              },
              {
                emulateJSON: true,
              }
            )
            .then(function (data) {
              layer.close(loading)
              layer.close(index)
              if (data.data.code == 1) {
                iziToast.success({
                  title: data.data.msg,
                  position: 'topRight',
                })
                setTimeout(function () {
                  window.location.href = '../../../index/qianye'
                }, 1000)
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
  },
})

$('#connect_qq').click(function () {
  var ii = layer.load(0, {
    shade: [0.1, '#fff'],
  })
  $.ajax({
    type: 'POST',
    url: '../qq_login.php',
    data: { type: 'qq' },
    dataType: 'json',
    success: function (data) {
      layer.close(ii)
      if (data.code == 1) {
        window.location.href = data.url
      } else {
        iziToast.error({
          title: data.msg,
          position: 'topRight',
        })
      }
    },
  })
})
