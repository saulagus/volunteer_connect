<?php
// shows all users with edit and delete options, only for admins tho

session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_role('admin');
// fetch newest users first
$result = $conn->query(
    "SELECT userId, name, email, role, createdAt
    FROM users
    ORDER BY createdAt DESC");
// collect all rows into an array to loop through in the HTML
$users = [];
$row = $result->fetch_assoc();
while ($row !== null) {
    // add current row to users array
    $users[] = $row;
    // go to next row
    $row = $result->fetch_assoc();
}
?>
<?php require_once '../includes/header.php'; ?>
<main>
    <h1>All Users</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= htmlspecialchars($user['createdAt']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= (int)$user['userId'] ?>">Edit</a>
                    <form method="POST" action="delete_user.php">
                        <input type="hidden" name="id" value="<?= (int)$user['userId'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php require_once '../includes/footer.php'; ?>
