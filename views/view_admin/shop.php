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
                <button id="myBtn"><i class="fa-solid fa-plus"></i>Thêm cửa hàng</button>
                <div id="myModal" class="modal">
                    <!-- Modal content -->
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h4 style="margin: 12px 37%;">Thêm cửa hàng</h4>
                        <form action="" id="form">
                            <input type="text" name="form_name" value="send_value" hidden="true">
                            <input type="text" id="maNV" name="maNV" hidden="true">
                            <div class="left">
                                <label for="username">Tài khoản</label>
                                <input type="text" id="taikhoan" name="taikhoan" placeholder="Tài khoản">
                                <span class="error" id="usernameError"></span>
                            </div>
                            <div class="right">
                                <label for="passw">Mật khẩu</label>
                                <input type="password" id="matkhau" name="matkhau" placeholder="Mật khẩu">
                                <button type="submit" id="save">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="infor">
                <h3>Thông tin cửa hàng</h3>
                <table class="table table-striped" id="myDataTable">
                    <thead class="title">
                        <tr>
                            <th scope="col">Mã cửa hàng</th>
                            <th scope="col">Tài khoản</th>
                            <th scope="col">Hành động</th>
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
     function updateSaveButton() {
        var saveButton = document.getElementById("save");
        saveButton.disabled = hasError; // Disable hoặc enable button tùy thuộc vào giá trị của biến kiểm tra lỗi
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="../../app/js/shop_funtion.js"></script>

</html>