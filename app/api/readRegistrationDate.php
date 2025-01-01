<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: GET"); 

include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == "GET"){
    if(isset($_GET['maKH'])){
        $registrationDate = getRegistrationDate($_GET['maKH']);
        echo $registrationDate;
    }else{
        $data = [
            'status' => 400,
            'message' => 'maKH is required',
        ];
        header("HTTP/1.0 400 Bad Request");
        echo json_encode($data);
    }
}else{
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method not allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
ob_end_flush();
?>
