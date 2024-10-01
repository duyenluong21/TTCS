<?php
require_once('model.php');
require_once 'db.php';
class Booked extends Database
{
    //read db 
    public function read()
    {
        $sqlp = Database::connect()->prepare("SELECT * FROM thongtinchuyenbay as a, khachhang as b
        , vedadat as c WHERE c.maKH = b.maKH and c.maCB = a.maCB ");
        $sqlp->execute();
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC);
    }
    //insert db
    // public function insert(){
    // }
    public function edit($maSanBay){
        $sql = Database::connect()->prepare("SELECT * FROM sanbay WHERE maSanBay = '".$maSanBay."'");
        if ($sql->execute()) {
            return $array = $sql->fetchAll(PDO::FETCH_ASSOC);
    
        }else{
            return "error";
        }
    }

    public function update_ticket($maSanBay,$tenSanBay, $diaDiem){
        $sql = Database::connect()->prepare("UPDATE sanbay SET tenSanBay='".$tenSanBay."',diaDiem='".$diaDiem."'
         WHERE maSanBay='".$maSanBay."' ");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $resultado = self::read();
            return $resultado;
        }else{
            return "error";
       }
        
    }
    public function delete_ticket($maVeDaDat){
        $sql = Database::connect()->prepare("DELETE FROM vedadat WHERE maVeDaDat = '".$maVeDaDat."'");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $resultado = self::read();
            return $resultado;
        }else{
            return "error";
       }
    }
}
