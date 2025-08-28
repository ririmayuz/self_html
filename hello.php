<?php

header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Origin: https://http://127.0.0.1:5500");



function dd($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

// $data = [
//     [
//         'id' => 1,
//         'name' => 'amy'
//     ],
//     [
//         'id' => 2,
//         'name' => 'bob'
//     ],
//     [
//         'id' => 3,
//         'name' => 'cat'
//     ]
// ];

$input = $_POST;

$data = [
    'id' => 14,
    'name' => '江雅茹',
    'msg'  => '您好 我吃一點'
];


// dd($data);
echo json_encode($data,JSON_UNESCAPED_UNICODE);