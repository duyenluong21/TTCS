<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7f6;
        }
        .header {
            color: white;
            text-align: center;
        }
        .navbar-custom {
            background-color: #007bff;
        }
        .navbar-custom .navbar-brand, .navbar-custom .nav-link {
            color: white;
        }
        .navbar-custom .nav-link:hover {
            background-color: #0056b3;
        }
        .icon {
            height: 30px;
            margin-right: 5px;
        }
        .logo img{
            height: 60px;
        }
    </style>
</head>
<body>
    <!-- Header với thanh điều hướng -->
    <header class="header">
        <!-- Thanh điều hướng (Navigation Bar) -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <a class="navbar-brand logo" href="home_shop.php">
                <img src="../../public/img/logo_shop.png" alt="logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="flight_shop.php">
                            <img src="../../public/img/flight_ic.png" class="icon" alt="Quản lý chuyến bay"> Quản lý chuyến bay
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ticket_shop.php">
                            <img src="../../public/img/ticket_ic.png" class="icon" alt="Quản lý vé"> Quản lý vé
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="passenger_shop.php">
                            <img src="../../public/img/user_ic.png" class="icon" alt="Quản lý khách hàng"> Quản lý khách hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="statistics_shop.php">
                            <img src="../../public/img/statistics_ic.png" class="icon" alt="Doanh thu cửa hàng"> Doanh thu cửa hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login_shop.php">
                            <img src="../../public/img/logout_ic.png" class="icon" alt="Đăng xuất"> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</body>