<!--上级迁移弹窗 - 开始-->
<div class="modal fade" id="modal_sjqy" tabindex="-1" aria-hidden="true">
  <!--begin::Modal dialog-->
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header">
        <!--begin::Modal title-->
        <h3 class="fw-boldest text-dark fs-1 mb-0">上级迁移</h3>
        <!--end::Modal title-->
        <!--begin::Close-->
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-2x">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
            </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--end::Modal header-->
      <!--begin::Modal body-->
      <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
        <!--begin::Form-->
        <form class="form" action="#" @submit.prevent="">
          <!--begin::Input group-->
          <div class="d-flex flex-column mb-7 fv-row">
            <input id="sjqyUidInput" type="text" class="form-control form-control-solid" placeholder="请输入要转移的上级UID" autocomplete="off">
          </div>
          <!--end::Input group-->
          <!--begin::Input group-->
          <div class="d-flex flex-column mb-7 fv-row">
            <input id="sjqyYqmInput" type="text" class="form-control form-control-solid" placeholder="请输入要转移的上级邀请码" autocomplete="off">
          </div>
          <!--end::Input group-->
          <!--begin::Actions-->
          <div class="text-center pt-15">
            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">取消</button>
            <button id="sjqyBtn" type="button" class="btn btn-primary"><span class="indicator-label">确认提交</span>
              <span class="indicator-progress"
                >Please wait...
                <span
                  class="spinner-border spinner-border-sm align-middle ms-2"
                ></span
              ></span></button>
          </div>
          <!--end::Actions-->
        </form>
        <!--end::Form-->
      </div>
      <!--end::Modal body-->
    </div>
    <!--end::Modal content-->
  </div>
  <!--end::Modal dialog-->
</div>
<!--上级迁移弹窗 - 结束-->
<!--站点公告 - 开始-->
<div class="modal fade" id="modal_usernotice" tabindex="-1" aria-hidden="true">
  <!--begin::Modal dialog-->
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header">
        <!--begin::Modal title-->
        <h3 class="fw-boldest text-dark fs-1 mb-0">站点公告</h3>
        <!--end::Modal title-->
        <!--begin::Close-->
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-2x">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
            </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--end::Modal header-->
      <!--begin::Form-->
      <form class="form">
        <!--begin::Modal body-->
        <div class="modal-body py-10 px-lg-17">
          <!--begin::Scroll-->
          <div class="scroll-y me-n7 pe-7" data-kt-scroll="true" data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">
            <?=$conf['notice']?>
          </div>
          <!--end::Scroll-->
        </div>
        <!--end::Modal body-->
        <!--begin::Modal footer-->
        <div class="modal-footer flex-center">
          <!--begin::Button-->
          <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">关闭</button>
          <!--end::Button-->
        </div>
        <!--end::Modal footer-->
      </form>
      <!--end::Form-->
    </div>
    <!--end::Modal content-->
  </div>
  <!--end::Modal dialog-->
</div>
<!--站点公告 - 结束-->
<!--重要通知 - 开始-->
<div class="modal fade" id="modal_tcgonggao" tabindex="-1" aria-hidden="true">
  <!--begin::Modal dialog-->
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header">
        <!--begin::Modal title-->
        <h3 class="fw-boldest text-dark fs-1 mb-0">重要通知</h3>
        <!--end::Modal title-->
        <!--begin::Close-->
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-2x">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
            </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--end::Modal header-->
      <!--begin::Form-->
      <form class="form">
        <!--begin::Modal body-->
        <div class="modal-body py-10 px-lg-17">
          <!--begin::Scroll-->
          <div class="scroll-y me-n7 pe-7" data-kt-scroll="true" data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">
            <?=$conf['tcgonggao']?>
          </div>
          <!--end::Scroll-->
        </div>
        <!--end::Modal body-->
        <!--begin::Modal footer-->
        <div class="modal-footer flex-center">
          <!--begin::Button-->
          <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">关闭</button>
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">不再提示</button>
          <!--end::Button-->
        </div>
        <!--end::Modal footer-->
      </form>
      <!--end::Form-->
    </div>
    <!--end::Modal content-->
  </div>
  <!--end::Modal dialog-->
</div>
<!--重要通知 - 结束-->
<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
  <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
  <span class="svg-icon">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
      <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black"></rect>
      <path
        d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
        fill="black"></path>
    </svg>
  </span>
  <!--end::Svg Icon-->
</div>
<!--end::Scrolltop-->
<!--end::Main-->
<!--begin::Javascript-->
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="static/js/plugins.bundle.js"></script>
<script src="static/js/scripts.bundle.js"></script>
<!--end::Javascript-->
<!-- main - 开始 -->
<script src="static/main/vue.min.js"></script>
<script src="static/main/vue-resource.min.js"></script>
<script src="static/main/axios.min.js"></script>
<script src="static/main/sy.js"></script>
<?php if ($conf['sykg']==1) {?>
<script type="text/javascript">
watermark('<?=$conf['sitename'];?>','昵称 : <?=$userrow['name'];?>','UID:<?=$userrow['uid'];?>');
</script>
<?php }?>
<!-- main - 结束 -->
<!-- 全局页面需要 - 开始 -->
<script src="static/utils/toast.js"></script>
<script src="static/utils/index.js"></script>
<script src="static/main/pages/aside.js"></script>
<script src="static/main/pages/index.js"></script>
<!-- 全局页面需要 - 结束 -->