<?php
$host = "localhost";
$usernam = "root";
$password = "";
$dbname = "quanlymaybay";

$conn = mysqli_connect($host, $usernam, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Kết nối thất bại: " . $conn->connect_error]));
}

// 2. Cấu hình ZaloPay
$config = [
    "app_id" => 2553,
    "key1" => "PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL",
    "key2" => "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz",
    "endpoint" => "https://sb-openapi.zalopay.vn/v2/create"
];

$tongThanhToan = isset($_POST['tongThanhToan']) ? (string)$_POST['tongThanhToan'] : 0;
$soLuongDat = isset($_POST['soLuongDat']) ? (string)$_POST['soLuongDat'] : 1;
$maKH = isset($_POST['maKH']) ? $_POST['maKH'] : '';
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';

if ($tongThanhToan <= 0 || $soLuongDat <= 0 || empty($maKH) || empty($order_id)) {
    echo json_encode(["error" => "Thiếu hoặc sai dữ liệu"]);
    exit;
}
$giaVe = $tongThanhToan / $soLuongDat;
$soLuong = $soLuongDat;

$embeddata = json_encode([
    "redirecturl" => "appflightbooking://home",
    "callback_url" => "https://16a2-222-252-22-40.ngrok-free.app/app/zalopay_php/zalopay_callback.php"
]);

$items = json_encode([[
    "itemid" => "ve",
    "itemname" => "Vé máy bay",
    "itemprice" => $giaVe,
    "itemquantity" => $soLuong,
]]);

$transID = microtime(true);
$app_trans_id = date("ymd") . "_" . str_replace('.', '', $transID);
$app_time = round(microtime(true) * 1000);
error_log("app time:", $app_time);

$order = [
    "app_id" => $config["app_id"],
    "app_time" => $app_time,
    "app_trans_id" => $app_trans_id,
    "app_user" => $maKH,
    "item" => $items,
    "embed_data" => $embeddata,
    "amount" => $tongThanhToan,
    "description" => "Thanh toán vé máy bay #$transID",
    "bank_code" => "",
    "callback_url" => "https://83d3-222-252-22-40.ngrok-free.app/app/zalopay_php/zalopay_callback.php"
];

// 5. Tính MAC
$data = $order["app_id"] . "|" . $order["app_trans_id"] . "|" . $order["app_user"] . "|" . $order["amount"]
    . "|" . $order["app_time"]. "|" . $order["embed_data"]  . "|" . $order["item"];
 file_put_contents("data_create.txt", $data);
$order["mac"] = hash_hmac("sha256", $data, $config["key1"]);

// Log giá trị của MAC
error_log("MAC: " . $order["mac"] . " (type: " . gettype($order["mac"]) . ")");

// 6. Gửi yêu cầu đến ZaloPay
$context = stream_context_create([
    "http" => [
        "header" => "Content-type: application/x-www-form-urlencoded\r\n",
        "method" => "POST",
        "content" => http_build_query($order)
    ]
]);

$resp = file_get_contents($config["endpoint"], false, $context);

if ($resp === FALSE) {
    echo json_encode(["error" => "Không kết nối được đến ZaloPay"]);
    exit;
}

$result = json_decode($resp, true);

// Log kết quả trả về từ ZaloPay
error_log("Response from ZaloPay: " . json_encode($result));
$stmt = $conn->prepare("UPDATE vedadat SET app_trans_id = ? WHERE order_id = ?");
$stmt->bind_param("ss", $app_trans_id, $order_id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    echo json_encode(["error" => "Cập nhật app_trans_id thất bại"]);
}
?>
