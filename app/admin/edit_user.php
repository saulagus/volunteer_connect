<?php
// file for admin to edit a user's name, email, and role

session_start();
require_once '../db_connect.php';
require_once '../includes/auth.php';
require_role('admin');

// get the user id from the URL on GET, or from the hidden field on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // check for id and handle when it's missing
    if (isset($_POST['id'])) {
        // cast to int to prevent injection and to ensure a valid number
        $id = (int)$_POST['id'];
    } else {
        // treat as invalid and go back to users if no id provided 
        $id = 0;
    }
} else {
    // on GET, id would be in the URL as a parameter 
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    } else {
        $id = 0;
    }
}

// if no valid id, go back to the users list
if ($id === 0) {
    header('Location: users.php');
    exit();
}

// fetch the user from the database
$stmt = $conn->prepare(
    "SELECT userId, name, email, role
    FROM users
    WHERE userId = ?");
// bind the id parameter and execute the query
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

// if no user found with that id, go back to the users list
if (!$user) {
    header('Location: users.php');
    exit();
}

$errors = [];

// handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // check for each field, trim whitespace, set to empty string if not provided
    if (isset($_POST['name'])) {
        $name = trim($_POST['name']);
    } else {
        $name = '';
    }
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
    } else {
        $email = '';
    }
    if (isset($_POST['role'])) {
        $role = trim($_POST['role']);
    } else {
        $role = '';
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
    }// add error if not valid email format
     elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    // validate role, only allow the three known roles
    $allowedRoles = ['admin', 'organiser', 'attendee'];
    if (!in_array($role, $allowedRoles)) {
        $errors[] = 'Invalid role selected.';
    }

    // check if the email is already taken by a different user
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "SELECT userId FROM users
            WHERE email = ? AND userId != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $existing    = $checkResult->fetch_assoc();
        $stmt->close();
        // if another user has that email
        if ($existing) {
            $errors[] = 'That email is already in use by another account.';
        }
    }

    // only update if there are no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE userId = ?");
        $stmt->bind_param("sssi", $name, $email, $role, $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['flash'] = 'User updated successfully.';
        header('Location: users.php');
        exit();
    }

} else {
    // pre-fill form with the existing user data on first load
    $name  = $user['name'];
    $email = $user['email'];
    $role  = $user['role'];
}
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <h1>Edit User</h1>

    <div class="formWrap">
        <!-- show any validation errors if they exist -->
        <?php if (!empty($errors)): ?>
            <ul class="formErrors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="edit_user.php">
            <input type="hidden" name="id" value="<?= (int)$user['userId'] ?>">

            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">

            <label for="role">Role</label>
            <select id="role" name="role">
                <option value="admin"     <?php if ($role === 'admin')     echo 'selected'; ?>>Admin</option>
                <option value="organiser" <?php if ($role === 'organiser') echo 'selected'; ?>>Organiser</option>
                <option value="attendee"  <?php if ($role === 'attendee')  echo 'selected'; ?>>Attendee</option>
            </select>

            <div class="formActions">
                <button type="submit" class="btn btnPrimary">Save Changes</button>
                <a href="users.php" class="btn btnSecondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
