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
    const BUY_TWO_GET_ONE_FREE_TEMPLATE = '名称：%s，数量：%d%s';
    const SUM_TEMPLATE = '总计：%.2f(元)';

    protected $groups = [];
    protected $buyTowGetOneFreeCodes = [];

    /**
     * Printer constructor.
     *
     * @param array $buyTowGetOneFreeCodes
     */
    public function __construct(array $buyTowGetOneFreeCodes = [])
    {
        $this->buyTowGetOneFreeCodes = $buyTowGetOneFreeCodes;
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
        $buyTwoGetOneFree = false;
        foreach ($this->groups as $code => $count) {
            $product = ProductShelf::get($code);
            if (in_array($product->code, $this->buyTowGetOneFreeCodes) && $count > 2) {
                $buyTwoGetOneFree = true;
                $output[] = sprintf(self::ITEM_TEMPLATE,
                    $product->name,
                    $count,
                    $product->unit,
                    $product->price,
                    $product->price * ($count - 1)
                );
                $total += $product->price * ($count - 1);
            } else {
                $output[] = sprintf(self::ITEM_TEMPLATE,
                    $product->name,
                    $count,
                    $product->unit,
                    $product->price,
                    $product->price * $count
                );
                $total += $product->price * $count;
            }
        }
        if ($buyTwoGetOneFree) {
            $output[] = '----------------------';
            $output[] = '买二赠一商品：';
            foreach ($this->groups as $code => $count) {
                $product = ProductShelf::get($code);
                if (in_array($product->code, ['ITEM000001', 'ITEM000002']) && $count > 2) {
                    $output[] = sprintf(self::BUY_TWO_GET_ONE_FREE_TEMPLATE, $product->name, 1, $product->unit);
                }
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