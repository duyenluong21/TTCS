var formulariop = document.getElementById('form');

formulariop.addEventListener('submit', function (e) {
    e.preventDefault();
    var datos = new FormData(document.getElementById('form'));
    var jsonObject = {};
    datos.forEach(function (value, key) {
        jsonObject[key] = value;
    });
    var jsonData = JSON.stringify(jsonObject);
    let maVe = datos.get('maVe');
    let soLuong = datos.get('soLuong');
    let hangVe = datos.get('hangVe');
    // let giaHangVe = datos.get('giaHangVe');
    let message = document.querySelector("#message");
    message.innerHTML = "";
     if (soLuong == "") {
        let tipo_mensaje = "Chưa có thông tin về số lượng";
        error(tipo_mensaje);
        return false;
    // } else if (soLuongCon == "") {
    //     let tipo_mensaje = "Chưa có thông tin về số lượng còn";
    //     error(tipo_mensaje);
    //     return false;
    } else if (hangVe == "") {
        let tipo_mensaje = "Chưa có thông tin về hạng vé ";
        error(tipo_mensaje);
        return false;
     } else if (maVe == "") {
      let tipo_mensaje = "Chưa có thông tin về mã vé ";
      error(tipo_mensaje);
    return false;
    }

    var url = "http://localhost:3000/app/api/createTicket.php";
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
            "Content-Type": "application/json"
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
print_table();
function print_table() {
    fetch('http://localhost:3000/app/api/readTicket.php')
    .then(response => response.json())
        .then(data => {
            // Đảm bảo rằng DataTable đã được hủy bỏ trước khi kích hoạt lại
            $('#myDataTable').DataTable().destroy();

            // Hiển thị dữ liệu trên DataTable
            $('#myDataTable').DataTable({
                data: data.data, // Giả sử API trả về một đối tượng với trường "data" chứa mảng dữ liệu
                columns: [
                    { data: 'maVe', title: 'Mã Vé' },
                    { data: 'soLuong', title: 'Số lượng' },
                    { data: 'hangVe', title: 'Hạng vé' },
                    {
                        data: null,
                        title: 'Hành Động',
                        render: function (data) {
                            return `<button class='fix' onclick='edit(${data.maVe});'><i class='ti-pencil-alt'></i></button>
                                    <button class='trash' onclick='delete_ticket(${data.maVe});'><i class='ti-trash'></i></button>`;
                        }
                    }
                ],
                destroy: true, // Đảm bảo hủy bỏ DataTable trước khi kích hoạt lại
            });
        })
        .catch(error => console.log(error));
        
}
function delete_ticket(maVe) {
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
            var url = "http://localhost:3000/app/api/deleteTicket.php?maVe=";
            fetch(url + maVe, {
                method: 'delete',
                headers: {
                    "Content-Type": "application/json",
                    // 'Content-Type': 'application/x-www-form-urlencoded',
                }
            }).then(response => response.text())
                .then(data => {
                    var ticketItem = document.querySelector('.ticket-item-' + data.maVe);
                    if (ticketItem) {
                        ticketItem.remove();
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
function edit(maVe) {

    var url = "http://localhost:3000/app/api/readTicket.php?maVe=" + maVe;
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
                    title: 'Chỉnh sửa thông tin vé',
                    html: `
            <form id="update_form">
                <input type="text" value="update_airline" name="form_name" hidden="true">
                <input type="text" value="${item.maVe}"   name="maVe" hidden="true">
                <label style = "float : left">Mã vé </label>
                <input type="text" value="${item.maVe}"   name="maVe" class="form-control" placeholder="Mã vé">
                <hr>
                <label style = "float : left">Số lượng vé </label>
                <input type="text" value="${item.soLuong}"   name="soLuong" class="form-control" placeholder="Số lượng vé">
                
                <hr>
                <label style = "float : left">Hạng vé </label>     
                <input type="text" value="${item.hangVe}"   name="hangVe" class="form-control" placeholder="Hạng vé">     
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
                        var updateUrl = "http://localhost:3000/app/api/updateTicket.php?maVe=" + maVe;
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