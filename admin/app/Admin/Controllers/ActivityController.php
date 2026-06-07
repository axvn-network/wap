<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Activity;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ActivityController extends AdminController
{
    protected $title = 'Quản lý hoạt động';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Activity(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('title', 'Tiêu đề');
            $grid->column('start_time', 'Thời gian bắt đầu')->display(function ($startTime) {
                return date('Y-m-d H:i:s', $startTime);
            });
            $grid->column('end_time', 'Thời gian kết thúc')->display(function ($endTime) {
                return date('Y-m-d H:i:s', $endTime);
            });
            $grid->column('status', 'Trạng thái')->switch();
            $grid->column('is_top', 'Ghim')->switch();
            $grid->column('created_at');
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Activity(), function (Show $show) {
            $show->field('id');
            $show->field('title', 'Tiêu đề');
            $show->field('content', 'Nội dung');
            $show->field('start_time', 'Thời gian bắt đầu');
            $show->field('end_time', 'Thời gian kết thúc');
            $show->field('status', 'Trạng thái');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Activity(), function (Form $form) {
            $form->display('id');
            $form->text('title', 'Tiêu đề')->required();
            $form->datetime('start_time', 'Thời gian bắt đầu')->required();
            $form->datetime('end_time', 'Thời gian kết thúc')->required();
            $form->switch('status', 'Trạng thái')->default(1);
            $form->switch('is_top', 'Ghim')->default(0);
            $form->editor('content', 'Nội dung');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
