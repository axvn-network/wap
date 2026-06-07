<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Message;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class MessageController extends AdminController
{
    protected $title = 'Tin nhắn';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Message::with(['user']), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('user.username', 'Tên người dùng');
            $grid->column('content', 'Nội dung');
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('user.username', 'Tên người dùng');
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
        return Show::make($id, new Message(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('content');
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
        return Form::make(new Message(), function (Form $form) {
            $form->display('id');
            $form->text('user_id')->required();
            $form->textarea('content')->required();

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
