<?php

$result = [];
$host = "localhost";
$usernam = "root";
$password = "";
$dbname = "quanlymaybay";

$conn = mysqli_connect($host, $usernam, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Kết nối thất bại: " . $conn->connect_error]));
}

try {
  $key2 = "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz";
  $postdata = file_get_contents('php://input');
  $postdatajson = json_decode($postdata, true);
  $mac = hash_hmac("sha256", $postdatajson["data"], $key2);


  $requestmac = $postdatajson["mac"];
  if (strcmp($mac, $requestmac) != 0) {
    $result["return_code"] = -1;
    $result["return_message"] = "mac not equal";
  } else {
      $datajson = json_decode($postdatajson["data"], true);
      $app_trans_id = $datajson["app_trans_id"];
      $zp_trans_id = $datajson["zp_trans_id"];
      file_put_contents("app_trans_id.txt", $app_trans_id);

      $stmt = $conn->prepare("UPDATE vedadat SET trangThai = 1, zp_trans_id = ? WHERE app_trans_id = ?");
      $stmt->bind_param("ss", $zp_trans_id, $app_trans_id);
      $stmt->execute();

      $result["return_code"] = 1;
      $result["return_message"] = "success";
  }
} catch (Exception $e) {
  $result["return_code"] = 0;
  $result["return_message"] = $e->getMessage();
}

echo json_encode($result);