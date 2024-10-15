<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Cửa hàng bán vé</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>

<body class="body-login">

    <div class="login-container">
        <div class="logo">
            <img src="../../public/img/logo_shop.png" alt="logo">
            <h3 class="text-login">Đăng nhập</h3>
        </div>

        <form id="loginForm">
            <div class="form-group">
                <label for="taikhoan">Tên đăng nhập</label>
                <input type="text" id="taikhoan" name="taikhoan" required>
            </div>
            <div class="form-group">
                <label for="matkhau">Mật khẩu</label>
                <input type="password" id="matkhau" name="matkhau" required>
            </div>

            <p id="error" class="error" style="display: none;"></p>
            <button type="submit" class="btn">Đăng Nhập</button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Ngăn chặn form gửi thông thường

            // Lấy thông tin từ form
            const taikhoan = document.getElementById('taikhoan').value;
            const matkhau = document.getElementById('matkhau').value;

            // Gửi yêu cầu đến API đăng nhập
            fetch('http://localhost:3000/app/api/process_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    taikhoan: taikhoan,
                    matkhau: matkhau
                })
            })
            .then(response => response.json())  // Chuyển đổi phản hồi sang JSON
            .then(data => {
                // Kiểm tra phản hồi từ API
                if (data.status === 200 && data.user && data.user.maNVshop) {
                    const maNVshop = data.user.maNVshop; // Lấy maNVshop từ phản hồi

                    // Lưu maNVshop vào localStorage để sử dụng sau này
                    localStorage.setItem('maNVshop', maNVshop);

                    // Chuyển hướng đến trang home_shop.php sau khi đăng nhập thành công
                    window.location.href = 'home_shop.php';
                } else {
                    // Hiển thị thông báo lỗi
                    const errorElement = document.getElementById('error');
                    errorElement.style.display = 'block';
                    errorElement.textContent = data.message || 'Lỗi không xác định!';
                }
            })
            .catch(error => {
                // Xử lý lỗi khi kết nối API thất bại
                console.error('Error:', error);
                const errorElement = document.getElementById('error');
                errorElement.style.display = 'block';
                errorElement.textContent = 'Lỗi kết nối đến máy chủ!';
            });
        });
    </script>

</body>

</html>
