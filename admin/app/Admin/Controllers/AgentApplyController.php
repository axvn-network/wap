<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\AgentApply\Pass;
use App\Admin\Actions\Grid\AgentApply\Refuse;
use App\Admin\Repositories\AgentApply;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class AgentApplyController extends AdminController
{
    protected $title = 'Duyệt đơn đăng ký đại lý';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(AgentApply::with(['user_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_data.username', 'Tên người dùng');
            $grid->column('username', 'Tên tài khoản');
            $grid->column('mobile', 'Số điện thoại');
            $grid->column('state', 'Trạng thái')->using([
                1 => 'Đang chờ duyệt',
                2 => 'Đã duyệt',
                3 => 'Đã từ chối'
            ])->dot([
                1 => 'primary',
                2 => 'success',
                3 => 'danger'
            ]);
            $grid->column('remark', 'Ghi chú');
            $grid->column('created_at', 'Thời gian đăng ký');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('user_data.username', 'Tên người dùng');
                $filter->equal('state', 'Trạng thái')->select([
                    1 => 'Đang chờ duyệt',
                    2 => 'Đã duyệt',
                    3 => 'Đã từ chối'
                ]);
            });

            $grid->disableCreateButton();
            $grid->disableDeleteButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit();

                if ($actions->row->state == 1) {
                    $actions->append(new Pass());
                    $actions->append(new Refuse());
                }
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
        return Show::make($id, new AgentApply(), function (Show $show) {
            $show->field('id');
            $show->field('username', 'Tên tài khoản');
            $show->field('mobile', 'Số điện thoại');
            $show->field('state', 'Trạng thái')->using([
                1 => 'Đang chờ duyệt',
                2 => 'Đã duyệt',
                3 => 'Đã từ chối'
            ]);
            $show->field('remark', 'Ghi chú');
            $show->field('created_at', 'Thời gian đăng ký');
            $show->field('updated_at', 'Thời gian cập nhật');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new AgentApply(), function (Form $form) {
            $form->display('id', 'ID');
            $form->display('username', 'Tên tài khoản');
            $form->display('mobile', 'Số điện thoại');
            $form->select('state', 'Trạng thái')->options([
                1 => 'Đang chờ duyệt',
                2 => 'Đã duyệt',
                3 => 'Đã từ chối'
            ]);
            $form->textarea('remark', 'Ghi chú');
        });
    }
}
