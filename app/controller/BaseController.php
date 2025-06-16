<?php
declare (strict_types = 1);

namespace app\controller;

use think\App;
use think\exception\ValidateException;
use think\facade\View;
use think\facade\Cookie;
use think\facade\Session;
use app\model\User;
use app\model\Category;

/**
 * 基础控制器
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 初始化
        $this->initialize();
    }

    /**
     * 初始化
     */
    protected function initialize()
    {
        // 检查用户登录状态
        $isLogin = User::isLogin();
        
        // 获取用户ID，优先从Session获取
        $userId = null;
        $sessionUserId = Session::get('user_id');
        
        if (Cookie::has('user_id')) {
            $userId = Cookie::get('user_id');
        } elseif ($sessionUserId) {
            $userId = $sessionUserId;
        }
        
        // 如果有用户ID，从数据库获取最新用户信息
        $user = null;
        if ($userId && $isLogin) {
            $userObj = User::find($userId);
            if ($userObj) {
                $user = $userObj->toArray();
                unset($user['password']); // 移除敏感信息
                
                // 更新Session和Cookie，确保信息同步
                Session::set('user_id', $userId);
                Session::set('user_info', $user);
                Cookie::set('user_id', $userId, 86400);
                Cookie::set('user_info', json_encode($user), 86400);
            }
        }
        
        // 如果数据库获取失败，尝试使用缓存的用户信息
        if (!$user && $isLogin) {
            $user = User::getCurrentUser();
        }
        
        // 获取分类列表
        $categories = Category::getAllCategories();
        
        // 调试信息 - 显示更多调试数据
        /*
        $cookieUserId = Cookie::get('user_id');
        $cookieUserInfo = Cookie::get('user_info');
        $cookieIsLogin = Cookie::get('is_login');
        $sessionUserInfo = Session::get('user_info');
        */
        
        // 设置全局模板变量
        View::assign([
            'isLogin' => $isLogin,
            'user' => $user,
            'categories' => $categories,
            'isAdmin' => $isLogin && User::isAdmin(),
            // 调试信息
            /*
            '是否登录' => $isLogin ? '是' : '否',
            'CookieUserId' => $cookieUserId,
            'Cookie登录状态' => $cookieIsLogin,
            'SessionUserId' => $sessionUserId,
            '数据库ID' => $userId,
            */
        ]);
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new \think\Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }
    
    /**
     * 成功操作返回的数据
     * @param string $msg 提示信息
     * @param mixed $data 要返回的数据
     * @param int $code 错误码，默认为1
     * @param string $type 输出类型
     * @return \think\response\Json|\think\response\View
     */
    protected function success($msg = '操作成功', $data = null, $code = 1, $type = 'json')
    {
        if ($type == 'json') {
            return json([
                'code' => $code,
                'msg'  => $msg,
                'data' => $data
            ]);
        }
        
        View::assign([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ]);
        
        return view();
    }
    
    /**
     * 失败操作返回的数据
     * @param string $msg 提示信息
     * @param mixed $data 要返回的数据
     * @param int $code 错误码，默认为0
     * @param string $type 输出类型
     * @return \think\response\Json|\think\response\View
     */
    protected function error($msg = '操作失败', $data = null, $code = 0, $type = 'json')
    {
        if ($type == 'json') {
            return json([
                'code' => $code,
                'msg'  => $msg,
                'data' => $data
            ]);
        }
        
        View::assign([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ]);
        
        return view();
    }
} 