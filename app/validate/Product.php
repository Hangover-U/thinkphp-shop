<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Product extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' => ['规则1', '规则2', ...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require|max:100',
        'category_id' => 'require|number',
        'price' => 'require|float|gt:0',
        'stock' => 'require|integer|egt:0',
        'description' => 'max:1000',
        'manufacturer' => 'max:100',
        'is_on_sale' => 'in:0,1',
        'is_recommend' => 'in:0,1',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' => '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '商品名称不能为空',
        'name.max' => '商品名称最多不能超过100个字符',
        'category_id.require' => '请选择商品分类',
        'category_id.number' => '商品分类ID必须是数字',
        'price.require' => '商品价格不能为空',
        'price.float' => '商品价格必须是浮点数',
        'price.gt' => '商品价格必须大于0',
        'stock.require' => '商品库存不能为空',
        'stock.integer' => '商品库存必须是整数',
        'stock.egt' => '商品库存必须大于等于0',
        'description.max' => '商品描述最多不能超过1000个字符',
        'manufacturer.max' => '厂商名称最多不能超过100个字符',
        'is_on_sale.in' => '上架状态只能是0或1',
        'is_recommend.in' => '推荐状态只能是0或1',
    ];
    
    /**
     * 定义验证场景
     *
     * @var array
     */
    protected $scene = [
        'edit' => ['name', 'category_id', 'price', 'stock', 'description', 'manufacturer', 'is_on_sale', 'is_recommend'],
    ];
} 