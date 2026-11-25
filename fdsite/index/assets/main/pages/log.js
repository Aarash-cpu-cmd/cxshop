const logVm = new Vue({
  el: '#loglist',
  data: {
    row: {},
    type: '',
    types: '',
    qq: '',
  },
  methods: {
    get: function (page, a) {
      data = { page: page, type: this.type, types: this.types, qq: this.qq }
      this.$http
        .post('/apisub.php?act=loglist', data, { emulateJSON: true })
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
  },
  created() {
    this.get(1)
  },
})
