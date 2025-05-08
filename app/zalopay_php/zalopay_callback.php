<?php
$body = file_get_contents("php://input");
$data = json_decode($body, true);

$key2 = "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz";

$mac = hash_hmac("sha256", $data["data"], $key2);

if ($mac != $data["mac"]) {
    echo json_encode(["return_code" => -1, "return_message" => "MAC không hợp lệ"]);
    exit;
}

$payment_data = json_decode($data["data"], true);
$order_id = $payment_data["app_trans_id"];

$conn = new mysqli("localhost", "root", "", "quanlymaybay");
if ($conn->connect_error) {
    die("Lỗi kết nối DB");
}

$stmt = $conn->prepare("UPDATE vedadat SET trangThai = 1 WHERE order_id = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();

echo json_encode(["return_code" => 1, "return_message" => "Xác nhận thành công"]);
?>
