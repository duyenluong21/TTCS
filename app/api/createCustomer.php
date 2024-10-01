<?php
    $conn = mysqli_connect("localhost","root" ,"") ;
    mysqli_select_db($conn,"quanlymaybay") ;


    $email = $_POST['email'] ; 
    $password = $_POST['password'];
    $fullname = $_POST['fullname'];
    $ngaysinh = $_POST['ngaysinh'] ;

    $sql = "INSERT INTO `account_kh`(`email`,`password`,`fullname`,`ngaysinh`)
    VALUES ('$email', '$password' , '$fullname' , '$ngaysinh')";

    if(mysqli_query($conn, $sql)){
        $result["success"] = "1" ;
        $result["message"] = "success" ;
        echo json_encode($result);
        mysqli_close($conn);
    }else{
        $result["success"] = "0" ;
        $result["message"] = "error" ;
        echo json_encode($result);
        mysqli_close($conn);
    }

    
?>