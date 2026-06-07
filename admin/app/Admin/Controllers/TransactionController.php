<?php

namespace App\Admin\Controllers;

use App\Models\Transaction;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class TransactionController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Transaction(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id', 'ID Người dùng');
            $grid->column('type', 'Loại')->label([
                'deposit' => 'Nạp tiền',
                'withdrawal' => 'Rút tiền',
                'commission' => 'Hoa hồng',
                'refund' => 'Hoàn trả',
            ]);
            $grid->column('amount', 'Số tiền')->sortable();
            $grid->column('before_balance', 'Số dư trước');
            $grid->column('after_balance', 'Số dư sau');
            $grid->column('description', 'Mô tả');
            $grid->column('created_at', 'Thời gian tạo')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id', 'ID Người dùng');
                $filter->equal('type', 'Loại')->select([
                    'deposit' => 'Nạp tiền',
                    'withdrawal' => 'Rút tiền',
                    'commission' => 'Hoa hồng',
                    'refund' => 'Hoàn trả',
                ]);
            });

            $grid->disableCreateButton();
            $grid->disableActions();
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
        return Show::make($id, new Transaction(), function (Show $show) {
            $show->field('id');
            $show->field('user_id', 'ID Người dùng');
            $show->field('type', 'Loại');
            $show->field('amount', 'Số tiền');
            $show->field('before_balance', 'Số dư trước');
            $show->field('after_balance', 'Số dư sau');
            $show->field('description', 'Mô tả');
            $show->field('created_at', 'Thời gian tạo');
            $show->field('updated_at', 'Thời gian cập nhật');
        });
    }
}
