<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doanh Thu Cửa Hàng</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        /* padding: 20px; */
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
        }

        canvas {
            margin: 20px 0;
            border: 1px solid #ccc; /* Thêm viền cho canvas */
            background-color: #fff; /* Đặt nền trắng cho canvas */
        }

    </style>
</head>
<body>
            <!-- Header -->
    <header class="header">
    <?php include("header_shop.php"); ?>
    </header>
    <div class="container">
        <h1>Thống Kê Doanh Thu</h1>
        <canvas id="revenueChart" width="400" height="200"></canvas>
    </div>

    <script>
document.addEventListener("DOMContentLoaded", async () => {
    const maShop = localStorage.getItem('maNVshop');
    const response = await fetch(`http://localhost:3000/app/api/readStatiscalShop.php?maShop=${maShop}`);
    
    if (!response.ok) {
        console.error('Error fetching data:', response.statusText);
        return;
    }

    const result = await response.json();

    if (result.status === 200) {
        const months = result.data.map(item => item.month);
        const totalTickets = result.data.map(item => parseInt(item.total_tickets));
        const totalRevenue = result.data.map(item => parseInt(item.total_revenue));

        // Vẽ biểu đồ
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'bar', // Loại biểu đồ
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Tổng Vé Bán Ra',
                        data: totalTickets,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        yAxisID: 'y-tickets'
                    },
                    {
                        label: 'Doanh Thu (VNĐ)',
                        data: totalRevenue,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 2,
                        type: 'line', // Đặt loại biểu đồ là 'line' cho doanh thu
                        fill: false,
                        yAxisID: 'y-revenue'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lượng'
                        }
                    },
                    'y-tickets': {
                        type: 'linear', // Định dạng trục y cho vé
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Tổng Vé Bán Ra'
                        },
                        beginAtZero: true
                    },
                    'y-revenue': {
                        type: 'linear', // Định dạng trục y cho doanh thu
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Doanh Thu (VNĐ)'
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (context.dataset.type === 'line') {
                                    label += ': ' + context.raw.toLocaleString() + ' VNĐ'; // Định dạng doanh thu
                                } else {
                                    label += ': ' + context.raw + ' vé'; // Định dạng số vé
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        display: true
                    }
                }
            }
        });

        // Hiện số vé bán ra trên các cột
        revenueChart.data.datasets[0].data.forEach((value, index) => {
            const bar = revenueChart.getDatasetMeta(0).data[index];
            const label = `${value} vé`;
            bar.tooltip._model.body = [{ lines: [label] }];
        });
        
        revenueChart.update();
    } else {
        console.error('Error in API response:', result.message);
    }
});

    </script>
</body>
</html>
