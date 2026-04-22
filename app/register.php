<?php
// file for new users to sign up as an attendee or organiser, stores a hashed password

session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// if already signed in, no need to register again
if (is_logged_in()) {
    header('Location: /index.php');
    exit();
}

$errors = [];
// default values used to refill the form after a failed submit
$name  = '';
$email = '';
$role  = 'attendee';

// handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // check for each field, trim whitespace, set to empty string if not provided
    if (isset($_POST['name'])) {
        $name = trim($_POST['name']);
    }
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
    }
    // don't trim passwords, spaces may be intentional
    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $password = '';
    }
    if (isset($_POST['confirm_password'])) {
        $confirm = $_POST['confirm_password'];
    } else {
        $confirm = '';
    }
    if (isset($_POST['role'])) {
        $role = trim($_POST['role']);
    }

    // validate name
    if ($name === '') {
        $errors[] = 'Name is required.';
    } // set limit to name length, avoid issues in db
    elseif (strlen($name) > 100) {
        $errors[] = 'Name must be 100 characters or fewer.';
    }

    // validate email
    if ($email === '') {
        $errors[] = 'Email is required.';
    } // add error if not valid email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    } // match the 150 char cap in the schema
    elseif (strlen($email) > 150) {
        $errors[] = 'Email must be 150 characters or fewer.';
    }

    // validate password, require a min length so accounts aren't trivially guessable
    if ($password === '') {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    // make sure the two password fields match
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // validate role, admin accounts can't be created from the public form
    $allowedRoles = ['attendee', 'organiser'];
    if (!in_array($role, $allowedRoles)) {
        $errors[] = 'Invalid role selected.';
    }

    // check that the email isn't already registered
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "SELECT id FROM users
            WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $existing    = $checkResult->fetch_assoc();
        $stmt->close();
        // if another account already uses this email
        if ($existing) {
            $errors[] = 'That email is already registered.';
        }
    }

    // only insert if there are no errors
    if (empty($errors)) {
        // hash the password so the raw value never lands in the db
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password, role)
            VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed, $role);
        $stmt->execute();
        $stmt->close();

        $_SESSION['flash'] = 'Account created. Please log in.';
        header('Location: /login.php');
        exit();
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<main>
    <h1>Register</h1>
    <!-- show any validation errors if they exist -->
    <?php if (!empty($errors)): ?>
        <ul class="formErrors">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="POST" action="register.php">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password">

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password">

        <label for="role">Register as</label>
        <select id="role" name="role">
            <option value="attendee"  <?php if ($role === 'attendee')  echo 'selected'; ?>>Attendee</option>
            <option value="organiser" <?php if ($role === 'organiser') echo 'selected'; ?>>Organiser</option>
        </select>

        <button type="submit">Create Account</button>
        <a href="login.php">Already have an account? Log in</a>
    </form>
</main>

<?php require_once 'includes/footer.php'; ?>
