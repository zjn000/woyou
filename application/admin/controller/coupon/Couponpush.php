<?php

namespace app\admin\controller\coupon;

use app\common\controller\Backend;
use app\admin\model\Admin;

/**
 * 优惠券人工推送记录
 *
 * @icon fa fa-circle-o
 */
class Couponpush extends Backend
{
    
    /**
     * CouponPush模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('CouponPush');

    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $adminName = Admin::where('')->column('id,nickname');

            if(!empty($list)){
                foreach ($list as $k => $v)
                {
                    $list[$k]['admin_id'] = isset($adminName[$v['admin_id']]) ? $adminName[$v['admin_id']] : '';
                }
            }

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }
    

}
