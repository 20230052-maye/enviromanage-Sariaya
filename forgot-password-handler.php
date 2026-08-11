<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
include "db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila'); // ensures correct time

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

    // --- Check for existing active OTP ---
    $stmt = $conn->prepare("
        SELECT id, code, expires_at
        FROM password_reset_codes
        WHERE user_id = ? AND used = 0 AND expires_at > NOW()
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $existing = $stmt->get_result();

    if ($existing->num_rows > 0) {
        $_SESSION['reset_email'] = $email;
        echo json_encode([
            'success' => "A verification code was already sent. Please check your email.",
            'redirect' => "verify-code.php"
        ]);
        exit;
    }

    // --- Generate new OTP ---
    $plain_code = rand(100000, 999999);
    $hashed_code = password_hash($plain_code, PASSWORD_DEFAULT);
    if (!$hashed_code) {
        throw new Exception("Failed to hash OTP code.");
    }

    $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes')); // DATETIME for MySQL

    // --- Insert new OTP ---
    $stmt = $conn->prepare("
        INSERT INTO password_reset_codes (user_id, code, expires_at, used, attempts)
        VALUES (?, ?, ?, 0, 0)
    ");
    $stmt->bind_param("iss", $user_id, $hashed_code, $expires_at);
    $stmt->execute();
    $stmt->close();

    $_SESSION['reset_email'] = $email;

    // --- Send email ---
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
        Your verification code is:<br>
        <h2>{$plain_code}</h2>
        This code will expire in 10 minutes.
    ";

    $mail->send();

    echo json_encode([
        'success' => "Verification code sent! Check your email.",
        'redirect' => "verify-code.php"
    ]);
    exit;

} catch (Exception $e) {
    error_log("forgot-password-handler error: " . $e->getMessage());

    echo json_encode([
        'success' => "Verification code generated! (Email not sent)",
        'redirect' => "verify-code.php"
    ]);
    exit;
}
?>