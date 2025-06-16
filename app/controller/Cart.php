<?php
declare (strict_types = 1);

namespace app\controller;

use app\model\Cart as CartModel;
use app\model\User;
use think\facade\View;
use think\facade\Session;
use app\controller\BaseController;

class Cart extends BaseController
{
    /**
     * 购物车页面
     */
    public function index()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = \think\facade\Cookie::get('user_id');
        if (!$userId) {
            return redirect('/user/login');
        }
        
        // 获取购物车商品
        $cartItems = CartModel::getUserCart($userId);
        View::assign('cartItems', $cartItems);
        
        // 计算总金额
        $totalAmount = CartModel::calculateTotal($userId);
        View::assign('totalAmount', $totalAmount);
        
        return view();
    }
    
    /**
     * 添加商品到购物车
     */
    public function add()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = \think\facade\Cookie::get('user_id');
        if (!$userId) {
            return $this->error('请先登录');
        }
        
        $productId = $this->request->param('product_id/d', 0);
        $quantity = $this->request->param('quantity/d', 1);
        
        if ($productId <= 0 || $quantity <= 0) {
            return $this->error('参数错误');
        }
        
        // 添加到购物车
        $result = CartModel::addToCart($userId, $productId, $quantity);
        if ($result === false) {
            return $this->error('添加失败，商品可能已下架或库存不足');
        }
        
        return $this->success('添加成功');
    }
    
    /**
     * 更新购物车商品数量
     */
    public function update()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = \think\facade\Cookie::get('user_id');
        if (!$userId) {
            return $this->error('请先登录');
        }
        
        $cartId = $this->request->param('cart_id/d', 0);
        $quantity = $this->request->param('quantity/d', 1);
        
        if ($cartId <= 0) {
            return $this->error('参数错误');
        }
        
        // 更新数量
        $result = CartModel::updateQuantity($userId, $cartId, $quantity);
        if ($result === false) {
            return $this->error('更新失败，商品可能已下架或库存不足');
        }
        
        return $this->success('更新成功');
    }
    
    /**
     * 更新购物车商品数量 (与前端JS匹配)
     */
    public function updateQuantity()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = \think\facade\Cookie::get('user_id');
        if (!$userId) {
            return $this->error('请先登录');
        }
        
        $cartId = $this->request->param('cart_id/d', 0);
        $quantity = $this->request->param('quantity/d', 1);
        
        if ($cartId <= 0) {
            return $this->error('参数错误');
        }
        
        // 更新数量
        $result = CartModel::updateQuantity($userId, $cartId, $quantity);
        if ($result === false) {
            return $this->error('更新失败，商品可能已下架或库存不足');
        }
        
        return $this->success('更新成功');
    }
    
    /**
     * 从购物车中移除商品
     */
    public function remove()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = \think\facade\Cookie::get('user_id');
        if (!$userId) {
            return $this->error('请先登录');
        }
        
        $cartId = $this->request->param('cart_id/d', 0);
        
        if ($cartId <= 0) {
            return $this->error('参数错误');
        }
        
        // 移除商品
        $result = CartModel::removeFromCart($userId, $cartId);
        if ($result === false) {
            return $this->error('移除失败');
        }
        
        return $this->success('移除成功');
    }
    
    /**
     * 清空购物车
     */
    public function clear()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = \think\facade\Cookie::get('user_id');
        if (!$userId) {
            return $this->error('请先登录');
        }
        
        // 清空购物车
        $result = CartModel::clearCart($userId);
        if ($result === false) {
            return $this->error('清空失败');
        }
        
        return $this->success('清空成功');
    }
    
    /**
     * 更新商品选中状态
     */
    public function updateSelected()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = \think\facade\Cookie::get('user_id');
        if (!$userId) {
            return $this->error('请先登录');
        }
        
        $cartId = $this->request->param('cart_id/d', 0);
        $selected = $this->request->param('selected/d', 1);
        
        if ($cartId <= 0) {
            return $this->error('参数错误');
        }
        
        // 更新选中状态
        $result = CartModel::updateSelected($userId, $cartId, $selected);
        if ($result === false) {
            return $this->error('更新失败');
        }
        
        return $this->success('更新成功');
    }
} 