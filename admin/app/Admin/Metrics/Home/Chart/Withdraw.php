<?php

namespace App\Admin\Metrics\Home\Chart;

use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\ApexCharts\Chart;
use App\Models\Withdraw as WithdrawModel;
use Illuminate\Support\Carbon;

class Withdraw extends Chart
{
    public function __construct($containerSelector = null, $options = [])
    {
        parent::__construct($containerSelector, $options);
        $this->setUpOptions();
        $this->title('Thống kê rút tiền'); // Adding a title
    }

    /**
     * Initializes chart options.
     */
    protected function setUpOptions()
    {
        $color = Admin::color();
        $colors = [$color->success(), $color->danger()];

        $this->options([
            'colors' => $colors,
            'chart' => [
                'type' => 'area',
                'height' => 430,
                'sparkline' => ['enabled' => true],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'xaxis' => [
                'categories' => [],
            ],
            'tooltip' => [
                'x' => [
                    'show' => false,
                ],
            ],
            'legend' => [
                'show' => true,
            ],
        ]);
    }

    /**
     * Handles chart data.
     */
    protected function buildData()
    {
        $approvedData = [];
        $refusedData = [];
        $categories = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $category = $date->format('m-d');

            $approvedCount = WithdrawModel::query()
                ->where('state', 2) // Approved
                ->whereDate('updated_at', $dateString)
                ->count();

            $refusedCount = WithdrawModel::query()
                ->where('state', 3) // Refused
                ->whereDate('updated_at', $dateString)
                ->count();

            $approvedData[] = $approvedCount;
            $refusedData[] = $refusedCount;
            $categories[] = $category;
        }

        $this->withData([
            [
                'name' => 'Đã duyệt',
                'data' => $approvedData,
            ],
            [
                'name' => 'Bị từ chối',
                'data' => $refusedData,
            ],
        ]);

        $this->withCategories($categories);
    }

    /**
     * Sets chart data.
     *
     * @param array $data
     *
     * @return $this
     */
    public function withData(array $data)
    {
        return $this->option('series', $data);
    }

    /**
     * Sets chart categories.
     *
     * @param array $data
     *
     * @return $this
     */
    public function withCategories(array $data)
    {
        return $this->option('xaxis.categories', $data);
    }

    /**
     * Renders the chart.
     *
     * @return string
     */
    public function render()
    {
        $this->buildData();
        return parent::render();
    }
}
