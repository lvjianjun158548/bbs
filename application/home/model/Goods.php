<?php

namespace app\home\model;

use think\Model;

class Goods extends Model
{
    protected static function init()
    {
        try {
            //实例化ES工具类
            $es = new \tools\es\MyElasticsearch();
            //设置新增回调
            self::afterInsert(function ($goods) use ($es) {
                //添加文档
                $doc = $goods->visible(['id', 'goods_name', 'goods_desc', 'goods_price'])->toArray();
                $doc['cate_name'] = $goods->category->cate_name;
                $es->add_doc($goods->id, $doc, 'goods_index', 'goods_type');
            });
            //设置更新回调
            self::afterUpdate(function ($goods) use ($es) {
                //修改文档
                $doc = $goods->visible(['id', 'goods_name', 'goods_desc', 'goods_price', 'cate_name'])->toArray();
                $doc['cate_name'] = $goods->category->cate_name;
                $body = ['doc' => $doc];
                $es->update_doc($goods->id, 'goods_index', 'goods_type', $body);
            });
            //设置删除回调
            self::afterDelete(function ($goods) use ($es) {
                //删除文档
                $es->delete_doc($goods->id, 'goods_index', 'goods_type');
            });
        } catch (\Exception $e) {
            $line = $e->getLine();
            $file = $e->getFile();
            $error = $e->getMessage();
           
            trace('服务器异常:'.$line.$file.$error);
        }
    }

    public function specGoods()
    {
        return $this->hasmany('SpecGoods', 'goods_id', 'id');
    }

    public function goodsImages()
    {
        return $this->hasmany('GoodsImages', 'goods_id', 'id');
    }

    public function categoryBind()
    {
        return $this->belongsTo('Category', 'cate_id', 'id');
    }
}
