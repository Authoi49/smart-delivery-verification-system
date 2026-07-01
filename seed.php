<?php
/**
 * seed.php
 * -----------------------------------------------------
 * Run this ONCE in your browser (e.g. http://localhost/smart-delivery/seed.php)
 * right after importing database.sql.
 * It creates 3 demo accounts with correctly hashed passwords
 * using PHP's own password_hash() function, so login is guaranteed
 * to work on your machine.
 *
 * Demo logins created:
 *   Admin    -> admin@delivery.com    / admin123
 *   Rider    -> rider@delivery.com    / rider123
 *   Customer -> customer@delivery.com / cust123
 *
 * IMPORTANT: Delete this file after running it once, so nobody
 * else can re-run it on a live server.
 */

require_once 'includes/db_connect.php';

$demoUsers = [
    ['full_name' => 'System Admin',   'email' => 'admin@delivery.com',    'phone' => '01700000000', 'password' => 'admin123', 'role' => 'admin'],
    ['full_name' => 'Karim Rider',    'email' => 'rider@delivery.com',    'phone' => '01711111111', 'password' => 'rider123', 'role' => 'rider'],
    ['full_name' => 'Rahim Customer', 'email' => 'customer@delivery.com', 'phone' => '01722222222', 'password' => 'cust123',  'role' => 'customer'],
];

echo "<h2>Smart Delivery Verification System — Seeding Demo Accounts</h2>";

foreach ($demoUsers as $u) {
    // Skip if this email already exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->bind_param("s", $u['email']);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<p>Skipped (already exists): " . htmlspecialchars($u['email']) . "</p>";
        $check->close();
        continue;
    }
    $check->close();

    $hashed = password_hash($u['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $u['full_name'], $u['email'], $u['phone'], $hashed, $u['role']);

    if ($stmt->execute()) {
        echo "<p>Created: <b>" . htmlspecialchars($u['role']) . "</b> — " . htmlspecialchars($u['email']) . " / password: " . htmlspecialchars($u['password']) . "</p>";
    } else {
        echo "<p>Failed to create " . htmlspecialchars($u['email']) . ": " . htmlspecialchars($stmt->error) . "</p>";
    }
    $stmt->close();
}

echo "<hr><p><b>Done.</b> You can now log in at <a href='login.php'>login.php</a>.</p>";
echo "<p style='color:red;'><b>Security tip:</b> delete seed.php now so it can't be re-run.</p>";

$conn->close();
