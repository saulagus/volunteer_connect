<?php
// shared HTML head, nav bar, and flash messages for every page

// allow pages to set their own title before including this file
if (isset($pageTitle)) {
    $fullTitle = htmlspecialchars($pageTitle) . ' - Volunteer Connect';
} else {
    $fullTitle = 'Volunteer Connect';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $fullTitle ?></title>
    <link rel="stylesheet" href="/volunteer_connect/app/public/css/style.css">
</head>
<body>

<nav class="navBar">
    <a href="/volunteer_connect/app/index.php" class="siteName">Volunteer Connect</a>

    <ul class="navLinks">
        <?php if (!is_logged_in()): ?>
            <li><a href="/volunteer_connect/app/login.php">Login</a></li>
            <li><a href="/volunteer_connect/app/register.php">Register</a></li>

        <?php elseif (current_role() === 'admin'): ?>
            <li><a href="/volunteer_connect/app/admin/dashboard.php">Dashboard</a></li>
            <li><a href="/volunteer_connect/app/admin/users.php">Users</a></li>
            <li><a href="/volunteer_connect/app/logout.php">Logout</a></li>

        <?php elseif (current_role() === 'organiser'): ?>
            <li><a href="/volunteer_connect/app/organiser/dashboard.php">My Events</a></li>
            <li><a href="/volunteer_connect/app/organiser/create_event.php">Create Event</a></li>
            <li><a href="/volunteer_connect/app/logout.php">Logout</a></li>

        <?php elseif (current_role() === 'attendee'): ?>
            <li><a href="/volunteer_connect/app/attendee/events.php">Browse Events</a></li>
            <li><a href="/volunteer_connect/app/attendee/my_bookings.php">My Bookings</a></li>
            <li><a href="/volunteer_connect/app/logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- show flash messages set before a redirect -->
<?php if (!empty($_SESSION['flash'])): ?>
    <div class="flashSuccess"><?= htmlspecialchars($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="flashError"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>
