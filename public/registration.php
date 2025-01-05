<?php

ob_start();

require_once __DIR__ . '/../configs/db.php'; 

$email_error = '';  

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $role = $_POST['role'];

    if (empty($name) || empty($email) || empty($password) || empty($cpassword)) {
        die("All fields are required.");
    }

    if ($password !== $cpassword) {
        die("Passwords do not match.");
    }

    $check_email_sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($check_email_sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $email_error = "Email is already taken.";
    }

    $check_username_sql = "SELECT * FROM users WHERE fname = :fname";
    $stmt = $pdo->prepare($check_username_sql);
    $stmt->bindParam(':fname', $name);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        die("This Full name is already taken. Please choose another one.");
    }

    if (!$email_error) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insert_sql = "INSERT INTO users (fname, email, password, role) VALUES (:fname, :email, :password, :role)";
        $stmt = $pdo->prepare($insert_sql);
        $stmt->bindParam(':fname', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            die("Error during registration.");
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
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
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
                    <input type="email" name="email" id="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>
                <?php if (!empty($email_error)): ?>
                    <span class="error"><?php echo $email_error; ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="icon-container">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    <span id="password-error" class="error"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="cpassword">Confirm Password</label>
                <div class="icon-container">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="cpassword" id="cpassword" placeholder="Confirm your password" required>
                    <span id="cpassword-error" class="error"></span>
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
        function validatePassword(password) {
            const minLength = 8;
            const uppercasePattern = /[A-Z]/;
            const specialCharPattern = /[!@#$%^&*(),.?":{}|<>]/;
            const numberPattern = /[0-9]/;

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

            return null;
        }

        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault();

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('cpassword').value;
            const passwordError = validatePassword(password);
            const confirmPasswordError = password !== confirmPassword ? "Passwords do not match." : null;

            const passwordErrorElement = document.getElementById('password-error');
            const confirmPasswordErrorElement = document.getElementById('cpassword-error');

            passwordErrorElement.textContent = '';
            confirmPasswordErrorElement.textContent = '';

            let isValid = true;

            if (passwordError) {
                passwordErrorElement.textContent = passwordError;
                isValid = false;
            }

            if (confirmPasswordError) {
                confirmPasswordErrorElement.textContent = confirmPasswordError;
                isValid = false;
            }

            if (isValid) {
                alert("Form is valid. Submitting the form!");
                this.submit();
            }
        });
    </script>
</body>
</html>
