@extends('agent.layouts.agent_template')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5>Tổng quan về đại lý</h5>

</div>

<!-- Content Row -->
<div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng số tiền nạp của thành viên cấp dưới</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$all_recharge}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng số tiền rút của thành viên cấp dưới</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$all_withdraw}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tổng số tiền cược của thành viên cấp dưới</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{$all_valid_bet}}</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tổng hoàn trả của thành viên cấp dưới</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$all_win_loss}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->

<div class="row">

    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tóm tắt dữ liệu gửi và rút tiền của các thành viên cấp dưới</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Bản ghi cá cược tiền gửi</div>
                        <a class="dropdown-item" href="#">Hồ sơ nạp tiền của thành viên cấp dưới</a>
                        <a class="dropdown-item" href="#">Hồ sơ rút tiền của thành viên cấp dưới</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Hồ sơ cá cược của thành viên cấp dưới</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="mt-4a text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Tiền gửi
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> Rút tiền
                    </span>
                    <span class="mr-2">Thời gian làm mới: 2020-08-18 00:00:00</span>
                </div>
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tỷ lệ tổng số tiền gửi và rút của các thành viên cấp dưới</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Báo cáo thống kê</div>
                        <a class="dropdown-item" href="#">Báo cáo lãi lỗ</a>
                        <a class="dropdown-item" href="#">Báo cáo hoa hồng</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Báo cáo hoa hồng đại lý cấp dưới</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="mt-4a text-center small">
                    <span class="mr-2">Thời gian làm mới: 2020-08-18 00:00:00</span>
                </div>
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Lợi nhuận
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Tổng chi phí
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Tổng doanh thu
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Content Column -->
    <div class="col-lg-6 mb-4">

        <!-- Project Card Example -->
        <!-- Approach -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông báo mới nhất</h6>
            </div>
            <div class="card-body">
                @foreach($list as $item)
                    <div style="padding-bottom:12px; padding-top:3px;">
                        <div class="text-truncate"><i class="fas fa-info-circle"></i>
                            <a href="/notice_detail/{{$item->id}}" rel="nofollow" target="_blank" title="{{$item->title}}">{{$item->title}}</a></div>
                        <div class="small text-gray-500" style="text-align:left; padding-left:20px;"> {{$item->created_at}}</div>
                    </div>
                @endforeach

            </div>
        </div>

    </div>

    <div class="col-lg-6 mb-4">

        <!-- Illustrations -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quảng bá đại lý</h6>

            </div>
            <div class="card-body">
                <p>Kính gửi đại lý <font color="#f00">{{$user->username}}</font>，chào mừng bạn tham gia! </p>
                <p>Mã giới thiệu của bạn: <font color="burlywood"><b>{{$user->id}}</b></font>
                </p>
                <p>Liên kết đại lý: <font color="burlywood"><b>{{env('PC_URL')}}/#/register?pid={{$user->id}}</b></font>
                </p>
                <div class="text-center">
                    <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 10rem;" id="girlImg" src="img/ewm.jpg" alt="">
                    <a href="/download-qrcode" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Lưu mã QR quảng cáo của tôi</a>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
