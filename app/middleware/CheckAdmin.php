<?php
declare (strict_types = 1);

namespace app\middleware;

use think\facade\Cookie;
use think\facade\Request;
use think\facade\Session;

class CheckAdmin
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 从Cookie中获取用户ID
        $userId = Cookie::get('user_id');
        $isLogin = Cookie::get('is_login');
        $userInfo = Cookie::get('user_info');
        
        // 判断是否登录
        if (!$isLogin || !$userId || !$userInfo) {
            // 未登录，跳转到登录页
            return redirect('/user/login');
        }
        
        // 解析用户信息
        $userInfo = json_decode($userInfo, true);
        
        // 判断是否是管理员
        if (!isset($userInfo['is_admin']) || $userInfo['is_admin'] != 1) {
            // 不是管理员，跳转到首页
            return redirect('/')->with('error', '您没有管理员权限');
        }
        
        return $next($request);
    }
} 