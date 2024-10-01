<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    <title>Passenger</title>
</head>

<body>

    <div id="contentpro">
        <div class="row1">
            <!--category-left -->
            
            <!--category-right -->
            <div class="category-right-detail">
                
                <div class="main-passenger">
                    <div class="header-passenger">
                        <div class="banner">
                            <img src="../../public/img/banner.png" alt="">
                        </div>
                    </div>
                    <div id="body-passenger">
                        <div class="infor">
                            <h3>Thông tin hành khách</h3>
                            <table class="table table-striped">
                                <thead class="title">
                                    <tr>
                                        <th scope="col">Họ và tên</th>
                                        <!-- <th scope="col">Tên</th> -->
                                        <th scope="col">Ngày sinh</th>
                                        <th scope="col">Giới tính</th>
                                        <th scope="col">Địa chỉ</th>
                                        <th scope="col">Loại hành khách</th>

                                </thead>
                                <tbody>
                                    <?php
                                    require_once "app/models/Passenger.php";
                                    require_once "app/models/db.php";
                                    $sentenes = new Passenger();
                                    $id = $data['maKH'];
                                    $row = $sentenes->readById_table1($id);
                                    foreach ($row as $item) {
                                        echo "<tr>";
                                        echo "<td>" . $item["fullname"] . "</td>";
                                        // echo "<td>" . $item["tenKH"] . "</td>";
                                        echo "<td>" . $item["ngaySinh"] . "</td>";
                                        echo "<td>" . $item["gioiTinh"] . "</td>";
                                        echo "<td>" . $item["diaChi"] . "</td>";
                                        echo "<td>" . $item["loaiHanhKhach"] . "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="infor">
                            <h3>Thông tin vé</h3>
                            <table class="table table-striped">
                                <thead class="title">
                                    <tr>
                                        <th scope="col">Họ và Tên</th>
                                        <th scope="col">Giá Vé</th>
                                        <th scope="col">Mã vé</th>
                                        <th scope="col">Thời gian khởi hành</th>
                                        <th scope="col">Số lượng vé đã đặt</th>
                                </thead>
                                <tbody>
                                <?php
                                    require_once "app/models/Passenger.php";
                                    require_once "app/models/db.php";
                                    $sentenes = new Passenger();
                                    $id = $data["maKH"];
                                    $row = $sentenes->readById_table2($id);
                                    if(count($row) > 0){
                                    foreach ($row as $item) {
                                        echo "<tr>";
                                        echo "<td>" . $item["fullname"] . "</td>";
                                        // echo "<td>" . $item["hoKH"]." ".$item["tenKH"]."</td>";                                   
                                        echo "<td>" . $item["giaVe"] . "</td>";
                                        echo "<td>" . $item["maVe"] . "</td>";    
                                        echo "<td>" . $item["gioBay"] . "</td>";
                                        echo "<td>" . $item["soLuongDat"] . "</td>";
                                        echo "</tr>";
                                    }
                                }else{
                                    echo "Chưa đặt vé nào";
                                }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="infor">
                            <h3>Chi tiết chuyến bay</h3>
                            <table class="table table-striped">
                                <thead class="title">
                                    <tr>
                                        <th scope="col">Mã chuyến bay</th>
                                        <th scope="col">Nơi đi</th>
                                        <th scope="col">Nơi đến</th>
                                       
                                        
                                        <th scope="col">Ngày đi</th>
                                        <th scope="col">Ngày đến</th>
                                       
                                        <th scope="col">Lịch khởi hành</th>
                                </thead>
                                <tbody>
                                <?php
                                    require_once "app/models/Passenger.php";
                                    require_once "app/models/db.php";
                                    $sentenes = new Passenger();
                                    $id = $data['maKH'];
                                    $row = $sentenes->readById_table2($id);
                                    foreach ($row as $item) {
                                        echo "<tr>";
                                        echo "<td>" . $item["maCB"] . "</td>";
                                        echo "<td>" . $item["diaDiemDi"] . "</td>";
                                        echo "<td>" . $item["diaDiemDen"] . "</td>";
                                                                               
                                        echo "<td>" . $item["ngayDi"] . "</td>";  
                                        echo "<td>" . $item["ngayDen"] . "</td>";                                     
                                        echo "<td>" . $item["gioBay"] . "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="javascript:history.back()"><div class="btn-exit">
                            <button>Thoát</button>
                        </div></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>