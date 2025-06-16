<?php
namespace app\model;

use think\Model;

class Product extends Model
{
    // 设置表名
    protected $name = 'product';
    
    // 设置软删除
    use \think\model\concern\SoftDelete;
    protected $deleteTime = 'delete_time';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 关联分类模型
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    /**
     * 获取推荐商品
     * @param int $limit 数量限制
     * @return array
     */
    public static function getRecommendProducts($limit = 6)
    {
        return self::where('is_on_sale', 1)
            ->where('is_recommend', 1)
            ->order('id', 'desc')
            ->limit($limit)
            ->select()
            ->toArray();
    }
    
    /**
     * 按分类获取商品
     * @param int $categoryId 分类ID
     * @param int $page 页码
     * @param int $pageSize 每页数量
     * @return array
     */
    public static function getProductsByCategory($categoryId, $page = 1, $pageSize = 10)
    {
        // 获取分类及其子分类的ID
        $categoryIds = Category::getChildrenIds($categoryId);
        
        return self::where('is_on_sale', 1)
            ->whereIn('category_id', $categoryIds)
            ->order('id', 'desc')
            ->paginate([
                'list_rows' => $pageSize,
                'page' => $page,
            ]);
    }
    
    /**
     * 搜索商品
     * @param string $keyword 关键词
     * @param int $page 页码
     * @param int $pageSize 每页数量
     * @return array
     */
    public static function searchProducts($keyword, $page = 1, $pageSize = 10)
    {
        return self::where('is_on_sale', 1)
            ->where('name', 'like', "%{$keyword}%")
            ->order('id', 'desc')
            ->paginate([
                'list_rows' => $pageSize,
                'page' => $page,
            ]);
    }
} 