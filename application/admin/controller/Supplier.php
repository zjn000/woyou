<?php

namespace app\admin\controller;

use app\common\controller\Backend;

use think\Controller;
use think\Request;

/**
 * 供应商管理
 *
 * @icon fa fa-circle-o
 */
class Supplier extends Backend
{
    
    /**
     * Supplier模型对象
     */
    protected $model = null;
    protected $modelValidate = true;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Supplier');

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

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }
    

}
