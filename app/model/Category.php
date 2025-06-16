<?php
namespace app\model;

use think\Model;

class Category extends Model
{
    // 设置表名
    protected $name = 'category';
    
    // 设置软删除
    use \think\model\concern\SoftDelete;
    protected $deleteTime = 'delete_time';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 获取分类树形结构
     * @param int $parentId 父级ID
     * @return array
     */
    public static function getCategoryTree($parentId = 0)
    {
        $categories = self::where('parent_id', $parentId)
            ->where('is_show', 1)
            ->order('sort', 'asc')
            ->select()
            ->toArray();
            
        foreach ($categories as &$category) {
            $children = self::getCategoryTree($category['id']);
            if (!empty($children)) {
                $category['children'] = $children;
            }
        }
        
        return $categories;
    }
    
    /**
     * 获取所有分类（平铺结构）
     * @return array
     */
    public static function getAllCategories()
    {
        return self::where('is_show', 1)
            ->order('sort', 'asc')
            ->select()
            ->toArray();
    }
    
    /**
     * 获取指定分类的子分类ID（包含自身）
     * @param int $categoryId 分类ID
     * @return array
     */
    public static function getChildrenIds($categoryId)
    {
        $ids = [$categoryId];
        $children = self::where('parent_id', $categoryId)->column('id');
        
        if (!empty($children)) {
            foreach ($children as $childId) {
                $ids = array_merge($ids, self::getChildrenIds($childId));
            }
        }
        
        return $ids;
    }
} 