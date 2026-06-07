@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Đơn hàng thanh toán</div>

                <div class="card-body">
                    <div class="order-info mb-4">
                        <h5>Thông tin đơn hàng</h5>
                        <p>Mã đơn hàng:{{ $order_no }}</p>
                        <p>Số tiền thanh toán:{{ number_format($payment_data['amount'], 2) }} đồng</p>
                    </div>

                    @if($channel->type == 'alipay')
                        <div class="alipay-payment">
                            <div id="alipay-qrcode"></div>
                            <p class="text-center mt-3">Vui lòng sử dụng Alipay để quét mã thanh toán</p>
                        </div>
                    @elseif($channel->type == 'wechat')
                        <div class="wechat-payment">
                            <div id="wechat-qrcode"></div>
                            <p class="text-center mt-3">Vui lòng sử dụng WeChat để quét mã thanh toán</p>
                        </div>
                    @elseif($channel->type == 'bank')
                        <div class="bank-payment">
                            <div class="bank-info">
                                <p>Ngân hàng mở tài khoản:{{ $payment_data['bank_name'] }}</p>
                                <p>Tên tài khoản:{{ $payment_data['account_name'] }}</p>
                                <p>Số tài khoản:{{ $payment_data['account_number'] }}</p>
                                <p>Số tiền:{{ number_format($payment_data['amount'], 2) }} đồng</p>
                            </div>
                            <div class="alert alert-info">
                                <p>Vui lòng sử dụng tài khoản ngân hàng trên để chuyển khoản, khi chuyển khoản vui lòng ghi chú mã đơn hàng:{{ $order_no }}</p>
                                <p>Sau khi chuyển khoản thành công, hệ thống sẽ tự động xác nhận đã nhận được tiền</p>
                            </div>
                        </div>
                    @endif

                    <div class="payment-status mt-4">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                role="progressbar" style="width: 0%"></div>
                        </div>
                        <p class="text-center mt-2" id="status-message">Đang chờ thanh toán...</p>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('payment.index') }}" class="btn btn-secondary">Quay lại</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.order-info {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}

.bank-info {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.bank-info p {
    margin-bottom: 0.5rem;
}

#alipay-qrcode, #wechat-qrcode {
    display: flex;
    justify-content: center;
    margin: 2rem 0;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
$(document).ready(function() {
    const orderNo = '{{ $order_no }}';
    let checkCount = 0;
    const maxChecks = 60; // Check up to 60 times, i.e. 5 minutes

    @if($channel->type == 'alipay')
        new QRCode(document.getElementById("alipay-qrcode"), {
            text: '{{ $payment_data["pay_url"] }}',
            width: 200,
            height: 200
        });
    @elseif($channel->type == 'wechat')
        new QRCode(document.getElementById("wechat-qrcode"), {
            text: '{{ $payment_data["code_url"] }}',
            width: 200,
            height: 200
        });
    @endif

    function checkPaymentStatus() {
        if (checkCount >= maxChecks) {
            $('#status-message').text('Thanh toán quá thời gian, vui lòng bắt đầu lại thanh toán');
            $('.progress-bar').css('width', '100%');
            return;
        }

        $.get('{{ route("payment.check-status") }}', { order_no: orderNo })
            .done(function(response) {
                if (response.success) {
                    const status = response.data.status;
                    const message = response.data.message;
                    
                    $('#status-message').text(message);
                    
                    if (status === 2) { // payment successful
                        $('.progress-bar').css('width', '100%');
                        setTimeout(function() {
                            window.location.href = '{{ route("payment.success") }}';
                        }, 1500);
                        return;
                    } else if (status === 3) { // payment failed
                        $('.progress-bar').css('width', '100%');
                        return;
                    }
                    
                    // Update progress bar
                    const progress = (checkCount / maxChecks) * 100;
                    $('.progress-bar').css('width', progress + '%');
                    
                    checkCount++;
                    setTimeout(checkPaymentStatus, 5000); // Check every 5 seconds
                }
            })
            .fail(function() {
                $('#status-message').text('Kiểm tra trạng thái thanh toán không thành công, vui lòng làm mới trang và thử lại');
            });
    }

    checkPaymentStatus();
});
</script>
@endpush
@endsection 