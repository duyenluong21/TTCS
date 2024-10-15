<!-- payment_success.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <style>
        /* styles.css */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Roboto', sans-serif;
            }

            body {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                background-color: #f4f4f4;
            }

            .container {
                background-color: #fff;
                padding: 20px 30px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                max-width: 600px;
                text-align: center;
            }

            .success-icon img {
                width: 80px;
                height: 80px;
                margin-bottom: 20px;
            }

            h1 {
                font-size: 24px;
                color: #4CAF50;
                margin-bottom: 15px;
            }

            p {
                font-size: 16px;
                color: #333;
                margin-bottom: 10px;
            }

            .order-summary {
                background-color: #f9f9f9;
                padding: 15px;
                border-radius: 8px;
                text-align: left;
                margin-top: 20px;
            }

            .order-summary p {
                font-size: 16px;
                margin-bottom: 10px;
                line-height: 1.6;
            }

            .order-summary strong {
                color: #333;
            }

            .actions {
                margin-top: 20px;
            }

            button {
                padding: 10px 20px;
                background-color: #4CAF50;
                border: none;
                color: white;
                font-size: 16px;
                cursor: pointer;
                border-radius: 5px;
                transition: background-color 0.3s ease;
            }

            button:hover {
                background-color: #45a049;
            }

            @media (max-width: 600px) {
                .container {
                    padding: 15px 20px;
                    max-width: 90%;
                }

                h1 {
                    font-size: 20px;
                }

                p {
                    font-size: 14px;
                }

                .order-summary p {
                    font-size: 14px;
                }
            }

    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <img src="https://img.icons8.com/color/96/000000/checked--v4.png" alt="Success">
        </div>
        <h1>Thanh toán thành công!</h1>
        <p>Cảm ơn bạn đã đặt vé. Thông tin giao dịch của bạn:</p>

        <div class="order-summary">
            <p><strong>Mã đơn hàng:</strong> <span id="orderId"></span></p>
            <p><strong>Mã vé:</strong> <span id="maVe"></span></p>
            <p><strong>Mã chuyến bay:</strong> <span id="maCB"></span></p>
            <p><strong>Mã khách hàng:</strong> <span id="maKH"></span></p>
            <p><strong>Số lượng đặt:</strong> <span id="ticketQuantity"></span></p>
            <p><strong>Tổng thanh toán:</strong> <span id="totalAmount"></span> VNĐ</p>
        </div>

        <div class="actions">
            <button onclick="goHome()">Quay lại trang chủ</button>
        </div>
    </div>

    <script>
        // Lấy dữ liệu từ localStorage
        const paymentSuccessData = JSON.parse(localStorage.getItem('paymentSuccessData'));

        // Hiển thị dữ liệu trên trang
        document.getElementById('orderId').textContent = paymentSuccessData.order_id;
        document.getElementById('maVe').textContent = paymentSuccessData.maVe;
        document.getElementById('maCB').textContent = paymentSuccessData.maCB;
        document.getElementById('maKH').textContent = paymentSuccessData.maKH;
        document.getElementById('ticketQuantity').textContent = paymentSuccessData.soLuongDat;
        document.getElementById('totalAmount').textContent = paymentSuccessData.tongThanhToan;

        function goHome(){
            window.location.href = "home_shop.php";
        }
    </script>
</body>
</html>
