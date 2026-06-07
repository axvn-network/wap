<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Rebate;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class RebateController extends AdminController
{
    protected $title = 'Lịch sử hoàn tiền';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Rebate::with(['user_data', 'from_user']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_data.username', 'Tên người dùng');
            $grid->column('from_user.username', 'Từ người dùng');
            $grid->column('level', 'Cấp độ');
            $grid->column('money', 'Số tiền');
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
        return Show::make($id, new Rebate(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('from_id');
            $show->field('level');
            $show->field('money');
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
        return Form::make(new Rebate(), function (Form $form) {
            $form->display('id');
            $form->display('user_id');
            $form->display('from_id');
            $form->display('level');
            $form->display('money');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
