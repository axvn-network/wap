<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Home;
use App\Http\Controllers\Controller;
use App\Models\ActivityApply;
use App\Models\AgentApply;
use App\Models\Recharge;
use App\Models\SystemConfig;
use App\Models\Withdraw;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Card;
use App\Services\TgService;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('Trang chủ')
            ->description('Tổng quan hệ thống')
            ->body(function (Row $row) {
                // Thống kê nhanh
                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(3, new Home\TodayRecharge());
                        $row->column(3, new Home\TodayWithdraw());
                        $row->column(3, new Home\TotalUsers());
                        $row->column(3, new Home\AgentPercent());
                    });
                    $column->row(function (Row $row) {
                        $row->column(3, new Home\MonthRecharge());
                        $row->column(3, new Home\MonthWithdraw());
                    });
                });

                // Hạn mức của đơn vị
                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $merchantCredit = $this->merchantCredit();
                        $tg = new TgService;
                        $gamelist = $tg->gamesalllist();
                        
                        $platgamename = ['universal' => 'Hạn mức chung'];
                        foreach ($gamelist as $val) {
                            $platgamename[$val['platform_code']] = $val['platformname'];
                        }
                        
                        $platgame = $tg->engamelist();

                        if (!empty($merchantCredit['data'])) {
                            if ($merchantCredit['data']['money_type'] == 2) {
                                $row->column(12, $this->card('Hạn mức chung', (string)$merchantCredit['data']['merchant_money']['universal_money'], '#20c9b1'));
                            }
                            if ($merchantCredit['data']['money_type'] == 1) {
                                foreach ($merchantCredit['data']['merchant_money'] as $key => $val) {
                                    $vote = str_replace('_money', '', $key);
                                    if (in_array($vote, $platgame) && isset($platgamename[$vote])) {
                                        $row->column(2, $this->card($platgamename[$vote], (string)$val, '#7986CB'));
                                    }
                                }
                            }
                        }
                    });
                });

                // Biểu đồ
                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(6, Card::make('Nạp tiền trong 7 ngày qua', new Home\Chart\Recharge()));
                        $row->column(6, Card::make('Rút tiền trong 7 ngày qua', new Home\Chart\Withdraw()));
                    });
                });
            });
    }

    protected function card($text, $money, $color = '#fff')
    {
        $content = $this->p($text, $money);
        return <<<EOF
<div style="background:$color;padding:10px 22px 16px;box-shadow:0 1px 3px 1px rgba(34, 25, 25, 0.1);margin-bottom:8px; border-radius: 5px;">
   <li style="list-style: none;">$content</li>
</div>
EOF;
    }

    protected function p($text, $money, $height = 40)
    {
        return "<p style='height:{$height}px;color:#fff;text-align: center;padding-top: 10px; font-size:16px;'><span style='width: 100%; float: left;'>$text</span><span style='width: 100%;float: left; font-weight: bold;'>$money</span></p>";
    }

    /**
     * Truy vấn hạn mức của đơn vị
     *
     * @return array
     */
    public function merchantCredit()
    {
        $this->api_url = SystemConfig::getValue('game_api');
        $this->api_account = SystemConfig::getValue('merchant_account');
        $this->sign_key = SystemConfig::getValue('api_secret');

        if (empty($this->api_url) || empty($this->api_account) || empty($this->sign_key)) {
            return ['data' => []]; // Trả về mảng rỗng nếu thiếu cấu hình
        }

        $data = [
            'api_account' => $this->api_account,
        ];
        $data['code'] = $this->generateCode($data);
        return $this->sendRequest($this->api_url . "/api/user/all-credit", $data);
    }

    public function getAlertData()
    {
        $recharge_apply = Recharge::where('state', 1)->count();
        $withdraw_apply = Withdraw::where('state', 1)->count();
        $agent_apply = AgentApply::where('state', 1)->count();
        $activity_apply = ActivityApply::where('state', 1)->count();
        
        $alert_type = SystemConfig::getValue('notice_set');
        $recharge_apply_audio = '';
        $withdraw_apply_audio = '';
        $activity_apply_audio = '';
        $agent_apply_audio = '';

        if ($alert_type == 1 || $alert_type == 2) {
            $recharge_apply_audio = SystemConfig::getValue('recharge_apply_audio');
            $withdraw_apply_audio = SystemConfig::getValue('withdraw_apply_audio');
            $activity_apply_audio = SystemConfig::getValue('activity_apply_audio');
            $agent_apply_audio = SystemConfig::getValue('agent_apply_audio');
        }

        return [
            'recharge_apply' => $recharge_apply,
            'withdraw_apply' => $withdraw_apply,
            'agent_apply' => $agent_apply,
            'activity_apply' => $activity_apply,
            'alert_type' => $alert_type,
            'recharge_apply_audio' => $recharge_apply_audio,
            'withdraw_apply_audio' => $withdraw_apply_audio,
            'activity_apply_audio' => $activity_apply_audio,
            'agent_apply_audio' => $agent_apply_audio,
        ];
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
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $contents = curl_exec($ch);
        curl_close($ch);

        return json_decode($contents, true);
    }
}
