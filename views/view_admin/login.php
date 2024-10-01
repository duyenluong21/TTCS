
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    <title>Login</title>
</head>
<body>
    <main id="main-login-doctor">
        <div class="box-doctor">
            <div class="inner-box">
                
                    <form method="POST" action="/app/controllers/xuLyLogin.php" class = "sign-in-form"  >
                        <div class="logo">
                            <img src="../../public/img/logo.png" alt="logo">
                            <h3>Go to work</h3>
                        </div>

                        <div class="heading">
                            <h6>Đăng nhập vào tài khoản của bạn</h6>
                        </div>

                        <div class = "actual-form">
                            <div class = "input-wrap">
                                <input type="text" class = "input-field" name="username" id="username" autocomplete="off" id = "name" required >
                                <label for="name">Tài khoản</label>

                            </div>
                            <div class = "input-wrap">
                                <input type="password" class = "input-field" name="passw" id ="passw" autocomplete="off" id = "password" required>
                                <label for="password">Mật khẩu</label>

                            </div>

                            <input type="submit" name="login" value = "Đăng nhập"class="sign-btn">
                            <a class = "#">Quên mật khẩu?</a>
                        </div>
                    </form>


                
            </div>
        </div>
    </main>
    <script src = "../../public/js/main.js"></script>
</body>
</html>