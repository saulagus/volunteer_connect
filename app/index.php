<?php
// landing page or redirects logged-in users straight to their dashboard

session_start();
require_once 'includes/auth.php';

// send logged-in users to the right place
if (is_logged_in()) {
    $role = current_role();
    if ($role === 'admin') {
        header('Location: /admin/dashboard.php');
        exit();
    }
    if ($role === 'organiser') {
        header('Location: /organiser/dashboard.php');
        exit();
    }
    if ($role === 'attendee') {
        header('Location: /attendee/events.php');
        exit();
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<main class="landingMain">

    <!-- hero-->
    <section class="hero">
        <div class="heroInner">
            <p class="heroEyebrow">Open to everyone &mdash; free to join</p>
            <h1 class="heroHeadline">Make a difference<br>in your community</h1>
            <p class="heroSub">
                Volunteer Connect brings people and causes together.
                Browse upcoming volunteer events, sign up in seconds,
                or create your own and manage your attendees.
            </p>
            <div class="heroBtns">
                <a href="/register.php" class="btn btnHeroP">Get Started Free</a>
                <a href="/login.php"    class="btn btnHeroS">Log In</a>
            </div>
        </div>
    </section>

    <!-- trust bar -->
    <section class="trustBar">
        <div class="trustInner">
            <div class="trustStat">
                <span class="trustNum">500+</span>
                <span class="trustLabel">Volunteers registered</span>
            </div>
            <div class="trustDivider" aria-hidden="true"></div>
            <div class="trustStat">
                <span class="trustNum">120+</span>
                <span class="trustLabel">Events organised</span>
            </div>
            <div class="trustDivider" aria-hidden="true"></div>
            <div class="trustStat">
                <span class="trustNum">40+</span>
                <span class="trustLabel">Community causes</span>
            </div>
        </div>
    </section>

    <!-- how it works -->
    <section class="howItWorks">
        <div class="sectionInner">
            <h2 class="sectionTitle">How it works</h2>
            <p class="sectionLead">Three steps from idea to impact.</p>

            <ol class="stepsList">
                <li class="stepItem">
                    <span class="stepNum">01</span>
                    <div class="stepBody">
                        <h3 class="stepTitle">Create a free account</h3>
                        <p class="stepDesc">Register in under a minute as a volunteer or an event organiser. No fees, no friction.</p>
                    </div>
                </li>
                <li class="stepItem">
                    <span class="stepNum">02</span>
                    <div class="stepBody">
                        <h3 class="stepTitle">Find or create an event</h3>
                        <p class="stepDesc">Browse upcoming opportunities by date or location — or post your own event and set the capacity.</p>
                    </div>
                </li>
                <li class="stepItem">
                    <span class="stepNum">03</span>
                    <div class="stepBody">
                        <h3 class="stepTitle">Show up and make an impact</h3>
                        <p class="stepDesc">Book your spot with one click. Organisers get a live attendee list; volunteers get a confirmation.</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <!-- audience split -->
    <section class="audienceSplit">
        <div class="sectionInner audienceGrid">

            <div class="audienceCard audienceCardAttendee">
                <span class="audienceTag">For Volunteers</span>
                <h2 class="audienceTitle">Find something worth showing up for</h2>
                <p class="audienceDesc">
                    Hundreds of community events need your time and energy.
                    Filter by date, search by cause, and book your place in seconds.
                </p>
                <a href="/register.php" class="btn btnPrimary">Browse Events</a>
            </div>

            <div class="audienceCard audienceCardOrganiser">
                <span class="audienceTag">For Organisers</span>
                <h2 class="audienceTitle">Rally volunteers around your cause</h2>
                <p class="audienceDesc">
                    Create an event, set a capacity, and watch registrations come in.
                    Manage your attendee list from a single dashboard.
                </p>
                <a href="/register.php" class="btn btnPrimary">Create an Event</a>
            </div>

        </div>
    </section>

    <!-- cta band -->
    <section class="ctaBand">
        <div class="ctaBandInner">
            <h2 class="ctaBandTitle">Ready to get involved?</h2>
            <p class="ctaBandSub">Join a growing community of people who turn intention into action.</p>
            <a href="/register.php" class="btn btnCtaBand">Create your free account</a>
        </div>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>
