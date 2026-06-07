@extends('agent.layouts.agent_template')

@section('content')
<!-- Page Heading -->
<h5>Thống kê biểu đồ</h1>
    <p class="mb-4"></p>

    <!-- Content Row -->
    <div class="row">

        <div class="col-xl-8 col-lg-7">

            <!-- Area Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tổng hợp dữ liệu nạp và rút tiền của thành viên cấp dưới</h6>
                </div>
                <div class="card-body">
                    <div class="mt-4a text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Nạp tiền
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Rút tiền
                        </span>
                        <span class="mr-2">Thời gian làm mới：{{date('Y-m-d H:i:s')}}</span>
                    </div>
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                    <hr>
                </div>
            </div>

            <!-- Area Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê báo cáo lãi lỗ</h6>
                </div>
                <div class="card-body">
                    <div class="mt-4a text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Nạp tiền
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Rút tiền
                        </span>
                        <span class="mr-2">Thời gian làm mới：{{date('Y-m-d H:i:s')}}</span>
                    </div>
                    <div class="chart-area">
                        <canvas id="myAreaChart2"></canvas>
                    </div>
                    <hr>
                </div>
            </div>


        </div>

        <!-- Donut Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tỷ lệ tổng số tiền gửi và rút của thành viên cấp dưới</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="mt-4a text-center small">
                        <span class="mr-2">Thời gian làm mới：{{date('Y-m-d H:i:s')}}</span>
                    </div>
                    <div class="chart-pie pt-4">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div style="padding-bottom:16px;"></div>
                    <hr>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Lợi nhuận
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Tổng chi
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Tổng thu nhập
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
@section('js')
<script src="/agent/js/laydate/laydate.js"></script>
<script>
    $('#collapseThree').addClass('show');
</script>
@endsection
