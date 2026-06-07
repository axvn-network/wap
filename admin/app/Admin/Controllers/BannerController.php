<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Banner;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class BannerController extends AdminController
{
    protected $title = 'Quản lý banner';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Banner(), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('img', 'Hình ảnh')->image();
            $grid->column('url', 'URL');
            $grid->column('sort', 'Thứ tự')->sortable();
            $grid->column('created_at', 'Thời gian tạo');
            $grid->column('updated_at', 'Thời gian cập nhật')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
        });
    }

    /**
     * Tạo giao diện chi tiết.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Banner(), function (Show $show) {
            $show->field('id', 'ID');
            $show->field('img', 'Hình ảnh')->image();
            $show->field('url', 'URL');
            $show->field('sort', 'Thứ tự');
            $show->field('created_at', 'Thời gian tạo');
            $show->field('updated_at', 'Thời gian cập nhật');
        });
    }

    /**
     * Tạo form.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Banner(), function (Form $form) {
            $form->display('id', 'ID');
            $form->image('img', 'Hình ảnh')->required()->uniqueName();
            $form->url('url', 'URL')->required();
            $form->number('sort', 'Thứ tự')->default(0);

            $form->display('created_at', 'Thời gian tạo');
            $form->display('updated_at', 'Thời gian cập nhật');
        });
    }
}
