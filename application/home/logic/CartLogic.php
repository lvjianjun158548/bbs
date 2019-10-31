<?php

namespace app\home\logic;
use app\home\model\Cart;
class CartLogic
{
    public static function getAllCart()
    {
        if (session('?user_info')) {
            $user_id = session('user_info.id');
            $data = Cart::where('user_id',$user_id)->field('user_id,goods_id,spec_goods_id,number,is_selected,id')->select();
            $data = $data->toArray();
        }else{
            $data = cookie('cart');
            //cookie('cart',null);
            // dump($data);
            // die;
            $data = array_values($data);
        }
        return $data;
    }

    public static function addCart($goods_id,$spec_goods_id,$number,$is_selected=1)
    {
        if (session('?user_info')) {
            $user_id = session('user_info.id');
            $where = [
                'goods_id'=>$goods_id,
                'spec_goods_id'=>$spec_goods_id,
                'user_id'=>$user_id,
                'is_selected'=>$is_selected
            ];
            $info = Cart::where($where)->find();
            //$info = $info->toArray();
            //$info = $info->toArray();
            if($info){
                $info = $info->toArray();
                // dump($info);
                // die;
                $number = $info['number'] + $number;
                $id = $info['id'];
                Cart::update(['number'=>$number],['id'=>$id],true);
            }else{
                Cart::create($where,true);
            }
        }else{
            $data = cookie('cart') ?: [];
            $key = $goods_id.'_'.$spec_goods_id;
            if(isset($data[$key])){
                $data[$key]['number'] = $data[$key]['number'] + $number;
            }else{
                $where = [
                    'id'=>$key,
                    'goods_id'=>$goods_id,
                    'spec_goods_id'=>$spec_goods_id,
                    'number'=>$number,
                    'is_selected'=>$is_selected
                ];
                $data[$key] = $where;
            }
            cookie('cart',$data,86400*7);
        }
        
        
    }

    public static function changeNum($id,$number){
        if(session('?user_info')){
            $user_id = session('user_info.id');
            Cart::update(['number' => $number], ['id'=>$id, 'user_id' => $user_id], true);
        }else{
            $data = cookie('cart') ?: [];
            $data[$id]['number'] = $number;
            cookie('cart', $data, 86400*7);
        }
        
    }
    public static function changeStatu($data){
        
        //dump($data);
        //die;
        if(session('?user_info')){
            $user_id = session('user_info.id');
            $status = $data['status'];
            $where['user_id'] = $user_id;
            // dump($data);
            // die;
            if($data['id']!='all'){
                $where['id'] = $data['id'];
                $list = Cart::update(['is_selected'=>$status],$where,true);
            }else{
                $list = Cart::update(['is_selected'=>$status],$where,true);
            }
            // dump($where);
            
            // //dump($list);
            // die;
            
        }else{
            $cart = cookie('cart');
            // dump($data);
            // die;
            $status = $data['status'];
            if($data['id']!='all'){
                $id = $data['id'];
                // echo $id;
                // die;
                $cart[$id]['is_selected'] = $status;
                //echo $cart[$id]['is_selected'];
                //die;
            }else{
                foreach($cart as $v){
                    //$id = $cart['id'];
                    $v['is_selected'] = $status;
                }
            }
            cookie('cart',$cart,3600*24*7);
            //$cart = cookie('cart');



            
        }
        
    }
    public static function delCart($id){
        Cart::destroy($id);
        if(session('?user_info')){
            $user_id = session('user_info.id');
            $res = Cart::destroy($id);
            //dump($res);
            return $res;

        }else{
            $data = cookie('cart') ?: [];
            
            unset($data[$id]);
            cookie('cart', $data, 86400*7);
            return true;
        }
    }
    public static function cookieToDb(){
        $data = cookie('cart');
        if(!empty($data)){
            foreach($data as $v){
                self::addCart($v['goods_id'],$v['spec_goods_id'],$v['number']);
            }
        }
        
        cookie('cart',null);
    }
}
