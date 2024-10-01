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
</head>
<body>
    <!--category-left -->
        <?php include("../TTCS/views/view_admin/category_left.php"); ?>
    <!--category-right -->
    <div class="category-right">
        <?php include("../TTCS/views/view_admin/header.php"); ?>

        <div id="ticketsContainer" style="height: 400px; width: 100%;"></div>
        <div id="revenueContainer" style="height: 400px; width: 100%;"></div>
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
$(document).ready(function() {
    let totalTickets = new Array(12).fill(0); // Khởi tạo mảng với 12 tháng
    let totalRevenue = new Array(12).fill(0); // Khởi tạo mảng doanh thu tương ứng

    fetch('http://localhost:3000/app/api/readStatistical.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 200) {
                // Lặp qua dữ liệu và gán giá trị cho tháng tương ứng
                data.data.forEach(item => {
                    const monthIndex = parseInt(item.month) - 1; // Chuyển đổi tháng thành chỉ số mảng (0-11)
                    totalTickets[monthIndex] = parseInt(item.total_tickets) || 0; // Cập nhật số vé bán ra
                    totalRevenue[monthIndex] = parseInt(item.total_revenue) || 0; // Cập nhật doanh thu
                });

                // Tạo biểu đồ số vé bán ra
                Highcharts.chart('ticketsContainer', {
                    title: {
                        text: 'Số vé bán ra hàng tháng'
                    },
                    xAxis: {
                        categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                    },
                    yAxis: {
                        title: {
                            text: 'Số lượng vé'
                        }
                    },
                    series: [{
                        name: 'Số vé bán ra',
                        data: totalTickets // Dữ liệu số vé bán ra
                    }]
                });

                // Tạo biểu đồ doanh thu
                Highcharts.chart('revenueContainer', {
                    title: {
                        text: 'Doanh thu hàng tháng'
                    },
                    xAxis: {
                        categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                    },
                    yAxis: {
                        title: {
                            text: 'Doanh thu (VND)'
                        }
                    },
                    series: [{
                        name: 'Doanh thu',
                        data: totalRevenue, // Dữ liệu doanh thu
                        color: '#33CC33'
                    }]
                });
            } else {
                console.error(data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
});

</script>

</body>