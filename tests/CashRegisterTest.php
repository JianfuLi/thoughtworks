<?php
/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/24
 * Time: 13:28
 */

namespace JeffLi\ThoughtWorks\Test;

use JeffLi\ThoughtWorks\CashRegister;

class CashRegisterTest extends \PHPUnit_Framework_TestCase
{
    function testPrintSingleProduct()
    {
        $cashRegister = new CashRegister();

        $this->assertEquals(
            '***<没钱赚商店>购物清单***' . PHP_EOL .
            '名称：可口可乐，数量：1瓶，单价：3.00(元)，小计：3.00(元)' . PHP_EOL .
            '----------------------' . PHP_EOL .
            '总计：3.00(元)' . PHP_EOL .
            '**********************'
            , $cashRegister->print('["ITEM000001"]')
        );

        $this->assertEquals(
            '***<没钱赚商店>购物清单***' . PHP_EOL .
            '名称：可口可乐，数量：2瓶，单价：3.00(元)，小计：6.00(元)' . PHP_EOL .
            '----------------------' . PHP_EOL .
            '总计：6.00(元)' . PHP_EOL .
            '**********************'
            , $cashRegister->print('["ITEM000001","ITEM000001"]')
        );

        $this->assertEquals(
            '***<没钱赚商店>购物清单***' . PHP_EOL .
            '名称：可口可乐，数量：3瓶，单价：3.00(元)，小计：9.00(元)' . PHP_EOL .
            '----------------------' . PHP_EOL .
            '总计：9.00(元)' . PHP_EOL .
            '**********************'
            , $cashRegister->print('["ITEM000001","ITEM000001","ITEM000001"]')
        );

        $this->assertEquals(
            '***<没钱赚商店>购物清单***' . PHP_EOL .
            '名称：可口可乐，数量：5瓶，单价：3.00(元)，小计：15.00(元)' . PHP_EOL .
            '----------------------' . PHP_EOL .
            '总计：15.00(元)' . PHP_EOL .
            '**********************'
            , $cashRegister->print('["ITEM000001","ITEM000001","ITEM000001","ITEM000001-2"]')
        );
    }


    function testPrintMultiProduct()
    {
        $cashRegister = new CashRegister();

        $this->assertEquals(
            '***<没钱赚商店>购物清单***' . PHP_EOL .
            '名称：可口可乐，数量：1瓶，单价：3.00(元)，小计：3.00(元)' . PHP_EOL .
            '名称：羽毛球，数量：1个，单价：5.00(元)，小计：5.00(元)' . PHP_EOL .
            '----------------------' . PHP_EOL .
            '总计：8.00(元)' . PHP_EOL .
            '**********************'
            , $cashRegister->print('["ITEM000001","ITEM000002"]')
        );
    }
}
