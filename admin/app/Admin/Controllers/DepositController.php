<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\Deposit\Pass;
use App\Admin\Actions\Grid\Deposit\Refuse;
use App\Admin\Repositories\Deposit;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class DepositController extends AdminController
{
    protected $title = 'Duyệt đơn nạp tiền';

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Deposit::with(['user_data', 'channel_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('user_data.username', 'Tên người dùng');
            $grid->column('channel_data.channel_name', 'Kênh');
            $grid->column('orderno', 'Mã đơn hàng');
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
     * Tạo giao diện xem chi tiết.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Deposit(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('channel_id');
            $show->field('orderno');
            $show->field('money');
            $show->field('state');
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
        return Form::make(new Deposit(), function (Form $form) {
            $form->display('id');
            $form->display('user_id');
            $form->display('channel_id');
            $form->display('orderno');
            $form->display('money');
            $form->display('state');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
