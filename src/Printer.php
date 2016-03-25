<?php
/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/24
 * Time: 13:27
 */

namespace JeffLi\ThoughtWorks;


class Printer
{
    const ITEM_TEMPLATE = '名称：%s，数量：%d%s，单价：%.2f(元)，小计：%.2f(元)';
    const SUM_TEMPLATE = '总计：%.2f(元)';

    function print($json)
    {
        $codes = $this->transformCodes(json_decode($json));
        $groups = $this->groupByCode($codes);
        $output = ['***<没钱赚商店>购物清单***'];
        $total = 0.0;
        foreach ($groups as $code => $count) {
            $product = ProductShelf::get($code);
            $output[] = sprintf(self::ITEM_TEMPLATE,
                $product->name,
                $count,
                $product->unit,
                $product->price,
                $product->price * $count
            );
            $total += $product->price * $count;
        }
        $output[] = '----------------------';
        $output[] = sprintf(self::SUM_TEMPLATE, $total);
        $output[] = '**********************';

        return implode(PHP_EOL, $output);
    }

    function groupByCode(array $codes)
    {
        $result = [];
        array_walk($codes, function ($code) use (&$result) {
            if (array_key_exists($code, $result)) {
                $result[$code] = $result[$code] + 1;
            } else {
                $result[$code] = 1;
            }
        });
        return $result;
    }

    function transformCodes(array $items)
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