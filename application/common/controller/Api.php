<?php

namespace app\common\controller;

use think\controller\Rest;
use think\Session;
use think\Request;

class Api extends Rest
{
    public function __construct()
    {
        parent::__construct();

        $token = trim(Request::instance()->post('token'));
        $timestamp = intval(Request::instance()->post('timestamp'));

        if(empty($token)|| empty($timestamp)){
            $this->jsonReturn(401,'缺少令牌参数');
        }

        if(abs(time()-$timestamp) > 180){
            $this->jsonReturn(401,'请求超时,请先校准手机时间');
        }

        $userInfo = Session::get('userInfo');
        if(empty($userInfo)){
            $this->jsonReturn(401,'用户未登录');
        }

        $sign = session_id();
        if(empty($sign)){
            $this->jsonReturn(401,'签名不能为空');
        }

        if($token !== $sign){
            $this->jsonReturn(401,'令牌参数错误');
        }
    }


    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $msg 信息
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     */
    protected function jsonReturn($code='200',$msg='成功',$data='') {

        $result = array(
            'code' => $code,
            'msg' => $msg
        );
        if(!empty($data))
        {
            $result['data'] = $data;
        }

        echo json_encode($result);
        exit();
    }

}
