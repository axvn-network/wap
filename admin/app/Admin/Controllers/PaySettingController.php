<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\PaySetting;
use App\Models\Bank;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PaySettingController extends AdminController
{
    protected $title = 'Cấu hình thanh toán';
    protected $state = [1 => 'Bật', 0 => 'Tắt'];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new PaySetting(['bank_data']), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('bank_data.bank_name', 'Ngân hàng');
            $grid->column('bank_no', 'Số tài khoản');
            $grid->column('bank_owner', 'Chủ tài khoản');
            $grid->column('bank_address', 'Chi nhánh');
            $grid->column('info', 'Ghi chú');
            $grid->column('state', 'Trạng thái')->using($this->state)->label([
                1 => 'success',
                0 => 'danger',
            ]);
            $grid->column('created_at', 'Thời gian tạo');
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', 'ID');
                $filter->equal('bank_id', 'Ngân hàng')->select(Bank::pluck('bank_name', 'id'));
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new PaySetting(), function (Form $form) {
            $list = Bank::orderBy('order', 'desc')->pluck('bank_name', 'id');
            
            $form->display('id', 'ID');
            $form->select('bank_id', 'Ngân hàng')->options($list)->required();
            $form->text('bank_no', 'Số tài khoản')->required();
            $form->text('bank_owner', 'Chủ tài khoản')->required();
            $form->text('bank_address', 'Chi nhánh')->required();
            $form->text('info', 'Ghi chú');
            $form->radio('state', 'Trạng thái')->options($this->state)->default(1);
        
            $form->display('created_at', 'Thời gian tạo');
            $form->display('updated_at', 'Thời gian cập nhật');

            $form->disableDeleteButton();
        });
    }
}
