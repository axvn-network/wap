<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\GameHistory;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class GameHistoryController extends AdminController
{
    protected $title = 'Lịch sử trò chơi';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(GameHistory::with(['user_data', 'platform_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_data.username', 'Tên người dùng');
            $grid->column('platform_data.platformname', 'Nền tảng');
            $grid->column('game_name', 'Tên trò chơi');
            $grid->column('bet', 'Tiền cược');
            $grid->column('prize', 'Tiền thưởng');
            $grid->column('created_at', 'Thời gian tạo');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('user_data.username', 'Tên người dùng');
            });

            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->disableEditButton();
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
        return Show::make($id, new GameHistory(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('api_id');
            $show->field('game_name');
            $show->field('bet');
            $show->field('prize');
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
        return Form::make(new GameHistory(), function (Form $form) {
            $form->display('id');
            $form->display('user_id');
            $form->display('api_id');
            $form->display('game_name');
            $form->display('bet');
            $form->display('prize');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
