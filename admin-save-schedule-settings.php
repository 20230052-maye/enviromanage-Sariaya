<?php
session_start();

header('Content-Type: application/json');

// Admin only
if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'admin'
){
    echo json_encode([
        "success"=>false,
        "message"=>"Unauthorized access."
    ]);
    exit;
}

// Database
$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if($conn->connect_error){
    echo json_encode([
        "success"=>false,
        "message"=>"Database connection failed."
    ]);
    exit;
}

// Read JSON
$input = json_decode(file_get_contents("php://input"), true);

$hours = isset($input["max_schedule_hours"])
    ? (int)$input["max_schedule_hours"]
    : 0;

// Validation
if($hours < 1 || $hours > 24){

    echo json_encode([
        "success"=>false,
        "message"=>"Maximum duration must be between 1 and 24 hours."
    ]);

    exit;
}

// Update
$stmt = $conn->prepare("
    UPDATE system_settings
    SET setting_value = ?
    WHERE setting_key = 'max_schedule_hours'
");

$value = (string)$hours;

$stmt->bind_param("s",$value);

if($stmt->execute()){

    echo json_encode([
        "success"=>true,
        "message"=>"Schedule settings updated successfully."
    ]);

}else{

    echo json_encode([
        "success"=>false,
        "message"=>"Unable to save settings."
    ]);

}

$stmt->close();
$conn->close();