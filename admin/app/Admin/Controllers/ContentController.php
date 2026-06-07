<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;

class ContentController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->title('Trang chủ')
            ->description('Mô tả...')
            ->row(function (Row $row) {
                // ...
            });
    }
}
