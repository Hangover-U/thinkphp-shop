<?php
declare (strict_types = 1);

namespace app\controller;

use app\controller\BaseController;
use app\model\Product;
use app\model\Category;
use think\facade\View;

class Index extends BaseController
{
    /**
     * 首页
     */
    public function index()
    {
        // 获取推荐商品
        $recommendProducts = Product::getRecommendProducts(6);
        View::assign('recommendProducts', $recommendProducts);
        
        // 获取最新商品
        $newProducts = Product::where('is_on_sale', 1)
            ->order('id', 'desc')
            ->limit(8)
            ->select()
            ->toArray();
        View::assign('newProducts', $newProducts);
        
        return view();
    }
    
    /**
     * 商品分类页 - 重定向到商品列表页
     */
    public function category()
    {
        $categoryId = $this->request->param('id/d', 0);
        return redirect('/products?category_id=' . $categoryId);
    }
    
    /**
     * 商品搜索页 - 重定向到商品列表页
     */
    public function search()
    {
        $keyword = $this->request->param('keyword', '');
        return redirect('/products?keyword=' . urlencode($keyword));
    }
    
    /**
     * 商品详情页
     */
    public function detail()
    {
        $productId = $this->request->param('id/d', 0);
        
        // 获取商品信息
        $product = Product::with('category')->find($productId);
        if (!$product || $product->is_on_sale != 1) {
            return redirect('/');
        }
        
        View::assign('product', $product);
        
        // 获取相关商品
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '<>', $productId)
            ->where('is_on_sale', 1)
            ->limit(4)
            ->select()
            ->toArray();
        View::assign('relatedProducts', $relatedProducts);
        
        return view();
    }
    
    /**
     * 商品列表页 - 支持多种筛选
     */
    public function list()
    {
        // 获取筛选参数
        $categoryId = $this->request->param('category_id/d', 0);
        $priceMin = $this->request->param('price_min/f', 0);
        $priceMax = $this->request->param('price_max/f', 0);
        $keyword = $this->request->param('keyword', '');
        $sort = $this->request->param('sort', 'default'); // default, price-asc, price-desc, sales-desc
        $page = $this->request->param('page/d', 1);
        $pageSize = $this->request->param('page_size/d', 12);
        
        // 构建查询条件
        $query = Product::where('is_on_sale', 1);
        
        // 分类筛选
        if ($categoryId > 0) {
            // 获取分类及其子分类的ID
            $categoryIds = Category::getChildrenIds($categoryId);
            $query->whereIn('category_id', $categoryIds);
            
            // 获取分类信息
            $category = Category::find($categoryId);
            View::assign('category', $category);
        }
        
        // 价格区间筛选
        if ($priceMin > 0) {
            $query->where('price', '>=', $priceMin);
        }
        if ($priceMax > 0) {
            $query->where('price', '<=', $priceMax);
        }
        
        // 关键词筛选
        if (!empty($keyword)) {
            $query->where('name', 'like', "%{$keyword}%");
            View::assign('pageTitle', '搜索结果：' . $keyword);
        }
        
        // 排序
        switch ($sort) {
            case 'price-asc':
                $query->order('price', 'asc');
                break;
            case 'price-desc':
                $query->order('price', 'desc');
                break;
            case 'sales-desc':
                $query->order('sales', 'desc');
                break;
            default:
                $query->order('id', 'desc');
        }
        
        // 分页获取数据
        $products = $query->paginate([
            'list_rows' => $pageSize,
            'page' => $page,
            'query' => $this->request->param()
        ]);
        
        // 获取所有分类
        $categories = Category::where('parent_id', 0)
            ->where('is_show', 1)
            ->order('sort', 'asc')
            ->select();
        
        // 传递数据到视图
        View::assign([
            'products' => $products,
            'categories' => $categories,
            'categoryId' => $categoryId,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
            'keyword' => $keyword,
            'sort' => $sort,
            'pageSize' => $pageSize
        ]);
        
        return view();
    }
    
    /**
     * 热门推荐页面
     */
    public function recommend()
    {
        $page = $this->request->param('page/d', 1);
        $pageSize = $this->request->param('page_size/d', 12);
        
        // 获取推荐商品
        $products = Product::where('is_on_sale', 1)
            ->where('is_recommend', 1)
            ->order('sales', 'desc')
            ->paginate([
                'list_rows' => $pageSize,
                'page' => $page,
                'query' => $this->request->param()
            ]);
        
        // 获取所有分类
        $categories = Category::where('parent_id', 0)
            ->where('is_show', 1)
            ->order('sort', 'asc')
            ->select();
        
        // 传递数据到视图
        View::assign([
            'products' => $products,
            'categories' => $categories,
            'pageSize' => $pageSize,
            'pageTitle' => '热门推荐'
        ]);
        
        // 使用与商品列表相同的视图模板
        return View::fetch('list');
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
