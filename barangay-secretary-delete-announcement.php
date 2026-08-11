<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');


if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'barangay_secretary'
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


if($conn->connect_error){

    echo json_encode([
        "success"=>false,
        "message"=>"Database error"
    ]);

    exit;

}



$secretaryId = $_SESSION['user_id'];

$announcementId = intval($_POST['id'] ?? 0);



if($announcementId <= 0){

    echo json_encode([
        "success"=>false,
        "message"=>"Invalid announcement"
    ]);

    exit;

}



/*
 CHECK IF ALREADY DELETED BY THIS SECRETARY
*/

$check = $conn->prepare("
SELECT id 
FROM secretary_deleted_announcements
WHERE secretary_id=? 
AND announcement_id=?
");


$check->bind_param(
    "ii",
    $secretaryId,
    $announcementId
);


$check->execute();

$result=$check->get_result();



if($result->num_rows > 0){

    echo json_encode([
        "success"=>false,
        "message"=>"Already deleted"
    ]);

    exit;

}



/*
 INSERT PERSONAL DELETE
*/

$stmt=$conn->prepare("
INSERT INTO secretary_deleted_announcements
(
secretary_id,
announcement_id,
deleted_at
)
VALUES
(
?,
?,
NOW()
)
");



$stmt->bind_param(
    "ii",
    $secretaryId,
    $announcementId
);



if($stmt->execute()){


    echo json_encode([
        "success"=>true,
        "message"=>"Announcement removed from your account."
    ]);


}else{


    echo json_encode([
        "success"=>false,
        "message"=>"Delete failed."
    ]);

}



$stmt->close();
$conn->close();
