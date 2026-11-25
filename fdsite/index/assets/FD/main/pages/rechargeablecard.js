const cardVm = new Vue({
  el: '#card',
  data: {
    row: {},
    addm: {
      num: '',
      money: '',
    },
  },
  methods: {
    get: function () {
      this.$http.post('/rechargeableCard.php?act=kmlist').then(function (data) {
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
    add: function () {
      // 生成随机卡密
      function generateRandomKey() {
        const length = 16
        const characters =
          'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
        let key = ''
        for (let i = 0; i < length; i++) {
          const randomIndex = Math.floor(Math.random() * characters.length)
          key += characters.charAt(randomIndex)
        }
        return key
      }
      for (let i = 0; i < this.addm.num; i++) {
        var load = layer.load(2)
        let content = generateRandomKey()
        this.$http
          .post(
            '/rechargeableCard.php?act=rechargeablecard',
            { content: content, money: this.addm.money },
            { emulateJSON: true }
          )
          .then(function (data) {
            layer.close(load)
            if (data.data.code == 1) {
              $('#modal-add').modal('hide')
              cardVm.get(1)
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
    },
    del: function (id) {
      layer.confirm(
        '您确定要删除该项吗？',
        {
          btn: ['确认', '关闭'],
        },
        function (index) {
          var load = layer.load(2)
          cardVm.$http
            .post('/rechargeableCard.php?act=deletekm', { id: id }, { emulateJSON: true })
            .then(function (data) {
              layer.close(load)
              if (data.data.code == 1) {
                cardVm.get()
                layer.close(index)
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
  },
  created() {
    this.get()
  },
})
