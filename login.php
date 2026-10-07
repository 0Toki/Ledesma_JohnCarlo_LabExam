<?php
require_once 'config.php';

// If already logged in, go to dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';
    $remember   = isset($_POST['remember']);

    if (empty($identifier) || empty($password)) {
        $error = 'Please enter both email/username and password.';
    } else {
        $user = findUser($identifier);
        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];

            // Remember me (30 days)
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                // In a real app you'd store the token hashed in the user record
            }

            redirect('dashboard.php');
        } else {
            $error = 'Invalid email/username or password.';
        }
    }
}

// Show success message if redirected from register
if (isset($_GET['registered'])) {
    $success = 'Account created successfully! Please log in.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Welcome back!</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Left: Form -->
        <div class="form-panel">
            <h1>Welcome back!</h1>
            <p class="subtitle">
                It’s wonderful to see you again. Please sign in below
                so you can pick up right where you left off and continue.
            </p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="" autocomplete="on">
                <div class="form-group">
                    <input 
                        type="text" 
                        name="identifier" 
                        placeholder="Email or Username" 
                        value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group password-wrapper">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Password" 
                        required
                    >
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Show password">
                        👁
                    </button>
                </div>

                <div class="options-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="1">
                        Remember me
                    </label>
                    <a href="forgot-password.php" class="forgot-link">forgot password?</a>
                </div>

                <button type="submit" class="btn-primary">Login</button>
            </form>

            <p class="bottom-text">
                Not a member? <a href="register.php">Register now</a>
            </p>
        </div>

        <div class="illustration">
            <img src="assets/social-media.png" alt="Login illustration" style="width: 100%; max-width: 420px; height: auto;">
        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁';
            }
        }
    </script>
</body>
</html>
