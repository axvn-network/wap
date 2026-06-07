<?php

namespace App\Services;

use App\Services\Payments\MomoService;
use App\Services\Payments\VnPayService;
use App\Services\Payments\ZaloPayService;
use InvalidArgumentException;

class PaymentServiceFactory
{
    public static function make($type, ...$arguments)
    {
        switch ($type) {
            case 'momo':
                return new MomoService(...$arguments);
            case 'zalopay':
                return new ZaloPayService(...$arguments);
            case 'vnpay':
                return new VnPayService(...$arguments);
            // Thêm các cổng thanh toán khác ở đây
            default:
                throw new InvalidArgumentException("Không tìm thấy dịch vụ thanh toán cho loại: {$type}");
        }
    }
}
