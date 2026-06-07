<?php

namespace App\Services;

use App\Models\Gamereport;
use Illuminate\Support\Facades\Log;

class GamereportService
{
    /**
     * Cập nhật hoặc tạo mới báo cáo trò chơi theo ngày cho người dùng
     * 
     * @param array $data
     * @return void
     */
    public function add($data)
    {
        // Chuẩn hóa thời gian theo ngày (00:00:00) để báo cáo theo ngày
        $addtime = strtotime(date('Y-m-d'));
        
        // Tìm bản ghi báo cáo của người dùng trong ngày hiện tại
        $info = Gamereport::where("uid", $data['uid'])
            ->where('addtime', $addtime)
            ->first();

        try {
            if ($info) {
                // Cập nhật bản ghi hiện có
                if (isset($data['recnum'])) {
                    $info->recnum += 1;
                }
                
                if (isset($data['totalrechange'])) {
                    $info->rechangenum += 1;
                    $info->totalrechange += $data['totalrechange'];
                }
                
                if (isset($data['totalwithdraw'])) {
                    $info->withdrawnum += 1;
                    $info->totalwithdraw += $data['totalwithdraw'];
                }
                
                if (isset($data['totalbet'])) {
                    $info->betnum += 1;
                    $info->totalbet += $data['totalbet'];
                    $info->totalvalidamount += ($data['totalvalidamount'] ?? $data['valid_amount'] ?? 0);
                }
                
                if (isset($data['totalwinloss'])) {
                    $info->totalwinloss += $data['totalwinloss'];
                }
                
                if (isset($data['totalredpackect'])) {
                    $info->redpackectnum += 1;
                    $info->totalredpackect += $data['totalredpackect'];
                }
                
                if (isset($data['releasewater'])) {
                    $info->releasewater += $data['releasewater'];
                    $info->releasewaternum += 1;
                }
                
                if (isset($data['rakeback'])) {
                    $info->rakeback += $data['rakeback'];
                    $info->rakebacknum += 1;
                }
                
                $info->save();
                Log::channel('backend')->info("Cập nhật báo cáo game cho UID: {$data['uid']}", $data);
            } else {
                // Tạo mới bản ghi cho ngày mới
                $data['addtime'] = $addtime;
                $data['created_at'] = date('Y-m-d H:i:s');

                if (isset($data['totalrechange'])) {
                    $data['rechangenum'] = 1;
                }
                
                if (isset($data['totalwithdraw'])) {
                    $data['withdrawnum'] = 1;
                }
                
                if (isset($data['totalbet'])) {
                    $data['betnum'] = 1;
                    $data['totalvalidamount'] = ($data['totalvalidamount'] ?? $data['valid_amount'] ?? 0);
                }

                if (isset($data['totalredpackect'])) {
                    $data['redpackectnum'] = 1;
                }

                if (isset($data['releasewater'])) {
                    $data['releasewaternum'] = 1;
                }

                if (isset($data['rakeback'])) {
                    $data['rakebacknum'] = 1;
                }

                unset($data['handle']);
                unset($data['valid_amount']); // Sử dụng totalvalidamount theo database
                
                Gamereport::create($data);
                Log::channel('backend')->info("Tạo mới báo cáo game cho UID: {$data['uid']}", $data);
            }
        } catch (\Exception $e) {
            Log::channel('backend')->error("Lỗi khi cập nhật báo cáo game cho UID: {$data['uid']}. Lỗi: " . $e->getMessage());
        }
    }
}
