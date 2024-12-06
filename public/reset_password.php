<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Reset Password</title>
</head>
<body>
    <div class="container reset">
        <h2>Reset Password</h2>
        <form class="form" id="form" method="POST">
            <div class="form-group">
                <label for="otp">Enter OTP</label>
                <div class="icon-container">
                    <i class="fa fa-key"></i>
                    <input type="text" name="otp" id="otp" placeholder="Enter OTP" required>
                </div>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <div class="icon-container">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="icon-container">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
                </div>
            </div>
            <div class="form-group">
                <button type="submit">Reset Password</button>
            </div>
        </form>
    </div>
</body>
</html>
