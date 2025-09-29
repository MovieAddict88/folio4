<?php
// Ensure the user is logged in and functions are available
if (!isset($_SESSION['user_id'])) {
    exit('Access Denied');
}
require_once __DIR__ . '/../../app/functions.php';

$pdo = pdo();

// Handle deleting a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        echo '<div style="color: green; margin-bottom: 15px;">Message deleted successfully!</div>';
    }
}


// Fetch all contact messages
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>

<h3>Contact Form Messages</h3>

<?php if (empty($messages)): ?>
    <p>No messages have been received yet.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Received At</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $message): ?>
                <tr>
                    <td><?php echo e(date('M j, Y, g:i a', strtotime($message['created_at']))); ?></td>
                    <td><?php echo e($message['name']); ?></td>
                    <td><a href="mailto:<?php echo e($message['email']); ?>"><?php echo e($message['email']); ?></a></td>
                    <td style="white-space: pre-wrap; max-width: 400px;"><?php echo e($message['message']); ?></td>
                    <td class="actions">
                        <form action="admin.php?page=messages" method="post" onsubmit="return confirm('Are you sure you want to delete this message?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                            <button type="submit" class="btn-danger" style="background:none; border:none; color:red; cursor:pointer; padding:0; font-size: inherit;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>