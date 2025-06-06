<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: POST") ;

include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == "POST"){

    $inputData = json_decode(file_get_contents("php://input"), true);
    if(empty($inputData)){
        $storeDetailTicket =  storeDetailTicket($_POST);

    }else{
        $storeDetailTicket = storeDetailTicket($inputData);
    }
    
   echo $storeDetailTicket;
}else{
    $data = [
        'status' => 404,
        'messange' => $requestMethod. 'Method not allowed',
    ];
    header("HTTP/1.0 404 Method not allowed");
    echo json_encode($data);
}
ob_end_flush();
?>