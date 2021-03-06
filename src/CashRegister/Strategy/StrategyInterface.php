<?php

/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/31
 * Time: 14:58
 */
namespace JeffLi\CashRegister\Strategy;

use JeffLi\CashRegister\Product;

interface StrategyInterface
{
    function calculatePrice(Product $product, $count);

    function printLine(Product $product, $count);

    function isMatch(Product $product, $count);

    function printAdditional(array $codes, array $counts);
}