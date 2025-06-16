<?php
namespace app\model;

use think\Model;
use think\facade\Session;
use think\facade\Cookie;

class User extends Model
{
    // 设置表名
    protected $name = 'user';
    
    // 设置软删除
    use \think\model\concern\SoftDelete;
    protected $deleteTime = 'delete_time';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    // 密码修改器
    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }
    
    /**
     * 用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return bool|array 登录成功返回用户信息，失败返回false
     */
    public static function login($username, $password)
    {
        $user = self::where('username', $username)
            ->where('status', 1)
            ->find();
            
        if (!$user) {
            return false;
        }
        
        if (!password_verify($password, $user->password)) {
            return false;
        }
        
        $userData = $user->toArray();
        
        // 移除敏感信息
        unset($userData['password']);
        
        // 使用Cookie保存登录状态
        Cookie::set('user_id', $user->id, 86400); // 保存一天
        Cookie::set('user_info', json_encode($userData), 86400);
        Cookie::set('is_login', '1', 86400);
        
        // 同时保存到Session作为备份
        Session::set('user_id', $user->id);
        Session::set('user_info', $userData);
        
        return $userData;
    }
    
    /**
     * 退出登录
     */
    public static function logout()
    {
        // 清除Cookie
        Cookie::delete('user_id');
        Cookie::delete('user_info');
        Cookie::delete('is_login');
        
        // 同时清除Session
        Session::delete('user_id');
        Session::delete('user_info');
    }
    
    /**
     * 检查是否登录
     * @return bool
     */
    public static function isLogin()
    {
        // 优先检查Cookie
        if (Cookie::has('is_login') && Cookie::has('user_id')) {
            return true;
        }
        
        // 备用检查Session
        return Session::has('user_id');
    }
    
    /**
     * 获取当前登录用户信息
     * @return array|null
     */
    public static function getCurrentUser()
    {
        // 首先尝试获取用户ID
        $userId = null;
        
        // 从Cookie获取用户ID
        if (Cookie::has('user_id')) {
            $userId = Cookie::get('user_id');
        }
        // 如果Cookie中没有，从Session中获取用户ID
        elseif (Session::has('user_id')) {
            $userId = Session::get('user_id');
        }
        
        // 如果获取到了用户ID，从数据库中查询最新用户信息
        if ($userId) {
            $user = self::find($userId);
            if ($user) {
                $userData = $user->toArray();
                unset($userData['password']); // 移除敏感信息
                
                // 更新Cookie和Session，确保信息同步
                Cookie::set('user_info', json_encode($userData), 86400);
                Session::set('user_info', $userData);
                
                return $userData;
            }
        }
        
        // 如果无法从数据库获取，尝试从缓存中获取
        // 优先从Cookie获取
        if (Cookie::has('user_info')) {
            return json_decode(Cookie::get('user_info'), true);
        }
        
        // 备用从Session获取
        return Session::get('user_info');
    }
    
    /**
     * 检查是否是管理员
     * @return bool
     */
    public static function isAdmin()
    {
        $user = self::getCurrentUser();
        return $user && isset($user['is_admin']) && $user['is_admin'] == 1;
    }
} 