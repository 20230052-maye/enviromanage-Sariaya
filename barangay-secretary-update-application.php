<?php
session_start();
date_default_timezone_set('Asia/Manila');

header("Content-Type: application/json");

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'barangay_secretary'
){
    echo json_encode([
        "success"=>false,
        "message"=>"Unauthorized."
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

if($conn->connect_error){
    echo json_encode([
        "success"=>false,
        "message"=>"Database connection failed."
    ]);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$action = $_POST['action'] ?? "";
$reason = trim($_POST['reason'] ?? "");

if($id <= 0){
    echo json_encode([
        "success"=>false,
        "message"=>"Invalid application."
    ]);
    exit;
}

/* Only pending applications can be processed */

$stmt = $conn->prepare("
SELECT approval_status
FROM users
WHERE id=?
AND role='resident'
LIMIT 1
");

$stmt->bind_param("i",$id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if(!$user){
    echo json_encode([
        "success"=>false,
        "message"=>"Application not found."
    ]);
    exit;
}

if($user['approval_status'] !== 'pending'){
    echo json_encode([
        "success"=>false,
        "message"=>"Application has already been processed."
    ]);
    exit;
}

/* APPROVE */

if($action==="approve"){

    $stmt=$conn->prepare("
   UPDATE users
SET
    approval_status='approved',
    rejection_reason=NULL,
    approved_at=NOW()
WHERE id=?
    ");

    $stmt->bind_param("i",$id);

}

/* REJECT */

elseif($action==="reject"){

    $stmt=$conn->prepare("
    UPDATE users
SET
    approval_status='rejected',
    rejection_reason=?,
    rejected_at=NOW()
WHERE id=?
    ");

    $stmt->bind_param("si",$reason,$id);

}else{

    echo json_encode([
        "success"=>false,
        "message"=>"Invalid action."
    ]);
    exit;

}

if($stmt->execute()){

    echo json_encode([
        "success"=>true
    ]);

}else{

    echo json_encode([
        "success"=>false,
        "message"=>"Unable to update application."
    ]);

}