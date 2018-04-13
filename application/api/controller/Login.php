<?php
namespace app\api\controller;

use app\common\controller\Publicapi;
use fast\Http;
use think\Request;
use think\Session;
use think\Db;


/**
 * 登录
 * Date: 2017/11/16 0016
 * Time: 14:00
 */
class Login extends Publicapi {

    const GET_ACCESS_TOKEN_URL = "https://api.weixin.qq.com/sns/jscode2session";

    /**
     *登录
     */
    public function index()
    {
        $code = Request::instance()->post('code');

        if(empty($code))
            $this->jsonReturn(400,'缺少code参数');

        $userInfo = $this->getsessionkey($code);

        if(isset($userInfo['errcode']))
            $this->jsonReturn(404,'获取用户信息出错');


        //获取用户信息
        $user = Db::name('user')->where('openid',$userInfo['openid'])->find();

        //第一次登录
        if(empty($user)){
            $data = array(
                'openid' => $userInfo['openid'],
                'status' => 1,
                'create_time' => time()
            );
            //创建用户
            $userid = Db::name('user')->insertGetId($data);
            $user = Db::name('user')->find($userid);
        }

        if($user['status'] == 2){
            $this->jsonReturn('200','你已被列入黑名单');
        }

        //更新登录时间
        Db::name('user')->where(['openid'=>$userInfo['openid']])->update(['login_time'=>time()]);

        $user['session_key'] = $userInfo['session_key'];

        //保存用户信息到会话中
        Session::set('userInfo',$user);

        //会话标志，作为用户令牌
        $sign = session_id();
        $this->jsonReturn(200,'成功',['token'=>$sign,'is_bind'=>$user['is_bind']]);
    }


    //  获取sessionkey 接口
    //***************************
    private function getsessionkey($code = ''){
        if (!$code)
            return [];
        $queryarr = array(
            "appid"      => 'wx5daf9ce4cabfdaf5',
            "secret"     => '00b07427db71490dbceffdf7cee0e8ef',
            "js_code"       => $code,
            "grant_type" => "authorization_code",
        );

        $response = Http::get(self::GET_ACCESS_TOKEN_URL, $queryarr);
        $ret = json_decode($response, TRUE);
        return $ret ? $ret : [];
    }



}

