<?php
namespace app\model;

use think\Model;

class Cart extends Model
{
    // 设置表名
    protected $name = 'cart';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 关联用户模型
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * 关联商品模型
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    /**
     * 获取用户购物车
     * @param int $userId 用户ID
     * @return array
     */
    public static function getUserCart($userId)
    {
        return self::with('product')
            ->where('user_id', $userId)
            ->select()
            ->toArray();
    }
    
    /**
     * 添加商品到购物车
     * @param int $userId 用户ID
     * @param int $productId 商品ID
     * @param int $quantity 数量
     * @return bool
     */
    public static function addToCart($userId, $productId, $quantity = 1)
    {
        // 检查商品是否存在且上架
        $product = Product::where('id', $productId)
            ->where('is_on_sale', 1)
            ->find();
            
        if (!$product) {
            return false;
        }
        
        // 检查库存
        if ($product->stock < $quantity) {
            return false;
        }
        
        // 检查购物车是否已存在该商品
        $cart = self::where('user_id', $userId)
            ->where('product_id', $productId)
            ->find();
            
        if ($cart) {
            // 更新数量
            $cart->quantity += $quantity;
            return $cart->save();
        } else {
            // 新增购物车项
            $cart = new self();
            $cart->user_id = $userId;
            $cart->product_id = $productId;
            $cart->quantity = $quantity;
            $cart->selected = 1;
            return $cart->save();
        }
    }
    
    /**
     * 更新购物车商品数量
     * @param int $userId 用户ID
     * @param int $cartId 购物车ID
     * @param int $quantity 数量
     * @return bool
     */
    public static function updateQuantity($userId, $cartId, $quantity)
    {
        if ($quantity <= 0) {
            return self::removeFromCart($userId, $cartId);
        }
        
        $cart = self::where('id', $cartId)
            ->where('user_id', $userId)
            ->find();
            
        if (!$cart) {
            return false;
        }
        
        // 检查库存
        $product = Product::find($cart->product_id);
        if (!$product || $product->stock < $quantity) {
            return false;
        }
        
        $cart->quantity = $quantity;
        return $cart->save();
    }
    
    /**
     * 从购物车中移除商品
     * @param int $userId 用户ID
     * @param int $cartId 购物车ID
     * @return bool
     */
    public static function removeFromCart($userId, $cartId)
    {
        return self::where('id', $cartId)
            ->where('user_id', $userId)
            ->delete();
    }
    
    /**
     * 清空购物车
     * @param int $userId 用户ID
     * @return bool
     */
    public static function clearCart($userId)
    {
        return self::where('user_id', $userId)->delete();
    }
    
    /**
     * 更新购物车商品选中状态
     * @param int $userId 用户ID
     * @param int $cartId 购物车ID
     * @param int $selected 是否选中：0否，1是
     * @return bool
     */
    public static function updateSelected($userId, $cartId, $selected)
    {
        return self::where('id', $cartId)
            ->where('user_id', $userId)
            ->update(['selected' => $selected ? 1 : 0]);
    }
    
    /**
     * 计算购物车总金额
     * @param int $userId 用户ID
     * @param bool $onlySelected 是否只计算选中的商品
     * @return float
     */
    public static function calculateTotal($userId, $onlySelected = true)
    {
        $query = self::with('product')
            ->where('user_id', $userId);
            
        if ($onlySelected) {
            $query->where('selected', 1);
        }
        
        $cartItems = $query->select();
        
        $total = 0;
        foreach ($cartItems as $item) {
            if ($item->product) {
                $total += $item->product->price * $item->quantity;
            }
        }
        
        return $total;
    }
} 