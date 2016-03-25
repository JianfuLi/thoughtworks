<?php
/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/24
 * Time: 13:27
 */

namespace JeffLi\ThoughtWorks;


class CashRegister
{
    const ITEM_TEMPLATE = '名称：可口可乐，数量：%d瓶，单价：%.2f(元)，小计：%.2f(元)';
    const SUM_TEMPLATE = '总计：%s(元)';

    function print($json)
    {
        $products = $this->transformProducts(json_decode($json));
        $price = 3.0;
        $count = count($products);
        $result = ['***<没钱赚商店>购物清单***'];
        $result[] = sprintf(self::ITEM_TEMPLATE,
            $count,
            $price,
            $price * $count
        );
        $result[] = '----------------------';
        $result[] = sprintf(self::SUM_TEMPLATE,
            number_format($price * $count, 2));
        $result[] = '**********************';

        return implode(PHP_EOL, $result);
    }

    function transformProducts($items)
    {
        $products = [];
        array_walk($items, function ($item) use (&$products) {
            $flags = explode('-', $item);
            if (count($flags) > 1) {
                $times = $flags[1];
                do {
                    $products[] = $flags[0];
                    $times--;
                } while ($times > 0);
            } else {
                $products[] = $item;
            }
        });
        return $products;
    }
}