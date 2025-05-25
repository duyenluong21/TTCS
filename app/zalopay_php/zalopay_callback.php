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
  file_put_contents("post_data.txt", $postdata);
  $postdatajson = json_decode($postdata, true);
  file_put_contents("post_datajson.txt", $postdatajson["data"]);
  $mac = hash_hmac("sha256", $postdatajson["data"], $key2);


  $requestmac = $postdatajson["mac"];
  if (strcmp($mac, $requestmac) != 0) {
    $result["return_code"] = -1;
    $result["return_message"] = "mac not equal";
  } else {
    $datajson = json_decode($postdatajson["data"], true);
    $app_trans_id = $datajson["app_trans_id"];
    $stmt = $conn->prepare("UPDATE vedadat SET trangThai = 1 WHERE app_trans_id = ?");
    $stmt->bind_param("s", $app_trans_id);
    $stmt->execute();

    $result["return_code"] = 1;
    $result["return_message"] = "success";
  }
} catch (Exception $e) {
  $result["return_code"] = 0;
  $result["return_message"] = $e->getMessage();
}

echo json_encode($result);