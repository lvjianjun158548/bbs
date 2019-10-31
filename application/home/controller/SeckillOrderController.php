<?php

namespace app\home\controller;

use app\home\model\UserInfo;

class SeckillOrderController extends Base
{
    public function index()
    {
        return view('Seckill_order_controller/seckillOrder');
    }

    public function info()
    {
        return view('Seckill_order_controller/seckillsettingInfo');
    }

    public function create()
    {
        $data = input();
        // $time = $data['birth'];
        // $time = strtotime($time);
        // $data['birth'] = $time;

        // dump($file);
        // die;
        $user_id = session('user_info.id');
        if (isset($user_id)) {
            $data['user_id'] = $user_id;
        } else {
            return json(['code' => 500, 'msg' => '必须先登录']);
        }
        $res = UserInfo::create($data, true);
        if ($res) {
            return json(['code' => 200, 'msg' => '个人信息注册成功']);
        } else {
            return json(['code' => 500, 'msg' => '个人信息注册失败']);
        }
    }

    public function upload()
    {
        $user_id = session('user_info.id');
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH.'public'.DS.'uploads');
        if ($info) {
            // 成功上传后 获取上传信息
            // 输出 jpg
            //echo $info->getExtension();
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $saveName = $info->getSaveName();
            $path = './uploads/'.$saveName;
            $res = UserInfo::update(['image' => $path], ['user_id' => $user_id], true);
            // dump($res);
            // die;
            if ($res) {
                return json(['code' => 200, 'msg' => '上传图片成功']);
            } else {
                return json(['code' => 500, 'msg' => '上传图片失败']);
            }
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            //echo $info->getFilename();
        } else {
            // 上传失败获取错误信息
            $error = $file->getError();
            return json(['code' => 400, 'msg' => $error]);
        }
    }
}
