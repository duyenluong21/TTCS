<?php
require_once('model.php');
require_once 'db.php';
class Airline extends Database
{
    //read db 
    public function read_json($db)
    {   
        $this -> conn = $db ; 
        $sqlp = "SELECT * FROM maybay";
        $stmt = $this->conn->prepare($sqlp);
        $stmt->execute();
        return $stmt;
        
    }
    public function create_json($db){
        $this -> conn = $db ; 
        $this->maMB =htmlspecialchars(strip_tags($this->maMB));
        $this->tenMayBay =htmlspecialchars(strip_tags($this->tenMayBay));
        $this->hangMayBay =htmlspecialchars(strip_tags($this->hangMayBay));
        $this->gheToiDa =htmlspecialchars(strip_tags($this->gheToiDa));
        $sqlQuery = "INSERT INTO maybay
        SET maMB = '".$this->maMB."',tenMayBay = '".$this->tenMayBay."',
        hangMayBay = '".$this->hangMayBay."',gheToiDa = '".$this->gheToiDa."' "; 
         $stmt = $this->conn->prepare($sqlQuery);
         if($stmt -> execute()){
            return true ;
         }
         print("Error %.\n".$stmt->error) ; 
         return false;
    }
    public function read()
    {
        $sqlp = Database::connect()->prepare("SELECT * FROM maybay");
        $sqlp->execute();
        return $array = $sqlp->fetchAll(PDO::FETCH_ASSOC);
    }
    //insert db
    public function insert(
        $tenMayBay,$hangMayBay,
        $gheToiDa,
    ) {
        
        $sql = Database::connect()->prepare("INSERT INTO maybay(tenMayBay,hangMayBay,gheToiDa)
        VALUES('$tenMayBay','$hangMayBay','$gheToiDa') ");
        if ($sql->execute()) {
            $result = self::read();
            return $result;
        }
    }
    public function edit($maMB){
        $sql = Database::connect()->prepare("SELECT * FROM maybay WHERE maMB = '".$maMB."'");
        if ($sql->execute()) {
            return $array = $sql->fetchAll(PDO::FETCH_ASSOC);
    
        }else{
            return "error";
        }
    }
    public function update_airline($maMB, $tenMayBay,$hangMayBay, $gheToiDa){
        $sql = Database::connect()->prepare("UPDATE maybay SET tenMayBay='".$tenMayBay."',hangMayBay='".$hangMayBay."',gheToiDa='".$gheToiDa."'
         WHERE maMB='".$maMB."' ");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $resultado = self::read();
            return $resultado;
        }else{
            return "error";
       }
        
    }
    public function delete_airline($maMB){
        $sql = Database::connect()->prepare("DELETE FROM maybay WHERE maMB = '".$maMB."'");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $resultado = self::read();
            return $resultado;
        }else{
            return "error";
       }
    }
}
