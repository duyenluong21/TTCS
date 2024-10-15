<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .content-background {
            /* background-image: url('../../public/img/img_BG.png');  */
            background-size: cover;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

    </style>
</head>
<body>

    <!-- Header với thanh điều hướng -->
    <header class="header">
        <?php include("header_shop.php"); ?>
    </header>

    <!-- Nội dung chính -->
    <div class="container my-4 content-background">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-custom text-center">
                    <img src="../../public/img/img_ticket_shop.png" class="img-fluid" alt="Vé đã bán" style="height: 190px; margin-top: 10px;">
                    <div class="card-body">
                        <h4 id="total-tickets">0 Vé đã bán</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom text-center">
                    <img src="../../public/img/img_flight_shop.png" class="img-fluid" alt="Chuyến bay" style="height: 190px; margin-top: 10px;">
                    <div class="card-body">
                        <h4 id="total-flights">0 Chuyến bay hiện có</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom text-center">
                    <img src="../../public/img/img_statiscal.png" class="img-fluid" alt="Doanh thu" style="height: 190px; margin-top: 10px;">
                    <div class="card-body">
                        <h4 id="total-revenue">Doanh thu: 0 VNĐ</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        async function fetchData() {
            const shopId = 3; // Thay đổi maShop ở đây nếu cần
            
            // Lấy dữ liệu vé đã bán
            const apiUrlTickets = `http://localhost:3000/app/api/readHomeShop.php?maShop=${shopId}`;
            // Lấy dữ liệu tổng số chuyến bay
            const apiUrlFlights = `http://localhost:3000/app/api/readSumFlight.php`;

            try {
                // Fetch vé đã bán
                const responseTickets = await fetch(apiUrlTickets);
                const resultTickets = await responseTickets.json();

                if (resultTickets.status === 200 && resultTickets.data.length > 0) {
                    const dataTickets = resultTickets.data[0];
                    // Cập nhật nội dung vé đã bán
                    document.getElementById('total-tickets').textContent = `${dataTickets.total_tickets} Vé đã bán`;
                    document.getElementById('total-revenue').textContent = `Doanh thu: ${parseInt(dataTickets.total_revenue).toLocaleString()} VNĐ`;
                } else {
                    console.error(resultTickets.message);
                }

                // Fetch tổng số chuyến bay
                const responseFlights = await fetch(apiUrlFlights);
                const resultFlights = await responseFlights.json();

                if (resultFlights.status === 200 && resultFlights.data.length > 0) {
                    const dataFlights = resultFlights.data[0];
                    // Cập nhật nội dung tổng số chuyến bay
                    document.getElementById('total-flights').textContent = `${dataFlights.total_flights} Chuyến bay hiện có`;
                } else {
                    console.error(resultFlights.message);
                }
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        // Gọi hàm fetchData khi trang được tải
        window.onload = fetchData;
    </script>

</body>
</html>
