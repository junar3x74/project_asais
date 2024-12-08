<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Register</title>
</head>
<body>
    <div class="container register">
        <h2>Register</h2>
        <form class="form" id="form" action="" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="icon-container">
                    <i class="fa fa-user"></i>
                    <input type="text" name="name" id="name" placeholder="Enter your full name" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <div class="icon-container">
                    <i class="fa fa-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="icon-container">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>
            </div>
            <div class="form-group">
                <label for="cpassword">Confirm Password</label>
                <div class="icon-container">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="cpassword" id="cpassword" placeholder="Confirm your password" required>
                </div>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <div class="icon-container select-container">
                    <i class="fa fa-user-tag"></i>
                    <select name="role" id="role" required>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <button type="submit">Register</button>
            </div>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>

<?php
    
     require_once __DIR__ . '/../configs/db.php';

    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Capture form data
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $role = $_POST['role'];
    
        // Validate form inputs
        if (empty($name) || empty($email) || empty($password) || empty($cpassword)) {
            die("All fields are required.");
        }
    
        if ($password !== $cpassword) {
            die("Passwords do not match.");
        }
    
        // Check if email is already registered
        $check_sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($check_sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            die("This email is already registered.");
        }
    
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        // Insert the user into the database
        $insert_sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
        $stmt = $pdo->prepare($insert_sql);
        $stmt->bindParam(':username', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
    
        if ($stmt->execute()) {
            echo "Registration successful!";
            header("Location: login.php"); // Redirect to login page
            exit;
        } else {
            die("Error during registration.");
        }
    }
    
    

?>