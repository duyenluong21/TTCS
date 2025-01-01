<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "GET") {
    if (isset($_GET['maVoucher'])) {
        $voucher = getVoucher($_GET['maVoucher']); // Pass only 'maVoucher'
        echo $voucher;
    } elseif (isset($_GET['maKH'])) {
        $maKH = $_GET['maKH']; // Ensure this is properly sanitized in function
        $getVoucherCustomerNewList = getVoucherCustomerNewList($maKH); // Pass 'maKH'
        echo $getVoucherCustomerNewList;
    } else {
        $data = [
            'status' => 400,
            'message' => 'Missing required parameters',
        ];
        header("HTTP/1.0 400 Bad Request");
        echo json_encode($data);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
ob_end_flush();
?>
