<?php
namespace app\common\library;

use think\session\driver\Redis;
use think\config;

class Session extends Redis{

    public function __construct()
    {

        parent::__construct(config('session'));
    }

    public function get($sessName,$prefix='think'){

        $this->open('tcp://127.0.0.1',$sessName);

        $str = $this->read($sessName);

        //没有这个session的值
        if(empty($str)){
            return [];
        }

        //获取从值域及后续的字符串
        $str =  strstr($str, $prefix.'|');

        if(!$str){
            return [];
        }

        //从第一个 '|' 符号位置截取后续字符串
        $str2 = substr($str,strpos($str,'|')+1);

        //获取字符串中第一个 '|' 符号位置
        $pos2 = strpos($str2,'|');

        //没有找到则$str2这个字符串为域名下全部值
        if(empty($pos2)){
            return unserialize($str2);
        }

        //截取从开始到指定位置的字符串
        $str3 = substr($str2,0,$pos2);

        //通过获取该字符串中最后一个｝的位置，进行截取
        return unserialize(substr($str3,0,strripos($str3,'}')+1));

    }

}