<?php

namespace app\home\validate;

use think\Validate;

    class Val extends Validate
    {
        protected $rule = [
            'phone|手机号' => 'require|regex:1[3-9]\d{9}|unique:user,phone',
             'password|密码' => 'require|length:2,20|confirm:repassword',
            // 'password|密码' => 'require|length:=3,20',
            'repassword|确认密码' => 'require|length:2,20',
            'code|短信验证码' => 'require|length:4',
            'phone.regex' => '手机号格式不正确',
            'username' => 'require',
            'passwd|密码' => 'require',
            'goods_id' => 'require|integer|gt:0',
            'spec_goods_id' => 'integer|egt:0',
            'number' => 'require',
            'id' => 'require|integer|gt:0',
            'cart_id'=>'require',
            'address_id' => 'require',
            'type_id' => 'require',
            'pay_type' => 'require',
            
        ];

        protected $scene = [
                   'res' => ['phone', 'password','repassword','code','phone.regex'],
                   'login' => ['username','passwd'],
                   'cart_add' => ['goods_id','spec_goods_id','number'],
                   'cart_change' => ['cart_id','number'],
                   'order_save' => ['address_id'],
                   'pay' => ['id','pay_type'],
               ];
    }
