<?php
require_once('model.php');
require_once 'db.php';
class Passenger extends Database {
    //read db 
    public function search($name){
        $sqlp = Database::connect()->prepare("SELECT * FROM khachhang
        WHERE hoKH like '%".$name."%' or tenKH like '%".$name."%' or maKH like '%".$name."%'");
        $sqlp->execute() ; 
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC) ;
    }
    public function readById_table1($maKH){
        $sqlp = Database::connect()->prepare("SELECT * FROM khachhang
        WHERE maKH = '".$maKH."'");
        $sqlp->execute() ; 
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC) ;
    }
    public function readById_table2($maKH){
        $sqlp = Database::connect()->prepare("SELECT * FROM veDaDat as a, thongtinchuyenbay as b, khachhang as c, ve as d
        WHERE a.maCB = b.maCB and a.maKH=c.maKH and a.maVe = d.maVe and a.maKH = '".$maKH."' ");
        $sqlp->execute() ; 
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC) ;
    }
    public function readById_table3($maKH){
        $sqlp = Database::connect()->prepare("SELECT * FROM ve as a, khachhang as b, thongtinchuyenbay as c where a.maKH = b.maKH
          and a.maKH = '".$maKH."'");
        $sqlp->execute() ; 
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC) ;
    }
    public function read_table1() {
        $sqlp = Database::connect()->prepare("SELECT * FROM khachhang");
        $sqlp->execute() ; 
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC) ;
    }
    public function read_table2() {
        $sqlp = Database::connect()->prepare("SELECT * FROM thongtinchuyenbay as tt");
        $sqlp->execute() ; 
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC) ;
    }
    public function read_table3() {
        $sqlp = Database::connect()->prepare("SELECT * FROM ve as a , khachhang as b, thongtinchuyenbay as c WHERE a.maKH = b.maKH and c.maCB = a.maCB");
        $sqlp->execute() ; 
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC) ;
    }

    public function page(){
        $sqlp = Database::connect()->prepare("SELECT count(maKH) as number from khachhang");
        $result = executeResult($sqlp);
        return $result;
    //     $number = 0;
    //     if($result != null && count($result) > 0){
    //         $number = $result[0]['number'];
    //     }
    //     $pages = ceil($number / 8);

    //     $current_page = 1;
    //     if(isset($_GET['page'])){
    //         $current_page = $_GET['page'];
    //     }
    //     $index = ($current_page - 1)*8;
    //     for($i = 1; $i <= $pages; $i++){
    //         echo '<li><a href="?page='.$i.'">'.$i.'</a></li>';
    //     }
    }
}
?>