<span class="dropdown" style="display:inline-block">
    <a id="" class="dropdown-toggle btn btn-sm btn-white waves-effect" data-toggle="dropdown" href="javascript:void(0)">
        <stub>{{$value}}</stub><span class="caret"></span>
    </a>
    <ul class="dropdown-menu" style="left: 0px; right: inherit;">
        <li class="dropdown-item"><a href="/{{ env('ADMIN_ROUTE_PREFIX') }}/recharge?user_data%5Busername%5D={{$value}}">Hồ sơ nạp tiền</a></li>
        <li class="dropdown-item"><a href="/{{ env('ADMIN_ROUTE_PREFIX') }}/withdraws?user_data%5Busername%5D={{$value}}">Hồ sơ rút tiền</a></li>
        <li class="dropdown-item"><a href="/{{ env('ADMIN_ROUTE_PREFIX') }}/transfer-logs?user_data%5Busername%5D={{$value}}">Hồ sơ chuyển đổi hạn ngạch</a></li>
        <li class="dropdown-item"><a href="/{{ env('ADMIN_ROUTE_PREFIX') }}/user-operate-logs?user_data%5Busername%5D={{$value}}">Nhật ký đăng nhập</a></li>
        <li class="dropdown-item"><a href="/{{ env('ADMIN_ROUTE_PREFIX') }}/usercard?user_data%5Busername%5D={{$value}}">Quản lý thẻ ngân hàng</a></li>
    </ul>
</span>