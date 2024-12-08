<?php
// Start output buffering
ob_start();

require_once __DIR__ . '/../configs/db.php'; // Include database connection

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
    $check_email_sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($check_email_sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        die("This email is already registered.");
    }

    // Check if username already exists
    $check_username_sql = "SELECT * FROM users WHERE fname = :fname";
    $stmt = $pdo->prepare($check_username_sql);
    $stmt->bindParam(':fname', $name);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        die("This Full name is already taken. Please choose another one.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database
    $insert_sql = "INSERT INTO users (fname, email, password, role) VALUES (:fname, :email, :password, :role)";
    $stmt = $pdo->prepare($insert_sql);
    $stmt->bindParam(':fname', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':role', $role);

    if ($stmt->execute()) {
        // Redirect after successful registration
        header("Location: login.php");
        exit;
    } else {
        die("Error during registration.");
    }
}

// End output buffering and flush output
ob_end_flush();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Register</title>
    <style>
        .error {
            color: red;
            font-size: 12px;
        }
    </style>
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
                    <span id="password-error" class="error"></span> <!-- Error message for password -->
                </div>
            </div>
            <div class="form-group">
                <label for="cpassword">Confirm Password</label>
                <div class="icon-container">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="cpassword" id="cpassword" placeholder="Confirm your password" required>
                    <span id="cpassword-error" class="error"></span> <!-- Error message for confirm password -->
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

    <script>
        // Password validation function
        function validatePassword(password) {
            // Regular expressions for password rules
            const minLength = 8; // Minimum length
            const uppercasePattern = /[A-Z]/; // At least one uppercase letter
            const specialCharPattern = /[!@#$%^&*(),.?":{}|<>]/; // At least one special character
            const numberPattern = /[0-9]/; // At least one number

            // Validate the password based on the patterns
            if (password.length < minLength) {
                return "Password must be at least 8 characters long.";
            }
            if (!uppercasePattern.test(password)) {
                return "Password must contain at least one uppercase letter.";
            }
            if (!specialCharPattern.test(password)) {
                return "Password must contain at least one special character.";
            }
            if (!numberPattern.test(password)) {
                return "Password must contain at least one number.";
            }

            // If all validations pass
            return null;
        }

        // Event listener for form submission
        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form submission

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('cpassword').value;
            const passwordError = validatePassword(password);
            const confirmPasswordError = password !== confirmPassword ? "Passwords do not match." : null;

            const passwordErrorElement = document.getElementById('password-error');
            const confirmPasswordErrorElement = document.getElementById('cpassword-error');

            // Reset previous error messages
            passwordErrorElement.textContent = '';
            confirmPasswordErrorElement.textContent = '';

            let isValid = true;

            // Show password validation error if any
            if (passwordError) {
                passwordErrorElement.textContent = passwordError;
                isValid = false;
            }

            // Show confirm password error if any
            if (confirmPasswordError) {
                confirmPasswordErrorElement.textContent = confirmPasswordError;
                isValid = false;
            }

            // If the form is valid, submit it
            if (isValid) {
                // Optionally, display a success prompt before submitting
                alert("Form is valid. Submitting the form!");
                this.submit(); // This will submit the form to the same page (or action specified in the form)
            }
            
        });
    </script>
</body>
</html>





