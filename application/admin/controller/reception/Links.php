<?php

namespace app\admin\controller\reception;

use app\common\controller\Backend;

use think\Controller;
use think\Request;

/**
 * 链接生成管理
 *
 * @icon fa fa-circle-o
 */
class Links extends Backend
{
    
    /**
     * Links模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Links');

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个方法
     * 因此在当前控制器中可不用编写增删改查的代码,如果需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");

            if ($params)
            {
                $time = time();
                $data = [
                    'name'=> trim($params['name']),
                    'links'=> "https://www.stwoyou.com/web/{$time}.html",
                    'image'=> $params['image1'].'|'.$params['image2'].'|'.$params['image3'],
                    'create_id'=> $this->auth->id,
                    'create_time'=> $time
                ];
                    //H5页面
                $str = <<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{$params['name']}</title>
    <style>
        body {
            margin: 0;
        }
        .bg img {
            display: block;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="bg">
    <img src="{$params['image1']}" alt="">
    <img src="{$params['image2']}" alt="">
    <img src="{$params['image3']}" alt="">
</div>
</body>
</html>
EOF;
                try{
                    $rs = file_put_contents(ROOT_PATH.'/public/web/'.$time.'.html',$str);

                    if($rs === false){
                        $this->error('生成html文件时失败');
                    }

                    $result = $this->model->save($data);
                    if ($result !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error($this->model->getError());
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
