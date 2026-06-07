
<?php

namespace App\\Http\\Controllers\\Agent;

use App\\Http\\Controllers\\Controller;
use App\\Models\\GameRecord;
use App\\Models\\Message;
use App\\Models\\Recharge;
use App\\Models\\TransferLog;
use App\\Models\\Withdraw;
use App\\Services\\GamereportService;
use Illuminate\\Http\\Request;
use App\\User;
use Illuminate\\Support\\Facades\\Auth;
use Illuminate\\Support\\Facades\\Hash;
use SimpleSoftwareIO\\QrCode\\Facades\\QrCode;
use App\\Models\\SystemConfig;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $childIds = User::getChild($user->id);
        $list = User::whereIn(\'id\', $childIds)->get();

        $start = $request->input(\'start\');
        $end = $request->input(\'end\');

        $statistics = [
            \'all_recharge\' => 0,
            \'all_withdraw\' => 0,
            \'all_valid_bet\' => 0,
            \'all_win_loss\' => 0,
        ];

        foreach ($list as $child) {
            $statistics[\'all_recharge\'] += User::rechargeSum($child->id, $start, $end);
            $statistics[\'all_withdraw\'] += User::withdrawSum($child->id, $start, $end);
            $statistics[\'all_valid_bet\'] += User::vaildBetSum($child->id, $start, $end);
            $statistics[\'all_win_loss\'] += User::totalfanhui($child->id, $start, $end);
        }

        $messages = Message::where(\'user_id\', 0)->orderBy(\'id\', \'desc\')->paginate(6);

        return view(\'agent.index\', array_merge(compact(\'user\', \'messages\'), $statistics));
    }

    public function getuserdata()
    {
        $user = Auth::user();
        $ret = User::getBetDayDta($user->id, 6);
        return response()->json($ret);
    }

    public function notice()
    {
        $list = Message::where(\'user_id\', 0)->orderBy(\'id\', \'desc\')->paginate(10);
        return view(\'agent.notice.notice\', compact(\'list\'));
    }

    public function message()
    {
        $user = Auth::user();
        $list = Message::where(\'user_id\', $user->id)->orderBy(\'id\', \'desc\')->paginate(10);
        return view(\'agent.notice.notice\', compact(\'list\'));
    }

    public function noticeDetail($id)
    {
        $item = Message::findOrFail($id);
        return view(\'agent.notice.notice_detail\', compact(\'item\'));
    }

    public function chart()
    {
        return view(\'agent.report.chart\');
    }

    public function todayData()
    {
        $user = Auth::user();
        $all_child = User::getChild($user->id);
        $today = date(\'Y-m-d\');

        $data = [
            \'child_member_count\' => User::whereIn(\'pid\', $all_child)->where(\'isagent\', 0)->count(),
            \'child_agent_count\' => User::whereIn(\'pid\', $all_child)->where(\'isagent\', 1)->count(),
            \'directly_member_count\' => User::where(\'pid\', $user->id)->where(\'isagent\', 0)->count(),
            \'directly_agent_count\' => User::where(\'pid\', $user->id)->where(\'isagent\', 1)->count(),
            \'add_member_count\' => User::where(\'pid\', $user->id)->whereDate(\'created_at\', $today)->count(),
            \'all_recharge\' => Recharge::whereIn(\'user_id\', $all_child)->whereDate(\'created_at\', $today)->where(\'state\', 2)->sum(\'amount\'),
            \'all_withdraw\' => Withdraw::whereIn(\'user_id\', $all_child)->whereDate(\'created_at\', $today)->where(\'state\', 2)->sum(\'amount\'),
            \'all_bet\' => GameRecord::whereIn(\'user_id\', $all_child)->whereDate(\'created_at\', $today)->sum(\'bet_amount\'),
            \'all_valid_bet\' => GameRecord::whereIn(\'user_id\', $all_child)->whereDate(\'created_at\', $today)->sum(\'valid_amount\'),
            \'win_loss\' => GameRecord::whereIn(\'user_id\', $all_child)->whereDate(\'created_at\', $today)->sum(\'win_loss\'),
        ];

        return view(\'agent.report.today_data\', $data);
    }

    public function profit(Request $request)
    {
        $user = Auth::user();
        $childIds = User::getChild($user->id);

        $username = $request->input(\'username\');
        if ($username) {
            $search_user = User::where(\'username\', $username)->first();
            if (!$search_user) {
                return back()->with(\'opMsg\', \'用户不存在\');
            }
            if (!in_array($search_user->id, $childIds)) {
                return back()->with(\'opMsg\', \'用户不在您的下级列表中\');
            }
            $childIds = [$search_user->id];
        }

        $start = $request->input(\'start\');
        $end = $request->input(\'end\');

        $list = User::whereIn(\'id\', $childIds)->paginate(10);

        foreach ($list as $k => $v) {
            $list[$k]->rechage_times = User::rechargeTimes($v->id, $start, $end);
            $list[$k]->withdraw_times = User::withdrawTimes($v->id, $start, $end);
            $list[$k]->all_recharge = User::rechargeSum($v->id, $start, $end);
            $list[$k]->all_withdraw = User::withdrawSum($v->id, $start, $end);
            $list[$k]->all_valid_bet = User::vaildBetSum($v->id, $start, $end);
            $list[$k]->all_win_loss = User::winLoss($v->id, $start, $end);
        }

        return view(\'agent.report.profit\', compact(\'list\', \'start\', \'end\', \'username\'));
    }

    public function commission(Request $request)
    {
        $user = Auth::user();
        $start = $request->input(\'start\');
        $end = $request->input(\'end\');

        $childIds = User::getChild($user->id);
        
        $totalStats = [
            \'rechage_times\' => 0,
            \'withdraw_times\' => 0,
            \'all_recharge\' => 0,
            \'all_withdraw\' => 0,
            \'all_valid_bet\' => 0,
            \'all_win_loss\' => 0,
            \'all_fanshui\' => 0,
            \'all_redpacket\' => 0,
            \'all_valid_betsum\' => 0,
        ];

        $list = User::whereIn(\'id\', $childIds)->get();

        foreach ($list as $child) {
            $totalStats[\'rechage_times\'] += User::rechargeTimes($child->id, $start, $end);
            $totalStats[\'withdraw_times\'] += User::withdrawTimes($child->id, $start, $end);
            $totalStats[\'all_recharge\'] += User::rechargeSum($child->id, $start, $end);
            $totalStats[\'all_withdraw\'] += User::withdrawSum($child->id, $start, $end);
            $totalStats[\'all_valid_bet\'] += User::vaildBetSum($child->id, $start, $end);
            $totalStats[\'all_win_loss\'] += User::winLoss($child->id, $start, $end);
            $totalStats[\'all_fanshui\'] += User::totalfanhui($child->id, $start, $end);
            $totalStats[\'all_redpacket\'] += User::redpacketSum($child->id, $start, $end);
            $totalStats[\'all_valid_betsum\'] += User::vaildBetCount($child->id, $start, $end);
        }

        $commissionData = (object)[
            \'username\' => $user->username,
            \'realname\' => $user->realname,
            \'isagent\' => $user->isagent,
            \'rechage_times\' => $totalStats[\'rechage_times\'] + User::rechargeTimes($user->id, $start, $end),
            \'withdraw_times\' => $totalStats[\'withdraw_times\'] + User::withdrawTimes($user->id, $start, $end),
            \'all_recharge\' => $totalStats[\'all_recharge\'] + User::rechargeSum($user->id, $start, $end),
            \'all_withdraw\' => $totalStats[\'all_withdraw\'] + User::withdrawSum($user->id, $start, $end),
            \'all_valid_bet\' => $totalStats[\'all_valid_bet\'] + User::vaildBetSum($user->id, $start, $end),
            \'all_win_loss\' => $totalStats[\'all_win_loss\'] + User::winLoss($user->id, $start, $end),
            \'all_fanshui\' => $totalStats[\'all_fanshui\'] + User::totalfanhui($user->id, $start, $end),
            \'all_redpacket\' => $totalStats[\'all_redpacket\'] + User::redpacketSum($user->id, $start, $end),
            \'all_valid_betsum\' => $totalStats[\'all_valid_betsum\'] + User::vaildBetCount($user->id, $start, $end),
            \'usersum\' => User::UserSum($user->id, $start, $end),
            \'agentsum\' => User::AgentSum($user->id, $start, $end),
            \'yongjinsum\' => User::Agentyongjin($user->id, $start, $end),
            \'waityongjinsum\' => $this->getwaityongjinsum($user->id),
        ];

        return view(\'agent.report.commission\', [\'list\' => [$commissionData], \'start\' => $start, \'end\' => $end, \'username\' => $request->username]);
    }

    protected function getwaityongjinsum($user_id)
    {
        $settlementday = (int)SystemConfig::getValue(\'settlement\', 0);
        $diffday = strtotime(date(\'Y-m-d\')) - $settlementday * 86400;

        $money = TransferLog::where(\'user_id\', $user_id)
            ->where(\'transfer_type\', 20)
            ->where(\'created_at\', \'>=\', date(\'Y-m-d H:i:s\', $diffday))
            ->sum(\'money\');

        return $money > 0 ? $money : 0;
    }

    public function subordinate(Request $request)
    {
        $user = Auth::user();
        $username = $request->input(\'username\');
        
        $childQuery = User::where(\'pid\', $user->id)->where(\'isagent\', 1);

        if ($username) {
            $childQuery->where(\'username\', $username);
        }
        
        $list = $childQuery->paginate(10);

        $start = $request->input(\'start\');
        $end = $request->input(\'end\');

        foreach ($list as $k => $v) {
            $res = self::agentcommission($v->id, $start, $end);

            $list[$k]->rechage_times = $res[\'rechage_times\'];
            $list[$k]->withdraw_times = $res[\'withdraw_times\'];
            $list[$k]->all_recharge = $res[\'all_recharge\'];
            $list[$k]->all_withdraw = $res[\'all_withdraw\'];
            $list[$k]->all_valid_bet = $res[\'all_valid_bet\'];
            $list[$k]->all_win_loss = $res[\'all_win_loss\'];
            $list[$k]->all_fanshui = $res[\'all_fanshui\'];
            $list[$k]->all_redpacket = $res[\'all_redpacket\'];
            $list[$k]->usersum = User::UserSum($v->id, $start, $end);
            $list[$k]->agentsum = User::AgentSum($v->id, $start, $end);
            $list[$k]->yongjinsum = $this->getwaityongjinsum($v->id);
        }
        return view(\'agent.report.subordinate\', compact(\'list\', \'start\', \'end\', \'username\'));
    }

    public function agentcommission($user_id, $start, $end)
    {
        $childIds = User::getChild($user_id);
        $lists = User::whereIn(\'id\', $childIds)->get();

        $stats = [
            \'rechage_times\' => 0, \'withdraw_times\' => 0, \'all_recharge\' => 0,
            \'all_withdraw\' => 0, \'all_valid_bet\' => 0, \'all_win_loss\' => 0,
            \'all_fanshui\' => 0, \'all_redpacket\' => 0,
        ];

        foreach ($lists as $v) {
            $stats[\'rechage_times\'] += User::rechargeTimes($v->id, $start, $end);
            $stats[\'withdraw_times\'] += User::withdrawTimes($v->id, $start, $end);
            $stats[\'all_recharge\'] += User::rechargeSum($v->id, $start, $end);
            $stats[\'all_withdraw\'] += User::withdrawSum($v->id, $start, $end);
            $stats[\'all_valid_bet\'] += User::vaildBetSum($v->id, $start, $end);
            $stats[\'all_win_loss\'] += User::winLoss($v->id, $start, $end);
            $stats[\'all_fanshui\'] += User::totalfanhui($v->id, $start, $end);
            $stats[\'all_redpacket\'] += User::redpacketSum($v->id, $start, $end);
        }
        
        $stats[\'yongjinsum\'] = User::Agentyongjin($user_id, $start, $end);
        $stats[\'usersum\'] = User::UserSum($user_id, $start, $end);
        $stats[\'agentsum\'] = User::AgentSum($user_id, $start, $end);

        return $stats;
    }

    public function addMember(Request $request)
    {
        if ($request->isMethod(\'post\')) {
            $data = $request->all();
            
            if (User::where(\'username\', $data[\'username\'])->exists()) {
                return back()->with(\'opMsg\', \'用户名已存在\');
            }

            $puser = Auth::user();
            $is_agent = 1;
            if($puser->pid) {
                $pp_user = User::find($puser->pid);
                if ($pp_user && $pp_user->allowagent == 0) {
                    $is_agent = 0;
                }
            }
            
            $user = User::create([
                \'username\' => $data[\'username\'],
                \'pid\' => $puser->id,
                \'password\' => Hash::make($data[\'password\']),
                \'realname\' => $data[\'realname\'],
                \'paypwd\' => Hash::make(\'123456\'),
                \'vip\' => 1,
                \'isagent\' => $is_agent
            ]);

            if ($puser->id) {
                $gamereportService = new GamereportService();
                $gamereportService->add([
                    \'uid\' => $puser->id,
                    \'pid\' => $puser->pid,
                    \'isagent\' => $puser->isagent,
                    \'recnum\' => 1,
                ]);
            }

            return redirect(\'/memberlist\')->with(\'opMsg\', \'添加成功\');
        }
        return view(\'agent.agent.add_member\');
    }

    public function memberList(Request $request)
    {
        $user = Auth::user();
        $username = $request->input(\'username\');
        $childIds = User::getChild($user->id);
        
        $query = User::whereIn(\'id\', $childIds)->where(\'status\', 1);

        if ($username) {
            $query->where(\'username\', $username);
        }
        
        $list = $query->paginate(10);

        foreach ($list as $k => $v) {
            $parent = User::find($v->pid);
            $list[$k]->parent = $parent ? $parent->username : \'\';
            $list[$k]->is_direct = $v[\'pid\'] == $user->id ? 1 : 0;
        }
        return view(\'agent.agent.member\', compact(\'list\', \'user\'));
    }

    public function betLog(Request $request)
    {
        $user = Auth::user();
        $childIds = User::getChild($user->id);
        $username = $request->input(\'username\');
        
        $query = GameRecord::whereIn(\'user_id\', $childIds);

        if ($username) {
            $query->where(\'username\', $username);
        }
        
        if ($request->filled(\'start\')) {
            $query->where(\'created_at\', \'>\', date(\'Y-m-d 00:00:00\', strtotime($request->start)));
        }
        
        if ($request->filled(\'end\')) {
            $query->where(\'created_at\', \'<=\', date(\'Y-m-d 23:59:59\', strtotime($request->end)));
        }

        $list = $query->orderBy(\'id\', \'desc\')->paginate(10);
        return view(\'agent.agent.bet_log\', compact(\'list\'));
    }

    public function rechargeLog(Request $request)
    {
        $user = Auth::user();
        $childIds = User::getChild($user->id);
        $username = $request->input(\'username\');
        
        $query = Recharge::whereIn(\'user_id\', $childIds);

        if ($username) {
            $user_id = User::where(\'username\', $username)->value(\'id\');
            $query->where(\'user_id\', $user_id);
        }

        if ($request->filled(\'start\')) {
            $query->where(\'created_at\', \'>\', date(\'Y-m-d 00:00:00\', strtotime($request->start)));
        }
        
        if ($request->filled(\'end\')) {
            $query->where(\'created_at\', \'<=\', date(\'Y-m-d 23:59:59\', strtotime($request->end)));
        }

        $list = $query->orderBy(\'id\', \'desc\')->paginate(10);
        return view(\'agent.agent.recharge_log\', compact(\'list\'));
    }

    public function withdrawLog(Request $request)
    {
        $user = Auth::user();
        $childIds = User::getChild($user->id);
        $username = $request->input(\'username\');

        $query = Withdraw::whereIn(\'user_id\', $childIds);
        
        if ($username) {
            $user_id = User::where(\'username\', $username)->value(\'id\');
            $query->where(\'user_id\', $user_id);
        }

        if ($request->filled(\'start\')) {
            $query->where(\'created_at\', \'>\', date(\'Y-m-d 00:00:00\', strtotime($request->start)));
        }
        
        if ($request->filled(\'end\')) {
            $query->where(\'created_at\', \'<=\', date(\'Y-m-d 23:59:59\', strtotime($request->end)));
        }

        $list = $query->orderBy(\'id\', \'desc\')->paginate(10);
        return view(\'agent.agent.recharge_log\', compact(\'list\'));
    }

    public function transferLog(Request $request)
    {
        $user = Auth::user();
        $childIds = User::getChild($user->id);
        $username = $request->input(\'username\');

        $query = TransferLog::whereIn(\'user_id\', $childIds)->whereIn(\'transfer_type\', [0, 1]);

        if ($username) {
            $user_id = User::where(\'username\', $username)->value(\'id\');
            $query->where(\'user_id\', $user_id);
        }
        
        if ($request->filled(\'start\')) {
            $query->where(\'created_at\', \'>\', date(\'Y-m-d 00:00:00\', strtotime($request->start)));
        }
        
        if ($request->filled(\'end\')) {
            $query->where(\'created_at\', \'<=\', date(\'Y-m-d 23:59:59\', strtotime($request->end)));
        }

        $list = $query->orderBy(\'id\', \'desc\')->paginate(10);
        return view(\'agent.agent.transfer_log\', compact(\'list\'));
    }

    public function releasewaterLog(Request $request)
    {
        $user = Auth::user();
        $childIds = User::getChild($user->id);
        $username = $request->input(\'username\');
        
        $query = TransferLog::whereIn(\'user_id\', $childIds)->where(\'transfer_type\', 6);
        
        if ($username) {
            $user_id = User::where(\'username\', $username)->value(\'id\');
            $query->where(\'user_id\', $user_id);
        }

        if ($request->filled(\'start\')) {
            $query->where(\'created_at\', \'>\', date(\'Y-m-d 00:00:00\', strtotime($request->start)));
        }
        
        if ($request->filled(\'end\')) {
            $query->where(\'created_at\', \'<=\', date(\'Y-m-d 23:59:59\', strtotime($request->end)));
        }

        $list = $query->orderBy(\'id\', \'desc\')->paginate(10);
        return view(\'agent.agent.releasewater_log\', compact(\'list\'));
    }

    public function generateQrcode()
    {
        $user = Auth::user();
        $str = env(\'APP_URL\') . \'/register?id=\' . $user->id;
        $filename = public_path(\'uploads/agent/qrcode/\' . $user->id . \'.png\');
        
        if (!file_exists($filename)) {
            QrCode::encoding(\'UTF-8\')->format(\'png\')->generate($str, $filename);
        }

        return response()->download($filename, uniqid() . \'.png\');
    }

    public function recharge(Request $request)
    {
        if ($request->isMethod(\'post\')) {
            $data = $request->validate([
                \'amount\' => \'required|numeric|min:0\',
                \'user_id\' => \'required|exists:users,id\',
            ]);

            $user = Auth::user();
            if ($data[\'amount\'] > $user->balance) {
                return back()->with(\'opMsg\', \'余额不足\');
            }

            \DB::transaction(function () use ($user, $data) {
                $user->decrement(\'balance\', $data[\'amount\']);
                $child = User::find($data[\'user_id\']);
                $child->increment(\'balance\', $data[\'amount\']);

                Recharge::create([
                    \'order_no\' => $child->id . time() . rand(10000, 90000),
                    \'out_trade_no\' => $child->id . time() . rand(10000, 90000),
                    \'user_id\' => $child->id,
                    \'amount\' => $data[\'amount\'],
                    \'cash_fee\' => 0,
                    \'real_money\' => $data[\'amount\'],
                    \'pay_way\' => 11,
                    \'info\' => \'代理充值\',
                    \'state\' => 2,
                ]);
            });

            return redirect(\'/memberlist\')->with(\'opMsg\', \'充值成功\');
        }
        
        $user_id = $request->input(\'user_id\');
        return view(\'agent.agent.recharge\', compact(\'user_id\'));
    }
}
