<?php
/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/31
 * Time: 15:02
 */

namespace JeffLi\ThoughtWorks\Strategy;

use JeffLi\ThoughtWorks\Product;
use JeffLi\ThoughtWorks\ProductShelf;

class BuyGetFreeStrategy implements StrategyInterface
{
    const TEMPLATE = '名称：%s，数量：%d%s，单价：%.2f(元)，小计：%.2f(元)';

    const DISCOUNTED_TEMPLATE = '名称：%s，数量：%d%s';

    protected $baseCount;

    protected $freeCount;

    protected $codes = [];

    /**
     * BuyGetFreeStrategy constructor.
     *
     * @param array $codes
     * @param $baseCount
     * @param $freeCount
     */
    public function __construct(array $codes, $baseCount, $freeCount)
    {
        $this->codes = $codes;
        $this->baseCount = $baseCount;
        $this->freeCount = $freeCount;
    }

    function calculatePrice(Product $product, $count)
    {
        if ($count > $this->baseCount) {
            return $product->price * ($count - $this->freeCount);
        } else {
            return $product->price * $count;
        }
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

    function isMatch(Product $product, $count)
    {
        return in_array($product->code, $this->codes) && $count > $this->baseCount;
    }

    function printAdditional(array $codes, array $counts)
    {
        $output = [];
        $output[] = '----------------------';
        $output[] = '买二赠一商品：';
        for ($i = 0, $c = count($codes); $i < $c; $i++) {
            $code = $codes[$i];
            $count = $counts[$i];
            $product = ProductShelf::get($code);
            if ($this->isMatch($product, $count)) {
                $output[] = sprintf(self::DISCOUNTED_TEMPLATE, $product->name, $this->freeCount, $product->unit);
            }
        }
        return count($output) > 2 ? $output : [];
    }
}