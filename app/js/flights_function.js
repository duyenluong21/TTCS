var formulariop = document.getElementById('form');

formulariop.addEventListener('submit', function (e) {
    e.preventDefault();
    var datos = new FormData(document.getElementById('form'));
    var jsonObject = {};
    datos.forEach(function (value, key) {
        jsonObject[key] = value;
    });
    var jsonData = JSON.stringify(jsonObject);
    let maCB = datos.get('maCB');
    let gioBay = datos.get('gioBay');
    let maMB = datos.get('maMB');
    let giaVe = datos.get('giaVe');
    let diaDiemDi = datos.get('diaDiemDi');
    let diaDiemDen = datos.get('diaDiemDen');
    let ngayDi = datos.get('ngayDi');
    let ngayDen = datos.get('ngayDen');
    let message = document.querySelector("#message");
    message.innerHTML = "";
    if (diaDiemDi == "") {
        let tipo_mensaje = "Chưa có thông tin về Địa điểm đi";
        error(tipo_mensaje);
        return false;
    } else if (diaDiemDen == "") {
        let tipo_mensaje = "Chưa có thông tin về Địa điểm đến";
        error(tipo_mensaje);
        return false;
    } else if (ngayDi == "") {
        let tipo_mensaje = "Chưa có thông tin về Ngày đi";
        error(tipo_mensaje);
        return false;
    } else if (ngayDen == "") {
        let tipo_mensaje = "Chưa có thông tin về Ngày đến";
        error(tipo_mensaje);
        return false;
    } else if (giaVe == "") {
        let tipo_mensaje = "Chưa có thông tin về giá Vé";
        error(tipo_mensaje);
        return false;
    } else if (gioBay == "") {
        let tipo_mensaje = "Chưa có thông tin về giờ bay";
        error(tipo_mensaje);
        return false;
    }

    var url = "http://localhost:3000/app/api/createFlight.php";

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
    },
    body: jsonData,
})
.then((res) => res.json())
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
function print_table() {
    fetch('http://localhost:3000/app/api/readFlights.php')
        .then((response) => response.json())
        .then(data => {
            // Đảm bảo rằng DataTable đã được hủy bỏ trước khi kích hoạt lại
            $('#myDataTable').DataTable().destroy();

            // Hiển thị dữ liệu trên DataTable
            $('#myDataTable').DataTable({
                data: data.data, // Giả sử API trả về một đối tượng với trường "data" chứa mảng dữ liệu
                columns: [
                    { data: 'maCB', title: 'Mã chuyến bay' },
                    { data: 'ngayDen', title: 'Ngày đến' },
                    { data: 'ngayDi', title: 'Ngày đi' },
                    { data: 'diaDiemDen', title: 'Địa Điểm đến' },
                    { data: 'diaDiemDi', title: 'Địa điểm đi' },
                    { data: 'giaVe', title: 'Giá vé' },
                    { data: 'ghiChu', title: 'Ghi chú' },
                    { data: 'gioBay', title: 'Giờ bay' },
                    {
                        data: null,
                        title: 'Hành Động',
                        render: function (data) {
                            return `<button class='fix' onclick='edit(${data.maCB});'><i class='ti-pencil-alt'></i></button>
                                    <button class='trash' onclick='delete_flights(${data.maCB});'><i class='ti-trash'></i></button>`;
                        }
                    }
                ],
                destroy: true, // Đảm bảo hủy bỏ DataTable trước khi kích hoạt lại
            });
        })
        .catch(error => console.log(error));
        
}
function delete_flights(maCB) {
    Swal.fire({
        title: 'Bạn đã chắc chắn chưa?',
        text: "Bạn sẽ không còn dữ liệu sau khi xóa",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Tôi đồng ý'
    }).then((result) => {
        if (result.isConfirmed) {
            var url = "http://localhost:3000/app/api/deleteFlight.php?maCB="  + maCB;
            // var formdata = new FormData();
            // formdata.append('form_name', 'delete_airline');
            // formdata.append('maMB', maMB);
            fetch(url, {
                method: 'delete',
                headers: {
                    "Content-Type": "application/json",
                    // 'Content-Type': 'application/x-www-form-urlencoded',
                }
            }).then(response => response.text())
                .then(data => {
                    var flightItem = document.querySelector('.flight-item-' + data.maCB);
                    if (flightItem) {
                        flightItem.remove();
                    }
                    console.log('Success')
                    print_table(data);
                    Swal.fire(
                        'Đã xóa',
                        'Bạn đã xóa thành công.',
                        'success'
                    )
                })
                .catch(error => console.error('Error:', error));

        }
    })
}
function edit(maCB) {

    var url = "http://localhost:3000/app/api/readFlights.php?maCB=" + maCB;
    fetch(url, {
        method: 'GET',
        headers: {
            "Content-Type": "multipart/form-data",
        }
    })
        .then(response => response.json())
        .then(data => {
            console.log('success', data);

            // Kiểm tra xem data có phải là một đối tượng không
            if (typeof data.data === 'object') {
                var item = data.data; // Lấy đối tượng từ data

                // Hiển thị form Swal.fire cho đối tượng này            
                Swal.fire({
                    title: 'Chỉnh sửa thông tin chuyến bay',
                    html: `
                <form id="update_form">
                    <input type="text" value="update_flights" name="form_name" hidden="true">
                    <input type="text" value="${item.maCB}"   name="maCB" hidden="true">
                    <label style = "float : left">Địa điểm đến </label>
                    <input type="text" value="${item.diaDiemDen}"   name="diaDiemDen" class="form-control" placeholder="Địa điểm đến">
                   
                    <hr>
                    <label style = "float : left">Địa điểm đi </label>
                    <input type="text" value="${item.diaDiemDi}"   name="diaDiemDi" class="form-control" placeholder="Địa điểm đi">
                    <hr> 
                    <label style = "float : left">Ngày đi </label>
                    <input type="date" value="${item.ngayDi}"   name="ngayDi" class="form-control" placeholder="Ngày khởi hành">
                    <hr>
                    <label style = "float : left">Ngày đến</label>
                    <input type="date" value="${item.ngayDen}"   name="ngayDen" class="form-control" placeholder="Ngày đến">
                    
                    <hr>
                    <label style = "float : left">Giá vé </label>
                    <input type="text" value="${item.giaVe}"   name="giaVe" class="form-control" placeholder="Giá vé">
                    <hr>
                    <label style = "float : left">Ghi chú </label>
                    <input type="text" value="${item.ghiChu}"   name="ghiChu" class="form-control" placeholder="Ghi chú">  
                    <hr>
                    <label style = "float : left">Giờ bay</label>
                    <input type="type" value="${item.gioBay}"   name="gioBay" class="form-control" placeholder="Giờ Bay">    
                </form>  
                
                `,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Save'
                }).then((result) => {
                    if (result.value) {

                        const formData = new FormData(document.querySelector("#update_form"));
                        const datos_actualizar = {};
                        formData.forEach((value, key) => {
                            datos_actualizar[key] = value;
                        });
                        console.log('Data to be sent:', datos_actualizar);
                        var updateUrl = "http://localhost:3000/app/api/updateFlights.php?maCB=" + maCB;
                        fetch(updateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams(datos_actualizar).toString(),
                        })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Success:', data);
                                print_table(data);
                                Swal.fire(
                                    'Đã sửa',
                                    'Chỉnh sửa thành công',
                                    'success'
                                )
                            })
                            .catch(function (error) {
                                console.error('Error:', error)
                            });
                    }
                });
            } else {
                console.error('Invalid data format. Expected an object.');
            }
        })
        .catch(function (error) {
            console.error('Error:', error);
        });

}