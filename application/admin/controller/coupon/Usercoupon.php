<?php

namespace app\admin\controller\coupon;

use app\common\controller\Backend;

use think\Controller;
use think\Request;

/**
 * 用户优惠券管理
 *
 * @icon fa fa-circle-o
 */
class Usercoupon extends Backend
{
    
    /**
     * UserCoupon模型对象
     */
    protected $model = null;

    protected $relationSearch = true;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('UserCoupon');

    }

    /**
     * 查看
     */
    public function index()
    {

        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model->with("Coupon,User")
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model->with("Coupon,User")
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            if(!empty($list)){

                foreach ($list as $k => $v)
                {
                    if($v['coupon']['type'] == 1){
                        $list[$k]['effective_time'] = $v['coupon']['valid_date'];
                        $list[$k]['type_name'] = __('Fixed_days');
                    }

                    if($v['coupon']['type'] == 2){
                        $list[$k]['effective_time'] = date('Y-m-d H:i:s',$v['coupon']['start_time']).'~'.date('Y-m-d H:i:s',$v['coupon']['end_time']);
                        $list[$k]['type_name'] = __('Time_period');
                    }

                    $list[$k]['face_value'] = '满'.$v['coupon']['activation_amount'].'-'.$v['coupon']['discount_amount'];

                }
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch();
    }
    

}
