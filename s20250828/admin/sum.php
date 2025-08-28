<?php
    // cors fix
    // 允許跨網域存取 或特定網域存取(舉例live server)
    header("Access-Control-Allow-Origin: *");
    // header("Access-Control-Allow-Origin: https://http://127.0.0.1:5500");


// 註解 寫在php內
// 如果寫在html , ajax接收檔案 就不會json

// 後台
// 1.接收菜單
// 2.菜單內容 麵線 甜不辣
// 3.煮 麵線 甜不辣
// 4.送餐 前面服務生 
// 5.送 麵線 甜不辣

function dd($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

$data = [
    [
        'id' => 1,
        'name' => 'amy'
    ],
    [
        'id' => 2,
        'name' => 'bob'
    ],
    [
        'id' => 3,
        'name' => 'cat'
    ]
];

echo json_encode($data);