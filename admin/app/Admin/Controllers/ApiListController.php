<?php

namespace App\Admin\Controllers;

use App\Models\GameList;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ApiListController extends AdminController
{
    protected $title = 'Danh sách API trò chơi';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(GameList::with(['platform_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('platform_data.platformname', 'Tên nền tảng');
            $grid->column('game_name_cn', 'Tên tiếng Trung');
            $grid->column('game_name_en', 'Tên tiếng Anh');
            $grid->column('game_name_vi', 'Tên tiếng Việt');
            $grid->column('game_code', 'Mã trò chơi');
            $grid->column('game_type', 'Loại trò chơi');
            $grid->column('state', 'Trạng thái')->switch();
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('game_name_cn', 'Tên tiếng Trung');
                $filter->like('game_name_en', 'Tên tiếng Anh');
                $filter->like('game_name_vi', 'Tên tiếng Việt');
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
        return Show::make($id, new GameList(), function (Show $show) {
            $show->field('id');
            $show->field('api_id');
            $show->field('game_name_cn');
            $show->field('game_name_en');
            $show->field('game_name_vi');
            $show->field('game_code');
            $show->field('game_type');
            $show->field('state');
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
        return Form::make(new GameList(), function (Form $form) {
            $form->display('id');
            $form->text('game_name_vi', 'Tên tiếng Việt');
            $form->switch('state', 'Trạng thái');
        });
    }
}
