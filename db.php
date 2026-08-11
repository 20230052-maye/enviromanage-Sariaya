<?php
$host = "localhost";
$user = "u823857209_enviromanage";
$pass = "Enviromanage4322";
$db   = "u823857209_enviromanage";


$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


/* ✅ FORCE PH TIMEZONE FOR MYSQL */
$conn->query("SET time_zone = '+08:00'");
?>