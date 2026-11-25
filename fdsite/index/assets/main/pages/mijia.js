const mijiaVm = new Vue({
  el: '#mijia',
  data: {
    row: {},
    uid: '',
    storeInfo: {},
    addm: {
      uid: '',
    },
    upm: {},
  },
  methods: {
    get: function (page) {
      this.$http
        .post(
          '/apisub.php?act=mijialist',
          { uid: this.uid },
          { emulateJSON: true }
        )
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
    add_m: function () {
      var load = layer.load(2)
      this.$http
        .post(
          '/apisub.php?act=mijia',
          { data: this.addm, active: 1 },
          { emulateJSON: true }
        )
        .then(function (data) {
          layer.close(load)
          if (data.data.code == 1) {
            $('#modal-add').modal('hide')
            mijiaVm.get(1)
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
    up_m: function () {
      var load = layer.load(2)
      this.$http
        .post(
          '/apisub.php?act=mijia',
          { data: this.upm, active: 2 },
          { emulateJSON: true }
        )
        .then(function (data) {
          layer.close(load)
          if (data.data.code == 1) {
            $('#modal-update').modal('hide')
            mijiaVm.get(1)
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
    del: function (mid) {
      layer.confirm(
        '您确定要删除该项吗？',
        {
          btn: ['确认', '关闭'],
        },
        function (index) {
          var load = layer.load(2)
          mijiaVm.$http
            .post(
              '/apisub.php?act=mijia_del',
              { mid: mid },
              { emulateJSON: true }
            )
            .then(function (data) {
              layer.close(load)
              if (data.data.code == 1) {
                mijiaVm.get(1)
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
    this.get(1)
  },
})
