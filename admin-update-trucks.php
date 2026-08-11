<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if(!$input || !isset($input['id'], $input['plate_no'])) {
    echo json_encode(['success'=>false,'message'=>'Invalid input']);
    exit;
}

$host = "localhost";
$user = "u820562602_fleurscents";
$pass = "Aa2RmDG?Pe0";
$db   = "u820562602_fleurscents_db";
$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error){
    echo json_encode(['success'=>false,'message'=>'DB connection failed']);
    exit;
}

$id = intval($input['id']);
$plate_no = $conn->real_escape_string($input['plate_no']);
$collector_id = isset($input['collector_id']) && $input['collector_id'] !== null ? intval($input['collector_id']) : NULL;

// 1️⃣ Unassign collector from other trucks
if($collector_id !== null){
    $conn->query("UPDATE trucks SET collector_id=NULL WHERE collector_id=$collector_id AND id<>$id");
}

// 2️⃣ Update truck safely
$sql = "UPDATE trucks SET plate_no='$plate_no', collector_id=" . ($collector_id !== null ? $collector_id : 'NULL') . " WHERE id=$id";

if($conn->query($sql)){
    echo json_encode(['success'=>true,'message'=>'Truck updated successfully.']);
} else {
    echo json_encode(['success'=>false,'message'=>'Failed to update truck.']);
}
?>