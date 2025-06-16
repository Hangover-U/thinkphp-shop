<?php
declare (strict_types = 1);

namespace app\controller;

use app\model\User as UserModel;
use think\facade\View;
use think\facade\Session;
use think\facade\Validate;
use app\controller\BaseController;
use think\facade\Cookie;

class User extends BaseController
{
    /**
     * 登录页面
     */
    public function login()
    {
        // 如果已登录，跳转到首页
        if (UserModel::isLogin()) {
            return redirect('/');
        }
        
        // 获取跳转地址
        $redirect = $this->request->param('redirect', '');
        View::assign('redirect', $redirect);
        
        return view();
    }
    
    /**
     * 处理登录请求
     */
    public function doLogin()
    {
        $data = $this->request->post();
        
        // 验证数据
        $validate = Validate::rule([
            'username' => 'require',
            'password' => 'require',
        ]);
        
        if (!$validate->check($data)) {
            return $this->error($validate->getError());
        }
        
        // 登录
        $result = UserModel::login($data['username'], $data['password']);
        if ($result === false) {
            return $this->error('用户名或密码错误');
        }
        
        // 获取跳转地址
        $redirect = $data['redirect'] ?? '/';
        
        // 登录成功，返回JSON响应，前端处理重定向
        if ($this->request->isAjax()) {
            return $this->success('登录成功', ['url' => $redirect]);
        }
        
        // 非Ajax请求直接重定向
        return redirect($redirect);
    }
    
    /**
     * 注册页面
     */
    public function register()
    {
        // 如果已登录，跳转到首页
        if (UserModel::isLogin()) {
            return redirect('/');
        }
        
        // 获取跳转地址
        $redirect = $this->request->param('redirect', '');
        View::assign('redirect', $redirect);
        
        return view();
    }
    
    /**
     * 处理注册请求
     */
    public function doRegister()
    {
        $data = $this->request->post();
        
        // 验证数据
        $validate = Validate::rule([
            'username' => 'require|min:3|max:20',
            'password' => 'require|min:6|max:20',
            'confirm_password' => 'require|confirm:password',
            'mobile' => 'require|mobile',
        ]);
        
        if (!$validate->check($data)) {
            return $this->error($validate->getError());
        }
        
        // 检查用户名是否已存在
        $exists = UserModel::where('username', $data['username'])->find();
        if ($exists) {
            return $this->error('用户名已存在');
        }
        
        // 检查手机号是否已存在
        $exists = UserModel::where('mobile', $data['mobile'])->find();
        if ($exists) {
            return $this->error('手机号已存在');
        }
        
        // 创建用户
        $user = new UserModel();
        $user->username = $data['username'];
        $user->password = $data['password'];
        $user->mobile = $data['mobile'];
        $user->email = $data['email'] ?? '';
        $user->real_name = $data['real_name'] ?? '';
        $user->is_admin = 0;
        $user->status = 1;
        $user->save();
        
        // 自动登录
        UserModel::login($data['username'], $data['password']);
        
        // 获取跳转地址
        $redirect = $data['redirect'] ?? '/';
        
        // 注册成功，返回JSON响应，前端处理重定向
        if ($this->request->isAjax()) {
            return $this->success('注册成功', ['url' => $redirect]);
        }
        
        // 非Ajax请求直接重定向
        return redirect($redirect);
    }
    
    /**
     * 退出登录
     */
    public function logout()
    {
        UserModel::logout();
        return redirect('/');
    }
    
    /**
     * 用户中心
     */
    public function center()
    {
        // 检查登录状态
        if (!UserModel::isLogin()) {
            return redirect('/user/login');
        }
        
        // 获取用户ID
        $userId = Session::get('user_id');
        if (empty($userId)) {
            // 尝试从Cookie获取
            $userId = Cookie::get('user_id');
        }
        
        // 从数据库获取最新用户信息
        if (!empty($userId)) {
            $user = UserModel::find($userId);
            if ($user) {
                $userData = $user->toArray();
                unset($userData['password']); // 移除敏感信息
                
                // 确保Session和Cookie同步
                Session::set('user_id', $userId);
                Session::set('user_info', $userData);
                Cookie::set('user_id', $userId, 86400);
                Cookie::set('user_info', json_encode($userData), 86400);
                
                View::assign('user', $userData);
                return view();
            }
        }
        
        // 如果无法获取用户信息，尝试使用getCurrentUser方法
        $user = UserModel::getCurrentUser();
        if ($user) {
            View::assign('user', $user);
            return view();
        }
        
        // 如果仍然无法获取用户信息，重定向到登录页
        UserModel::logout(); // 清除可能存在问题的登录状态
        return redirect('/user/login');
    }
    
    /**
     * 修改个人信息
     */
    public function updateInfo()
    {
        // 检查登录状态
        if (!UserModel::isLogin()) {
            return $this->error('请先登录');
        }
        
        $data = $this->request->post();
        
        // 尝试从多个来源获取用户ID
        $userId = Session::get('user_id');
        if (empty($userId)) {
            // 尝试从Cookie获取
            $userId = Cookie::get('user_id');
        }
        
        // 如果仍然没有ID但显示登录状态，可能是Cookie/Session同步问题
        if (empty($userId) && UserModel::isLogin()) {
            // 获取当前用户信息，该方法已优化为优先使用数据库查询
            $userInfo = UserModel::getCurrentUser();
            if ($userInfo && isset($userInfo['id'])) {
                $userId = $userInfo['id'];
                // 更新Session
                Session::set('user_id', $userId);
            }
        }
        
        // 确保用户ID不为空
        if (empty($userId)) {
            return $this->error('用户信息获取失败，请重新登录');
        }
        
        // 获取要修改的字段
        $updateData = [];
        if(isset($data['email'])) $updateData['email'] = $data['email'];
        if(isset($data['mobile'])) $updateData['mobile'] = $data['mobile'];
        if(isset($data['real_name'])) $updateData['real_name'] = $data['real_name'];
        
        // 如果没有任何字段需要更新，直接返回成功
        if(empty($updateData)) {
            return $this->success('没有内容需要更新');
        }
        
        // 验证数据
        $validate = Validate::rule([
            'email' => 'email',
            'mobile' => 'mobile',
        ]);
        
        if (!$validate->check($updateData)) {
            return $this->error($validate->getError());
        }
        
        // 如果修改手机号，检查是否已存在
        if (isset($updateData['mobile'])) {
            $exists = UserModel::where('mobile', $updateData['mobile'])
                ->where('id', '<>', $userId)
                ->find();
            if ($exists) {
                return $this->error('手机号已存在');
            }
        }
        
        try {
            // 更新用户信息，确保WHERE条件中有正确的用户ID
            $result = UserModel::where('id', $userId)->update($updateData);
            
            // 更新session中的用户信息
            $user = UserModel::find($userId);
            
            if (!$user) {
                return $this->error('用户信息获取失败，请重新登录');
            }
            
            $userArray = $user->toArray();
            unset($userArray['password']); // 移除敏感信息
            
            // 更新Session和Cookie
            Session::set('user_info', $userArray);
            Cookie::set('user_info', json_encode($userArray), 86400);
            
            return $this->success('更新成功');
        } catch (\Exception $e) {
            return $this->error('更新失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 修改密码
     */
    public function updatePassword()
    {
        // 检查登录状态
        if (!UserModel::isLogin()) {
            return $this->error('请先登录');
        }
        
        $data = $this->request->post();
        $userId = Session::get('user_id');
        
        // 验证数据
        $validate = Validate::rule([
            'old_password' => 'require',
            'new_password' => 'require|min:6|max:20',
            'confirm_password' => 'require|confirm:new_password',
        ]);
        
        if (!$validate->check($data)) {
            return $this->error($validate->getError());
        }
        
        // 验证旧密码
        $user = UserModel::find($userId);
        if (!password_verify($data['old_password'], $user->password)) {
            return $this->error('旧密码错误');
        }
        
        // 更新密码
        $user->password = $data['new_password'];
        $user->save();
        
        // 退出登录，重新登录
        UserModel::logout();
        
        return $this->success('密码修改成功，请重新登录');
    }
    
    /**
     * 用户列表（管理员功能）
     */
    public function list()
    {
        // 检查是否是管理员
        if (!UserModel::isAdmin()) {
            return redirect('/');
        }
        
        $users = UserModel::order('id', 'desc')->paginate(10);
        View::assign('users', $users);
        
        return view();
    }
} 