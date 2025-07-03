<?php
function dd($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

// 1.假資料
$data = [
    [
        'id'=> 2,
        'class'=> 'alert-info',
        'text'=> '提示1',
    ],
    [
        'id'=> 3,
        'class'=> 'alert-success',
        'text'=> '提示2',
    ],
       
    [
        'id'=> 4,
        'class'=> 'alert-warning',
        'text'=> '提示3',
    ],
        
    [
        'id'=> 5,
        'class'=> 'alert-danger',
        'text'=> '提示4',
    ],
        

];

// 2.真實DB
// $data = db , select all ...

// dd($data);
// array to json
echo json_encode($data);

// 1.php 產出 $data arry
// 2.轉換成json
// echo json_encode($data);
?>

