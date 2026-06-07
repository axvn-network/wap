@extends('agent.layouts.agent_template')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5>Thêm thành viên cấp dưới</h5>
</div>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Thông tin đăng ký thành viên</h6>
    </div>
    <form class="user" action="/add-member" method="post" onsubmit="return checkForm()">
        @csrf
        <div class="modal-body" style="padding-top:35px;">
            <div class="form-group row">
                <div class="col-sm-6a">
                    Tài khoản:
                </div>
                <div class="col-sm-6b">
                    <input type="text" name="username" class="form-control form-control-user" placeholder="* Vui lòng nhập độ dài 6-9 ký tự, có thể nhập chữ cái hoặc số" required minlength="6">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                    Mật khẩu:
                </div>
                <div class="col-sm-6b">
                    <input type="password" id="password" name="password" class="form-control form-control-user" placeholder="* Quy tắc mật khẩu: phải là ký tự chữ hoặc số dài 6-24" required minlength="6">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                    Xác nhận mật khẩu:
                </div>
                <div class="col-sm-6b">
                    <input type="password" id="repassword" class="form-control form-control-user" placeholder="* Vui lòng nhập lại mật khẩu" required>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                    Tên thật:
                </div>
                <div class="col-sm-6b">
                    <input type="text" name="realname" class="form-control form-control-user" placeholder="* Vui lòng nhập họ và tên" required>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                    Mật khẩu rút tiền:
                </div>
                <div class="col-sm-6b" style="padding-top:15px;">
                    * Mật khẩu rút tiền mặc định là <font color="#f00">【123456】</font>, thành viên có thể tự thay đổi sau khi đăng nhập, hãy nhớ kỹ!
                </div>
            </div>
            <div class="form-group row" style="padding-top:15px;">
                <div class="col-sm-6a"></div>
                <div class="col-sm-6b">
                    <button type="submit" class="btn btn-google btn-user btn-block">
                        Đăng ký ngay
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@section('js')
<script src="/agent/js/laydate/laydate.js"></script>
<script>
    $('#collapseFour').addClass('show');
</script>
<script>
function checkForm() {
    var password = $('#password').val();
    var repassword = $('#repassword').val();
    if (password != repassword) {
        alert('Hai mật khẩu không khớp');
        return false;
    }
}
</script>
@endsection