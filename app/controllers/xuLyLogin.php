<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $username = $_POST["username"]; 
   $passw = $_POST["passw"];
	
   if ($username === "admin" && $passw === "123456") {	 
     header("location: /home");
   } else{
        echo "<h2><span style='color:red'>Sai tài khoản hoặc mật khẩu</span></h2>";
   }
}
?>