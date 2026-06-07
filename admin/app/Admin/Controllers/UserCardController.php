<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\UserCard;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class UserCardController extends AdminController
{
    protected $title = 'Thẻ ngân hàng của thành viên';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(UserCard::with(['user_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_data.username', 'Tên người dùng')->view('admin.field.user_username');
            $grid->column('bank', 'Loại');
            $grid->column('bank_no', 'Số tài khoản/Địa chỉ USDT');
            $grid->column('bank_owner', 'Tên chủ tài khoản/Giao thức');
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('user_data.username', 'Tên người dùng');
            });

            // Vô hiệu hóa các nút không cần thiết vì người dùng tự quản lý thẻ
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
        return Show::make($id, UserCard::with(['user_data']), function (Show $show) {
            $show->field('id', 'ID');
            $show->field('user_data.username', 'Tên người dùng');
            $show->field('bank', 'Loại');
            $show->field('bank_no', 'Số tài khoản/Địa chỉ USDT');
            $show->field('bank_address', 'Chi nhánh');
            $show->field('bank_owner', 'Tên chủ tài khoản/Giao thức');
            $show->field('created_at', 'Thời gian tạo');
            $show->field('updated_at', 'Thời gian cập nhật');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new UserCard(), function (Form $form) {
            $form->display('id', 'ID');
            $form->display('user_id', 'ID Người dùng');
            $form->display('bank', 'Loại');
            $form->display('bank_no', 'Số tài khoản/Địa chỉ USDT');
            $form->display('bank_address', 'Chi nhánh');
            $form->display('bank_owner', 'Tên chủ tài khoản/Giao thức');

            $form->display('created_at', 'Thời gian tạo');
            $form->display('updated_at', 'Thời gian cập nhật');

            $form->disableDeleteButton();
            $form->disableViewButton();
        });
    }
}
