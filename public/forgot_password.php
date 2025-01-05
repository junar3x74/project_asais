<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

require_once __DIR__ . '/../configs/db.php'; 
require_once __DIR__ . '/../vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;
use Carbon\Carbon;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();

session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    
    if (empty($email)) {
        $_SESSION['error'] = "Please enter your email address.";
        header('Location: forgot_password.php');
        exit;
    }

    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        $otp = random_int(100000, 999999);
        $otp_expiry = Carbon::now('Asia/Manila')->addMinutes(10)->format('Y-m-d H:i:s'); 
        $updateStmt = $pdo->prepare("UPDATE users SET otp = :otp, otp_expiry = :otp_expiry WHERE email = :email");
        if ($updateStmt->execute(['otp' => $otp, 'otp_expiry' => $otp_expiry, 'email' => $email])) {    
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['EMAIL_USERNAME']; 
                $mail->Password = $_ENV['EMAIL_PASSWORD']; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                
                $mail->setFrom($_ENV['EMAIL_USERNAME'], 'Password Reset');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP for Password Reset';
                $mail->Body = "<p>Your OTP is <strong>{$otp}</strong>.</p><p>This OTP will expire in 10 minutes.</p>";

            
                $mail->send();
                $_SESSION['success'] = "An OTP has been sent to your email.";
                header('Location: reset_password.php');
                exit;
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
                $_SESSION['error'] = "Failed to send OTP email. Please try again.";
            }
        } else {
            $_SESSION['error'] = "Failed to generate OTP. Please try again.";
        }
    } else {
        $_SESSION['error'] = "No account found with this email.";
    }

    header('Location: forgot_password.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container forgot">
        <h2>Forgot Password</h2>
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
