<?php
/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/31
 * Time: 15:18
 */

namespace JeffLi\CashRegister\Strategy;

use JeffLi\CashRegister\Product;

class DefaultStrategy implements StrategyInterface
{
    const TEMPLATE = '名称：%s，数量：%d%s，单价：%.2f(元)，小计：%.2f(元)';

    function calculatePrice(Product $product, $count)
    {
        return $product->price * $count;
    }

    function printLine(Product $product, $count)
    {
        return sprintf(self::TEMPLATE,
            $product->name,
            $count,
            $product->unit,
            $product->price,
            $this->calculatePrice($product, $count)
        );
    }

    function printAdditional(array $codes, array $counts)
    {
        // TODO: Implement printAdditional() method.
    }

    function isMatch(Product $product, $count)
    {
        return true;
    }
}