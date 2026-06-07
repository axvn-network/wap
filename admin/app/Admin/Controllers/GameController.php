<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Game;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class GameController extends AdminController
{
    protected $title = 'Quản lý trò chơi';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Game(), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('name', 'Tên');
            $grid->column('img', 'Hình ảnh')->image();
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
        return Show::make($id, new Game(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('img');
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
        return Form::make(new Game(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->image('img')->uniqueName()->required();
            $form->editor('content');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
