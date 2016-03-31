<?php
/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/24
 * Time: 13:27
 */

namespace JeffLi\ThoughtWorks;


use JeffLi\ThoughtWorks\Strategy\BuyGetFreeStrategy;
use JeffLi\ThoughtWorks\Strategy\DefaultStrategy;
use JeffLi\ThoughtWorks\Strategy\StrategyInterface;

class Printer
{
    const ITEM_TEMPLATE = '名称：%s，数量：%d%s，单价：%.2f(元)，小计：%.2f(元)';
    const BUY_TWO_GET_ONE_FREE_TEMPLATE = '名称：%s，数量：%d%s';
    const BUY_95_OFF_TEMPLATE = '名称：%s，数量：%d%s，单价：%.2f(元)，小计：%.2f(元)，节省%.2f(元)';
    const SUM_TEMPLATE = '总计：%.2f(元)';

    protected $groups = [];
    protected $strategies = [];

    /**
     * Printer constructor.
     */
    public function __construct()
    {
        $this->strategies[] = new DefaultStrategy();
    }

    function addStrategy(StrategyInterface $strategy)
    {
        array_unshift($this->strategies, $strategy);
    }

    function append($json)
    {
        $codes = $this->transformCodes(json_decode($json));
        $groups = $this->groupByCodes($codes);
        $this->mergeCodes($groups);
    }


    function print()
    {
        $output = ['***<没钱赚商店>购物清单***'];
        $total = 0.0;

        foreach ($this->groups as $code => $count) {
            $product = ProductShelf::get($code);
            $priorityStrategy = null;
            $strategies = array_filter($this->strategies,
                function (StrategyInterface $strategy) use ($product, $count, &$priorityStrategy) {
                    $isMatch = $strategy->isMatch($product, $count);
                    if ($isMatch && $strategy instanceof BuyGetFreeStrategy) {
                        $priorityStrategy = $strategy;
                    }
                    return $isMatch;
                }
            );

            $strategy = is_null($priorityStrategy) ? reset($strategies) : $priorityStrategy;
            $output[] = $strategy->printLine($product, $count);
            $total += $strategy->calculatePrice($product, $count);
        }
        foreach ($this->strategies as $strategy) {
            $additional = $strategy->printAdditional(
                array_keys($this->groups),
                array_values($this->groups)
            );
            if (is_array($additional) && !empty($additional)) {
                $output = array_merge($output, $additional);
            }
        }
        $output[] = '----------------------';
        $output[] = sprintf(self::SUM_TEMPLATE, $total);
        $output[] = '**********************';

        return implode(PHP_EOL, $output);
    }

    protected function mergeCodes(array $groups)
    {
        foreach ($groups as $code => $count) {
            if (array_key_exists($code, $this->groups)) {
                $this->groups[$code] = $this->groups[$code] + $count;
            } else {
                $this->groups[$code] = $count;
            }
        }
    }

    protected function groupByCodes(array $codes)
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

    protected function transformCodes(array $items)
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