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
                    <h3>Thêm thông báo</h3>
                    <form action="" id ="form" onsubmit="">
                        <input type="text" name="form_name" value="send_value" hidden="true">
                        <input type="text" id="maSanBay" name="maSanBay" hidden="true">
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
                            <button class="exit">Thoát</button>
                        </div>
                    </form>

                </div>

                <div class="airport-left">
                    <table class="table table-striped" id="myDataTable">
                        <thead class="title">
                            <tr>
                                <th scope="col">Thông báo</th>
                                <th scope="col">Ngày tạo</th>
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
    <!-- <script>
        var dataTable = $('#myDataTable').DataTable();
        dataTable.destroy();
        $(document).ready(function() {
            print_table();
        });
    </script> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="module" src="../../app/js/security_function.js"></script>
    <!-- <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging.js"></script>
<script>
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

  // Khởi tạo Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
  
  const messaging = firebase.messaging();

  // Lấy token
  messaging.getToken({ vapidKey: 'BDMipD5wwIVWKySodyBuw3f5WM2gl-zcwxumYKKvX5VkkpHu3GbGaquWi6oUJLV5ppAjIfdj5hqYMjq07uE1eUQ' }).then((currentToken) => {
    if (currentToken) {
      console.log('Token của thiết bị:', currentToken);
      // Gửi token này lên server của bạn nếu cần
    } else {
      console.log('Không thể lấy token');
    }
  }).catch((err) => {
    console.log('Lỗi khi lấy token:', err);
  });
</script> -->


</body>