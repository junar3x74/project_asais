<?php
ob_start();
require_once __DIR__ . '/../configs/db.php';

ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (isset($_SESSION['id'])) {
    $redirect = $_SESSION['role'] === 'teacher' ? "teacher_dashboard.php" : "student_dashboard.php";
    header("Location: $redirect");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "Please enter both email and password.";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");

    try {
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Database query error: " . $e->getMessage());
        echo "An error occurred. Please try again later.";
        exit();
    }

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['id'] = $user['id'];
            $_SESSION['fname'] = htmlspecialchars($user['fname']);
            $_SESSION['role'] = htmlspecialchars($user['role']);
            $redirect = $user['role'] === 'teacher' ? "teacher_dashboard.php" : "student_dashboard.php";
            header("Location: $redirect");
            exit();
        } else {
            echo "Incorrect password.";
            exit();
        }
    } else {
        echo "No user found with this email.";
        exit();
    }
}

ob_end_flush();
?>
