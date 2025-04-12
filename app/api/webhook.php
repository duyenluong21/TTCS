<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("ngrok-skip-browser-warning: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

session_start();
include 'function.php';

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

if (preg_match('/Đặt vé (\w+)/', $request['queryResult']['queryText'], $matches)) {
    $maCB = $matches[1];

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
                "lifespanCount" => 30,
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

    
            echo json_encode([
            "fulfillmentText" => $responseText,
            "outputContexts" => [
                [
                    "name" => $request['session'] . "/contexts/dat_ve_context",
                    "lifespanCount" => 15,
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
    } else {
        echo json_encode(["fulfillmentText" => "Không thể lấy thông tin khách hàng. Vui lòng thử lại."]);
        exit;
    }

} 
if (strtolower(trim($request['queryResult']['queryText'])) === "xác nhận đặt vé") {
    $maCB = $request['queryResult']['parameters']['maCB'] ?? null;
    $maKH = $request['queryResult']['parameters']['maKH'] ?? null;
    $hangVe = $request['queryResult']['parameters']['hangVe'] ?? null;
    $soLuong = $request['queryResult']['parameters']['soLuong'] ?? null;
    foreach ($request['queryResult']['outputContexts'] ?? [] as $context) {
        if (strpos($context['name'], 'dat_ve_context') !== false && isset($context['parameters'])) {
            $params = $context['parameters'];
            if (empty($maCB) && isset($params['maCB'])) $maCB = $params['maCB'];
            if (empty($maKH) && isset($params['maKH'])) $maKH = $params['maKH'];
            if (empty($hangVe) && isset($params['hangVe'])) $hangVe = $params['hangVe'];
            if (empty($soLuong) && isset($params['soLuong'])) $soLuong = $params['soLuong'];
        }
    }
    error_log("[DEBUG] Booking params: " . print_r([
        'maCB' => $maCB,
        'maKH' => $maKH,
        'hangVe' => $hangVe,
        'soLuong' => $soLuong
    ], true));

    $missing = [];
    if (empty($maCB)) $missing[] = 'mã chuyến bay';
    if (empty($maKH)) $missing[] = 'mã khách hàng';
    if (empty($hangVe)) $missing[] = 'hạng vé';
    if (empty($soLuong)) $missing[] = 'số lượng vé';

    if (!empty($missing)) {
        $errorMsg = "Thiếu thông tin: " . implode(', ', $missing) . ". Vui lòng bắt đầu lại từ đầu.";
        error_log("[ERROR] $errorMsg");
        echo json_encode(["fulfillmentText" => $errorMsg]);
        exit;
    }


    $flightInfo = getFlightApp(['maCB' => $maCB]);
    $flightData = json_decode($flightInfo, true);

    if (empty($flightData) || !isset($flightData['status']) || $flightData['status'] != 200) {
        error_log("[ERROR] Lỗi API chuyến bay: " . $flightInfo);
        echo json_encode(["fulfillmentText" => "Hiện không thể lấy thông tin giá vé. Vui lòng thử lại sau."]);
        exit;
    }

    $giaVe = 0;
    $chuyenBay = $flightData['data'];
    error_log("Data: " . print_r($chuyenBay, true));
    if (!empty($chuyenBay['maCB'])) {
        $maCB = $chuyenBay['maCB'];
        $sql = "SELECT giaVe FROM thongtinchuyenbay WHERE maCB = '$maCB' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $giaVe = (int)$row['giaVe'];
        }
    }

    if ($giaVe <= 0) {
        error_log("[ERROR] Không tìm thấy giá vé cho hạng $hangVe");
        echo json_encode(["fulfillmentText" => "Hiện không có vé hạng $hangVe. Vui lòng chọn hạng khác."]);
        exit;
    }


    $response = getMaVeFromHangVe(['hangVe' => $hangVe]);

    $responseData = json_decode($response, true);
    
    if ($responseData['status'] == 200) {
        $maVe = $responseData['data']['maVe'];
    } else {
        echo json_encode(["fulfillmentText" => "Không tìm thấy mã vé cho hạng '$hangVe'."]);
    }

    $detailInput = [
        'order_id' => date("Y-m-d H:i:s"),
        'maVe' => $maVe,
        'maCB' => $maCB,
        'maKH' => $maKH,
        'soLuongDat' => $soLuong,
        'tongThanhToan' => $giaVe * $soLuong,
        'nguonDat' => 'app',
        'maShop' => null
    ];

    error_log("[INFO] Dữ liệu đặt vé: " . json_encode($detailInput));

    $result = storeDetailTicket($detailInput);
    $flightData = json_decode($result, true);

    if ($result && is_string($result)) {
        $result = json_decode($result, true);
    }
    
    if ($result && isset($result['status'])) {
        if ($result['status'] == 201) {
            unset($_SESSION['maCB'], $_SESSION['hangVe'], $_SESSION['soLuong']);
            echo json_encode(["fulfillmentText" => "Vé của bạn đã được đặt thành công!"]);
        } else {
            error_log("[ERROR] Lỗi DB: " . json_encode($result));
            echo json_encode(["fulfillmentText" => "Đã có lỗi xảy ra: {$result['messange']}"]);
        }
    } else {
        error_log("[ERROR] Kết quả không hợp lệ từ storeDetailTicket: " . json_encode($result));
        echo json_encode(["fulfillmentText" => "Lỗi hệ thống. Vui lòng liên hệ hỗ trợ."]);
    }

    $query = "SELECT soLuongCon FROM soluongve WHERE maCB = '$maCB' AND maVe = '$maVe' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $soLuongCon = $row['soLuongCon'] - $soLuong;

        $ticketParams = [
            'maCB' => $maCB,
            'maVe' => $maVe,
        ];

        $ticketInput = [
            'soLuongCon' => $soLuongCon,
        ];

        $updateResult = updateNumberOfTickets($ticketInput, $ticketParams);

        if ($updateResult) {
            $responseText = "Vé đã được đặt và số lượng vé còn lại đã được cập nhật.";
            echo json_encode(["fulfillmentText" => $responseText]);
        } else {
            $responseText = "Đặt vé thành công, nhưng không thể cập nhật số lượng vé còn lại.";
            echo json_encode(["fulfillmentText" => $responseText]);
        }
    } else {
        echo json_encode(["fulfillmentText" => "Không tìm thấy vé hoặc chuyến bay cho mã: $maCB"]);
    }
    exit;
} elseif (strtolower(trim($request['queryResult']['queryText'])) === "không xác nhận") {
    $responseText = "Bạn đã hủy đặt vé. Nếu cần hỗ trợ, hãy liên hệ tổng đài.";
    echo json_encode(["fulfillmentText" => $responseText]);
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
