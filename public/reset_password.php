<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/">
</head>
<body>
    <form method="POST">
        <h2>Reset Password</h2>
        <label for="otp">Enter OTP:</label>
        <input type="text" id="otp" name="otp" required>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="submit">Reset Password</button>
    </form>
    <script src=""></script>
</body>
</html>
