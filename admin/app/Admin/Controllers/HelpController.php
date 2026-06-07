<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Help;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class HelpController extends AdminController
{
    protected $title = 'Trung tâm trợ giúp';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Help(), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('title', 'Tiêu đề');
            $grid->column('content', 'Nội dung');
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
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
        return Show::make($id, new Help(), function (Show $show) {
            $show->field('id');
            $show->field('title');
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
        return Form::make(new Help(), function (Form $form) {
            $form->display('id');
            $form->text('title')->required();
            $form->editor('content');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
