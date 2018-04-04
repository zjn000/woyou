<?php

namespace app\admin\validate;

use think\Validate;

class ProductCategory extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'name'              => "require|unique:supplier"
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'name.require'   => '必填',
        'name.unique'   => '分类已存在'
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => [],
        'edit' => [],
    ];
}
