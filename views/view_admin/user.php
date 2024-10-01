<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    <title>User</title>
    <style>
    .error {
        color: red;
        display: block; /* Để nó xuống dòng */
        margin-top: 5px; /* Điều chỉnh khoảng cách giữa trường nhập liệu và thông báo lỗi */
        font-size: 14px; /* Điều chỉnh kích thước font chữ */
        font-weight: bold; /* Đặt độ đậm cho font chữ */
    }
    </style>
    <!--JQuery and dataTable -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bcryptjs/2.2.0/bcrypt.min.js" integrity="sha512-BJZhA/ftU3DVJvbBMWZwp7hXc49RJHq0xH81tTgLlG16/OkDq7VbNX6nUnx+QY4bBZkXtJoG0b0qihuia64X0w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>var hasError = false; // Biến kiểm tra lỗi</script>
</head>

<body>

    <!--category-left -->
    <?php include("../TTCS/views/view_admin/category_left.php"); ?>
    <!--category-right -->
    <div class="category-right">
        <?php include("../TTCS/views/view_admin/header.php"); ?>
        <div id="body-user">
            <div class="btn-exit" id="close">
                <button id="myBtn"><i class="fa-solid fa-plus"></i>Thêm nhân viên</button>
                <div id="myModal" class="modal">
                    <!-- Modal content -->
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h4 style="margin: 12px 37%;">Thêm Nhân Viên</h4>
                        <form action="" id="form">
                            <input type="text" name="form_name" value="send_value" hidden="true">
                            <input type="text" id="maNV" name="maNV" hidden="true">
                            <div class="left">
                                <label for="hoNV">Họ</label>
                                <input type="text" id="hoNV" name="hoNV" placeholder="Họ">
                                <span class="error" id="hoNVError"></span>
                                <label for="tenNV">Tên</label>
                                <input type="text" id="tenNV" name="tenNV" placeholder="Tên">
                                <span class="error" id="tenNVError"></span>
                                <label for="username">Tài khoản</label>
                                <input type="text" id="username" name="username" placeholder="Tài khoản">
                                <span class="error" id="usernameError"></span>
                            </div>
                            <div class="right">
                                <label for="kinhNghiem">Kinh nghiệm</label>
                                <input type="text" id="kinhNghiem" name="kinhNghiem" placeholder="Kinh nghiệm">
                                <span class="error" id="kinhNghiemError"></span>
                                <label for="trinhDoHocVan">Trình độ học vấn</label>
                                <input type="text" id="trinhDoHocVan" name="trinhDoHocVan" placeholder="Trình độ học vấn">
                                <span class="error" id="trinhDoHocVanError"></span>
                                <label for="chucVu">Chức vụ</label>
                                <select id="chucVu" name="chucVu">
                                    <option value="Nhân viên tại quầy">Nhân viên tại quầy</option>
                                    <option value="Nhân viên soát vé">Nhân viên soát vé</option>
                                    <option value="Nhân viên kiểm tra">Nhân viên kiểm tra</option>

                                </select>
                                <label for="passw">Mật khẩu</label>
                                <input type="password" id="passw" name="passw" placeholder="Mật khẩu">
                                <button type="submit" id="save">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="infor">
                <h3>Thông tin nhân viên</h3>
                <table class="table table-striped" id="myDataTable">
                    <thead class="title">
                        <tr>
                            <th scope="col" style="width: 80px;">Mã nhân viên</th>
                            <th scope="col">Họ</th>
                            <th scope="col" style="width: 100px;">Tên</th>
                            <th scope="col">Chức vụ</th>
                            <th scope="col">Kinh nghiệm</th>
                            <th scope="col">Học vấn</th>
                            <!-- <th scope="col">Tài khoản</th>
                            <th scope="col">Mật khẩu</th> -->
                            <th scope="col" style="width: 50px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="tbody">

                    </tbody>
                </table>
            </div>
            <a href="javascript:history.back()">
                <div class="btn-exit">
                    <button>Thoát</button>
                </div>
            </a>
        </div>
        <div class="error_notice" id="message">
        </div>
</body>
<script>
    var modal = document.getElementById("myModal");
    var btn = document.getElementById("myBtn");
    var span = document.getElementsByClassName("close")[0];
    btn.onclick = function() {
        modal.style.display = "block";
    }
    span.onclick = function() {
        modal.style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<script>
        var dataTable = $('#myDataTable').DataTable();
        dataTable.destroy();
        $(document).ready(function() {
            print_table();
        });
</script>
<script>
    var hoNVInput = document.getElementById("hoNV");
    

    hoNVInput.addEventListener("input", function () {
        validatehoNV();
    });

    function validatehoNV() {
        var hoNVValue = hoNVInput.value.trim();
        var hoNVError = document.getElementById("hoNVError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (hoNVValue.length < 2) {
            hoNVError.innerHTML = "Họ phải nhập dưới dạng chuỗi và ít nhất 2 kí tự.";
            hasError = true;
        } else {
            hoNVError.innerHTML = "";
            hasError = false;
        }
        updateSaveButton();
    }
</script>
<script>
    var tenNVInput = document.getElementById("tenNV");
    

    tenNVInput.addEventListener("input", function () {
        validatetenNV();
    });

    function validatetenNV() {
        var tenNVValue = tenNVInput.value.trim();
        var tenNVError = document.getElementById("tenNVError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (tenNVValue.length < 2) {
            tenNVError.innerHTML = "Tên phải nhập dưới dạng chuỗi và ít nhất 2 kí tự.";
            hasError = true;
        } else {
            tenNVError.innerHTML = "";
            hasError = false;
        }
        updateSaveButton();
    }
</script>
<script>
    var usernameInput = document.getElementById("username");
    

    usernameInput.addEventListener("input", function () {
        validateusername();
    });

    function validateusername() {
        var usernameValue = usernameInput.value.trim();
        var usernameError = document.getElementById("usernameError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (usernameValue.length < 8) {
            usernameError.innerHTML = "Tên tài khoản phải nhập dưới dạng chuỗi và ít nhất 8 kí tự.";
            hasError = true;
        } else {
            usernameError.innerHTML = "";
            hasError = false;
        }
        updateSaveButton();
    }
</script>
<script>
    var kinhNghiemInput = document.getElementById("kinhNghiem");
    

    kinhNghiemInput.addEventListener("input", function () {
        validatekinhNghiem();
    });

    function validatekinhNghiem() {
        var kinhNghiemValue = kinhNghiemInput.value.trim();
        var kinhNghiemError = document.getElementById("kinhNghiemError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (kinhNghiemValue.length < 4) {
            kinhNghiemError.innerHTML = "Kinh nghiệm phải nhập dưới dạng chuỗi";
            hasError = true;
        } else {
            kinhNghiemError.innerHTML = "";
            hasError = false;
        }
        updateSaveButton();
    }
</script>
<script>
    var trinhDoHocVanInput = document.getElementById("trinhDoHocVan");
    

    trinhDoHocVanInput.addEventListener("input", function () {
        validatetrinhDoHocVan();
    });

    function validatetrinhDoHocVan() {
        var trinhDoHocVanValue = trinhDoHocVanInput.value.trim();
        var trinhDoHocVanError = document.getElementById("trinhDoHocVanError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (trinhDoHocVanValue.length < 4) {
            trinhDoHocVanError.innerHTML = "Trình độ học vấn phải nhập dưới dạng chuỗi.";
             hasError = true;
        } else {
            trinhDoHocVanError.innerHTML = "";
            hasError = false;
        }
        updateSaveButton();
    }
</script>
<script>
     function updateSaveButton() {
        var saveButton = document.getElementById("save");
        saveButton.disabled = hasError; // Disable hoặc enable button tùy thuộc vào giá trị của biến kiểm tra lỗi
    }
</script>
<script>
    hoNVInput.addEventListener("input", function () {
        validatehoNV();
    });

    tenNVInput.addEventListener("input", function () {
        validatetenNV();
    });

    kinhNghiemInput.addEventListener("input", function () {
        validatekinhNghiem();
    });
    trinhDoHocVanInput.addEventListener("input", function () {
        validatetrinhDoHocVan();
    });
    usernameInput.addEventListener("input", function () {
        va();
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="../../app/js/user_function.js"></script>

</html>