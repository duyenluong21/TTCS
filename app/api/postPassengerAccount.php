<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "POST") {
    $inputData = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($inputData['email']) || !isset($inputData['password'])) {
        echo json_encode([
            'status' => 400,
            'message' => 'Email và mật khẩu là bắt buộc'
        ]);
        exit;
    }

    $email = mysqli_real_escape_string($conn, $inputData['email']);
    $password = $inputData['password'];

    // Gọi hàm để lấy thông tin người dùng từ CSDL
    $user = postPassengerAccount($email);

    if ($user) {
        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            // Tạo JWT token
            $token = generateJWT($user);

            echo json_encode([
                'status' => 200,
                'message' => 'Đăng nhập thành công',
                'data' => $user,
                'token' => $token,
                'isFingerprintRegistered' => (bool)$user['isFingerprintRegistered']
            ]);
        } else {
            echo json_encode([
                'status' => 401,
                'message' => 'Thông tin đăng nhập không hợp lệ'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 404,
            'message' => 'Người dùng không tồn tại'
        ]);
    }
} else {
    echo json_encode([
        'status' => 405,
        'message' => 'Phương thức không được phép'
    ]);
}
ob_end_flush();
?>
