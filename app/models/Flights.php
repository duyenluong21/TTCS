<?php
require_once('model.php');
require_once 'db.php';
class Flights extends Database
{
    //read db 
    public function read()
    {
        $sqlp = Database::connect()->prepare("SELECT * FROM maybay as a, thongtinchuyenbay as b
        WHERE a.maMB = b.maMB");
        $sqlp->execute();
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC);
    }
    //insert db
    public function insert(
        $maCB, $gioBay, $maMB,$giaVe
        ,$diaDiemDi,$diaDiemDen, $ngayDi,$ngayDen
    ) {
        
        $sql = Database::connect()->prepare("INSERT INTO thongtinchuyenbay(maCB,gioBay,maMB,giaVe,diaDiemDi,diaDiemDen,ngayDi,ngayDen)
        VALUES('$maCB','$gioBay','$maMB','$giaVe','$diaDiemDi','$diaDiemDen','$ngayDi','$ngayDen') 
        ");
        if ($sql->execute()) {
            $result = self::read();
            return $result;
        }
    }
    public function edit($maCB){
        $sql = Database::connect()->prepare("SELECT * FROM maybay as a, thongtinchuyenbay as b
        WHERE a.maMB = b.maMB and maCB = '".$maCB."'");
        if ($sql->execute()) {
            return $array = $sql->fetchAll(PDO::FETCH_ASSOC);  
        }else{
            return "error";
        }
    }
    public function update_flights($maCB,$giaVe
    ,$diaDiemDi,$diaDiemDen, $ngayDi,$ngayDen){
        $sql = Database::connect()->prepare("UPDATE thongtinchuyenbay SET diaDiemDi='".$diaDiemDi."',diaDiemDen='".$diaDiemDen."',
        ngayDi='".$ngayDi."',giaVe='".$giaVe."',ngayDen='".$ngayDen."'
         WHERE maCB='".$maCB."' ");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $resultado = self::read();
            return $resultado;
        }else{
            return "error";
       }
        
    }
    public function delete_flights($maCB){
        $sql = Database::connect()->prepare("DELETE FROM  thongtinchuyenbay 
        WHERE maCB= '".$maCB."'");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $resultado = self::read();
            return $resultado;
        }else{
            return "error";
       }
    }
}
