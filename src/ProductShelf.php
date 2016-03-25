<?php
/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/25
 * Time: 15:22
 */

namespace JeffLi\ThoughtWorks;


class ProductShelf
{
    static function all()
    {
        return [
            'ITEM000001' => new Product('ITEM000001', '可口可乐', '瓶', 3.0),
            'ITEM000002' => new Product('ITEM000002', '羽毛球', '个', 5.0),
            'ITEM000003' => new Product('ITEM000003', '苹果', '斤', 5.5),
        ];
    }

    /**
     * @param $code
     *
     * @return Product
     */
    static function get($code)
    {
        $all = self::all();
        return $all[$code];
    }
}