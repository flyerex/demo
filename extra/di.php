<?php

namespace App\Classes\Catalog\client;

use App\Classes\Catalog\Interfaces\IRecordTemplate;
use App\Classes\Catalog\Interfaces\IClassProduct;

//Внедрение зависимости в RecordTemplate от IClassProduct
class RecordProduct implements IRecordTemplate
{
    public $object;

    public function __construct(IClassProduct $object)
    {
        $this->object = $object;
    }

    public function getPrice()
    {
        return $this->object->price;
    }
    public function getStock()
    {
        return $this->object->getStock();
    }

    public function __get($key)
    {
        return $this->object->$key;
    }
}
