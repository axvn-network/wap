<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
    <div class="sidebar-brand-text mx-3">Quản trị đại lý</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <!-- Nav Item - Dashboard -->
  <li class="nav-item active">
    <a class="nav-link" href="/">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>{{ __('admin.agent_homepage') }}</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Nav Item - Pages Collapse Menu -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
      <i class="fas fa-fw fa-cog">
      </i>
      <span>
        {{ __('admin.homepage_content') }}
      </span>
    </a>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="{{url('/notice')}}">
          {{ __('admin.notice_information') }}
        </a>
        <a class="collapse-item" href="{{url('/message')}}">
          {{ __('admin.inbox_mail') }}
        </a>
      </div>
    </div>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
      <i class="fas fa-fw fa-cog">
      </i>
      <span>
        Tổng quan đại lý
      </span>
    </a>
    <div id="collapseThree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="{{url('/chart')}}">
          {{ __('admin.chart_overview') }}
        </a>
        <a class="collapse-item" href="{{url('/today')}}">
          Tổng quan hôm nay
        </a>
        <a class="collapse-item" href="{{url('/profit')}}">
          Báo cáo lời lỗ
        </a>
          <a class="collapse-item" href="{{url('/commission')}}">
              Báo cáo hoa hồng
          </a>
          <a class="collapse-item" href="{{url('/subordinate')}}">
              Báo cáo hoa hồng đại lý cấp dưới
          </a>

      </div>
    </div>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
      <i class="fas fa-fw fa-cog">
      </i>
      <span>
        Trang đại lý
      </span>
    </a>
    <div id="collapseFour" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="{{url('/add-member')}}">
          Thêm thành viên cấp dưới
        </a>
        <a class="collapse-item" href="{{url('/editPassword')}}">
          Đổi mật khẩu
        </a>
        <a class="collapse-item" href="{{url('/memberlist')}}">
          Danh sách thành viên cấp dưới
        </a>
        <a class="collapse-item" href="{{url('/bet-log')}}">
          Lịch sử đặt cược thành viên cấp dưới
        </a>
        <a href="{{url('/recharge-log')}}" class="collapse-item">
          Lịch sử nạp tiền thành viên cấp dưới
        </a>
        <a href="{{url('/withdraw-log')}}" class="collapse-item">
          Lịch sử rút tiền thành viên cấp dưới
        </a>
        <a href="{{url('/transfer-log')}}" class="collapse-item">
          Lịch sử chuyển khoản thành viên cấp dưới
        </a>
          <a href="{{url('/rebate')}}" class="collapse-item">
              Lịch sử hoàn trả thành viên cấp dưới
          </a>


      </div>
    </div>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

</ul>
