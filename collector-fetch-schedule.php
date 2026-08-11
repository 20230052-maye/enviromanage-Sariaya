<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);
    exit;
}

if (empty($_GET['date'])) {
    echo json_encode([
        "success" => false,
        "message" => "Date is required."
    ]);
    exit;
}

$date = $_GET['date'];
$day = date("l", strtotime($date));
$collectorId = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT
        s.id,
        s.barangay,
        s.day_of_week,
        s.start_time,
        s.end_time,
        s.garbage_type,
        u.first_name,
        u.middle_initial,
        u.last_name
    FROM schedules s
    INNER JOIN trucks t
        ON s.truck_id = t.id
    INNER JOIN users u
        ON t.collector_id = u.id
    WHERE s.day_of_week = ?
      AND t.collector_id = ?
    ORDER BY s.start_time ASC
");

$stmt->bind_param("si", $day, $collectorId);
$stmt->execute();

$result = $stmt->get_result();

$schedules = [];

while ($row = $result->fetch_assoc()) {

    $collectorName = $row["first_name"];

    if (!empty($row["middle_initial"])) {
        $collectorName .= " " . strtoupper($row["middle_initial"]) . ".";
    }

    $collectorName .= " " . $row["last_name"];

   $schedules[] = [
    "id" => $row["id"],
    "barangay" => $row["barangay"],
    "day" => $row["day_of_week"],

    // Raw values
    "start_time" => $row["start_time"],
    "end_time"   => $row["end_time"],

    // Formatted value
    "time" =>
        date("g:i A", strtotime($row["start_time"])) .
        " - " .
        date("g:i A", strtotime($row["end_time"])),

    "garbage_type" => $row["garbage_type"],
    "collector_name" => $collectorName
];
}

echo json_encode([
    "success" => true,
    "date" => $date,
    "day" => $day,
    "schedules" => $schedules
]);

$stmt->close();
$conn->close();
?>