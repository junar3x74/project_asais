<?php
require_once __DIR__ . '/../configs/db.php'; // Correct path to your db.php

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch the submitted data
    $otp = trim($_POST['otp']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Validate input
    if (empty($otp) || empty($newPassword) || empty($confirmPassword)) {
        die("All fields are required.");
    }

    if ($newPassword !== $confirmPassword) {
        die("Passwords do not match.");
    }

    try {
        // Check if OTP exists and is valid (not expired)
        $stmt = $pdo->prepare("SELECT email FROM users WHERE otp = :otp AND otp_expiry > NOW()");
        $stmt->execute(['otp' => $otp]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("Invalid or expired OTP.");
        }

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $stmt = $pdo->prepare("UPDATE users SET password = :password, otp = NULL, otp_expiry = NULL WHERE otp = :otp");
        $stmt->execute(['password' => $hashedPassword, 'otp' => $otp]);

        echo "Password successfully reset. You can now <a href='login.php'>login</a>.";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="container reset">
        <h2>Reset Password</h2>
        <form class="form" id="form" method="POST" action="reset_password.php">
            <div class="form-group">
                <label for="otp">Enter OTP</label>
                <input type="text" name="otp" id="otp" placeholder="Enter OTP" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
            </div>

            <div class="form-group">
                <button type="submit">Reset Password</button>
            </div>
        </form>
    </div>
</body>
</html>
