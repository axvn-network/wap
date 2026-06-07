<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityApply;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function activityType()
    {
        $list = ActivityType::all();
        return $this->returnMsg(200, $list);
    }

    public function activityList(Request $request)
    {
        $type = $request->input('type');
        $list = Activity::when($type, function ($query) use ($type) {
            return $query->where('type', $type);
        })->select('id', 'title', 'type', 'entitle', 'apply_count', 'banner', 'can_apply', 'state', 'created_at')->orderBy('id', 'desc')->paginate(10);
        return $this->returnMsg(200, $list);
    }

    public function activitydeatil(Request $request)
    {
        $id = $request->input('id', 0);
        $activity = Activity::where('id', $id)->first();
        return $this->returnMsg(200, $activity);
    }

    public function doactivity(Request $request)
    {
        $user = Auth::user();
        $activity_id = $request->input('activityid');

        $activity = Activity::find($activity_id);
        if (!$activity) {
            return $this->returnMsg(202, '', '活动不存在');
        }

        $isapple = ActivityApply::where("user_id", $user->id)->where('activity_id', $activity_id)->first();
        if ($isapple) {
            $messages = [
                1 => '您已经申请过，等待管理员审核',
                2 => '您已经申请过，已审核通过',
                3 => '您已经申请过，审核未通过',
            ];
            return $this->returnMsg(202, '', $messages[$isapple->state] ?? '');
        }

        ActivityApply::create([
            'activity_id' => $activity_id,
            'user_id' => $user->id,
            'state' => 1,
        ]);

        return $this->returnMsg(200, '', '申请成功');
    }

    public function activityApplyLog(Request $request)
    {
        $user = Auth::user();
        $limit = $request->input('limit', 10);
        $list = ActivityApply::with('activity:id,title')->where("user_id", $user->id)->paginate($limit);

        return $this->returnMsg(200, $list);
    }
}
