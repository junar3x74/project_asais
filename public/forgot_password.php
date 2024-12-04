<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form class="form" method="POST" onsubmit="return redirectToPage()">
            <div class="form-group">
                <label for="email">Enter your email:</label>
                <input type="email" id="email" name="email" placeholder="Email" >
            </div>
            <div class="form-group">
                <button type="submit">Send OTP</button>
            </div>
        </form>
        <p>Remembered your password? <a href="login.php">Go back to login</a></p>
    </div>

    <script>
        function redirectToPage() {
            window.location.href = 'reset_password.php';
            return false;
        }
    </script>
</body>
</html>
