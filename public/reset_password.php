<?php
require_once __DIR__ . '/../configs/db.php'; // Correct path to your db.php

// Start session at the top
session_start();

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch the submitted data
    $otp = trim($_POST['otp']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Validate input
    if (empty($otp) || empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: reset_password.php');
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: reset_password.php');
        exit();
    }

    try {
        // Check if OTP exists and is valid (not expired)
        $stmt = $pdo->prepare("SELECT email FROM users WHERE otp = :otp AND otp_expiry > NOW()");
        $stmt->execute(['otp' => $otp]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "Invalid or expired OTP.";
            header('Location: reset_password.php');
            exit();
        }

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $stmt = $pdo->prepare("UPDATE users SET password = :password, otp = NULL, otp_expiry = NULL WHERE otp = :otp");
        $stmt->execute(['password' => $hashedPassword, 'otp' => $otp]);

        // Set success message
        $_SESSION['success'] = "Password successfully reset. You can now <a href='login.php'>login</a>.";
        header('Location: reset_password.php'); // Redirect to the same page to show the success message
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header('Location: reset_password.php');
        exit();
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
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
</head>
<body>
    <div class="container reset">
        <h2>Reset Password</h2>

        <!-- Display Success or Error Message -->
        <?php if (isset($_SESSION['success'])): ?>
            <p style="color: green;"><?php echo $_SESSION['success']; ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

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
