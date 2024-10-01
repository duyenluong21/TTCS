<?php
require_once('model.php');
require_once 'db.php';
class Airport extends Database
{
    //read db 
    public function read_json($db)
    {   
        $this -> conn = $db ; 
        $sqlp = "SELECT * FROM sanbay";
        $stmt = $this->conn->prepare($sqlp);
        $stmt->execute();
        return $stmt;
        
    }
    public function create_json($db){
        $this -> conn = $db ; 
        $this->maSanBay =htmlspecialchars(strip_tags($this->maSanBay));
        $this->tenSanBay =htmlspecialchars(strip_tags($this->tenSanBay));
        $this->diaDiem =htmlspecialchars(strip_tags($this->diaDiem));
        $sqlQuery = "INSERT INTO sanbay
        SET maSanBay = '".$this->maSanBay."',tenSanBay = '".$this->tenSanBay."',
        hangMayBay = '".$this->diaDiem."' "; 
         $stmt = $this->conn->prepare($sqlQuery);
         if($stmt -> execute()){
            return true ;
         }
         print("Error %.\n".$stmt->error) ; 
         return false;
    }
    public function read()
    {
        $sqlp = Database::connect()->prepare("SELECT * FROM sanbay");
        $sqlp->execute();
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC);
    }
    //insert db
    public function insert(
        $maSanBay,$tenSanBay,
        $diaDiem,
    ) {
        
        $sql = Database::connect()->prepare("INSERT INTO sanbay(maSanBay,tenSanBay,diaDiem)
        VALUES('$maSanBay','$tenSanBay','$diaDiem') ");
        if ($sql->execute()) {
            $result = self::read();
            return $result;
        }
    }
    public function edit($maSanBay){
        $sql = Database::connect()->prepare("SELECT * FROM sanbay WHERE maSanBay = '".$maSanBay."'");
        if ($sql->execute()) {
            return $array = $sql->fetchAll(PDO::FETCH_ASSOC);
    
        }else{
            return "error";
        }
    }
    public function update_airport($maSanBay,$tenSanBay, $diaDiem){
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
    public function delete_airport($maSanBay){
        $sql = Database::connect()->prepare("DELETE FROM sanbay WHERE maSanBay = '".$maSanBay."'");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $resultado = self::read();
            return $resultado;
        }else{
            return "error";
       }
    }
}
