<?php

namespace App\Admin\Metrics\Home;

use App\Models\User;
use Dcat\Admin\Widgets\Metrics\Card;

class TotalUsers extends Card
{
    protected $height = 150;

    public function __construct()
    {
        parent::__construct();

        $this->title('Tổng số thành viên');
    }

    public function render()
    {
        $this->content(User::query()->count());
        return parent::render();
    }
}
