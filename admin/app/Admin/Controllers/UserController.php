<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\User;
use App\Models\UserVip;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Illuminate\Support\Facades\Hash;
use Dcat\Admin\Http\Controllers\AdminController;

use App\Admin\Actions\Grid\User\Balance;
use App\Admin\Actions\Grid\User\BackBalance;
use App\Admin\Renderable\UserBalance;
use Dcat\Admin\Widgets\Card as WidgetsCard;
use App\Services\TgService;

class UserController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new User(), function (Grid $grid) {
            $grid->model()->with(['parent_agent'])->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('pid', '上级代理')->display(function ($pid) {
                return $this->parent_agent['username'] ?? '无';
            });
            $grid->column('username')->view('admin.field.user_username');
            $grid->column('realname');
            $grid->column('vip', '等级')->display(function ($vip) {
                $vipinfo = UserVip::find($vip);
                return ($vipinfo) ? $vipinfo->vipname : '注册会员';
            });
            $grid->column('isagent', '是否代理')->using([1 => '是', 0 => '否'])->dot([0 => 'danger', 1 => 'success']);
            $grid->column('balance');
            $grid->column('mbalance');
            $grid->column('phone');
            $grid->column('mail');
            $grid->column('paysum');
            $grid->column('status')->using([1 => '正常', 0 => '禁用'])->dot([0 => 'danger', 1 => 'success']);
            $grid->column('isblack','黑名单')->using([1 => '是', 0 => '否'])->dot([0 => 'success', 1 => 'danger']);
            $grid->column('wallet_balance', '游戏余额')
                ->display('接口余额') // 设置按钮名称
                ->modal(function ($modal) {
                    // 设置弹窗标题
                    $modal->title('游戏余额');
                    $res = UserBalance::make(['id' => $this->username]);
                    return $res;
                });
            $grid->column('lastip', '最后登录IP')->display(function () {
                return $this->lastip.'/'.$this->last_login_ip_address;
            });
             $grid->column('reg_ip','注册IP');
            $grid->column('created_at');

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                // append一个操作
                $actions->append(new Balance());
                $actions->append(new BackBalance());
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('username');
                 $filter->where('parent_name', function ($q) {
                    $q->whereHas('parent_agent', function ($q) {
                        $q->where('username', 'like', "%{$this->input}%");
                    });
                }, '上级代理');
                $filter->equal('isagent', '代理会员')->select([
                    0 => '非代理会员',
                    1 => '代理会员',
                ]);
                 $filter->equal('status', '状态')->select([
                    0 => '禁用',
                    1 => '正常',
                ]);
                $filter->equal('isblack', '黑名单')->select([
                    0 => '否',
                    1 => '是',
                ]);
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
        return Show::make($id, new User(['parent_agent']), function (Show $show) {
            $show->field('id');
            $show->field('pid', '上级代理')->as(function ($pid) {
                return $this->parent_agent['username'] ?? '无';
            });
            $show->field('username');
            $show->field('realname');
            $show->field('vip','会员等级')->as(function ($vip){
                $vipinfo = UserVip::find($vip);
                return ($vipinfo) ? $vipinfo->vipname : '注册会员';
            });
            $show->field('level');
            $show->field('isonline')->using([1 => '在线', 0 => '离线']);
            $show->field('isagent')->using([1 => '是', 0 => '否']);
            $show->field('allowagent','允许开代理')->using([1 => '是', 0 => '否']);
            $show->field('balance');
            $show->field('mbalance');
            $show->field('phone');
            $show->field('mail');
            $show->field('paysum');
            $show->field('status')->using([1 => '正常', 0 => '禁用']);
            $show->field('isdel')->using([1 => '是', 0 => '否']);
            $show->field('isblack')->using([1 => '是', 0 => '否']);
            $show->field('lastip');
            $show->logintime()->as(function ($logintime) {
                return date('Y-m-d H:i:s', $logintime);
            });
            $show->field('reg_ip');
            $show->field('loginsum');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }



    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new User(), function (Form $form) {
            $form->display('id');
            if ($form->isCreating()) {
                $form->text('username')->rules('required|unique:users', ['required' => '请填写用户名', 'unique' => '用户名重复']);
            } else {
                $form->display('username');
            }
            $form->password('password', '密码')->creationRules('required|min:6|max:16', ['required' => '请填写密码', 'min' => '密码最少6位数', 'max' => '密码最多16位'])->help('不修改请留空');
            $form->text('realname')->rules('required', ['required' => '请填写真实姓名']);

            $settlements = UserVip::all();
            $options = [];
            foreach ($settlements as $k => $v) {
                $options[$v->id] = $v->vipname;
            }
            $form->select('vip', '会员等级')->options($options);


            $form->display('level');
            $form->password('paypwd', '支付密码')->help('不修改请留空');

            $form->radio('isagent','是否代理')->options([1 => '是', 0 => '否'])->default(0);

            $settlements = \App\Models\User::where('isagent', 1)->get();
            $options = [0 => '无'];
            foreach ($settlements as $k => $v) {
                $options[$v->id] = $v->username;
            }
            $form->select('pid', '上级代理')->options($options)->default(0);

            $form->radio('allowagent','允许开代理')->options([1 => '是', 0 => '否'])->default(1);
            $form->display('balance');
            $form->display('mbalance');
            $form->text('phone');
            $form->text('mail');
            $form->radio('status')->options([1 => '正常', 0 => '禁用'])->default(1);
            $form->radio('isdel','是否删除')->options([1 => '是', 0 => '否'])->default(0);
            $form->radio('isblack','黑名单')->options([1 => '是', 0 => '否'])->default(0);
            $form->text('sourceurl');


            $form->saving(function (Form $form) {
                // 判断是否是新增操作
                $form->vip = empty($form->vip) ? 1 :  $form->vip;
                if ($form->isCreating()) {
                    $form->settlement_id = 3;
                    $form->pid = intval($form->pid);
                    $form->password = Hash::make($form->password);
                    $form->paypwd = $form->paypwd ? Hash::make($form->paypwd) : '';
                } else {
                    if ($form->isagent) {
                        $form->settlement_id = 3;
                    }
                    $form->pid = intval($form->pid);

                    if ($form->password) {
                        $form->password = Hash::make($form->password);
                    } else {
                        $form->deleteInput('password');
                    }

                    if ($form->paypwd) {
                         $form->paypwd = Hash::make($form->paypwd);
                    } else {
                        $form->deleteInput('paypwd');
                    }
                }
            });

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
