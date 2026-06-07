<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Home\Chart\Recharge;
use App\Admin\Metrics\Home\Chart\Withdraw;
use App\Admin\Metrics\Home\TotalUsers;
use App\Admin\Metrics\Home\TotalAgents;
use App\Admin\Metrics\Home\TodayRecharge;
use App\Admin\Metrics\Home\TodayWithdraw;
use App\Http\Controllers\Controller;
use Dcat\Admin\Http\Controllers\Dashboard;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('Trang chủ')
            ->description('Tổng quan...')
            ->body(function (Row $row) {
                $row->column(3, new TotalUsers());
                $row->column(3, new TotalAgents());
                $row->column(3, new TodayRecharge());
                $row->column(3, new TodayWithdraw());
            })
            ->body(function (Row $row) {
                $row->column(6, function (Column $column) {
                    $column->append(new Recharge());
                });
                $row->column(6, function (Column $column) {
                    $column->append(new Withdraw());
                });
            });
    }
}
