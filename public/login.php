<?php
// Start output buffering
ob_start();

// Include the database connection
require_once __DIR__ . '/../configs/db.php'; 

// Start session at the top
session_start();

// Prevent caching of the page
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect logged-in users to their respective dashboard
if (isset($_SESSION['id'])) {
    if ($_SESSION['role'] == 'teacher') {
        header("Location: teacher_dashboard.php"); // Redirect if logged in as teacher
    } else {
        header("Location: student_dashboard.php"); // Redirect if logged in as student
    }
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate form inputs
    if (empty($email) || empty($password)) {
        die("Please enter both email and password.");
    }

    // Check if the user exists in the database
    $check_user_sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($check_user_sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // If the user is found
    if ($stmt->rowCount() > 0) {
        // Fetch user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Store user data in session
            $_SESSION['id'] = $user['id'];        // Unique user ID
            $_SESSION['fname'] = $user['fname'];  // User's first name
            $_SESSION['role'] = $user['role'];    // Role (teacher/student)

            // Role-based redirection
            if ($user['role'] == 'teacher') {
                header("Location: teacher_dashboard.php");  // Redirect to teacher's dashboard
            } else {
                header("Location: student_dashboard.php");  // Redirect to student's dashboard
            }
            exit;
        } else {
            // Invalid password
            die("Incorrect password.");
        }
    } else {
        // User not found
        die("No user found with this email.");
    }
}

// End output buffering
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
</body>
</html>
