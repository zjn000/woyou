<?php
namespace app\api\controller;

use app\common\controller\Api;
use think\Request;
use think\Session;

class Test extends Api{

    /**
     * 判断手机是否绑定
     */
    public function is_bind(){
        $is_bind = Session::get('userInfo')['is_bind'];
        $this->jsonReturn(200,'成功',$is_bind);
    }


    /**
     * 根据货架id获取货架信息
     */
    public function bind_phone(){

        $appid = 'wx5daf9ce4cabfdaf5';
        $userInfo = Session::get('userInfo');
        $encryptedData = Request::instance()->post('encryptedData');
        $iv = Request::instance()->post('iv');

        if(empty($userInfo['session_key']) || empty($encryptedData) || empty($iv)){
            $this->jsonReturn(400,'参数错误');
        }


        $params = [
            'appid' => $appid,
            'sessionKey' => trim($userInfo['session_key']),
            'encryptedData' => trim($encryptedData),
            'iv' => trim($iv)
        ];

        $errCode = $this->decryptData($params, $data );

        if ($errCode == 0) {

            Db::name('user')->where(['openid'=>$userInfo['openid']])->update(['phone'=>$data['phoneNumber'],'is_bind'=>1]);

            Session::set('userInfo')['is_bind'] = 1;


            $this->jsonReturn(200,'成功');

        } else {
            $this->jsonReturn(401,'失败',$errCode);

        }

    }


    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $params=[
     *      'appid'         string 小程序appid
     *      'sessionKey'    string 用户sessionKey
     *      'encryptedData' string 加密的用户数据
     *      'iv'            string 与用户数据一同返回的初始向量
     * ];
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    private function decryptData( $params,&$data )
    {
        //-41001: encodingAesKey 非法
        //-41003: aes 解密失败
        //-41004: 解密后得到的buffer非法
        //-41005: base64加密失败
        //-41016: base64解密失败
        static $OK = 0;
        static $IllegalAesKey = -41001;
        static $IllegalIv = -41002;
        static $IllegalBuffer = -41003;
        static $DecodeBase64Error = -41004;


        if (strlen($params['sessionKey']) != 24) {
            return $IllegalAesKey;
        }
        $aesKey=base64_decode($params['sessionKey']);


        if (strlen($params['iv']) != 24) {
            return $IllegalIv;
        }
        $aesIV=base64_decode($params['iv']);

        $aesCipher=base64_decode($params['encryptedData']);

        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj=json_decode( $result );
        if( $dataObj  == NULL )
        {
            return $IllegalBuffer;
        }
        if( $dataObj->watermark->appid != $params['appid'] )
        {
            return $IllegalBuffer;
        }
        $data = $result;
        return $OK;
    }



}