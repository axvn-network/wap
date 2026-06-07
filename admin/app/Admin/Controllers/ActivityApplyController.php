<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\Activity\Pass;
use App\Admin\Actions\Grid\Activity\Refuse;
use App\Admin\Repositories\ActivityApply;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ActivityApplyController extends AdminController
{
    protected $title = 'Duyệt đơn đăng ký hoạt động';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(ActivityApply::with(['user_data', 'activity_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_data.username', 'Tên người dùng');
            $grid->column('activity_data.title', 'Tên hoạt động');
            $grid->column('state', 'Trạng thái')->using([
                1 => 'Đang chờ duyệt',
                2 => 'Đã duyệt',
                3 => 'Đã từ chối'
            ])->dot([
                1 => 'primary',
                2 => 'success',
                3 => 'danger'
            ]);
            $grid->column('remark', 'Ghi chú của quản trị viên');
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
            $grid->disableEditButton();
            $grid->disableDeleteButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
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
        return Show::make($id, ActivityApply::with(['user_data', 'activity_data']), function (Show $show) {
            $show->field('id');
            $show->field('user_data.username', 'Tên người dùng');
            $show->field('activity_data.title', 'Tên hoạt động');
            $show->field('state', 'Trạng thái')->using([
                1 => 'Đang chờ duyệt',
                2 => 'Đã duyệt',
                3 => 'Đã từ chối'
            ]);
            $show->field('remark', 'Ghi chú của quản trị viên');
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
        return Form::make(new ActivityApply(), function (Form $form) {
            $form->display('id');
            $form->display('uid', 'ID Người dùng');
            $form->display('activityid', 'ID Hoạt động');
            $form->display('state', 'Trạng thái')->with(function ($value) {
                $states = [1 => 'Đang chờ duyệt', 2 => 'Đã duyệt', 3 => 'Đã từ chối'];
                return $states[$value] ?? 'Không xác định';
            });
            $form->textarea('remark', 'Ghi chú của quản trị viên');

            $form->disableDeleteButton();
            $form->disableViewButton();
        });
    }
}
