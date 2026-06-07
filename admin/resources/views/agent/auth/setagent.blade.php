@extends('agent.layouts.agent_template')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5>
        Cài đặt hoàn trả đại lý
    </h5>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Cài đặt hoàn trả đại lý
        </h6>
    </div>
    <form class="user" action="{{url('/changefanshui')}}" method="post" onsubmit="return checkForm();">
        @csrf
        <div class="modal-body" style="padding-top:35px;">
            <div class="form-group row">
                <div class="col-sm-6a">
                    Tên tài khoản：
                </div>
                <div class="col-sm-6b">
                   {{$userinfo->username}}
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                    Tỷ lệ hoàn trả：
                </div>
                <div class="col-sm-6b">
                    <input type="text" name="fanshui"  id="fanshui" value="{{$userinfo->fanshuifee}}" class="form-control form-control-user" placeholder="Vui lòng nhập tỷ lệ hoàn trả" required> %
                </div>
            </div>
            <input type="hidden"  name="uid"  value=" {{$userinfo->id}}">
            <div class="form-group row">
                <div class="col-sm-6a">
                </div>
                <div class="col-sm-6b">
                    <button type="submit" class="btn btn-primary btn-user btn-block">
                        Cài đặt đại lý
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@section('js')
<script>

    function checkForm() {
        var fanshui = $("#fanshui").val();
        if (fanshui < 0) {
            alert('Vui lòng nhập tỷ lệ hoàn trả đại lý hợp lệ');
            return false;
        }

    }
</script>
@endsection