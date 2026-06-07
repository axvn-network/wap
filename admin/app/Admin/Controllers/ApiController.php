<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Api;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ApiController extends AdminController
{
    protected $title = 'Quản lý API';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Api(), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('api_name', 'Tên API');
            $grid->column('api_code', 'Mã API');
            $grid->column('api_key', 'Khóa API');
            $grid->column('api_balance', 'Số dư');
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
        return Show::make($id, new Api(), function (Show $show) {
            $show->field('id');
            $show->field('api_name');
            $show->field('api_code');
            $show->field('api_key');
            $show->field('api_balance');
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
        return Form::make(new Api(), function (Form $form) {
            $form->display('id');
            $form->text('api_name', 'Tên API');
            $form->text('api_code', 'Mã API');
            $form->text('api_key', 'Khóa API');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
