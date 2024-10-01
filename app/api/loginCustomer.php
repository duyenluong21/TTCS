<?php
   // Include hoặc require file chứa hàm logIn
   include 'function.php';

// Kiểm tra xem có dữ liệu được gửi từ form không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form (đã validate trước đó)
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Gọi hàm logIn để đăng nhập
    $loginResult = logIn(['email' => $email, 'password' => $password]);

    // Kiểm tra kết quả đăng nhập
    if ($loginResult) {
        // Đăng nhập thành công
        echo "Đăng nhập thành công!";
        // Redirect hoặc thực hiện các hành động cần thiết
    } else {
        // Đăng nhập thất bại
        echo "Đăng nhập thất bại!";
        // Hiển thị thông báo hoặc thực hiện các hành động cần thiết
    }
}
?>