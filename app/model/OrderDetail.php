<?php
namespace app\model;

use think\Model;

class OrderDetail extends Model
{
    // 设置表名
    protected $name = 'order_detail';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 关联订单模型
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    
    /**
     * 关联商品模型
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
} 