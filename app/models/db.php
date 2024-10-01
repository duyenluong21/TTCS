<?php

class Database
{
	private $host= "localhost" ;
	private $user="root";
	private $pass="";
	private $database="quanlymaybay";
	private $conn ;
  public function connect(){
    $this -> conn = null ; 
    try {
      $this -> conn = new PDO("mysql:host=" .$this->host. ";dbname=" .$this->database."", $this ->user, $this ->pass);
      // set the PDO error mode to exception
      $this -> conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      echo "";
    } catch(PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
    return  $this -> conn;
  }
}
?>