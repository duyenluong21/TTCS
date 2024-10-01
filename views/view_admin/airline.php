<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    <title>Airline</title>
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
                <button id="myBtn"><i class="fa-solid fa-plus"></i>Thêm máy bay</button>
                <div id="myModal" class="modal">
                    <!-- Modal content -->
                    <div class="modal-content" style="width : 45%">
                        <span class="close">&times;</span>
                        <h4 style="margin: 10px 36%;">Thêm Máy Bay</h4>
                        <form action="" id="form" onsubmit="return validateForm()">
                            <input type="text" name="form_name" value="send_value" hidden="true">
                            <div class="left" style="width: 85%;">
                                <input type="text" id="maMB" name="maMB" hidden="true">

                                <label for="tenMayBay">Tên máy bay</label>
                                <input type="text" id="tenMayBay" name="tenMayBay" placeholder="">
                                <span class="error" id="tenMayBayError"></span>
                                <label for="hangMayBay">Hãng máy bay</label>
                                <input type="text" id="hangMayBay" name="hangMayBay" placeholder="">
                                <span class="error" id="hangMayBayError"></span>
                                <label for="gheToiDa">Ghế tối đa</label>
                                <input type="text" id="gheToiDa" name="gheToiDa" placeholder="">
                                <span class="error" id="gheToiDaError"></span>
                                <button type="submit" value="Submit" id="save">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="infor">
                <h3>Thông tin máy bay</h3>
                <table class="table table-striped" id="myDataTable">
                    <thead class="title">
                        <tr>
                            <th scope="col">Tên máy bay</th>
                            <th scope="col">Hãng máy bay</th>
                            <th scope="col">Ghế tối đa</th>
                            <th scope="col">Hành động</th>
                    </thead>
                    <tbody class="tbody">

                    </tbody>
                </table>
            </div>
            <div class="btn-exit">
                <button>Thoát</button>
            </div>
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
    var tenMayBayInput = document.getElementById("tenMayBay");
    

    tenMayBayInput.addEventListener("input", function () {
        validateTenMayBay();
    });

    function validateTenMayBay() {
        var tenMayBayValue = tenMayBayInput.value.trim();
        var tenMayBayError = document.getElementById("tenMayBayError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (tenMayBayValue.length < 4) {
            tenMayBayError.innerHTML = "Tên máy bay phải nhập dưới dạng chuỗi";
            hasError = true;
        } else {
            tenMayBayError.innerHTML = "";
            hasError = false;
        }
        updateSaveButton(); 
    }
</script>
<script>
    var hangMayBayInput = document.getElementById("hangMayBay");
    

    hangMayBayInput.addEventListener("input", function () {
        validatehangMayBay();
    });

    function validatehangMayBay() {
        var hangMayBayValue = hangMayBayInput.value.trim();
        var hangMayBayError = document.getElementById("hangMayBayError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (hangMayBayValue.length < 4) {
            hangMayBayError.innerHTML = "Hãng máy bay phải nhập dưới dạng chuỗi";
            hasError = true;
        } else {
            hangMayBayError.innerHTML = "";
            hasError = false;
        }
        updateSaveButton(); 
    }
</script>
<script>
        var gheToiDaInput = document.getElementById("gheToiDa");

        gheToiDaInput.addEventListener("input", function () {
            validategheToiDa();
        });

        function validategheToiDa() {
            var gheToiDaValue = gheToiDaInput.value.trim();
            var gheToiDaError = document.getElementById("gheToiDaError");

            if (!/^\d+$/.test(gheToiDaValue) || gheToiDaValue.length < 3) {
                gheToiDaError.innerHTML = "Ghế tối đa phải được nhập bằng số.";
                hasError = true;
            } else {
                gheToiDaError.innerHTML = "";
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
    tenMayBayInput.addEventListener("input", function () {
        validateTenMayBay();
    });

    hangMayBayInput.addEventListener("input", function () {
        validatehangMayBay();
    });

    gheToiDaInput.addEventListener("input", function () {
        validategheToiDa();
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="../../app/js/airline_function.js"></script>

</html>