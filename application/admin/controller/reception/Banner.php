<?php

namespace app\admin\controller\reception;
use app\common\controller\Backend;

/**
 * 轮播图管理
 *
 * @icon fa fa-circle-o
 */
class Banner extends Backend
{
    
    /**
     * Banner模型对象
     */
    protected $model = null;

    protected $searchFields = 'id,name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Banner');

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个方法
     * 因此在当前控制器中可不用编写增删改查的代码,如果需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 删除
     */
    public function del($ids = "")
    {

        if ($ids)
        {
            $deleteIds = explode(',',$ids);
            $rs = $this->model->destroy($deleteIds);

            if($rs){
                $this->success();
            }
        }
        $this->error();
    }
}
