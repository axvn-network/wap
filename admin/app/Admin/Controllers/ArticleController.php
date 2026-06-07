<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Article;
use App\Models\Articlescate;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ArticleController extends AdminController
{
    protected $title = 'Quản lý bài viết';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Article(), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('title', 'Tiêu đề');
            $grid->column('cate_id', 'Danh mục')->display(function ($cateId) {
                return Articlescate::where('id', $cateId)->value('title');
            });
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
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
        return Show::make($id, new Article(), function (Show $show) {
            $show->field('id');
            $show->field('title', 'Tiêu đề');
            $show->field('content', 'Nội dung');
            $show->field('cate_id', 'Danh mục');
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
        return Form::make(new Article(), function (Form $form) {
            $form->display('id');
            $form->text('title', 'Tiêu đề')->required();
            $form->select('cate_id', 'Danh mục')->options(Articlescate::pluck('title', 'id'))->required();
            $form->editor('content', 'Nội dung');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
