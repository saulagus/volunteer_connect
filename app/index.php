<?php
session_start();
require_once 'db_connect.php';
require_once 'includes/auth.php';

// Fetch numbers to use for homepage stats
$event_count = $conn->query("SELECT COUNT(*) as total FROM events WHERE status = 'active'")->fetch_assoc()['total'];
$volunteer_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'attendee'")->fetch_assoc()['total'];

require_once 'includes/header.php'; 
?>

<main>
    <!-- Logout confirmation -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out'): ?>
        <div class="flashSuccess">You have been successfully logged out. See you soon!</div>
    <?php endif; ?>

    <!-- HERO SECTION -->
    <section style="text-align: center; padding: var(--space-7) 0; border-bottom: 1px solid var(--line);">
        <h1 style="font-size: var(--text-3xl); margin-bottom: var(--space-4);">Small acts, big impact.</h1>
        <p style="max-width: 40rem; margin: 0 auto var(--space-6); color: var(--ink-muted); font-size: var(--text-lg);">
            Volunteer Connect is where community happens. Join thousands of volunteers 
            finding local opportunities to make a difference every day.
        </p>

        <div class="tableActions" style="justify-content: center;">
            <?php if (is_logged_in()): ?>
                <!-- If logged in, show 'Go to Dashboard' instead of Login -->
                <?php 
                    $role = current_role();

                    // Handle all three roles 
                    if ($role === 'admin') {
                        $dashLink = "/volunteer_connect/app/admin/dashboard.php";
                    } elseif ($role === 'organiser') {
                        $dashLink = "/volunteer_connect/app/organiser/dashboard.php";
                    } else {
                        $dashLink = "/volunteer_connect/app/attendee/events.php";
                    }
                ?>
                <a href="<?= $dashLink ?>" class="btn btnPrimary">Go to My Dashboard</a>
                <a href="/volunteer_connect/app/logout.php" class="btn btnSecondary">Log Out</a>
            <?php else: ?>
                <!-- If not logged in, show standard CTAs -->
                <a href="/volunteer_connect/app/register.php" class="btn btnPrimary">Start Volunteering</a>
                <a href="/volunteer_connect/app/login.php" class="btn btnSecondary">Login</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- IMPACT STATS -->
    <div class="sectionHeader">
        <h2>Our Growing Community</h2>
    </div>
    
    <div class="statCards">
        <div class="statCard">
            <h2><?= $event_count ?></h2>
            <p>Active Events</p>
            <a href="/volunteer_connect/app/login.php">Browse Opportunities</a>
        </div>
        <div class="statCard">
            <h2><?= $volunteer_count ?></h2>
            <p>Volunteers Joined</p>
            <a href="/volunteer_connect/app/register.php">Join the Movement</a>
        </div>
        <div class="statCard">
            <h2>100%</h2>
            <p>Community Driven</p>
            <span>No fees, just impact.</span>
        </div>
    </div>

</main>

<?php require_once 'includes/footer.php'; ?>
