<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

require_once __DIR__ . '/../configs/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = filter_var($_POST['otp'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($otp) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: reset_password.php');
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: reset_password.php');
        exit;
    }

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()]{8,}$/', $password)) {
        $_SESSION['error'] = "Password must be at least 8 characters long and include at least one letter and one number.";
        header('Location: reset_password.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE otp = :otp AND otp_expiry > NOW()");
    $stmt->execute(['otp' => $otp]);
    $user = $stmt->fetch();

    if ($user) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $pdo->beginTransaction();

        try {
            $updateStmt = $pdo->prepare("UPDATE users SET password = :password, otp = NULL, otp_expiry = NULL WHERE id = :id");
            $updateStmt->execute(['password' => $hashedPassword, 'id' => $user['id']]);

            $invalidateStmt = $pdo->prepare("UPDATE users SET otp = NULL WHERE otp = :otp");
            $invalidateStmt->execute(['otp' => $otp]);

            $pdo->commit();

            echo '<script>
                alert("Password successfully reset!");
                window.location.href = "login.php";
            </script>';
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Failed to reset password. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Invalid or expired OTP.";
    }

    header('Location: reset_password.php');
    exit;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container forgot">
        <h2>Reset Password</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <p style="color: green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <form class="form" method="POST">
            <div class="form-group">
                <label for="otp">Enter OTP:</label>
                <div class="icon-container">
                    <i class="fa fa-key"></i>
                    <input type="text" id="otp" name="otp" placeholder="OTP" required>
                </div>
            </div>
            <div class="form-group">
                <label for="password">New Password:</label>
                <div class="icon-container">
                    <i class="fa fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="New Password" required>
                </div>
                <p>Password must be at least 8 characters, with at least one letter and one number.</p>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <div class="icon-container">
                    <i class="fa fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
            </div>
            <div class="form-group">
                <button type="submit">Reset Password</button>
            </div>
        </form>
        <p>Remembered your password? <a href="login.php">Go back to login</a></p>
    </div>
</body>
</html>
