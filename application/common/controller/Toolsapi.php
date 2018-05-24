<?php

namespace app\common\controller;


use fast\Auth;
use think\controller\Rest;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;

class Toolsapi extends Rest
{

    public function __construct()
    {
        parent::__construct();

        $requset = Request::instance();

        $modulename = $requset->module();
        $controllername = strtolower($requset->controller());
        $actionname = strtolower($requset->action());

        $str = $modulename.'/'.$controllername.'/'.$actionname;

        //接口访问频次配置
        //name:标识名 second：间隔时间（秒） times:最大次数
        $array_frequency_config = [
            'tools/user/login' => ['name'=>'tools_user_login','second'=>300,'times'=>10],
            'tools/user/drop_out' => ['name'=>'tools_user_drop_out','second'=>300,'times'=>10],
            'tools/shelves/get_list' => ['name'=>'tools_shelves_get_list','second'=>3,'times'=>5],
            'tools/shelves/search' => ['name'=>'tools_shelves_search','second'=>30,'times'=>10],
            'tools/product/get_list' => ['name'=>'tools_product_get_list','second'=>3,'times'=>5],
            'tools/product/search' => ['name'=>'tools_product_search','second'=>30,'times'=>10],
            'tools/product/save' => ['name'=>'tools_product_save','second'=>5,'times'=>2]
        ];


        $rs = $this->check_frequency($array_frequency_config[$str]['name'],$array_frequency_config[$str]['second'],$array_frequency_config[$str]['times']);

        if($rs === false){
            send_json(0,'操作太频繁，请'.$array_frequency_config[$str]['second'].'秒后再进行操作');
        }


        //无须验证登录
        $noNeedLogin = [
            'tools/user/login',
            'tools/user/drop_out',
            'tools/shelves/search',
            'tools/product/search',
        ];
        $noNeedCheck = [];

        // 检测是否需要验证登录
        if (!$this->match($noNeedLogin,$str))
        {
            //检测是否登录
            if (!$this->isLogin())
            {
                $this->autologin();
            }
            // 判断是否需要验证权限
            if (!$this->match($noNeedCheck,$str))
            {
                $auth = Auth::instance();
                $uid= Session::get('admin')['id'];
                // 判断控制器和方法判断是否有对应权限
                if (!$auth->check($str,$uid))
                {
                    send_json(0,'无权限操作');
                }
            }
        }
    }


    /**
     * 检测是否登录
     *
     * @return boolean
     */
    protected function isLogin()
    {
        return Session::get('admin') ? true : false;
    }


    /**
     * 自动登录
     * @return boolean
     */
    public function autologin()
    {
        $requset = Request::instance()->route();

        $keeplogin = $requset['keep_tools_login'];
        if(empty($requset['keep_tools_login'])){
            $keeplogin = Cookie::get('keep_tools_login');
        }

        if (!$keeplogin)
        {
            send_json(-1,'找不到cookie');
        }

        //解密
        $keeplogin = base64_decode(str_pad(strtr($keeplogin, '-_', '+/'), strlen($keeplogin) % 4, '=', STR_PAD_RIGHT));
        $keeplogin = authcode($keeplogin,'DECODE','f5d659trr02152v50zdfa366');


        list($id, $keeptime, $expiretime, $key) = explode('|', $keeplogin);
        if ($id && $keeptime && $expiretime && $key && $expiretime > time())
        {

            $admin = Db::name('admin')->find($id);

            if (empty($admin) || empty($admin['token']))
            {
                send_json(-1,'用户找不到');
            }
            //token有变更
            if ($key != md5(md5($id) . md5($keeptime) . md5($expiretime) . $admin['token']))
            {
                send_json(-1,'cookie失效');
            }
            Session::set("admin", $admin);
            //刷新自动登录的时效
            $this->keeplogin($admin,$keeptime);
        }
        else
        {
            send_json(-1,'cookie失效');
        }
    }


    /**
     * 刷新保持登录的Cookie
     * @param array $admin  管理员信息
     * @param int $keeptime 保存时间（秒）
     * @return boolean
     */
    protected function keeplogin($admin,$keeptime = 0)
    {
        if ($keeptime)
        {
            $expiretime = time() + $keeptime;
            $key = md5(md5($admin['id']) . md5($keeptime) . md5($expiretime) . $admin['token']);
            $data = [$admin['id'], $keeptime, $expiretime, $key];

            //加密
            $cookie_var = authcode(implode('|', $data),'ENCODE','f5d659trr02152v50zdfa366');
            $cookie_var = rtrim(strtr(base64_encode($cookie_var), '+/', '-_'), '=');
            Cookie::set('keep_tools_login', $cookie_var);
            return $cookie_var;
        }
    }


    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     * @param string $str 当前规则
     */
    public function match($arr = [],$str = '')
    {

        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr)
        {
            return FALSE;
        }

        // 是否存在
        if (in_array($str, $arr))
        {
            return TRUE;
        }

        // 没找到匹配
        return FALSE;
    }


    /**
     * 业务逻辑上设置访问接口时的时间
     * @param string $time_name 访问名
     * @return bool
     */
    protected function set_check_time($time_name = null){

        if(empty($time_name)){
            return false;
        }

        Session::set($time_name.'_time',time());

        return true;
    }


    /**
     * 用于业务逻辑上访问的检测
     * @param string $time_name 访问名
     * @param integer $min_time    最小访问间隔时间
     * @param integer $max_time    最大访问间隔时间
     * @return bool
     */
    protected function check_time($time_name = null, $min_time = null, $max_time = null){

        if(empty($time_name)){
            return false;
        }

        $now_time = time();

        $temp_time = Session::get($time_name.'_time');

        if(empty($temp_time)){
            return true;
        }

        if(!empty($min_time) && (($now_time - $temp_time) < $min_time)){
            return false;
        }

        if(!empty($max_time) && (($now_time - $temp_time) > $max_time)){
            return false;
        }

        return true;
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


    /**
     * 检测签名，验证数据完整
     * @param string $sign    签名
     * @param array $data     参数数据
     * @return bool   true 数据正常 | false 数据异常
     */
    protected function check_sign($sign = '',$data = [])
    {

        if(empty($data) || empty($sign))
        {
            return false;
        }

        //签名步骤一：按字典序排序参数
        ksort($data);

        $string = "";
        foreach ($data as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $string .= $k . "=" . $v . "&";
            }
        }

        $string = trim($string, "&");

        //签名步骤二：在string后加入KEY
        $string = $string . "&key=dsfj2FH4544DHJa34d01f511ng9d451f";
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);

        if($sign !== $result){
            return false;
        }

        return true;

    }
}
