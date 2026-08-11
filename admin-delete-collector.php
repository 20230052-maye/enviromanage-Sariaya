<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "enviromanage";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Collector ID missing'
    ]);
    exit;
}

$id = intval($data['id']);

$conn->begin_transaction();

try {

    // Delete personal info first
    $stmt1 = $conn->prepare("DELETE FROM personal_info WHERE user_id = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();

    // Delete user (only if collector)
    $stmt2 = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'collector'");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Collector deleted successfully'
    ]);

} catch (Exception $e) {

    $conn->rollback();

    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete collector'
    ]);
}
?>