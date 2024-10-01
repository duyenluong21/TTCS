<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    <title>Ticket</title>
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
        <div id="main-booked">
            <div id="container-booked">
                <div class="container-booked">
                    <h3 style="font-size: 1.55rem;">Danh sách vé</h3>
                    <div class="btn-exit">
                        <button id="myBtn"><i class="fa-solid fa-plus"></i>Thêm vé</button>
                        <div id="myModal" class="modal">
                            <!-- Modal content -->
                            <div class="modal-content" style="width : 45%">
                                <span class="close">&times;</span>
                                <h4 style="margin: 10px 36%;">Thêm Vé</h4>
                                <form action="" id ="form" onsubmit="return validateForm()">
                                    <input type="text" name="form_name" value="send_value" hidden="true">

                                    <!-- <input type="text" id="maVe" name="maVe" hidden="true"> -->
                                    <div class="left" style="width: 85%;">
                                        <label for="maVe">Mã vé</label>
                                        <input type="text" id="maVe" name="maVe" placeholder=""> 
                                        <span class="error" id="maVeError"></span>
                                        <!-- <label for="loaiVe">Loại vé</label>
                                        <input type="text" id="loaiVe" name="loaiVe" placeholder=""> -->

                                        <label for="soLuong">Số lượng</label>
                                        <input type="text" id="soLuong" name="soLuong" placeholder="">
                                        <span class="error" id="soLuongError"></span>
                                        <!-- <label for="soLuongCon">Số lượng còn</label>
                                        <input type="text" id="soLuongCon" name="soLuongCon" placeholder=""> -->
                                        <label for="hangVe">Hạng vé</label>
                                        <input type="text" id="hangVe" name="hangVe" placeholder="">
                                        <span class="error" id="hangVeError"></span>
                                        <button type="submit" value="Submit" id="save">Save</button>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-booked">
                    <table class="table table-striped" id="myDataTable">
                        <thead class="title">
                            <tr>
                                <th scope="col">Mã vé</th>
                                <th scope="col">Số lượng</th>
                                <th scope="col">Hạng vé</th>
                                 <!-- <th scope="col">Giá hạng vé</th> -->
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
        var maVeInput = document.getElementById("maVe");

        maVeInput.addEventListener("input", function () {
            validatemaVe();
        });

        function validatemaVe() {
            var maVeValue = maVeInput.value.trim();
            var maVeError = document.getElementById("maVeError");

            if (!/^\d+$/.test(maVeValue)) {
                maVeError.innerHTML = "Mã vé phải được nhập bằng số và giá trị dương.";
                hasError = true; // Set biến kiểm tra lỗi thành true
            } else {
                maVeError.innerHTML = "";
                hasError = false;
            }
            updateSaveButton(); 
        }
</script>
<script>
        var soLuongInput = document.getElementById("soLuong");

        soLuongInput.addEventListener("input", function () {
            validatesoLuong();
        });

        function validatesoLuong() {
            var soLuongValue = soLuongInput.value.trim();
            var soLuongError = document.getElementById("soLuongError");

            if (!/^\d+$/.test(soLuongValue)) {
                soLuongError.innerHTML = "Số lượng vé phải được nhập bằng số và giá trị dương.";
                hasError = true;
            } else {
                soLuongError.innerHTML = "";
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
    var hangVeInput = document.getElementById("hangVe");
    

    hangVeInput.addEventListener("input", function () {
        validatehangVe();
    });

    function validatehangVe() {
        var hangVeValue = hangVeInput.value.trim();
        var hangVeError = document.getElementById("hangVeError");
        // Kiểm tra Địa điểm đi (phải là chuỗi có ít nhất 4 ký tự)
        if (hangVeValue.length < 4) {
            hangVeError.innerHTML = "Hạng vé phải nhập ít nhất 4 kí tự và chỉ chứa chữ cái.";
            hasError = true;
        } else {
            hangVeError.innerHTML = "";
            hasError = false;

        }
        updateSaveButton();
    }
</script>
<script>
    maVeInput.addEventListener("input", function () {
        validatemaVe();
    });

    soLuongInput.addEventListener("input", function () {
        validatesoLuong();
    });

    hangVeInput.addEventListener("input", function () {
        validatehangVe();
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="../../app/js/booked_function.js"></script>
</body>

</html>