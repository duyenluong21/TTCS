<?php  // Kết nối tới CSDL
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: POST") ;

include 'function.php';
$requestMethod = $_SERVER["REQUEST_METHOD"];
// Nhận dữ liệu từ AJAX và gọi hàm loginUser
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
        // Kiểm tra xem dữ liệu có được giải mã thành công không
        if (json_last_error() !== JSON_ERROR_NONE) {
            return error422('Dữ liệu đầu vào không hợp lệ');
        }
    $userInput = [
        'matkhau' => $input['matkhau'] ?? null,
        'taikhoan' => $input['taikhoan'] ?? null
    ];

    // Gọi hàm loginUser và xử lý đăng nhập
    loginUserShop($userInput);
}else{
    $data = [
        'status' => 404,
        'messange' => $requestMethod. 'Method not allowed',
    ];
    header("HTTP/1.0 404 Method not allowed");
    echo json_encode($data);
}
?>