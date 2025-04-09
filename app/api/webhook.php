<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("ngrok-skip-browser-warning: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

session_start();

$requestData = file_get_contents("php://input");
file_put_contents("php://stderr", $requestData . "\n");
$request = json_decode($requestData, true);
$maKH = '';
if (isset($request['originalDetectIntentRequest']['payload']['maKH'])) {
    $maKH = $request['originalDetectIntentRequest']['payload']['maKH'];
    $_SESSION['maKH'] = $maKH;
} elseif (isset($_SESSION['maKH'])) {
    $maKH = $_SESSION['maKH'];
}

error_log("Mã khách hàng là: " . $maKH);

if (preg_match('/Đặt vé (\w+)/', $request['queryResult']['queryText'], $matches)) {
    $maCB = $matches[1];

    include 'function.php';
    $flightParams = ['maCB' => $maCB];
    $flightInfo = getFlightApp($flightParams);
    $flightData = json_decode($flightInfo, true);

    if (!isset($flightData['status']) || $flightData['status'] != 200) {
        echo json_encode(["fulfillmentText" => "Không tìm thấy thông tin chuyến bay cho mã: $maCB"]);
        exit;
    }

    $chuyenBay = $flightData['data'];
    $responseText = "Chi tiết chuyến bay:\n";
    $responseText .= "Mã chuyến bay: " . $chuyenBay['maCB'] . "\n";
    $responseText .= "Điểm đi: " . $chuyenBay['diaDiemDi'] . "\n";
    $responseText .= "Điểm đến: " . $chuyenBay['diaDiemDen'] . "\n";
    $responseText .= "Thời gian khởi hành: " . $chuyenBay['gioBay'] . "\n\n";

    if (!empty($chuyenBay['maVe']) && !empty($chuyenBay['soLuongCon']) && !empty($chuyenBay['hangVe'])) {
        $maVeArray = explode(', ', $chuyenBay['maVe']);
        $soLuongConArray = explode(', ', $chuyenBay['soLuongCon']);
        $hangVeArray = explode(', ', $chuyenBay['hangVe']);

        if (count($maVeArray) === count($soLuongConArray) && count($maVeArray) === count($hangVeArray)) {
            $danhSachVe = [];
            for ($i = 0; $i < count($maVeArray); $i++) {
                $danhSachVe[] = [
                    'maVe' => trim($maVeArray[$i]),
                    'hangVe' => trim($hangVeArray[$i]),
                    'soLuongCon' => trim($soLuongConArray[$i])
                ];
            }

            $responseText .= "Các hạng vé và số lượng ghế còn lại:\n";
            foreach ($danhSachVe as $ve) {
                $responseText .= "- Hạng vé: " . $ve['hangVe'] . "\n";
                $responseText .= "  Ghế còn: " . $ve['soLuongCon'] . "\n\n";
            }
        } else {
            $responseText .= "Lỗi: Dữ liệu vé không khớp, vui lòng thử lại sau.\n\n";
        }
    } else {
        $responseText .= "Không tìm thấy thông tin vé cho chuyến bay này.\n\n";
    }

    $responseText .= "Bạn có muốn hãy nhập hạng vé và số lượng vé mong muốn?\n";

    echo json_encode(["fulfillmentText" => $responseText]);
    exit;
}

if (preg_match('/(?:Tôi muốn đặt|Đặt|Muốn đặt)?\s*(?:hạng\s*)?(.+?)\s*(\d+)\s*v(?:é|e)(?:.*?chuyến bay\s*([A-Za-z0-9]+))?/iu', $request['queryResult']['queryText'], $matches)) {
    $hangVe = trim($matches[1]);
    $soLuong = (int) trim($matches[2]);

    $maCB = $request['queryResult']['parameters']['maCB'] ?? null;
    error_log("maCB", $maCB);

    if (empty($maCB)) {
        foreach ($request['queryResult']['outputContexts'] as $context) {
            if (strpos($context['name'], 'dat_ve_context') !== false && isset($context['parameters']['maCB'])) {
                $maCB = $context['parameters']['maCB'];
                break;
            }
        }
    }

    if (empty($maCB)) {
        echo json_encode(["fulfillmentText" => "Bạn vui lòng nhập mã chuyến bay trước khi đặt vé nhé."]);
        exit;
    }
    if ($soLuong <= 0) {
        echo json_encode(["fulfillmentText" => "Số lượng vé không hợp lệ. Vui lòng nhập lại."]);
        exit;
    }

    $responseText = "Bạn muốn đặt $soLuong vé cho hạng $hangVe cho chuyến bay $maCB. Xác nhận đặt vé? (Xác nhận/Không xác nhận)";

    echo json_encode([
        "fulfillmentText" => $responseText,
        "outputContexts" => [
            [
                "name" => $request['session'] . "/contexts/dat_ve_context",
                "lifespanCount" => 5,
                "parameters" => [
                    "hangVe" => $hangVe,
                    "soLuong" => $soLuong,
                    "maCB" => $maCB,
                    "maKH" => $maKH ?? null
                ]
            ]
        ]
    ]);
    exit;
}



if (strtolower(trim($request['queryResult']['queryText'])) === "xác nhận") {
    include 'function.php';
    $maKH = $request['originalDetectIntentRequest']['payload']['maKH'];
    if ($maKH === null) {
        echo json_encode(["fulfillmentText" => "Không tìm thấy mã khách hàng. Vui lòng đăng nhập hoặc cung cấp thông tin."]);
        exit;
    }

    $passengerParams = ['maKH' => $maKH];
    $passengerDataJson = getPassenger($passengerParams);
    $passengerData = json_decode($passengerDataJson, true);

    if ($passengerData['status'] == 200) {
        $kh = $passengerData['data'];
        $responseText = "Vui lòng xác nhận lại thông tin cá nhân:\n"
                      . "Tên: " . $kh['fullname'] . "\n"
                      . "SĐT: " . $kh['sDT'] . "\n"
                      . "Email: " . $kh['email'] . "\n"
                      . "Địa chỉ: " . $kh['diaChi'] . "\n\n"
                      . "Nếu thông tin chính xác, vui lòng nhắn 'Xác nhận đặt vé'.";

        echo json_encode(["fulfillmentText" => $responseText]);
        exit;
    } else {
        echo json_encode(["fulfillmentText" => "Không thể lấy thông tin khách hàng. Vui lòng thử lại."]);
        exit;
    }

} elseif (strtolower(trim($request['queryResult']['queryText'])) === "xác nhận đặt vé") {
    $responseText = "Vé của bạn đã được đặt thành công! Cảm ơn bạn.";
    echo json_encode(["fulfillmentText" => $responseText]);
    exit;
} elseif (strtolower(trim($request['queryResult']['queryText'])) === "không xác nhận") {
    $responseText = "Bạn đã hủy đặt vé. Nếu cần hỗ trợ, hãy liên hệ tổng đài.";
    echo json_encode(["fulfillmentText" => $responseText]);
    exit;
}


// if (preg_match('/(?:Tôi muốn đặt|Đặt|Muốn đặt)?\s*(?:hạng\s*)?(.+?)\s*(\d+)\s*v(?:é|e)/iu', $request['queryResult']['queryText'], $matches)) {
//     $hangVe = trim($matches[1]);
//     $soLuong = (int) trim($matches[2]);
//     // $maCB = $request['queryResult']['parameters']['maCB'] ?? null;

//     if (!$maKH) {
//         echo json_encode([
//             "fulfillmentText" => "Không tìm thấy mã khách hàng. Vui lòng đăng nhập hoặc cung cấp mã khách hàng."
//         ]);
//         exit;
//     }

//     if ($soLuong <= 0) {
//         echo json_encode([
//             "fulfillmentText" => "Số lượng vé không hợp lệ. Vui lòng nhập lại."
//         ]);
//         exit;
//     }

//     $responseText = "Bạn muốn đặt $soLuong vé cho hạng $hangVe";
//     // if ($maCB) $responseText .= " trên chuyến bay $maCB";
//     $responseText .= ". Xác nhận đặt vé? (Xác nhận/Không xác nhận)";

//     echo json_encode([
//         "fulfillmentText" => $responseText,
//         "outputContexts" => [
//             [
//                 "name" => "$session/contexts/datve_context",
//                 "lifespanCount" => 5,
//                 "parameters" => [
//                     "hangVe" => $hangVe,
//                     "soLuong" => $soLuong,
//                     "maKH" => $maKH,
//                     "maCB" => $maCB
//                 ]
//             ]
//         ]
//         ]);
//     exit;
// }


if (strtolower(trim($request['queryResult']['queryText'])) === "xác nhận") {
    $data = null;
    foreach ($contexts as $context) {
        if (strpos($context['name'], 'datve_context') !== false) {
            $data = $context['parameters'];
            break;
        }
    }

    $hangVe = $data['hangVe'] ?? null;
    $soLuong = $data['soLuong'] ?? null;
    $maKH = $data['maKH'] ?? null;
    $maCB = $data['maCB'] ?? null;

    error_log("Xác nhận: hangVe=$hangVe | soLuong=$soLuong | maCB=$maCB | maKH=$maKH");

    if (!$hangVe || !$soLuong || !$maKH || !$maCB) {
        echo json_encode(["fulfillmentText" => "Thông tin đặt vé không hợp lệ. Vui lòng thử lại."]);
        exit;
    }

    if (!isset($_SESSION['maKH']) && isset($request['originalDetectIntentRequest']['payload']['maKH'])) {
        $_SESSION['maKH'] = $request['originalDetectIntentRequest']['payload']['maKH'];
    }
    $detailInput = [
        'order_id' => uniqid(),
        'maVe' => strtoupper(substr(md5(time()), 0, 8)),
        'maCB' => $maCB,
        'maKH' => $maKH,
        'soLuongDat' => $soLuong,
        'tongThanhToan' => 1000000 * $soLuong,
        'nguonDat' => 'app',
    ];

    error_log("Chi tiết đặt vé: " . print_r($detailInput, true));

    storeDetailTicket($detailInput);
    unset($_SESSION['hangVe'], $_SESSION['soLuong'], $_SESSION['maKH']);

    echo json_encode(["fulfillmentText" => "Vé của bạn đã được đặt thành công! Cảm ơn bạn."]);
    exit;

} elseif (strtolower(trim($request['queryResult']['queryText'])) === "không xác nhận") {
    unset($_SESSION['hangVe'], $_SESSION['soLuong'], $_SESSION['maKH']);
    echo json_encode([
        "fulfillmentText" => "Bạn đã hủy đặt vé. Nếu cần hỗ trợ, hãy liên hệ tổng đài."
    ]);
    exit;
}
elseif (strtolower(trim($request['queryResult']['queryText'])) === "không xác nhận") {
    unset($_SESSION['hangVe'], $_SESSION['soLuong'], $_SESSION['maKH']);
    echo json_encode(["fulfillmentText" => "Bạn đã hủy đặt vé. Nếu cần hỗ trợ, hãy liên hệ tổng đài."]);
    exit;
}


if (!$request || !isset($request['queryResult']['parameters'])) {
    echo json_encode(["fulfillmentText" => "Bạn muốn bay từ đâu đến đâu và vào ngày nào?"]);
    exit;
}

$parameters = $request['queryResult']['parameters'];

$diaDiemDi = isset($parameters['diaDiemDi']) ? $parameters['diaDiemDi'] : null;
$diaDiemDen = isset($parameters['diaDiemDen']) ? $parameters['diaDiemDen'] : null;
$ngayDi = isset($parameters['ngayDi']) ? $parameters['ngayDi'] : null;

if ($diaDiemDi && !$diaDiemDen && !$ngayDi) {
    echo json_encode(["fulfillmentText" => "Bạn muốn bay từ $diaDiemDi đến đâu?"]);
    exit;
}
if ($diaDiemDen && !$diaDiemDi && !$ngayDi) {
    echo json_encode(["fulfillmentText" => "Bạn muốn bay từ đâu đến $diaDiemDen?"]);
    exit;
}

if ($diaDiemDi && $diaDiemDen && !$ngayDi) {
    echo json_encode(["fulfillmentText" => "Bạn muốn bay từ $diaDiemDi đến $diaDiemDen vào ngày nào?"]);
    exit;
}
if (!$diaDiemDi && !$diaDiemDen && !$ngayDi && !$maCB) {
    echo json_encode(["fulfillmentText" => "Bạn vui lòng cung cấp thông tin về địa điểm đi, địa điểm đến và ngày bay nhé!"]);
    exit;
}

include 'function.php';
$flightData = findFlight([
    "diaDiemDi" => $diaDiemDi,
    "diaDiemDen" => $diaDiemDen,
    "ngayDi" => $ngayDi
]);

if (!$flightData) {
    echo json_encode(["fulfillmentText" => "Lỗi hệ thống: Không thể lấy dữ liệu chuyến bay."]);
    exit;
}

$flightDataArray = json_decode($flightData, true);

if (!isset($flightDataArray['status'])) {
    echo json_encode(["fulfillmentText" => "Lỗi hệ thống: Dữ liệu trả về không hợp lệ."]);
    exit;
}

if ($flightDataArray['status'] == 200 && !empty($flightDataArray['flights'])) {
    $ngayDiFormatted = date("d/m/Y", strtotime($ngayDi));
    $flightList = "Danh sách chuyến bay từ $diaDiemDi đến $diaDiemDen vào ngày $ngayDiFormatted:\n\n";

    foreach ($flightDataArray['flights'] as $flight) {
        $flightList .= "Mã CB: {$flight['maCB']}\n";
        $flightList .= "Giờ bay: {$flight['gioBay']}\n";
        $flightList .= "Giá: " . number_format($flight['giaVe'], 0, ',', '.') . " VND\n";
        $flightList .= "Để đặt vé, nhập: 'Đặt vé {$flight['maCB']}'\n";
    }

    echo json_encode(["fulfillmentText" => $flightList]);
} else {
    $ngayDiFormatted = date("d/m/Y", strtotime($ngayDi));
    echo json_encode(["fulfillmentText" => "Không có chuyến bay nào từ $diaDiemDi đến $diaDiemDen vào ngày $ngayDiFormatted. Bạn có muốn thử một ngày khác không?"]);
}

ob_end_flush();
