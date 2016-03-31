<?php
/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/31
 * Time: 15:07
 */

namespace JeffLi\ThoughtWorks\Strategy;

use JeffLi\ThoughtWorks\Product;

class DiscountStrategy implements StrategyInterface
{
    const TEMPLATE = '名称：%s，数量：%d%s，单价：%.2f(元)，小计：%.2f(元)，节省%.2f(元)';

    protected $discount;

    protected $codes = [];

    /**
     * DiscountStrategy constructor.
     *
     * @param array $codes
     * @param $discount
     */
    public function __construct(array $codes, $discount)
    {
        $this->codes = $codes;
        $this->discount = $discount;
    }

    function isMatch(Product $product, $count)
    {
        return in_array($product->code, $this->codes);
    }

    protected function getPrice(Product $product, $count)
    {
        return $product->price * $count;
    }

    function calculatePrice(Product $product, $count)
    {
        return $this->getPrice($product, $count) * $this->discount;
    }

    function printLine(Product $product, $count)
    {
        $price = $this->calculatePrice($product, $count);
        $off = $this->getPrice($product, $count) - $price;
        $output = sprintf(self::TEMPLATE,
            $product->name,
            $count,
            $product->unit,
            $product->price,
            $price,
            $off
        );
        return $output;
    }

    function printAdditional(array $codes, array $counts)
    {
        // TODO: Implement printAdditional() method.
    }
}