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
    const BUY_95_OFF_TEMPLATE = '名称：%s，数量：%d%s，单价：%.2f(元)，小计：%.2f(元)，节省%.2f(元)';
    const SUM_TEMPLATE = '总计：%.2f(元)';

    protected $groups = [];
    protected $buyTowGetOneFreeCodes = [];
    protected $buy95OffCodes = [];

    /**
     * Printer constructor.
     *
     * @param array $buyTowGetOneFreeCodes
     * @param array $buy95OffCodes
     */
    public function __construct(array $buyTowGetOneFreeCodes = [], array $buy95OffCodes = [])
    {
        $this->buyTowGetOneFreeCodes = $buyTowGetOneFreeCodes;
        $this->buy95OffCodes = $buy95OffCodes;
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
            if ($this->isMatchBuyTwoGetOneFree($code, $count)) {
                $buyTwoGetOneFree = true;
                list($text, $price) = $this->printBuyTwoGetOneFree($product, $count);
            } else if ($this->isMatch95Off($code)) {
                list($text, $price) = $this->print95Off($product, $count);
            } else {
                list($text, $price) = $this->printNormal($product, $count);
            }
            $output[] = $text;
            $total += $price;
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

    protected function printBuyTwoGetOneFree($product, $count)
    {
        $price = $product->price * ($count - 1);
        $output = sprintf(self::ITEM_TEMPLATE,
            $product->name,
            $count,
            $product->unit,
            $product->price,
            $price
        );
        return [$output, $price];
    }

    protected function print95Off($product, $count)
    {
        $price = $product->price * $count * 0.95;
        $off = $product->price * $count * 0.05;
        $output = sprintf(self::BUY_95_OFF_TEMPLATE,
            $product->name,
            $count,
            $product->unit,
            $product->price,
            $price,
            $off
        );
        return [$output, $price];
    }

    protected function printNormal($product, $count)
    {
        $price = $product->price * $count;
        $output = sprintf(self::ITEM_TEMPLATE,
            $product->name,
            $count,
            $product->unit,
            $product->price,
            $price
        );
        return [$output, $price];
    }

    protected function isMatchBuyTwoGetOneFree($code, $count)
    {
        return in_array($code, $this->buyTowGetOneFreeCodes) && $count > 2;
    }

    protected function isMatch95Off($code)
    {
        return in_array($code, $this->buy95OffCodes);
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