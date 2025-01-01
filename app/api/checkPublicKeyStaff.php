<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "GET") {

    if (isset($_GET['maNV'])) {
        $maKH = $_GET['maNV'];

        $hasPublicKey = checkIfUserHasPublicKeyStaff($maNV);

        if ($hasPublicKey) {
            $response = [
                'status' => 200,
                'message' => 'Khách hàng đã có public key.',
                'hasPublicKey' => true
            ];
            header("HTTP/1.0 200 OK");
            echo json_encode($response);
        } else {
            $response = [
                'status' => 404,
                'message' => 'Khách hàng chưa có public key.',
                'hasPublicKey' => false
            ];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($response);
        }
    } else {
        $response = [
            'status' => 400,
            'message' => 'Thiếu tham số maNV.'
        ];
        header("HTTP/1.0 400 Bad Request");
        echo json_encode($response);
    }
} else {
    $response = [
        'status' => 405,
        'message' => 'Method Not Allowed'
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($response);
}
ob_end_flush();
?>