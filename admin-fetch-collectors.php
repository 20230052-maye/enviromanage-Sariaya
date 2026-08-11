<?php
header('Content-Type: application/json; charset=utf-8');

$host = "localhost";
$user = "u823857209_enviromanage";
$pass = "Enviromanage4322";
$db   = "u823857209_enviromanage";


$conn = new mysqli($host, $user, $pass, $db);

// =====================
// 1. SAFE CONNECTION
// =====================
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

// =====================
// 2. QUERY (FIXED)
// NOTE: assigned_truck_id usually comes from trucks table
// =====================
$sql = "
    SELECT 
        u.id,
        u.email,
        u.username,
        u.role,
        u.first_name,
        u.middle_initial,
        u.last_name,
        u.gender,
        u.birthdate,
        u.phone,
        u.house_no,
        u.street,
        u.barangay,
        u.postal_code,
        t.id AS assigned_truck_id

    FROM users u
    LEFT JOIN trucks t 
        ON t.collector_id = u.id

    WHERE u.role = 'collector'
    ORDER BY u.id ASC
";

$result = $conn->query($sql);

// =====================
// 3. RESPONSE ARRAY
// =====================
$users = [];

if ($result) {

    while ($row = $result->fetch_assoc()) {

        // =====================
        // FULL NAME
        // =====================
        $middleInitial = '';

        if (!empty($row['middle_initial'])) {
            $middleInitial = strtoupper(trim($row['middle_initial'])) . '.';
        }

        $display_name = trim(
            ($row['first_name'] ?? '') . ' ' .
            $middleInitial . ' ' .
            ($row['last_name'] ?? '')
        );

        if ($display_name === '') {
            $display_name = 'Pending';
        }

        // =====================
        // ADDRESS
        // =====================
        $addressParts = [];

        if (!empty($row['house_no']) || !empty($row['street'])) {
            $addressParts[] = trim(
                ($row['house_no'] ?? '') . ' ' .
                ($row['street'] ?? '')
            );
        }

        if (!empty($row['barangay'])) {
            $addressParts[] = $row['barangay'];
        }

        if (!empty($row['postal_code'])) {
            $addressParts[] = $row['postal_code'];
        }

        $display_address = !empty($addressParts)
            ? implode(', ', $addressParts)
            : 'Pending';

        // =====================
        // USER OBJECT
        // =====================
        $users[] = [
            'id' => (int)$row['id'],
            'email' => $row['email'] ?? '',
            'username' => $row['username'] ?? '',
            'role' => $row['role'] ?? '',

            'first_name' => $row['first_name'] ?? '',
            'middle_initial' => $row['middle_initial'] ?? '',
            'last_name' => $row['last_name'] ?? '',

            'display_name' => $display_name,

            'gender' => $row['gender'] ?? '',
            'birthdate' => $row['birthdate'] ?? '',
            'phone' => $row['phone'] ?? '',

            // FIXED: comes from JOIN
            'assigned_truck_id' => $row['assigned_truck_id'] ?? null,

            'display_address' => $display_address
        ];
    }
}

// =====================
// 4. OUTPUT
// =====================
echo json_encode([
    "success" => true,
    "data" => $users
]);

$conn->close();
?>