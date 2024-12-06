<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Forgot Password</title>
</head>
<body>
    <div class="container forgot">
        <h2>Forgot Password</h2>
        <form class="form" method="POST" onsubmit="return redirectToPage()">
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

    <script>
        function redirectToPage() {
            window.location.href = 'reset_password.php';
            return false;
        }
    </script>
</body>
</html>
