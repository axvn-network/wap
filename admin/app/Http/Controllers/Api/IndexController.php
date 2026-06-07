<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetGameUrlRequest;
use App\Http\Requests\Api\TransRecordRequest;
use App\Models\Activity;
use App\Models\ActivityApply;
use App\Models\ActivityType;
use App\Models\Bank;
use App\Models\Api;
use App\Models\User_Api;
use App\Models\Message;
use App\Models\UserMessage;
use App\Models\PaySetting;
use App\Models\SystemConfig;
use App\Models\UserCard;
use App\Models\User;
use App\Models\Usersmoney;
use App\Services\TgService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TransferLog;
use App\Models\Recharge;
use App\Models\Withdraw;
use App\Models\Article;
use App\Models\UserVip;
use App\Models\Banner;
use App\Models\GameRecord;
use App\Models\AgentApply;
use App\Models\GameList;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    protected $tgService;

    public function __construct(TgService $tgService)
    {
        $this->tgService = $tgService;
    }

    public function credit(Request $request)
    {
        $api_code = $request->input('api_code');
        $data = $this->tgService->credit($api_code);
        return $data;
    }

    public function bannerList(Request $request)
    {
        $type = $request->input('type', 2);
        $bannerlist = Banner::where('type', $type)
            ->select("pic as src", "jump_url")
            ->get()
            ->map(function ($banner) {
                return [
                    "src" => config('app.url') . '/uploads/' . $banner->src,
                    "background" => "#f4f6ff",
                    'url' => $banner->jump_url
                ];
            });

        return $this->returnMsg(200, $bannerlist);
    }

    public function article(Request $request)
    {
        $type = $request->input('type');
        $data = Article::where('cateid', $type)->first();
        return $this->returnMsg(200, $data);
    }

    public function Systemstatus()
    {
        $isclose = SystemConfig::query()->find("isclose");
        $data = [];
        if ($isclose['value']) {
            $webcontent = SystemConfig::query()->find("webcontent");
            $data['content'] = $webcontent['value'];
            $data['isclose'] = 1;
        } else {
            $data['content'] = '';
            $data['isclose'] = 0;
        }
        return $this->returnMsg(200, $data);
    }

    public function uservip()
    {
        $vip = UserVip::get();
        return $this->returnMsg(200, $vip);
    }

    public function homenotice()
    {
        $notice = Article::where('cateid', 6)->limit(3)->pluck("name");
        return $this->returnMsg(200, $notice);
    }

    public function homecontent()
    {
        $notice = Article::where('cateid', '<', 6)->get();
        return $this->returnMsg(200, $notice);
    }

    public function homenoticelist(Request $request)
    {
        $notice = Article::where('cateid', 6)->paginate(10);
        return $this->returnMsg(200, $notice);
    }

    public function homenoticedeatil(Request $request)
    {
        $data = $request->all();
        $notice = Article::where('id', $data['id'])->first();
        return $this->returnMsg(200, $notice);
    }

    public function cateList()
    {
        $list = [
            ["id" => 1, "pid" => 0, "name" => "电子游艺", "enname" => "concise"],
            ["id" => 2, "pid" => 0, "name" => "棋牌游戏", "enname" => "joker"],
            ["id" => 3, "pid" => 0, "name" => "视讯直播", "enname" => "realbet"],
            ["id" => 4, "pid" => 0, "name" => "彩票游戏", "enname" => "lottery"],
            ["id" => 5, "pid" => 0, "name" => "电竞游戏", "enname" => "gaming"],
            ["id" => 6, "pid" => 0, "name" => "体育赛事", "enname" => "sport"],
        ];
        return $this->returnMsg(200, $list);
    }

    public function noticeList(Request $request)
    {
        $limit = $request->input('limit', 10);
        $list = Message::orderBy('id', 'desc')->paginate($limit);
        return $this->returnMsg(200, $list);
    }

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

    public function getServicerUrl()
    {
        $url = SystemConfig::getValue('kf_url');
        return $this->returnMsg(200, ['url' => $url, 'domain' => config('app.url')]);
    }

    public function getGameList(Request $request)
    {
        $platform = $request->input('platform_name');
        $category = $request->input('game_type');

        $gamelist = GameList::when($platform, function ($query) use ($platform) {
            return $query->where('platform_name', $platform);
        })
            ->when($category, function ($query) use ($category) {
                return $query->where('category_id', $category);
            })
            ->where('is_top', 1)
            ->where('app_state', 1)
            ->select('name', 'game_code as gamecode', 'category_id', 'game_code', 'app_state')
            ->orderBy('order_by', 'desc')
            ->get()
            ->pluck('app_state', 'gamecode')
            ->toArray();

        $game_type = $request->input('game_type');
        $res = $this->tgService->gametypelist($game_type);

        $gamelist1 = collect($res['data'])->filter(function ($vals) use ($gamelist) {
            return (isset($gamelist[$vals['gamecode']]) && $gamelist[$vals['gamecode']]) || in_array($vals['gamecode'], ['ae', 'fgdz', 'pp', 'obgdy']);
        })->values()->all();

        return $this->returnMsg(200, $gamelist1);
    }

    public function getGameUrl(GetGameUrlRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        $this->registerApiIfNecessary($data['plat_name'], $user);

        if ($user->transferstatus == 1) {
            $mz = $this->allmz($data['plat_name'], $user->id);
            if ($mz['code'] != 200) {
                return $this->returnMsg(500, [], $mz['message']);
            }
        }

        $res = $this->tgService->login($user->username, $data['plat_name'], $this->getGameTypeForLogin($data['game_code']), $data['is_mobile_url'] ?? 1, $data['game_type']);

        if ($res['code'] == 200) {
            return $this->returnMsg(200, ['url' => $res['data']]);
        }

        return $this->returnMsg(500, $res, $res['message']);
    }

    private function registerApiIfNecessary($apiCode, $user)
    {
        $userApi = User_Api::where('api_code', $apiCode)->where('user_id', $user->id)->first();

        if (!$userApi) {
            $result = $this->tgService->register($apiCode, $user->username);
            if ($result['code'] != 200) {
                return $this->returnMsg(201, '', $result['message']);
            }

            User_Api::create([
                'user_id' => $user->id,
                'api_user' => $user->username,
                'api_pass' => '123456',
                'api_code' => $apiCode,
            ]);
        }
    }

    private function getGameTypeForLogin($gameType)
    {
        $gameTypes = [
            'sport' => '5',
            'concise' => '3',
            'gaming' => '7',
            'joker' => '6',
            'lottery' => '4',
        ];

        return $gameTypes[$gameType] ?? '1';
    }

    private function allmz($plat_name, $userId)
    {
        return DB::transaction(function () use ($plat_name, $userId) {
            $user = User::lockForUpdate()->find($userId);

            $this->transferFromPreviousGame($plat_name, $user);
            $this->transferToNewGame($plat_name, $user);

            return ['code' => 200, 'message' => '成功'];
        });
    }

    private function transferFromPreviousGame($plat_name, $user)
    {
        $transferLog = TransferLog::where('transfer_type', 0)->where('user_id', $user->id)->latest()->first();

        if ($transferLog && $transferLog->api_type != $plat_name) {
            $result = $this->tgService->balance($transferLog->api_type, $user->username);
            if ($result['code'] != 200) {
                throw new \Exception($result['message']);
            }

            $api_money = (int)$result['data'];

            if ($api_money >= 1) {
                $this->withdrawFromGame($user, $transferLog->api_type, $api_money);
            }
        }
    }

    private function withdrawFromGame($user, $api_type, $api_money)
    {
        $client_transfer_id = time() . $user->id . rand(100000, 999999);
        $res = $this->tgService->withdrawal($user->username, $api_money, $client_transfer_id, $api_type);
        if ($res['code'] != 200) {
            throw new \Exception($res['message']);
        }

        $user->increment('balance', $api_money);
        $this->createTransferLog($client_transfer_id, $api_type, $user, 1, $api_money, 1);

        $user_api = User_Api::where('api_code', $api_type)->where('user_id', $user->id)->first();
        if ($user_api) {
            $user_api->api_money = max(0, $user_api->api_money - $api_money);
            $user_api->save();
        }
    }

    private function transferToNewGame($plat_name, $user)
    {
        $balance = (int)$user->balance;
        if ($balance >= 1) {
            $client_transfer_id = time() . $user->id . rand(100000, 999999);
            $res = $this->tgService->deposit($user->username, $balance, $client_transfer_id, $plat_name);

            if ($res['code'] != 200) {
                throw new \Exception($res['message']);
            }

            $user->decrement('balance', $balance);
            $this->createTransferLog($client_transfer_id, $plat_name, $user, 0, -$balance, 1);

            $user_api = User_Api::where('api_code', $plat_name)->where('user_id', $user->id)->first();
            if ($user_api) {
                $user_api->increment('api_money', $balance);
            }
        }
    }

    private function createTransferLog($order_no, $api_type, $user, $transfer_type, $money, $state)
    {
        TransferLog::create([
            'order_no' => $order_no,
            'api_type' => $api_type,
            'user_id' => $user->id,
            'transfer_type' => $transfer_type,
            'money' => $money,
            'cash_fee' => 0,
            'real_money' => abs($money),
            'before_money' => $user->balance,
            'after_money' => $user->balance + $money,
            'state' => $state
        ]);
    }

    public function betRecord(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        list($start, $end) = $this->getDateRange($data['date'] ?? null);
        $api_type = $data['api_type'] ?? '';
        $pagesize = $data['pagesize'] ?? 10;

        $game_list = $this->tgService->getallgamename();

        $list = GameRecord::where('user_id', $user->id)
            ->when($api_type, function ($query) use ($api_type) {
                return $query->where('platform_type', strtolower($api_type));
            })
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })
            ->orderBy('id', 'desc')
            ->select('bet_id', 'bet_time', 'platform_type', 'bet_amount', 'win_loss', 'status')
            ->paginate($pagesize);

        $list->getCollection()->transform(function ($item) use ($game_list) {
            $item->Code = $game_list[$item->platform_type] ?? '';
            return $item;
        });

        return $this->returnMsg(200, $list);
    }

    private function getDateRange($date)
    {
        $ranges = [
            1 => [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')],
            2 => [date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 23:59:59')],
            3 => [date('Y-m-d 00:00:00', strtotime('-15 days')), date('Y-m-d 23:59:59')],
            4 => [date('Y-m-d 00:00:00', strtotime('-30 days')), date('Y-m-d 23:59:59')],
        ];

        return $ranges[$date] ?? [null, null];
    }

    public function getdogame()
    {
        $gamelist = $this->tgService->getallgamename();
        unset($gamelist['universal']);
        return $this->returnMsg(200, $gamelist);
    }

    public function transRecord(TransRecordRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        list($start, $end) = $this->getDateRange($data['date'] ?? null);

        $list = $this->getRecordByType($data, $user, $start, $end);

        return $this->returnMsg(200, $list);
    }

    private function getRecordByType($data, $user, $start, $end)
    {
        $type = $data['type'];
        $pagesize = $data['pagesize'] ?? 10;

        switch ($type) {
            case 1:
                return $this->getRechargeRecords($user, $start, $end, $pagesize);
            case 2:
                return $this->getWithdrawRecords($user, $start, $end, $pagesize);
            case 3:
                return $this->getTransferInRecords($user, $start, $end, $data['api_type'] ?? '', $pagesize);
            case 4:
                return $this->getTransferOutRecords($user, $start, $end, $data['api_type'] ?? '', $pagesize);
            default:
                return [];
        }
    }

    private function getRechargeRecords($user, $start, $end, $pagesize)
    {
        $pay_way = [1 => '银行卡', 2 => '', 3 => '支付宝', 4 => '微信', 5 => 'USDT-TRC20', 6 => 'USDT-ERC20', 10 => '充值赠送'];
        $list = Recharge::where('user_id', $user->id)
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })
            ->orderBy('id', 'desc')
            ->select('amount', 'created_at', 'state', 'pay_way', 'out_trade_no')
            ->paginate($pagesize);

        $list->getCollection()->transform(function ($item) use ($pay_way) {
            $item->pay_way = $pay_way[$item->pay_way] ?? '';
            $item->amount = abs($item->amount);
            return $item;
        });

        return $list;
    }

    private function getWithdrawRecords($user, $start, $end, $pagesize)
    {
        $pay_way = [0 => '未记录', 1 => '银行卡', 2 => 'USDT-TRC20', 3 => 'USDT-ERC20'];
        $list = Withdraw::where('user_id', $user->id)
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })
            ->orderBy('id', 'desc')
            ->select('real_money', 'created_at', 'state', 'order_no as out_trade_no', 'type')
            ->paginate($pagesize);

        $list->getCollection()->transform(function ($item) use ($pay_way) {
            $item->pay_way = $pay_way[$item->type] ?? '';
            $item->amount = abs($item->real_money);
            return $item;
        });

        return $list;
    }

    private function getTransferInRecords($user, $start, $end, $api_type, $pagesize)
    {
        $gamelist = $this->tgService->getallmoneygamelist();
        $list = TransferLog::where('user_id', $user->id)->where('transfer_type', 0)
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })
            ->when($api_type, function ($query) use ($api_type) {
                return $query->where('api_type', $api_type);
            })
            ->select('real_money', 'created_at', 'state', 'api_type')
            ->orderBy('id', 'desc')
            ->paginate($pagesize);

        $list->getCollection()->transform(function ($item) use ($gamelist) {
            $item->pay_way = $gamelist[$item->api_type] ?? '';
            $item->amount = abs($item->real_money);
            return $item;
        });

        return $list;
    }

    private function getTransferOutRecords($user, $start, $end, $api_type, $pagesize)
    {
        $gamelist = $this->tgService->getallmoneygamelist();
        $list = TransferLog::where('user_id', $user->id)->whereIn('transfer_type', [1, 3])
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })
            ->when($api_type, function ($query) use ($api_type) {
                return $query->where('api_type', $api_type);
            })
            ->select('real_money', 'created_at', 'state', 'api_type')
            ->orderBy('id', 'desc')
            ->paginate($pagesize);

        $list->getCollection()->transform(function ($item) use ($gamelist) {
            $item->pay_way = ($item->api_type == 'web') ? '优惠活动' : ($gamelist[$item->api_type] ?? '');
            $item->amount = abs($item->real_money);
            return $item;
        });

        return $list;
    }

    public function userbalancelist()
    {
        $user = Auth::user();
        $Api = Api::where('state', 1)->orderBy('order_by', 'asc')->get();
        $data = [];
        foreach ($Api as $key => $v) {
            $User_Api = User_Api::where('api_code', $v->api_code)->where('user_id', $user->id)->first();
            $data[$key]['balance'] = $User_Api ? sprintf("%.2f", $User_Api->api_money) : 0;
            $data[$key]['name'] = $v->api_name;
            $data[$key]['platname'] = $v->api_code;
        }
        return $this->returnMsg(200, $data);
    }

    public function userapimoney(Request $request, $api_code)
    {
        $user = Auth::user();
        $this->registerApiIfNecessary($api_code, $user);

        $result = $this->tgService->balance($api_code, $user->username);
        if ($result['code'] != 200) {
            return $this->returnMsg(201, '', $result['message']);
        }

        $userApi = User_Api::where('api_code', $api_code)->where('user_id', $user->id)->first();
        $userApi->api_money = $result['data'];
        $userApi->save();

        return $this->returnMsg(200, ['balance' => $result['data']]);
    }

    public function uptransferstatus(Request $request)
    {
        $user = Auth::user();
        $user->update($request->all());
        return $this->returnMsg(200, '', '申请成功');
    }

    public function fanshui(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        list($start, $end) = $this->getDateRange($data['date'] ?? null);
        $pagesize = $data['pagesize'] ?? 10;
        $api_type = $data['api_type'] ?? '';
        $type = $data['type'] ?? '';

        $game_list = $this->tgService->getallgamename();

        $lists = TransferLog::where('user_id', $user->id)
            ->where('transfer_type', 6)
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })
            ->when($api_type, function ($query) use ($api_type) {
                return $query->where('platform_type', $api_type);
            })
            ->when($type, function ($query) use ($type) {
                return $query->where('state', $type - 1);
            })
            ->orderBy('id', 'desc')
            ->paginate($pagesize);

        $lists->getCollection()->transform(function ($item) use ($game_list) {
            $item->gamename = $game_list[$item->platform_type] ?? '';
            return $item;
        });

        $list['list'] = $lists;
        $list['jisuan'] = TransferLog::where('user_id', $user->id)->where('transfer_type', 6)->where('state', 1)->sum('real_money');
        $list['nojisuan'] = TransferLog::where('user_id', $user->id)->where('transfer_type', 6)->where('state', 0)->sum('real_money');

        return $this->returnMsg(200, $list);
    }

    public function dofanshui(Request $request)
    {
        $user = Auth::user();
        $userfanshui = TransferLog::where('user_id', $user->id)->where('state', 0)->where('transfer_type', 6)->sum('real_money');

        if ($userfanshui > 0) {
            DB::transaction(function () use ($user, $userfanshui) {
                $user->increment('balance', $userfanshui);

                $betlist = TransferLog::where('user_id', $user->id)->where('state', 0)->where('transfer_type', 6)->pluck('betid');

                TransferLog::where('user_id', $user->id)
                    ->where('state', 0)
                    ->update(['state' => 1, 'updated_at' => now()]);

                GameRecord::where('user_id', $user->id)->whereIn('bet_id', $betlist)->update(['is_back' => 1, 'updated_at' => now()]);
            });

            return $this->returnMsg(200, '', '成功领取');
        }

        return $this->returnMsg(202, '', '没有可领取的返水');
    }

    public function banklist()
    {
        $banklist = Bank::where('state', 1)->get()->map(function ($bank) {
            $bank->ico = config('app.url') . '/uploads/' . $bank->bank_img;
            return $bank;
        });

        return $this->returnMsg(200, $banklist);
    }

    public function getpaybank()
    {
        $cardlist = PaySetting::with('bank_data')->where('state', 1)->get()->map(function ($card) {
            if (isset($card->bank_data) && !in_array($card->bank_data->bank_name, ['USDT', '银行类型后台添加'])) {
                $card->ico = config('app.url') . '/uploads/' . $card->bank_data->bank_img;
            } else {
                $card->ico = '';
            }
            return $card;
        });

        return $this->returnMsg(200, $cardlist);
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

    public function getAllUserCard()
    {
        $user = Auth::user();
        $list = UserCard::where('user_id', $user->id)->get()->map(function ($card) {
            if (!in_array($card->bank, ['USDT', 'ebpay'])) {
                $banklist = Bank::where('bank_name', $card->bank)->first();
                if($banklist){
                    $card->ico = config('app.url') . '/uploads/' . $banklist->bank_img;
                }
            } else {
                $card->ico = '';
            }
            return $card;
        });

        return $this->returnMsg(200, $list);
    }

    public function systemBankCardInfo(Request $request)
    {
        $payType = $request->input('payType');
        $query = PaySetting::where('state', 1);

        if ($payType != 1) {
            $query->where('bank_id', '>', 1);
        } else {
            $query->where('bank_id', 1);
        }

        $card = $query->first();
        return $this->returnMsg(200, $card);
    }

    public function gameslist(Request $request)
    {
        $gamecode = $request->input('gamecode');
        $gamelist = $this->tgService->gameslist($gamecode);
        return $this->returnMsg(200, $gamelist['data'] ?? []);
    }

    public function messagecenter(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type');

        $list = Message::where('type', $type)
            ->where(function ($query) use ($user) {
                $query->where('user_id', 0)
                    ->orWhere('vip_id', $user->vip)
                    ->orWhere('isagent', $user->is_agent)
                    ->orWhere('user_id', $user->id);
            })
            ->paginate(10);

        $list->getCollection()->transform(function ($item) use ($user) {
            $item->is_read = UserMessage::where('message_id', $item->id)->where('user_id', $user->id)->exists();
            $item->desc = mb_substr(strip_tags($item->content), 0, 20, 'utf-8');
            return $item;
        });

        return $this->returnMsg(200, $list);
    }

    public function message(Request $request)
    {
        $user = Auth::user();
        $id = $request->input('id');

        $list = Message::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', 0)
                    ->orWhere('vip_id', $user->vip)
                    ->orWhere('isagent', $user->is_agent)
                    ->orWhere('user_id', $user->id);
            })
            ->first();

        return $this->returnMsg(200, $list);
    }

    public function app()
    {
        $configKeys = [
            'ios_download_url', 'ios_download_qrcode', 'site_title',
            'redpacket', 'site_state', 'fanshui', 'isclose',
            'repair_tips', 'webcontent', 'site_logo'
        ];
        $config = SystemConfig::whereIn('key', $configKeys)->pluck('value', 'key');

        $data = [
            'ios_download_qrcode' => config('app.url') . '/uploads/' . ($config['ios_download_qrcode'] ?? ''),
            'ios_download_url' => $config['ios_download_url'] ?? '',
            'h5_url' => config('app.wap_url'),
            'title' => $config['site_title'] ?? 'TG娱乐城',
            'redpacket_switch' => $config['redpacket'] ?? null,
            'site_state' => $config['site_state'] ?? null,
            'fanshui' => $config['fanshui'] ?? null,
            'index_modal' => $config['isclose'] ?? null,
            'repair_tips' => $config['repair_tips'] ?? null,
            'webcontent' => $config['webcontent'] ?? null,
            'site_logo' => config('app.url') . '/uploads/' . ($config['site_logo'] ?? ''),
        ];

        return $this->returnMsg(200, $data);
    }

    public function applyagentdo(Request $request)
    {
        $user = Auth::user();

        if (AgentApply::where('user_id', $user->id)->exists()) {
            return $this->returnMsg(500, '', '您已申请过代理');
        }

        AgentApply::create([
            'user_id' => $user->id,
            'apply_info' => $request->input('apply_info'),
            'state' => 1,
            'mobile' => $request->input('mobile'),
        ]);

        return $this->returnMsg(200, '', '申请成功');
    }

    public function getAgentLoginUrl()
    {
        return $this->returnMsg(200, ['url' => config('app.agent_login')]);
    }

    public function getVisitUrl(Request $request)
    {
        $origin = $request->header('origin');
        $isMobile = $this->isMobile($request->header('user-agent'));

        if ($isMobile) {
            $wapurl = config('app.wap_url');
            if ($origin == $wapurl) {
                return $this->returnMsg(500, [], 'wap');
            }
            return $this->returnMsg(200, ['url' => $wapurl]);
        }

        $url = config('app.pc_url');
        if ($origin == $url) {
            return $this->returnMsg(500, [], 'pc');
        }
        return $this->returnMsg(200, ['url' => $url]);
    }

    private function isMobile($userAgent)
    {
        $regex = '/Mobi|Android|iPhone/i';
        return preg_match($regex, $userAgent);
    }

    public function getAllPlat()
    {
        $vaild_plat = GameList::where('app_state', 1)->where('is_top', 1)->distinct()->pluck('platform_name');
        return $this->returnMsg(200, $vaild_plat);
    }

    public function getAllGameList(Request $request)
    {
        $platform = $request->input('platform');
        $category = $request->input('category');

        $list = GameList::when($platform, function ($query) use ($platform) {
            return $query->where('platform_name', $platform);
        })
            ->when($category, function ($query) use ($category) {
                return $query->where('category_id', $category);
            })
            ->where('is_top', 1)
            ->where('site_state', 1)
            ->select('name', 'platform_name', 'category_id', 'game_code', 'app_state', 'check_yes_img', 'check_no_img', 'api_logo_img', 'mobile_img')
            ->orderBy('order_by', 'asc')
            ->get()
            ->filter(function ($value) {
                return Api::where('api_code', $value['platform_name'])->where('state', 1)->exists();
            })
            ->values()
            ->all();

        return $this->returnMsg(200, $list);
    }

    public function getAppUrl()
    {
        return $this->returnMsg(200, ['url' => config('app.url')]);
    }
}
