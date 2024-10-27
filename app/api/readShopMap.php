<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once "../models/db.php";
include_once "../models/Airline.php"; // Bạn có thể bỏ cái này nếu không cần
include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == "GET"){
    if(isset($_GET['q'])){ // Lấy giá trị từ query 'q'
        $query = $_GET['q']; // Lấy tham số query từ URL
        $shop = searchShop($query); // Gọi hàm tìm kiếm
        echo $shop; // Trả kết quả dạng JSON
    } else {
        $data = [
            'status' => 400,
            'message' => 'No search query provided',
        ];
        echo json_encode($data);
    }
} else {
    $data = [
        'status' => 404,
        'message' => $requestMethod . ' Method not allowed',
    ];
    header("HTTP/1.0 404 Method not allowed");
    echo json_encode($data);
}
?>
