@extends('agent.layouts.agent_template')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary mb-4">
        Báo cáo hoa hồng
        </h6>
        <div class="input-group" style="z-index:999; padding-left:0; padding-bottom:20px;">
            <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="{{url('/profit')}}" method="get">
                <div class="input-group">
                    <input type="text" class="form-control border-0 small" placeholder="Vui lòng chọn thời gian bắt đầu" id="start1" name="start" value="{{$start}}">&nbsp;&nbsp;
                    <input type="text" class="form-control border-0 small" placeholder="Vui lòng chọn thời gian kết thúc" id="end1" name="end" value="{{$end}}">&nbsp;&nbsp;
                    <input type="text" class="form-control border-0 small" name="username" placeholder="Vui lòng nhập tên người dùng..." aria-label="Search" aria-describedby="basic-addon2" value="{{$username}}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm">
                            </i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th rowspan="2" style="vertical-align: middle;">Tài khoản</th>
                    <th rowspan="2" style="vertical-align: middle;">Họ và tên</th>
                    <th rowspan="2" style="vertical-align: middle;">Chế độ</th>
                    <th colspan="2">Số lượng cấp dưới</th>
                    <th colspan="2">Dòng tiền cấp dưới</th>
                    <th colspan="3">Thưởng cấp dưới</th>
                    <th colspan="2"  style="vertical-align: middle;">Cược hợp lệ của cấp dưới</th>
                    <th rowspan="2"  style="vertical-align: middle;">Tổng thắng/thua của cấp dưới</th>
                    <th rowspan="2" style="vertical-align: middle;">Hoa hồng đã thanh toán</th>
                    <th rowspan="2" style="vertical-align: middle;">Hoa hồng chưa thanh toán</th>
                </tr>
                <tr>
                    <th>Tổng số người</th>
                    <th>Tổng số đại lý</th>
                    <th>Tổng số lần nạp</th>
                    <th>Tổng số lần rút</th>
                    <th>Tổng số tiền nạp</th>
                    <th>Tổng tiền thưởng</th>
                    <th>Tổng hoàn trả</th>
                    <th>Tổng số tiền</th>
                    <th>Tổng số lượng</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($list as $item)
                    <tr>
                        <td>{{$item->username}}</td>
                        <td>{{$item->realname}}</td>
                        <td>{{$item->isagent == 1 ? 'Đại lý' : 'Hội viên'}}</td>
                        <td>{{$item->usersum}}</td>
                        <td>{{$item->agentsum}}</td>
                        <td>{{$item->rechage_times}}</td>
                        <td>{{$item->withdraw_times}}</td>
                        <td>{{$item->all_recharge}}</td>
                        <td>{{$item->all_redpacket}}</td>
                        <td>{{$item->all_fanshui}}</td>
                        <td>{{$item->all_valid_bet}}</td>
                        <td>{{$item->all_valid_betsum}}</td>
                        <td>{{$item->all_win_loss}}</td>
                        <td>{{$item->yongjinsum}}</td>
                        <td>{{$item->waityongjinsum}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

@endsection
@section('js')
<script src="/agent/js/laydate/laydate.js"></script>
<script>
    $('#collapseThree').addClass('show');
</script>
<script>
    lay('#version').html('-v' + laydate.v);

    //Thực hiện một phiên bản laydate
    laydate.render({
        elem: '#start1' //Chỉ định phần tử
    });

    laydate.render({
        elem: '#end1' //Chỉ định phần tử
    });
</script>
@endsection
