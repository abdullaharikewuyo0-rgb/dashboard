<?php
require 'db.php';

$email    = 'jane.smith@example.edu';
$password = 'password123';
$hash     = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
$existing = $stmt->fetch();

if ($existing) {
    $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
    $upd->execute([$hash, $email]);
    echo "✅ Password updated for {$email}<br>";
} else {
    $ins = $pdo->prepare('INSERT INTO users (name, initials, email, password_hash, role) VALUES (?, ?, ?, ?, ?)');
    $ins->execute(['Jane Smith', 'JS', $email, $hash, 'student']);
    echo "✅ User created: {$email}<br>";
}

echo "Password: {$password}<br>";
echo "Hash: {$hash}<br>";
echo "Login: <a href='login.php'>Go to login</a>";