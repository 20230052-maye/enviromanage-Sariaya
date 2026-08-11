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


// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);


$conn->set_charset("utf8mb4");



$secretaryID = $_SESSION['user_id'];


$id = intval($_POST['id'] ?? 0);


$remarks = trim($_POST['remarks'] ?? "");


$reason = trim($_POST['reason'] ?? "");




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




if($reason == ""){

    echo json_encode([
        "success"=>false,
        "message"=>"Return reason is required."
    ]);

    exit;

}




$stmt = $conn->prepare("
UPDATE complaints

SET

validation_status = 'Rejected',

action_status = 'Returned',

remarks = ?,

admin_notes = ?,

reviewed_by = ?,

reviewed_at = NOW()


WHERE id = ?

AND validation_status = 'Under Review'

");



$stmt->bind_param(

"ssii",

$remarks,

$reason,

$secretaryID,

$id

);



$stmt->execute();




if($stmt->affected_rows > 0){


    echo json_encode([

        "success"=>true,

        "message"=>"Complaint returned to resident."

    ]);



}else{


    echo json_encode([

        "success"=>false,

        "message"=>"Complaint cannot be returned."

    ]);

}



?>