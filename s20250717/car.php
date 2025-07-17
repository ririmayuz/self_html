<?php
// $car = '紅色的汽車';
// $tank = '綠色的坦克車';

function dd($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}


class Car
{

    // porperties
    public $name;
    public $color;


    // methods
    function run()
    {
        echo "$this->color 的 $this->name 正在跑";
    }
}


// new
$car = new Car();
$car->name = '小客車';
$car->color = '紅色';
$car->run();

$tank = new Car();
$tank->name = '坦克車';
$tank->color = '綠色';
$tank->run();


dd($car);
dd($tank);
