<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="../../public/css/datatables.min.css">
    <title>Passenger</title>
    <!--JQuery and dataTable -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>

<body>
    <div id="contentpro">
        <div class="row1">
            <!--category-left -->
            <?php include("../TTCS/views/view_admin/category_left.php"); ?>
            <!--category-right -->
            <div class="category-right">
                <?php include("../TTCS/views/view_admin/header.php"); ?>
                <div class="main-passenger">
                    <div class="header-passenger">
                        <div class="banner">
                            <img src="../../public/img/banner.png" alt="">
                        </div>
                    </div>
                    <div id="body-passenger">
                        <div class="infor">
                            <h3>Thông tin hành khách</h3>
                            <table class="table table-striped" id="myDataTable">
                                <thead class="title">
                                    <tr>
                                        <th scope="col">Họ và tên</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Ngày sinh</th>
                                        <th scope="col" style="width: 80px;">Giới tính</th>
                                        <th scope="col" style="width: 250px;">Địa chỉ</th>
                                        <th scope="col" style="width: 80px;">Số điện thoại</th>
                                        <th scope="col" style="width: 80px;">Hành động</th>

                                </thead>
                                <tbody class = "tbody">

                                </tbody>
                            </table>
                        </div>
                        <a href="javascript:history.back()">
                        <div class="btn-exit">
                            <button>Thoát</button>
                        </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
function print_table(){
    fetch('http://localhost:3000/app/api/readPassenger.php')
    .then(response => response.json())
        .then(data => {
            // Đảm bảo rằng DataTable đã được hủy bỏ trước khi kích hoạt lại
            $('#myDataTable').DataTable().destroy();

            // Hiển thị dữ liệu trên DataTable
            $('#myDataTable').DataTable({
                data: data.data, // Giả sử API trả về một đối tượng với trường "data" chứa mảng dữ liệu
                columns: [
                    { data: 'fullname', title: 'Họ và tên' },
                     { data: 'email', title: 'Email' },
                    { data: 'ngaySinh', title: 'Ngày sinh' },
                    { data: 'gioiTinh', title: 'Giới tính' },
                    { data: 'diaChi', title: 'Địa chỉ' },
                    { data: 'soDT', title: 'Số điện thoại' },
                    {
                        data: null,
                        title: 'Hành Động',
                        render: function (data) {
                            return `<a href = \"/detail/${data.maKH}\">Xem chi tiết`;
                        }
                    }
                ],
                destroy: true, // Đảm bảo hủy bỏ DataTable trước khi kích hoạt lại
            });
        })
        .catch(error => console.log(error));
}

</script>
<script>
        var dataTable = $('#myDataTable').DataTable();
        dataTable.destroy();
        $(document).ready(function() {
            print_table();
        });
    </script>
</body>
</html>