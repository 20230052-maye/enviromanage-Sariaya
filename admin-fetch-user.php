<?php
header('Content-Type: application/json; charset=utf-8');

$host = "localhost";
$user = "u823857209_enviromanage";
$pass = "Enviromanage4322";
$db   = "u823857209_enviromanage";


$conn = new mysqli($host, $user, $pass, $db);

// =====================
// 1. SAFE DB CONNECTION
// =====================
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

// =====================
// 2. QUERY (UPDATED)
// =====================
$sql = "SELECT 
            id,
            email,
            username,
            role,
            first_name,
            middle_initial,
            last_name,
            gender,
            birthdate,
            phone,

            house_no,
            street,
            barangay,
            postal_code,

            profile_photo,
            is_logged_in,
            last_activity,
            created_at
        FROM users
        ORDER BY id ASC";

$result = $conn->query($sql);

// =====================
// 3. RESPONSE ARRAY
// =====================
$users = [];

if ($result) {

    while ($row = $result->fetch_assoc()) {

        // ---------------------
        // FULL NAME
        // ---------------------
        $display_name = trim(
            ($row['first_name'] ?? '') . ' ' .
            (!empty($row['middle_initial']) ? $row['middle_initial'] . '. ' : '') .
            ($row['last_name'] ?? '')
        );

        if ($display_name === '') {
            $display_name = 'Pending';
        }

        // ---------------------
        // ADDRESS (UPDATED FORMAT)
        // ---------------------
        $addressParts = [];

        if (!empty($row['house_no']) || !empty($row['street'])) {
            $addressParts[] = trim(($row['house_no'] ?? '') . ' ' . ($row['street'] ?? ''));
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

        // ---------------------
        // USER OBJECT
        // ---------------------
        $users[] = [
            'id' => (int)$row['id'],
            'email' => $row['email'] ?? '',
            'username' => $row['username'] ?? '',
            'role' => $row['role'] ?? '',

            'first_name' => $row['first_name'] ?? '',
            'middle_initial' => $row['middle_initial'] ?? '',
            'last_name' => $row['last_name'] ?? '',

            'display_name' => $display_name,
            'display_address' => $display_address,

            'phone' => $row['phone'] ?? '',
            'gender' => $row['gender'] ?? '',
            'birthdate' => $row['birthdate'] ?? '',
            'last_activity' => $row['last_activity'] ?? '',
            'created_at' => $row['created_at'] ?? '',

            // NEW STRUCTURED ADDRESS
            'house_no' => $row['house_no'] ?? '',
            'street' => $row['street'] ?? '',
            'barangay' => $row['barangay'] ?? '',
            'postal_code' => $row['postal_code'] ?? '',

            'profile_photo' => $row['profile_photo'] ?? '',
            'is_logged_in' => (int)($row['is_logged_in'] ?? 0)
        ];
    }
}

// =====================
// 4. OUTPUT
// =====================
echo json_encode([
    "success" => true,
    "users" => $users
]);

$conn->close();
?>