<?php
/**
 * auth.php
 * Session helper functions. Include this AFTER session_start()
 * on any page that needs to check login state or restrict by role.
 */

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        header("Location: ../login.php");
        exit();
    }
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function current_user_name() {
    return $_SESSION['full_name'] ?? '';
}

function generate_otp() {
    return strval(random_int(100000, 999999));
}
