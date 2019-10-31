<?php

namespace app\home\controller;

use app\home\logic\CartLogic;
use app\home\logic\GoodsLogic;

class CartController extends Base
{
    /**
     * 显示资源列表.
     *
     * @return \think\Response
     */
    public function cart_list()
    {
        $list = CartLogic::getAllCart();
        foreach ($list as &$v) {
            $goods = GoodsLogic::getGoodsWithSpecGoods($v['goods_id'], $v['spec_goods_id']);
            $v['goods'] = $goods;
        }
        // dump($list);die;
        return view('Cart_controller/cart_list', ['list' => $list]);
    }

    public function addcart()
    {
        $data = input();
        // dump($data);
        $validate = $this->validate($data, 'Val.cart_add');
        if (true !== $validate) {
            $this->error($validate);
        }
        CartLogic::addCart($data['goods_id'], $data['goods_spec_id'], $data['number']);
        $goods = GoodsLogic::getGoodsWithSpecGoods($data['goods_id'], $data['goods_spec_id']);

        return view('Cart_controller/cart_add', ['goods' => $goods, 'number' => $data['number']]);
    }

    public function changeNum()
    {
        $data = input();
        $validate = $this->validate($data, 'Val.cart_change');
        if (true !== $validate) {
            return json(['code' => 500, 'msg' => '参数错误']);
        }

        CartLogic::changeNum($data['cart_id'], $data['number']);

        return json(['code' => 200, 'msg' => '修改购车成功']);
    }

    public function changeStatu()
    {
        $data = input();
        $list = CartLogic::changeStatu($data);

        return json(['code' => 200, 'msg' => '全选修改成功']);
    }

    public function cartDel()
    {
        $data = input();

        if (!isset($data['id'])) {
            return json(['code' => 500, 'msg' => '删除购物车参数错误']);
        }
        $id = $data['id'];
        $res = CartLogic::delCart($id);

        return json(['code' => 200, 'msg' => '删除购无车成功']);
    }
}
