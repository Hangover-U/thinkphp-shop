<?php
declare (strict_types = 1);

namespace app\controller;

use app\model\Order as OrderModel;
use app\model\User;
use app\model\Cart as CartModel;
use think\facade\View;
use think\facade\Cookie;
use think\facade\Validate;
use app\controller\BaseController;

class Order extends BaseController
{
    /**
     * 订单列表页
     */
    public function index()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = Cookie::get('user_id');
        if (!$userId) {
            return redirect('/user/login');
        }
        
        $status = $this->request->param('status/d', null);
        $page = $this->request->param('page/d', 1);
        
        // 获取订单列表
        $orders = OrderModel::getUserOrders($userId, $status, $page);
        View::assign('orders', $orders);
        View::assign('status', $status);
        
        return view();
    }
    
    /**
     * 订单详情页
     */
    public function detail()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = Cookie::get('user_id');
        if (!$userId) {
            return redirect('/user/login');
        }
        
        $orderId = $this->request->param('id/d', 0);
        
        // 获取订单信息
        $order = OrderModel::with('orderDetails')
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->find();
            
        if (!$order) {
            return redirect('/order');
        }
        
        View::assign('order', $order);
        
        return view();
    }
    
    /**
     * 创建订单页面
     */
    public function create()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = Cookie::get('user_id');
        if (!$userId) {
            return redirect('/user/login');
        }
        
        // 获取购物车已选中的商品
        $cartItems = CartModel::with('product')
            ->where('user_id', $userId)
            ->where('selected', 1)
            ->select();
            
        if ($cartItems->isEmpty()) {
            return redirect('/cart');
        }
        
        View::assign('cartItems', $cartItems);
        
        // 计算总金额
        $totalAmount = CartModel::calculateTotal($userId, true);
        View::assign('totalAmount', $totalAmount);
        
        // 获取用户信息
        $user = User::find($userId);
        View::assign('user', $user);
        
        return view();
    }
    
    /**
     * 提交订单
     */
    public function submit()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = Cookie::get('user_id');
        if (!$userId) {
            return $this->error('请先登录');
        }
        
        $data = $this->request->post();
        
        // 验证数据
        $validate = Validate::rule([
            'address' => 'require',
            'consignee' => 'require',
            'mobile' => 'require|mobile',
        ]);
        
        if (!$validate->check($data)) {
            return $this->error($validate->getError());
        }
        
        // 创建订单
        $result = OrderModel::createOrder($userId, $data['address'], $data['consignee'], $data['mobile']);
        if ($result === false) {
            return $this->error('创建订单失败，请检查购物车商品');
        }
        
        return $this->success('订单创建成功', ['order_id' => $result['id']]);
    }
    
    /**
     * 支付订单
     */
    public function pay()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = Cookie::get('user_id');
        if (!$userId) {
            return $this->error('请先登录');
        }
        
        $orderId = $this->request->param('order_id/d', 0);
        
        if ($orderId <= 0) {
            return $this->error('参数错误');
        }
        
        // 支付订单（模拟支付）
        $result = OrderModel::payOrder($orderId, $userId);
        if ($result === false) {
            return $this->error('支付失败，订单可能已支付或已取消');
        }
        
        return $this->success('支付成功');
    }
    
    /**
     * 取消订单
     */
    public function cancel()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = Cookie::get('user_id');
        if (!$userId) {
            return $this->error('请先登录');
        }
        
        $orderId = $this->request->param('order_id/d', 0);
        
        if ($orderId <= 0) {
            return $this->error('参数错误');
        }
        
        // 取消订单
        $result = OrderModel::cancelOrder($orderId, $userId);
        if ($result === false) {
            return $this->error('取消失败，订单可能已支付');
        }
        
        return $this->success('取消成功');
    }
    
    /**
     * 确认收货
     */
    public function complete()
    {
        // 检查登录状态 - 从Cookie获取
        $userId = Cookie::get('user_id');
        if (!$userId) {
            return $this->error('请先登录');
        }
        
        $orderId = $this->request->param('order_id/d', 0);
        
        if ($orderId <= 0) {
            return $this->error('参数错误');
        }
        
        // 确认收货
        $order = OrderModel::where('id', $orderId)
            ->where('user_id', $userId)
            ->where('status', OrderModel::STATUS_SHIPPED)
            ->find();
            
        if (!$order) {
            return $this->error('订单不存在或状态不正确');
        }
        
        $order->status = OrderModel::STATUS_COMPLETED;
        $order->complete_time = date('Y-m-d H:i:s');
        $result = $order->save();
        
        if ($result === false) {
            return $this->error('确认收货失败');
        }
        
        return $this->success('确认收货成功');
    }
} 