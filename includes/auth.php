<?php
// Starts the session if one isn't already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Returns true if someone is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Returns true if the logged-in user is an admin
function isAdmin()
{
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

// Blocks the page unless someone is logged in. Add above all pages that requires log in.
function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: /auth/login.php");
        exit();
    }
}

// Blocks the page unless the logged-in user is an admin. Add abovee all pages that requires admin.
function requireAdmin()
{
    requireLogin();
    if (!isAdmin()) {
        header("Location: /customer/shop.php");
        exit();
    }
}