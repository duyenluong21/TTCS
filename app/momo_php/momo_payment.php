<?php
$maKH = isset($_POST['maKH']) ? $_POST['maKH'] : '';  
$orderId = isset($_POST['order_id']) ? $_POST['order_id'] : '';  

if (!empty($maKH) && !empty($orderId)) {
    $connection = mysqli_connect($db_host, $db_user, $db_password, $db_name);
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $query = "SELECT tongThanhToan FROM vedadat WHERE maKH = '$maKH' AND order_id = '$orderId' AND trangThai = 0 LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $tongThanhToan = $row['tongThanhToan'];
        
        echo json_encode(["status" => "success", "tongThanhToan" => $tongThanhToan]);
    } else {
        echo json_encode(["status" => "error", "message" => "Không tìm thấy giao dịch hoặc đã thanh toán"]);
    }
    mysqli_close($connection);
} else {
    echo json_encode(["status" => "error", "message" => "Thiếu thông tin maKH hoặc orderId"]);
}

$endpoint    = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = "MOMO";
$accessKey   = "F8BBA842ECF85";
$secretKey   = "K951B6PE1waDMi640xX08PD3vg6EkVlz";

$orderInfo    = "Thanh toán vé máy bay";
$amount       = strval($tongThanhToan);
$orderId      = time() . "";
$redirectUrl  = "http://192.168.1.7/TTCS/vnpay_php/thankyou_momo.php";
$ipnUrl       = "http://192.168.1.7/TTCS/vnpay_phpm/momo_ipn.php";
$extraData    = "";

$requestId = time() . "";
$requestType = "captureWallet";

$rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";

$signature = hash_hmac("sha256", $rawHash, $secretKey);
$data = [
    'partnerCode' => $partnerCode,
    'accessKey'   => $accessKey,
    'requestId'   => $requestId,
    'amount'      => $amount,
    'orderId'     => $orderId,
    'orderInfo'   => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl'      => $ipnUrl,
    'extraData'   => $extraData,
    'requestType' => $requestType,
    'signature'   => $signature,
    'lang'        => 'vi'
];

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$result = curl_exec($ch);
curl_close($ch);

$jsonResult = json_decode($result, true);

if (!empty($jsonResult['payUrl'])) {
    header('Location: ' . $jsonResult['payUrl']);
    exit;
} else {
    echo "Lỗi tạo đơn hàng MoMo: ";
    print_r($jsonResult);
}
