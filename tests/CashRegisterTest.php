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
    function testPrint()
    {
        $expected = '';
        $cashRegister = new CashRegister();
        $actual = $cashRegister->print();
        $this->assertEquals($expected, $actual);
    }
}
