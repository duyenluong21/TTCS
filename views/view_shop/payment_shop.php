<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-custom {
            margin-bottom: 20px;
        }
        .btn-custom {
            margin: 10px 0;
        }
        .total-amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
        <!-- Header -->
    <header class="header">
    <?php include("header_shop.php"); ?>
    </header>
    <div class="container">
        <h1 class="text-center my-5">Thông Tin Thanh Toán</h1>
        
        <div class="row">
            <!-- Thẻ thông tin chuyến bay đã chọn -->
            <div class="col-md-4 d-flex flex-fill">
                <div class="card card-custom flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin chuyến bay</h5>
                        <div id="flight-details">
                            <!-- Thông tin chuyến bay sẽ được chèn ở đây -->
                        </div>
                        
                        <!-- Chọn loại vé -->
                        <div class="mt-4">
                            <label for="ticketType"><strong>Chọn loại vé:</strong></label>
                            <select id="ticketType" class="form-select" onchange="updateTotalAmount()">
                               
                            </select>
                        </div>
                        <div class="mt-4">
                        <label for="ticket-quantity">Số lượng vé:</label>
                        <input type="number" id="ticket-quantity" min="1" value="1" onchange="updateTotalAmount()">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thẻ thông tin khách hàng đã chọn -->
            <div class="col-md-4 d-flex flex-fill">
            <div class="card card-custom flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin khách hàng</h5>
                        <div id="passenger-details">
                            <!-- Thông tin khách hàng sẽ được chèn ở đây -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thẻ thông tin số tiền cần thanh toán -->
            <div class="col-md-4 d-flex flex-fill">
            <div class="card card-custom flex-fill">
                    <div class="card-body text-center">
                        <h5 class="card-title">Số tiền thanh toán</h5>
                        <p class="total-amount" id="total-amount">0 VNĐ</p>
                        <button class="btn btn-success btn-block btn-custom" onclick="payByCash()">Thanh Toán Tiền Mặt</button>
                        <button class="btn btn-info btn-block btn-custom" onclick="payByQRCode()">Thanh Toán Quét Mã</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
    // Hàm để lấy tham số từ URL
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    let ticketTypeDropdown; // Declare as global variable
    let flightData; // Declare for storing flight data

    async function fetchData() {
    const customerId = getQueryParam('customerId');
    const flightCode = getQueryParam('flightCode');

    try {
        const flightResponse = await fetch(`http://localhost:3000/app/api/readFlightApp.php?maCB=${flightCode}`);
        flightData = await flightResponse.json();

        if (flightData.status !== 200) {
            document.getElementById('flight-details').innerHTML = `<p class="text-danger">Không tìm thấy thông tin chuyến bay.</p>`;
        } else {
            document.getElementById('flight-details').innerHTML = `
                <p><strong>Mã chuyến bay:</strong> ${flightData.data.maCB}</p>
                <p><strong>Điểm đi:</strong> ${flightData.data.diaDiemDi}</p>
                <p><strong>Điểm đến:</strong> ${flightData.data.diaDiemDen}</p>
                <p><strong>Ngày đi:</strong> ${flightData.data.ngayDi}</p>
                <p><strong>Ngày đến:</strong> ${flightData.data.ngayDen}</p>
                <p><strong>Giá vé:</strong> ${flightData.data.giaVe} VNĐ</p>
            `;

            // Chuyển đổi dữ liệu từ chuỗi sang mảng
            const hangVeArray = flightData.data.hangVe.split(', '); // Chia hạng vé thành mảng
            const maVeArray = flightData.data.maVe.split(', '); // Chia mã vé thành mảng
            const soLuongConArray = flightData.data.soLuongCon.split(', '); // Chia số lượng còn thành mảng

            ticketTypeDropdown = document.getElementById('ticketType'); 
            ticketTypeDropdown.innerHTML = ''; // Xóa tất cả tùy chọn hiện tại

            hangVeArray.forEach((hangVeItem, index) => {
                const option = document.createElement('option');
                option.value = maVeArray[index]; // Lưu mã vé tương ứng
                option.text = `${hangVeItem} (Còn ${soLuongConArray[index]} vé)`; 
                option.dataset.seats = soLuongConArray[index]; // Lưu số lượng còn
                option.dataset.class = hangVeItem; // Lưu hạng vé
                ticketTypeDropdown.appendChild(option); // Thêm tùy chọn vào dropdown
            });

            // Thêm sự kiện lắng nghe sau khi dropdown đã được khởi tạo
            ticketTypeDropdown.addEventListener('change', updateTotalAmount);
        }

        // Kiểm tra thông tin khách hàng
        const passengerResponse = await fetch(`http://localhost:3000/app/api/readPassenger.php?maKH=${customerId}`);
        const passengerData = await passengerResponse.json();

        if (passengerData.status !== 200) {
            document.getElementById('passenger-details').innerHTML = `<p class="text-danger">Không tìm thấy thông tin khách hàng.</p>`;
        } else {
            document.getElementById('passenger-details').innerHTML = `
                <p><strong>Họ tên:</strong> ${passengerData.data.fullname}</p>
                <p><strong>Email:</strong> ${passengerData.data.email}</p>
                <p><strong>Số điện thoại:</strong> ${passengerData.data.soDT}</p>
                <p><strong>Địa chỉ:</strong> ${passengerData.data.diaChi}</p>
            `;
        }

        // Cập nhật số tiền thanh toán
        document.getElementById('total-amount').innerText = `${flightData.data.giaVe} VNĐ`;

    } catch (error) {
        console.error('Error fetching data:', error);
        alert('Có lỗi xảy ra khi lấy thông tin.');
    }
}

    // Gọi hàm fetchData khi trang được tải
    window.onload = fetchData;

    function payByCash() {
        processPayment('cash');
    }

    function payByQRCode() {
        processPayment('qr');
    }

    async function processPayment(paymentMethod) {
    const ticketQuantity = document.getElementById('ticket-quantity').value; // Số lượng vé
    const maCB = getQueryParam('flightCode'); // Lấy mã chuyến bay từ URL
    const maVe = ticketTypeDropdown.options[ticketTypeDropdown.selectedIndex].value; // Lấy mã vé tương ứng từ dropdown
    const maKH = getQueryParam('customerId'); // Lấy mã khách hàng từ URL
    const orderId = generateOrderId(); // Tạo ID đơn hàng mới
    const totalAmount = calculateTotalAmount(); // Tính tổng tiền
    const maNVshop = localStorage.getItem('maNVshop');

    // Cập nhật số lượng vé
    const updateResult = await updateNumberOfTickets(maCB, maVe, ticketQuantity);

    // Gửi thông tin đặt vé
    const detailInput = {
        order_id: orderId,
        maVe: maVe,
        maCB: maCB, // Sử dụng maCB đã lấy từ flightCode
        maKH: maKH, // Sử dụng maKH đã lấy từ customerId
        soLuongDat: ticketQuantity,
        tongThanhToan: totalAmount,
        nguonDat: "Cửa hàng",
        maShop: maNVshop // Nếu không có mã cửa hàng
    };
    console.log(detailInput);

    try {
    const response = await fetch('http://localhost:3000/app/api/createDetailTicket.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(detailInput)
    });

    if (!response.ok) {
        const errorData = await response.json();
        console.error('Error during payment processing:', errorData);
        return;
    }

    const responseData = await response.json();
    console.log('Success:', responseData);
    // Lưu thông tin vào localStorage sau khi thanh toán thành công
    localStorage.setItem('paymentSuccessData', JSON.stringify(detailInput));

        // Điều hướng sang trang "Thanh toán thành công"
        window.location.href = 'payment_success.php';
} catch (error) {
    console.error('Fetch error:', error);
}
}
async function updateNumberOfTickets(maCB, maVe, ticketQuantity) {
    const selectedOption = ticketTypeDropdown.options[ticketTypeDropdown.selectedIndex];
    const currentAvailableSeats = parseInt(selectedOption.dataset.seats); // Lấy số lượng vé còn từ dropdown

    // Tính số lượng vé còn lại
    const updatedSeats = currentAvailableSeats - ticketQuantity;

    // Tạo URL chỉ chứa maCB và maVe
    const url = `http://localhost:3000/app/api/updateNumberOfTickets.php?maCB=${maCB}&maVe=${maVe}`;

    console.log('Updating ticket params:', { maCB, maVe, updatedSeats }); // Kiểm tra thông tin trước khi gửi

    try {
        // Gửi request với method PUT và gửi số lượng còn lại trong body
        const response = await fetch(url, {
            method: 'PUT', // Phương thức PUT để cập nhật dữ liệu
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                soLuongCon: updatedSeats.toString() // Chuyển số lượng còn lại thành chuỗi
            })
        });

        const result = await response.json();
        if (result.status === 200) {
            console.log(result.message); // Cập nhật thành công
        } else {
            alert(result.message); // Hiển thị thông báo lỗi
        }
    } catch (error) {
        console.error('Error updating ticket quantity:', error);
        alert('Có lỗi xảy ra khi cập nhật số lượng vé.');
    }
}




function generateOrderId() {
    const now = new Date();

    // Lấy các phần của ngày tháng năm và giờ phút giây
    const year = now.getFullYear();
    const month = (now.getMonth() + 1).toString().padStart(2, '0'); // Tháng 0-11, cần +1
    const day = now.getDate().toString().padStart(2, '0');
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    
    // Tạo chuỗi thời gian theo định dạng "YYYY-MM-DD HH:MM:SS"
    const formattedDateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    
    // Ghép chuỗi để tạo orderId
    const orderId = `${formattedDateTime}`;
    
    return orderId;
}

    function calculateTotalAmount() {
        // Tính tổng tiền dựa trên loại vé và số lượng
        const selectedOption = ticketTypeDropdown.options[ticketTypeDropdown.selectedIndex];
        const selectedTicketType = selectedOption.dataset.class; // Lấy loại vé đã chọn
        const basePrice = flightData.data.giaVe; // Lấy giá vé cơ bản từ flightData
        let totalPrice = basePrice;

        // Tính toán tổng tiền dựa trên loại vé
        if (selectedTicketType === 'Thương gia') {
            totalPrice = basePrice * 1.5; // 50% giá hơn cho hạng thương gia
        } else if (selectedTicketType === 'Cao cấp') {
            totalPrice = basePrice * 2; // Giá gấp đôi cho hạng cao cấp
        } else if (selectedTicketType === 'Phổ thông') {
            totalPrice = basePrice; // Không thay đổi cho hạng phổ thông
        }

        const ticketQuantity = parseInt(document.getElementById('ticket-quantity').value, 10) || 1; // Lấy số lượng vé đã chọn
        return totalPrice * ticketQuantity; // Trả về tổng tiền
    }

    function updateTotalAmount() {
        const totalAmount = calculateTotalAmount();
        document.getElementById('total-amount').innerText = `${totalAmount} VNĐ`;
    }
</script>


</body>
</html>
