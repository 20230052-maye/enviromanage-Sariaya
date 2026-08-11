<?php

session_start();

header("Content-Type: application/json");


if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] != "barangay_secretary"
){

    echo json_encode([
        "success"=>false,
        "message"=>"Unauthorized"
    ]);

    exit;

}



$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);


$conn->set_charset("utf8mb4");



$secretaryID = $_SESSION['user_id'];


$id = intval($_POST['id'] ?? 0);

$remarks = trim($_POST['remarks'] ?? '');



if($id <= 0){

    echo json_encode([
        "success"=>false,
        "message"=>"Invalid complaint."
    ]);

    exit;

}



if($remarks == ""){

    echo json_encode([
        "success"=>false,
        "message"=>"Remarks are required."
    ]);

    exit;

}




$stmt = $conn->prepare("
UPDATE complaints

SET

validation_status = 'Approved',

action_status = 'Forwarded',

remarks = ?,

reviewed_by = ?,

reviewed_at = NOW()

WHERE id = ?

AND validation_status = 'Under Review'

");


$stmt->bind_param(
"sii",
$remarks,
$secretaryID,
$id
);



$stmt->execute();



if($stmt->affected_rows > 0){


    echo json_encode([

        "success"=>true,

        "message"=>"Complaint forwarded to MENRO."

    ]);



}else{


    echo json_encode([

        "success"=>false,

        "message"=>"Complaint cannot be forwarded."

    ]);

}



?>