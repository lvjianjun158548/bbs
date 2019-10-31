<?php

namespace app\home\controller;

use think\Controller;

class Room extends Controller
{
    /**
     * 显示资源列表.
     *
     * @return \think\Response
     */
    public function index($id = 0)
    {
        $live = \app\home\model\Live::find($id);
        if ($live) {
            $live['goods'] = \app\home\model\LiveGoods::where('live_id', $id)->select();
        }
        $this->view->engine->layout(false);

        return view('Room/index', ['live' => $live]);
    }
}
