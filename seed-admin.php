<?php
// Run this file ONCE to create an admin account
// Then DELETE it for security

include "db.php";

// Admin details (EDIT THIS IF YOU WANT)
$email = "admin@enviromanage.com";
$username = "admin";
$password = "Admin123"; // change this after login!

$first_name = "System";
$middle_initial = "A";
$last_name = "Administrator";
$gender = "Male";
$birthdate = "2000-01-01";
$phone = "9123456789";
$house_street = "Admin Street";
$barangay = "Main";
$postal_code = "4000";

echo "<h3>Seeding Admin Account...</h3>";

// Check if admin already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "❌ Admin already exists!";
} else {
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert admin
    $stmt = $conn->prepare("
        INSERT INTO users 
        (email, username, password, role, first_name, middle_initial, last_name, gender, birthdate, phone, house_street, barangay, postal_code) 
        VALUES (?, ?, ?, 'admin', ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssssssss",
        $email,
        $username,
        $hashedPassword,
        $first_name,
        $middle_initial,
        $last_name,
        $gender,
        $birthdate,
        $phone,
        $house_street,
        $barangay,
        $postal_code
    );

    if ($stmt->execute()) {
        echo "✅ Admin account created successfully!<br>";
        echo "📧 Email: $email <br>";
        echo "🔑 Password: $password <br><br>";
        echo "<strong>⚠️ DELETE THIS FILE AFTER USE!</strong>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}

$conn->close();
?>