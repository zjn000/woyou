<?php

namespace app\admin\validate;

use think\Validate;

class Product extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'barcode'              => "require|unique:product",
        'name'                 => "require|chsAlphaNum"

    ];
    /**
     * 提示消息
     */
    protected $message = [
        'barcode.require'   => '必填',
        'barcode.unique'   => '商品条形码已存在',
        'name.require'   => '必填',
        'name.chsAlphaNum'   => '名称必须是中文，字母数字'
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => [],
        'edit' => [],
    ];
    
}
