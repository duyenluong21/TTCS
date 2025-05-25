<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "GET") {
    if (isset($_GET['maCB']) && isset($_GET['maVe'])) {
        $params = [
            'maCB' => $_GET['maCB'],
            'maVe' => $_GET['maVe']
        ];
        $response = getSoLuongCon($params);
        echo $response;
    } else {
        $data = [
            'status' => 422,
            'message' => 'Thiếu maCB hoặc maVe'
        ];
        header("HTTP/1.0 422 Unprocessable Entity");
        echo json_encode($data);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method not allowed'
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
ob_end_flush();
?>
