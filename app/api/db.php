<?php

class Database
{
	public $host= "localhost" ;
	public $user="root";
	public $pass="";
	public $database="quanlymaybay";
	public $conn ;
  public function __construct()
    {

        $this->host = 'localhost';
        $this->user = 'root';
        $this->pass = '';
        $this->database = 'quanlymaybay';

    }

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