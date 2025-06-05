<?php
$input = $_POST;

// 1 看起來比較聰明
foreach ($input as $key => $value) {
    # code...
}

// 2 實際比較好維護
$input['name'] = 'VVVVVIP';
$input['mobile'] = 'VVVVVIP';
$input['address'] = 'VVVVVIP';

$input['vip'] = 'VVVVVIP';



$data = $input;

