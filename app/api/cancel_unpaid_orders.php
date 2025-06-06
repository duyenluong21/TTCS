<?php

header('Content-Type: application/json');
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (php_sapi_name() !== 'cli' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$result = [];
$host = "localhost";
$usernam = "root";
$password = "";
$dbname = "quanlymaybay";

$mysqli = mysqli_connect($host, $usernam, $password, $dbname);
if ($mysqli->connect_error) {
    die(json_encode(["error" => "Kết nối thất bại: " . $conn->connect_error]));
}

$threshold = date('Y-m-d H:i:s', strtotime('-5 minutes'));

$sql = "SELECT maVeDaDat, maVe, maCB, soLuongDat
        FROM veDaDat
        WHERE trangThai = 0
          AND create_at < ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('s', $threshold);
$stmt->execute();
$result = $stmt->get_result();

$canceledCount = 0;

if ($result->num_rows > 0) {
    $mysqli->begin_transaction();
    try {
        while ($row = $result->fetch_assoc()) {
            $idVeDaDat   = (int)$row['maVeDaDat'];
            $maVe        = (int)$row['maVe'];
            $qtyDat      = (int)$row['soLuongDat'];
            $maCB = (int)$row['maCB'];

            $upd = "
                UPDATE soLuongVe
                SET soLuongCon = soLuongCon + ?
                WHERE maCB = ?
                AND maVe = ?
                LIMIT 1
            ";
            $uStmt = $mysqli->prepare($upd);
            $uStmt->bind_param('iii', $qtyDat, $maCB, $maVe);
            $uStmt->execute();
            $uStmt->close();

            $del = "DELETE FROM veDaDat WHERE maVeDaDat = ?";
            $dStmt = $mysqli->prepare($del);
            $dStmt->bind_param('i', $idVeDaDat);
            $dStmt->execute();
            $dStmt->close();

            $canceledCount++;
        }
        $mysqli->commit();
    } catch (Exception $e) {
        $mysqli->rollback();
        http_response_code(500);
        echo json_encode([
            'error'   => 'Failed to cancel orders',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

$stmt->close();
$mysqli->close();

echo json_encode([
    'status'         => 'success',
    'threshold_time' => $threshold,
    'canceled_count' => $canceledCount
]);
