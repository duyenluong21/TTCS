<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once "../models/Airline.php";
include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == "GET"){

    // Lấy chuỗi tìm kiếm từ query string (URL)
    if (isset($_GET['searchQuery'])) {
        $searchQuery = $_GET['searchQuery'];

        // Gọi hàm searchCustomer để tìm kiếm khách hàng
        $searchCustomer = searchCustomer($searchQuery);

        // Trả về kết quả tìm kiếm
        echo $searchCustomer;
    } else {
        $data = [
            'status' => 400,
            'message' => 'Vui lòng nhập thông tin cần tìm kiếm',
        ];
        header("HTTP/1.0 400 Bad Request");
        echo json_encode($data);
    }
    
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method not allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
