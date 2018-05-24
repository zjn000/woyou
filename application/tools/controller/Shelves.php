<?php
namespace app\tools\controller;

use app\common\controller\Toolsapi;
use think\Db;
use think\Request;

class Shelves extends Toolsapi{


    /**
     * 获取货架列表
     * GET
     * @param integer   p       页码
     * @param string    sign    签名
     */
    public function get_list(){

        $param = Request::instance()->route();

        if(empty($param['sign']) || empty($param['p'])){
            send_json(0,'参数异常');
        }

        $page = intval($param['p']);

        if($this->check_sign($param['sign'],$param) === false){
            send_json(0,'数据包异常');
        }

        if($this->check_time('tools_shelves_get_list',2) === false){
            send_json(0,'操作太频繁');
        }

        $data = Db::name('shelves')->field('id,name,adds,principal,tell')->where(['status'=>1])->page($page,10)->select();

        $this->set_check_time('tools_shelves_get_list');

        send_json(1,'成功',$data);
    }

    /**
     * 搜索货架
     * GET
     * @param string    s       关键字
     * @param integer   p       页码
     * @param string    sign    签名
     */
    public function search(){

        $param = Request::instance()->route();

        if(empty($param['sign']) || empty($param['p']) || empty($param['s'])){
            send_json(0,'参数异常');
        }

        $name = trim($param['s']);
        $page = intval($param['p']);

        if($this->check_sign($param['sign'],$param) === false){
            send_json(0,'数据包异常');
        }


        if($this->check_time('tools_shelves_search',2) === false){
            send_json(0,'操作太频繁');
        }

        $data = Db::name('shelves')->field('id,name,adds,principal,tell')->where(['status'=>1,'name'=>['like','%'.$name.'%']])->page($page,10)->select();

        $this->set_check_time('tools_shelves_search');

        send_json(1,'成功',$data);
    }

}