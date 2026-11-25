const classVm = new Vue({
  el: '#class',
  data: {
    row: {},
    storeInfo: {},
  },
  methods: {
    get: function (page) {
      this.$http
        .post(
          '/apisub.php?act=classlist',
          { page: page },
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
    form: function (form) {
      var load = layer.load(2)
      this.$http
        .post(
          '/apisub.php?act=upclass',
          { data: $('#form-' + form).serialize() },
          { emulateJSON: true }
        )
        .then(function (data) {
          layer.close(load)
          console.log($('#form-' + form).serialize())
          if (data.data.code == 1) {
            this.get(this.row.current_page)
            $('#modal-' + form).modal('hide')
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
  },
  created() {
    this.get(1)
  },
})
