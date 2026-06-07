@extends('agent.layouts.agent_template')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary mb-4">
        Báo cáo lợi nhuận
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
                        <th>Tài khoản</th>
                        <th>Họ và tên</th>
                        <th>Cấp trên</th>
                        <th>Loại tài khoản</th>
                        <th>Số lần nạp</th>
                        <th>Số lần rút</th>
                        <th>Tổng nạp</th>
                        <th>Tổng rút</th>
                        <th>Tổng cược hợp lệ</th>
                        <th>Tổng thắng thua</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($list as $item)
                    <tr>
                        <td>{{$item->username}}</td>
                        <td>{{$item->realname}}</td>
                        <td>{{$item->getAgentData()->username}}</td>
                        <td>{{$item->is_agent == 1 ? 'Đại lý' : 'Hội viên'}}</td>
                        <td>{{$item->rechage_times}}</td>
                        <td>{{$item->withdraw_times}}</td>
                        <td>{{$item->all_recharge}}</td>
                        <td>{{$item->all_withdraw}}</td>
                        <td>{{$item->all_valid_bet}}</td>
                        <td>{{$item->all_win_loss}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers">
                    <ul class="pagination">
                        {{$list->links()}}
                    </ul>
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