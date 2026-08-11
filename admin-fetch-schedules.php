<?php
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "data" => []
    ]);
    exit;
}

$sql = "SELECT 
            s.id,
            s.barangay,
            s.day_of_week,
            s.start_time,
            s.end_time,
            s.garbage_type,
            s.truck_id,
            s.created_at,

            t.plate_no,

            u.first_name,
            u.middle_initial,
            u.last_name

        FROM schedules s
        LEFT JOIN trucks t 
            ON s.truck_id = t.id
        LEFT JOIN users u 
            ON t.collector_id = u.id
        ORDER BY s.id DESC";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error,
        "sql" => $sql
    ]);
    exit;
}

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {

        $first = $row['first_name'] ?? '';
        $middle = !empty($row['middle_initial']) ? $row['middle_initial'] . '. ' : '';
        $last = $row['last_name'] ?? '';

        $collector_name = trim($first . ' ' . $middle . ' ' . $last);

        if ($collector_name === '') {
            $collector_name = null;
        }

        $truck_display = $row['plate_no'];

        if ($collector_name) {
            $truck_display .= " - " . $collector_name;
        }

        $data[] = [
            "id" => (int)$row['id'],
            "barangay" => $row['barangay'],
            "day_of_week" => $row['day_of_week'],
            "start_time" => $row['start_time'],
            "end_time" => $row['end_time'],
            "garbage_type" => $row['garbage_type'],
            "truck_id" => (int)$row['truck_id'],
            "truck" => $truck_display,
            "created_at" => $row['created_at']
        ];
    }

    echo json_encode([
        "success" => true,
        "data" => $data
    ]);

} else {
    echo json_encode([
        "success" => false,
        "message" => "Query failed",
        "data" => []
    ]);
}

$conn->close();
?>