<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $firstname  = trim($_POST['firstname'] ?? '');
    $lastname   = trim($_POST['lastname'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $suffix     = trim($_POST['suffix'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $gender     = trim($_POST['gender'] ?? '');
    $month      = $_POST['month'] ?? '';
    $day        = $_POST['day'] ?? '';
    $year       = $_POST['year'] ?? '';
    $phone      = trim($_POST['phone'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';
    $agree      = isset($_POST['agree']);

    // Validation
    if (!$firstname || !$lastname || !$email || !$password || !$confirm) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (emailExists($email)) {
        $error = 'This email is already registered.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (!isStrongPassword($password)) {
        $error = 'Password must be at least 8 characters and include a number and a special symbol.';
    } elseif (!$agree) {
        $error = 'You must agree to the Terms of Service and Privacy Policy.';
    } else {
        // Create user
        $users = getUsers();
        $newUser = [
            'id'         => uniqid('user_', true),
            'firstname'  => $firstname,
            'lastname'   => $lastname,
            'email'      => strtolower($email),
            'username'   => strtolower(explode('@', $email)[0]), // simple username from email
            'suffix'     => $suffix,
            'address'    => $address,
            'gender'     => $gender,
            'dob'        => sprintf('%04d-%02d-%02d', (int)$year, (int)$month, (int)$day),
            'phone'      => $phone,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $users[] = $newUser;
        if (saveUsers($users)) {
            redirect('login.php?registered=1');
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}

// Generate year options (from current year back 100 years)
$currentYear = (int)date('Y');
$years = range($currentYear, $currentYear - 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Left: Illustration -->
        <div class="illustration">
            <img src="assets/smart-people.png" alt="Register illustration" style="width: 100%; max-width: 420px; height: auto;">
        </div>

        <!-- Right: Form -->
        <div class="form-panel">
            <h1>Register</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="" autocomplete="on">
                <!-- Name row -->
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" name="firstname" placeholder="Firstname" 
                               value="<?= htmlspecialchars($old['firstname'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="lastname" placeholder="Lastname" 
                               value="<?= htmlspecialchars($old['lastname'] ?? '') ?>" required>
                    </div>
                </div>

                <!-- Email + Suffix -->
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <input type="email" name="email" placeholder="Email Address" 
                               value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <input type="text" name="suffix" placeholder="Suffix" 
                               value="<?= htmlspecialchars($old['suffix'] ?? '') ?>">
                    </div>
                </div>

                <!-- Address + Gender -->
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <input type="text" name="address" placeholder="Address" 
                               value="<?= htmlspecialchars($old['address'] ?? '') ?>">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <select name="gender">
                            <option value="">Gender</option>
                            <option value="Male"   <?= ($old['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($old['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other"  <?= ($old['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            <option value="Prefer not to say" <?= ($old['gender'] ?? '') === 'Prefer not to say' ? 'selected' : '' ?>>Prefer not to say</option>
                        </select>
                    </div>
                </div>

                <!-- Date of Birth -->
                <div class="form-group">
                    <span class="field-label">Date of Birth</span>
                    <div class="dob-row">
                        <select name="month" required>
                            <option value="">month</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= (isset($old['month']) && (int)$old['month'] === $m) ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <select name="day" required>
                            <option value="">day</option>
                            <?php for ($d = 1; $d <= 31; $d++): ?>
                                <option value="<?= $d ?>" <?= (isset($old['day']) && (int)$old['day'] === $d) ? 'selected' : '' ?>>
                                    <?= $d ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <select name="year" required>
                            <option value="">year</option>
                            <?php foreach ($years as $y): ?>
                                <option value="<?= $y ?>" <?= (isset($old['year']) && (int)$old['year'] === $y) ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <input type="tel" name="phone" placeholder="Phone Number" 
                           value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                </div>

                <!-- Password -->
                <div class="form-group password-wrapper">
                    <input type="password" name="password" id="reg-password" placeholder="Password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('reg-password', this)">👁</button>
                </div>

                <!-- Confirm Password -->
                <div class="form-group password-wrapper">
                    <input type="password" name="confirm_password" id="reg-confirm" placeholder="Confirm Password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('reg-confirm', this)">👁</button>
                </div>
                <p class="password-hint">
                    Password must be at least 8 characters and include a number and a special symbol
                </p>

                <button type="submit" class="btn-primary" style="margin-top: 20px;">Register Now</button>

                <div style="margin-top: 16px;">
                    <label class="checkbox-label">
                        <input type="checkbox" name="agree" value="1" <?= isset($old['agree']) ? 'checked' : '' ?>>
                        I agree to the Terms of Service and Privacy Policy
                    </label>
                </div>
            </form>

            <p class="bottom-text">
                Already have an account? <a href="login.php">Login</a>
            </p>
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
