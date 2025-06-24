<?php
function dd($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

//使用get取得form傳來的資料，資料從name屬性取得
$data = $_GET;

// $num1 = $data['num1'];
// $num2 = $data['num2'];
$num1 = floatval($data['num1'] ?? 0);
$num2 = floatval($data['num2'] ?? 0);
$opt = $data['opt'];

$result = null;

switch ($opt) {
    case '+':
        $result = $num1 + $num2;
        break;
        print_r($opt);
    case '-':
        $result = $num1 - $num2;

        break;

    case '*':
        $result = $num1 * $num2;

        break;

    case '/':
        if ($num2 == 0) {
            $result = '除數不可為 0';
        } else {
            $result = $num1 / $num2;
        }

        break;

    default:
        break;
}


$data['result'] = $result;
dd($data);
