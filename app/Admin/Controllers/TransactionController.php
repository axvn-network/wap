<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Transaction;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class TransactionController extends AdminController
{
    protected $title = 'Lịch sử giao dịch';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Transaction::with(['user']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user.username', 'Tên người dùng');
            $grid->column('type', 'Loại giao dịch')->using([
                1 => 'Nạp tiền',
                2 => 'Rút tiền',
                3 => 'Hoa hồng',
                4 => 'Mua hàng',
                5 => 'Hoàn tiền',
            ])->dot([
                1 => 'success',
                2 => 'primary',
                3 => 'info',
                4 => 'warning',
                5 => 'danger',
            ]);
            $grid->column('amount', 'Số tiền');
            $grid->column('balance_before', 'Số dư trước');
            $grid->column('balance_after', 'Số dư sau');
            $grid->column('created_at', 'Thời gian');

            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->disableEditButton();
            $grid->disableActions();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('user.username', 'Tên người dùng');
                $filter->in('type', 'Loại giao dịch')->multipleSelect([
                    1 => 'Nạp tiền',
                    2 => 'Rút tiền',
                    3 => 'Hoa hồng',
                    4 => 'Mua hàng',
                    5 => 'Hoàn tiền',
                ]);
            });
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
        return Show::make($id, Transaction::with(['user']), function (Show $show) {
            $show->field('id');
            $show->field('user.username', 'Tên người dùng');
            $show->field('type', 'Loại giao dịch')->using([
                1 => 'Nạp tiền',
                2 => 'Rút tiền',
                3 => 'Hoa hồng',
                4 => 'Mua hàng',
                5 => 'Hoàn tiền',
            ]);
            $show->field('amount', 'Số tiền');
            $show->field('balance_before', 'Số dư trước');
            $show->field('balance_after', 'Số dư sau');
            $show->field('created_at', 'Thời gian');
            $show->field('updated_at');

            $show->disableDeleteButton();
            $show->disableEditButton();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Transaction(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->select('type')->options([
                1 => 'Nạp tiền',
                2 => 'Rút tiền',
                3 => 'Hoa hồng',
                4 => 'Mua hàng',
                5 => 'Hoàn tiền',
            ]);
            $form->text('amount');
            $form->text('balance_before');
            $form->text('balance_after');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
