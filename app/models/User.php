<?php
require_once('model.php');
require_once 'db.php';
class User extends Database
{
    //read db 
    public function read()
    {
        $sqlp = Database::connect()->prepare("SELECT * FROM nhanvien");
        $sqlp->execute();
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC);
    }
    //insert db
    public function insert(
        $maNV,$hoNV,
        $tenNV,
        $kinhNghiem,
        $trinhDoHocVan,
        $chucVu,
        $username,
        $passw,
    ) {
        
        $sql = Database::connect()->prepare("INSERT INTO nhanvien(maNV,hoNV,tenNV,kinhNghiem,trinhDoHocVan,chucVu,username,passw)
        VALUES('$maNV','$hoNV','$tenNV','$kinhNghiem','$trinhDoHocVan','$chucVu','$username','$passw') ");
        if ($sql->execute()) {
            $result = self::read();
            return $result;
        }
    }
    public function edit($maNV){
        $sql = Database::connect()->prepare("SELECT * FROM nhanvien WHERE maNV = '".$maNV."'");
        if ($sql->execute()) {
            return $array = $sql->fetchAll(PDO::FETCH_ASSOC);
    
        }else{
            return "error";
        }
    }
    public function update_user($maNV,$hoNV, $tenNV,$chucVu,$kinhNghiem,$username,$passw){
        $sql = Database::connect()->prepare("UPDATE nhanvien SET hoNV='".$hoNV."',tenNV='".$tenNV."', chucVu='".$chucVu."',
         kinhNghiem='".$kinhNghiem."', username='".$username."',passw='".$passw."' WHERE maNV='".$maNV."' ");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $resultado = self::read();
            return $resultado;
        }else{
            return "error";
       }
        
    }
    public function delete_user($maNV){
        $sql = Database::connect()->prepare("DELETE FROM nhanvien WHERE maNV = '".$maNV."'");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $resultado = self::read();
            return $resultado;
        }else{
            return "error";
       }
    }
}
