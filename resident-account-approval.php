<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli(
     "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
   $application_id = "APP" . str_pad($row['id'], 5, "0", STR_PAD_LEFT);
} else {
    $application_id = "APP000";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Account Pending Approval</title>

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Roboto',sans-serif;
}

body{
    background:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.container{
    text-align:center;
    max-width:520px;
    padding:40px;
}

img{
    width:120px;
    margin-bottom:25px;
}

h2{
    color:#1D4525;
    margin-bottom:15px;
}

p{
    color:#555;
    font-size:16px;
    line-height:1.7;
}

.status{
    display:inline-block;
    margin-top:25px;
    padding:12px 24px;
    background:#fff8e1;
    color:#d68910;
    border:1px solid #f4d03f;
    border-radius:30px;
    font-weight:600;
}

.logout{
    display:inline-block;
    margin-top:30px;
    text-decoration:none;
    color:white;
    background:#1D4525;
    padding:12px 30px;
    border-radius:6px;
}

.logout:hover{
    background:#163519;
}

</style>

</head>

<body>

<div class="container">

    <img src="assets/logo-512.png">

    <h2>Account Pending Approval</h2>

    <p>
        Thank you for registering with EnviroManage.
    </p>

    <p style="margin-top:15px;">
        Your account is currently being reviewed by your assigned
        Barangay Secretary.
    </p>

    <p style="margin-top:15px;">
        You will be able to access the Resident Dashboard once your
        account has been approved.
    </p>

    <p style="margin-top:10px; font-weight:600; color:#1D4525;">
    Application ID: <?php echo $application_id; ?>
</p>

    <div class="status">
        Status: Pending Approval
    </div>

    <br>

    <a href="logout.php" class="logout">
        Logout
    </a>

</div>

</body>
</html>