<?php
require_once 'database.php';
require_once 'C:\xampp\htdocs\TTCS\app\api\vendor\autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

function error422($message)
{

    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}
function generateJWT($user)
{
    $payload = [
        "iat" => time(),
        "exp" => time() + 3600, // Token hết hạn sau 1 giờ
        "data" => [
            "maKH" => $user['maKH'],
            "fullname" => $user['fullname'],
            "email" => $user['email']
        ]
    ];

    $secretKey = "mySuperSecretKey";
    return JWT::encode($payload, $secretKey, 'HS256');
}

function verifyJWT()
{
    $secretKey = "mySuperSecretKey";
    $headers = apache_request_headers();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

    if (isset($authHeader) && strpos($authHeader, 'Bearer ') === 0) {
        $token = substr($authHeader, 7);

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            return $decoded;
        } catch (Exception $e) {

            http_response_code("401");
            echo json_encode(["message" => "Unauthorized access. Invalid token.", "error" => $e->getMessage()]);
            exit();
        }
    } else {
        http_response_code("401");
        echo json_encode(["message" => "Authorization header missing."]);
        exit();
    }
}

//-----------------------------------------------------------Airline-------------------------------------------------------
function storeAirline($airlineInput)
{
    global $conn;

    $tenMayBay = mysqli_real_escape_string($conn, $airlineInput['tenMayBay']);
    $hangMayBay = mysqli_real_escape_string($conn, $airlineInput['hangMayBay']);
    $gheToiDa = mysqli_real_escape_string($conn, $airlineInput['gheToiDa']);
    // Kiểm tra trùng lặp
    $duplicateQuery = "SELECT COUNT(*) AS count FROM maybay 
      WHERE tenMayBay = '$tenMayBay' 
      AND hangMayBay = '$hangMayBay' 
      AND gheToiDa = '$gheToiDa'";
    $duplicateResult = mysqli_query($conn, $duplicateQuery);
    if ($duplicateResult) {
        $duplicateRow = mysqli_fetch_assoc($duplicateResult);
        $duplicateCount = $duplicateRow['count'];

        if ($duplicateCount > 0) {
            $data = [
                'status' => 400,
                'message' => 'Đã có máy bay này. Vui lòng kiểm tra lại.',
            ];
            header("HTTP/1.0 400 Bad Request");
            echo json_encode($data);
            return;
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error during duplicate check.',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }
    if (empty(trim($tenMayBay))) {
        return error422('Hãy nhập tên máy bay');
    } elseif (empty(trim($hangMayBay))) {
        return error422('Hãy nhập hãng may bay');
    } elseif (empty(trim($gheToiDa))) {
        return error422('Hãy nhập ghế tối đa');
    } else {
        $query = "INSERT INTO maybay (tenMayBay,hangMayBay,gheToiDa) VALUES ('$tenMayBay','$hangMayBay','$gheToiDa')";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Máy bay đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}
function getAirlineList()
{

    global $conn;
    $query = "SELECT * FROM maybay";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Customer List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No airline found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getAirline($airlineParams)
{
    global $conn;
    if ($airlineParams['maMB'] == null) {
        return error422('Nhập mã máy bay');
    }

    $airlineId = mysqli_real_escape_string($conn, $airlineParams['maMB']);
    $query = "SELECT * FROM maybay WHERE maMB = '$airlineId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Airline Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có máy bay nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function updateAirline($airlineInput, $airlineParams)
{
    global $conn;

    if (!isset($airlineParams['maMB'])) {
        return error422('Mã máy bay không tìm thấy');
    } elseif ($airlineParams['maMB'] == null) {
        return error422('Nhập mã máy bay');
    }

    $airlineId = intval(mysqli_real_escape_string($conn, $airlineParams['maMB']));
    $tenMayBay = mysqli_real_escape_string($conn, $_POST['tenMayBay']);
    $hangMayBay = mysqli_real_escape_string($conn, $_POST['hangMayBay']);
    $gheToiDa = mysqli_real_escape_string($conn, $_POST['gheToiDa']);

    if (empty(trim($tenMayBay))) {
        return error422('Hãy nhập tên máy bay');
    } elseif (empty(trim($hangMayBay))) {
        return error422('Hãy nhập hãng may bay');
    } elseif (empty(trim($gheToiDa))) {
        return error422('Hãy nhập ghế tối đa');
    } else {
        $query = "UPDATE maybay SET tenMayBay='$tenMayBay',hangMayBay = '$hangMayBay',gheToiDa = '$gheToiDa' WHERE maMB = '$airlineId' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 200,
                'messange' => 'Máy bay đã được sửa thành công',
            ];
            header("HTTP/1.0 200 Success");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}

function deleteAirline($airlineParams)
{
    global $conn;

    if (!isset($airlineParams['maMB'])) {
        return error422('Mã máy bay không tìm thấy');
    } elseif ($airlineParams['maMB'] == null) {
        return error422('Nhập mã máy bay');
    }

    $airlineId = mysqli_real_escape_string($conn, $airlineParams['maMB']);

    $query = "DELETE FROM maybay WHERE maMB = '$airlineId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 204,
            'messange' => 'Xóa thành công',
        ];
        header("HTTP/1.0 204 Deleted");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 404,
            'messange' => 'Không tìm thấy máy bay',
        ];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
}
//end airline

//----------------------------------Aiport---------------------------------------------------------------
function getAirportList()
{

    global $conn;
    $query = "SELECT * FROM sanbay";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Airport List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No airline found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getAirport($airportParams)
{
    global $conn;
    if ($airportParams['maSanBay'] == null) {
        return error422('Nhập mã máy bay');
    }

    $airportId = mysqli_real_escape_string($conn, $airportParams['maSanBay']);
    $query = "SELECT * FROM sanbay WHERE maSanBay = '$airportId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Airport Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có sân bay nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function storeAirport($airportInput)
{
    global $conn;

    $tenSanBay = mysqli_real_escape_string($conn, $airportInput['tenSanBay']);
    $diaDiem = mysqli_real_escape_string($conn, $airportInput['diaDiem']);
    // Kiểm tra trùng lặp
    $duplicateQuery = "SELECT COUNT(*) AS count FROM sanbay 
                   WHERE tenSanBay = '$tenSanBay' 
                   OR diaDiem = '$diaDiem' ";
    $duplicateResult = mysqli_query($conn, $duplicateQuery);
    if ($duplicateResult) {
        $duplicateRow = mysqli_fetch_assoc($duplicateResult);
        $duplicateCount = $duplicateRow['count'];

        if ($duplicateCount > 0) {
            $data = [
                'status' => 400,
                'message' => 'Đã có chuyến bay này. Vui lòng kiểm tra lại.',
            ];
            header("HTTP/1.0 400 Bad Request");
            echo json_encode($data);
            return;
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error during duplicate check.',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }
    if (empty(trim($tenSanBay))) {
        return error422('Hãy nhập tên sân bay');
    } elseif (empty(trim($diaDiem))) {
        return error422('Hãy nhập địa điểm');
    } else {
        $query = "INSERT INTO sanbay (tenSanBay, diaDiem) VALUES ('$tenSanBay','$diaDiem')";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Sân bay đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}

function deleteAirport($airportParams)
{
    global $conn;

    if (!isset($airportParams['maSanBay'])) {
        return error422('Mã sân bay không tìm thấy');
    } elseif ($airportParams['maSanBay'] == null) {
        return error422('Nhập mã sân bay');
    }

    $airportId = mysqli_real_escape_string($conn, $airportParams['maSanBay']);

    $query = "DELETE FROM sanbay WHERE maSanBay = '$airportId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 204,
            'messange' => 'Xóa thành công',
        ];
        header("HTTP/1.0 204 Deleted");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 404,
            'messange' => 'Không tìm thấy sân bay',
        ];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
}

function updateAirport($airportInput, $airportParams)
{
    global $conn;

    if (!isset($airportParams['maSanBay'])) {
        return error422('Mã sân bay không tìm thấy');
    } elseif ($airportParams['maSanBay'] == null) {
        return error422('Nhập mã sân bay');
    }

    $airportId = mysqli_real_escape_string($conn, $airportParams['maSanBay']);
    $tenSanBay = mysqli_real_escape_string($conn, $_POST['tenSanBay']);
    $diaDiem = mysqli_real_escape_string($conn, $_POST['diaDiem']);

    if (empty(trim($tenSanBay))) {
        return error422('Hãy nhập tên sân bay');
    } elseif (empty(trim($diaDiem))) {
        return error422('Hãy nhập địa điểm');
    } else {
        $query = "UPDATE sanbay SET tenSanBay='$tenSanBay',diaDiem = '$diaDiem' WHERE maSanBay = '$airportId' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 200,
                'messange' => 'Sân bay đã được sửa thành công',
            ];
            header("HTTP/1.0 200 Success");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}


//end airport

//--------------------------------------------------------------------Passenger----------------------------------
function getPassengerList()
{
    $decoded = verifyJWT();
    global $conn;
    $query = "SELECT * FROM khachhang";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Customer List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No customer found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
function getPassengerAccountList()
{

    global $conn;
    $query = "SELECT * FROM khachhang";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Customer List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No customer found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
function getPassengerAccount($passengerParams)
{
    global $conn;
    if ($passengerParams['maKH'] == null) {
        return error422('Nhập mã khách hàng');
    }

    $passengerId = mysqli_real_escape_string($conn, $passengerParams['maKH']);
    $query = "SELECT * FROM khachhang WHERE maKH = '$passengerId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Customer Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có khách hàng nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
function postPassengerAccount($email)
{
    global $conn;

    $query = "SELECT * FROM khachhang WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result); // Trả về thông tin người dùng nếu tìm thấy
    }

    return null; // Trả về null nếu không tìm thấy người dùng
}
function getPassenger($passengerParams)
{
    global $conn;
    if ($passengerParams['maKH'] == null) {
        return error422('Nhập mã khách hàng');
    }

    $passengerId = mysqli_real_escape_string($conn, $passengerParams['maKH']);
    $query = "SELECT * FROM khachhang WHERE maKH = '$passengerId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Customer Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có khách hàng nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function storePassenger($passengerInput)
{
    global $conn;

    $fullname = mysqli_real_escape_string($conn, $passengerInput['fullname']);
    $email = mysqli_real_escape_string($conn, $passengerInput['email']);
    $password = mysqli_real_escape_string($conn, $passengerInput['password']);
    $gioiTinh = mysqli_real_escape_string($conn, $passengerInput['gioiTinh']);
    $ngaySinh = mysqli_real_escape_string($conn, $passengerInput['ngaySinh']);
    $salt = mysqli_real_escape_string($conn, $passengerInput['salt']);
    $diaChi = mysqli_real_escape_string($conn, $passengerInput['diaChi']);
    $soDT = mysqli_real_escape_string($conn, $passengerInput['soDT']);
    $loaiHanhKhach = mysqli_real_escape_string($conn, $passengerInput['loaiHanhKhach']);
    $ngayDangKy = mysqli_real_escape_string($conn, $passengerInput['ngayDangKy']);


    if (empty(trim($fullname))) {
        return error422('Hãy nhập họ tên khách hàng');
    } elseif (empty(trim($email))) {
        return error422('Hãy nhập email khách hàng');
    } elseif (empty(trim($password))) {
        return error422('Hãy nhập password khách hàng');
    } elseif (empty(trim($ngaySinh))) {
        return error422('Hãy nhập ngày sinh khách hàng');
    } else {
        $query = "INSERT INTO khachhang (fullname, email, password, salt, gioiTinh, ngaySinh, diaChi, soDT, loaiHanhKhach, ngayDangKy)
        VALUES ('$fullname','$email', '$password', '$salt','$gioiTinh','$ngaySinh','$diaChi','$soDT','$loaiHanhKhach', '$ngayDangKy')";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Khách hàng đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}

function findFlight($params)
{
    global $conn;

    $diaDiemDi = mysqli_real_escape_string($conn, $params["diaDiemDi"]);
    $diaDiemDen = mysqli_real_escape_string($conn, $params["diaDiemDen"]);
    $ngayDi = date("Y-m-d", strtotime($params["ngayDi"]));

    $query = "SELECT maCB, gioBay, giaVe, diaDiemDi, diaDiemDen, ngayDi FROM thongtinchuyenbay WHERE diaDiemDi = ? AND diaDiemDen = ? AND ngayDi = ? ORDER BY gioBay ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $diaDiemDi, $diaDiemDen, $ngayDi);
    $stmt->execute();
    $result = $stmt->get_result();

    $flights = [];
    while ($row = $result->fetch_assoc()) {
        $flights[] = [
            "maCB" => $row["maCB"],
            "ngayBay" => $row["ngayDi"],
            "gioBay" => $row["gioBay"],
            "giaVe" => $row["giaVe"],
            "diaDiemDi" => $row["diaDiemDi"],
            "diaDiemDen" => $row["diaDiemDen"]
        ];
    }

    if (!empty($flights)) {
        return json_encode([
            "status" => 200,
            "message" => "Danh sách chuyến bay",
            "flights" => $flights
        ]);
    } else {
        return json_encode([
            "status" => 404,
            "message" => "Không có chuyến bay nào từ $diaDiemDi đến $diaDiemDen vào ngày $ngayDi."
        ]);
    }
}


function updateUserFingerprint($passengerInput, $passengerParams)
{
    global $conn;

    if (!isset($passengerParams['maKH'])) {
        return error422('Mã khách hàng không tìm thấy');
    } elseif ($passengerParams['maKH'] == null) {
        return error422('Nhập mã khách hàng');
    }

    $maKH = mysqli_real_escape_string($conn, $passengerParams['maKH']);
    $isFingerprintRegistered = isset($passengerInput['isFingerprintRegistered']) ? filter_var($passengerInput['isFingerprintRegistered'], FILTER_VALIDATE_BOOLEAN) : false;
    $query = "UPDATE khachhang SET isFingerprintRegistered='$isFingerprintRegistered' WHERE maKH='$maKH' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        $data = [
            'status' => 200,
            'messange' => 'Khách hàng đã được sửa thành công',
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Method not allowed");
        echo json_encode($data);
    }
}

function deletePassenger($passengerParams)
{
    global $conn;

    if (!isset($passengerParams['maKH'])) {
        return error422('Mã khách hàng không tìm thấy');
    } elseif ($passengerParams['maKH'] == null) {
        return error422('Nhập mã khách hàng');
    }

    $customerId = mysqli_real_escape_string($conn, $passengerParams['maKH']);

    $query = "DELETE FROM khachhang WHERE maKH = '$customerId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 204,
            'messange' => 'Xóa thành công',
        ];
        header("HTTP/1.0 204 Deleted");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 404,
            'messange' => 'Không tìm thấy khách hàng',
        ];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
}

function updatePassenger($passengerInput, $passengerParams)
{
    global $conn;

    if (!isset($passengerParams['maKH'])) {
        return error422('Mã khách hàng không tìm thấy');
    } elseif ($passengerParams['maKH'] == null) {
        return error422('Nhập mã khách hàng');
    }

    $maKH = mysqli_real_escape_string($conn, $passengerParams['maKH']);
    $fullname = mysqli_real_escape_string($conn, $passengerInput['fullname']);
    $email = mysqli_real_escape_string($conn, $passengerInput['email']);
    $gioiTinh = mysqli_real_escape_string($conn, $passengerInput['gioiTinh']);
    $ngaySinh = mysqli_real_escape_string($conn, $passengerInput['ngaySinh']);
    $soCCCD = mysqli_real_escape_string($conn, $passengerInput['soCCCD']);
    $diaChi = mysqli_real_escape_string($conn, $passengerInput['diaChi']);
    $soDT = mysqli_real_escape_string($conn, $passengerInput['soDT']);
    $loaiHanhKhach = mysqli_real_escape_string($conn, $passengerInput['loaiHanhKhach']);
    $query = "UPDATE khachhang SET fullname='$fullname',email = '$email', gioiTinh = '$gioiTinh', ngaySinh = '$ngaySinh',
        diaChi = '$diaChi', soDT = '$soDT' WHERE maKH = '$maKH' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        $data = [
            'status' => 200,
            'messange' => 'Khách hàng đã được sửa thành công',
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Method not allowed");
        echo json_encode($data);
    }
}

function updatePWPassenger($passengerInput, $passengerParams)
{
    global $conn;

    if (!isset($passengerParams['maKH'])) {
        return error422('Mã khách hàng không tìm thấy');
    } elseif ($passengerParams['maKH'] == null) {
        return error422('Nhập mã khách hàng');
    }

    if (!isset($passengerInput['password']) || $passengerInput['password'] == null) {
        return error422('Nhập mật khẩu mới');
    }

    $maKH = mysqli_real_escape_string($conn, $passengerParams['maKH']);
    $password = mysqli_real_escape_string($conn, $passengerInput['password']);
    $salt = isset($passengerInput['salt']) ? mysqli_real_escape_string($conn, $passengerInput['salt']) : '';

    $query = "UPDATE khachhang SET password = '$password', salt = '$salt' WHERE maKH = '$maKH' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 200,
            'message' => 'Khách hàng đã được sửa thành công',
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        $error = mysqli_error($conn); // Thêm chi tiết lỗi SQL
        $data = [
            'status' => 500,
            'message' => 'Internal server error: ' . $error,
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}


//end passenger

//--------------------------------------Account passenger---------------------------------------------------
function storeUser($userInput)
{
    global $conn;

    $hoNV = mysqli_real_escape_string($conn, $userInput['hoNV']);
    $tenNV = mysqli_real_escape_string($conn, $userInput['tenNV']);
    $chucVu = mysqli_real_escape_string($conn, $userInput['chucVu']);
    $trinhDoHocVan = mysqli_real_escape_string($conn, $userInput['trinhDoHocVan']);
    $kinhNghiem = mysqli_real_escape_string($conn, $userInput['kinhNghiem']);
    $username = mysqli_real_escape_string($conn, $userInput['username']);
    $passw = mysqli_real_escape_string($conn, $userInput['passw']);
    $salt = bin2hex(random_bytes(22));
    $hashedPassword = password_hash($passw,  PASSWORD_BCRYPT);
    // Kiểm tra trùng lặp
    $duplicateQuery = "SELECT COUNT(*) AS count FROM nhanvien 
                   WHERE hoNV = '$hoNV' 
                   AND tenNV = '$tenNV' 
                   AND chucVu = '$chucVu'
                   AND trinhDoHocVan = '$trinhDoHocVan'
                   AND username = '$username'
                   AND kinhNghiem = '$kinhNghiem'
                   AND passw = '$passw'";
    $duplicateResult = mysqli_query($conn, $duplicateQuery);
    if ($duplicateResult) {
        $duplicateRow = mysqli_fetch_assoc($duplicateResult);
        $duplicateCount = $duplicateRow['count'];

        if ($duplicateCount > 0) {
            $data = [
                'status' => 400,
                'message' => 'Đã có chuyến bay này. Vui lòng kiểm tra lại.',
            ];
            header("HTTP/1.0 400 Bad Request");
            echo json_encode($data);
            return;
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error during duplicate check.',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }
    if (empty(trim($hoNV))) {
        return error422('Hãy nhập họ nhân viên');
    } elseif (empty(trim($tenNV))) {
        return error422('Hãy nhập tên nhân viên');
    } elseif (empty(trim($chucVu))) {
        return error422('Hãy nhập chức vụ nhân viên');
    } elseif (empty(trim($trinhDoHocVan))) {
        return error422('Hãy nhập trình độ học vấn nhân viên');
    } elseif (empty(trim($kinhNghiem))) {
        return error422('Hãy nhập kinh nghiệm nhân viên');
    } elseif (empty(trim($username))) {
        return error422('Hãy nhập tài khoản nhân viên');
    } elseif (empty(trim($passw))) {
        return error422('Hãy nhập mật khẩu cho nhân viên');
    } else {
        $query = "INSERT INTO nhanvien (hoNV,tenNV,chucVu,trinhDoHocVan,kinhNghiem,username,passw,salt)
         VALUES ('$hoNV','$tenNV','$chucVu','$trinhDoHocVan','$kinhNghiem','$username','$hashedPassword','$salt')";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Nhân viên đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}
function getUserList()
{

    global $conn;
    $query = "SELECT * FROM nhanvien";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Customer List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No airline found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getUserChatList()
{

    global $conn;
    $query = "SELECT * FROM nhanvien where chucVu = 'Nhân viên tư vấn'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Customer List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No airline found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getUser($userParams)
{
    global $conn;
    if ($userParams['maNV'] == null) {
        return error422('Nhập mã nhân viên');
    }

    $userId = mysqli_real_escape_string($conn, $userParams['maNV']);
    $query = "SELECT * FROM nhanvien WHERE maNV = '$userId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'User Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có nhân viên nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function updateUser($userInput, $userParams)
{
    global $conn;

    if (!isset($userParams['maNV'])) {
        return error422('Mã nhân viên không tìm thấy');
    } elseif ($userParams['maNV'] == null) {
        return error422('Nhập mã nhân viên');
    }

    $maNV = mysqli_real_escape_string($conn, $userParams['maNV']);
    $hoNV = mysqli_real_escape_string($conn, $_POST['hoNV']);
    $tenNV = mysqli_real_escape_string($conn, $_POST['tenNV']);
    $ngaySinhNV = mysqli_real_escape_string($conn, $_POST['ngaySinhNV']);
    $sdtNV = mysqli_real_escape_string($conn, $_POST['sdtNV']);
    $chucVu = mysqli_real_escape_string($conn, $_POST['chucVu']);
    $trinhDoHocVan = mysqli_real_escape_string($conn, $_POST['trinhDoHocVan']);
    $kinhNghiem = mysqli_real_escape_string($conn, $_POST['kinhNghiem']);
    $trangThaiHoatDong = mysqli_real_escape_string($conn, $_POST['trangThaiHoatDong']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $passw = mysqli_real_escape_string($conn, $_POST['passw']);

    if (empty(trim($hoNV))) {
        return error422('Hãy nhập họ nhân viên');
    } elseif (empty(trim($tenNV))) {
        return error422('Hãy nhập tên nhân viên');
    } elseif (empty(trim($chucVu))) {
        return error422('Hãy nhập chức vụ nhân viên');
    } elseif (empty(trim($trinhDoHocVan))) {
        return error422('Hãy nhập trình độ học vấn nhân viên');
    } elseif (empty(trim($kinhNghiem))) {
        return error422('Hãy nhập kinh nghiệm nhân viên');
    } else {
        $query = "UPDATE nhanvien SET hoNV='$hoNV', tenNV = '$tenNV',username = '$username',
        passw='$passw',chucVu ='$chucVu',trinhDoHocVan = '$trinhDoHocVan',kinhNghiem = '$kinhNghiem',trangThaiHoatDong = '$trangThaiHoatDong'
        WHERE maNV = '$maNV' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 200,
                'messange' => 'Nhân viên đã được sửa thành công',
            ];
            header("HTTP/1.0 200 Success");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}

function getPublicKey($passengerParams)
{
    global $conn;
    if ($passengerParams['maKH'] == null) {
        return error422('Nhập mã khách hàng');
    }

    $passengerId = mysqli_real_escape_string($conn, $passengerParams['maKH']);
    $query = "SELECT public_key FROM khachhang WHERE maKH = '$passengerId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Public Key Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có khách hàng nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getPublicKeyStaff($staffParams)
{
    global $conn;
    if ($staffParams['maNV'] == null) {
        return error422('Nhập mã nhân viên');
    }

    $staffId = mysqli_real_escape_string($conn, $staffParams['maNV']);
    $query = "SELECT public_key FROM nhanvien WHERE maNV = '$staffId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Public Key Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có nhân viên nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function updatePublicKey($passengerInput, $passengerParams)
{
    global $conn;
    if (!isset($passengerParams['maKH'])) {
        return error422('Mã khách hàng không tìm thấy');
    } elseif ($passengerParams['maKH'] == null) {
        return error422('Nhập mã khách hàng');
    }
    $maKH = mysqli_real_escape_string($conn, $passengerParams['maKH']);
    $public_key = mysqli_real_escape_string($conn, $passengerInput['public_key']);

    $query = "UPDATE khachhang SET public_key = '$public_key' WHERE maKH = '$maKH' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        $data = [
            'status' => 200,
            'messange' => 'Public key được update thành công',
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Method not allowed");
        echo json_encode($data);
    }
}

function updatePublicKeyStaff($staffInput, $staffParams)
{
    global $conn;
    if (!isset($staffParams['maNV'])) {
        return error422('Mã nhân viên không tìm thấy');
    } elseif ($staffParams['maNV'] == null) {
        return error422('Nhập mã nhân viên');
    }
    $maNV = mysqli_real_escape_string($conn, $staffParams['maNV']);
    $public_key = mysqli_real_escape_string($conn, $staffInput['public_key']);

    $query = "UPDATE nhanvien SET public_key = '$public_key' WHERE maNV = '$maNV' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        $data = [
            'status' => 200,
            'messange' => 'Public key được update thành công',
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Method not allowed");
        echo json_encode($data);
    }
}

function checkIfUserHasPublicKey($maKH)
{
    global $conn;
    $maKH = mysqli_real_escape_string($conn, $maKH);

    // Truy vấn kiểm tra public_key
    $query = "SELECT public_key FROM khachhang WHERE maKH = '$maKH' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row['public_key']) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

function checkIfUserHasPublicKeyStaff($maNV)
{
    global $conn;
    $maNV = mysqli_real_escape_string($conn, $maNV);

    $query = "SELECT public_key FROM nhanvien WHERE maKH = '$maNV' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row['public_key']) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

function deleteUser($userParams)
{
    global $conn;

    if (!isset($userParams['maNV'])) {
        return error422('Mã nhân viên không tìm thấy');
    } elseif ($userParams['maNV'] == null) {
        return error422('Nhập mã nhân viên');
    }

    $userId = mysqli_real_escape_string($conn, $userParams['maNV']);

    $query = "DELETE FROM nhanvien WHERE maNV = '$userId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 204,
            'messange' => 'Xóa thành công',
        ];
        header("HTTP/1.0 204 Deleted");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 404,
            'messange' => 'Không tìm thấy nhân viên',
        ];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
}


//------------------------------------------flights------------------------------------
function getFlightList()
{

    global $conn;
    $query = "SELECT * FROM thongtinchuyenbay";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Flight List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No flight found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getFlightListApp()
{

    global $conn;
    $query = "SELECT 
    tcb.*, 
    GROUP_CONCAT(DISTINCT slv.soLuongCon ORDER BY slv.soLuongCon SEPARATOR ', ') AS soLuongCon,
    GROUP_CONCAT(DISTINCT v.hangVe ORDER BY v.hangVe SEPARATOR ', ') AS hangVe
FROM thongtinchuyenbay tcb
LEFT JOIN soluongve slv ON tcb.maCB = slv.maCB
LEFT JOIN ve v ON slv.maVe = v.maVe
GROUP BY tcb.maCB
ORDER BY tcb.gioBay ASC;";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Flight List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No flight found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getFlightApp($flightParams)
{
    global $conn;
    if ($flightParams['maCB'] == null) {
        return error422('Nhập mã chuyến bay');
    }

    $flightId = mysqli_real_escape_string($conn, $flightParams['maCB']);
    $query = "SELECT 
                    tcb.*,
                    GROUP_CONCAT(v.maVe ORDER BY v.maVe SEPARATOR ', ') AS maVe,
                    GROUP_CONCAT(COALESCE(slv.soLuongCon, 0) ORDER BY v.maVe SEPARATOR ', ') AS soLuongCon,
                    GROUP_CONCAT(v.hangVe ORDER BY v.maVe SEPARATOR ', ') AS hangVe
                FROM thongtinchuyenbay tcb
                LEFT JOIN soluongve slv ON tcb.maCB = slv.maCB
                LEFT JOIN ve v ON slv.maVe = v.maVe
                WHERE tcb.maCB = '$flightId'
                GROUP BY tcb.maCB
                ORDER BY tcb.gioBay ASC;";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Flight Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có chuyến bay nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getFlight($flightParams)
{
    global $conn;
    if ($flightParams['maCB'] == null) {
        return error422('Nhập mã chuyến bay');
    }

    $flightId = mysqli_real_escape_string($conn, $flightParams['maCB']);
    $query = "SELECT * FROM thongtinchuyenbay WHERE maCB = '$flightId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Flight Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có chuyến bay nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function storeFlight($flightInput)
{
    global $conn;

    //$maDB = mysqli_real_escape_string($conn, $flightInput['maDB']);
    $maMB = mysqli_real_escape_string($conn, $flightInput['maMB']);
    $ngayDen = mysqli_real_escape_string($conn, $flightInput['ngayDen']);
    $ngayDi = mysqli_real_escape_string($conn, $flightInput['ngayDi']);
    $diaDiemDen = mysqli_real_escape_string($conn, $flightInput['diaDiemDen']);
    $diaDiemDi = mysqli_real_escape_string($conn, $flightInput['diaDiemDi']);
    $giaVe = mysqli_real_escape_string($conn, $flightInput['giaVe']);
    $ghiChu = mysqli_real_escape_string($conn, $flightInput['ghiChu']);
    $gioBay = mysqli_real_escape_string($conn, $flightInput['gioBay']);

    // Kiểm tra trùng lặp
    $duplicateQuery = "SELECT COUNT(*) AS count FROM thongtinchuyenbay 
                   WHERE maMB = '$maMB' 
                   AND ngayDi = '$ngayDi' 
                   AND ngayDen = '$ngayDen'
                   AND diaDiemDen = '$diaDiemDen'
                   AND diaDiemDi = '$diaDiemDi'
                   AND giaVe = '$giaVe'
                   AND ghiChu = '$ghiChu'
                   AND gioBay = '$gioBay'";
    $duplicateResult = mysqli_query($conn, $duplicateQuery);

    if ($duplicateResult) {
        $duplicateRow = mysqli_fetch_assoc($duplicateResult);
        $duplicateCount = $duplicateRow['count'];

        if ($duplicateCount > 0) {
            $data = [
                'status' => 400,
                'message' => 'Đã có chuyến bay này. Vui lòng kiểm tra lại.',
            ];
            header("HTTP/1.0 400 Bad Request");
            echo json_encode($data);
            return;
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error during duplicate check.',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }

    // if(empty(trim($maDB))){
    //     return error422('Hãy nhập mã đường bay');
    // }
    if (empty(trim($maMB))) {
        return error422('Hãy nhập mã máy bay');
    } elseif (empty(trim($ngayDen))) {
        return error422('Hãy nhập ngày đến');
    } elseif (empty(trim($ngayDi))) {
        return error422('Hãy nhập ngày đi');
    } elseif (empty(trim($diaDiemDen))) {
        return error422('Hãy nhập địa điểm đến');
    } elseif (empty(trim($diaDiemDi))) {
        return error422('Hãy nhập địa điểm đi');
    } elseif (empty(trim($giaVe))) {
        return error422('Hãy nhập giá vé');
    } elseif (empty(trim($ghiChu))) {
        return error422('Hãy nhập ghi chú');
    } elseif (empty(trim($gioBay))) {
        return error422('Hãy nhập giờ bay');
    } else {
        $query = "INSERT INTO thongtinchuyenbay (maMB,ngayDen,ngayDi,diaDiemDen,diaDiemDi,giaVe,ghiChu,gioBay)
         VALUES ('$maMB','$ngayDen','$ngayDi','$diaDiemDen','$diaDiemDi','$giaVe','$ghiChu','$gioBay')";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Chuyến bay đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}

function deleteFlight($flightParams)
{
    global $conn;

    if (!isset($flightParams['maCB'])) {
        return error422('Mã chuyến bay không tìm thấy');
    } elseif ($flightParams['maCB'] == null) {
        return error422('Nhập mã chuyến bay');
    }

    $flightId = mysqli_real_escape_string($conn, $flightParams['maCB']);

    $query = "DELETE FROM thongtinchuyenbay WHERE maCB = '$flightId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 204,
            'messange' => 'Xóa thành công',
        ];
        header("HTTP/1.1 204 No Content");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 404,
            'messange' => 'Không tìm thấy chuyến bay',
        ];
        header("HTTP/1.1 404 Not Found");
        echo json_encode($data);
    }
}

function updateFlight($flightInput, $flightParams)
{
    global $conn;

    if (!isset($flightParams['maCB'])) {
        return error422('Mã chuyến bay không tìm thấy');
    } elseif ($flightParams['maCB'] == null) {
        return error422('Nhập mã chuyến bay');
    }

    $flightId = mysqli_real_escape_string($conn, $flightParams['maCB']);
    //  $maDB = mysqli_real_escape_string($conn, $_POST['maDB']);
    $maMB = mysqli_real_escape_string($conn, $_POST['maMB']);
    $ngayDen = mysqli_real_escape_string($conn, $_POST['ngayDen']);
    $ngayDi = mysqli_real_escape_string($conn, $_POST['ngayDi']);
    $diaDiemDen = mysqli_real_escape_string($conn, $_POST['diaDiemDen']);
    $diaDiemDi = mysqli_real_escape_string($conn, $_POST['diaDiemDi']);
    $giaVe = mysqli_real_escape_string($conn, $_POST['giaVe']);
    $ghiChu = mysqli_real_escape_string($conn, $_POST['ghiChu']);
    $gioBay = mysqli_real_escape_string($conn, $_POST['gioBay']);

    if (empty(trim($ngayDen))) {
        return error422('Hãy nhập ngày đến');
    } elseif (empty(trim($ngayDi))) {
        return error422('Hãy nhập ngày đi');
    } elseif (empty(trim($diaDiemDen))) {
        return error422('Hãy nhập địa điểm đến');
    } elseif (empty(trim($diaDiemDi))) {
        return error422('Hãy nhập địa điểm đi');
    } elseif (empty(trim($giaVe))) {
        return error422('Hãy nhập giá vé');
    } elseif (empty(trim($ghiChu))) {
        return error422('Hãy nhập ghi chú');
    } elseif (empty(trim($gioBay))) {
        return error422('Hãy nhập giờ bay');
    } else {
        $query = "UPDATE thongtinchuyenbay SET maMB = '$maMB', ngayDen = '$ngayDen', ngayDi = '$ngayDi',
        diaDiemDen = '$diaDiemDen', diaDiemDi = '$diaDiemDi', giaVe = '$giaVe', ghiChu = '$ghiChu', gioBay='$gioBay' 
        WHERE maCB = '$flightId' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 200,
                'messange' => 'Chuyến bay đã được sửa thành công',
            ];
            header("HTTP/1.0 200 Success");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}

//------------------------Ve------------------------------------------------------------------------------
function getTicketList()
{

    global $conn;
    $query = "SELECT * FROM ve";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Ticket List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No ticket found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getTicket($ticketParams)
{
    global $conn;
    if ($ticketParams['maVe'] == null) {
        return error422('Nhập mã vé');
    }

    $ticketId = mysqli_real_escape_string($conn, $ticketParams['maVe']);
    $query = "SELECT * FROM ve WHERE maVe = '$ticketId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Ticket Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có vé nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function storeTicket($ticketInput)
{
    global $conn;
    $maVe = mysqli_real_escape_string($conn, $ticketInput['maVe']);
    $soLuong = mysqli_real_escape_string($conn, $ticketInput['soLuong']);
    $hangVe = mysqli_real_escape_string($conn, $ticketInput['hangVe']);
    // Kiểm tra trùng lặp
    $duplicateQuery = "SELECT COUNT(*) AS count FROM ve 
                   WHERE maVe = '$maVe' 
                   OR soLuong = '$soLuong' 
                   OR hangVe = '$hangVe'";
    $duplicateResult = mysqli_query($conn, $duplicateQuery);
    if ($duplicateResult) {
        $duplicateRow = mysqli_fetch_assoc($duplicateResult);
        $duplicateCount = $duplicateRow['count'];

        if ($duplicateCount > 0) {
            $data = [
                'status' => 400,
                'message' => 'Đã có vé này. Vui lòng kiểm tra lại.',
            ];
            header("HTTP/1.0 400 Bad Request");
            echo json_encode($data);
            return;
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error during duplicate check.',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }

    if (empty(trim($maVe))) {
        return error422('Hãy nhập mã vé');
    } elseif (empty(trim($soLuong))) {
        return error422('Hãy nhập số lượng');
    } elseif (empty(trim($hangVe))) {
        return error422('Hãy nhập hạng vé');
    } else {
        $query = "INSERT INTO ve(maVe, soLuong, hangVe)
	VALUES ('$maVe','$soLuong','$hangVe' )";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Vé đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}
function deleteTicket($ticketParams)
{
    global $conn;

    if (!isset($ticketParams['maVe'])) {
        return error422('Mã vé không tìm thấy');
    } elseif ($ticketParams['maVe'] == null) {
        return error422('Nhập mã vé');
    }

    $ticketId = mysqli_real_escape_string($conn, $ticketParams['maVe']);

    $query = "DELETE FROM ve WHERE maVe = '$ticketId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 204,
            'messange' => 'Xóa thành công',
        ];
        header("HTTP/1.0 204 Deleted");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 404,
            'messange' => 'Không tìm thấy chuyến bay',
        ];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
}


function updateTicket($ticketInput, $ticketParams)
{
    global $conn;

    if (!isset($ticketParams['maVe'])) {
        return error422('Mã vé không tìm thấy');
    } elseif ($ticketParams['maVe'] == null) {
        return error422('Nhập mã vé');
    }
    $maVe = mysqli_real_escape_string($conn, $ticketParams['maVe']);
    $soLuong = mysqli_real_escape_string($conn, $_POST['soLuong']);
    $hangVe = mysqli_real_escape_string($conn, $_POST['hangVe']);
    if (empty(trim($soLuong))) {
        return error422('Hãy nhập số lượng');
    }
    elseif (empty(trim($hangVe))) {
        return error422('Hãy nhập hạng vé');
    } else {
        $query = "UPDATE ve SET soLuong = '$soLuong',
        hangVe = '$hangVe'
        WHERE maVe = '$maVe' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 200,
                'messange' => 'Vé đã được sửa thành công',
            ];
            header("HTTP/1.0 200 Success");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}

function updateNumberOfTickets($ticketInput, $ticketParams)
{
    global $conn;
    if (!isset($ticketParams['maCB'])) {
        return error422('Mã chuyến bay không tìm thấy');
    } elseif ($ticketParams['maCB'] == null) {
        return error422('Nhập mã chuyến bay');
    }

    if (!isset($ticketParams['maVe'])) {
        return error422('Mã vé không tìm thấy');
    } elseif ($ticketParams['maVe'] == null) {
        return error422('Nhập mã vé');
    }

    $maCB = mysqli_real_escape_string($conn, $ticketParams['maCB']);
    $maVe = mysqli_real_escape_string($conn, $ticketParams['maVe']);
    $soLuongCon = mysqli_real_escape_string($conn, $ticketInput['soLuongCon']);
    $query = "
        UPDATE soluongve 
        SET soLuongCon = '$soLuongCon' 
        WHERE maCB = '$maCB' AND maVe = '$maVe'
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 200,
            'message' => 'Số lượng vé đã được cập nhật thành công',
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'message' => 'Lỗi hệ thống, không thể cập nhật',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}


//----------------------------------------------chi tiết khách hàng---------------------------------------------------
function getDetailPassenger($detailParams)
{
    global $conn;
    if ($detailParams['maVe'] == null) {
        return error422('Nhập mã vé');
    }

    $detailId = mysqli_real_escape_string($conn, $detailParams['maKH']);
    $query = "SELECT * FROM veDaDat as a, thongtinchuyenbay as b, khachhang as c, ve as d
    WHERE a.maCB = b.maCB and a.maKH=c.maKH and a.maVe = d.maVe and a.maVe = '$detailId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Customer Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có khách hàng nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

//-----------------------DetailTicket-------------------------------------------------
function getDetailTicketList()
{

    global $conn;
    $query = "SELECT * FROM khachhang as a, thongtinchuyenbay as b , ve as c , vedadat as d
    WHERE a.maKH = d.maKH and b.maCB = d.maCB and c.maVe = d.maVe";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Ticket List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No ticket found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getDetailTicket($detailTicketParams)
{
    global $conn;
    if ($detailTicketParams['maVe'] == null) {
        return error422('Nhập mã vé');
    }

    $ticketId = mysqli_real_escape_string($conn, $detailTicketParams['maVe']);
    $query = "SELECT * FROM khachhang as a, thongtinchuyenbay as b , ve as c , vedadat as d
     WHERE maVe = '$ticketId' and a.maKH = d.maKH and b.maCB = d.maCB and c.maVe = d.maVe LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Ticket Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có vé nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
// ---------------------------------------------------Create Detail Ticket    ---------------------------------------------------
function storeDetailTicket($detailInput)
{
    global $conn;
    $order_id = mysqli_real_escape_string($conn, $detailInput['order_id']);
    $maVe = mysqli_real_escape_string($conn, $detailInput['maVe']);
    $maCB = mysqli_real_escape_string($conn, $detailInput['maCB']);
    $maKH = mysqli_real_escape_string($conn, $detailInput['maKH']);
    $soLuongDat = mysqli_real_escape_string($conn, $detailInput['soLuongDat']);
    $tongThanhToan = mysqli_real_escape_string($conn, $detailInput['tongThanhToan']);
    $nguonDat = mysqli_real_escape_string($conn, $detailInput['nguonDat']);
    // Đặt maShop thành NULL nếu nguồn đặt là "app"
    $maShop = ($nguonDat == "app") ? null : mysqli_real_escape_string($conn, $detailInput['maShop']);

    if (empty(trim($maVe))) {
        return error422('Hãy nhập mã Vé');
    } elseif (empty(trim($maCB))) {
        return error422('Hãy nhập mã chuyến bay');
    } elseif (empty(trim($maKH))) {
        return error422('Hãy nhập mã khách hàng');
    } elseif (empty(trim($soLuongDat))) {
        return error422('Hãy nhập số lượng đặt');
    } else {
        $query = "INSERT INTO vedadat (order_id,maVe,maCB,maKH,soLuongDat,tongThanhToan,nguonDat,maShop)
         VALUES ('$order_id','$maVe','$maCB','$maKH','$soLuongDat','$tongThanhToan','$nguonDat', " . ($maShop ? "'$maShop'" : 'NULL') . ")";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Đặt vé thành công',
            ];
            header("HTTP/1.0 201 Created");
            return json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
                'error' => mysqli_error($conn),
            ];
            header("HTTP/1.0 500 Method not allowed");
            return json_encode($data);
        }
    }
}

function getTicketWebList($ticketParams)
{

    global $conn;

    if ($ticketParams['maShop'] == null) {
        return error422('Nhập mã cửa hàng');
    }

    $shopId = mysqli_real_escape_string($conn, $ticketParams['maShop']);
    $query = "SELECT * FROM khachhang as a, thongtinchuyenbay as b , ve as c , vedadat as d
    WHERE a.maKH = d.maKH and b.maCB = d.maCB and c.maVe = d.maVe and nguonDat = 'Cửa hàng' and d.maShop = '$shopId'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $ticketArray = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ticketArray[] = $row;
        }

        if (!empty($ticketArray)) {
            $data = [
                'status' => 200,
                'message' => 'Ticket Shop Fetched Successfully',
                'data' => $ticketArray
            ];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có vé đã đặt nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}

function storeMess($messInput)
{
    global $conn;

    $noiDung1 = mysqli_real_escape_string($conn, $messInput['noiDung1']);
    $thoiGianGui = mysqli_real_escape_string($conn, $messInput['thoiGianGui']);
    $maKH = mysqli_real_escape_string($conn, $messInput['maKH']);
    $noiDung2 = mysqli_real_escape_string($conn, $messInput['noiDung2']);

    if (empty(trim($thoiGianGui))) {
        return error422('Hãy nhập thời gian gửi');
    } elseif (empty(trim($maKH))) {
        return error422('Hãy nhập mã khách hàng');
    } else {
        $query = "INSERT INTO tinnhan (noiDung1,thoiGianGui,maKH,noiDung2) VALUES ('$noiDung1','$thoiGianGui','$maKH','$noiDung2')";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Tin nhắn đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}
function getMessList()
{

    global $conn;
    $query = "SELECT * FROM khachhang as a, tinnhan as b
    WHERE a.maKH = b.maKH";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Messenger List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No messenger found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getMess($messParams)
{
    global $conn;

    if ($messParams['maKH'] == null) {
        return error422('Nhập mã khách hàng');
    }

    $passengerId = mysqli_real_escape_string($conn, $messParams['maKH']);
    $query = "SELECT * FROM tinnhan WHERE maKH = '$passengerId'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $messArray = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messArray[] = $row;
        }

        if (!empty($messArray)) {
            $data = [
                'status' => 200,
                'message' => 'Mess Fetched Successfully',
                'data' => $messArray
            ];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có tin nhắn nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}
function getChat($chatParams)
{
    global $conn;
    if ($chatParams['maKH'] == null) {
        return error422('Nhập mã KH');
    }

    $userId = mysqli_real_escape_string($conn, $chatParams['maKH']);
    $query = "SELECT DISTINCT a.fullname, b.maKH FROM khachhang as a, tinnhan as b
    WHERE a.maKH = b.maKH";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'User Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có khách hàng nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
function getChatList()
{

    global $conn;
    $query = "SELECT DISTINCT a.fullname, b.maKH FROM khachhang as a, tinnhan as b
        WHERE a.maKH = b.maKH";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Chat List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No ticket found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
function logInUser($accountInput)
{
    global $conn;
    $username = mysqli_real_escape_string($conn, $accountInput['username']);
    $passw = mysqli_real_escape_string($conn, $accountInput['passw']);

    if (empty(trim($username)) || empty(trim($passw))) {
        return error422('Hãy nhập username và mật khẩu');
    } else {
        // Sử dụng Prepared Statements để ngăn chặn SQL Injection
        $query = "SELECT * FROM `nhanvien` WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) != 0) {
            $row = mysqli_fetch_assoc($result);
            $dbusername = $row['username'];
            $dbpassword = $row['passw'];

            // Kiểm tra mật khẩu sử dụng password_verify
            if ($dbusername == $username && password_verify($passw, $dbpassword)) {
                $login = true;
            } else {
                $login = false;
            }
        } else {
            $login = false;
        }

        return $login;
    }
}
// Voucher
function storeVoucher($voucherInput)
{
    global $conn;

    $code = mysqli_real_escape_string($conn, $voucherInput['code']);
    $discount = mysqli_real_escape_string($conn, $voucherInput['discount']);
    $ngayHetHan = mysqli_real_escape_string($conn, $voucherInput['ngayHetHan']);
    $ngayTao = mysqli_real_escape_string($conn, $voucherInput['ngayTao']);

    if (empty(trim($code))) {
        return error422('Hãy nhập mã code voucher');
    } elseif (empty(trim($discount))) {
        return error422('Hãy nhập khuyến mãi');
    } elseif (empty(trim($ngayHetHan))) {
        return error422('Hãy nhập ngày hết hạn');
    } elseif (empty(trim($ngayTao))) {
        return error422('Hãy nhập ngày tạo');
    } else {
        $query = "INSERT INTO voucher (code,discount,ngayHetHan,trangThai,ngayTao) VALUES ('$code','$discount','$ngayHetHan','', '$ngayTao')";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Voucher đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}

function storeVoucherUsage($voucherInput)
{
    global $conn;

    $maKH = mysqli_real_escape_string($conn, $voucherInput['maKH']);
    $maVoucher = mysqli_real_escape_string($conn, $voucherInput['maVoucher']);
    $ngayDung = mysqli_real_escape_string($conn, $voucherInput['ngayDung']);

    if (empty(trim($maKH))) {
        return error422('Hãy nhập mã KH dùng voucher');
    } elseif (empty(trim($maVoucher))) {
        return error422('Hãy nhập mã khuyến mãi');
    } elseif (empty(trim($ngayDung))) {
        return error422('Hãy nhập ngày dùng');
    } else {
        $query = "INSERT INTO voucher_usage (maKH, maVoucher, ngayDung) VALUES ('$maKH','$maVoucher','$ngayDung')";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Voucher đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}

function getVoucherCustomerNewList($maKH)
{
    global $conn;

    // Ensure $maKH is properly sanitized to prevent SQL injection
    $maKH = mysqli_real_escape_string($conn, $maKH);

    $query = "SELECT v.*
                FROM voucher v
                WHERE (v.trangThai = 'Khách hàng' OR v.trangThai = 'Khách hàng mới')
                AND v.ngayHetHan >= CURDATE()  -- Kiểm tra những voucher còn hạn
                AND NOT EXISTS (
                SELECT 1
                FROM voucher_usage vu
                WHERE vu.maVoucher = v.maVoucher
                AND vu.maKH = '$maKH'
);";

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Voucher List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No vouchers found for this customer',
            ];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getVoucherVIP($maKH)
{
    global $conn;

    // Ensure $maKH is properly sanitized to prevent SQL injection
    $maKH = mysqli_real_escape_string($conn, $maKH);

    $query = "SELECT v.*
              FROM voucher v
              LEFT JOIN voucher_usage vu
              ON v.maVoucher = vu.maVoucher AND vu.maKH = '$maKH'
              WHERE (v.trangThai = 'Khách hàng' OR v.trangThai = 'Khách hàng VIP')
              AND v.ngayHetHan >= CURDATE()
              AND vu.maVoucher IS NULL";

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Voucher List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No vouchers found for this customer',
            ];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}


function getVoucherCustomerList($maKH)
{
    global $conn;

    // Ensure $maKH is properly sanitized to prevent SQL injection
    $maKH = mysqli_real_escape_string($conn, $maKH);

    // Query to get vouchers that have not been used by the specific customer
    $query = "SELECT v.*
              FROM voucher v
              LEFT JOIN voucher_usage vu
              ON v.maVoucher = vu.maVoucher AND vu.maKH = '$maKH'
              WHERE v.trangThai = 'Khách hàng'
              AND v.ngayHetHan >= CURDATE()
              AND vu.maVoucher IS NULL";

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Voucher List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No vouchers found for this customer',
            ];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}



function getVoucherList()
{

    global $conn;
    $query = "SELECT v.*
        FROM voucher v
        LEFT JOIN voucher_usage vu
        ON v.maVoucher = vu.maVoucher
        WHERE vu.maVoucher IS NULL
        AND v.ngayHetHan >= CURDATE()";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Voucher List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No voucher found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
function getVoucher($voucherParams)
{
    global $conn;
    if ($voucherParams['maVoucher'] == null) {
        return error422('Nhập mã voucher');
    }

    $voucherId = mysqli_real_escape_string($conn, $voucherParams['maVoucher']);
    $query = "SELECT * FROM voucher WHERE maVoucher = '$voucherId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Voucher Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có voucher nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
function deleteExpiredVouchers()
{
    global $conn;

    // Lấy ngày hiện tại
    $currentDate = date('Y-m-d');

    // Câu lệnh SQL để xóa các voucher đã hết hạn
    $query = "DELETE FROM voucher WHERE ngayHetHan <= '$currentDate'";
    $result = mysqli_query($conn, $query);

    // Kiểm tra và phản hồi lại kết quả
    if ($result) {
        $data = [
            'status' => 204,
            'message' => 'Xóa các voucher hết hạn thành công',
        ];
        header("HTTP/1.0 204 Deleted");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'message' => 'Lỗi khi xóa các voucher hết hạn',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}
function getRegistrationDate($maKH)
{

    global $conn;
    $query = "SELECT ngayDangKy FROM khachhang WHERE maKH = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $maKH);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $res = mysqli_fetch_assoc($result);

            $data = [
                'status' => 200,
                'message' => 'Registration date fetched successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No registration date found for this user',
            ];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($data);
        }

        mysqli_stmt_close($stmt);
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}

function getTicketCount($maKH)
{
    global $conn;

    // Query để lấy số lượng vé đã đặt trong năm hiện tại
    $query = "
        SELECT COALESCE(SUM(soLuongDat), 0) AS total_tickets
        FROM veDaDat
        WHERE maKH = ?
        AND YEAR(create_at) = YEAR(CURDATE())
    ";

    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $maKH);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $data = [
                'status' => 200,
                'message' => 'Ticket count fetched successfully',
                'data' => [
                    'total_tickets' => $row['total_tickets']
                ]
            ];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No tickets found for this user',
            ];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($data);
        }

        mysqli_stmt_close($stmt);
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}


// End voucher

// Start security
function storeSecurity($securityInput)
{
    global $conn;

    $thongBao = mysqli_real_escape_string($conn, $securityInput['thongBao']);
    $ngayTao = mysqli_real_escape_string($conn, $securityInput['ngayTao']);
    if (empty(trim($thongBao))) {
        return error422('Hãy nhập thông báo');
    } else {
        $query = "INSERT INTO anninh (thongBao,ngayTao) VALUES ('$thongBao','$ngayTao')";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Thông báo đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}
function getSecurityList()
{

    global $conn;
    $query = "SELECT * FROM anninh";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Security List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No security found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
function getSecurity($securityParams)
{
    global $conn;
    if ($securityParams['maCB'] == null) {
        return error422('Nhập mã chuyến bay');
    }

    $securityId = mysqli_real_escape_string($conn, $securityParams['maAnNinh']);
    $query = "SELECT * FROM thongtinchuyenbay WHERE maCB = '$securityId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Security Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có thông báo nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
function deleteSecurity($securityParams)
{
    global $conn;

    if (!isset($securityParams['maAnNinh'])) {
        return error422('Mã thông báo không tìm thấy');
    } elseif ($securityParams['maAnNinh'] == null) {
        return error422('Nhập mã thông báo');
    }

    $securityId = mysqli_real_escape_string($conn, $securityParams['maAnNinh']);

    $query = "DELETE FROM anninh WHERE maAnNinh = '$securityId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 204,
            'messange' => 'Xóa thành công',
        ];
        header("HTTP/1.0 204 Deleted");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 404,
            'messange' => 'Không tìm thấy thông báo an ninh',
        ];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
}
// End security

// Statiscal

function getStatiscalList()
{
    global $conn;
    $query = "SELECT MONTH(create_at) AS month, 
                    SUM(soLuongDat) AS total_tickets, 
                    SUM(tongThanhToan) AS total_revenue
                FROM veDaDat
                GROUP BY MONTH(create_at);";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Security List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No security found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getStatiscalOfShopList($shopParams)
{
    global $conn;
    if (!isset($shopParams['maShop'])) {
        return error422('Mã cửa hàng không tìm thấy');
    } elseif ($shopParams['maShop'] == null) {
        return error422('Nhập mã thông báo');
    }

    $shopId = mysqli_real_escape_string($conn, $shopParams['maShop']);
    $query = "SELECT MONTH(create_at) AS month, 
                    SUM(soLuongDat) AS total_tickets, 
                    SUM(tongThanhToan) AS total_revenue
                FROM veDaDat
                WHERE maShop = '$shopId'
                GROUP BY MONTH(create_at);";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Security List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No security found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getHomeShop($shopParams)
{
    global $conn;
    if (!isset($shopParams['maShop'])) {
        return error422('Mã cửa hàng không tìm thấy');
    } elseif ($shopParams['maShop'] == null) {
        return error422('Nhập mã thông báo');
    }

    $shopId = mysqli_real_escape_string($conn, $shopParams['maShop']);
    $query = "SELECT SUM(soLuongDat) AS total_tickets, 
                    SUM(tongThanhToan) AS total_revenue
                FROM veDaDat
                WHERE maShop = '$shopId'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Security List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No security found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getSumFlightNow()
{
    global $conn;
    $query = "SELECT COUNT(maCB) AS total_flights
                FROM thongtinchuyenbay
                WHERE ngayDi >= NOW()";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Security List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No security found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getStatisticAdmin()
{
    global $conn;
    $query = "SELECT 
                MONTH(create_at) AS month,
                v.maShop,                         
                SUM(v.soLuongDat) AS tongVe,    
                SUM(v.tongThanhToan) AS tongTien,
                u.taikhoan 
            FROM 
                veDaDat v
            JOIN 
                user_shop u ON v.maShop = u.maNVShop
            GROUP BY
                MONTH(create_at), v.maShop, u.taikhoan;
                ";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Security List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No security found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

// end statiscal
//Số chuyến bay và số vé đã mua
function getVeDaDatList($securityParams)
{
    global $conn;
    if ($securityParams['maKH'] == null) {
        return error422('Nhập mã khách hàng');
    }

    $userId = mysqli_real_escape_string($conn, $securityParams['maKH']);
    $query = "SELECT 
                    maKH AS userID,  -- Mã người dùng
                    COUNT(DISTINCT maCB) AS tongSoChuyenBay,  -- Tổng số chuyến bay đã bay cho người dùng này
                    SUM(soLuongDat) AS tongSoLuongVeDat  -- Tổng số lượng vé đã đặt cho người dùng này
                FROM veDaDat
                WHERE maKH = '$userId'  -- Chỉ lấy thông tin cho user ID này
                GROUP BY maKH;";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Sum ticket Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có thông báo nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
// End Số chuyến bay và số vé đã mua


// Thêm qcao
function storeAd($adInput)
{
    global $conn;

    // Lấy dữ liệu từ input
    $description = mysqli_real_escape_string($conn, $adInput['description']);
    $img = mysqli_real_escape_string($conn, $adInput['img']);
    $name = mysqli_real_escape_string($conn, $adInput['name']);
    $create_at = date('Y-m-d H:i:s'); // Thời gian hiện tại

    // Kiểm tra nếu description hoặc img trống
    if (empty(trim($description)) || empty(trim($img))) {
        return error422('Hãy nhập đầy đủ thông tin quảng cáo');
    } else {
        // Chèn dữ liệu vào bảng quảng cáo
        $query = "INSERT INTO quangCao (name, img, description, create_at) VALUES ('$name','$img', '$description', '$create_at')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $data = [
                'status' => 201,
                'message' => 'Quảng cáo đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Internal server error");
            echo json_encode($data);
        }
    }
}

function getAdList()
{

    global $conn;
    $query = "SELECT * FROM quangcao";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Ad List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No ad found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getAd($adParams)
{
    global $conn;
    if ($adParams['maqc'] == null) {
        return error422('Nhập mã quảng cáo');
    }

    $adId = mysqli_real_escape_string($conn, $adParams['maqc']);
    $query = "SELECT * FROM quangcao WHERE maqc = '$adId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Ad Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có quảng cáo nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function deleteAd($adParams)
{
    global $conn;

    if (!isset($adParams['maqc'])) {
        return error422('Mã quảng cáo không tìm thấy');
    } elseif ($adParams['maqc'] == null) {
        return error422('Nhập mã quảng cáo');
    }

    $adId = mysqli_real_escape_string($conn, $adParams['maqc']);

    $query = "DELETE FROM quangcao WHERE maqc = '$adId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 204,
            'messange' => 'Xóa thành công',
        ];
        header("HTTP/1.0 204 Deleted");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 404,
            'messange' => 'Không tìm thấy quảng cáo',
        ];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
}

function updateAd($adInput, $adParams)
{
    global $conn;

    if (!isset($adParams['maqc'])) {
        return error422('Mã quảng cáo không tìm thấy');
    } elseif ($adParams['maqc'] == null) {
        return error422('Nhập mã quảng cáo');
    }

    $adId = intval(mysqli_real_escape_string($conn, $adParams['maqc']));
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $img = mysqli_real_escape_string($conn, $_POST['img']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $place = mysqli_real_escape_string($conn, $_POST['place']);

    $query = "UPDATE quangcao SET name='$name',img = '$img',description = '$description', place = '$place' WHERE maqc = '$adId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        $data = [
            'status' => 200,
            'messange' => 'Quảng cáo được sửa thành công',
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Method not allowed");
        echo json_encode($data);
    }
}
//End qcao

//Login chủ cửa hàng
function loginUserShop($userInput)
{
    global $conn;

    // Lấy thông tin username và password từ đầu vào
    $taikhoan = mysqli_real_escape_string($conn, trim($userInput['taikhoan']));
    $matkhau = mysqli_real_escape_string($conn, trim($userInput['matkhau']));

    // Kiểm tra xem username và password có trống không
    if (empty($taikhoan) || empty($matkhau)) {
        return error422('Vui lòng nhập cả username và mật khẩu');
    }

    // Truy vấn cơ sở dữ liệu để lấy thông tin người dùng theo username
    $query = "SELECT * FROM user_shop WHERE taikhoan = '$taikhoan'";
    $result = mysqli_query($conn, $query);

    // Kiểm tra xem người dùng có tồn tại không
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // var_dump($matkhau);
        // var_dump($user['matkhau']);
        // var_dump(password_verify($matkhau, $user['matkhau']));

        // So sánh mật khẩu đã hash bằng password_verify
        if (password_verify($matkhau, $user['matkhau'])) {
            // Mật khẩu đúng, đăng nhập thành công
            $data = [
                'status' => 200,
                'message' => 'Đăng nhập thành công',
                'user' => [
                    'maNVshop' => $user['maNVshop'],
                    'taikhoan' => $user['taikhoan'],
                ]
            ];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            // Mật khẩu sai
            return error422('Mật khẩu không đúng');
        }
    } else {
        // Không tìm thấy username
        return error422('Username không tồn tại');
    }
}


function storeUserShop($userInput)
{
    global $conn;

    $taikhoan = mysqli_real_escape_string($conn, $userInput['taikhoan']);
    $matkhau = mysqli_real_escape_string($conn, $userInput['matkhau']);
    $kinhdo = mysqli_real_escape_string($conn, $userInput['kinhdo']);
    $vido = mysqli_real_escape_string($conn, $userInput['vido']);

    // Kiểm tra nếu cả username và dienTen bị bỏ trống
    if (empty(trim($taikhoan)) || empty(trim($matkhau))) {
        return error422('Hãy nhập thông tin username và mật khẩu');
    } else {
        // Kiểm tra sự tồn tại của username (nếu có)
        if (!empty($taikhoan)) {
            $checkUsernameQuery = "SELECT * FROM user_shop WHERE taikhoan = '$taikhoan' LIMIT 1";
            $checkUsernameResult = mysqli_query($conn, $checkUsernameQuery);
            if (mysqli_num_rows($checkUsernameResult) > 0) {
                // Username đã tồn tại
                $data = [
                    'status' => 422,
                    'message' => 'Username đã tồn tại. Vui lòng chọn tên khác.',
                ];
                header("HTTP/1.0 422 Unprocessable Entity");
                echo json_encode($data);
                return;
            }
        }

        // Chèn dữ liệu người dùng mới vào bảng
        $query = "INSERT INTO user_shop (taikhoan, matkhau, kinhdo, vido) 
                  VALUES ('$taikhoan', '$matkhau', '$kinhdo', '$vido')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            // Lấy user_id của bản ghi vừa được thêm vào
            $maNVshop = mysqli_insert_id($conn);
            $data = [
                'status' => 201,
                'message' => 'Tài khoản đã được thêm thành công',
                'maNVshop' => $maNVshop
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
        }
    }
}

function searchCustomer($searchQuery)
{
    global $conn;

    // Lấy thông tin tìm kiếm và sử dụng `mysqli_real_escape_string` để tránh SQL Injection
    $searchQuery = mysqli_real_escape_string($conn, $searchQuery);

    // Kiểm tra nếu chuỗi tìm kiếm trống
    if (empty(trim($searchQuery))) {
        return error422('Hãy nhập thông tin cần tìm kiếm');
    }

    // Truy vấn tìm kiếm khách hàng theo tên hoặc email
    $query = "SELECT * FROM khachhang WHERE fullname LIKE '%$searchQuery%' OR soDT LIKE '%$searchQuery%'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Customer List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No customer found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}


function addCustomer($customerInput)
{
    global $conn;

    // Lấy thông tin khách hàng và xử lý tránh SQL Injection
    $fullname = mysqli_real_escape_string($conn, $customerInput['fullname']);
    $email = mysqli_real_escape_string($conn, $customerInput['email']);
    $soDT = mysqli_real_escape_string($conn, $customerInput['soDT']);

    // Kiểm tra xem các trường thông tin không được bỏ trống
    if (empty(trim($fullname)) || empty(trim($soDT))) {
        return error422('Hãy nhập thông tin tên và email khách hàng');
    } else {
        // Kiểm tra xem khách hàng đã tồn tại hay chưa
        $checkEmailQuery = "SELECT * FROM khachhang WHERE soDT = '$soDT' LIMIT 1";
        $checkEmailResult = mysqli_query($conn, $checkEmailQuery);

        if (mysqli_num_rows($checkEmailResult) > 0) {
            // Email đã tồn tại
            $data = [
                'status' => 422,
                'message' => 'Email đã tồn tại. Vui lòng sử dụng email khác.',
            ];
            header("HTTP/1.0 422 Unprocessable Entity");
            echo json_encode($data);
            return;
        }

        // Chèn khách hàng mới vào cơ sở dữ liệu
        $query = "INSERT INTO khachhang (fullname, email, soDT) VALUES ('$fullname', '$email', '$soDT')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            // Lấy `customer_id` của bản ghi vừa được thêm vào
            $customer_id = mysqli_insert_id($conn);
            $data = [
                'status' => 201,
                'message' => 'Khách hàng đã được thêm thành công',
                'customer_id' => $customer_id
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            // Nếu có lỗi trong quá trình thêm
            $data = [
                'status' => 500,
                'message' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
        }
    }
}

// Shop
function storeShop($shopInput)
{
    global $conn;

    $taikhoan = mysqli_real_escape_string($conn, $shopInput['taikhoan']);
    $matkhau = mysqli_real_escape_string($conn, $shopInput['matkhau']);
    // Kiểm tra trùng lặp
    $duplicateQuery = "SELECT COUNT(*) AS count FROM user_shop 
      WHERE taikhoan = '$taikhoan'";
    $duplicateResult = mysqli_query($conn, $duplicateQuery);
    if ($duplicateResult) {
        $duplicateRow = mysqli_fetch_assoc($duplicateResult);
        $duplicateCount = $duplicateRow['count'];

        if ($duplicateCount > 0) {
            $data = [
                'status' => 400,
                'message' => 'Đã có cửa hàng này. Vui lòng kiểm tra lại.',
            ];
            header("HTTP/1.0 400 Bad Request");
            echo json_encode($data);
            return;
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal server error during duplicate check.',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }
    if (empty(trim($taikhoan))) {
        return error422('Hãy nhập tên máy bay');
    } elseif (empty(trim($matkhau))) {
        return error422('Hãy nhập hãng may bay');
    } else {
        // Hash mật khẩu trước khi lưu
        $hashedPassword = password_hash($matkhau, PASSWORD_DEFAULT);
        $query = "INSERT INTO user_shop (taikhoan,matkhau) VALUES ('$taikhoan','$hashedPassword')";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 201,
                'messange' => 'Cửa hàng đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }
}
function getShopList()
{

    global $conn;
    $query = "SELECT * FROM user_shop";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Customer List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No airline found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function getShop($shopParams)
{
    global $conn;
    if ($shopParams['maNVshop'] == null) {
        return error422('Nhập mã cửa hàng');
    }

    $shopId = mysqli_real_escape_string($conn, $shopParams['maNVshop']);
    $query = "SELECT * FROM user_shop WHERE maNVshop = '$shopId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Shop Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không có cửa hàng nào được tìm thấy'
            ];
            header("HTTP/1.0 404 Internal server error");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

function updateShop($shopInput, $shopParams)
{
    global $conn;

    if (!isset($shopParams['maNVshop'])) {
        return error422('Mã cửa hàng không tìm thấy');
    } elseif ($shopParams['maNVshop'] == null) {
        return error422('Nhập mã cửa hàng');
    }

    $shopId = intval(mysqli_real_escape_string($conn, $shopParams['maNVshop']));
    $taikhoan = mysqli_real_escape_string($conn, $_POST['taikhoan']);
    $matkhau = mysqli_real_escape_string($conn, $_POST['matkhau']);
    $tenShop = mysqli_real_escape_string($conn, $_POST['tenShop']);
    $diaChi = mysqli_real_escape_string($conn, $_POST['diaChi']);

    $hashedPassword = password_hash($matkhau, PASSWORD_DEFAULT);
    $query = "UPDATE user_shop SET taikhoan='$taikhoan', matkhau = '$hashedPassword', tenShop = '$tenShop', diaChi = '$diaChi'
         WHERE maNVshop = '$shopId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {

        $data = [
            'status' => 200,
            'messange' => 'Cửa hàng đã được sửa thành công',
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Method not allowed");
        echo json_encode($data);
    }
}

function deleteShop($shopParams)
{
    global $conn;

    if (!isset($shopParams['maNVshop'])) {
        return error422('Mã cửa hàng không tìm thấy');
    } elseif ($shopParams['maNVshop'] == null) {
        return error422('Nhập mã cửa hàng');
    }

    $shopId = mysqli_real_escape_string($conn, $shopParams['maNVshop']);

    $query = "DELETE FROM user_shop WHERE maNVshop = '$shopId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 204,
            'messange' => 'Xóa thành công',
        ];
        header("HTTP/1.0 204 Deleted");
        echo json_encode($data);
    } else {
        $data = [
            'status' => 404,
            'messange' => 'Không tìm thấy cửa hàng',
        ];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
}

function searchShop($query)
{
    $db = new Database();
    $conn = $db->connect();

    // Chuẩn bị lại query cho đúng
    $query = "%" . trim($query) . "%"; // Thêm wildcard để tìm kiếm chính xác hơn

    // Sử dụng SQL để tìm kiếm shop
    $sql = "SELECT * FROM user_shop WHERE tenShop LIKE :query";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':query', $query);
    $stmt->execute();

    $shops = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $shops[] = $row;
    }

    return json_encode($shops); // Trả về dữ liệu dạng JSON
}
//End shop

// Thanh toán app KH
function getPay($payParams)
{
    global $conn;
    if ($payParams['maKH'] == null) {
        return error422('Nhập mã khách hàng');
    }

    $payId = mysqli_real_escape_string($conn, $payParams['maKH']);
    $query = "SELECT maKH,
                    DATE(create_at) AS ngayThanhToan,
                    TIME(create_at) AS gioThanhToan,
                    tongThanhToan
                FROM veDaDat
                WHERE maKH = '$payId'
                ORDER BY create_at DESC";

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Customer List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 405,
                'messange' =>  'No airline found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
// End thanh toán

function getMaVeFromHangVe($params)
{
    global $conn;

    if (!isset($params['hangVe']) || empty($params['hangVe'])) {
        return json_encode([
            'status' => 422,
            'message' => 'Vui lòng nhập hạng vé'
        ]);
    }

    $hangVe = mysqli_real_escape_string($conn, $params['hangVe']);
    $sql = "SELECT maVe FROM ve WHERE hangVe = '$hangVe' LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Lấy mã vé thành công',
                'data' => $row
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Không tìm thấy mã vé cho hạng đã chọn'
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Lỗi truy vấn CSDL'
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}