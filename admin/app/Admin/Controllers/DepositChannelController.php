<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\DepositChannel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class DepositChannelController extends AdminController
{
    protected $title = 'Kênh nạp tiền';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new DepositChannel(), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('channel_name', 'Tên kênh');
            $grid->column('channel_code', 'Mã kênh');
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('channel_name', 'Tên kênh');
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
        return Show::make($id, new DepositChannel(), function (Show $show) {
            $show->field('id', 'ID');
            $show->field('channel_name', 'Tên kênh');
            $show->field('channel_code', 'Mã kênh');
            $show->field('created_at', 'Thời gian tạo');
            $show->field('updated_at', 'Thời gian cập nhật');
        });
    }

    /**
     * Tạo form thêm/sửa.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new DepositChannel(), function (Form $form) {
            $form->display('id', 'ID');
            $form->text('channel_name', 'Tên kênh')->required();
            $form->text('channel_code', 'Mã kênh')->required();

            $form->display('created_at', 'Thời gian tạo');
            $form->display('updated_at', 'Thời gian cập nhật');
        });
    }
}
