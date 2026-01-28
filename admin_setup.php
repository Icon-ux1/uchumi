<?php
include 'config.php';

$admins = [
    [
        'username' => 'Icon',
        'password' => 'Icon147*',
        'email' => 'admin@uchumi.com',
        'full_name' => 'Icon Admin'
    ],
    [
        'username' => 'Adilex',
        'password' => 'Adilex123',
        'email' => 'adilex@uchumi.com',
        'full_name' => 'Adilex Admin'
    ],
    [
        'username' => 'Lewis',
        'password' => 'Lewis123',
        'email' => 'lewis@uchumi.com',
        'full_name' => 'Lewis Admin'
    ]
];

// Ensure user_role column exists
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS user_role VARCHAR(20) DEFAULT 'user'");

foreach ($admins as $admin) {
    $username = $admin['username'];
    $password = $admin['password'];
    $email = $admin['email'];
    $full_name = $admin['full_name'];
    $role = 'admin';

    // Check if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing user to admin
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ?, user_role = ?, full_name = ?, email = ? WHERE username = ?");
        $stmt->bind_param("sssss", $hashed_password, $role, $full_name, $email, $username);
        if ($stmt->execute()) {
            echo "Admin user '$username' updated successfully.<br>";
        } else {
            echo "Error updating admin user '$username': " . $conn->error . "<br>";
        }
    } else {
        // Create new admin user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, user_role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $hashed_password, $email, $full_name, $role);
        if ($stmt->execute()) {
            echo "Admin user '$username' created successfully.<br>";
        } else {
            echo "Error creating admin user '$username': " . $conn->error . "<br>";
        }
    }
}

echo "<br><a href='login.php'>Go to Login</a>";
?>
