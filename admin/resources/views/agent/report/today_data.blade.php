@extends('agent.layouts.agent_template')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
    <h5>{{ __('admin.today_overview') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tổng số hội viên cấp dưới</th>
                        <th>Tổng số đại lý cấp dưới</th>
                        <th>Số hội viên trực thuộc</th>
                        <th>Số đại lý trực thuộc cấp dưới</th>
                        <th>Số hội viên mới hôm nay</th>
                        <th>Tổng nạp hôm nay</th>
                        <th>Tổng rút hôm nay</th>
                        <th>Cược hôm nay</th>
                        <th>Cược hợp lệ hôm nay</th>
                        <th>Thắng thua hôm nay</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>{{$child_member_count}}</td>
                        <td>{{$child_agent_count}}</td>
                        <td>{{$directly_member_count}}</td>
                        <td>{{$directly_agent_count}}</td>
                        <td>{{$add_member_count}}</td>
                        <td>{{$all_recharge}}</td>
                        <td>{{$all_withdraw}}</td>
                        <td>{{$all_bet}}</td>
                        <td>{{$all_valid_bet}}</td>
                        <td>{{$win_loss}}</td>
                    </tr>
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
@endsection