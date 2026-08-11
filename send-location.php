<?php
// send-location.php
include 'db_connection.php'; // your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $truck_id = $_POST['truck_id'] ?? null;
    $lat = $_POST['lat'] ?? null;
    $lng = $_POST['lng'] ?? null;
    $capacity = $_POST['capacity'] ?? 0;
    $location = $_POST['location'] ?? '';

    if ($truck_id && $lat && $lng) {
        $stmt = $conn->prepare("
            INSERT INTO truck_locations (truck_id, lat, lng, capacity, location, last_updated)
            VALUES (?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                lat = VALUES(lat),
                lng = VALUES(lng),
                capacity = VALUES(capacity),
                location = VALUES(location),
                last_updated = NOW()
        ");
        $stmt->bind_param("iddis", $truck_id, $lat, $lng, $capacity, $location);
        $stmt->execute();

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}