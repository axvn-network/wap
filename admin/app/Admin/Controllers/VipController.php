<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Vip;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class VipController extends AdminController
{
    protected $title = 'Cấp độ VIP';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Vip(), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('name', 'Tên');
            $grid->column('level', 'Cấp độ')->sortable();
            $grid->column('direct_drive', 'Hoa hồng trực tiếp (%)');
            $grid->column('second_level', 'Hoa hồng cấp 2 (%)');
            $grid->column('third_level', 'Hoa hồng cấp 3 (%)');
            $grid->column('created_at', 'Thời gian tạo')->sortable();
            $grid->column('updated_at', 'Thời gian cập nhật')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('name', 'Tên');
                $filter->equal('level', 'Cấp độ');
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
        return Show::make($id, new Vip(), function (Show $show) {
            $show->field('id', 'ID');
            $show->field('name', 'Tên');
            $show->field('level', 'Cấp độ');
            $show->field('direct_drive', 'Hoa hồng trực tiếp (%)');
            $show->field('second_level', 'Hoa hồng cấp 2 (%)');
            $show->field('third_level', 'Hoa hồng cấp 3 (%)');
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
        return Form::make(new Vip(), function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', 'Tên')->required();
            $form->number('level', 'Cấp độ')->required()->unique();
            $form->decimal('direct_drive', 'Hoa hồng trực tiếp (%)')->required();
            $form->decimal('second_level', 'Hoa hồng cấp 2 (%)')->required();
            $form->decimal('third_level', 'Hoa hồng cấp 3 (%)')->required();

            $form->display('created_at', 'Thời gian tạo');
            $form->display('updated_at', 'Thời gian cập nhật');
        });
    }
}
