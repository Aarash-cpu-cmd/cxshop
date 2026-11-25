$('#sjqy').click(function () {
  layer.prompt(
    { title: '请输入要转移的上级UID', formType: 3 },
    function (uid, index) {
      layer.close(index)
      layer.prompt(
        { title: '请输入要转移的上级邀请码', formType: 3 },
        function (yqm, index) {
          layer.close(index)
          var load = layer.load(2)
          $.ajax({
            type: 'POST',
            url: '../apisub.php?act=sjqy',
            data: { uid: uid, yqm: yqm },
            dataType: 'json',
            success: function (data) {
              layer.close(load)
              if (data.code == 1) {
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
            },
          })
        }
      )
    }
  )
})

$('#logout').click(function () {
  layer.confirm(
    '您确定要退出登录吗？',
    {
      btn: ['确认', '关闭'],
    },
    function (index) {
      layer.close(index)
      iziToast.success({
        title: '退出登录成功',
        position: 'topRight',
      })
      window.location = '../apisub.php?act=logout'
    }
  )
})
