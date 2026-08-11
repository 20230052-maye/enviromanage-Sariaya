<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
include "db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila'); // ensure time is correct

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['email'])) {
        throw new Exception("Invalid request.");
    }

    $email = trim($_POST['email']);

    // --- Check if user exists ---
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['error' => "Email not found."]);
        exit;
    }

    $user = $result->fetch_assoc();
    $user_id = $user['id'];

    // --- Generate new OTP ---
    $plain_code = rand(100000, 999999);
    $hashed_code = password_hash($plain_code, PASSWORD_DEFAULT);
    $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes')); // 10 mins validity

    // --- Insert new OTP into password_reset_codes ---
    $stmt = $conn->prepare("
        INSERT INTO password_reset_codes (user_id, code, expires_at, used, attempts)
        VALUES (?, ?, ?, 0, 0)
    ");
    $stmt->bind_param("iss", $user_id, $hashed_code, $expires_at);
    $stmt->execute();
    $otp_id = $stmt->insert_id;
    $stmt->close();

    $_SESSION['reset_email'] = $email;

    error_log("Resend OTP generated: ID {$otp_id}, user_id={$user_id}, expires_at={$expires_at}, code={$plain_code}");

    // --- Send email via PHPMailer ---
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'dianapanugabsit@gmail.com';
    $mail->Password   = 'fvheirmeqpauyqnx';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    $mail->setFrom('dianapanugabsit@gmail.com', 'EnviroManage');
    $mail->addAddress($email, $user['username']);
    $mail->isHTML(true);
    $mail->Subject = 'EnviroManage Password Reset Code';
    $mail->Body    = "
        Hello {$user['username']},<br><br>
        Your new verification code is:<br>
        <h2>{$plain_code}</h2>
        This code will expire in 10 minutes.
    ";

    $mail->send();

    echo json_encode([
        'success' => "A new verification code has been sent! Check your email."
    ]);
    exit;

} catch (Exception $e) {
    error_log("Error in resend-otp-handler: " . $e->getMessage());

    echo json_encode([
        'error' => "Failed to resend verification code. Please try again later."
    ]);
    exit;
}
?>