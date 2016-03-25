<?php
/**
 * Created by PhpStorm.
 * User: JeffLi
 * Date: 16/3/25
 * Time: 15:21
 */

namespace JeffLi\ThoughtWorks;


class Product
{
    public $name;
    public $unit;
    public $code;
    public $type;
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