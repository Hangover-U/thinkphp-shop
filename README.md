# 在线购物系统

基于 ThinkPHP 6 框架开发的完整在线购物系统，包含前台商城和后台管理功能。

## 项目概述

本系统是一个功能完善的电子商务平台，采用 MVC 架构设计，实现了商品展示、用户管理、购物车、订单处理等核心功能。系统分为前台商城和后台管理两大部分，为用户提供良好的购物体验，同时为管理员提供高效的内容管理工具。

## 环境要求

- PHP >= 7.3.4
- MySQL >= 8.0.42
- Composer
- 操作系统：Windows/Linux/MacOS

## 技术栈

- 后端框架：ThinkPHP 6.0
- 前端框架：Bootstrap 5
- 数据库：MySQL 8.0
- 前端 JS 库：jQuery

## 安装步骤

1. 克隆代码到本地

   ```
   git clone https://github.com/Hangover-U/thinkphp-shop.git
   ```

2. 进入项目目录

   ```
   cd shop
   ```

3. 安装依赖

   ```
   composer install
   ```

4. 创建数据库并导入数据

   ```sql
   CREATE DATABASE IF NOT EXISTS thinkphp6_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   然后导入项目根目录下的`Dump20250615.sql`文件

5. 配置数据库连接
   修改`config/database.php`文件中的数据库连接信息

6. 启动开发服务器

   ```
   php think run
   ```

7. 访问系统
   - 前台：http://localhost:8000
   - 后台：http://localhost:8000/admin (需要管理员账号)

## 系统功能

### 前台功能

1. **用户管理**

   - 用户注册：支持用户名、密码、手机号注册
   - 用户登录：使用用户名和密码登录
   - 个人中心：查看和修改个人信息
   - 密码修改：支持用户修改密码

2. **商品浏览**

   - 首页展示：推荐商品和最新商品展示
   - 分类浏览：按商品分类查看商品
   - 商品搜索：支持关键词搜索商品
   - 商品详情：查看商品详细信息

3. **购物车**

   - 添加商品：将商品添加到购物车
   - 修改数量：调整购物车中商品数量
   - 删除商品：从购物车中移除商品
   - 清空购物车：一键清空购物车

4. **订单管理**
   - 创建订单：从购物车创建订单
   - 订单支付：模拟订单支付流程
   - 订单查询：查看订单状态和详情
   - 确认收货：确认已收到商品
   - 取消订单：取消未支付订单

### 后台功能

1. **控制台**

   - 数据统计：商品、订单、用户数量统计
   - 快捷操作：常用功能快速访问

2. **商品管理**

   - 商品列表：查看所有商品
   - 添加商品：新增商品信息
   - 编辑商品：修改商品信息
   - 删除商品：下架并删除商品

3. **订单管理**

   - 订单列表：查看所有订单
   - 订单详情：查看订单详细信息
   - 订单发货：处理已付款订单

4. **用户管理**
   - 用户列表：查看所有用户
   - 添加用户：创建新用户
   - 编辑用户：修改用户信息
   - 删除用户：删除用户账号

## 数据库设计

系统包含以下主要数据表：

1. **用户表(user)**：存储用户基本信息
2. **商品分类表(category)**：存储商品分类信息
3. **商品表(product)**：存储商品详细信息
4. **购物车表(cart)**：存储用户购物车信息
5. **订单表(order)**：存储订单基本信息
6. **订单详情表(order_detail)**：存储订单商品明细

## 测试账号

- 普通用户：test_user / qwe123
- 管理员：admin / qwe123

## 目录结构

```
shop/
├── app/                    # 应用目录
│   ├── controller/         # 控制器目录
│   ├── model/              # 模型目录
│   ├── middleware/         # 中间件目录
│   └── validate/           # 验证器目录
├── config/                 # 配置目录
├── public/                 # 公共资源目录
│   └── static/             # 静态资源目录
├── route/                  # 路由定义目录
├── runtime/                # 运行时目录
├── vendor/                 # Composer依赖包
└── view/                   # 视图模板目录
    ├── admin/              # 管理员视图
    ├── index/              # 前台首页视图
    ├── user/               # 用户相关视图
    ├── cart/               # 购物车视图
    ├── order/              # 订单相关视图
    └── layout/             # 布局模板
```

## 开发团队

- 开发者：Hangover-U
- 联系方式：772774627@qq.com

## 版权信息

本项目基于 ThinkPHP 框架开发，遵循 Apache2 开源协议。

ThinkPHP® 商标和著作权所有者为上海顶想信息科技有限公司。

更多细节参阅 [LICENSE.txt](LICENSE.txt)

## 界面设计风格 - 新拟态渐变风格

系统提供了一套美观的新拟态(Neumorphism)渐变设计风格，具有以下特点：

### 主要设计元素

```css
:root {
  --primary-gradient: linear-gradient(135deg, #6e8efb, #a777e3);
  --secondary-gradient: linear-gradient(135deg, #a777e3, #8569d9);
  --accent-gradient: linear-gradient(135deg, #43cbff, #9708cc);
  --button-gradient: linear-gradient(135deg, #6e8efb, #a777e3);
  --background-color: #e6e9f0;
  --card-bg: #e6e9f0;
  --text-primary: #333333;
  --text-secondary: #666666;
  --text-muted: #888888;
  --shadow-color-light: rgba(255, 255, 255, 0.7);
  --shadow-color-dark: rgba(174, 174, 192, 0.4);
}
```

### 新拟态卡片样式

```css
.neomorphic-card {
  background: var(--card-bg);
  border-radius: 20px;
  box-shadow: 8px 8px 15px var(--shadow-color-dark), -8px -8px 15px var(--shadow-color-light);
  margin-bottom: 2rem;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.neomorphic-card:hover {
  transform: translateY(-5px);
  box-shadow: 12px 12px 20px var(--shadow-color-dark), -12px -12px 20px var(--shadow-color-light);
}
```

### 渐变标题和文字

```css
.product-title {
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 0.8rem;
  color: var(--text-primary);
  background: var(--primary-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.product-price {
  font-size: 1.5rem;
  font-weight: 700;
  background: var(--secondary-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 1.5rem;
  display: block;
}
```

### 新拟态按钮

```css
.neomorphic-btn {
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.9rem;
  color: white;
  background: var(--button-gradient);
  box-shadow: 5px 5px 10px var(--shadow-color-dark), -5px -5px 10px var(--shadow-color-light);
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  z-index: 1;
}

.neomorphic-btn::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #a777e3, #6e8efb);
  z-index: -1;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.neomorphic-btn:hover::before {
  opacity: 1;
}
```

### 渐变边框效果

```css
.gradient-border {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
}

.gradient-border::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  border-radius: 20px;
  padding: 3px;
  background: var(--accent-gradient);
  -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.neomorphic-card:hover .gradient-border::before {
  opacity: 1;
}
```

### 完整设计文件

完整的设计样式可以在 `shop/public/neomorphism_style.html` 文件中找到，可以通过复制该文件中的 CSS 和 HTML 结构来实现相同的设计效果。
