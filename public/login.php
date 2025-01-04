<?php
ob_start();
require_once __DIR__ . '/../configs/db.php'; 
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (isset($_SESSION['id'])) {
    if ($_SESSION['role'] == 'teacher') {
        header("Location: teacher_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        $check_user_sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($check_user_sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['id'] = $user['id'];
                $_SESSION['fname'] = $user['fname'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'teacher') {
                    header("Location: teacher_dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }
                exit;
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "No user found with this email.";
        }
    }
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script>
        function showPopup(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <div class="container">
        <form class="form" id="form" method="POST">
            <h2 id="login-header">Login</h2>
            <div class="form-group">
                <label for="email">Email</label>
                <div class="icon-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
            </div>           
            <div class="form-group">
                <label for="password">Password</label>
                <div class="icon-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>           
            <div class="form-group">
                <button type="submit">Login</button>
            </div>            
            <div class="form-group">
                <p><a href="forgot_password.php">Forgot Password?</a></p>
                <p><a href="registration.php">Sign up?</a></p>
            </div>
        </form>
    </div>

    <?php
    if (isset($error_message)) {
        echo "<script>showPopup('$error_message');</script>";
    }
    ?>

</body>
</html>
