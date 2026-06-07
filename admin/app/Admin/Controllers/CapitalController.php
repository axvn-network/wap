<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Capital;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\User;

class CapitalController extends AdminController
{
    protected $title = 'Quản lý vốn';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Capital::with(['user_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_data.username', 'Tên người dùng');
            $grid->column('action', 'Hành động')->using([
                'recharge' => 'Nạp tiền',
                'withdrawal' => 'Rút tiền',
                'send' => 'Gửi đi',
                'receive' => 'Nhận',
            ])->label([
                'recharge' => 'primary',
                'withdrawal' => 'success',
                'send' => 'danger',
                'receive' => 'info',
            ]);
            $grid->column('amount', 'Số tiền');
            $grid->column('remark', 'Ghi chú');
            $grid->column('created_at', 'Thời gian tạo');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('user_data.username', 'Tên người dùng');
                $filter->equal('action', 'Hành động')->select([
                    'recharge' => 'Nạp tiền',
                    'withdrawal' => 'Rút tiền',
                    'send' => 'Gửi đi',
                    'receive' => 'Nhận',
                ]);
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
        return Show::make($id, new Capital(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('action');
            $show->field('amount');
            $show->field('remark');
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
        return Form::make(new Capital(), function (Form $form) {
            $form->display('id');
            $form->display('user_id');
            $form->display('action');
            $form->display('amount');
            $form->display('remark');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
