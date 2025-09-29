<?php
// Ensure the user is logged in and functions are available
if (!isset($_SESSION['user_id'])) {
    exit('Access Denied');
}
require_once __DIR__ . '/../../app/functions.php';

$pdo = pdo();
$message = '';
$edit_item = null;

// Handle POST requests (add, update, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD or UPDATE
    if ($action === 'save') {
        $id = $_POST['id'] ?? null;
        $data = [
            'quote' => $_POST['quote'],
            'author' => $_POST['author'],
            'author_role' => $_POST['author_role']
        ];

        if ($id) { // Update existing item
            $stmt = $pdo->prepare("UPDATE testimonials SET quote=?, author=?, author_role=? WHERE id=?");
            $stmt->execute(array_values(array_merge($data, ['id' => $id])));
            $message = '<div style="color: green; margin-bottom: 15px;">Testimonial updated successfully!</div>';
        } else { // Insert new item
            $stmt = $pdo->prepare("INSERT INTO testimonials (quote, author, author_role) VALUES (?, ?, ?)");
            $stmt->execute(array_values($data));
            $message = '<div style="color: green; margin-bottom: 15px;">Testimonial added successfully!</div>';
        }
    }

    // DELETE
    if ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
            $stmt->execute([$id]);
            $message = '<div style="color: green; margin-bottom: 15px;">Testimonial deleted successfully!</div>';
        }
    }
}

// Handle GET request for editing an item
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_item = $stmt->fetch();
}

// Fetch all testimonial entries to display in the table
$items = $pdo->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll();
?>

<?php echo $message; ?>

<!-- Form for Adding or Editing a Testimonial -->
<h3><?php echo $edit_item ? 'Edit' : 'Add New'; ?> Testimonial</h3>
<form action="admin.php?page=testimonials" method="post">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?php echo e($edit_item['id'] ?? ''); ?>">

    <div class="form-group">
        <label for="quote">Quote</label>
        <textarea id="quote" name="quote" required><?php echo e($edit_item['quote'] ?? ''); ?></textarea>
    </div>
    <div class="form-group">
        <label for="author">Author Name</label>
        <input type="text" id="author" name="author" value="<?php echo e($edit_item['author'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="author_role">Author's Role/Position (e.g., Mentor, Client)</label>
        <input type="text" id="author_role" name="author_role" value="<?php echo e($edit_item['author_role'] ?? ''); ?>">
    </div>

    <button type="submit" class="btn"><?php echo $edit_item ? 'Update Testimonial' : 'Add Testimonial'; ?></button>
    <?php if ($edit_item): ?>
        <a href="admin.php?page=testimonials" style="margin-left: 10px;">Cancel Edit</a>
    <?php endif; ?>
</form>

<hr style="margin: 40px 0;">

<!-- Table of Existing Testimonials -->
<h3>Manage Testimonials</h3>
<table>
    <thead>
        <tr>
            <th>Quote</th>
            <th>Author</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($items)): ?>
            <tr>
                <td colspan="3" style="text-align: center;">No testimonials found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo e(substr($item['quote'], 0, 80)); ?>...</td>
                    <td><?php echo e($item['author']); ?> (<?php echo e($item['author_role']); ?>)</td>
                    <td class="actions">
                        <a href="admin.php?page=testimonials&edit=<?php echo $item['id']; ?>">Edit</a>
                        <form action="admin.php?page=testimonials" method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="btn-danger" style="background:none; border:none; color:red; cursor:pointer; padding:0; font-size: inherit;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>