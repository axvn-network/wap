<?php

namespace App\Admin\Metrics\Home;

use App\Models\Agent;
use Dcat\Admin\Widgets\Metrics\Card;

class TotalAgents extends Card
{
    protected $height = 150;

    public function __construct()
    {
        parent::__construct();

        $this->title('Tổng số đại lý');
    }

    public function render()
    {
        $this->content(Agent::query()->count());
        return parent::render();
    }
}
