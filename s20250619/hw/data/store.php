<?php 
function dd($data){
    echo "<pre>";
    print_r($data);
    echo "/<pre>";
}

//使用get取得form傳來的資料，資料從name屬性取得
$num1 = $_GET['num1'];
$num2 = $_GET['num2'];
$opt = $_GET['opt'];

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
                    $result = $num1 / $num2;
                    
                    break; 

                default:
                    break;
            }
            
      


// dd($data);
echo json_encode($result);
?>
