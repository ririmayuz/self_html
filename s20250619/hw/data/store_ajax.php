<?php
$data = $_GET;

$num1 = floatval($data['num1'] ?? 0);
$num2 = floatval($data['num2'] ?? 0);
$opt = $data['opt']?? null;;

switch ($opt) {
    case '+':
        $result = $num1 + $num2;
        break;
    case '-':
        $result = $num1 - $num2;
        break;
    case '*':
        $result = $num1 * $num2;
        break;
    case '/':
        $result = ($num2 == 0) ? '除數不可為 0' : $num1 / $num2;
        break;
    default:
        $result = '未知運算符號';
}

$data['result'] = $result;

echo json_encode([
    'result' => $result,
    'opt' => $opt,
    'num1' => $num1,
    'num2' => $num2
]);