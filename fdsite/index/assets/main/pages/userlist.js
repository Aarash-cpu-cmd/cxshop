const userlistVm = new Vue({
  el: '#userlist',
  data: {
    row: {},
    type: '1',
    qq: '',
    addprice: '',
    ddinfo3: {
      status: false,
      info: [],
    },
    row1: '',
    storeInfo: {},
    checkInfo: {},
  },
  methods: {
    get: function (page) {
      this.$http
        .post(
          '/apisub.php?act=userlist',
          { type: this.type, qq: this.qq, page: page },
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
    dj: function () {
      this.$http
        .post('/apisub.php?act=adddjlist', { emulateJSON: true })
        .then(function (data) {
          if (data.data.code == 1) {
            this.row1 = data.body
          } else {
            iziToast.error({
              title: data.data.msg,
              position: 'topRight',
            })
          }
        })
    },
    handleMenu: function (command) {
      const { res, type } = command
      if (type == 'cz') {
        this.cz(res.uid, res.name)
      } else if (type == 'changeLevel') {
        this.ddinfo(res)
        $('#modal-gjuser').modal('show')
      } else if (type == 'czmm') {
        this.czmm(res.uid)
      } else if (type == 'checkInfo') {
        this.checkInfo = res
        $('#modal-userInfo').modal('show')
      }
    },
    form: function (form) {
      var load = layer.load(2)
      this.$http
        .post(
          '/apisub.php?act=' + form,
          { data: $('#form-' + form).serialize() },
          { emulateJSON: true }
        )
        .then(function (data) {
          layer.close(load)
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
    ddinfo: function (a) {
      this.ddinfo3.info = a
    },
    adduser: function () {
      var load = layer.load(2)
      $.post(
        '/apisub.php?act=adduser',
        { data: $('#form-adduser').serialize() },
        function (data) {
          layer.close(load)
          if (data.code == 1) {
            layer.confirm(
              data.msg,
              {
                btn: ['确定开通', '取消'],
                title: '开通扣费', //按钮
              },
              function (index) {
                var load = layer.load(2)
                userlistVm.$http
                  .post(
                    '/apisub.php?act=adduser',
                    { data: $('#form-adduser').serialize(), type: 1 },
                    { emulateJSON: true }
                  )
                  .then(function (data) {
                    layer.close(load)
                    layer.close(index)
                    if (data.data.code == 1) {
                      userlistVm.get(this.row.current_page)
                      $('#modal-adduser').modal('hide')
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
              function () {}
            )
          } else {
            iziToast.error({
              title: data.msg,
              position: 'topRight',
            })
          }
        }
      )
    },
    cz: function (uid, name) {
      layer.prompt(
        {
          title:
            '你将为<font color="red">' + name + '</font>充值请输入充值金额',
          formType: 3,
        },
        function (money, index) {
          layer.close(index)
          var load = layer.load(2)
          $.post(
            '/apisub.php?act=userjk',
            { uid: uid, money: money },
            function (data) {
              layer.close(load)
              if (data.code == 1) {
                userlistVm.get(userlistVm.row.current_page)
                iziToast.success({
                  title: data.msg,
                  position: 'topRight',
                })
              } else {
                iziToast.error({
                  title: data.msg,
                  position: 'topRight',
                })
              }
            }
          )
        }
      )
    },
    gj: function (uid, name) {
      var load = layer.load(2)
      $.post(
        '/apisub.php?act=usergj',
        { data: $('#form-gjuser').serialize() },
        function (data) {
          layer.close(load)
          if (data.code == 1) {
            layer.confirm(
              data.msg,
              {
                btn: ['确定改价并充值', '取消'],
                title: '改价扣费', //按钮
              },
              function (index) {
                var load = layer.load(2)
                userlistVm.$http
                  .post(
                    '/apisub.php?act=usergj',
                    { data: $('#form-gjuser').serialize(), type: 1 },
                    { emulateJSON: true }
                  )
                  .then(function (data) {
                    layer.close(load)
                    layer.close(index)
                    if (data.data.code == 1) {
                      $('#modal-gjuser').modal('hide')
                      userlistVm.get(userlistVm.row.current_page)
                      iziToast.success({
                        title: data.data.msg,
                        position: 'topRight',
                      })
                    } else {
                      $('#modal-gjuser').modal('hide')
                      iziToast.error({
                        title: data.data.msg,
                        position: 'topRight',
                      })
                    }
                  })
              },
              function () {}
            )
          } else {
            $('#modal-gjuser').modal('hide')
            iziToast.error({
              title: data.msg,
              position: 'topRight',
            })
          }
        }
      )
    },
    czmm: function (uid) {
      var load = layer.load(2)
      userlistVm.$http
        .post('/apisub.php?act=user_czmm', { uid: uid }, { emulateJSON: true })
        .then(function (data) {
          layer.close(load)
          if (data.data.code == 1) {
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
    ktapi: function (uid) {
      layer.confirm(
        '给下级开通API，将扣除5元余额',
        {
          title: '温馨提示',
          icon: 1,
          btn: ['确定', '取消'], //按钮
        },
        function (index) {
          var load = layer.load(2)
          axios
            .get('/apisub.php?act=ktapi&type=2&uid=' + uid)
            .then(function (data) {
              layer.close(load)
              layer.close(index)
              if (data.data.code == 1) {
                userlistVm.get(userlistVm.row.current_page)
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
    yqm: function (uid, name) {
      layer.prompt(
        {
          title:
            '你将为<font color="red">' +
            name +
            '</font>设置邀请码，邀请码最低4位数',
          formType: 3,
        },
        function (yqm, index) {
          layer.close(index)
          var load = layer.load(2)
          $.post('/apisub.php?act=szyqm', { uid, yqm }, function (data) {
            layer.close(load)
            if (data.code == 1) {
              userlistVm.get(userlistVm.row.current_page)
              iziToast.success({
                title: data.msg,
                position: 'topRight',
              })
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
    ban: function (uid, active) {
      var load = layer.load(2)
      $.post('/apisub.php?act=user_ban', { uid, active }, function (data) {
        layer.close(load)
        if (data.code == 1) {
          userlistVm.get(userlistVm.row.current_page)
          iziToast.success({
            title: data.msg,
            position: 'topRight',
          })
        } else {
          iziToast.error({
            title: data.msg,
            position: 'topRight',
          })
        }
      })
    },
    ktvip: function (uid, active) {
      var load = layer.load(2)
      $.post('/apisub.php?act=user_vip', { uid, active }, function (data) {
        layer.close(load)
        if (data.code == 1) {
          userlistVm.get(userlistVm.row.current_page)
          iziToast.success({
            title: data.msg,
            position: 'topRight',
          })
        } else {
          iziToast.error({
            title: data.msg,
            position: 'topRight',
          })
        }
      })
    },
  },
  created() {
    this.get(1)
    this.dj()
  },
})
