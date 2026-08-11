<?php

session_start();

require __DIR__ . '/vendor/autoload.php';
include "db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {

    // --------------------------------------------------
    // CHECK REQUEST
    // --------------------------------------------------

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['email'])) {
        throw new Exception("Invalid request.");
    }

    $email = trim($_POST['email']);


    // --------------------------------------------------
    // CHECK IF USER EXISTS
    // --------------------------------------------------

    $stmt = $conn->prepare("
        SELECT id, username
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {

        echo json_encode([
            'error' => 'Email not found.'
        ]);

        exit;
    }

    $user = $result->fetch_assoc();

    $user_id = $user['id'];
    $username = $user['username'];

    $stmt->close();


    // --------------------------------------------------
    // CHECK FOR EXISTING ACTIVE OTP
    // --------------------------------------------------

    $stmt = $conn->prepare("
        SELECT id, expires_at
        FROM password_reset_codes
        WHERE user_id = ?
        AND used = 0
        AND expires_at > NOW()
        ORDER BY id DESC
        LIMIT 1
    ");

    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $existing = $stmt->get_result();

    if ($existing->num_rows > 0) {

        $_SESSION['reset_email'] = $email;

        echo json_encode([
            'success' => 'A verification code was already sent. Please check your email.',
            'redirect' => 'verify-code.php'
        ]);

        exit;
    }

    $stmt->close();


    // --------------------------------------------------
    // GENERATE OTP
    // --------------------------------------------------

    $plain_code = random_int(100000, 999999);

    $hashed_code = password_hash(
        $plain_code,
        PASSWORD_DEFAULT
    );

    if (!$hashed_code) {
        throw new Exception("Failed to hash OTP code.");
    }

    $expires_at = date(
        'Y-m-d H:i:s',
        strtotime('+10 minutes')
    );


    // --------------------------------------------------
    // SEND EMAIL
    // --------------------------------------------------

    $mail = new PHPMailer(true);

    $mail->isSMTP();

    /*
     * TEMPORARY DEBUGGING
     *
     * This allows us to see the actual SMTP problem.
     * Remove SMTPDebug after the email works.
     */
    $mail->SMTPDebug = 2;

    $mail->Debugoutput = function ($str, $level) {
        error_log("PHPMailer [$level]: $str");
    };

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username = 'enviromngsariaya@gmail.com';

    /*
     * IMPORTANT:
     * Put your NEW Gmail App Password here.
     *
     * Do NOT use your normal Gmail password.
     */
    $mail->Password = 'izdafogfhwybkcjt';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    $mail->Port = 465;


    // --------------------------------------------------
    // EMAIL DETAILS
    // --------------------------------------------------

    $mail->setFrom(
        'enviromngsariaya@gmail.com',
        'EnviroManage'
    );

    $mail->addAddress(
        $email,
        $username
    );

    $mail->isHTML(true);

    $mail->Subject = 'EnviroManage Password Reset Code';

    $mail->Body = "
        <p>Hello {$username},</p>

        <p>
            Your EnviroManage password reset
            verification code is:
        </p>

        <h2>{$plain_code}</h2>

        <p>
            This code will expire in
            <strong>10 minutes</strong>.
        </p>

        <p>
            If you did not request a password reset,
            please ignore this email.
        </p>
    ";

    $mail->AltBody = "
        Hello {$username},

        Your EnviroManage password reset verification code is:

        {$plain_code}

        This code will expire in 10 minutes.

        If you did not request a password reset,
        please ignore this email.
    ";


    // --------------------------------------------------
    // SEND
    // --------------------------------------------------

    $mail->send();


    // --------------------------------------------------
    // ONLY SAVE OTP AFTER EMAIL SUCCESSFULLY SENDS
    // --------------------------------------------------

    $stmt = $conn->prepare("
        INSERT INTO password_reset_codes
        (
            user_id,
            code,
            expires_at,
            used,
            attempts
        )
        VALUES (?, ?, ?, 0, 0)
    ");

    if (!$stmt) {
        throw new Exception(
            "Database error while saving OTP: " . $conn->error
        );
    }

    $stmt->bind_param(
        "iss",
        $user_id,
        $hashed_code,
        $expires_at
    );

    if (!$stmt->execute()) {
        throw new Exception(
            "Failed to save verification code: " .
            $stmt->error
        );
    }

    $stmt->close();


    // --------------------------------------------------
    // SAVE EMAIL IN SESSION
    // --------------------------------------------------

    $_SESSION['reset_email'] = $email;


    // --------------------------------------------------
    // SUCCESS
    // --------------------------------------------------

    echo json_encode([
        'success' => 'Verification code sent! Check your email.',
        'redirect' => 'verify-code.php'
    ]);

    exit;


} catch (Exception $e) {

    // Log complete error to server error log
    error_log(
        "forgot-password-handler error: " .
        $e->getMessage()
    );


    // Return actual error to JavaScript
    echo json_encode([
        'error' => 'Email could not be sent.',
        'details' => $e->getMessage()
    ]);

    exit;
}
?>
```
