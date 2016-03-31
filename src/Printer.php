<?php
/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/24
 * Time: 13:27
 */

namespace JeffLi\ThoughtWorks;

use JeffLi\ThoughtWorks\Strategy\BuyGetFreeStrategy;
use JeffLi\ThoughtWorks\Strategy\StrategyInterface;
use JeffLi\ThoughtWorks\Strategy\DefaultStrategy;

/**
 * Class Printer
 * @package JeffLi\ThoughtWorks
 */
class Printer
{
    /**
     * @var array
     */
    protected $groups = [];
    /**
     * @var array
     */
    protected $strategies = [];

    /**
     * Printer constructor.
     */
    public function __construct()
    {
        $this->strategies[] = new DefaultStrategy();
    }

    /**
     * @param StrategyInterface $strategy
     */
    function addStrategy(StrategyInterface $strategy)
    {
        array_unshift($this->strategies, $strategy);
    }

    /**
     * @param string $json
     */
    function append($json)
    {
        $codes = $this->transformCodes(json_decode($json));
        $groups = $this->groupByCodes($codes);
        $this->mergeCodes($groups);
    }

    /**
     * @return string
     */
    function print()
    {
        $output = $this->printHeader();
        $output = array_merge($output, $this->printProductList());
        $output = array_merge($output, $this->printAdditional());
        $output = array_merge($output, $this->printFooter());
        return implode(PHP_EOL, $output);
    }

    /**
     * @param Product $product
     * @param int $count
     *
     * @return StrategyInterface
     */
    protected function getStrategy(Product $product, $count)
    {
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

        return is_null($priorityStrategy) ? reset($strategies) : $priorityStrategy;
    }

    /**
     * @return array
     */
    protected function printHeader()
    {
        return ['***<没钱赚商店>购物清单***'];
    }

    /**
     * @return array
     */
    protected function printFooter()
    {
        $output = [];
        $output[] = '----------------------';
        $output[] = sprintf('总计：%.2f(元)', $this->calculateProductList());
        $output[] = '**********************';
        return $output;
    }

    /**
     * @return array
     */
    protected function printProductList()
    {
        $output = [];
        foreach ($this->groups as $code => $count) {
            $product = ProductShelf::get($code);
            $strategy = $this->getStrategy($product, $count);
            $output[] = $strategy->printLine($product, $count);
        }
        return $output;
    }

    /**
     * @return array
     */
    protected function printAdditional()
    {
        $output = [];
        foreach ($this->strategies as $strategy) {
            $additional = $strategy->printAdditional(
                array_keys($this->groups),
                array_values($this->groups)
            );
            if (is_array($additional) && !empty($additional)) {
                $output = array_merge($output, $additional);
            }
        }
        return $output;
    }

    /**
     * @return float
     */
    protected function calculateProductList()
    {
        $total = 0.0;
        foreach ($this->groups as $code => $count) {
            $product = ProductShelf::get($code);
            $strategy = $this->getStrategy($product, $count);
            $total += $strategy->calculatePrice($product, $count);
        }
        return $total;
    }

    /**
     * @param array $groups
     */
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

    /**
     * @param array $codes
     *
     * @return array
     */
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

    /**
     * @param array $items
     *
     * @return array
     */
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