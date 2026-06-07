<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\AgentSettlement;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class AgentSettlementController extends AdminController
{
    protected $title = 'Quyết toán hoa hồng đại lý';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(AgentSettlement::with(['user_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_data.username', 'Tên người dùng');
            $grid->column('money', 'Số tiền');
            $grid->column('state', 'Trạng thái')->using([
                1 => 'Đang chờ duyệt',
                2 => 'Đã duyệt',
                3 => 'Đã từ chối'
            ])->dot([
                1 => 'primary',
                2 => 'success',
                3 => 'danger'
            ]);
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
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new AgentSettlement(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('money');
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
        return Form::make(new AgentSettlement(), function (Form $form) {
            $form->display('id');
            $form->display('user_id');
            $form->display('money');
            $form->display('state');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
