<?php
declare (strict_types = 1);

namespace app\controller;

use app\controller\BaseController;
use app\model\Product;
use app\model\Category;
use app\model\Order;
use app\model\User;
use think\facade\View;
use think\facade\Request;
use think\facade\Filesystem;
use think\facade\Validate;

class Admin extends BaseController
{
    /**
     * 前置操作
     */
    protected $middleware = [
        'check_admin' => ['except' => []]
    ];
    
    /**
     * 管理员控制台首页
     */
    public function index()
    {
        // 获取统计数据
        $productCount = Product::count();
        $orderCount = Order::count();
        $pendingOrderCount = Order::where('status', 1)->count(); // 已付款待发货
        $userCount = User::count(); // 添加用户统计
        
        // 获取商品分类统计
        $parentCategories = Category::where('parent_id', 0)->where('is_show', 1)->select();
        $categoryStats = [];
        $parentCategoryStats = [];
        $childCategoryStats = [];
        
        foreach ($parentCategories as $category) {
            // 获取子分类ID列表
            $childCategoryIds = Category::where('parent_id', $category['id'])->where('is_show', 1)->column('id');
            
            // 计算父分类的总商品数（包含子分类的商品）
            $productCountInParentCategory = 0;
            
            // 先计算直接归属于父分类的商品
            $directProductCount = Product::where('category_id', $category['id'])->count();
            $productCountInParentCategory += $directProductCount;
            
            // 获取子分类
            $childCategories = Category::where('parent_id', $category['id'])->where('is_show', 1)->select();
            foreach ($childCategories as $childCategory) {
                $productCountInChildCategory = Product::where('category_id', $childCategory['id'])->count();
                $productCountInParentCategory += $productCountInChildCategory;
                
                // 子分类统计
                if ($productCountInChildCategory > 0) {
                    $childCategoryStats[] = [
                        'name' => $childCategory['name'],
                        'count' => $productCountInChildCategory,
                        'parent_name' => $category['name'],
                        'type' => 'child'
                    ];
                }
            }
            
            // 父分类统计（包含所有子分类的商品）
            $parentCategoryStats[] = [
                'name' => $category['name'],
                'count' => $productCountInParentCategory,
                'type' => 'parent'
            ];
        }
        
        // 合并分类统计数据，先显示父分类，再显示子分类
        $categoryStats = array_merge($parentCategoryStats, $childCategoryStats);
        
        // 获取热门商品
        $hotProducts = Product::order('sales', 'desc')->limit(5)->select();
        
        // 获取最近7天的订单统计
        $orderStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i day"));
            $count = Order::whereDay('create_time', $date)->count();
            $orderStats[] = [
                'date' => date('m-d', strtotime($date)),
                'count' => $count
            ];
        }
        
        View::assign([
            'productCount' => $productCount,
            'orderCount' => $orderCount,
            'pendingOrderCount' => $pendingOrderCount,
            'userCount' => $userCount,
            'categoryStats' => $categoryStats,
            'hotProducts' => $hotProducts,
            'orderStats' => $orderStats
        ]);
        
        return view();
    }
    
    /**
     * 商品列表
     */
    public function productList()
    {
        $products = Product::with('category')
            ->order('id', 'desc')
            ->paginate(10);
        
        View::assign('products', $products);
        
        return view();
    }
    
    /**
     * 添加商品页面
     */
    public function addProduct()
    {
        // 获取所有分类
        $categories = Category::where('is_show', 1)->select();
        View::assign('categories', $categories);
        
        return view();
    }
    
    /**
     * 处理添加商品
     */
    public function doAddProduct()
    {
        // 获取表单数据
        $data = Request::post();
        
        // 验证数据
        $validate = validate('Product');
        if (!$validate->check($data)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        
        // 处理图片上传
        $file = Request::file('image');
        if ($file) {
            try {
                // 验证图片
                validate(['image'=>[
                    'fileSize' => 10485760, // 10MB
                    'fileExt' => 'jpg,jpeg,png,gif,webp',
                    'fileMime' => 'image/jpeg,image/png,image/gif,image/webp',
                ]])->check(['image' => $file]);
                
                // 使用日期目录保存文件，避免文件过多
                $savePath = date('Ymd');
                // 使用ThinkPHP的文件系统上传图片
                $savename = Filesystem::disk('public')->putFile($savePath, $file, 'md5');
                if ($savename) {
                    // 设置图片路径为/static/uploads/product/文件名
                    // 替换所有反斜杠为正斜杠，确保URL格式正确
                    $data['image'] = '/static/uploads/product/' . str_replace('\\', '/', $savename);
                } else {
                    return json(['code' => 0, 'msg' => '图片上传失败，请检查上传目录权限']);
                }
            } catch (\Exception $e) {
                return json(['code' => 0, 'msg' => '图片上传失败：' . $e->getMessage()]);
            }
        } else {
            return json(['code' => 0, 'msg' => '请上传商品图片']);
        }
        
        // 保存商品数据
        $product = new Product;
        $product->save($data);
        
        return json(['code' => 1, 'msg' => '商品添加成功']);
    }
    
    /**
     * 编辑商品页面
     */
    public function editProduct($id)
    {
        // 获取商品信息
        $product = Product::find($id);
        if (!$product) {
            return redirect('/admin/product_list');
        }
        
        // 获取所有分类
        $categories = Category::where('is_show', 1)->select();
        
        View::assign([
            'product' => $product,
            'categories' => $categories
        ]);
        
        return view();
    }
    
    /**
     * 处理编辑商品
     */
    public function doEditProduct()
    {
        try {
            // 获取表单数据
            $data = Request::post();
            $id = $data['id'] ?? 0;
            
            if (!$id) {
                return json(['code' => 0, 'msg' => '商品ID不能为空']);
            }
            
            // 确保复选框字段存在
            if (!isset($data['is_on_sale'])) {
                $data['is_on_sale'] = 0;
            }
            if (!isset($data['is_recommend'])) {
                $data['is_recommend'] = 0;
            }
            
            // 验证数据
            $validate = validate('Product');
            if (!$validate->scene('edit')->check($data)) {
                return json(['code' => 0, 'msg' => $validate->getError()]);
            }
            
            // 查询原商品数据
            $product = Product::find($id);
            if (!$product) {
                return json(['code' => 0, 'msg' => '商品不存在']);
            }
            
            // 处理图片上传
            $file = Request::file('image');
            if ($file && $file->isValid()) {
                try {
                    // 验证图片
                    validate(['image'=>[
                        'fileSize' => 10485760, // 10MB
                        'fileExt' => 'jpg,jpeg,png,gif,webp',
                        'fileMime' => 'image/jpeg,image/png,image/gif,image/webp',
                    ]])->check(['image' => $file]);
                    
                    // 使用日期目录保存文件，避免文件过多
                    $savePath = date('Ymd');
                    // 使用ThinkPHP的文件系统上传图片
                    $savename = Filesystem::disk('public')->putFile($savePath, $file, 'md5');
                    if ($savename) {
                        // 设置图片路径为/static/uploads/product/文件名
                        // 替换所有反斜杠为正斜杠，确保URL格式正确
                        $data['image'] = '/static/uploads/product/' . str_replace('\\', '/', $savename);
                    } else {
                        return json(['code' => 0, 'msg' => '图片上传失败，请检查上传目录权限']);
                    }
                } catch (\Exception $e) {
                    return json(['code' => 0, 'msg' => '图片上传失败：' . $e->getMessage()]);
                }
            } else {
                // 如果没有上传新图片，保留原图片
                unset($data['image']);
            }
            
            // 更新商品数据
            $result = Product::update($data, ['id' => $id]);
            
            if ($result) {
                return json(['code' => 1, 'msg' => '商品更新成功']);
            } else {
                return json(['code' => 0, 'msg' => '商品更新失败']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误：' . $e->getMessage()]);
        }
    }
    
    /**
     * 删除商品
     */
    public function deleteProduct()
    {
        $id = Request::param('id/d', 0);
        
        if ($id > 0) {
            Product::destroy($id);
            return json(['code' => 1, 'msg' => '商品删除成功']);
        } else {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
    }
    
    /**
     * 订单管理
     */
    public function orderList()
    {
        $status = Request::param('status');
        
        $query = Order::with(['orderDetails']);
        
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
        
        $orders = $query->order('id', 'desc')->paginate(10);
        
        View::assign([
            'orders' => $orders,
            'status' => $status
        ]);
        
        return view();
    }
    
    /**
     * 订单详情
     */
    public function orderDetail($id)
    {
        $order = Order::with(['orderDetails'])->find($id);
        
        if (!$order) {
            return redirect('/admin/order_list');
        }
        
        View::assign('order', $order);
        
        return view();
    }
    
    /**
     * 发货处理
     */
    public function ship()
    {
        $orderId = Request::param('order_id/d', 0);
        
        if ($orderId > 0) {
            $order = Order::find($orderId);
            
            if ($order && $order->status == 1) {
                $order->status = 2;
                $order->delivery_time = date('Y-m-d H:i:s');
                $order->save();
                
                return json(['code' => 1, 'msg' => '发货成功']);
            } else {
                return json(['code' => 0, 'msg' => '订单状态不正确']);
            }
        } else {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
    }
    
    /**
     * 用户管理列表
     */
    public function userList()
    {
        $users = User::order('id', 'desc')->paginate(10);
        View::assign('users', $users);
        
        return view();
    }
    
    /**
     * 添加用户页面
     */
    public function addUser()
    {
        return view();
    }
    
    /**
     * 处理添加用户
     */
    public function doAddUser()
    {
        $data = Request::post();
        
        // 验证数据
        $validate = Validate::rule([
            'username' => 'require|min:3|max:20',
            'password' => 'require|min:6|max:20',
            'mobile' => 'require|mobile',
            'email' => 'email',
        ]);
        
        if (!$validate->check($data)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        
        // 检查用户名是否已存在
        $exists = User::where('username', $data['username'])->find();
        if ($exists) {
            return json(['code' => 0, 'msg' => '用户名已存在']);
        }
        
        // 检查手机号是否已存在
        $exists = User::where('mobile', $data['mobile'])->find();
        if ($exists) {
            return json(['code' => 0, 'msg' => '手机号已存在']);
        }
        
        // 创建用户
        $user = new User();
        $user->username = $data['username'];
        $user->password = $data['password'];
        $user->mobile = $data['mobile'];
        $user->email = $data['email'] ?? '';
        $user->real_name = $data['real_name'] ?? '';
        $user->is_admin = isset($data['is_admin']) ? 1 : 0;
        $user->status = isset($data['status']) ? 1 : 0;
        $user->save();
        
        return json(['code' => 1, 'msg' => '用户添加成功']);
    }
    
    /**
     * 编辑用户页面
     */
    public function editUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('/admin/user_list');
        }
        
        View::assign('user', $user);
        
        return view();
    }
    
    /**
     * 处理编辑用户
     */
    public function doEditUser()
    {
        $data = Request::post();
        $id = $data['id'];
        
        // 验证数据
        $validate = Validate::rule([
            'mobile' => 'mobile',
            'email' => 'email',
        ]);
        
        if (!$validate->check($data)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        
        // 如果修改手机号，检查是否已存在
        if (!empty($data['mobile'])) {
            $exists = User::where('mobile', $data['mobile'])
                ->where('id', '<>', $id)
                ->find();
            if ($exists) {
                return json(['code' => 0, 'msg' => '手机号已存在']);
            }
        }
        
        // 更新用户信息
        $user = User::find($id);
        if (isset($data['mobile'])) $user->mobile = $data['mobile'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (isset($data['real_name'])) $user->real_name = $data['real_name'];
        
        // 明确设置管理员权限和状态，不依赖isset判断
        $user->is_admin = isset($data['is_admin']) && $data['is_admin'] == '1' ? 1 : 0;
        $user->status = isset($data['status']) && $data['status'] == '1' ? 1 : 0;
        
        // 如果提供了新密码，则更新密码
        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }
        
        $user->save();
        
        return json(['code' => 1, 'msg' => '用户更新成功']);
    }
    
    /**
     * 删除用户
     */
    public function deleteUser()
    {
        $id = Request::param('id/d', 0);
        
        if ($id > 0) {
            // 不允许删除自己
            $currentUser = User::getCurrentUser();
            if ($currentUser['id'] == $id) {
                return json(['code' => 0, 'msg' => '不能删除当前登录的用户']);
            }
            
            User::destroy($id);
            return json(['code' => 1, 'msg' => '用户删除成功']);
        } else {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
    }
} 