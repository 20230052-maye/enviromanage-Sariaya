<?php
header('Content-Type: application/json');

session_start();

require __DIR__ . '/vendor/autoload.php';
include "db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing user ID"
    ]);
    exit;
}

$id = (int)$data['id'];
$status = (int)($data['is_logged_in'] ?? 0);
$reason = trim($data['reason'] ?? '');

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

/*
    is_logged_in:
    1 = Active
    0 = Deactivated
*/

// ======================================
// GET USER INFO
// ======================================

$userStmt = $conn->prepare("
    SELECT 
        username,
        first_name,
        last_name,
        middle_initial,
        email,
        role
    FROM users
    WHERE id = ?
    LIMIT 1
");

$userStmt->bind_param("i", $id);
$userStmt->execute();

$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "User not found"
    ]);

    exit;
}

$user = $userResult->fetch_assoc();

$userStmt->close();

$username = $user['username'];
$fullname = $user['fullname'];
$email = $user['email'];
$role = strtolower($user['role']);

// ======================================
// DEACTIVATE
// ======================================

if ($status === 0) {

    $stmt = $conn->prepare("
        UPDATE users 
        SET is_logged_in = 0,
            deactivation_reason = ?
        WHERE id = ?
    ");

    $stmt->bind_param("si", $reason, $id);

} else {

    // ======================================
    // ACTIVATE
    // ======================================

    $stmt = $conn->prepare("
        UPDATE users 
        SET is_logged_in = 1,
            deactivation_reason = NULL
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
}

// ======================================
// EXECUTE UPDATE
// ======================================

if ($stmt->execute()) {

    // ======================================
    // SEND EMAIL ONLY:
    // - resident role
    // - when deactivated
    // ======================================

    if ($status === 0 && $role === 'resident') {

        try {

            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dianapanugabsit@gmail.com';
            $mail->Password   = 'fvheirmeqpauyqnx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('dianapanugabsit@gmail.com', 'EnviroManage');

            $mail->addAddress($email, $username);

            $mail->isHTML(true);

            $mail->Subject = 'EnviroManage Account Deactivation Notice';

            $mail->Body = "
                Hello {$fullname},<br><br>

                Your EnviroManage account has been temporarily 
                <strong>deactivated</strong> due to the following reason:<br><br>

                <div style='padding:10px; background:#f5f5f5; border-left:4px solid red;'>
                    {$reason}
                </div>

                <br>

                You are given a grace period of 
                <strong>15–30 days</strong> to comply with the 
                rules and regulations implemented by MENRO.<br><br>

                Please visit the MENRO Office and look for the 
                <strong>EnviroManage Administrator</strong> 
                to request possible reactivation of your account.<br><br>

                Failure to comply within the given timeframe may result in:
                <ul>
                    <li>Permanent account deletion</li>
                    <li>Continued account deactivation until further notice</li>
                </ul>

                Thank you for your cooperation.<br><br>

                — EnviroManage System
            ";

            $mail->send();

        } catch (Exception $e) {

            error_log("Deactivation Email Error: " . $e->getMessage());
        }
    }

    echo json_encode([
        "success" => true,
        "newStatus" => $status
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>