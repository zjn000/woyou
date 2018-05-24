<?php

namespace app\api\model;

use think\Exception;
use think\Model;
use think\Db;

class UserCoupon extends Model
{
    // 表名
    protected $name = 'user_coupon';

    /**
     * 获取用户有效优惠券
     * @param int $uid      用户id
     * @param int $price    使用限制金额，默认为0
     * @param bool $all    是否获取全部，true 全部,false 返回优惠金额最大的一张券
     * @return null|array 有效优惠券信息
     */
    public function getValidCoupon($uid = 0,$price = 0,$all = true){

        //获取用户所有未使用的券is_use
        $data = $this->field('id,uid,coupon_id,create_time')->where(['uid'=>$uid,'is_use'=>0])->select();

        //判断是否存在
        if(empty($data)){
            return null;
        }

        //得到用户未使用优惠券coupon_id组
        foreach ($data as $key=>$item){
            $arrCouponIds[$key]=$item['coupon_id'];
        }
        //去重
        $couponIds = array_unique($arrCouponIds);


        //根据coupon_id组获取有效的，上架中的优惠券信息
        $now = time();
        $couponList = Db::name('coupon')
            ->where(['status'=> 1])
            ->where(['id'=> ['IN',$couponIds]])
            ->where('activation_amount>discount_amount')
            ->where('type=1 OR type=2 AND '.$now.' BETWEEN start_time AND end_time')
            ->field('id,name,activation_amount,discount_amount,type,valid_date,start_time,end_time')
            ->select();

        //判断是否存在
        if(empty($couponList)){
            return null;
        }


        //得到上架优惠券信息集合，得到优惠券信息集合
        foreach ($couponList as $k=>$value){
            $arrCoupon[$value['id']] = $value;
        }

        //初始化变量
        $list = null;

        //循环用户所有未使用的券，去除过期优惠券，将有效的券拼凑成集合
        foreach ($data as $key=>$row){

            //判断用户未使用的券是否仍然有效
            if (isset($arrCoupon[$row['coupon_id']])){

                //当有传入金额时且金额必须大于0
                if ($price>0){
                    //判断优惠券的激活金额是否大于使用限制，若是，则过滤这张券（结束当次循环）
                    if ($arrCoupon[$row['coupon_id']]['activation_amount']>$price){
                        continue;
                    }
                }

                //当券的类型为固定天数时，需要过滤失效券
                if ($arrCoupon[$row['coupon_id']]['type'] == 1){

                    //激活时间
                    $time1 = $row['create_time'];
                    //最大有效时间
                    $time2 = $row['create_time']+$arrCoupon[$row['coupon_id']]['valid_date']*86400;

                    //判断当前时间是否在激活时间与最大有效时间内
                    if($now>$time1 && $now<$time2){

                        $list[$key] = [
                            'id' => $row['id'],
                            'name' => $arrCoupon[$row['coupon_id']]['name'],
                            'num1' => $arrCoupon[$row['coupon_id']]['activation_amount'],
                            'num2' => $arrCoupon[$row['coupon_id']]['discount_amount'],
                            'start_time' => date('Y-m-d',$time1),
                            'end_time' => date('Y-m-d',$time2)
                        ];

                    }
                }
                else{
                    //当券的类型为固定时间段
                    $list[$key] = [
                        'id' => $row['id'],
                        'name' => $arrCoupon[$row['coupon_id']]['name'],
                        'num1' => $arrCoupon[$row['coupon_id']]['activation_amount'],
                        'num2' => $arrCoupon[$row['coupon_id']]['discount_amount'],
                        'start_time' => date('Y-m-d',$arrCoupon[$row['coupon_id']]['start_time']),
                        'end_time' => date('Y-m-d',$arrCoupon[$row['coupon_id']]['end_time'])
                    ];
                }
            }
        }

        //判断是否存在
        if(empty($list)){
            return null;
        }

        //是否全部
        if($all){
            return array_values($list);
        }

        //先获取数组中num2列的值组成新的数组（不保留索引）
        $sort = array_column($list, 'num2');
        //按列排序数组list
        array_multisort($sort, SORT_ASC, $list);
        //得到最后一个元素
        return end($list);

    }

    /**
     * 推送用户一张优惠券
     * @param int $uid          用户id
     * @param int $coupon_id    优惠券id
     * @return bool 成功true 失败false
     */
    public function pushCoupon($uid = 0,$coupon_id=0){
        $data = array(
            'uid' => $uid,
            'coupon_id' => intval($coupon_id),
            'is_use' => 0,
            'create_time' => time()
        );

        try{
            Db::name('user_coupon')->insert($data);
        }catch (Exception $e){
            return false;
        }

        return true;
    }




}
