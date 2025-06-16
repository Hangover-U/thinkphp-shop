<?php
namespace app\model;

use think\Model;

class Order extends Model
{
    // 设置表名
    protected $name = 'order';
    
    // 设置软删除
    use \think\model\concern\SoftDelete;
    protected $deleteTime = 'delete_time';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    // 订单状态常量
    const STATUS_UNPAID = 0;    // 待付款
    const STATUS_PAID = 1;      // 已付款
    const STATUS_SHIPPED = 2;   // 已发货
    const STATUS_COMPLETED = 3; // 已完成
    const STATUS_CANCELLED = 4; // 已取消
    
    /**
     * 关联用户模型
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * 关联订单详情模型
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
    
    /**
     * 生成订单号
     * @return string
     */
    public static function generateOrderNo()
    {
        return date('YmdHis') . mt_rand(1000, 9999);
    }
    
    /**
     * 创建订单
     * @param int $userId 用户ID
     * @param string $address 收货地址
     * @param string $consignee 收货人
     * @param string $mobile 联系电话
     * @return array|bool 成功返回订单信息，失败返回false
     */
    public static function createOrder($userId, $address, $consignee, $mobile)
    {
        // 获取用户选中的购物车商品
        $cartItems = Cart::with('product')
            ->where('user_id', $userId)
            ->where('selected', 1)
            ->select();
            
        if ($cartItems->isEmpty()) {
            return false;
        }
        
        // 检查库存
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return false;
            }
        }
        
        // 开启事务
        $order = null;
        \think\facade\Db::startTrans();
        try {
            // 创建订单
            $order = new self();
            $order->order_no = self::generateOrderNo();
            $order->user_id = $userId;
            $order->total_amount = Cart::calculateTotal($userId, true);
            $order->status = self::STATUS_UNPAID;
            $order->address = $address;
            $order->consignee = $consignee;
            $order->mobile = $mobile;
            $order->save();
            
            // 创建订单详情
            foreach ($cartItems as $item) {
                // 确保商品图片路径正确
                $productImage = $item->product->image;
                
                // 如果是/storage/路径，统一修改为/static/路径
                if (strpos($productImage, '/storage/uploads/product/') === 0) {
                    // 根据商品名称选择合适的默认图片
                    if (strpos(strtolower($item->product->name), '手机') !== false) {
                        $productImage = '/static/uploads/product/phone.jpg';
                    } elseif (strpos(strtolower($item->product->name), '笔记本') !== false || 
                             strpos(strtolower($item->product->name), '电脑') !== false) {
                        $productImage = '/static/uploads/product/laptop.jpg';
                    } else {
                        $productImage = '/static/uploads/product/default.jpg';
                    }
                }
                // 修复Windows路径分隔符
                else if (strpos($productImage, '\\') !== false) {
                    $productImage = str_replace('\\', '/', $productImage);
                }
                // 如果不是以/static/开头，使用默认图片
                else if (strpos($productImage, '/static/') !== 0) {
                    $productImage = '/static/uploads/product/default.jpg';
                }
                
                $detail = new OrderDetail();
                $detail->order_id = $order->id;
                $detail->product_id = $item->product_id;
                $detail->product_name = $item->product->name;
                $detail->product_image = $productImage;
                $detail->product_price = $item->product->price;
                $detail->quantity = $item->quantity;
                $detail->total_price = $item->product->price * $item->quantity;
                $detail->save();
                
                // 减少库存，增加销量
                $item->product->stock -= $item->quantity;
                $item->product->sales += $item->quantity;
                $item->product->save();
            }
            
            // 清空已选中的购物车商品
            Cart::where('user_id', $userId)
                ->where('selected', 1)
                ->delete();
                
            \think\facade\Db::commit();
            return $order->toArray();
        } catch (\Exception $e) {
            \think\facade\Db::rollback();
            return false;
        }
    }
    
    /**
     * 支付订单
     * @param int $orderId 订单ID
     * @param int $userId 用户ID
     * @return bool
     */
    public static function payOrder($orderId, $userId)
    {
        $order = self::where('id', $orderId)
            ->where('user_id', $userId)
            ->where('status', self::STATUS_UNPAID)
            ->find();
            
        if (!$order) {
            return false;
        }
        
        $order->status = self::STATUS_PAID;
        $order->pay_time = date('Y-m-d H:i:s');
        return $order->save();
    }
    
    /**
     * 取消订单
     * @param int $orderId 订单ID
     * @param int $userId 用户ID
     * @return bool
     */
    public static function cancelOrder($orderId, $userId)
    {
        $order = self::where('id', $orderId)
            ->where('user_id', $userId)
            ->where('status', self::STATUS_UNPAID)
            ->find();
            
        if (!$order) {
            return false;
        }
        
        // 开启事务
        \think\facade\Db::startTrans();
        try {
            // 修改订单状态
            $order->status = self::STATUS_CANCELLED;
            $order->save();
            
            // 恢复库存，减少销量
            $details = OrderDetail::where('order_id', $orderId)->select();
            foreach ($details as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $product->stock += $detail->quantity;
                    $product->sales -= $detail->quantity;
                    $product->save();
                }
            }
            
            \think\facade\Db::commit();
            return true;
        } catch (\Exception $e) {
            \think\facade\Db::rollback();
            return false;
        }
    }
    
    /**
     * 获取用户订单列表
     * @param int $userId 用户ID
     * @param int $status 订单状态，null表示全部
     * @param int $page 页码
     * @param int $pageSize 每页数量
     * @return array
     */
    public static function getUserOrders($userId, $status = null, $page = 1, $pageSize = 10)
    {
        $query = self::with('orderDetails')
            ->where('user_id', $userId);
            
        if ($status !== null) {
            $query->where('status', $status);
        }
        
        return $query->order('id', 'desc')
            ->paginate([
                'list_rows' => $pageSize,
                'page' => $page,
            ]);
    }
} 