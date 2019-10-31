<?php

namespace tools\live;

class Live
{
    private static $_config = [
        'push_key' => '1XGYhosUDW', //推流鉴权key
        'live_key' => 'SLPuAO5Mk0', //播流鉴权key
        'push_domain' => 'ljjpush.tbyue.com', //推流域名
        'live_domain' => 'ljjlive.tbyue.com', //播流域名
        'app_name' => 'beihe', //自定义应用名称
        'stream_name' => '0', //自定义流名称
        'expire' => 1800, //有效期 秒
    ];

    public static function getUrl($user_id = 0, $time = '')
    {
        $res = [];
        self::$_config['stream_name'] = $user_id;
        if (!$time) {
            $time = time();
        }
        $time += self::$_config['expire'];

        $push_sstring = '/'.self::$_config['app_name'].'/'.self::$_config['stream_name'].'-'.$time.'-0-0-'.self::$_config['push_key'];
        $live_sstring = '/'.self::$_config['app_name'].'/'.self::$_config['stream_name'].'.flv-'.$time.'-0-0-'.self::$_config['live_key'];

        $push_md5 = md5($push_sstring);
        $live_md5 = md5($live_sstring);

        $res['push_url'] = 'rtmp://'.self::$_config['push_domain'].'/'.self::$_config['app_name'];
        $res['push_key'] = self::$_config['stream_name'].'?auth_key='.$time.'-0-0-'.$push_md5;
        $res['live_url'] = 'http://'.self::$_config['live_domain'].'/'.self::$_config['app_name'].'/'.self::$_config['stream_name'].'.flv?auth_key='.$time.'-0-0-'.$live_md5;

        return $res;
    }
}
