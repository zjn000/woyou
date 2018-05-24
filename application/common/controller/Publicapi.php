<?php

namespace app\common\controller;

use think\controller\Rest;
use think\Request;
use think\Session;


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


    /**
     * 检测单位时间内访问频率
     * @param $frequency_name       次数标识
     * @param $second               间隔时间（秒）
     * @param int $max_frequency    最大次数
     * @return bool     true 正常    false 异常
     */
    protected function check_frequency($frequency_name,$second,$max_frequency = 0){
        if(empty($frequency_name)){
            return false;
        }

        if(empty($max_frequency)){
            return true;
        }

        //当前时间戳
        $time = time();

        $temp_timestamps = Session::get($frequency_name.'_timestamps');

        // 第一次请求，或超过间隔时间时
        if(empty($temp_timestamps) || $time - $temp_timestamps > $second){
            Session::set($frequency_name.'_timestamps',$time);  //刷新时间戳
            Session::set($frequency_name.'_times',1);           //刷新次数
            return true;
        }

        // 获取时间段内的请求次数，进行比较
        $temp_frequency = Session::get($frequency_name.'_times');

        //在间隔时间内
        if($time - $temp_timestamps < $second){
            // 如果次数大于最大次数，异常
            if($temp_frequency > $max_frequency){
                return false;
            }else{
                //次数+1
                Session::set($frequency_name.'_times',(int)$temp_frequency + 1);
            }
        }

        return true;
    }


}
