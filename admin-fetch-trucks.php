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
            t.id,
            t.plate_no,
            t.collector_id,
            t.created_at,

            u.first_name,
            u.middle_initial,
            u.last_name

        FROM trucks t
        LEFT JOIN users u 
            ON t.collector_id = u.id 
            AND u.role = 'collector'
        ORDER BY t.id DESC";

$result = $conn->query($sql);

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {

        // Safe collector ID
        $collector_id = isset($row['collector_id']) ? (int)$row['collector_id'] : null;

        // Build collector full name safely
        $first = $row['first_name'] ?? '';
        $middle = !empty($row['middle_initial']) ? $row['middle_initial'] . '. ' : '';
        $last = $row['last_name'] ?? '';

        $collector_name = trim($first . ' ' . $middle . ' ' . $last);

        if ($collector_name === '') {
            $collector_name = null;
        }

        $data[] = [
            'id' => (int)$row['id'],
            'plate_no' => $row['plate_no'],
            'collector_id' => $collector_id,
            'collector_name' => $collector_name,
            'created_at' => $row['created_at']
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