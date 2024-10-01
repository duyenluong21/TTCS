<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    <title>Airport</title>
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

        <div id="main-airport">
            <div class="container-right-airport">
                <div class="airport-right">
                    <h3>Thêm sân bay</h3>
                    <form action="" id ="form" onsubmit="return validateForm()">
                        <input type="text" name="form_name" value="send_value" hidden="true">
                        <input type="text" id="maSanBay" name="maSanBay" hidden="true">
                        <div class="input-airport">
                            <label for="">Sân bay</label>
                            <input type="text" id="tenSanBay" name="tenSanBay" placeholder="">
                            <span class="error" id="tenSanBayError"></span>
                        </div>
                        <div class="location-airport">
                            <label for="">Địa điểm</label>
                            <input type="text" id="diaDiem" name="diaDiem">
                            <span class="error" id="diaDiemError"></span>
                        </div>
                        <div class="btn-airport">
                            <button type="submit" id="save" class="save">Lưu</button>
                            <button class="exit">Thoát</button>
                        </div>
                    </form>

                </div>

                <div class="airport-left">
                    <table class="table table-striped" id="myDataTable">
                        <thead class="title">
                            <tr>
                                <th scope="col" style="width: 100px;">Mã sân bay</th>
                                <th scope="col">Sân bay</th>
                                <th scope="col">Địa điểm</th>
                                <th scope="col">Hành động</th>
                        </thead>
                        <tbody class="tbody">

                        </tbody>
                    </table>
                </div>
                <div class="error_notice" id="message">
                </div>
            </div>
        </div>
    </div>
    <script>
        var dataTable = $('#myDataTable').DataTable();
        dataTable.destroy();
        $(document).ready(function() {
            print_table();
        });
    </script>
    <script>
    var tenSanBayInput = document.getElementById("tenSanBay");
    

    tenSanBayInput.addEventListener("input", function () {
        validatetenSanBay();
    });

    function validatetenSanBay() {
        var tenSanBayValue = tenSanBayInput.value.trim();
        var tenSanBayError = document.getElementById("tenSanBayError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (tenSanBayValue.length < 4) {
            tenSanBayError.innerHTML = "Tên sân bay phải nhập dưới dạng chuỗi ";
            hasError = true;
        } else {
            tenSanBayError.innerHTML = "";
            hasError = false;
        }
        updateSaveButton(); 
    }
</script>
<script>
    var diaDiemInput = document.getElementById("diaDiem");
    

    diaDiemInput.addEventListener("input", function () {
        validatediaDiem();
    });

    function validatediaDiem() {
        var diaDiemValue = diaDiemInput.value.trim();
        var diaDiemError = document.getElementById("diaDiemError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (diaDiemValue.length < 4) {
            diaDiemError.innerHTML = "Địa điểm phải nhập dưới dạng chuỗi ";
            hasError = true;
        } else {
            diaDiemError.innerHTML = "";
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
    tenSanBayInput.addEventListener("input", function () {
        validatetenSanBay();
    });

    diaDiemInput.addEventListener("input", function () {
        validatediaDiem();
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="../../app/js/airport_function.js"></script>

</body>