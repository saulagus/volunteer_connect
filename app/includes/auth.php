<?php
// file responsible for handling role authentication, used in pages that require specific roles to access

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /volunteer_connect/app/login.php?error=unauthorized');
        exit();
    }
}

function require_role($required_role) {
    // make sure they're logged in first
    require_login();
    // kick out if not authorized role
    if ($_SESSION['user_role'] !== $required_role) {
        header('Location: /volunteer_connect/app/index.php?error=unauthorized');
        exit();
    }
}

function require_ownership($resource_owner_id) {
    // make sure the logged-in user owns this record before letting them edit/delete it
    if ((int)$_SESSION['user_id'] !== (int)$resource_owner_id) {
        header('Location: /volunteer_connect/app/index.php?error=unauthorized');
        exit();
    }
}

function is_logged_in() {
    // used in header.php to show/hide nav links
    return isset($_SESSION['user_id']);
}

function current_role() {
    // used in header.php to show the right nav links per role
    if (isset($_SESSION['user_role'])) {
        return $_SESSION['user_role'];
    }
    return null;
}
