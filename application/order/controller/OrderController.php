<?php

namespace app\order\controller;

use think\Controller;
use app\home\model\Order;
use app\home\model\OrderGoods;
use app\home\model\Goods;
use app\home\model\SpecGoods;

class OrderController extends Controller
{
    /**
     * 显示资源列表.
     *
     * @return \think\Response
     */
    public function index()
    {
        while (true) {
            // $order_time = $order['create_time'];
            $time = time();
            $order = Order::where('order_status', 0)->where('create_time', '<', $time - 1800)->find();
            // $order = $order->toArray();

            if ($order) {
                $order->order_status = 5;
                $order->save();
                $order_goods = OrderGoods::with('goods', 'spec_goods')->where('id', $order['id'])->select();
                // die;
                $goods = [];
                $spec_goods = [];
                foreach ($order_goods as $v) {
                    if ($v['spec_goods_id']) {
                        $spec_goods[] = [
                            'id' => $v['spec_goods']['id'],
                            'store_frozen' => $v['spec_goods']['store_frozen'] - $v['number'],
                            'store_count' => $v['spec_goods']['store_count'] + $v['number'],
                        ];
                    } else {
                        $goods[] = [
                            'id' => $v['goods']['id'],
                            'frozen_number' => $v['goods']['frozen_number'] - $v['number'],
                            'goods_number' => $v['goods']['goods_number'] + $v['number'],
                        ];
                    }
                }
                $goods_model = new Goods();
                $spec_goods_model = new SpecGoods();
                $goods_model->saveAll($goods);
                $spec_goods_model->saveAll($spec_goods);

                echo 'success';
            }
            echo '没有数据';
            sleep(1);
        }
    }

    public function test()
    {
        // die;
        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
        $params = [
            'index' => 'test_index',
        ];
        $r = $es->indices()->create($params);
        dump($r);
        die;
    }

    public function test01()
    {
        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
        $params = [
            'index' => 'test_index',
            'type' => 'test_type',
            'id' => 103,
            'body' => ['id' => 100, 'title' => 'PHP从入门到精通', 'author' => '张三'],
        ];

        $r = $es->index($params);
        dump($r);
        die;
    }

    public function test02(){
        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
        $params = [
            'index' => 'test_index',
            'type' => 'test_type',
            'id' => 100,
            'body' => [
                'doc' => ['id'=>100, 'title'=>'ES从入门到精通', 'author' => '张三']
            ]
        ];

        $r = $es->update($params);
        dump($r);die;
    }

    public function test03(){
        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
        $params = [
            'index' => 'test_index',
            'type' => 'test_type',
            'id' => 100,
            'body' => [
                'doc' => ['id'=>100, 'title'=>'ES从入门到精通', 'author' => '张三']
            ]
        ];

        $r = $es->update($params);
        dump($r);die;
    }
    public function del(){
        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
        $params = [
            'index' => 'test_index',
            'type' => 'test_type',
            'id' => 100,
        ];

        $r = $es->delete($params);
        dump($r);die;
    }
}
