<?php
$host = "localhost";
$user = "u820562602_fleurscents";
$pass = "Aa2RmDG?Pe0";
$db   = "u820562602_fleurscents_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

/* ✅ FORCE PH TIMEZONE FOR MYSQL */
$conn->query("SET time_zone = '+08:00'");
?>