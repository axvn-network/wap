<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Financial;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FinancialController extends AdminController
{
    protected $title = 'Sản phẩm tài chính';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Financial(), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('title', 'Tiêu đề');
            $grid->column('days', 'Số ngày');
            $grid->column('rate', 'Lãi suất');
            $grid->column('min_money', 'Số tiền tối thiểu');
            $grid->column('max_money', 'Số tiền tối đa');
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
        });
    }

    /**
     * Tạo giao diện xem chi tiết.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Financial(), function (Show $show) {
            $show->field('id');
            $show->field('title');
            $show->field('days');
            $show->field('rate');
            $show->field('min_money');
            $show->field('max_money');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Tạo form thêm/sửa.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Financial(), function (Form $form) {
            $form->display('id');
            $form->text('title')->required();
            $form->number('days')->required();
            $form->decimal('rate', 'Lãi suất')->required();
            $form->number('min_money')->required();
            $form->number('max_money')->required();

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
