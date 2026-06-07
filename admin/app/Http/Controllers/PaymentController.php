<?php

namespace App\Http\Controllers;

use App\Models\PaymentChannel;
use App\Models\Recharge;
use App\Services\PaymentServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index()
    {
        $channels = PaymentChannel::where('status', true)
            ->orderBy('sort')
            ->get();

        return view('payment.index', compact('channels'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'channel_id' => 'required|exists:payment_channels,id',
            'amount' => 'required|numeric|min:1'
        ]);

        $channel = PaymentChannel::findOrFail($request->channel_id);

        if (!$channel->isAvailable()) {
            return back()->with('error', trans('payment.channel_unavailable'));
        }

        if (!$channel->validateAmount($request->amount)) {
            return back()->with('error', trans('payment.amount_out_of_range', [
                'min' => number_format($channel->min_amount, 2),
                'max' => number_format($channel->max_amount, 2)
            ]));
        }

        $paymentService = PaymentServiceFactory::make($channel->type, Auth::user(), $channel, $request->amount);
        $result = $paymentService->createPayment();

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return view('payment.pay', [
            'order_no' => $result['data']['order_no'],
            'payment_data' => $result['data']['payment_data'],
            'channel' => $channel
        ]);
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'order_no' => 'required|string'
        ]);

        $recharge = Recharge::where('order_no', $request->order_no)->first();

        if (!$recharge) {
            return response()->json([
                'success' => false,
                'message' => trans('payment.order_not_found')
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $recharge->state,
                'message' => $this->getStatusMessage($recharge->state)
            ]
        ]);
    }

    public function handleCallback(Request $request, $channel_type)
    {
        Log::info("Nhận callback thanh toán cho kênh: {$channel_type}", $request->all());

        $channel = PaymentChannel::where('type', $channel_type)->firstOrFail();
        $paymentService = PaymentServiceFactory::make($channel_type, null, $channel, 0);
        $result = $paymentService->handlePaymentCallback($request->all());

        if ($result['success']) {
            return $paymentService->successResponse();
        }

        Log::error("Xử lý callback thanh toán thất bại cho kênh: {$channel_type}", ['message' => $result['message']]);
        return $paymentService->failureResponse($result['message']);
    }

    protected function getStatusMessage($state)
    {
        return trans("payment.status.{$state}", [], 'vi', 'payment.status.unknown');
    }
}
