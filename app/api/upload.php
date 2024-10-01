<?php
// Kiểm tra nếu yêu cầu là POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra nếu file đã được tải lên
    if (isset($_FILES['image'])) {
        $target_dir = dirname(__DIR__) . "/../uploads/quangCao/";
        // echo "Target directory: " . $target_dir;
        // if (!is_dir($target_dir)) {
        //     mkdir($target_dir, 0777, true); // Tạo thư mục nếu không tồn tại
        // }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra kích thước ảnh
        if ($_FILES["image"]["size"] > 500000) { // 500KB
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
                echo "The file ". htmlspecialchars(basename($_FILES["image"]["name"])). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "No file uploaded.";
    }
} else {
    echo "Invalid request method.";
}
?>
