<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FinancialOrder;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FinancialOrderController extends AdminController
{
    protected $title = 'Lệnh tài chính';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(FinancialOrder::with(['user_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_data.username', 'Tên người dùng');
            $grid->column('financial_id', 'ID Sản phẩm');
            $grid->column('money', 'Số tiền');
            $grid->column('state', 'Trạng thái')->using([
                1 => 'Đang giữ',
                2 => 'Đã kết thúc'
            ])->dot([
                1 => 'primary',
                2 => 'success'
            ]);
            $grid->column('created_at', 'Thời gian tạo');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('user_data.username', 'Tên người dùng');
            });

            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->disableEditButton();
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
        return Show::make($id, new FinancialOrder(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('financial_id');
            $show->field('money');
            $show->field('state');
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
        return Form::make(new FinancialOrder(), function (Form $form) {
            $form->display('id');
            $form->display('user_id');
            $form->display('financial_id');
            $form->display('money');
            $form->display('state');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
