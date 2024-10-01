<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: GET"); 

include_once "../models/db.php";
include_once "../models/Airline.php";
include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == "GET"){
    if(isset($_GET['maKH'])){
        $ticketCount = getTicketCount($_GET['maKH']);
        echo $ticketCount;
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
?>
