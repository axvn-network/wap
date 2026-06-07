<?php

namespace App\Services\Payments;

interface PaymentServiceInterface
{
    public function createPayment();

    public function handlePaymentCallback(array $data);

    public function successResponse();

    public function failureResponse($message);
}
