<?php
namespace app\api\controller;

use app\common\controller\Api;
use wxpay\JsapiPay;
use think\Request;

class Pay extends Api{


    // 小程序支付
    public function index()
    {
        $request = Request::instance();

        $oid = intval($request->post('oid'));

        $order = db('order')->find($oid);
        if(empty($order)){
            $this->jsonReturn(400,'订单参数错误');
        }

        if ($order['status'] > 0){
            $this->jsonReturn(400,'订单已过期,请重新下单');
        }

        $user = db('user')->where('id',$order['uid'])->field('id,openid,status')->find();

        if(empty($user)){
            $this->jsonReturn(404,'订单用户不存在');
        }

        if($user['status'] == 2){
            $this->jsonReturn(404,'此用户已被列入黑名单');
        }

        $params = [
            'body'         => '我有便利',
            'out_trade_no' => $order['o_no'],
            'total_fee'    => $order['total']*100
        ];

        $result = \wxpay\JsapiPay::getParams($params, $user['openid']);

        $this->jsonReturn(200,'成功',json_decode($result));

    }

}