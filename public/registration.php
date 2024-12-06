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
