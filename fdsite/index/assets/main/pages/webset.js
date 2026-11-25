const websetVm = new Vue({
  el: '#add',
  data: {},
  methods: {
    add: function () {
      var loading = layer.load(2)
      this.$http
        .post(
          '/apisub.php?act=webset',
          { data: $('#form-web').serialize() },
          { emulateJSON: true }
        )
        .then(function (data) {
          layer.close(loading)
          if (data.data.code == 1) {
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
        })
    },
  },
  mounted() {
    this.$nextTick(function () {
      $('.preloader').fadeOut('slow')
    })
  },
})
