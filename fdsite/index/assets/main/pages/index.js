const homeVm = new Vue({
  el: '#userindex',
  data: {
    row: {},
    dailitongji: {},
    inte: '',
    rankList: [],
  },
  methods: {
    userinfo: function () {
      this.$http.post('/apisub.php?act=userinfo').then(function (data) {
        if (data.data.code == 1) {
          this.row = data.data
          this.dailitongji = data.data.dailitongji
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
    yecz: function () {
      layer.alert(
        '请联系您的上级QQ：' +
          this.row.sjuser +
          '，进行充值。（下级点充值，此处将显示您的QQ）',
        { icon: 1, title: '温馨提示' }
      )
    },
    ktapi: function () {
      layer.confirm(
        '后台余额满300元可免费开通，反之需花费10元开通',
        {
          title: '温馨提示',
          icon: 1,
          btn: ['确定', '取消'], //按钮
        },
        function () {
          var load = layer.load(2)
          axios.get('/apisub.php?act=ktapi&type=1').then(function (data) {
            layer.close(load)
            if (data.data.code == 1) {
              layer.alert(
                data.data.msg,
                { icon: 1, title: '温馨提示' },
                function () {
                  setTimeout(function () {
                    window.location.href = ''
                  })
                }
              )
            } else {
              layer.msg(data.data.msg, { icon: 2 })
            }
          })
        }
      )
    },
    ghapi: function () {
      layer.confirm(
        '确定更换key吗，更换之后之前的就不能用了',
        {
          title: '温馨提示',
          icon: 1,
          btn: ['确定', '取消'], //按钮
        },
        function () {
          var load = layer.load(2)
          axios.get('/apisub.php?act=ktapi&type=3').then(function (data) {
            layer.close(load)
            if (data.data.code == 1) {
              layer.alert(
                data.data.msg,
                { icon: 1, title: '温馨提示' },
                function () {
                  setTimeout(function () {
                    window.location.href = ''
                  })
                }
              )
            } else {
              layer.msg(data.data.msg, { icon: 2 })
            }
          })
        }
      )
    },
    szyqprice: function () {
      layer.prompt(
        { title: '设置下级默认费率，首次自动生成邀请码', formType: 3 },
        function (yqprice, index) {
          layer.close(index)
          var load = layer.load(2)
          $.post('/apisub.php?act=yqprice', { yqprice }, function (data) {
            layer.close(load)
            if (data.code == 1) {
              homeVm.userinfo()
              layer.alert(data.msg, { icon: 1 })
            } else {
              layer.msg(data.msg, { icon: 2 })
            }
          })
        }
      )
    },
    connect_qq: function () {
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
            layer.alert(data.msg, {
              icon: 7,
            })
          }
        },
      })
    },
    szgg: function () {
      layer.prompt(
        { title: '设置代理公告，您的代理可看到', formType: 2 },
        function (notice, index) {
          layer.close(index)
          var load = layer.load(2)
          $.post('/apisub.php?act=user_notice', { notice }, function (data) {
            layer.close(load)
            if (data.code == 1) {
              homeVm.userinfo()
              layer.msg(data.msg, { icon: 1 })
            } else {
              layer.msg(data.msg, { icon: 2 })
            }
          })
        }
      )
    },
    getrank: function () {
      this.$http.post('/apisub.php?act=getclassrank').then(function (data) {
        if (data.data.code == 1) {
          this.rankList = data.body.data
        } else {
          layer.msg(data.data.msg, { icon: 2 })
        }
      })
    },
  },
  created() {
    this.userinfo()
    this.getrank()
  },
})
