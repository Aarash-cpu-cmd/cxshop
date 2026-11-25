const chargeVm = new Vue({
  el: '#charge',
  data: {
    money: '',
    km: '',
    querykm: '',
    kminfo: {
      content: '请先查询',
      money: '0',
      status: '',
    },
    out_trade_no: '',
  },
  methods: {
    pay: function () {
      var load = layer.load(2)
      this.$http
        .post(
          '/apisub.php?act=pay',
          { money: this.money },
          { emulateJSON: true }
        )
        .then(function (data) {
          layer.close(load)
          if (data.data.code == 1) {
            $('pay2').show()
            this.out_trade_no = data.data.out_trade_no
            iziToast.success({
              title: data.data.msg,
              position: 'topRight',
            })
            layer.open({
              type: 1,
              title: '请选择支付方式',
              closeBtn: 0,
              area: ['250px', '300px'],
              skin: 'layui-bg-gray', //没有背景色
              shadeClose: true,
              content: $('#pay2'),
              end: function () {
                $('#pay2').hide()
              },
            })
          } else {
            iziToast.error({
              title: data.data.msg,
              position: 'topRight',
            })
          }
        })
    },
    paycard: function () {
      var load = layer.load(2)
      this.$http
        .post('/rechargeableCard.php?act=kmpay', { content: this.km }, { emulateJSON: true })
        .then(function (data) {
          layer.close(load)
          if (data.data.code == 1) {
            iziToast.success({
              title: data.data.msg,
              position: 'topRight',
            })
            setTimeout(function () {
              location.reload()
            }, 1000)
          } else {
            iziToast.error({
              title: data.data.msg,
              position: 'topRight',
            })
          }
        })
    },
    querycard: function () {
      var load = layer.load(2)
      this.$http
        .post(
          '/rechargeableCard.php?act=querykm',
          { content: this.querykm },
          { emulateJSON: true }
        )
        .then(function (data) {
          layer.close(load)
          if (data.data.code == 1) {
            this.kminfo = data.data.data[0]
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
    szgg: function () {
      layer.prompt(
        { title: '设置代理公告，您的代理可看到', formType: 2 },
        function (notice, index) {
          layer.close(index)
          var load = layer.load(2)
          $.post('/apisub.php?act=user_notice', { notice }, function (data) {
            layer.close(load)
            if (data.code == 1) {
              iziToast.success({
                title: data.msg,
                position: 'topRight',
              })
              window.location.href = ''
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
  },
  mounted() {
    $('.preloader').fadeOut('slow')
  },
})
