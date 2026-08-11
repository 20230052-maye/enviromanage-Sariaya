<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
date_default_timezone_set('Asia/Manila');

header("Content-Type: application/json");

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'collector'
) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access."
    ]);
    exit;
}


$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);


if ($conn->connect_error) {

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);

    exit;
}


// MySQL timezone
$conn->query("SET time_zone = '+08:00'");


// ============================
// GET POST DATA
// ============================

$schedule_id     = intval($_POST['schedule_id'] ?? 0);
$barangay        = trim($_POST['barangay'] ?? '');
$street          = trim($_POST['street'] ?? '');
$status          = trim($_POST['status'] ?? '');
$collection_date = trim($_POST['collection_date'] ?? '');



// ============================
// BASIC VALIDATION
// ============================

$allowedStatus = [
    "Pending",
    "In Progress",
    "Completed",
    "Incomplete"
];


if (
    $schedule_id <= 0 ||
    $barangay === "" ||
    $street === "" ||
    $collection_date === "" ||
    !in_array($status, $allowedStatus)
) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid collection data.",
        "debug" => [
            "schedule_id"=>$schedule_id,
            "barangay"=>$barangay,
            "street"=>$street,
            "status"=>$status,
            "collection_date"=>$collection_date
        ]
    ]);

    exit;
}



// ============================
// ONLY TODAY CAN BE UPDATED
// ============================

$todayPH = date("Y-m-d");


if ($collection_date !== $todayPH) {

    echo json_encode([
        "success" => false,
        "message" => "Only today's collection can be updated."
    ]);

    exit;
}



// ============================
// CHECK EXISTING RECORD
// ============================

$check = $conn->prepare("
    SELECT id
    FROM collection_progress
    WHERE schedule_id = ?
      AND barangay = ?
      AND street = ?
      AND collection_date = ?
");


if(!$check){

    echo json_encode([
        "success"=>false,
        "message"=>$conn->error
    ]);

    exit;
}


$check->bind_param(
    "isss",
    $schedule_id,
    $barangay,
    $street,
    $collection_date
);


$check->execute();

$result = $check->get_result();



// ============================
// UPDATE EXISTING
// ============================

if($result->num_rows > 0){


    $row = $result->fetch_assoc();

    $updated_at = date("Y-m-d H:i:s");


    $update = $conn->prepare("
        UPDATE collection_progress
        SET
            status = ?,
            updated_at = ?
        WHERE id = ?
    ");


    $update->bind_param(
        "ssi",
        $status,
        $updated_at,
        $row['id']
    );


    if($update->execute()){


        echo json_encode([
            "success"=>true,
            "action"=>"updated",
            "progress_id"=>$row['id'],
            "message"=>"Collection progress updated successfully."
        ]);


    }else{


        echo json_encode([
            "success"=>false,
            "message"=>$update->error
        ]);

    }



}



// ============================
// INSERT NEW RECORD
// ============================

else{


    $insert = $conn->prepare("
        INSERT INTO collection_progress
        (
            schedule_id,
            barangay,
            street,
            collection_date,
            status
        )
        VALUES
        (
            ?,?,?,?,?
        )
    ");


    $insert->bind_param(
        "issss",
        $schedule_id,
        $barangay,
        $street,
        $collection_date,
        $status
    );



    if($insert->execute()){


        echo json_encode([
            "success"=>true,
            "action"=>"inserted",
            "progress_id"=>$conn->insert_id,
            "message"=>"Collection progress saved successfully."
        ]);


    }else{


        echo json_encode([
            "success"=>false,
            "message"=>$insert->error
        ]);

    }


}


$conn->close();

?>