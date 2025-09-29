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
            'year_range' => $_POST['year_range'],
            'position' => $_POST['position'],
            'institution' => $_POST['institution'],
            'description' => $_POST['description']
        ];

        if ($id) { // Update existing item
            $stmt = $pdo->prepare("UPDATE experience SET year_range=?, position=?, institution=?, description=? WHERE id=?");
            $stmt->execute(array_values(array_merge($data, ['id' => $id])));
            $message = '<div style="color: green; margin-bottom: 15px;">Experience entry updated successfully!</div>';
        } else { // Insert new item
            $stmt = $pdo->prepare("INSERT INTO experience (year_range, position, institution, description) VALUES (?, ?, ?, ?)");
            $stmt->execute(array_values($data));
            $message = '<div style="color: green; margin-bottom: 15px;">Experience entry added successfully!</div>';
        }
    }

    // DELETE
    if ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM experience WHERE id = ?");
            $stmt->execute([$id]);
            $message = '<div style="color: green; margin-bottom: 15px;">Experience entry deleted successfully!</div>';
        }
    }
}

// Handle GET request for editing an item
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_item = $stmt->fetch();
}

// Fetch all experience entries to display in the table
$items = $pdo->query("SELECT * FROM experience ORDER BY year_range DESC")->fetchAll();
?>

<?php echo $message; ?>

<!-- Form for Adding or Editing an Entry -->
<h3><?php echo $edit_item ? 'Edit' : 'Add New'; ?> Experience Entry</h3>
<form action="admin.php?page=experience" method="post">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?php echo e($edit_item['id'] ?? ''); ?>">

    <div class="form-group">
        <label for="year_range">Year Range (e.g., 2020-Present)</label>
        <input type="text" id="year_range" name="year_range" value="<?php echo e($edit_item['year_range'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="position">Position / Job Title</label>
        <input type="text" id="position" name="position" value="<?php echo e($edit_item['position'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="institution">Company / Institution</label>
        <input type="text" id="institution" name="institution" value="<?php echo e($edit_item['institution'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Job Description & Responsibilities (Optional)</label>
        <textarea id="description" name="description"><?php echo e($edit_item['description'] ?? ''); ?></textarea>
    </div>

    <button type="submit" class="btn"><?php echo $edit_item ? 'Update Entry' : 'Add Entry'; ?></button>
    <?php if ($edit_item): ?>
        <a href="admin.php?page=experience" style="margin-left: 10px;">Cancel Edit</a>
    <?php endif; ?>
</form>

<hr style="margin: 40px 0;">

<!-- Table of Existing Entries -->
<h3>Manage Experience Entries</h3>
<table>
    <thead>
        <tr>
            <th>Year Range</th>
            <th>Position</th>
            <th>Company</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($items)): ?>
            <tr>
                <td colspan="4" style="text-align: center;">No experience entries found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo e($item['year_range']); ?></td>
                    <td><?php echo e($item['position']); ?></td>
                    <td><?php echo e($item['institution']); ?></td>
                    <td class="actions">
                        <a href="admin.php?page=experience&edit=<?php echo $item['id']; ?>">Edit</a>
                        <form action="admin.php?page=experience" method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this entry?');">
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