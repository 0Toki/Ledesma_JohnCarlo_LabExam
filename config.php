<?php
// Simple file-based user storage
define('USERS_FILE', __DIR__ . '/users.json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Load all users from JSON file
 */
function getUsers(): array {
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    $data = file_get_contents(USERS_FILE);
    $users = json_decode($data, true);
    return is_array($users) ? $users : [];
}

/**
 * Save users array to JSON file
 */
function saveUsers(array $users): bool {
    return file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Find user by email or username
 */
function findUser(string $identifier): ?array {
    $users = getUsers();
    $identifier = strtolower(trim($identifier));
    foreach ($users as $user) {
        if (strtolower($user['email']) === $identifier || 
            strtolower($user['username'] ?? '') === $identifier) {
            return $user;
        }
    }
    return null;
}

/**
 * Check if email already exists
 */
function emailExists(string $email): bool {
    return findUser($email) !== null;
}

/**
 * Validate password strength
 */
function isStrongPassword(string $password): bool {
    // At least 8 chars, 1 number, 1 special character
    return strlen($password) >= 8
        && preg_match('/[0-9]/', $password)
        && preg_match('/[^A-Za-z0-9]/', $password);
}

/**
 * Redirect helper
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Get current logged-in user
 */
function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['id'] === $_SESSION['user_id']) {
            return $user;
        }
    }
    return null;
}
