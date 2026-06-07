<?php

namespace App\Services\Payments;

use App\Models\Recharge;
use App\User;
use App\Services\GamereportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VnPayService implements PaymentServiceInterface
{
    protected $user;
    protected $channel;
    protected $amount;

    public function __construct($user, $channel, $amount)
    {
        $this->user = $user;
        $this->channel = $channel;
        $this->amount = $amount;
    }

    public function createPayment()
    {
        $order_no = 'VNP' . date('ymdHis') . rand(100, 999);

        Recharge::create([
            'order_no' => $order_no,
            'user_id' => $this->user->id,
            'amount' => $this->amount,
            'real_money' => $this->amount,
            'pay_way' => 2, // Giả định 2 là VnPay dựa trên controller
            'state' => 1, // Chờ thanh toán
        ]);

        Log::channel('backend')->info("Khởi tạo thanh toán VnPay", [
            'user_id' => $this->user->id,
            'order_no' => $order_no,
            'amount' => $this->amount
        ]);

        return [
            'success' => true,
            'data' => [
                'order_no' => $order_no,
                'payment_data' => []
            ]
        ];
    }

    public function handlePaymentCallback(array $data)
    {
        Log::channel('backend')->info('Nhận callback từ VnPay', $data);

        $order_no = $data['vnp_TxnRef'] ?? '';
        
        return DB::transaction(function () use ($order_no, $data) {
            $recharge = Recharge::where('order_no', $order_no)->lockForUpdate()->first();

            if (!$recharge) {
                Log::channel('backend')->error("Callback VnPay: Không tìm thấy đơn hàng", ['order_no' => $order_no]);
                return ['success' => false, 'message' => 'Order not found'];
            }

            if ($recharge->state != 1) {
                Log::channel('backend')->warning("Callback VnPay: Đơn hàng đã được xử lý trước đó", ['order_no' => $order_no, 'state' => $recharge->state]);
                return ['success' => true, 'message' => 'Already processed'];
            }

            if (isset($data['vnp_ResponseCode']) && $data['vnp_ResponseCode'] == '00') {
                // Thanh toán thành công
                $recharge->state = 2;
                $recharge->save();

                $user = User::lockForUpdate()->find($recharge->user_id);
                if ($user) {
                    $user->balance += $recharge->amount;
                    $user->paysum += $recharge->amount;
                    $user->save();

                    // Cập nhật báo cáo game
                    $gamereport = new GamereportService();
                    $gamereport->add([
                        'uid' => $user->id,
                        'pid' => $user->pid,
                        'isagent' => $user->isagent,
                        'totalrechange' => $recharge->amount,
                    ]);

                    Log::channel('backend')->info("Callback VnPay: Nạp tiền thành công", [
                        'user_id' => $user->id,
                        'order_no' => $order_no,
                        'amount' => $recharge->amount
                    ]);
                }
            } else {
                // Thanh toán thất bại
                $recharge->state = 3;
                $recharge->save();
                Log::channel('backend')->warning("Callback VnPay: Thanh toán thất bại hoặc bị hủy", ['order_no' => $order_no]);
            }

            return ['success' => true];
        });
    }

    public function successResponse()
    {
        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }

    public function failureResponse($message)
    {
        return response()->json(['RspCode' => '99', 'Message' => $message]);
    }
}
