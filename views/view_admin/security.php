<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    <title>Security</title>
    <style>
        .error {
            color: red;
            display: block;
            margin-top: 5px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
    <!-- JQuery và DataTable -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <!-- Firebase -->
    <script src="https://www.gstatic.com/firebasejs/10.14.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.14.0/firebase-database.js"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
</head>

<body>
    
    <!--category-left -->
    <?php include("../TTCS/views/view_admin/category_left.php"); ?>
    <!--category-right -->
    <div class="category-right">
        <?php include("../TTCS/views/view_admin/header.php"); ?>

        <div id="main-airport">
            <div class="container-right-airport">
                <div class="airport-right">
                    <h3>Thêm thông báo</h3>
                    <form id="form">
                        <input type="text" name="form_name" value="send_value" hidden>
                        <input type="text" id="maSanBay" name="maSanBay" hidden>
                        <div class="input-airport">
                            <label for="">Thông báo</label>
                            <input type="text" id="thongBao" name="thongBao" placeholder="">
                            <span class="error" id="thongBaoError"></span>
                        </div>
                        <div class="input-airport">
                            <label for="">Ngày tạo</label>
                            <input type="datetime-local" id="ngayTao" name="ngayTao" placeholder="">
                            <span class="error" id="ngayTaoError"></span>
                        </div>
                        <div class="btn-airport">
                            <button type="submit" id="save" class="save">Lưu</button>
                            <button class="exit">Thoát</button>
                        </div>
                    </form>
                </div>

                <div class="airport-left">
                    <table class="table table-striped" id="myDataTable">
                        <thead class="title">
                            <tr>
                                <th scope="col">Thông báo</th>
                                <th scope="col">Ngày tạo</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                        </tbody>
                    </table>
                </div>
                <div class="error_notice" id="message"></div>
            </div>
        </div>
    </div>

    <script type="module">
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.14.0/firebase-app.js";
        import { getDatabase, ref, push, onValue, remove } from "https://www.gstatic.com/firebasejs/10.14.0/firebase-database.js";

        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyCc80A_SdAfkoVioULDDinlSUFJtjAlDAA",
            authDomain: "bookingflight-76b84.firebaseapp.com",
            databaseURL: "https://bookingflight-76b84-default-rtdb.firebaseio.com",
            projectId: "bookingflight-76b84",
            storageBucket: "bookingflight-76b84.appspot.com",
            messagingSenderId: "360606142971",
            appId: "1:360606142971:web:08e335025bfc66d7b2fb5e",
            measurementId: "G-NFJM33SXQR"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const database = getDatabase(app);

        // Xử lý form khi submit
        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault(); // Ngăn hành động mặc định của form

            // Lấy giá trị từ các trường input
            const thongBao = document.getElementById('thongBao').value;
            const ngayTao = document.getElementById('ngayTao').value;

            // Kiểm tra lỗi
            let hasError = false;

            if (!thongBao) {
                document.getElementById('thongBaoError').innerText = 'Vui lòng nhập thông báo';
                hasError = true;
            } else {
                document.getElementById('thongBaoError').innerText = '';
            }

            if (!ngayTao) {
                document.getElementById('ngayTaoError').innerText = 'Vui lòng chọn ngày tạo';
                hasError = true;
            } else {
                document.getElementById('ngayTaoError').innerText = '';
            }

            // Nếu không có lỗi, lưu vào Firebase
            if (!hasError) {
                const newNotification = {
                    thongBao: thongBao,
                    ngayTao: ngayTao,
                };

                // Ghi dữ liệu vào Firebase
                push(ref(database, 'thongBao'), newNotification).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: 'Lưu thông báo thành công.'
                    });

                    // Xóa nội dung trong form
                    document.getElementById('thongBao').value = '';
                    document.getElementById('ngayTao').value = '';
                }).catch((error) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Lưu thông báo không thành công.'
                    });
                });
            }
        });

        // Lấy bảng DataTable
        let dataTable = $('#myDataTable').DataTable();

        // Lắng nghe thay đổi từ Firebase và cập nhật bảng
        onValue(ref(database, 'thongBao'), function(snapshot) {
            // Xóa dữ liệu hiện tại trong bảng trước khi thêm mới
            dataTable.clear();

            // Duyệt qua các thông báo trong Firebase
            snapshot.forEach(function(childSnapshot) {
                let thongBao = childSnapshot.val().thongBao;
                let ngayTao = new Date(childSnapshot.val().ngayTao).toLocaleString(); // Định dạng ngày

                // Thêm dòng mới vào bảng DataTable
                dataTable.row.add([
                    thongBao,
                    ngayTao,
                    '<button class="btn btn-danger" onclick="deleteNotification(\'' + childSnapshot.key + '\')">Xóa</button>'
                ]).draw(false);
            });
        });

        // Hàm xóa thông báo
        window.deleteNotification = function(key) {
            remove(ref(database, 'thongBao/' + key)).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Đã xóa!',
                    text: 'Thông báo đã được xóa thành công.'
                });
            }).catch(function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Không thể xóa thông báo.'
                });
            });
        }
    </script>

</body>

</html>
