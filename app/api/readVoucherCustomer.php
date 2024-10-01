<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

// Include necessary files
include_once "../models/db.php";
include_once "../models/Airline.php";
include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "GET") {
    if (isset($_GET['maVoucher'])) {
        // Ensure 'maVoucher' parameter is present for getVoucher function
        if (!empty($_GET['maVoucher'])) {
            $maVoucher = mysqli_real_escape_string($conn, $_GET['maVoucher']);
            $voucher = getVoucher($maVoucher);
            echo $voucher;
        } else {
            $data = [
                'status' => 400,
                'message' => 'Missing maVoucher parameter',
            ];
            header("HTTP/1.0 400 Bad Request");
            echo json_encode($data);
        }
    } elseif (isset($_GET['maKH'])) {
        // Ensure 'maKH' parameter is present for getVoucherVIP function
        if (!empty($_GET['maKH'])) {
            $maKH = mysqli_real_escape_string($conn, $_GET['maKH']);
            $getVoucherCustomer = getVoucherCustomerList($maKH);
            echo $getVoucherCustomer;
        } else {
            $data = [
                'status' => 400,
                'message' => 'Missing maKH parameter',
            ];
            header("HTTP/1.0 400 Bad Request");
            echo json_encode($data);
        }
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
?>
