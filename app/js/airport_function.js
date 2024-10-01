var formulariop = document.getElementById('form');

formulariop.addEventListener('submit', function (e) {
    e.preventDefault();
    var datos = new FormData(document.getElementById('form'));
    var jsonObject = {};
    datos.forEach(function (value, key) {
        jsonObject[key] = value;
    });
    var jsonData = JSON.stringify(jsonObject);
    let maSanBay = datos.get('maSanBay');
    let tenSanBay = datos.get('tenSanBay');
    let diaDiem = datos.get('diaDiem');
    let message = document.querySelector("#message");
    message.innerHTML = "";
    if (tenSanBay == "") {
        let tipo_mensaje = "Chưa có thông tin về Tên sân bay";
        error(tipo_mensaje);
        return false;
    } else if (diaDiem == "") {
        let tipo_mensaje = "Chưa có thông tin về Địa điểm";
        error(tipo_mensaje);
        return false;
    }
    // Lưu trạng thái gốc của form
    var originalFormData = new FormData(document.getElementById('form'));
    var originalJsonObject = {};
    originalFormData.forEach(function (value, key) {
        originalJsonObject[key] = value;
    });
    var originalJsonData = JSON.stringify(originalJsonObject);
    var url = "http://localhost:3000/app/api/createAirport.php";
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
print_table(Response.data);

// function print_table(data) {
//     fetch('http://localhost:3000/app/api/readAirport.php')
//         .then((res) => res.json())
//         .then(Response => {
//             console.log(Response.data);
//             let output = '';
//             for (let i in Response.data) {
//                 output += `<tr class="airport-item-${Response.data[i].maSanBay}">
//                <td class="ma-san-bay-column">${Response.data[i].maSanBay}</td>
//                <td class="ten-san-bay-column">${Response.data[i].tenSanBay}</td>
//                <td class="dia-diem-column">${Response.data[i].diaDiem}</td>
//                <td>                           
//                    <button class='fix' onclick='edit(${Response.data[i].maSanBay});'><i class='ti-pencil-alt'></i></button>
//                    <button class='trash' onclick='delete_airport(${Response.data[i].maSanBay});'><i class='ti-trash'></i></button>
//                </td>
//            </tr>`;
//             }

//             document.querySelector('.tbody').innerHTML = output;

//         }).catch(error => console.log(error));


//     $(document).ready(function () {
//         $('.table').dataTable({
//             // "ajax": 'http://localhost:3000/app/api/readAirline.php'
//         });

//     });
// }
function print_table() {
    fetch('http://localhost:3000/app/api/readAirport.php')
        .then(response => response.json())
        .then(data => {
            // Đảm bảo rằng DataTable đã được hủy bỏ trước khi kích hoạt lại
            $('#myDataTable').DataTable().destroy();

            // Hiển thị dữ liệu trên DataTable
            $('#myDataTable').DataTable({
                data: data.data, // Giả sử API trả về một đối tượng với trường "data" chứa mảng dữ liệu
                columns: [
                    { data: 'maSanBay', title: 'Mã Sân Bay' },
                    { data: 'tenSanBay', title: 'Tên Sân Bay' },
                    { data: 'diaDiem', title: 'Địa Điểm' },
                    {
                        data: null,
                        title: 'Hành Động',
                        render: function (data) {
                            return `<button class='fix' onclick='edit(${data.maSanBay});'><i class='ti-pencil-alt'></i></button>
                                    <button class='trash' onclick='delete_airport(${data.maSanBay});'><i class='ti-trash'></i></button>`;
                        }
                    }
                ],
                destroy: true, // Đảm bảo hủy bỏ DataTable trước khi kích hoạt lại
            });
        })
        .catch(error => console.log(error));
}

// Gọi hàm print_table để hiển thị dữ liệu khi trang được tải
print_table();

function delete_airport(maSanBay) {
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
            var url = "http://localhost:3000/app/api/deleteAirport.php?maSanBay=" +maSanBay;
            fetch(url, {
                method: 'delete',
                headers: {
                    "Content-Type": "application/json",
                }
            }).then(response => response.text())
                .then(data => {
                    var airportItem = document.querySelector('.airport-item-' + data.maSanBay);
                    if (airportItem) {
                        airportItem.remove();
                    }
                    console.log('Success', Response)
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
function edit(maSanBay) {
    var url = "http://localhost:3000/app/api/readAirport.php?maSanBay=" + maSanBay;

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
                    title: 'Chỉnh sửa thông tin nhân viên',
                    html: `
              <form id="update_form">
                <input type="text" value="update_airport" name="form_name" hidden="true">
                <input type="text" id="maSanBay" value="${item.maSanBay}" name="maSanBay" hidden="true">
                <label style="float: left">Tên sân bay </label>
                <input type="text" id="tenSanBay" value="${item.tenSanBay}" name="tenSanBay" class="form-control" placeholder="Tên sân bay">
                <hr><label style="float: left">Địa điểm </label>
                <input type="text" id="diaDiem" value="${item.diaDiem}" name="diaDiem" class="form-control" placeholder="Địa điểm">
                <hr>             
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
                        var updateUrl = "http://localhost:3000/app/api/updateAirport.php?maSanBay=" + maSanBay;
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
