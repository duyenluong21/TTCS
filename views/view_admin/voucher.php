<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    <title>Voucher</title>
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
                <button id="myBtn"><i class="fa-solid fa-plus"></i>Thêm khuyến mãi</button>
                <div id="myModal" class="modal">
                    <!-- Modal content -->
                    <div class="modal-content" style="width : 45%">
                        <span class="close">&times;</span>
                        <h4 style="text-align: center;">Thêm khuyến mãi</h4>
                        <form action="" id="form" onsubmit="return validateForm()">
                            <input type="text" name="form_name" value="send_value" hidden="true">
                            <div class="left" style="width: 100%; margin-left: 70px;">
                                <input type="text" id="code" name="code" hidden="true">
                                <label for="code">Code</label>
                                <input type="text" id="code" name="code" placeholder="">
                                <span class="error" id="tenMayBayError"></span>
                                <label for="discount">Giảm giá</label>
                                <input type="text" id="discount" name="discount" placeholder="">
                                <span class="error" id="hangMayBayError"></span>
                                <label for="ngayHetHan">Ngày hết hạn</label>
                                <input type="text" id="ngayHetHan" name="ngayHetHan" placeholder="">
                                <span class="error" id="gheToiDaError"></span>
                                <label for="ngayTao">Ngày tạo</label>
                                <input type="text" id="ngayTao" name="ngayTao" placeholder="">
                                <span class="error" id="gheToiDaError"></span>
                                <label for="trangThai">Dành cho</label>
                                <input type="text" id="trangThai" name="trangThai" placeholder="">
                                <span class="error" id="gheToiDaError"></span>
                                <button type="submit" value="Submit" id="save">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="infor">
                <h3>Thông tin khuyến mãi</h3>
                <table class="table table-striped" id="myDataTable">
                    <thead class="title">
                        <tr>
                            <th scope="col">Code</th>
                            <th scope="col">Giảm giá</th>
                            <th scope="col">Ngày hết hạn</th>
                            <th scope="col">Ngày tạo</th>
                            <th scope="col">Dành cho</th>
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
     function updateSaveButton() {
        var saveButton = document.getElementById("save");
        saveButton.disabled = hasError; // Disable hoặc enable button tùy thuộc vào giá trị của biến kiểm tra lỗi
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="../../app/js/voucher_function.js"></script>

</html>