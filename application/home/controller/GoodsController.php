<?php

namespace app\home\controller;

use app\home\model\Goods;
use app\home\model\SpecValue;

class GoodsController extends Base
{
    /**
     * 显示资源列表.
     *
     * @return \think\Response
     */
    public function index($id = 0)
    {
        
        //接收参数
        $keywords = input('keywords');
        if (empty($keywords)) {
            //获取指定分类下商品列表
            if (!preg_match('/^\d+$/', $id)) {
                $this->error('参数错误');
            }
            //查询分类下的商品
            $list = \app\home\model\Goods::where('cate_id', $id)->order('id desc')->paginate(10);
            //查询分类名称
            $category_info = \app\home\model\Category::find($id);
            $cate_name = $category_info['cate_name'];
        } else {
            try {
                //从ES中搜索
                $list = \app\home\logic\GoodsLogic::search();
                // dump($list);
                // die;
                $cate_name = $keywords;
            } catch (\Exception $e) {
                $line = $e->getLine();
                $file = $e->getFile();
                $error = $e->getMessage();
               
                $this->error('服务器异常:'.$line.$file.$error);
            }
        }

        return view('Goods_controller/index', ['list' => $list, 'cate_name' => $cate_name]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function detail()
    {
        $data = input();
        $id = $data['id'];
        $goods = Goods::with('spec_goods,goods_images')->find($id);
        $goods = $goods->toArray();
        //dump($goods);
        // die;

        if (!$goods) {
            $this->error('未获取到商品的信息');
        }
        if (!empty($goods['spec_goods'])) {
            $goods['price'] = $goods['spec_goods'][0]['price'];
        }
        $value_ids = array_column($goods['spec_goods'], 'value_ids');
        $value_ids = implode('_', $value_ids);
        $value_ids = explode('_', $value_ids);
        $value_ids = array_unique($value_ids);
        $spec_values = SpecValue::with('spec_bind')->where('id', 'in', $value_ids)->select();
        $spec_values = $spec_values->toArray();
        $spec = [];
        foreach ($spec_values as $v) {
            $spec[$v['spec_id']] = [
                'spec_name' => $v['spec_name'],
                'spec_id' => $v['spec_id'],
                'spec_val' => [],
            ];
        }
        foreach ($spec_values as $v) {
            $spec[$v['spec_id']]['spec_val'][] = $v;
        }
        // dump($spec);
        // die;
        $value_ids_map = [];
        foreach ($goods['spec_goods'] as $v) {
            $value_ids_map[$v['value_ids']] = [
                'price' => $v['price'],
                'id' => $v['id'],
            ];
        }
        //dump($value_ids_map);

        $value_ids_map = json_encode($value_ids_map);
       
        return view('Goods_controller/detail', ['spec' => $spec, 'goods' => $goods, 'value_ids_map' => $value_ids_map]);

       
    }
}
