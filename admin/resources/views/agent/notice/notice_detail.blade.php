@extends('agent.layouts.agent_template')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5>{{ __('admin.notice_information') }}</h5>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Nội dung thông báo</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <tbody>
                    <tr>
                        <td align="center"><b>{{$item->title}}</b></td>
                    </tr>
                    <tr>
                        <td align="center">Thời gian đăng：{{$item->created_at}}</td>
                    </tr>
                    <tr>
                        <td>{!! $item->content !!}</td>
                    </tr>
                    <tr>
                        <!-- <td align="right">
                            <a href="notice.html" class="btn btn-warning btn-icon-split">
                                <span class="icon text-white-50">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                                <span class="text">Quay lại danh sách thông báo</span>
                            </a>
                        </td> -->

                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection