var formulariop = document.getElementById('form');

formulariop.addEventListener('submit', function (e) {
    e.preventDefault();
    var datos = new FormData(document.getElementById('form'));
    var jsonObject = {};
    datos.forEach(function (value, key) {
    jsonObject[key] = value;
});
var jsonData = JSON.stringify(jsonObject);
    let maMB = datos.get('maMB');
    let tenMayBay = datos.get('tenMayBay');
    let hangMayBay = datos.get('hangMayBay');
    let gheToiDa = datos.get('gheToiDa');
    let message = document.querySelector("#message");
    message.innerHTML = "";
    if (tenMayBay == "") {
        let tipo_mensaje = "Chưa có thông tin về Tên máy bay";
        error(tipo_mensaje);
        return false;
    } else if (hangMayBay == "") {
        let tipo_mensaje = "Chưa có thông tin về tên máy bay";
        error(tipo_mensaje);
        return false;
    } else if (gheToiDa == "") {
        let tipo_mensaje = "Chưa có thông tin về ghế tối đa";
        error(tipo_mensaje);
        return false;
    }

    var url = "http://localhost:3000/app/api/createVoucher.php";
    // Lưu trạng thái gốc của form
    var originalFormData = new FormData(document.getElementById('form'));
    var originalJsonObject = {};
    originalFormData.forEach(function (value, key) {
        originalJsonObject[key] = value;
    });
    var originalJsonData = JSON.stringify(originalJsonObject);
    fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
             //'Content-Type': 'application/x-www-form-urlencoded',
          },
        body: jsonData,
    }).then((res) => res.json())
    .then(response => {
        console.log('Response:', response);
        if (response.status === 201) {
            // Nếu thành công, reset form và hiển thị thông báo
            formulariop.reset();
            Swal.fire(
                'Đã thêm',
                'Bạn đã thêm thành công.',
                'success'
            );
        } else if (response.status === 400) {
            // Nếu trùng lặp, phục hồi trạng thái gốc của form và hiển thị thông báo lỗi
            restoreOriginalState(originalJsonData);
            Swal.fire(
                'Lỗi',
                response.message,
                'error'
            );
        } else {
            // Xử lý các trường hợp lỗi khác nếu cần
            console.error('Error:', response);
        }
    })
        .catch(error => console.error('Error:', error));
        function restoreOriginalState(originalData) {
            // Phục hồi trạng thái gốc của form
            var form = document.getElementById('form');
            var originalJsonObject = JSON.parse(originalData);
            for (var key in originalJsonObject) {
                if (Object.prototype.hasOwnProperty.call(originalJsonObject, key)) {
                    var value = originalJsonObject[key];
                    form.elements[key].value = value;
                }
            }
        }
});


const error = (tipo_mensaje) => {
    message.innerHTML += `
    <div class="row">
        <div class="col-md-5 offset-md-3">
            <div class="alert alert-danger" role="alert" style="height: 100%;">
                <h4 class="alert-heading">Lỗi!</h4>
                <p> *${tipo_mensaje}</p> 
            </div>
        </div>

    </div>

    `;
}
print_table();
deleteExpiredVouchers()

function print_table() {
    fetch('http://localhost:3000/app/api/readVoucher.php')
        .then(response => response.json())
        .then(data => {
            // Đảm bảo rằng DataTable đã được hủy bỏ trước khi kích hoạt lại
            $('#myDataTable').DataTable().destroy();

            // Hiển thị dữ liệu trên DataTable
            $('#myDataTable').DataTable({
                data: data.data, // Giả sử API trả về một đối tượng với trường "data" chứa mảng dữ liệu
                columns: [
                    { data: 'code', title: 'Code' },
                    { data: 'discount', title: 'Khuyến mãi' },
                    { data: 'ngayHetHan', title: 'Ngày hết hạn' },
                    { data: 'ngayTao', title: 'Ngày tạo' },
                    { data: 'trangThai', title: 'Dành cho' }
                ],
                destroy: true, // Đảm bảo hủy bỏ DataTable trước khi kích hoạt lại
            });
        })
        .catch(error => console.log(error));
}

function deleteExpiredVouchers() {
    Swal.fire({
        title: 'Bạn có chắc chắn muốn xóa tất cả các voucher hết hạn?',
        text: "Bạn sẽ không thể khôi phục dữ liệu sau khi xóa",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Tôi đồng ý'
    }).then((result) => {
        if (result.isConfirmed) {
            var url = "http://localhost:3000/app/api/deleteVoucher.php"; // URL đến file PHP xử lý xóa voucher hết hạn

            fetch(url, {
                method: 'DELETE',
                headers: {
                    "Content-Type": "application/json",
                }
            })
            .then(response => response.text()) // Chuyển đổi phản hồi sang JSON
            .then(data => {
                // Kiểm tra xem dữ liệu có hợp lệ không
                if (data.status === 204) {
                    console.log('Success');
                    print_table();  // Cập nhật lại bảng nếu cần
                    Swal.fire(
                        'Đã xóa',
                        'Tất cả các voucher hết hạn đã được xóa thành công.',
                        'success'
                    );
                }
            })
            .catch(
                error => console.error('Error:', error)
            );
        }
    });
}
