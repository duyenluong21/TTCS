<?php
include 'function.php';
// Kiểm tra nếu yêu cầu là POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra nếu file đã được tải lên
    if (isset($_FILES['image']) && isset($_POST['description']) && isset($_POST['name'])) {
        $target_dir = dirname(__DIR__) . "/../uploads/quangCao/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra kích thước ảnh
        if ($_FILES["image"]["size"] > 50000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Kiểm tra định dạng file
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Kiểm tra xem $uploadOk có bằng 0 không (có lỗi không)
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Gọi hàm lưu quảng cáo với tên file đã tải lên
                $adInput = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'img' => basename($_FILES["image"]["name"]), // Chỉ lấy tên file
                ];

                // Gọi hàm lưu quảng cáo
                storeAd($adInput);
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "No file or description uploaded.";
    }
} else {
    echo "Invalid request method.";
}
?>
