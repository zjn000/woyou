<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\admin\model\Admin;
use think\Controller;
use think\Request;

/**
 * 货架管理管理
 *
 * @icon fa fa-circle-o
 */
class Shelves extends Backend
{
    
    /**
     * Shelves模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Shelves');

        $adminName = Admin::where('')->column('id,nickname');

        $this->view->assign('groupdata', $adminName);
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

            foreach ($list as $k => $v)
            {
                $list[$k]['bd_name'] = isset($adminName[$v['bd']]) ? $adminName[$v['bd']] : '';
                $list[$k]['URL'] = 'https://www.stwoyou.com/applets/'.$v['id'];
            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * Selectpage搜索
     *
     * @internal
     */
    public function selectpage()
    {
        return parent::selectpage();
    }













}
