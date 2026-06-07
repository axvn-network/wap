<?php

namespace App\Services;

class Zgpay
{

    public function __construct($merchant_id, $api_secret)
    {
        $this->merchant_id = $merchant_id;
        $this->api_secret = $api_secret;
    }

    public function withdraw($order_no, $amount, $username, $wallet_address)
    {
        $url = 'https://zgpay.cc/api/withdraw';
        $ch = curl_init($url);

        $merchant_id = $this->merchant_id;
        // Mã đơn hàng của đơn vị
        $out_trade_no = $order_no;
        // Số tiền rút
        $total_fee = $amount;
        // Tên người dùng
        $user_name = $username;
        // Địa chỉ ví của người dùng
        $user_wallet = $wallet_address;
        // URL callback khi thanh toán thành công
        $notify_url = env('APP_URL') . '/zgp-withdraw-callback';
        // Khóa bí mật của đơn vị
        $api_secret = $this->api_secret;

        $sign = "";
        if ($merchant_id != '') {
            $sign .= $merchant_id;
        }
        if ($notify_url != '') {
            $sign .= $notify_url;
        }
        if ($out_trade_no != '') {
            $sign .= $out_trade_no;
        }
        if ($total_fee != '') {
            $sign .= $total_fee;
        }
        if ($user_name != '') {
            $sign .= $user_name;
        }
        if ($user_wallet != '') {
            $sign .= $user_wallet;
        }
        $sign = md5($sign . $api_secret);

        $data = array(
            'merchant_id' => $merchant_id,
            'notify_url' => $notify_url,
            'out_trade_no' => $out_trade_no,
            'total_fee' => $total_fee,
            'user_name' => $user_name,
            'user_wallet' => $user_wallet,
            'sign' => $sign,
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Thực thi yêu cầu
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
