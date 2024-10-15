<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Khách Hàng</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <style>
        /* CSS cho giao diện */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-container input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }

        .search-container button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th,
        table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
            color: #333;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination button {
            margin: 0 5px;
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .pagination button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header class="header">
    <?php include("header_shop.php"); ?>
    </header>
    <div class="container">
        <h1>Danh Sách Khách Hàng</h1>
        <p id="error" class="error" style="display: none;"></p>

        <!-- Thanh tìm kiếm -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Tìm kiếm khách hàng...">
            <button id="searchButton">Tìm kiếm</button>
        </div>

        <table id="customerTable">
            <thead>
                <tr>
                    <th>Mã Khách Hàng</th>
                    <th>Họ Tên</th>
                    <th>Email</th>
                    <th>Giới Tính</th>
                    <th>Địa Chỉ</th>
                    <th>Số Điện Thoại</th>
                    <th>Ngày Sinh</th>
                    <th>Ngày Đăng Ký</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dữ liệu khách hàng sẽ được chèn ở đây -->
            </tbody>
        </table>

        <div class="pagination" id="pagination"></div>
    </div>

    <script>
        const customersPerPage = 5; // Số khách hàng trên mỗi trang
        let currentPage = 1; // Trang hiện tại
        let customers = []; // Mảng chứa danh sách khách hàng

        // Hàm để gọi API và hiển thị danh sách khách hàng
        function fetchCustomers() {
            fetch('http://localhost:3000/app/api/readPassenger.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200 && Array.isArray(data.data)) {
                        customers = data.data; // Lưu danh sách khách hàng vào mảng
                        displayCustomers(); // Hiển thị khách hàng
                        setupPagination(); // Thiết lập phân trang
                    } else {
                        const errorElement = document.getElementById('error');
                        errorElement.style.display = 'block';
                        errorElement.textContent = data.message || 'Không tìm thấy khách hàng!';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorElement = document.getElementById('error');
                    errorElement.style.display = 'block';
                    errorElement.textContent = 'Lỗi kết nối đến máy chủ!';
                });
        }

        // Hàm để hiển thị khách hàng trên trang
        function displayCustomers() {
            const customerTableBody = document.querySelector('#customerTable tbody');
            customerTableBody.innerHTML = ''; // Xóa nội dung cũ

            // Tính toán chỉ số bắt đầu và kết thúc
            const startIndex = (currentPage - 1) * customersPerPage;
            const endIndex = Math.min(startIndex + customersPerPage, customers.length);

            // Lặp qua danh sách khách hàng và chèn vào bảng
            for (let i = startIndex; i < endIndex; i++) {
                const customer = customers[i];
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${customer.maKH}</td>
                    <td>${customer.fullname}</td>
                    <td>${customer.email}</td>
                    <td>${customer.gioiTinh}</td>
                    <td>${customer.diaChi}</td>
                    <td>${customer.soDT}</td>
                    <td>${customer.ngaySinh}</td>
                    <td>${customer.ngayDangKy}</td>
                `;
                customerTableBody.appendChild(row);
            }
        }

        // Hàm để thiết lập phân trang
        function setupPagination() {
            const paginationElement = document.getElementById('pagination');
            paginationElement.innerHTML = ''; // Xóa nội dung cũ
            const totalPages = Math.ceil(customers.length / customersPerPage);

            // Tạo nút "Trước"
            if (currentPage > 1) {
                const prevButton = document.createElement('button');
                prevButton.textContent = 'Trước';
                prevButton.onclick = () => {
                    currentPage--;
                    displayCustomers();
                    setupPagination();
                };
                paginationElement.appendChild(prevButton);
            }

            // Tạo nút cho từng trang
            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement('button');
                pageButton.textContent = i;
                pageButton.onclick = () => {
                    currentPage = i;
                    displayCustomers();
                    setupPagination();
                };
                paginationElement.appendChild(pageButton);
            }

            // Tạo nút "Tiếp theo"
            if (currentPage < totalPages) {
                const nextButton = document.createElement('button');
                nextButton.textContent = 'Tiếp theo';
                nextButton.onclick = () => {
                    currentPage++;
                    displayCustomers();
                    setupPagination();
                };
                paginationElement.appendChild(nextButton);
            }
        }

        // Hàm tìm kiếm khách hàng
// Hàm tìm kiếm khách hàng
function searchCustomers() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const filteredCustomers = customers.filter(customer =>
        customer.fullname.toLowerCase().includes(searchInput) ||
        customer.email.toLowerCase().includes(searchInput) ||
        customer.diaChi.toLowerCase().includes(searchInput)
    );

    // Hiển thị khách hàng đã lọc
    const customerTableBody = document.querySelector('#customerTable tbody');
    customerTableBody.innerHTML = ''; // Xóa nội dung cũ

    filteredCustomers.forEach(customer => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${customer.maKH}</td>
            <td>${customer.fullname}</td>
            <td>${customer.email}</td>
            <td>${customer.gioiTinh}</td>
            <td>${customer.diaChi}</td>
            <td>${customer.soDT}</td>
            <td>${customer.ngaySinh}</td>
            <td>${customer.ngayDangKy}</td>
        `;
        customerTableBody.appendChild(row);
    });
}

// Sự kiện cho ô tìm kiếm
document.getElementById('searchInput').addEventListener('input', searchCustomers);

// Gọi hàm khi trang được tải
window.onload = fetchCustomers;
    </script>
</body>

</html>
