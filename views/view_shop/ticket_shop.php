<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý vé</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Thêm CSS cho pagination.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" />

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 20px;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .table {
            width: 90%; 
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .search-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .pagination {
            display: flex;
            justify-content: center;
        }

        .pagination li {
            padding: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
    <?php include("header_shop.php"); ?>
    </header>
<div class="container-ticket">
    <h2 class="text-center">Quản lý vé đã đặt</h2>

    <!-- Thanh tìm kiếm và bộ lọc -->
    <div class="search-bar">
        <div class="col-md-6">
            <input type="text" id="search-ticket" class="form-control" placeholder="Tìm tên khách hàng, số điện thoại...">
        </div>
    </div>

    <!-- Bảng danh sách vé -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Tên khách hàng</th>
                <th scope="col">Ngày sinh</th>
                <th scope="col">Số điện thoại</th>
                <th scope="col">Ngày đi</th>
                <th scope="col">Giờ bay</th>
                <th scope="col">Địa điểm đi</th>
                <th scope="col">Địa điểm đến</th>
                <th scope="col">Hạng vé</th>
                <th scope="col">Số lượng đặt</th>
                <th scope="col">Tổng thanh toán</th>
                <th scope="col">Ngày đặt vé</th>
            </tr>
        </thead>
        <tbody id="ticket-list">
            <!-- Các vé sẽ được thêm động tại đây -->
        </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <div id="pagination-container"></div>
</div>

<!-- Bao gồm jQuery và pagination.js -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.min.js"></script>

<script>
    var ticketsData = [];  // Dữ liệu vé
    var ticketsPerPage = 10;  // Số vé trên mỗi trang

    $(document).ready(function() {
    // Lấy maNVshop từ localStorage
    const maNVshop = localStorage.getItem('maNVshop');

    if (maNVshop) {
        // Gọi API với maNVshop
        fetchTickets(maNVshop);
    } else {
        console.error('Không tìm thấy maNVshop. Vui lòng đăng nhập.');
        // Chuyển hướng về trang đăng nhập nếu không có maNVshop
        window.location.href = 'login_shop.php';
    }
});

    // Hàm lấy dữ liệu vé từ API
    function fetchTickets(maShop) {
        $.ajax({
            url: 'http://localhost:3000/app/api/readTicketWeb.php?maShop=${maShop}',
            method: 'GET',
            dataType: 'json',
            success: function(result) {
                if (result.status === 200 && result.data.length > 0) {
                    ticketsData = result.data;
                    applyFiltersAndPagination(); // Gọi hàm áp dụng bộ lọc và phân trang
                } else {
                    console.error('Không có vé nào được tìm thấy.');
                    $('#ticket-list').html('<tr><td colspan="10" class="text-center">Không có vé nào được tìm thấy.</td></tr>');
                }
            },
            error: function(error) {
                console.error('Lỗi khi lấy dữ liệu vé:', error);
            }
        });
    }

    // Hàm áp dụng bộ lọc và phân trang
    function applyFiltersAndPagination() {
        const searchTerm = $('#search-ticket').val().toLowerCase();

        // Lọc dữ liệu dựa trên từ khóa tìm kiếm
        const filteredData = ticketsData.filter(ticket => {
            const fullname = ticket.fullname.toLowerCase();
            const soDT = ticket.soDT.toLowerCase();
            return fullname.includes(searchTerm) || soDT.includes(searchTerm);
        });

        // Nếu không có dữ liệu sau khi lọc
        if (filteredData.length === 0) {
            $('#ticket-list').html('<tr><td colspan="10" class="text-center">Không tìm thấy vé nào.</td></tr>');
            $('#pagination-container').empty(); // Xóa phân trang nếu không có dữ liệu
            return;
        }

        // Áp dụng phân trang cho dữ liệu đã lọc
        $('#pagination-container').pagination({
            dataSource: filteredData,
            pageSize: ticketsPerPage,
            callback: function(data, pagination) {
                displayTickets(data);
            }
        });
    }

    // Hàm hiển thị vé
    function displayTickets(tickets) {
        const ticketList = $('#ticket-list');
        ticketList.empty();

        tickets.forEach(ticket => {
            const ticketRow = `
                <tr>
                    <td>${ticket.fullname}</td>
                    <td>${ticket.ngaySinh}</td>
                    <td>${ticket.soDT}</td>
                    <td>${ticket.ngayDi}</td>
                    <td>${ticket.gioBay}</td>
                    <td>${ticket.diaDiemDi}</td>
                    <td>${ticket.diaDiemDen}</td>
                    <td>${ticket.hangVe}</td>
                    <td>${ticket.soLuongDat}</td>
                    <td>${Number(ticket.tongThanhToan).toLocaleString()} VNĐ</td>
                    <td>${ticket.create_at}</td>
                </tr>
            `;
            ticketList.append(ticketRow);
        });
    }

    // Gọi hàm fetchTickets sau khi trang tải xong
    $(document).ready(function() {
        fetchTickets();

        // Lắng nghe sự kiện khi người dùng nhập vào thanh tìm kiếm
        $('#search-ticket').on('input', function() {
            applyFiltersAndPagination();
        });
    });
</script>

</body>
</html>
