<?php
session_start();
include "db.php";
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['error'=>"Invalid request."]);
    exit();
}

$email = $_POST['email'] ?? '';
$code = $_POST['code'] ?? '';

if(empty($email) || empty($code)){
    echo json_encode(['error'=>"Email or code missing."]);
    exit();
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows===0){
    echo json_encode(['error'=>"User not found."]);
    exit();
}
$user = $result->fetch_assoc();
$user_id = $user['id'];

$stmt = $conn->prepare("
    SELECT id, code, expires_at, attempts
    FROM password_reset_codes
    WHERE user_id=? AND used=0 AND expires_at>NOW()
    ORDER BY id DESC LIMIT 1
");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result=$stmt->get_result();
if($result->num_rows===0){
    echo json_encode(['error'=>"No active code found. Request a new one."]);
    exit();
}

$row=$result->fetch_assoc();
$otp_id=$row['id'];
$hashed_code=$row['code'];
$attempts=$row['attempts'];

if($attempts>=5){
    echo json_encode(['error'=>"Too many invalid attempts. Request a new code."]);
    exit();
}

if(!password_verify($code,$hashed_code)){
    $stmt=$conn->prepare("UPDATE password_reset_codes SET attempts=attempts+1 WHERE id=?");
    $stmt->bind_param("i",$otp_id);
    $stmt->execute();
    echo json_encode(['error'=>"Invalid verification code."]);
    exit();
}

// mark OTP as used
$stmt=$conn->prepare("UPDATE password_reset_codes SET used=1 WHERE id=?");
$stmt->bind_param("i",$otp_id);
$stmt->execute();

$_SESSION['verified_reset']=true;
echo json_encode(['success'=>"Code verified! You may now reset your password."]);
exit();
?>