<?php

namespace app\home\logic;
use app\home\model\Cart;
class OrderLogic{
    public static function getCartWithGoods(){
        $user_id = session('user_info.id');
       
        $cart_data = Cart::with('goods,spec_goods')->where('user_id',$user_id)->where('is_selected',1)->select();
        $cart_data = $cart_data->toArray();
        // dump($cart_data);
        // die;
        $total_price = 0;
        $total_number = 0;
        foreach($cart_data as &$v){
            if(isset($v['spec_goods_id'])){
                $v['goods']['goods_price'] = $v['spec_goods']['price'];
                $v['goods']['cost_price'] = $v['spec_goods']['cost_price'];
                $v['goods']['goods_number'] = $v['spec_goods']['store_count'];
                $v['goods']['frozen_number'] = $v['spec_goods']['store_frozen'];
            }
            $total_number+=$v['number'];
            $total_price+=$v['goods']['goods_price']*$v['number'];
        }
        $data = [
            'cart_data'=>$cart_data,
            'total_price'=>$total_price,
            'total_number'=>$total_number
        ];
        // dump($data);
        // die;
        return $data;
    }
    
} 