<?php
header('Content-Type: application/json');
session_start();

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$truck_id = (int)$data['id'];
$reassign_to = isset($data['reassign_to']) ? (int)$data['reassign_to'] : null;

$conn->begin_transaction();

try {

    // 1. Get collector assigned to truck
    $stmt = $conn->prepare("SELECT collector_id FROM trucks WHERE id = ?");
    $stmt->bind_param("i", $truck_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $truck = $result->fetch_assoc();
    $stmt->close();

    $collector_id = $truck['collector_id'] ?? null;

    // 2. Handle collector reassignment or unassign
    if (!empty($collector_id)) {

        if ($reassign_to) {

            // assign collector to new truck
            $stmt = $conn->prepare("UPDATE trucks SET collector_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $collector_id, $reassign_to);
            $stmt->execute();
            $stmt->close();

        } else {

            // just unassign
           $stmt = $conn->prepare("UPDATE trucks SET collector_id = NULL WHERE id = ?");
$stmt->bind_param("i", $truck_id);
$stmt->execute();
$stmt->close();
        }
    }

    // 3. Delete truck
    $stmt = $conn->prepare("DELETE FROM trucks WHERE id = ?");
    $stmt->bind_param("i", $truck_id);
    $stmt->execute();

    if ($stmt->affected_rows <= 0) {
        throw new Exception("Truck not found");
    }

    $stmt->close();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => $reassign_to
            ? 'Truck deleted and collector reassigned'
            : 'Truck deleted and collector unassigned'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>