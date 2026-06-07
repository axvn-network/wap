<?php

namespace App\Admin\Metrics\Home;

use App\Models\Withdraw;
use Dcat\Admin\Widgets\Metrics\Card;
use Illuminate\Support\Carbon;

class TodayWithdraw extends Card
{
    protected $height = 150;

    public function __construct()
    {
        parent::__construct();

        $this->title('Rút tiền hôm nay');
    }

    public function render()
    {
        $todayWithdraw = Withdraw::query()->where('state', 2)->whereDate('updated_at', Carbon::today())->sum('amount');
        $this->content(number_format($todayWithdraw, 2));
        return parent::render();
    }
}
