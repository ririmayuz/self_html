<?php 
function dd($data){
    echo "<pre>";
    print_r($data);
    echo "/<pre>";
}
$data = [
    [
        'id' => 1,
        'num1' => '100',
        'num2' => '50',
        'obt' => '+',
        'value' => '150',
    ],
    [
        'id' => 2,
        'num1' => '150',
        'num2' => '50',
        'obt' => '-',
        'value' => '100',
    ],
    [
        'id' => 3,
        'num1' => '10',
        'num2' => '50',
        'obt' => '*',
        'value' => '500',
    ],
    [
        'id' => 4,
        'num1' => '100',
        'num2' => '50',
        'obt' => '/',
        'value' => '2',
    ]
];

// dd($data);
echo json_encode($data);
?>
