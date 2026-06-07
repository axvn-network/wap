@extends('agent.layouts.agent_template')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5>Lịch sử rút tiền của thành viên cấp dưới</h5>
</div>

<div class="input-group" style="z-index:999; padding-left:0; padding-bottom:20px;">
    <a href="?days=3" class="btn btn-success btn-icon-split">
        <span class="text">3 ngày gần nhất</span>
    </a>
    &nbsp;&nbsp;
    <a href="?days=7" class="btn btn-success btn-icon-split">
        <span class="text">7 ngày gần nhất</span>
    </a>
    &nbsp;&nbsp;
    <a href="?days=30" class="btn btn-success btn-icon-split">
        <span class="text">30 ngày gần nhất</span>
    </a>
</div>


<div class="input-group" style="z-index:999; padding-left:0; padding-bottom:20px;">
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="/withdraw-log">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Vui lòng chọn thời gian bắt đầu" name="start" id="start1">&nbsp;&nbsp;
            <input type="text" class="form-control bg-light border-0 small" placeholder="Vui lòng chọn thời gian kết thúc" name="end" id="end1">&nbsp;&nbsp;
            <input type="text" class="form-control bg-light border-0 small" placeholder="Vui lòng nhập tài khoản..." name="username" aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">

    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lịch sử rút tiền của thành viên cấp dưới</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                      <th>ID</th>
					  <th>Mã đơn</th>
                      <th>Tài khoản người chơi</th>
                      <th>Số tiền rút</th>
					  <th>Trạng thái rút tiền</th>
                      <th>Thời gian rút tiền</th>
					  <th>Ghi chú</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($list as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{$item->order_no}}</td>
                        <td>{{$item->user_data->username}}</td>
                        <td>{{$item->amount}}</td>
                        <td>
                            @switch($item->state)
                                @case(1)
                                Đang chờ duyệt
                                @break
                                @case(2)
                                Đã hoàn thành
                                @break
                                @case(3)
                                Đã từ chối
                                @break
                            @endswitch
                        </td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->info}}</td>
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
    $('#collapseFour').addClass('show');
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