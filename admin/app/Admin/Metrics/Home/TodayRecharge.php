<?php

namespace App\Admin\Metrics\Home;

use App\Models\Recharge;
use Dcat\Admin\Widgets\Metrics\Card;
use Illuminate\Support\Carbon;

class TodayRecharge extends Card
{
    protected $height = 150;

    public function __construct()
    {
        parent::__construct();

        $this->title('Nạp tiền hôm nay');
    }

    public function render()
    {
        $todayRecharge = Recharge::query()->whereDate('created_at', Carbon::today())->sum('amount');
        $this->content(number_format($todayRecharge, 2));
        return parent::render();
    }
}
