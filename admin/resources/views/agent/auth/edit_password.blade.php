@extends('agent.layouts.agent_template')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5>
        Đổi mật khẩu
    </h5>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Đổi mật khẩu
        </h6>
    </div>
    <form class="user" action="{{url('/editPassword')}}" method="post" onsubmit="return checkForm();">
        @csrf
        <div class="modal-body" style="padding-top:35px;">
            <div class="form-group row">
                <div class="col-sm-6a">
                    Mật khẩu cũ:
                </div>
                <div class="col-sm-6b">
                    <input type="password" name="old_password" required class="form-control form-control-user" placeholder="Vui lòng nhập mật khẩu cũ">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                    Mật khẩu mới:
                </div>
                <div class="col-sm-6b">
                    <input type="password" name="new_password"  id="new_password" class="form-control form-control-user" placeholder="Vui lòng nhập mật khẩu mới" required>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                    Xác nhận mật khẩu mới:
                </div>
                <div class="col-sm-6b">
                    <input type="password" id="re_password"  class="form-control form-control-user" placeholder="Vui lòng nhập lại mật khẩu mới" required>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                </div>
                <div class="col-sm-6b">
                    <button type="submit" class="btn btn-primary btn-user btn-block">
                        Đổi mật khẩu
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
        var new_password = $("#new_password").val();
        var re_password = $("#re_password").val();
        if (new_password.length < 6) {
            alert('Mật khẩu phải có ít nhất 6 ký tự');
            return false;
        }
        if (new_password != re_password) {
            alert('Hai lần nhập mật khẩu không khớp');
            return false;
        }
    }
</script>
@endsection