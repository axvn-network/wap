<?php

namespace App\Services;

use Illuminate\Support\Facades\Cookie;
use App\Models\SystemConfig;
use App\Models\Api;

class TgService
{
    protected $api_account;
    protected $sign_key;
    protected $api_url;
    protected $showlang;

    public function __construct()
    {
        $this->api_url = SystemConfig::getValue('game_api');
        $this->api_account = SystemConfig::getValue('merchant_account');
        $this->sign_key = SystemConfig::getValue('api_secret');
        $this->showlang = empty(Cookie::get("userlang")) ? 'vi' : Cookie::get("userlang');
    }

    private function generateCode(array $data)
    {
        ksort($data);
        $str = '';
        foreach ($data as $v) {
            $str .= $v;
        }
        $str .= $this->sign_key;
        return strtoupper(md5($str));
    }

    private function sendRequest($url, $post_data = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 600);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $contents = curl_exec($ch);
        curl_close($ch);

        return json_decode($contents, true);
    }

    private function sendRequest_ley($url, $post_data = [])
    {
        $post_data['account'] = $this->api_account;
        $post_data['api_key'] = $this->sign_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);

        if (!$data) {
            return ['Code' => -1, 'Message' => 'Giao tiếp giữa máy chủ và API thất bại, vui lòng kiểm tra lại địa chỉ API.'];
        }

        $decoded_data = json_decode($data, true);
        if (!is_array($decoded_data)) {
            return ['Code' => -1, 'Message' => 'Phân tích dữ liệu trả về thất bại.'];
        }

        return $decoded_data;
    }

    public function register($api_code, $username, $password = '123456')
    {
        $data = [
            'username' => $username,
            'password' => $password,
            'api_code' => $api_code,
        ];
        $res = $this->sendRequest_ley($this->api_url . "/ley/register", $data);
        if ($res['Code'] != '0') {
            return ['code' => 201, 'message' => $res['Message']];
        }
        return ['code' => 200, 'message' => 'Thành công'];
    }

    public function login($username, $plat_type = "ag", $game_type = "1", $is_mobile_url = 0, $game_code = "0", $lang = 'vi-vn', $password = '123456')
    {
        $data = [
            'api_code' => $plat_type,
            'username' => $username,
            'password' => $password,
            'isMobile' => $is_mobile_url,
            'gameType' => $game_type, // Loại game: 1=Live, 2=Bắn cá, 3=Slot, 4=Xổ số, 5=Thể thao, 6=Game bài, 7=E-sport
            'gameCode' => $game_code,
        ];

        $res = $this->sendRequest_ley($this->api_url . "/ley/login", $data);
        if ($res['Code'] != '0') {
            return ['code' => 201, 'message' => $res['Message']];
        }
        return ['code' => 200, 'message' => 'Thành công', 'data' => $res['Data']['url']];
    }

    public function balance($code, $user_api, $pass_api = '123456')
    {
        $data = [
            'api_code' => $code,
            'username' => $user_api,
            'password' => $pass_api,
        ];
        $res = $this->sendRequest_ley($this->api_url . "/ley/balance", $data);
        if ($res['Code'] != '0') {
            return ['code' => 201, 'message' => $res['Message']];
        }
        return ['code' => 200, 'message' => 'Thành công', 'data' => $res['Data']['balance']];
    }

    public function deposit($user_api, $amount, $transferno, $code)
    {
        $data = [
            'api_code' => $code,
            'username' => $user_api,
            'password' => '123456',
            'amount' => floor($amount),
            'transferno' => $transferno,
        ];
        $res = $this->sendRequest_ley($this->api_url . "/ley/deposit", $data);
        if ($res['Code'] != '0') {
            return ['code' => 201, 'message' => $res['Message']];
        }
        return ['code' => 200, 'message' => 'Thành công'];
    }

    public function withdrawal($user_api, $amount, $transferno, $code)
    {
        $data = [
            'api_code' => $code,
            'username' => $user_api,
            'password' => '123456',
            'amount' => floor($amount),
            'transferno' => $transferno,
        ];
        $res = $this->sendRequest_ley($this->api_url . "/ley/withdrawal", $data);
        if ($res['Code'] != '0') {
            return ['code' => 201, 'message' => $res['Message']];
        }
        return ['code' => 200, 'message' => 'Thành công'];
    }

    public function credit($api_code)
    {
        $data = ['api_code' => $api_code];
        $res = $this->sendRequest_ley($this->api_url . "/ley/credit", $data);
        if ($res['Code'] != '0') {
            return ['code' => 201, 'message' => $res['Message']];
        }
        return ['code' => 200, 'message' => 'Thành công', 'data' => $res['Data']['money']];
    }

    public function gamerecord($data)
    {
        $post_data = [
            'page' => $data['page'],
            'pageSize' => $data['pageSize'],
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
            'method' => $data['method']
        ];
        return $this->sendRequest_ley($this->api_url . "/ley/gamerecord", $post_data);
    }

    public function gamesalllist()
    {
        return Api::where('state', 1)->get(['api_code as platform_code', 'api_name as platformname'])->toArray();
    }

    public function getallgamename()
    {
        return Api::where('state', 1)->pluck('api_name', 'api_code')->toArray();
    }

    public function engamelist()
    {
        $res = $this->sendRequest($this->api_url . "/api/engamelist", []);
        if (empty($res) || !is_array($res) || !isset($res['code']) || ($res['code'] != 1 && $res['code'] != 'success') || !isset($res['data'])) {
            return [];
        }
        return $res['data'];
    }
}
