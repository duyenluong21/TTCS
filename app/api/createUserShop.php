<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: POST") ;

include_once "../models/Airline.php";
include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == "POST"){

    $inputData = json_decode(file_get_contents("php://input"), true);
    if(empty($inputData)){
        $storeUserShop =  storeUserShop($_POST);

    }else{
        $storeUserShop = storeUserShop($inputData);
    }
    
   echo $storeUserShop;
}else{
    $data = [
        'status' => 404,
        'messange' => $requestMethod. 'Method not allowed',
    ];
    header("HTTP/1.0 404 Method not allowed");
    echo json_encode($data);
}
?>