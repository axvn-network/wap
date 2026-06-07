<?php

namespace App\Admin\Actions\Grid\Recharge;

use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Recharge;
use Illuminate\Support\Facades\Log;

class Refuse extends RowAction
{
    /**
     * @return string
     */
	protected $title = 'Từ chối';

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
        $model = Recharge::find($id);
        
        if (!$model || $model->state != 1) {
            return $this->response()->error('Đơn nạp không tồn tại hoặc đã được xử lý');
        }

        $model->state = 3; // Đã từ chối
        $model->save();

        Log::channel('backend')->warning("Từ chối đơn nạp tiền", [
            'admin_id' => \Admin::user()->id ?? 0,
            'user_id' => $model->user_id,
            'amount' => $model->amount,
            'order_no' => $model->order_no,
            'ip' => $request->ip()
        ]);

        return $this->response()->success('Đã từ chối đơn nạp')->refresh();
    }

    /**
	 * @return string|array|void
	 */
	public function confirm()
	{
		return ['Xác nhận từ chối đơn nạp?', 'Hành động này không thể hoàn tác.'];
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
}
