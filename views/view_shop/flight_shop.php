<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Chuyến Bay - Đặt Vé</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7f6;
        }

        .card-custom {
            margin-bottom: 20px;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .pagination {
            display: flex;
            justify-content: center;
        }

        .pagination li {
            padding: 10px;
        }

        .tab-content {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
    <?php include("header_shop.php"); ?>
    </header>

    <!-- Nội dung chính -->
    <div class="container my-4">
        <h2 class="text-center">Danh Sách Chuyến Bay</h2>

        <!-- Tabs điều hướng -->
        <ul class="nav nav-tabs" id="flightTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="upcoming-flights-tab" data-toggle="tab" href="#upcoming-flights" role="tab" aria-controls="upcoming-flights" aria-selected="true">Chuyến Bay Sắp Bay</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="past-flights-tab" data-toggle="tab" href="#past-flights" role="tab" aria-controls="past-flights" aria-selected="false">Chuyến Bay Đã Bay</a>
            </li>
        </ul>

        <div class="tab-content" id="flightTabContent">
            <!-- Tab Chuyến bay sắp bay -->
            <div class="tab-pane fade show active" id="upcoming-flights" role="tabpanel" aria-labelledby="upcoming-flights-tab">
                <div class="row search-bar">
                    <div class="col-md-6">
                        <input type="text" id="searchUpcomingOrigin" class="form-control" placeholder="Tìm nơi đi...">
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="searchUpcomingDestination" class="form-control" placeholder="Tìm nơi đến...">
                    </div>
                </div>
                <div class="row" id="upcoming-flight-list">
                    <!-- Các chuyến bay sắp bay sẽ được thêm động tại đây -->
                </div>
                <div id="upcoming-pagination-container"></div>
            </div>

            <!-- Tab Chuyến bay đã bay -->
            <div class="tab-pane fade" id="past-flights" role="tabpanel" aria-labelledby="past-flights-tab">
                <div class="row search-bar">
                    <div class="col-md-6">
                        <input type="text" id="searchPastOrigin" class="form-control" placeholder="Tìm nơi đi...">
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="searchPastDestination" class="form-control" placeholder="Tìm nơi đến...">
                    </div>
                </div>
                <div class="row" id="past-flight-list">
                    <!-- Các chuyến bay đã bay sẽ được thêm động tại đây -->
                </div>
                <div id="past-pagination-container"></div>
            </div>
        </div>
    </div>
    <!-- Modal để tìm hoặc thêm khách hàng -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bookingModalLabel">Đặt Vé Chuyến Bay</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="step1">
          <!-- Bước 1: Tìm kiếm khách hàng -->
          <h5>Tìm kiếm khách hàng</h5>
          <input type="text" id="searchCustomerInput" class="form-control" placeholder="Nhập tên hoặc email khách hàng...">
          <ul id="searchResults" class="list-group mt-2"></ul>
          <button class="btn btn-success mt-3" onclick="goToStep2()">Khách hàng mới</button>
        </div>
        
        <div id="step2" style="display:none;">
          <!-- Bước 2: Thêm thông tin khách hàng mới -->
          <h5>Thêm khách hàng mới</h5>
          <input type="text" id="newCustomerName" class="form-control" placeholder="Tên khách hàng">
          <input type="text" id="newCustomerPhone" class="form-control mt-3" placeholder="Số điện thoại khách hàng">
          <button class="btn btn-primary mt-3" onclick="addNewCustomer()">Thêm Khách Hàng</button>
        </div>
      </div>
    </div>
  </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" />

    <script>
        let flightCode; 
        let customerId; 
        var flightsData = [];  // Dữ liệu chuyến bay
        var flightsPerPage = 12;  // Số chuyến bay trên mỗi trang

        // Hàm gọi API và xử lý dữ liệu với từ khóa tìm kiếm
        function fetchFlights() {
            $.ajax({
                url: 'http://localhost:3000/app/api/readFlightApp.php',
                method: 'GET',
                dataType: 'json',
                success: function(result) {
                    if (result.status === 200 && result.data.length > 0) {
                        flightsData = result.data;  // Lưu dữ liệu chuyến bay
                        applyFiltersAndPaginate();
                    } else {
                        console.error('Không có chuyến bay nào được tìm thấy.');
                    }
                },
                error: function(error) {
                    console.error('Lỗi khi lấy dữ liệu chuyến bay:', error);
                }
            });
        }

        // Phân loại chuyến bay
        function categorizeFlights() {
            const now = new Date();
            const upcomingFlights = flightsData.filter(flight => new Date(flight.ngayDi) > now);
            const pastFlights = flightsData.filter(flight => new Date(flight.ngayDi) <= now);

            return { upcomingFlights, pastFlights };
        }

// Hàm lọc và áp dụng phân trang
function applyFiltersAndPaginate() {
    const origin = $('#searchUpcomingOrigin').val();
    const destination = $('#searchUpcomingDestination').val();

    const originPast = $('#searchPastOrigin').val();
    const destinationPast = $('#searchPastDestination').val();
    const { upcomingFlights, pastFlights } = categorizeFlights();

    // Lọc chuyến bay sắp tới
    const filteredUpcomingFlights = upcomingFlights.filter(flight => {
        const originMatch = flight.diaDiemDi && flight.diaDiemDi.toLowerCase().includes(origin.toLowerCase());
        const destinationMatch = flight.diaDiemDen && flight.diaDiemDen.toLowerCase().includes(destination.toLowerCase());
        return originMatch && destinationMatch;
    });

    // Lọc chuyến bay đã qua
    const filteredPastFlights = pastFlights.filter(flight => {
        const originMatch = flight.diaDiemDi && flight.diaDiemDi.toLowerCase().includes(originPast.toLowerCase());
        const destinationMatch = flight.diaDiemDen && flight.diaDiemDen.toLowerCase().includes(destinationPast.toLowerCase());
        return originMatch && destinationMatch;
    });

    // Hiển thị chuyến bay sắp tới
    $('#upcoming-pagination-container').pagination({
        dataSource: filteredUpcomingFlights, // Đổi từ upcomingFlights sang filteredUpcomingFlights
        pageSize: flightsPerPage,
        callback: function(data, pagination) {
            displayFlights(data, '#upcoming-flight-list');
        }
    });

    // Hiển thị chuyến bay đã qua
    $('#past-pagination-container').pagination({
        dataSource: filteredPastFlights, // Đổi từ pastFlights sang filteredPastFlights
        pageSize: flightsPerPage,
        callback: function(data, pagination) {
            displayFlights(data, '#past-flight-list');
        }
    });

    // Kiểm tra và hiển thị thông báo nếu không có chuyến bay nào được tìm thấy
    if (filteredUpcomingFlights.length === 0) {
        $('#upcoming-flight-list').html('<li class="list-group-item text-warning">Không tìm thấy chuyến bay nào.</li>');
    }

    if (filteredPastFlights.length === 0) {
        $('#past-flight-list').html('<li class="list-group-item text-warning">Không tìm thấy chuyến bay nào.</li>');
    }
}


// Hàm hiển thị các chuyến bay
function displayFlights(flights, container) {
    const flightList = $(container);
    flightList.empty();  // Xóa nội dung cũ nếu có

    // Nếu không có kết quả, hiển thị thông báo
    if (flights.length === 0) {
        flightList.append('<div class="col-12"><p class="text-center">Không tìm thấy chuyến bay nào.</p></div>');
        return;
    }

    // Duyệt qua danh sách chuyến bay và tạo thẻ cho từng chuyến bay
    flights.forEach(flight => {
        // Kiểm tra xem chuyến bay đã hoàn thành hay chưa
        const ngayDen = new Date(flight.ngayDen); // Ngày đến của chuyến bay
        const now = new Date(); // Thời gian hiện tại

        // Kiểm tra nếu chuyến bay đã hoàn thành (ngày đến trước ngày hiện tại)
        let bookingButton = '';
        if (ngayDen > now) {
            // Nếu chuyến bay chưa bay, hiển thị nút "Đặt vé"
            bookingButton = `<button class="btn btn-primary" onclick="openBookingModal('${flight.maCB}')">Đặt Vé</button>`;
        } else {
            // Nếu chuyến bay đã hoàn thành, không hiển thị nút "Đặt vé"
            bookingButton = `<p class="text-danger">Chuyến bay đã hoàn thành.</p>`;
        }

        const flightCard = `
            <div class="col-md-4">
                <div class="card card-custom">
                    <div class="card-body text-center">
                        <h5 class="card-title">Chuyến Bay ${flight.maCB}</h5>
                        <p class="card-text">Điểm đi: ${flight.diaDiemDi}</p>
                        <p class="card-text">Điểm đến: ${flight.diaDiemDen}</p>
                        <p class="card-text">Ngày đi: ${flight.ngayDi}</p>
                        <p class="card-text">Ngày đến: ${flight.ngayDen}</p>
                        <p class="card-text">Giá vé: ${Number(flight.giaVe).toLocaleString()} VNĐ</p>
                        <p class="card-text">Số lượng vé còn: ${flight.soLuongCon}</p>
                        ${bookingButton}
                    </div>
                </div>
            </div>
        `;
        flightList.append(flightCard);
    });
}

        

// Gọi hàm để lấy và hiển thị chuyến bay sau khi trang tải xong
$(document).ready(function() {
    fetchFlights();

    // Bắt sự kiện khi người dùng nhập vào thanh tìm kiếm nơi đi cho chuyến bay sắp tới
    $('#searchUpcomingOrigin, #searchUpcomingDestination').on('input', function() {
        const origin = $('#searchUpcomingOrigin').val().trim();
        const destination = $('#searchUpcomingDestination').val().trim();
        applyFiltersAndPaginate(origin, destination);
    });

    // Bắt sự kiện khi người dùng nhập vào thanh tìm kiếm nơi đi cho chuyến bay đã qua
    $('#searchPastOrigin, #searchPastDestination').on('input', function() {
        const origin = $('#searchPastOrigin').val().trim();
        const destination = $('#searchPastDestination').val().trim();
        applyFiltersAndPaginate(origin, destination);
    });
});

        function openBookingModal(code) {
                // Lưu maCB vào modal
                flightCode = code;
                $('#bookingModal').modal('show');
                $('#step1').show();
                $('#step2').hide();
        }

        // Chuyển từ bước 1 sang bước 2 (thêm khách hàng mới)
        function goToStep2() {
            $('#step1').hide();
            $('#step2').show();
        }

// Tìm kiếm khách hàng
function searchCustomer(query) {
    // Kiểm tra xem query có rỗng không
    if (!query.trim()) {
        const resultsContainer = document.getElementById("searchResults");
        resultsContainer.innerHTML = ''; // Xóa danh sách hiện tại

        // Hiển thị thông báo yêu cầu nhập thông tin
        const noQueryItem = document.createElement('li');
        noQueryItem.className = 'list-group-item text-warning'; // Thêm lớp CSS để làm nổi bật
        noQueryItem.textContent = 'Vui lòng nhập thông tin cần tìm kiếm';
        resultsContainer.appendChild(noQueryItem); // Thêm thông báo vào <ul>
        return; // Dừng hàm nếu không có giá trị tìm kiếm
    }

    // Nếu query không rỗng, gửi yêu cầu
    fetch(`http://localhost:3000/app/api/searchCustomer.php?searchQuery=${query}`)
        .then(response => {
            return response.json();
        })
        .then(data => {
            console.log('Kết quả tìm kiếm khách hàng:', data);
            const resultsContainer = document.getElementById("searchResults");
            resultsContainer.innerHTML = ''; // Xóa danh sách hiện tại

            if (data.status === 200) {
                // Nếu tìm thấy khách hàng, hiển thị thông tin
                data.data.forEach(customer => {
                    const listItem = document.createElement('li');
                    listItem.className = 'list-group-item list-group-item-action'; // Thêm class để làm nổi bật khi di chuột qua

                    // Hiển thị thông tin khách hàng và thêm sự kiện click
                    listItem.textContent = `Tên: ${customer.fullname}, Email: ${customer.email}, Số điện thoại: ${customer.soDT}`;
                    listItem.addEventListener('click', function() {
                        // Chuyển hướng sang trang thanh toán, truyền ID khách hàng qua URL
                        window.location.href = `payment_shop.php?customerId=${customer.maKH}&flightCode=${flightCode}`;
                    });

                    resultsContainer.appendChild(listItem);
                });
            } else {
                // Nếu không tìm thấy khách hàng, hiển thị thông báo không tồn tại
                const noResultItem = document.createElement('li');
                noResultItem.className = 'list-group-item text-danger';
                noResultItem.textContent = 'Không tồn tại khách hàng bạn đang tìm. Vui lòng thêm khách hàng mới';
                resultsContainer.appendChild(noResultItem);
            }
        })
        .catch(error => {
            console.error('Lỗi khi tìm kiếm khách hàng:', error);
        });
}


document.getElementById('searchCustomerInput').addEventListener('input', function() {
    let query = this.value.trim();
    if (query.length > 0) {
        searchCustomer(query);
    }
});


// Chọn khách hàng từ danh sách tìm kiếm
function selectCustomer(customerId) {
    alert('Bạn đã chọn khách hàng ID: ' + customerId);
    // Sau khi chọn khách hàng, tiếp tục với bước đặt vé...
}

// Thêm khách hàng mới
function addNewCustomer() {
    var fullname = $('#newCustomerName').val();
    var soDT = $('#newCustomerPhone').val();

    $.ajax({
        url: 'http://localhost:3000/app/api/createCustomerShop.php',  // API thêm khách hàng
        method: 'POST',
        data: { fullname: fullname, soDT: soDT },
        success: function(response) {
            var data = response;  
            if (data.status === 201) {
                var customerId = data.customer_id;  // Lấy customer_id từ phản hồi
                if (customerId) {
                // Chuyển hướng đến trang thanh toán với mã khách hàng
                    window.location.href = `payment_shop.php?customerId=${customerId}&flightCode=${flightCode}`;
                }   
            } else {
                console.error('Thêm khách hàng không thành công:', data.message);
            }
        },
        error: function(error) {
            console.error('Lỗi khi thêm khách hàng mới:', error);
        }
    });
}


// Gọi tìm kiếm khách hàng khi người dùng nhập thông tin
$('#searchCustomerInput').on('input', function() {
    var query = $(this).val();
    searchCustomer(query);
});
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
