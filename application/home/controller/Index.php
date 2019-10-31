<?php

namespace app\home\controller;

class Index extends Base
{
    
    public function index()
    {
        
        $lives = \app\home\model\Live::order('id desc')->limit(6)->select();
        //渲染模板
         return view('Index/index', ['lives'=>$lives]);
    }

    
}
        
