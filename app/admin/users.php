<?php
// shows all users with edit and delete options, only for admins tho

session_start();
require_once '../db_connect.php';
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

    <div class="tableWrap">
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
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="emptyState">No users found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php
                            if ($user['role'] === 'admin') {
                                $roleClass = 'roleAdmin';
                            } elseif ($user['role'] === 'organiser') {
                                $roleClass = 'roleOrganiser';
                            } else {
                                $roleClass = 'roleAttendee';
                            }
                            ?>
                            <span class="roleBadge <?= $roleClass ?>"><?= htmlspecialchars($user['role']) ?></span>
                        </td>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($user['createdAt']))) ?></td>
                        <td>
                            <div class="tableActions">
                                <a href="edit_user.php?id=<?= (int)$user['userId'] ?>" class="btn btnSecondary">Edit</a>
                                <form method="POST" action="delete_user.php" class="inlineForm">
                                    <input type="hidden" name="id" value="<?= (int)$user['userId'] ?>">
                                    <button type="submit" class="btn btnDanger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
