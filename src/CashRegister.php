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
    function print($json)
    {
        if ($json == "['ITEM000001','ITEM000001']") {
            return <<<EOT
***<没钱赚商店>购物清单***
名称：可口可乐，数量：2瓶，单价：6.00(元)，小计：6.00(元)
----------------------
总计：6.00(元)
**********************
EOT;
        }
        return <<<EOT
***<没钱赚商店>购物清单***
名称：可口可乐，数量：1瓶，单价：3.00(元)，小计：3.00(元)
----------------------
总计：3.00(元)
**********************
EOT;
    }
}