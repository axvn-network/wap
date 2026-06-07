<?php

namespace App\Services\Payments;

use App\Models\Recharge;
use Illuminate\Support\Facades\Log;

class ZaloPayService implements PaymentServiceInterface
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
        $order_no = 'ZALO' . time() . rand(1000, 9999);

        Recharge::create([
            'order_no' => $order_no,
            'user_id' => $this->user->id,
            'amount' => $this->amount,
            'channel_id' => $this->channel->id,
            'state' => 1, // Pending
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
        Log::info('ZaloPay callback received', $data);

        $order_no = $data['app_trans_id'];
        $recharge = Recharge::where('order_no', $order_no)->first();

        if ($recharge && $recharge->state == 1) {
            if ($data['status'] == 1) {
                $recharge->update(['state' => 2]); // Success
            } else {
                $recharge->update(['state' => 3]); // Failed
            }
        }

        return ['success' => true];
    }

    public function successResponse()
    {
        return response()->json(['return_code' => 1, 'return_message' => 'success']);
    }

    public function failureResponse($message)
    {
        return response()->json(['return_code' => 2, 'return_message' => $message]);
    }
}
