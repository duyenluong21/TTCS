var formulariop = document.getElementById('form');

formulariop.addEventListener('submit', function (e) {
    e.preventDefault();
    var datos = new FormData(document.getElementById('form'));
    var jsonObject = {};
    datos.forEach(function (value, key) {
        jsonObject[key] = value;
    });
    var jsonData = JSON.stringify(jsonObject);
    var datos = new FormData(document.getElementById('form'));
    let maNV = datos.get('maNV');
    let hoNV = datos.get('tenNV');
    let tenNV = datos.get('tenNV');
    let ngaySinhNV = datos.get('ngaySinhNV');
    let kinhNghiem = datos.get('kinhNghiem');
    let trinhDoHocVan = datos.get('trinhDoHocVan');
    let chucVu = datos.get('chucVu');
    let username = datos.get('username');
    let passw = datos.get('passw');
    let message = document.querySelector("#message");
    message.innerHTML = "";
    if (tenNV == "") {
        let tipo_mensaje = "Chưa có thông tin về Tên";
        error(tipo_mensaje);
        return false;
    } else if (ngaySinhNV == "") {
        let tipo_mensaje = "Chưa có thông tin về Ngày sinh";
        error(tipo_mensaje);
        return false;
    } else if (hoNV == "") {
        let tipo_mensaje = "Chưa có thông tin về Họ ";
        error(tipo_mensaje);
        return false;
    } else if (kinhNghiem == "") {
        let tipo_mensaje = "Chưa có thông tin về Kinh nghiệm";
        error(tipo_mensaje);
        return false;
    } else if (chucVu == "") {
        let tipo_mensaje = "Chưa có thông tin về Chức vụ";
        error(tipo_mensaje);
        return false;
    } else if (trinhDoHocVan == "") {
        let tipo_mensaje = "Hãy điền đầy đủ vào về Trình độ học vấn";
        error(tipo_mensaje);
        return false;
    } else if (username == "") {
        let tipo_mensaje = "Hãy điền đầy đủ vào về Tài khoản";
        error(tipo_mensaje);
        return false;
    } else if (passw == "") {
        let tipo_mensaje = "Hãy điền đầy đủ vào về Mật khẩu";
        error(tipo_mensaje);
        return false;
    }
    var url = "http://localhost:3000/app/api/createUser.php";
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
                <P> *${tipo_mensaje}</P>
            </div>
        </div>

    </div>

    `;
}
print_table(Response.data);
function print_table() {
    fetch('http://localhost:3000/app/api/readUser.php')
        .then((res) => res.json())
        .then(data => {
            // Đảm bảo rằng DataTable đã được hủy bỏ trước khi kích hoạt lại
            $('#myDataTable').DataTable().destroy();

            // Hiển thị dữ liệu trên DataTable
            $('#myDataTable').DataTable({
                data: data.data, // Giả sử API trả về một đối tượng với trường "data" chứa mảng dữ liệu
                columns: [
                    { data: 'maNV', title: 'Mã nhân viên' },
                    { data: 'hoNV', title: 'Họ' },
                    { data: 'tenNV', title: 'Tên' },
                    { data: 'chucVu', title: 'Chức vụ' },
                    { data: 'kinhNghiem', title: 'Kinh nghiệm' },
                    { data: 'trinhDoHocVan', title: 'Học vấn' },
                    // { data: 'username', title: 'Tài khoản' },
                    // { data: 'passw', title: 'Mật khẩu' },
                    {
                        data: null,
                        title: 'Hành Động',
                        render: function (data) {
                            return `<button class='fix' onclick='edit(${data.maNV});'><i class='ti-pencil-alt'></i></button>
                                    <button class='trash' onclick='delete_user(${data.maNV});'><i class='ti-trash'></i></button>`;
                        }
                    }
                ],
                destroy: true, // Đảm bảo hủy bỏ DataTable trước khi kích hoạt lại
            });
        })
        .catch(error => console.log(error));
}
function delete_user(maNV) {
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
            var url = "http://localhost:3000/app/api/deleteUser.php?maNV="  + maNV;
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
                    var userItem = document.querySelector('.user-item-' + data.maNV);
                    if (userItem) {
                        userItem.remove();
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
function edit(maNV) {
    var url = "http://localhost:3000/app/api/readUser.php?maNV=" + maNV;

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
                    <input type="text" value="update_user" name="form_name" hidden="true">
                    <input type="text" value="${item.maNV}"   name="maNV" hidden="true">
    
                    <label style = "float : left">Họ </label>
                    <input type="text" value="${item.hoNV}"   name="hoNV" class="form-control" placeholder="Họ nhân viên">
                    
                    <hr><label style = "float : left">Tên </label>
                    <input type="text" value="${item.tenNV}"   name="tenNV" class="form-control" placeholder="Tên nhân viên">
                    
                    <hr><label style = "float : left">Chức Vụ </label>                
                    <input type="text" value="${item.chucVu}"   name="chucVu" class="form-control" placeholder="Chức vụ">
                    
                    <hr><label style = "float : left">Kinh nghiệm </label>
                    <input type="text" value="${item.kinhNghiem}" name="kinhNghiem"  class="form-control" placeholder="Kinh nghiệm">
                    
                    
                    <hr><label style = "float : left">Trình độ học vấn</label>
                    <input type="text" value="${item.trinhDoHocVan}"   name="trinhDoHocVan" class="form-control" placeholder="Học vấn">
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
                        var updateUrl = "http://localhost:3000/app/api/updateUser.php?maNV=" + maNV;
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