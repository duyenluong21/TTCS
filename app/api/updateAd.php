<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: POST") ;

include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy mã quảng cáo và các thông tin khác
    $maqc = $_POST['maqc'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $place = $_POST['place'];

    // Kiểm tra nếu có ảnh mới được tải lên
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];

        // Đường dẫn lưu trữ ảnh
        $uploadDir = '../uploads/quangCao/';
        $fileName = basename($image['name']);
        $uploadFilePath = $uploadDir . $fileName;

        // Di chuyển tệp hình ảnh được tải lên đến thư mục đích
        if (move_uploaded_file($image['tmp_name'], $uploadFilePath)) {
            // Cập nhật thông tin quảng cáo bao gồm cả đường dẫn ảnh mới
            $query = "UPDATE quangcao SET name = '$name', description = '$description', img = '$fileName', place = '$place' WHERE maqc = '$maqc'";
        } else {
            echo json_encode(['error' => 'Failed to upload image.']);
            exit;
        }
    } else {
        // Nếu không có ảnh mới, chỉ cập nhật các thông tin khác
        $query = "UPDATE quangcao SET name = '$name', description = '$description' WHERE maqc = '$maqc'";
    }

    // Thực thi câu lệnh SQL
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => 'Ad updated successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to update ad.']);
    }
}
?>