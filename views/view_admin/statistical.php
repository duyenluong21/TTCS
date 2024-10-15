<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Statistical</title>
    <style>
        /* Thêm một chút CSS để các tab trông đẹp hơn */
        .tab {
            display: none;
        }
        .tab-active {
            display: block;
        }
        .tab-links {
            margin: 10px;
        }
        .tab-link {
            padding: 10px;
            cursor: pointer;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            margin-right: 5px;
        }
        .tab-link.active {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <!--category-left -->
    <?php include("../TTCS/views/view_admin/category_left.php"); ?>
    <!--category-right -->
    <div class="category-right">
        <?php include("../TTCS/views/view_admin/header.php"); ?>

        <div class="tab-links">
            <button class="tab-link active" data-tab="allStores">Doanh thu tất cả cửa hàng</button>
            <button class="tab-link" data-tab="specificStores">Doanh thu của từng cửa hàng</button>
        </div>

        <div id="allStores" class="tab tab-active">
            <div id="ticketsContainer" style="height: 400px; width: 100%;"></div>
            <div id="revenueContainer" style="height: 400px; width: 100%;"></div>
        </div>

        <div id="specificStores" class="tab">
            <div id="specificTicketsContainer" style="height: 400px; width: 100%;"></div>
            <div id="specificRevenueContainer" style="height: 400px; width: 100%;"></div>
        </div>
    </div>
    
    <script src="https://code.highcharts.com/highcharts.js"></script>
    
    <script>
    $(document).ready(function() {
        let totalTickets = new Array(12).fill(0); // Khởi tạo mảng với 12 tháng
        let totalRevenue = new Array(12).fill(0); // Khởi tạo mảng doanh thu tương ứng

        // Hàm để tải dữ liệu cho tất cả cửa hàng
        function loadAllStoresData() {
            fetch('http://localhost:3000/app/api/readStatistical.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200) {
                        data.data.forEach(item => {
                            const monthIndex = parseInt(item.month) - 1;
                            totalTickets[monthIndex] = parseInt(item.total_tickets) || 0;
                            totalRevenue[monthIndex] = parseInt(item.total_revenue) || 0;
                        });

                        // Tạo biểu đồ số vé bán ra cho tất cả cửa hàng
                        Highcharts.chart('ticketsContainer', {
                            title: { text: 'Số vé bán ra hàng tháng (Tất cả cửa hàng)' },
                            xAxis: { categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] },
                            yAxis: { title: { text: 'Số lượng vé' } },
                            series: [{ name: 'Số vé bán ra', data: totalTickets }]
                        });

                        // Tạo biểu đồ doanh thu cho tất cả cửa hàng
                        Highcharts.chart('revenueContainer', {
                            title: { text: 'Doanh thu hàng tháng (Tất cả cửa hàng)' },
                            xAxis: { categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] },
                            yAxis: { title: { text: 'Doanh thu (VND)' } },
                            series: [{ name: 'Doanh thu', data: totalRevenue, color: '#33CC33' }]
                        });
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }

// Hàm để tải dữ liệu cho từng cửa hàng
function loadSpecificStoresData() {
    // Lấy dữ liệu cho từng cửa hàng từ API
    fetch('http://localhost:3000/app/api/readStatisticAdmin.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 200) {
                const storeData = {}; // Đối tượng để lưu dữ liệu theo cửa hàng

                // Duyệt qua từng mục trong dữ liệu
                data.data.forEach(item => {
                    const monthIndex = parseInt(item.month) - 1; // Chuyển đổi tháng thành chỉ số mảng

                    // Nếu cửa hàng chưa tồn tại trong đối tượng storeData, khởi tạo nó
                    if (!storeData[item.taikhoan]) {
                        storeData[item.taikhoan] = {
                            totalTickets: new Array(12).fill(0),
                            totalRevenue: new Array(12).fill(0),
                        };
                    }

                    // Cộng số vé và doanh thu cho cửa hàng
                    storeData[item.taikhoan].totalTickets[monthIndex] += parseInt(item.tongVe) || 0;
                    storeData[item.taikhoan].totalRevenue[monthIndex] += parseInt(item.tongTien) || 0;
                });

                // Tạo mảng series cho biểu đồ
                const ticketSeries = [];
                const revenueSeries = [];
                for (const store in storeData) {
                    ticketSeries.push({
                        name: store, // Tên cửa hàng
                        data: storeData[store].totalTickets // Dữ liệu số vé
                    });

                    revenueSeries.push({
                        name: store, // Tên cửa hàng
                        data: storeData[store].totalRevenue // Dữ liệu doanh thu
                        
                    });
                }

                // Tạo biểu đồ số vé bán ra cho từng cửa hàng
                Highcharts.chart('specificTicketsContainer', {
                    title: { text: 'Số vé bán ra hàng tháng (Từng cửa hàng)' },
                    xAxis: { categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] },
                    yAxis: { title: { text: 'Số lượng vé' } },
                    series: ticketSeries // Sử dụng mảng series đã tạo
                });

                // Tạo biểu đồ doanh thu cho từng cửa hàng
                Highcharts.chart('specificRevenueContainer', {
                    title: { text: 'Doanh thu hàng tháng (Từng cửa hàng)' },
                    xAxis: { categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] },
                    yAxis: { title: { text: 'Doanh thu (VND)' } },
                    series: revenueSeries // Sử dụng mảng series đã tạo
                });
            } else {
                console.error(data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}


        // Gọi hàm tải dữ liệu cho tất cả cửa hàng khi tải trang
        loadAllStoresData();

        // Sự kiện click cho các tab
        $('.tab-link').on('click', function() {
            const tabId = $(this).data('tab');

            // Ẩn tất cả các tab
            $('.tab').removeClass('tab-active');
            // Hiện tab được chọn
            $('#' + tabId).addClass('tab-active');

            // Thay đổi trạng thái của các tab
            $('.tab-link').removeClass('active');
            $(this).addClass('active');

            // Nếu tab là 'specificStores', tải dữ liệu cho các cửa hàng
            if (tabId === 'specificStores') {
                loadSpecificStoresData();
            }
        });
    });
    </script>
</body>
</html>
