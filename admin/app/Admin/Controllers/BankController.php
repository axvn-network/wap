<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Bank;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class BankController extends AdminController
{
    protected $title = 'Quản lý ngân hàng';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Bank(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('bank_name', 'Tên ngân hàng');
            $grid->column('bank_code', 'Mã ngân hàng');
            $grid->column('created_at', 'Thời gian tạo');
            $grid->column('updated_at', 'Thời gian cập nhật')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('bank_name', 'Tên ngân hàng');
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
        return Show::make($id, new Bank(), function (Show $show) {
            $show->field('id', 'ID');
            $show->field('bank_name', 'Tên ngân hàng');
            $show->field('bank_code', 'Mã ngân hàng');
            $show->field('created_at', 'Thời gian tạo');
            $show->field('updated_at', 'Thời gian cập nhật');
        });
    }

    /**
     * Tạo form thêm/sửa.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Bank(), function (Form $form) {
            $form->display('id', 'ID');
            $form->text('bank_name', 'Tên ngân hàng')->required();
            $form->text('bank_code', 'Mã ngân hàng')->required();

            $form->display('created_at', 'Thời gian tạo');
            $form->display('updated_at', 'Thời gian cập nhật');
        });
    }
}
