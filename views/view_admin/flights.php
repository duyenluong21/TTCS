<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    <style>
    .error {
        color: red;
        display: block; /* Để nó xuống dòng */
        margin-top: 5px; /* Điều chỉnh khoảng cách giữa trường nhập liệu và thông báo lỗi */
        font-size: 14px; /* Điều chỉnh kích thước font chữ */
        font-weight: bold; /* Đặt độ đậm cho font chữ */
    }
    </style>
    <title>Flights</title>
    <!--JQuery and dataTable -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>var hasError = false; // Biến kiểm tra lỗi</script>
</head>

<body>

    <!--category-left -->
    <?php include("../TTCS/views/view_admin/category_left.php"); ?>
    <!--category-right -->
    <div class="category-right">
        <?php include("../TTCS/views/view_admin/header.php"); ?>
        <div id="body-user">
            <div class="btn-exit">
                <button id="myBtn"><i class="fa-solid fa-plus"></i>Thêm chuyến bay</button>
                <div id="myModal" class="modal">
                    <!-- Modal content -->
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h4 style="margin: 10px 36%;">Thêm Chuyến Bay</h4>
                        <form action="" id ="form" onsubmit="return validateForm()">
                        <input type="text" name="form_name" value="send_value" hidden="true">
                    
                            <div class="left">
                                <!-- <label for="maCB">Mã chuyến bay</label>
                                <input type="text" id="maCB" name="maCB" placeholder=""> -->
                                <label for="maMB">Mã máy bay</label>
                                <input type="text" id="maMB" name="maMB" placeholder="">
                                <span class="error" id="maMBError"></span>
                                <label for="diaDiemDi">Địa điểm khởi hành</label>
                                <input type="text" id="diaDiemDi" name="diaDiemDi" placeholder="">
                                <span class="error" id="diaDiemDiError"></span>
                                <label for="ghiChu">Ghi chú</label>
                                <input type="text" id="ghiChu" name="ghiChu" placeholder="">
                                <span class="error" id="ghiChuError"></span>
                                <label for="ngayDi">Ngày khởi hành</label>
                                <input type="date" id="ngayDi" name="ngayDi" placeholder="" style="width: 85%;" min="<?php echo date('Y-m-d'); ?>">
                            
                            </div>
                            <div class="right">
                                <label for="gioBay">Giờ bay</label>
                                <input type="time" id="gioBay" name="gioBay" placeholder="">
                                <!-- <label for="gheToiDa">Seats</label>
                                <input type="text" id="gheToiDa" name="gheToiDa" placeholder=""> -->
                                <label for="diaDiemDen">Địa điểm đến</label>
                                <input type="text" id="diaDiemDen" name="diaDiemDen" placeholder="">
                                <span class="error" id="diaDiemDenError"></span>
                                <label for="giaVe">Giá vé</label>
                                <input type="text" id="giaVe" name="giaVe" placeholder="">
                                <span class="error" id="giaVeError"></span>
                                <label for="ngayDen">Ngày đến</label>
                                <input type="date" id="ngayDen" name="ngayDen" placeholder="" style="width: 85%;" min="<?php echo date('Y-m-d'); ?>">
                                <button type="submit" value="Submit" id="save">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="infor">
                <h3>Thông tin chuyến bay</h3>
                <table class="table table-striped" id= "myDataTable">
                    <thead class="title">
                        <tr>
                            <th scope="col" style="width: 80px;">Mã chuyến bay</th>
                            <th scope="col">Ngày đến</th>
                            <th scope="col">Ngày đi</th>
                            <th scope="col">Địa điểm đến</th>
                            <th scope="col">Địa điểm đi</th>
                            <th scope="col">Số lượng ghế</th>
                            <th scope="col" style="width: 200px">Ghi chú</th>
                            <th scope="col">Giờ bay</th>
                            <th scope="col">Hành động</th>
                    </thead>
                    <tbody  class="tbody">

                    </tbody>
                </table>
            </div>
            <div class="btn-exit">
                <button>Thoát</button>
            </div>
        </div>
        <div class="error_notice" id = "message">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src ="../../app/js/flights_function.js"></script>
<script>
        var maMBInput = document.getElementById("maMB");

        maMBInput.addEventListener("input", function () {
            validateMaMB();
        });

        function validateMaMB() {
            var maMBValue = maMBInput.value.trim();
            var maMBError = document.getElementById("maMBError");

            if (!/^\d+$/.test(maMBValue)) {
                maMBError.innerHTML = "Mã máy bay phải được nhập bằng số và giá trị dương.";
                hasError = true;
            } else {
                maMBError.innerHTML = "";
                hasError = false;
            }
            updateSaveButton();
        }
</script>
<script>
        var giaVeInput = document.getElementById("giaVe");

        giaVeInput.addEventListener("input", function () {
            validateGiaVe();
        });

        function validateGiaVe() {
            var giaVeValue = giaVeInput.value.trim();
            var giaVeError = document.getElementById("giaVeError");

            if (!/^\d+$/.test(giaVeValue) || giaVeValue.length < 6) {
                giaVeError.innerHTML = "Giá vé phải được nhập bằng số và giá trị dương.";
                hasError = true;
            } else {
                giaVeError.innerHTML = "";
                hasError = false;
            }
            updateSaveButton();
        }
</script>
<script>
    var diaDiemDiInput = document.getElementById("diaDiemDi");
    

    diaDiemDiInput.addEventListener("input", function () {
        validateDiaDiemDi();
    });

    function validateDiaDiemDi() {
        var diaDiemDiValue = diaDiemDiInput.value.trim();
        var diaDiemDiError = document.getElementById("diaDiemDiError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (diaDiemDiValue.length < 4) {
            diaDiemDiError.innerHTML = "Địa điểm đi phải nhập dưới dạng chuỗi và ít nhất 4 kí tự.";
            hasError = true;
        } else {
            diaDiemDiError.innerHTML = "";
            hasError = false;
        }
        updateSaveButton();
    }
</script>
<script>
    var diaDiemDenInput = document.getElementById("diaDiemDen");
    

    diaDiemDenInput.addEventListener("input", function () {
        validateDiaDiemDen();
    });

    function validateDiaDiemDen() {
        var diaDiemDenValue = diaDiemDenInput.value.trim();
        var diaDiemDenError = document.getElementById("diaDiemDenError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (diaDiemDenValue.length < 4) {
            diaDiemDenError.innerHTML = "Địa điểm đến phải nhập dưới dạng chuỗi và ít nhất 4 kí tự.";
            hasError = true;
        } else {
            diaDiemDenError.innerHTML = "";
            hasError = false;
        }
        updateSaveButton();
    }
</script>
<script>
    var ghiChuInput = document.getElementById("ghiChu");
    

    ghiChuInput.addEventListener("input", function () {
        validateGhiChu();
    });

    function validateGhiChu() {
        var ghiChuValue = ghiChuInput.value.trim();
        var ghiChuError = document.getElementById("ghiChuError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (ghiChuValue.length < 4) {
            ghiChuError.innerHTML = "Ghi chú  phải nhập dưới dạng chuỗi và ít nhất 4 kí tự.";
            hasError = true;
        } else {
            ghiChuError.innerHTML = "";
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
    maMBInput.addEventListener("input", function () {
        validateMaMB();
    });

    giaVeInput.addEventListener("input", function () {
        validateGiaVe();
    });

    ghiChuInput.addEventListener("input", function () {
        validateGhiChu();
    });
    diaDiemDiInput.addEventListener("input", function () {
        validateDiaDiemDi();
    });
    diaDiemDenInput.addEventListener("input", function () {
        validateDiaDiemDen();
    });
</script>

</html>