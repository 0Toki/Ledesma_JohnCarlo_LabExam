<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = currentUser();
if (!$user) {
    // Session is stale
    session_destroy();
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard {
            max-width: 600px;
            margin: 60px auto;
            padding: 40px;
            text-align: center;
        }
        .dashboard h1 {
            margin-bottom: 8px;
        }
        .user-card {
            background: #f9f9f9;
            border-radius: 16px;
            padding: 30px;
            margin: 30px 0;
            text-align: left;
        }
        .user-card p {
            margin: 10px 0;
            font-size: 0.95rem;
        }
        .user-card strong {
            display: inline-block;
            width: 120px;
            color: #555;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 32px;
            background: #1a1a1a;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
        }
        .logout-btn:hover {
            background: #333;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Welcome, <?= htmlspecialchars($user['firstname']) ?>!</h1>
        <p class="subtitle" style="margin: 0 auto 20px;">You are successfully logged in.</p>

        <div class="user-card">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <?php if (!empty($user['phone'])): ?>
                <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
            <?php endif; ?>
            <?php if (!empty($user['gender'])): ?>
                <p><strong>Gender:</strong> <?= htmlspecialchars($user['gender']) ?></p>
            <?php endif; ?>
            <?php if (!empty($user['dob']) && $user['dob'] !== '0000-00-00'): ?>
                <p><strong>Date of Birth:</strong> <?= htmlspecialchars($user['dob']) ?></p>
            <?php endif; ?>
            <?php if (!empty($user['address'])): ?>
                <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
            <?php endif; ?>
            <p><strong>Joined:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
        </div>

        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
