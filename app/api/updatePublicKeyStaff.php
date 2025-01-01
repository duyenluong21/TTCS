<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT") ;

include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == "PUT"){

    $inputData = json_decode(file_get_contents("php://input"), true);
        $updatePublicKey = updatePublicKeyStaff($inputData,$_GET);
    echo $updatePublicKey;
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