<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE") ; 


include_once "../models/db.php";
include_once "../models/Airline.php";
include 'function.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == "DELETE"){
        $deleteFlight = deleteFlight($_GET);
        // echo $deleteFlight;

}else{
    $data = [
        'status' => 404,
        'messange' => $requestMethod. 'Method not allowed',
    ];
    header("HTTP/1.0 404 Method not allowed");
    echo json_encode($data);
}
// $database = new Database();
// $db = $database->connect();
// $airline = new Airline($db);
// $read = $airline->read_json($db);
// $num = $read->rowCount();

//     if($num > 0) {
//         $question_array = [] ; 
//         $question_array['data'] = [] ;
//         while($row = $read -> fetch(PDO::FETCH_ASSOC)){
//             extract($row) ;
            
//             $airline_item = array(
//                 'maMB' => $maMB,
//                 'tenMayBay' => $tenMayBay,
//                 'hangMayBay' => $hangMayBay,
//                 'gheToiDa' => $gheToiDa
//             );
//             array_push($question_array['data'], $airline_item);


//         }
//         http_response_code(202);
//         echo json_encode(array("message" => "Success!."));
//         echo json_encode($question_array);

//     }else {
//         http_response_code(404);
//         echo json_encode(
//         array("message" => "No record found.")
// );
//     }
?>
