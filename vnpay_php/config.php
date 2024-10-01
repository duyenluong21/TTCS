<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$host = "localhost";
$usernam = "root";
$password = "";
$dbname = "quanlymaybay";

$conn = mysqli_connect($host, $usernam,$password,$dbname);

if(!$conn){
    die("Connect Failed: " . mysqli_connect_error());
}
$vnp_TmnCode = "D7JLW8KN"; //Mã định danh merchant kết nối (Terminal Id)
$vnp_HashSecret = "ZFIRJBMBFAINNHJWFUHYJYQXPFIIVOTY"; //Secret key
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "http://192.168.1.8/TTCS/vnpay_php/vnpay_return.php";
$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
$apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
//Config input format
//Expire
$startTime = date("YmdHis");
$expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));
