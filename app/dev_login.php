<?php
session_start();
$role = $_GET['role'] ?? 'admin';
$accounts = [
    'admin'     => [1, 'Admin User',     'admin'],
    'organiser' => [2, 'Jane Organiser', 'organiser'],
    'attendee'  => [3, 'Tom Attendee',   'attendee'],
];
if (!isset($accounts[$role])) { die('Unknown role'); }
[$id, $name, $r] = $accounts[$role];
$_SESSION['user_id']   = $id;
$_SESSION['user_name'] = $name;
$_SESSION['user_role'] = $r;
$redirects = ['admin' => '/admin/dashboard.php', 'organiser' => '/organiser/dashboard.php', 'attendee' => '/attendee/events.php'];
header('Location: ' . $redirects[$role]);
exit();
