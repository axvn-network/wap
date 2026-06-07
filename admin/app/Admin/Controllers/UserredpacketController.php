<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Userredpacket;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class UserredpacketController extends AdminController
{
    protected $title = "Danh sách nhận lì xì của thành viên";

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Userredpacket::with(['user_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_data.username', 'Tên người dùng');
            $grid->column('redpacketfee', 'Tỷ lệ lì xì');
            $grid->column('money', 'Số tiền nạp');
            $grid->column('redpacketmoney', 'Số tiền lì xì');
            $grid->column('status', 'Trạng thái')->using([0 => 'Chờ nhận', 1 => 'Đã nhận'])->dot([0 => 'danger', 1 => 'success']);
            $grid->column('usetime', 'Thời gian nhận')->display(function ($usetime) {
                return $usetime ? date('Y-m-d H:i:s', $usetime) : 'Chưa nhận';
            });
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('user_data.username', 'Tên người dùng');
                $filter->equal('status')->select([0 => 'Chờ nhận', 1 => 'Đã nhận']);
            });

            $grid->disableCreateButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableDelete();
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
        return Show::make($id, Userredpacket::with(['user_data']), function (Show $show) {
            $show->field('id');
            $show->field('user_data.username', 'Tên người dùng');
            $show->field('redpacketid', 'ID Lì xì');
            $show->field('redpacketfee', 'Tỷ lệ lì xì');
            $show->field('money', 'Số tiền nạp');
            $show->field('redpacketmoney', 'Số tiền lì xì');
            $show->field('status', 'Trạng thái')->using([0 => 'Chờ nhận', 1 => 'Đã nhận']);
            $show->field('usetime', 'Thời gian nhận')->as(function ($usetime) {
                return $usetime ? date('Y-m-d H:i:s', $usetime) : 'Chưa nhận';
            });
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
        return Form::make(new Userredpacket(), function (Form $form) {
            $form->display('id');
            $form->display('uid', 'ID Người dùng');
            $form->display('redpacketid', 'ID Lì xì');
            $form->display('redpacketfee', 'Tỷ lệ lì xì');
            $form->display('money', 'Số tiền nạp');
            $form->display('redpacketmoney', 'Số tiền lì xì');
            $form->select('status', 'Trạng thái')->options([0 => 'Chờ nhận', 1 => 'Đã nhận']);
            $form->datetime('usetime', 'Thời gian nhận');

            $form->display('created_at');
            $form->display('updated_at');

            $form->disableViewButton();
            $form->disableDeleteButton();
        });
    }
}
