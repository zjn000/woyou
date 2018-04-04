<?php

namespace app\common\controller;

use think\controller\Rest;
use think\Request;

class Publicapi extends Rest
{
    public function __construct()
    {
        parent::__construct();
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
