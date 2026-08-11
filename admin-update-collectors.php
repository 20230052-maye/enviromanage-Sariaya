<?php
header('Content-Type: application/json; charset=utf-8');

// =====================
// READ JSON INPUT
// =====================
$data = json_decode(file_get_contents('php://input'), true);

// =====================
// VALIDATE INPUT
// =====================
$collectorId = isset($data['id']) ? intval($data['id']) : 0;

$first_name = isset($data['first_name'])
    ? trim($data['first_name'])
    : '';

$middle_initial = isset($data['middle_initial'])
    ? trim($data['middle_initial'])
    : '';

$last_name = isset($data['last_name'])
    ? trim($data['last_name'])
    : '';

$phone = isset($data['phone'])
    ? trim($data['phone'])
    : '';

$assignedTruck = isset($data['truck_id']) &&
                 $data['truck_id'] !== ''
    ? intval($data['truck_id'])
    : null;

if (!$collectorId) {
    echo json_encode([
        'success' => false,
        'message' => 'Collector ID is required.'
    ]);
    exit;
}

if (!$first_name || !$last_name || !$phone) {
    echo json_encode([
        'success' => false,
        'message' => 'First name, last name, and contact are required.'
    ]);
    exit;
}

// =====================
// DATABASE CONNECTION
// =====================
$host = "localhost";
$user = "u823857209_enviromanage";
$pass = "Enviromanage4322";
$db   = "u823857209_enviromanage";


$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.'
    ]);
    exit;
}

// =====================
// CHECK COLLECTOR EXISTS
// =====================
$stmtCheck = $conn->prepare("
    SELECT id
    FROM users
    WHERE id = ?
    AND role = 'collector'
");

$stmtCheck->bind_param("i", $collectorId);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows === 0) {

    echo json_encode([
        'success' => false,
        'message' => 'Collector not found.'
    ]);

    $stmtCheck->close();
    $conn->close();
    exit;
}

$stmtCheck->close();

// =====================
// UNASSIGN OLD TRUCK
// =====================
$stmtClearOld = $conn->prepare("
    UPDATE trucks
    SET collector_id = NULL
    WHERE collector_id = ?
");

$stmtClearOld->bind_param("i", $collectorId);
$stmtClearOld->execute();
$stmtClearOld->close();

// =====================
// ASSIGN NEW TRUCK
// =====================
if ($assignedTruck !== null) {

    // Check if truck already belongs to another collector
    $stmtTruckCheck = $conn->prepare("
        SELECT collector_id
        FROM trucks
        WHERE id = ?
    ");

    $stmtTruckCheck->bind_param("i", $assignedTruck);
    $stmtTruckCheck->execute();

    $resultTruck = $stmtTruckCheck->get_result();
    $truckRow = $resultTruck->fetch_assoc();

    $stmtTruckCheck->close();

    if (
        $truckRow &&
        $truckRow['collector_id'] !== null &&
        $truckRow['collector_id'] != $collectorId
    ) {

        echo json_encode([
            'success' => false,
            'message' => 'Truck is already assigned to another collector.'
        ]);

        $conn->close();
        exit;
    }

    // Assign truck to collector
    $stmtAssignTruck = $conn->prepare("
        UPDATE trucks
        SET collector_id = ?
        WHERE id = ?
    ");

    $stmtAssignTruck->bind_param(
        "ii",
        $collectorId,
        $assignedTruck
    );

    $stmtAssignTruck->execute();
    $stmtAssignTruck->close();
}

// =====================
// UPDATE USER INFO
// =====================
$stmtUpdate = $conn->prepare("
    UPDATE users
    SET
        first_name = ?,
        middle_initial = ?,
        last_name = ?,
        phone = ?,
        assigned_truck_id = ?
    WHERE id = ?
");

$stmtUpdate->bind_param(
    "ssssii",
    $first_name,
    $middle_initial,
    $last_name,
    $phone,
    $assignedTruck,
    $collectorId
);

$success = $stmtUpdate->execute();

$stmtUpdate->close();

// =====================
// RESPONSE
// =====================
if ($success) {

    echo json_encode([
        'success' => true,
        'message' => 'Collector updated successfully.'
    ]);

} else {

    echo json_encode([
        'success' => false,
        'message' => 'Failed to update collector.'
    ]);
}

$conn->close();
?>