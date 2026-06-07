<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\Withdrawal\Pass;
use App\Admin\Actions\Grid\Withdrawal\Refuse;
use App\Admin\Repositories\Withdrawal;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class WithdrawalController extends AdminController
{
    protected $title = 'Duyệt đơn rút tiền';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Withdrawal::with(['user_data', 'bank_card_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_data.username', 'Tên người dùng');
            $grid->column('bank_card_data.bank_name', 'Ngân hàng');
            $grid->column('bank_card_data.bank_account', 'Tài khoản ngân hàng');
            $grid->column('bank_card_data.bank_user', 'Chủ tài khoản');
            $grid->column('money', 'Số tiền');
            $grid->column('state', 'Trạng thái')->using([
                1 => 'Đang chờ duyệt',
                2 => 'Đã duyệt',
                3 => 'Đã từ chối'
            ])->dot([
                1 => 'primary',
                2 => 'success',
                3 => 'danger'
            ]);
            $grid->column('created_at', 'Thời gian tạo');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('user_data.username', 'Tên người dùng');
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($actions->row->state == 1) {
                    $actions->append(new Pass());
                    $actions->append(new Refuse());
                }
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
        return Show::make($id, new Withdrawal(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('bank_card_id');
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
        return Form::make(new Withdrawal(), function (Form $form) {
            $form->display('id');
            $form->display('user_id');
            $form->display('bank_card_id');
            $form->display('money');
            $form->display('state');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
