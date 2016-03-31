<?php
/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/25
 * Time: 15:21
 */

namespace JeffLi\CashRegister;


/**
 * Class Product
 * @package JeffLi\CashRegister
 */
class Product
{
    /**
     * @var string 名称
     */
    public $name;
    /**
     * @var string 单位
     */
    public $unit;
    /**
     * @var string 代码
     */
    public $code;
    /**
     * @var string 分类
     */
    public $type;
    /**
     * @var float 价格
     */
    public $price;

    /**
     * Product constructor.
     *
     * @param $code
     * @param $name
     * @param $unit
     * @param $price
     * @param $type
     */
    public function __construct($code, $name, $unit, $price, $type = null)
    {
        $this->name = $name;
        $this->unit = $unit;
        $this->code = $code;
        $this->type = $type;
        $this->price = $price;
    }


}