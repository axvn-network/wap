@extends('agent.layouts.agent_template')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5>Danh sách thành viên cấp dưới</h5>
</div>
<div class="input-group" style="z-index:999; padding-left:0; padding-bottom:20px;">
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="/member">
        <div class="input-group">
            <input type="text" name="username" class="form-control bg-light border-0 small" placeholder="Vui lòng nhập tài khoản..." aria-label="Search" aria-describedby="basic-addon2">
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
        <h6 class="m-0 font-weight-bold text-primary">Danh sách thành viên cấp dưới</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="overflow-x: auto; padding: 0 !important;">
            <table class="table table-bordered" id="dataTable" width="1800" cellspacing="0" style="text-align:center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tài khoản</th>
                        <th>Họ và tên</th>
                        <th>Loại tài khoản</th>
                        <th>Đại lý cấp trên</th>
                        <th>Tỷ lệ hoàn trả của đại lý</th>
                        <th>Số dư hệ thống</th>
                        <th>Số dư trò chơi</th>
                        <th>Thời gian tạo</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($list as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>
                            <div class="dropdown mb-4a">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{$item->username}}
                                </button>
                                <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="/bet-log?username={{$item->username}}">Lịch sử đặt cược của thành viên</a>
                                    <a class="dropdown-item" href="/recharge-log?username={{$item->username}}">Lịch sử nạp tiền của thành viên</a>
                                    <a class="dropdown-item" href="/withdraw-log?username={{$item->username}}">Lịch sử rút tiền của thành viên</a>
                                    <a class="dropdown-item" href="/transfer-log?username={{$item->username}}">Lịch sử chuyển khoản của thành viên</a>
                                    <!-- <a class="dropdown-item" href="commission.html">Báo cáo doanh thu đặt cược của thành viên</a> -->
                                    <!-- <a class="dropdown-item" href="rebate.html">Lịch sử hoàn trả của thành viên</a> -->
                                </div>
                            </div>
                        </td>
                        <td>{{$item->realname}}</td>
                        <td>{{$item->isagent == 1 ? 'Đại lý' : 'Thành viên'}}</td>
                        <td>{{$item->parent}}</td>
                        <td>
                            @if($user->allowagent)
                                {{$item->fanshuifee}}%
                            @if ($item->is_direct)
                            <a class="btn btn-danger btn-icon-split btn-sm" href="/changefanshui?uid={{$item->id}}">
                                <span class="text">Đặt làm đại lý</span></a>
                            @endif
                            @else
                               Không có quyền
                            @endif
                        </td>

                        <td>
                            {{$item->balance}} <br>
                            <a class="btn btn-danger btn-icon-split btn-sm mt-1" href="#" data-toggle="modal" data-target="#edModal">
                                <span class="text">Điều chỉnh hạn ngạch</span>
                            </a>
                        </td>

                        <td>
                            - <br>
                            <a href="#" class="btn btn-warning btn-icon-split btn-sm mt-1">
                                <span class="text">Làm mới</span>
                            </a>
                        </td>
                        <td>{{$item->created_at}}</td>
                        <td>
                            {{$item->isonline == 1 ? 'Trực tuyến' : 'Ngoại tuyến'}}
                        </td>
                        <td>
                            <a href="/recharge?user_id={{$item->id}}" class="btn btn-warning btn-icon-split btn-sm"><span class="text">Nạp tiền</span></a>
                        </td>
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
            <!-- Logout Modal-->
            <div class="modal fade" id="dlModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Tỷ lệ hoàn trả của đại lý</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <form class="user">
                            <div class="modal-body">

                                <div class="form-group row">
                                    <div class="col-sm-6a1">
                                        Tỷ lệ hoàn điểm：
                                    </div>
                                    <div class="col-sm-6b1">
                                        <input type="text" id="fanshui" class="form-control form-control-user" value="0" >
                                    </div>
                                    <div class="col-sm-6c1" style="padding-top:15px;">
                                        &nbsp; %&nbsp;&nbsp; <font color="#f00">Không vượt quá 0.1%</font>
                                    </div>
                                </div>

                            </div>
                        </form>
                        <div class="modal-footer">
                            <button class="btn btn-primary saveagentfanshui"  type="button">Đặt làm đại lý cấp dưới</button>
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="edModal" tabindex="-1" role="dialog" aria-labelledby="edModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="edModalLabel">Điều chỉnh hạn ngạch</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <form class="user" action="" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group row">
                                    <div class="col-sm-4" style="line-height: 38px;">
                                        Số tiền (+/-):
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="number" name="amount" class="form-control form-control-user" placeholder="Nhập số tiền (VD: 100000 hoặc -50000)">
                                    </div>
                                </div>
                                <div class="form-group row mt-2">
                                    <div class="col-sm-4" style="line-height: 38px;">
                                        Ghi chú:
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" name="remark" class="form-control form-control-user" placeholder="Lý do điều chỉnh">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-danger" type="submit">Xác nhận điều chỉnh</button>
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="/agent/js/laydate/laydate.js"></script>
<script>
    var uid=0;
    $('#collapseFour').addClass('show');
    function opendlModal(id){
        uid = id
        $('#dlModal').modal('show')
    }
    $('.saveagentfanshui').click(function(){
        if($('#fanshui').val()<=0){
            alert("Vui lòng nhập dữ liệu chính xác");
            return false;
        }
        $.ajax({
            type: 'post',
            url: "/changefanshui",
            data : {fanshui:$('#fanshui').val(),uid:uid},
            dataType: "json",
            success: function(data) {
                $('.modal-footer').show();
                if(data.code == 0) {
                    $('.modal-body').html(data.message);
                } else {
                    $('.modal-body').html(data.message);
                }
            }
        });
        })
</script>
@endsection
