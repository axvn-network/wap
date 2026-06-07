<?php

namespace App\Services\Payments;

use App\Models\Recharge;
use App\User;
use App\Services\GamereportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MomoService implements PaymentServiceInterface
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
        $order_no = 'MOM' . date('ymdHis') . rand(100, 999);

        Recharge::create([
            'order_no' => $order_no,
            'user_id' => $this->user->id,
            'amount' => $this->amount,
            'real_money' => $this->amount,
            'pay_way' => 4, // Giả định 4 là Momo
            'state' => 1, // Pending
        ]);

        Log::channel('backend')->info("Khởi tạo thanh toán Momo", [
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
        Log::channel('backend')->info('Nhận callback từ Momo', $data);

        $order_no = $data['orderId'] ?? '';
        
        return DB::transaction(function () use ($order_no, $data) {
            $recharge = Recharge::where('order_no', $order_no)->lockForUpdate()->first();

            if (!$recharge) {
                Log::channel('backend')->error("Callback Momo: Không tìm thấy đơn hàng", ['order_no' => $order_no]);
                return ['success' => false, 'message' => 'Order not found'];
            }

            if ($recharge->state != 1) {
                Log::channel('backend')->warning("Callback Momo: Đơn hàng đã được xử lý trước đó", ['order_no' => $order_no, 'state' => $recharge->state]);
                return ['success' => true, 'message' => 'Already processed'];
            }

            // Theo tài liệu Momo, resultCode = 0 là thành công
            if (isset($data['resultCode']) && $data['resultCode'] == 0) {
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

                    Log::channel('backend')->info("Callback Momo: Nạp tiền thành công", [
                        'user_id' => $user->id,
                        'order_no' => $order_no,
                        'amount' => $recharge->amount
                    ]);
                }
            } else {
                $recharge->state = 3;
                $recharge->save();
                Log::channel('backend')->warning("Callback Momo: Thanh toán thất bại", ['order_no' => $order_no, 'resultCode' => $data['resultCode'] ?? 'N/A']);
            }

            return ['success' => true];
        });
    }

    public function successResponse()
    {
        return response()->json(['resultCode' => 0, 'message' => 'Thành công']);
    }

    public function failureResponse($message)
    {
        return response()->json(['resultCode' => 1, 'message' => $message]);
    }
}
