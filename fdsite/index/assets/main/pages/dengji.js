const dengjiVm = new Vue({
  el: '#dengji',
  data: {
    row: {},
    uid: '<?php echo $uid ?>',
    storeInfo: {},
    addm: {
      uid: '<?php echo $uid ?>',
      addkf: 1,
      gjkf: 1,
    },
    upm: {},
  },
  methods: {
    get: function (page) {
      this.$http
        .post(
          '/apisub.php?act=djlist',
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
          '/apisub.php?act=dj',
          { data: this.addm, active: 1 },
          { emulateJSON: true }
        )
        .then(function (data) {
          layer.close(load)
          if (data.data.code == 1) {
            $('#modal-add').modal('hide')
            dengjiVm.get(1)
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
          '/apisub.php?act=dj',
          { data: this.upm, active: 2 },
          { emulateJSON: true }
        )
        .then(function (data) {
          layer.close(load)
          if (data.data.code == 1) {
            $('#modal-update').modal('hide')
            dengjiVm.get(1)
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
    del: function (id) {
      layer.confirm(
        '您确定要删除该项吗？',
        {
          btn: ['确认', '关闭'],
        },
        function (index) {
          var load = layer.load(2)
          dengjiVm.$http
            .post('/apisub.php?act=dj_del', { id: id }, { emulateJSON: true })
            .then(function (data) {
              layer.close(load)
              if (data.data.code == 1) {
                dengjiVm.get(1)
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
