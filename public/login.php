<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container">
        <form class="form" id="form" method="POST">
            <h2 id="login-header">Login</h2>            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>           
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
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
