<?php

namespace App\Services;

use App\Models\Recharge;
use App\Models\User;
use App\Models\PaymentChannel;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    protected $user;
    protected $channel;
    protected $amount;
    protected $orderNo;

    public function __construct(User $user, PaymentChannel $channel, float $amount)
    {
        $this->user = $user;
        $this->channel = $channel;
        $this->amount = $amount;
        $this->orderNo = $this->generateOrderNo();
    }

    public function createPayment()
    {
        try {
            // Tạo bản ghi nạp tiền
            $recharge = Recharge::create([
                'order_no' => $this->orderNo,
                'user_id' => $this->user->id,
                'amount' => $this->amount,
                'pay_way' => $this->channel->id,
                'state' => 0,
                'info' => $this->channel->name
            ]);

            // Tạo thanh toán theo kênh thanh toán
            $paymentData = $this->createChannelPayment($recharge);

            // Cache thông tin thanh toán
            $this->cachePaymentInfo($recharge, $paymentData);

            return [
                'success' => true,
                'data' => [
                    'order_no' => $this->orderNo,
                    'payment_data' => $paymentData
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Tạo thanh toán thất bại: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Tạo thanh toán thất bại'
            ];
        }
    }

    protected function createChannelPayment($recharge)
    {
        switch ($this->channel->type) {
            case 'alipay':
                return $this->createAlipayPayment($recharge);
            case 'wechat':
                return $this->createWechatPayment($recharge);
            case 'bank':
                return $this->createBankPayment($recharge);
            default:
                throw new \Exception('Kênh thanh toán không được hỗ trợ');
        }
    }

    protected function createAlipayPayment($recharge)
    {
        // Logic thanh toán Alipay
        $config = [
            'app_id' => $this->channel->config['app_id'],
            'private_key' => $this->channel->config['private_key'],
            'public_key' => $this->channel->config['public_key'],
            'notify_url' => route('payment.alipay.notify'),
            'return_url' => route('payment.alipay.return'),
        ];

        $order = [
            'out_trade_no' => $this->orderNo,
            'total_amount' => $this->amount,
            'subject' => 'Nạp tiền ' . $this->amount . ' đồng',
        ];

        // Gọi SDK Alipay để tạo thanh toán
        return $this->callAlipaySDK($config, $order);
    }

    protected function createWechatPayment($recharge)
    {
        // Logic thanh toán WeChat
        $config = [
            'app_id' => $this->channel->config['app_id'],
            'mch_id' => $this->channel->config['mch_id'],
            'key' => $this->channel->config['key'],
            'notify_url' => route('payment.wechat.notify'),
        ];

        $order = [
            'out_trade_no' => $this->orderNo,
            'total_fee' => $this->amount * 100, // Chuyển đổi sang xu
            'body' => 'Nạp tiền ' . $this->amount . ' đồng',
        ];

        // Gọi SDK WeChat để tạo thanh toán
        return $this->callWechatSDK($config, $order);
    }

    protected function createBankPayment($recharge)
    {
        // Logic thanh toán qua ngân hàng
        return [
            'bank_name' => $this->channel->config['bank_name'],
            'account_name' => $this->channel->config['account_name'],
            'account_number' => $this->channel->config['account_number'],
            'amount' => $this->amount,
            'order_no' => $this->orderNo
        ];
    }

    protected function generateOrderNo()
    {
        return date('YmdHis') . Str::random(6);
    }

    protected function cachePaymentInfo($recharge, $paymentData)
    {
        $cacheKey = 'payment_' . $this->orderNo;
        Cache::put($cacheKey, [
            'recharge_id' => $recharge->id,
            'user_id' => $this->user->id,
            'amount' => $this->amount,
            'channel' => $this->channel->type,
            'payment_data' => $paymentData
        ], now()->addHours(24));
    }

    public function handlePaymentCallback($data)
    {
        try {
            $cacheKey = 'payment_' . $data['order_no'];
            $paymentInfo = Cache::get($cacheKey);

            if (!$paymentInfo) {
                throw new \Exception('Thông tin thanh toán không tồn tại hoặc đã hết hạn');
            }

            // Xác minh callback thanh toán
            if (!$this->verifyPaymentCallback($data)) {
                throw new \Exception('Xác minh thanh toán thất bại');
            }

            // Cập nhật bản ghi nạp tiền
            $recharge = Recharge::find($paymentInfo['recharge_id']);
            if ($recharge->state != 0) { // Kiểm tra xem đơn hàng đã được xử lý chưa
                return [
                    'success' => true,
                    'message' => 'Đơn hàng đã được xử lý'
                ];
            }
            $recharge->state = 2; // Thanh toán thành công
            $recharge->save();

            // Cập nhật số dư người dùng và tạo bản ghi giao dịch
            $user = User::find($paymentInfo['user_id']);
            $transactionService = resolve(TransactionService::class);
            $transactionService->createTransaction(
                $user,
                'deposit',
                $paymentInfo['amount'],
                'Nạp tiền qua ' . $recharge->info
            );

            // Xóa cache
            Cache::forget($cacheKey);

            return [
                'success' => true,
                'message' => 'Xử lý thanh toán thành công'
            ];
        } catch (\Exception $e) {
            Log::error('Xử lý callback thanh toán thất bại: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    protected function verifyPaymentCallback($data)
    {
        switch ($this->channel->type) {
            case 'alipay':
                return $this->verifyAlipayCallback($data);
            case 'wechat':
                return $this->verifyWechatCallback($data);
            case 'bank':
                return $this->verifyBankCallback($data);
            default:
                return false;
        }
    }

    protected function verifyAlipayCallback($data)
    {
        // Logic xác minh callback Alipay
        return true;
    }

    protected function verifyWechatCallback($data)
    {
        // Logic xác minh callback WeChat
        return true;
    }

    protected function verifyBankCallback($data)
    {
        // Logic xác minh callback ngân hàng
        return true;
    }
}
