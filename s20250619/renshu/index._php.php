<?php 
include "./data/data.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>練習</title>
    <style>
        table {
            width: 80%;
            margin: auto;
            text-align: center;
        }

        table,
        th,
        tr,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th>id</th>
            <th>name</th>
            <th>mobile</th>
        </tr>
        <?php foreach ($data as $key => $value):?>
            <tr>
                <td><?= $value['id']?></td>
                <td><?= $value['name']?></td>
                <td><?= $value['mobile']?></td>
            </tr>
        <?php endforeach;?>
    </table>
</body>
</html>