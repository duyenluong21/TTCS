<?php
header('Content-Type: application/json');

$config = [
    "app_id" => 2553,
    "key1" => "PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL",
    "key2" => "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz",
    "endpoint" => "https://sb-openapi.zalopay.vn/v2/refund"
];

$host = "localhost";
$username = "root";
$password = "";
$dbname = "quanlymaybay";

$order_id = isset($_POST['order_id']) ? trim($_POST['order_id']) : '';
if (empty($order_id)) {
    echo json_encode(["code" => 0, "message" => "Thiếu hoặc sai order_id."]);
    exit;
}

$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    echo json_encode(["code" => 0, "message" => "Kết nối thất bại: " . $mysqli->connect_error]);
    exit;
}

$stmt = $mysqli->prepare("SELECT app_trans_id, tongThanhToan, maCB, soLuongDat, maVe, zp_trans_id FROM veDaDat WHERE order_id = ? AND trangThai = 1");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(["code" => 0, "message" => "Không tìm thấy vé với order_id: $order_id"]);
    exit;
}

$app_trans_id = isset($row['app_trans_id']) ? trim((string)$row['app_trans_id']) : '';
$zp_trans_id = intval($row['zp_trans_id']);
$amount = (int)$row['tongThanhToan'];
$maCB = $row['maCB'];
$maVe = $row['maVe'];
$soLuongDat = (int)$row['soLuongDat'];
file_put_contents("zp_trans_id.txt", $zp_trans_id);

$timestamp = round(microtime(true) * 1000);
$uid = "$timestamp" . rand(111, 999);

$params = [
    "app_id" => $config["app_id"],
    "m_refund_id" => date("ymd") . "_" . $config["app_id"] . "_" . $uid,
    "timestamp" => $timestamp,
    "zp_trans_id" => $zp_trans_id,
    "amount" => $amount,
    "description" => "ZaloPay Intergration Demo"
];

$data = $params["app_id"] . "|" . $params["zp_trans_id"] . "|" . $params["amount"]
    . "|" . $params["description"] . "|" . $params["timestamp"];
file_put_contents("data.txt", $data);
$params["mac"] = hash_hmac("sha256", $data, $config["key1"]);
file_put_contents("debug_params.txt", json_encode($params, JSON_PRETTY_PRINT));

$context = stream_context_create([
    "http" => [
        "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
        "method"  => "POST",
        "content" => http_build_query($params)
    ]
]);

$response = file_get_contents($config["endpoint"], false, $context);
$result = json_decode($response, true);
file_put_contents("log_response.txt", $response);

if ($result && isset($result['return_code'])) {
    if ($result['return_code'] == 1) {

        $update = $mysqli->prepare("UPDATE veDaDat SET refund_status = 1 WHERE order_id = ?");
        $update->bind_param("s", $order_id);
        $update->execute();

        $updateSoLuong = $mysqli->prepare("UPDATE soLuongVe SET soLuongCon = soLuongCon + ? WHERE maCB = ? AND maVe = ?");
        $updateSoLuong->bind_param("iii", $soLuongDat, $maCB, $maVe);
        $updateSoLuong->execute();

        echo json_encode([
            "status" => 1,
            "message" => "Hoàn tiền thành công.",
            "data" => $result
        ]);
    } elseif ($result['return_code'] == 3) {
        
        $check = $mysqli->prepare("SELECT refund_request_time, refund_status FROM veDaDat WHERE order_id = ?");
        $check->bind_param("s", $order_id);
        $check->execute();
        $res = $check->get_result()->fetch_assoc();

        $now = new DateTime();
        $requestTime = $res['refund_request_time'] ? new DateTime($res['refund_request_time']) : null;

        if (!$requestTime) {
            $updateTime = $mysqli->prepare("UPDATE veDaDat SET refund_request_time = NOW() WHERE order_id = ?");
            $updateTime->bind_param("s", $order_id);
            $updateTime->execute();
            echo json_encode([
                "status" => 2,
                "message" => "Yêu cầu hoàn tiền đã được ghi nhận. Vui lòng kiểm tra lại sau vài phút.",
                "zalopay_response" => $result
            ]);
            exit;
        } else {
            $diff = $now->getTimestamp() - $requestTime->getTimestamp();
            if ($diff >= 180) {
                $update = $mysqli->prepare("UPDATE veDaDat SET refund_status = 1 WHERE order_id = ?");
                $update->bind_param("s", $order_id);
                $update->execute();

                $updateSoLuong = $mysqli->prepare("UPDATE soLuongVe SET soLuongCon = soLuongCon + ? WHERE maCB = ? AND maVe = ?");
                $updateSoLuong->bind_param("iii", $soLuongDat, $maCB, $maVe);
                $updateSoLuong->execute();

                echo json_encode([
                    "status" => 1,
                    "message" => "Hoàn tiền thành công (giả lập sau 3 phút).",
                    "data" => $result
                ]);
                exit;
            }
        }
    } else {
        echo json_encode([
            "status" => 0,
            "message" => "Hoàn tiền thất bại.",
            "error" => $result['return_message'] ?? "Không rõ lỗi",
            "zalopay_response" => $result
        ]);
    }
} else {
    echo json_encode([
        "status" => 0,
        "message" => "Lỗi không xác định",
        "response" => $result
    ]);
}

$stmt->close();
$mysqli->close();
