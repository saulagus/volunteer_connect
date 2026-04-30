<?php
// file to destroy the session and log user out

session_start();

// clear all session data on the server side
$_SESSION = [];

// remove the session cookie from the browser so there's no leftover cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    // delete cookie
    setcookie(
        // use the same data as original cookie
        session_name(), '',
        // set expiration in the past
        time() - 3600,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// destroy the session on the server
session_destroy();
// redirect to login 
header("Location: /volunteer_connect/app/index.php?msg=logged_out");
exit();