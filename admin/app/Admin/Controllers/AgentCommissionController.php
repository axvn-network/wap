<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\AgentCommission;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class AgentCommissionController extends AdminController
{
    protected $title = 'Chi tiết hoa hồng đại lý';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new AgentCommission(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_id', 'ID Người dùng');
            $grid->column('order_id', 'ID Đơn hàng');
            $grid->column('son_user_id', 'ID thành viên cấp dưới');
            $grid->column('son_username', 'Tên người dùng cấp dưới');
            $grid->column('level', 'Cấp');
            $grid->column('rate', 'Tỷ lệ');
            $grid->column('fee', 'Phí');
            $grid->column('money', 'Số tiền');
            $grid->column('created_at', 'Thời gian tạo');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id', 'ID Người dùng');
                $filter->equal('son_user_id', 'ID thành viên cấp dưới');
            });

            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->disableEditButton();
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
        return Show::make($id, new AgentCommission(), function (Show $show) {
            $show->field('id');
            $show->field('user_id', 'ID Người dùng');
            $show->field('order_id', 'ID Đơn hàng');
            $show->field('son_user_id', 'ID thành viên cấp dưới');
            $show->field('son_username', 'Tên người dùng cấp dưới');
            $show->field('level', 'Cấp');
            $show->field('rate', 'Tỷ lệ');
            $show->field('fee', 'Phí');
            $show->field('money', 'Số tiền');
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
        return Form::make(new AgentCommission(), function (Form $form) {
            $form->display('id');
            $form->display('user_id');
            $form->display('order_id');
            $form->display('son_user_id');
            $form->display('son_username');
            $form->display('level');
            $form->display('rate');
            $form->display('fee');
            $form->display('money');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
