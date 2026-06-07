<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\User\DownUser;
use App\Admin\Actions\Grid\User\Recharge;
use App\Admin\Actions\Grid\User\SendMessage;
use App\Admin\Actions\Grid\User\UpUser;
use App\Admin\Repositories\Users;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class UsersController extends AdminController
{
    protected $title = 'Quản lý người dùng';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Users::with(['inviter']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('username', 'Tên đăng nhập');
            $grid->column('phone', 'Số điện thoại');
            $grid->column('money', 'Số dư');
            $grid->column('inviter.username', 'Người mời');
            $grid->column('created_at', 'Thời gian tạo');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('username', 'Tên đăng nhập');
                $filter->like('phone', 'Số điện thoại');
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new Recharge());
                $actions->append(new SendMessage());
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
        return Show::make($id, Users::with(['inviter']), function (Show $show) {
            $show->field('id');
            $show->field('username', 'Tên đăng nhập');
            $show->field('phone', 'Số điện thoại');
            $show->field('money', 'Số dư');
            $show->field('inviter.username', 'Người mời');
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
        return Form::make(new Users(), function (Form $form) {
            $form->display('id');
            $form->text('username', 'Tên đăng nhập')->required();
            $form->text('phone', 'Số điện thoại');
            $form->password('password', 'Mật khẩu')->required();

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
