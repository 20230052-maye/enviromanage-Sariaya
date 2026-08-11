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



if($id <= 0){

    echo json_encode([
        "success"=>false,
        "message"=>"Invalid complaint."
    ]);

    exit;

}




// GET SECRETARY BARANGAY

$stmt = $conn->prepare("
SELECT barangay
FROM users
WHERE id = ?
");


$stmt->bind_param(
"i",
$secretaryID
);


$stmt->execute();


$secretary =
$stmt->get_result()->fetch_assoc();



$barangay =
$secretary['barangay'];





// CHECK EXISTING UNDER REVIEW

$check = $conn->prepare("
SELECT rc.id
FROM resident_complaints rc
INNER JOIN users u
ON rc.resident_id = u.id
WHERE u.barangay = ?
AND rc.validation_status = 'Under Review'
LIMIT 1
");


$check->bind_param(
"s",
$barangay
);


$check->execute();



if($check->get_result()->num_rows > 0){


    echo json_encode([

        "success"=>false,

        "message"=>"Another complaint is currently under review."

    ]);


    exit;

}




// UPDATE SELECTED COMPLAINT

$update = $conn->prepare("
UPDATE resident_complaints

SET

validation_status = 'Under Review',

reviewed_by = ?,

reviewed_at = NOW()

WHERE id = ?

AND validation_status = 'Waiting'

");


$update->bind_param(
"ii",
$secretaryID,
$id
);



$update->execute();



if($update->affected_rows > 0){


    echo json_encode([

        "success"=>true,

        "message"=>"Complaint is now under review."

    ]);



}else{


    echo json_encode([

        "success"=>false,

        "message"=>"Complaint cannot be reviewed."

    ]);

}



?>