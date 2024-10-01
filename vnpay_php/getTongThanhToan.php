<?php
require_once("./config.php");

// Thực hiện truy vấn để lấy giá trị tongThanhToan từ CSDL
// (Đây chỉ là ví dụ, bạn cần thay thế bằng mã truy vấn thực tế của bạn)
$connection = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT tongThanhToan FROM vedadat";
$result = mysqli_query($connection, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $tongThanhToan = $row['tongThanhToan'];
} else {
    // Xử lý khi truy vấn thất bại
    $tongThanhToan = 0; // hoặc bất kỳ giá trị mặc định nào bạn chọn
}

mysqli_close($connection);
?>
