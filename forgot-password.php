<?php
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $user = findUser($email);
        // Always show the same message for security (don't reveal if email exists)
        $message = 'If an account with that email exists, a password reset link has been sent.';
        // In a real app you would generate a token and send an email here
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="justify-content: center;">
        <div class="form-panel" style="max-width: 420px;">
            <h1>Forgot password?</h1>
            <p class="subtitle">
                Enter the email address associated with your account and we’ll send you a link to reset your password.
            </p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <button type="submit" class="btn-primary">Send Reset Link</button>
                </form>
            <?php endif; ?>

            <p class="bottom-text">
                <a href="login.php">← Back to Login</a>
            </p>
        </div>
    </div>
</body>
</html>
