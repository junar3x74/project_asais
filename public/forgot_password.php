<?php
require_once __DIR__ . '/../configs/db.php'; // Database connection
require_once __DIR__ . '/../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); // Adjust path if needed
$dotenv->load();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a 6-digit OTP and its expiry in UTC
        $otp = random_int(100000, 999999);
        $otp_expiry = new DateTime('now', new DateTimeZone('UTC')); // Current time in UTC
        $otp_expiry->modify('+10 minutes'); // Set expiry to 10 minutes from now
        $otp_expiry = $otp_expiry->format('Y-m-d H:i:s'); // Format for database

        // Save the OTP and expiry in the database
        $updateStmt = $pdo->prepare("UPDATE users SET otp = :otp, otp_expiry = :otp_expiry WHERE email = :email");
        $updateStmt->execute([
            'otp' => $otp,
            'otp_expiry' => $otp_expiry,
            'email' => $email
        ]);

        // Send the OTP email
        $mail = new PHPMailer(true);
        try {
            // Email server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USERNAME']; // Gmail address
            $mail->Password = $_ENV['EMAIL_PASSWORD']; // App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email content
            $mail->setFrom($_ENV['EMAIL_USERNAME'], 'Password Reset');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body = "<p>Your OTP is <strong>{$otp}</strong>.</p><p>This OTP will expire in 10 minutes.</p>";

            $mail->send();
            $_SESSION['success'] = "An OTP has been sent to your email.";
            header('Location: reset_password.php'); // Redirect to reset password page
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Failed to send OTP email. Error: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = "No account found with this email.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container forgot">
        <h2>Forgot Password</h2>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <p style="color: green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <form class="form" method="POST">
            <div class="form-group">
                <label for="email">Enter your email:</label>
                <div class="icon-container">
                    <i class="fa fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>
            </div>
            <div class="form-group">
                <button type="submit">Send OTP</button>
            </div>
        </form>
        <p>Remembered your password? <a href="login.php">Go back to login</a></p>
    </div>
</body>
</html>
