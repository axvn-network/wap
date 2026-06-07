<?php

namespace App\Admin\Actions\Grid\Recharge;

use App\Models\SystemConfig;
use App\Models\Users;
use App\Models\UserVip;
use App\Models\Activity;
use App\Models\ActivityApply;
use App\Models\UserOperateLog;
use App\Services\Lib;
use App\Services\GamereportService;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Recharge;
use App\Models\RedEnvelopes;
use App\Models\Userredpacket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\User;

class Pass extends RowAction
{
    /**
     * @return string
     */
	protected $title = 'Duyệt nạp';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $id = $this->getKey();
        
        return DB::transaction(function () use ($id, $request) {
            $model = Recharge::lockForUpdate()->find($id);
            if (!$model || $model->state != 1) {
                return $this->response()->error('Đơn nạp không tồn tại hoặc đã được xử lý');
            }

            $user = User::lockForUpdate()->find($model->user_id);
            if (!$user) {
                return $this->response()->error('Người dùng không tồn tại');
            }

            // Cập nhật số dư và tổng nạp
            $user->balance += $model->amount;
            $user->paysum += $model->amount;
            $user->save();

            // Cập nhật trạng thái đơn nạp
            $model->state = 2;
            $model->save();

            // Lấy thông tin IP
            $ip = $request->ip();
            $res = Lib::getIpAddress($ip);
            $res = json_decode($res, true);
            $ip_address = '';
            if ($res && isset($res['code']) && $res['code'] == 200) {
                $ip_address = ($res['data']['country'] ?? '') . ($res['data']['province'] ?? '') . ($res['data']['city'] ?? '');
            }

            // Ghi log vận hành (Hệ thống cũ)
            UserOperateLog::insertLog($user->id, 7, $request->header('User-Agent'), $ip, $ip_address, 'Admin duyệt nạp cho 【' . $user->username . '】. Số tiền: ' . $model->amount);

            // Ghi log backend chuẩn hóa
            Log::channel('backend')->info("Duyệt nạp tiền thành công", [
                'admin_id' => \Admin::user()->id ?? 0,
                'user_id' => $user->id,
                'username' => $user->username,
                'amount' => $model->amount,
                'order_no' => $model->order_no,
                'ip' => $ip
            ]);

            // Cập nhật báo cáo game (Kích hoạt lại mã đã comment)
            $Gamereport = new GamereportService();
            $reportData = [
                'uid' => $user->id,
                'pid' => $user->pid,
                'isagent' => $user->isagent,
                'totalrechange' => $model->amount,
            ];
            $Gamereport->add($reportData);

            // Thưởng thêm nếu có cấu hình
            $this->sendmoney($user, $model->amount);

            // Kiểm tra và phát hồng bao (Kích hoạt lại mã đã comment)
            $this->checkredbao($user, $model->amount);

            // Nâng cấp level hội viên
            $this->upuserlevel($model->user_id);

            return $this->response()->success('Duyệt nạp thành công')->refresh();
        });
    }

    /**
     * Tặng thêm tiền nạp theo cấu hình %
     */
    public function sendmoney($user, $money)
    {
        $recharge_fee = SystemConfig::getValue("recharge_fee");
        if ($recharge_fee && $recharge_fee > 0) {
            $amount = $money * $recharge_fee / 100;
            if ($amount > 0) {
                $user = User::find($user->id);
                $user->balance += $amount;
                $user->save();

                $order_no = 'TG' . $user->id . time() . rand(1000, 9999);
                Recharge::create([
                    'order_no' => $order_no,
                    'out_trade_no' => $order_no,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'cash_fee' => 0,
                    'real_money' => $amount,
                    'pay_way' => 10, // Nạp tặng kèm
                    'info' => 'Thưởng thêm ' . $recharge_fee . '% khi nạp tiền',
                    'state' => 2,
                ]);

                Log::channel('backend')->info("Thưởng thêm tiền nạp cho UID: {$user->id}", ['amount' => $amount, 'rate' => $recharge_fee]);
            }
        }
    }

    /**
     * Kiểm tra và phát hồng bao hoạt động
     */
    public function checkredbao($user, $money)
    {
        // Kiểm tra xem có đang tham gia hoạt động hồng bao không
        $hasActivity = ActivityApply::where('user_id', $user->id)->where('activity_id', 1)->where('status', 1)->exists();
        
        if ($hasActivity) {
            $redblist = RedEnvelopes::where('status', 1)->get();
            foreach ($redblist as $val) {
                if ($money >= $val->day_flow && $money <= $val->flow_money) {
                    $count = $this->getUserRedpacketNum($user, $val->id);
                    if ($count < $val->recharge) {
                        Userredpacket::create([
                            'uid' => $user->id,
                            'redpacketid' => $val->id,
                            'redpacketfee' => $val->money,
                            'money' => $money,
                            'redpacketmoney' => $money * $val->money / 100,
                            'status' => 0,
                        ]);
                        Log::channel('backend')->info("Tạo hồng bao chờ nhận cho UID: {$user->id}", ['redpacket_id' => $val->id]);
                    }
                }
            }
        }
    }

    public function getUserRedpacketNum($user, $redpacketid)
    {
        return Userredpacket::where(['uid' => $user->id, 'redpacketid' => $redpacketid])->count();
    }

	/**
	 * @return string|array|void
	 */
	public function confirm()
	{
		return ['Xác nhận duyệt nạp tiền?', 'Tiền sẽ được cộng trực tiếp vào tài khoản người dùng.'];
	}

    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }

    /**
     * @return array
     */
    protected function parameters()
    {
        return [];
    }

    /**
     * Nâng cấp level hội viên dựa trên tổng nạp và tổng cược
     */
    public function upuserlevel($uid)
    {
        $userinfo = Users::find($uid);
        if (!$userinfo) return;

        // Tìm cấp độ VIP cao nhất thỏa mãn điều kiện
        $uservip = UserVip::where('status', 1)
            ->where('recharge', '<=', $userinfo->paysum)
            ->where('flow', '<=', $userinfo->totalgame)
            ->orderBy('id', 'desc')
            ->first();

        if ($uservip && $uservip->id > $userinfo->vip) {
            $userinfo->vip = $uservip->id;
            $userinfo->save();
            Log::channel('backend')->info("Người dùng nâng cấp VIP", ['uid' => $uid, 'new_vip' => $uservip->id]);
        }
    }
}
