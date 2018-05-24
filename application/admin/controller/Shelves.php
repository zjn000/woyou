<?php

namespace app\admin\controller;

use app\common\behavior\Common;
use app\common\controller\Backend;
use app\admin\model\Admin;
use think\Controller;
use think\Db;
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



    /**
     * 导出盘点
     */
    public function export_inventory($ids=null){

        $row_shelves = $this->model->get($ids);
        if (!$row_shelves)
            $this->error(__('No Results were found'));

        $list = Db::name('shelves_product')->alias('a')
            ->field('a.product_id,a.standard_number,a.num,a.lv,b.name,b.barcode')
            ->where("a.shelves_id={$ids} and a.status=1")
            ->order("a.lv asc")
            ->join('__PRODUCT__ b','a.product_id=b.id')
            ->select();

        if (!$list)
            $this->error(__('没有上线商品'));

        $headList = ['货架名称','所属层数','商品名称','商品条形码','标准数量','剩余数量','应补货数量'];

        //层数名称
        $lv_name_list = [
            0=>'',
            1=>'第1层',
            2=>'第2层',
            3=>'第3层',
            4=>'第4层',
            5=>'第5层',
            6=>'冰箱1层',
            7=>'冰箱2层',
            8=>'冰箱3层',
            9=>'冰箱4层'
        ];


        $data = [];
        //注意：纯数字超过11位变科学计数法，超过15位则其后数字将变为0，解决方案，在数据前加上"\t"将其变成字符串
        foreach ($list as $key=>$row){

            if($row['standard_number']-$row['num'] > 0){
                $data[$key] = [
                    $row_shelves['name'],
                    $lv_name_list[$row['lv']],
                    $row['name'],
                    "\t".$row['barcode'],
                    $row['standard_number'],
                    $row['num'],
                    $row['standard_number']-$row['num']
                ];
            }
        }

        Common::exportToCsv($row_shelves['name'].'_'.date('Y-m-d').'.csv',$headList,$data);
    }


    /**
     * 复制A货架的上线商品到B货架上
     */
    public function copy(){

        $shelves_list = Db::name('shelves')->where(['status'=>1])->order('id desc')->column('id,name');
        $this->view->assign('shelvesdata', $shelves_list);

        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");

            if ($params)
            {

                $a = intval($params['a']);
                $b = intval($params['b']);

                $time = time();
                $uid = $this->auth->id;

                if($a == $b){
                    $this->error('货架不能为同一个');
                }

                $a_list = Db::name('shelves_product')->field('product_id,standard_number,num,lv,status')->where(['shelves_id'=>$a,'status'=>1])->select();

                if(empty($a_list)){
                    $this->error('源货架内没有上线的商品数据');
                }

                $b_list = Db::name('shelves_product')->field('shelves_id')->where(['shelves_id'=>$b])->select();

                if(!empty($b_list)){
                    $this->error('目标货架内已有商品数据，请清空后重试');
                }


                foreach ($a_list as $key=>$row){
                    $data[$key]=array(
                        'shelves_id' => $b,
                        'product_id' => $row['product_id'],
                        'standard_number'=>$row['standard_number'],
                        'num'=>0,
                        'lv'=>$row['lv'] ,
                        'status'=>1,
                        'create_id'=>$uid,
                        'create_time'=>$time
                    );
                }

                try
                {
                    $result = Db::name('shelves_product')->insertAll($data);
                    if ($result !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error('系统繁忙');
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }






}
