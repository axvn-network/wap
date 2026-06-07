<?php

namespace App\Admin\Forms;

use App\Models\User;
use Dcat\Admin\Widgets\Form;
use App\Services\TransactionService;
use Illuminate\Support\Facades\DB;

class Userbalance extends Form
{
    public function handle(array $input)
    {
        $id = $this->payload['id'] ?? null;
        $user = User::find($id);
        if (!$user) {
            return $this->error('Không tìm thấy người dùng');
        }

        $balance = $input['balance'] ?? 0;
        if ($balance == 0) {
            return $this->error('Số tiền điều chỉnh không hợp lệ');
        }

        $balanceSource = $input['balance_source'] ?? 'Điều chỉnh thủ công';

        DB::beginTransaction();
        try {
            $transactionService = new TransactionService();
            if ($balance > 0) {
                $transactionService->deposit($user, $balance, $balanceSource);
            } else {
                $transactionService->withdraw($user, abs($balance), $balanceSource);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Thao tác thất bại: ' . $e->getMessage());
        }

        return $this->success('Điều chỉnh số dư thành công');
    }

    public function form()
    {
        $this->confirm('Bạn có chắc chắn muốn điều chỉnh số dư không?');
        $this->decimal('balance', 'Số tiền điều chỉnh')->rules('required')->default(0.00)->help('Nhập số tiền cần điều chỉnh. Số dương để cộng, số âm để trừ.');
        $this->text('balance_source', 'Lý do điều chỉnh')->default('Quản trị viên điều chỉnh');
    }

    public function defaultVariables()
    {
        $id = $this->payload['id'] ?? null;
        $user = User::find($id);
        if ($user) {
            return [
                'balance' => 0.00,
                'balance_source' => 'Quản trị viên điều chỉnh',
            ];
        }
        return [];
    }
}
