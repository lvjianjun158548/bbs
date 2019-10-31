<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use app\home\model\Category;
class Base extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    protected $category;
    public function __construct()
    {
        //
        parent::__construct();
        
        $cate = cache('category');
        if(!$cate){
            $cate = Category::select();
            $cate = $cate->toArray();
            $cate = get_tree_list($cate);
            cache('category',$cate);
        }
        $cate = cache('category');
        //$this->category = $cate;
        $this->assign('category',$cate);

        

    }

   
}
