<?php
// $car = '紅色的汽車';
// $tank = '綠色的坦克車';
function dd($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

class Cat{
    //porperties
    public $name;
    public $food;

    //methods
    function eat(){
        echo "$this->name 在吃 $this->food<br>";
    }
}

//new
$cat = new Cat();
$cat->name = '小毬';
$cat->food = '超巨大龍蝦';


$cat2 = new Cat();
$cat2->name = '小鞠';
$cat2->food = '變種巨白菜';
$cat2->eat();

dd($cat);
dd($cat2);