<?php
namespace app\tools\controller;

use app\common\controller\Toolsapi;
use fast\Random;
use think\Cookie;
use think\Db;
use think\Request;
use think\Session;

class User extends Toolsapi{


    /**
     * 登录
     * POST
     * @param string username  用户名
     * @param string password  密码
     * GET
     * @param string sign 签名
     */
    public function login(){

        $request = Request::instance();

        if(empty($request->route()['sign'])){
            send_json(0,'参数异常');
        }


        if($this->check_sign($request->route()['sign'],$request->post()) === false){
            send_json(0,'数据包异常');
        }

        $username = $request->post('username');
        $password = $request->post('password');

        $admin = Db::name('admin')->field('id,username,nickname,password,salt,loginfailure,logintime,token,status,updatetime')
            ->where(['username' => $username,'status'=>'normal'])->find();

        if(empty($admin)){
            send_json(0,'用户名错误');
        }

        //判断密码
        if ($admin['password'] != md5(md5($password) . $admin['salt']))
        {
            //记录密码错误次数
            $admin['loginfailure']++;

            if($admin['loginfailure']>5){
                send_json(0,'密码错误频繁，请联系技术部');
            }

            $admin['updatetime'] = time();
            Db::name('admin')->update($admin);
            send_json(0,'密码错误');
        }

        //登录成功则刷新错误次数
        $admin['loginfailure'] = 0;
        //最后登录时间以及更新时间刷新
        $admin['logintime'] = time();
        $admin['updatetime'] = time();
        //token刷新
        $admin['token'] = Random::uuid();

        Db::name('admin')->update($admin);

        Session::set("admin", $admin);
        //刷新cookie保持登录7天
        $keep_tools_login = $this->keeplogin($admin,604800);
        send_json(1,'登录成功',['keep_tools_login'=>$keep_tools_login]);
    }

    /**
     * 退出
     */
    public function drop_out(){

        if(!$this->isLogin()){
            send_json(1,'退出成功');
        }

        $admin = Db::name('admin')->find(intval(Session::get('admin')['id']));

        if (!$admin)
        {
            send_json(1,'退出成功');
        }

        //最后登录时间以及更新时间刷新
        $admin['logintime'] = time();
        $admin['updatetime'] = time();
        //token刷新
        $admin['token'] = '';

        Db::name('admin')->update($admin);
        //清除session中数据
        $_SESSION=[];
        //删除cookie
        Cookie::delete("keep_tools_login");

        send_json(1,'退出成功');
    }







}