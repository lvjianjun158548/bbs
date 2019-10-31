<?php

namespace app\home\controller;

use think\Controller;
use app\home\model\User;
use app\home\logic\CartLogic;
class Login extends Controller
{
    public function login()
    {
        $this->view->engine->layout(false);

        return view('Login/login');
    }

    public function qqcallback()
    {
        require_once './plugin/qq/Connect2.1/API/qqConnectAPI.php';
        $qc = new \QC();
        $access_token = $qc->qq_callback(); //接口调用过程中的临时令牌
        $openid = $qc->get_openid(); //第三方帐号在本应用中的唯一标识
        //获取第三方帐号用户信息（比如昵称、头像。。。）
        $qc = new \QC($access_token, $openid);
        $info = $qc->get_user_info();
        //dump($info);die;
        //接下来就是自动登录和注册流程
        //判断是否已经关联绑定用户
        $open_user = \app\home\model\OpenUser::where('open_type', 'qq')->where('openid', $openid)->find();
        // $open_user = $open_user->toArray();
        if ($open_user && $open_user['user_id']) {
            //已经关联过用户，直接登录成功
            //同步用户信息到用户表
            $user = \app\home\model\User::find($open_user['user_id']);
            // $user = $user->toArray();
            $user->nickname = $info['nickname'];
            $user->save();
            //设置登录标识
            session('user_info', $user->toArray());
            $this->redirect('home/index/index');
        }
        if (!$open_user) {
            //第一次登录，没有记录，添加一条记录到open_user表
            $open_user = \app\home\model\OpenUser::create(['open_type' => 'qq', 'openid' => $openid]);
        }
        //让第三方帐号去关联用户（可能是注册，可能是登录）
        //记录第三方帐号到session中，用于后续关联用户
        session('open_user_id', $open_user['id']);
        session('open_user_nickname', $info['nickname']);
        $this->redirect('home/login/login');
    }

    public function dologin()
    {
        $data = input();
        $passwd = encrypt_password($data['passwd']);
        $username = $data['username'];

        $validate = $this->validate($data, 'Val.login');
        if (true !== $validate) {
            $this->error($validate);
        }
        $info = User::where(function ($query) use ($data) {
            $query->where('username', $data['username'])->whereOr('email', $data['username']);
        })->where('password', $passwd)->find();
        if ($info) {
            session('user_info', $info->toArray());
            //关联第三方用户
            $open_user_id = session('open_user_id');
            if ($open_user_id) {
                //关联用户
                \app\home\model\OpenUser::update(['user_id' => $info['id']], ['id' => $open_user_id], true);
                session('open_user_id', null);
            }
            //同步昵称到用户表
            $nickname = session('open_user_nickname');
            if ($nickname) {
                \app\home\model\User::update(['nickname' => $nickname], ['id' => $info['id']], true);
                session('open_user_nickname', null);
            }
            CartLogic::cookieToDb();
            if(session('?back')){
                $back = session('back');
                $this->redirect($back);
            }
            $this->success('登录成功', '/home/Index/index');
        } else {
            $this->error('密码或用户名错误');
        }
    }

    public function register()
    {
        $this->view->engine->layout(false);

        return view(Login/register);
    }

    public function registerDo()
    {
        $data = input();
        $phone = $data['phone'];
        $data['username'] = $data['phone'];
        $data['nickname'] = encrypt_phone($data['phone']);

        $validate = $this->validate($data, 'Val.res');
        if (true !== $validate) {
            $this->error($validate);
        }
        $data['password'] = encrypt_password($data['password']);
        $code = cache('register_code'.$phone);

        if ($code != $data['code']) {
            $this->error('验证码不正确');
        }
        cache('register_code'.$phone, null);
        // cache('register_time'.$phone, null);
        $res = User::create($data, true);
        if ($res) {
            $this->success('注册账号成功', 'login');
        } else {
            $this->error('注册账号失败');
        }
    }
    
    public function sendcode()
    {
        $data = input();
        $phone = $data['phone'];
        //dump($data);
        if (time() - cache('register_time'.$phone) < 60) {
            $this->error('不可使在短时间内进行多次验证');
        }
        $code = mt_rand(1000, 9999);
        $msg = '验证码为'.$code.'请注意保密';
        $res = send_msg($phone, $msg);
        $res = true;
        if (true === $res) {
            cache('register_code'.$phone, $code, 180);
            cache('register_time'.$phone, time(), 180);

            $code = cache('register_code'.$phone);

            return json(['code' => 200, 'msg' => '验证码发送成功', 'code_c' => cache('register_code'.$phone)]);
        } else {
            return json(['code' => 400, 'msg' => '验证码发送失败']);
        }
    }

    public function cookieTodb(){
        $data = cookie('cart') ?: [];
        foreach($data as $v){
            slef::addCart($v['goods_id'],$v['spec_goods_id'],$v['number']);

        }
        cookie('cart',null);
    }
}
