<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Recharge;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Admin\Actions\Grid\Recharge\Pass;
use App\Admin\Actions\Grid\Recharge\Refuse;

class RechargeController extends AdminController
{
    // Bổ sung tiêu đề cho Controller
    protected $title = 'Quản lý nạp tiền';

    // Việt hóa toàn bộ phương thức thanh toán
    protected $pay_way = [
        1 => 'Chuyển khoản ngân hàng', 
        2 => 'Thanh toán ZGPAY', 
        3 => 'Quét mã Alipay', 
        4 => 'Quét mã WeChat', 
        5 => 'USDT-TRC20',
        6 => 'USDT-ERC20', 
        10 => 'Nạp tặng kèm',
        11 => 'Đại lý nạp'
    ];

    /**
     * Tạo giao diện danh sách.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Recharge::with(['user_data']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id', 'ID')->sortable();
            
            // Mở khóa các cột bị ẩn và thêm nhãn tiếng Việt
            $grid->column('order_no', 'Mã đơn hàng');
            $grid->column('out_trade_no', 'Mã GD ngoài');
            $grid->column('user_data.username', 'Tên người dùng')->view('admin.field.user_username');
            
            $grid->column('amount', 'Số tiền nạp');
            $grid->column('pay_way', 'Phương thức')->using($this->pay_way);
            $grid->column('usdt_type', 'Giao thức USDT'); // Mở khóa hiển thị giao thức USDT
            $grid->column('usdt_rate', 'Tỷ giá');
            $grid->column('real_money', 'Thực nhận');
            $grid->column('bank_no', 'STK / Địa chỉ ví'); // Mở khóa hiển thị thông tin STK/Ví
            
            // Tối ưu UI hiển thị trạng thái
            $grid->column('state', 'Trạng thái')
                 ->using([1 => 'Chờ duyệt', 2 => 'Đã hoàn thành', 3 => 'Đã từ chối'])
                 ->dot([1 => 'primary', 2 => 'success', 3 => 'danger']);
                 
            $grid->column('created_at', 'Thời gian tạo');

            // Kích hoạt tìm kiếm nhanh theo tên
            $grid->quickSearch(['user_data.username']);

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', 'ID');
                $filter->like('user_data.username', 'Tên người dùng'); // Đổi equal thành like để tìm kiếm tương đối
                $filter->between('created_at', 'Thời gian')->date();
                $filter->equal('pay_way', 'Phương thức')->select($this->pay_way);
            });
            
            // Bảo mật: Ẩn nút tự tạo đơn nạp thủ công trên danh sách
            $grid->disableCreateButton();
            
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                
                // Chỉ hiển thị Duyệt/Từ chối nếu đơn đang ở trạng thái Chờ duyệt
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
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Recharge(), function (Show $show) {
            $show->field('id', 'ID');
            $show->field('order_no', 'Mã đơn hàng');
            $show->field('out_trade_no', 'Mã GD ngoài');
            $show->field('user_id', 'ID Người dùng');
            $show->field('amount', 'Số tiền nạp');
            $show->field('cash_fee', 'Phí giao dịch');
            $show->field('real_money', 'Thực nhận');
            $show->field('pay_way', 'Phương thức nạp')->using($this->pay_way);
            
            $show->field('bank_no', 'STK / Địa chỉ ví');
            $show->field('bank', 'Ngân hàng / Mạng');
            $show->field('bank_address', 'Chi nhánh');
            $show->field('bank_owner', 'Người gửi / Tên ví');
            $show->field('info', 'Thông tin thêm');
            
            $show->field('state', 'Trạng thái')->using([1 => 'Chờ duyệt', 2 => 'Đã hoàn thành', 3 => 'Đã từ chối']);
            $show->field('created_at', 'Thời gian tạo');
            $show->field('updated_at', 'Thời gian cập nhật');

            $show->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
                    $tools->disableDelete();
                });
        });
    }

    /**
     * Tạo form thêm/sửa.
     * Chú ý: Vì đây là dữ liệu tài chính, toàn bộ form đã được khóa cứng (display) không cho sửa thủ công để đảm bảo bảo mật.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Recharge(), function (Form $form) {
            $form->display('id', 'ID');
            $form->display('order_no', 'Mã đơn hàng');
            $form->display('out_trade_no', 'Mã GD ngoài');
            $form->display('user_id', 'ID Người dùng');
            $form->display('amount', 'Số tiền nạp');
            $form->display('cash_fee', 'Phí giao dịch');
            $form->display('real_money', 'Thực nhận');
            
            // Dùng field select nhưng bị khóa (disable) để hiển thị nhãn đẹp hơn
            $form->select('pay_way', 'Phương thức nạp')->options($this->pay_way)->disable();
            
            $form->display('bank', 'Ngân hàng / Mạng');
            $form->display('bank_no', 'STK / Địa chỉ ví');
            $form->display('bank_address', 'Chi nhánh');
            $form->display('bank_owner', 'Người gửi / Tên ví');
            $form->display('info', 'Thông tin thêm');
            
            $form->select('state', 'Trạng thái')->options([1 => 'Chờ duyệt', 2 => 'Đã hoàn thành', 3 => 'Đã từ chối'])->disable();

            $form->display('created_at', 'Thời gian tạo');
            $form->display('updated_at', 'Thời gian cập nhật');

            // Khóa các công cụ không an toàn
            $form->disableViewButton();
            $form->disableDeleteButton();
        });
    }
}