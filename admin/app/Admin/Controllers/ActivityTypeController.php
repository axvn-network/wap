<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\ActivityType;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ActivityTypeController extends AdminController
{
    protected $title = 'Loại hoạt động';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ActivityType(), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('title', 'Tiêu đề');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
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
        return Show::make($id, new ActivityType(), function (Show $show) {
            $show->field('id');
            $show->field('title', 'Tiêu đề');
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
        return Form::make(new ActivityType(), function (Form $form) {
            $form->display('id');
            $form->text('title', 'Tiêu đề');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
