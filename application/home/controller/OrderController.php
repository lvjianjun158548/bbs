<?php

namespace app\home\controller;

use think\Request;
use think\Db;
use app\home\model\Address;
use app\home\logic\OrderLogic;
use app\home\model\Order;
use app\home\model\OrderGoods;
use app\home\model\Goods;
use app\home\model\SpecGoods;

class OrderController extends Base
{
    /**
     * 显示资源列表.
     *
     * @return \think\Response
     */
    public function create()
    {
        if (!session('?user_info')) {
            session('back', 'home/OrderController/create');
            $this->redirect('home/Login/login');
        }
        $user_id = session('user_info.id');
        $address = Address::where('user_id', $user_id)->select();
        $address = $address->toArray();
        $cart_data = OrderLogic::getCartWithGoods();
        // dump($data);
        // die;

        return view('Order_controller/order', ['address' => $address, 'cart_data' => $cart_data]);
    }

    /**
     * 保存新建的资源.
     *
     * @param \think\Request $request
     *
     * @return \think\Response
     */
    public function save(Request $request)
    {
        Db::startTrans();
        try {
            $data = input();
            $validate = $this->validate($data, 'Val.order_save');
            if (true !== $validate) {
                throw new \Exception('传入的参数错误');
            }
            $user_id = session('user_info.id');
            $address_id = $data['address_id'];
            $order_sn = $user_id.time().mt_rand(1000, 9999);
            $address = Address::where('user_id', $user_id)->find($data['address_id']);
            $address = $address->toArray();
            if (!$address) {
                throw new \Exception('地址信息错误');
            }
            $res = OrderLogic::getCartWithGoods();

            foreach ($res['cart_data'] as $v) {
                if ($v['number'] > $v['goods']['goods_number']) {
                    throw new \Exception('商品库存不足');
                }
            }

            $total_price = $res['total_price'];
            $total_number = $res['total_number'];
            $order_data = [
                'order_sn' => $order_sn,
                'consignee' => $address['consignee'],
                'phone' => $address['phone'],
                'address' => $address['address'],
                'user_id' => $user_id,
                'goods_price' => $res['total_price'], //商品总价
                'shipping_price' => '0.00', //邮费
                'coupon_price' => '0.00', //优惠抵扣金额
                'order_amount' => $res['total_price'], //应付金额 = 商品总价 + 邮费 - 优惠抵扣金额
                'total_amount' => $res['total_price'], //订单总金额 = 商品总价 + 邮费
            ];
            $order = Order::create($order_data, true);
            $order_goods_data = [];

            foreach ($res['cart_data'] as $v) {
                $order_goods_data[] = [
                    'order_id' => $order['id'],
                    'goods_id' => $v['goods_id'],
                    'spec_goods_id' => $v['spec_goods_id'],
                    'number' => $v['number'],
                    'goods_name' => $v['goods']['goods_name'],
                    'goods_logo' => $v['goods']['goods_logo'],
                    'goods_price' => $v['goods']['goods_price'],
                    'spec_value_names' => $v['spec_goods']['value_names'],
                ];
            }

            $order_goods_model = new OrderGoods();
            $order_goods_model->saveAll($order_goods_data);

            $goods = [];
            $spec_goods = [];

            foreach ($res['cart_data'] as $v) {
                if (isset($v['spec_goods_id'])) {
                    $spec_goods[] = [
                        'id' => $v['spec_goods']['id'],
                        'store_count' => $v['spec_goods']['store_count'] - $v['number'],
                        'store_frozen' => $v['spec_goods']['store_count'] + $v['number'],
                    ];
                } else {
                    $goods[] = [
                        'id' => $v['goods']['id'],
                        'goods_number' => $v['goods']['goods_number'] - $v['number'],
                        'frozen_number' => $v['goods']['frozen_number'] + $v['number'],
                    ];
                }
            }

            $goods_model = new Goods();

            $goods_model->saveAll($goods);
            $goodsSpec_model = new SpecGoods();
            $goodsSpec_model->saveAll($spec_goods);

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $msg = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();

            $this->error($msg.';File:'.$file.':Line:'.$line);
        }

        $this->redirect('home/OrderController/pay?id='.$order['id']);
    }

    /**
     * 显示指定的资源.
     *
     * @param int $id
     *
     * @return \think\Response
     */
    public function pay($id)
    {
        $order = Order::find($id);

        $pay_type = config('pay_type');

        return view('Order_controller/pay', ['order' => $order, 'pay_type' => $pay_type]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     *
     * @return \think\Response
     */
    public function callback()
    {
        $data = input();
        // dump($data);
        // die;
        require_once './plugin/alipay/config.php';
        require_once './plugin/alipay/pagepay/service/AlipayTradeService.php';

        $arr = $_GET;
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);
        //echo $data['out_trade_no'];
        $order = Order::get(['order_sn' => $data['out_trade_no']]);
        if ($result) {
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);

            //支付宝交易号
            $trade_no = htmlspecialchars($_GET['trade_no']);

            return view('Order_controller/pay_success', ['order' => $order]);
        //echo '验证成功<br />支付宝交易号：'.$trade_no;

        //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            return view('Order_controller/pay_fail');
        }
    }

    public function notify()
    {
        $data = input();
        trace('order_notify 开始参数'.json_encode($data, JSON_UNESCAPED_UNICODE), 'error');
        require_once './plugin/alipay/config.php';
        require_once './plugin/alipay/pagepay/service/AlipayTradeService.php';

        $arr = $_GET;
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);
        if ($result) {//验证成功
            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号

            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];

            if ('TRADE_FINISHED' == $_POST['trade_status']) {
                //订单编号 订单金热 订单状态 修改状态 记录支付信息 扣减冻结库存
                $order = Order::where('order_sn', $out_trade_no)->find();
                if (!$order) {
                    trace('order-notify 支付失败：订单不存在；订单编号：'.$params['out_trade_no'], 'error');
                    echo 'fail';
                    die;
                }
                if (0 != $trade_status) {
                    trace('order-notify 支付失败：订单状态不是未付款；状态值为：'.$order['order_status'], 'error');
                    echo 'fail';
                    die;
                }
                if ($order['order_amount'] != $params['total_amount']) {
                    trace('order-notify 支付失败：交易金额不正确；订单应付款金额：'.$order['order_amount'].';实际支付金额：'.$params['total_amount'], 'error');
                    echo 'fail';
                    die;
                }
                $order->order_status = 1;
                $order->pay_time = time();
                $order->save();
                \app\home\model\PayLog::create(['order_sn' => $data['out_trade_no'], 'json' => json_encode($data, JSON_UNESCAPED_UNICODE)], true);
                $order_goods = Order::with('goods', 'spec_goods')->where('id', $ordre.id)->select();
                $goods = [];
                $spec_goods = [];
                foreach ($order_goods as $v) {
                    if ($v['spec_goods_id']) {
                        $spec_goods[] = [
                            'id' => $v['spec_goods']['id'],
                            'store_frozen' => $v['spec_goods']['store_frozen'] - $order['number'],
                        ];
                    } else {
                        $goods[] = [
                            'id' => $v['goods']['id'],
                            'frozen_number' => $v['goods']['frozen_number'] - $order['number'],
                        ];
                    }
                }
                $goods_model = new Goods();
                $spec_goods_model = new SpecGoods();
                $goods_model->saveAll($goods);
                $spec_goods_model->saveAll($spec_goods);
                echo 'suceess';
                die;
            } elseif ('TRADE_SUCCESS' == $_POST['trade_status']) {
            }
        } else {
            //验证失败
            // echo '验证失败';
            return view('Order_controller/pay_fail');
        }
    }

    public function toPay(Request $request, $id)
    {
        $data = input();
        $id = $data['id'];
        $order = Order::find($id);
        $validate = $this->validate($data, 'Val.pay');
        if (true !== $validate) {
            $this->error($validate);
        }
        switch ($data['pay_type']) {
             case 'wechat':
                $this->error('微信支付正在搭建');
             break;
             case 'union':
                $this->error('银联支付正在搭建');
             break;
             case 'alipay':
                $html = "<form id='alipayment' action='/plugin/alipay/pagepay/pagepay.php' method='post' style='display: none'>
                <input id='WIDout_trade_no' name='WIDout_trade_no' value='{$order['order_sn']}'/>
                <input id='WIDsubject' name='WIDsubject' value='品优购商城订单'/>
                <input id='WIDtotal_amount' name='WIDtotal_amount' value='{$order['order_amount']}'/>
                <input id='WIDbody' name='WIDbody' value='测试支付，支付了也不发货'/>
            </form><script>document.getElementById('alipayment').submit();</script>";
                echo $html;
                break;
             default:
             $html = "<form id='alipayment' action='/plugin/alipay/pagepay/pagepay.php' method='post' style='display: none'>
             <input id='WIDout_trade_no' name='WIDout_trade_no' value='{$order['order_sn']}'/>
             <input id='WIDsubject' name='WIDsubject' value='品优购商城订单'/>
             <input id='WIDtotal_amount' name='WIDtotal_amount' value='{$order['order_amount']}'/>
             <input id='WIDbody' name='WIDbody' value='测试支付，支付了也不发货'/>
         </form><script>document.getElementById('alipayment').submit();</script>";
             echo $html;
         }
    }

    /**
     * 删除指定资源.
     *
     * @param int $id
     *
     * @return \think\Response
     */
    public function delete($id)
    {
    }
}
