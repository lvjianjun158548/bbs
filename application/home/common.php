<?php

if (!function_exists('encrypt_password')) {
    /**
     * 快速导入Traits PHP5.5以上无需调用.
     *
     * @param string $class trait库
     * @param string $ext   类库后缀
     *
     * @return bool
     */
    function encrypt_password($password)
    {
        return $password = md5($password);
    }
}

if (!function_exists('encrypt_phone')) {
    /**
     * 快速导入Traits PHP5.5以上无需调用.
     *
     * @param string $class trait库
     * @param string $ext   类库后缀
     *
     * @return bool
     */
    function encrypt_phone($phone)
    {
        return $phone = substr($phone, 0, 3).'****'.substr($phone, 7);
    }
}

if (!function_exists('curl_request')) {
    //使用curl函数库发送请求
    function curl_request($url, $post = false, $params = [], $https = false)
    {
        $ch = curl_init($url);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        if (false === $res) {
            //请求发送失败
            $msg = curl_error($ch);

            return [$msg];
        }
        curl_close($ch);

        return $res;
    }
}

if (!function_exists('send_msg')) {
    function send_msg($phone, $msg)
    {
        //请求地址
        $gateway = config('msg.gateway');
        $appkey = config('msg.appkey');
        //请求参数 get请求 参数拼接在url中
        $url = $gateway.'?appkey='.$appkey.'&mobile='.$phone.'&content='.$msg;
        //$res = curl_request($url, false, [], true);
        //post请求 参数也必须放在url中，是接口的要求
        $res = curl_request($url, true, [], true);
        //dump($res);die;
        if (is_array($res)) {
            //请求发送失败
            return $res[0];
        }
        //请求发送成功
        $arr = json_decode($res, true);
        if (!isset($arr['code']) || 10000 != $arr['code']) {
            return isset($arr['msg']) ? $arr['msg'] : '短信接口异常';
        }
        if (!isset($arr['result']['ReturnStatus']) || 'Success' != $arr['result']['ReturnStatus']) {
            //短信发送失败
            return isset($arr['result']['Message']) ? $arr['result']['Message'] : '短信发送失败';
        }

        return true;
    }
}


if (!function_exists('get_cate_list')) {
    //递归函数 实现无限级分类列表
    function get_cate_list($list,$pid=0,$level=0) {
        static $tree = array();
        foreach($list as $row) {
            if($row['pid']==$pid) {
                $row['level'] = $level;
                $tree[] = $row;
                get_cate_list($list, $row['id'], $level + 1);
            }
        }
        return $tree;
    }
}

if(!function_exists('get_tree_list')){
    //引用方式实现 父子级树状结构
    function get_tree_list($list){
        //将每条数据中的id值作为其下标
        $temp = [];
        foreach($list as $v){
            $v['son'] = [];
            $temp[$v['id']] = $v;
        }
        //获取分类树
        foreach($temp as $k=>$v){
            $temp[$v['pid']]['son'][] = &$temp[$v['id']];
        }
        return isset($temp[0]['son']) ? $temp[0]['son'] : [];
    }
}
