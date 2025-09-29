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
            'year' => $_POST['year'],
            'degree' => $_POST['degree'],
            'institution' => $_POST['institution'],
            'description' => $_POST['description']
        ];

        if ($id) { // Update existing item
            $stmt = $pdo->prepare("UPDATE education SET year=?, degree=?, institution=?, description=? WHERE id=?");
            $stmt->execute(array_values(array_merge($data, ['id' => $id])));
            $message = '<div style="color: green; margin-bottom: 15px;">Education entry updated successfully!</div>';
        } else { // Insert new item
            $stmt = $pdo->prepare("INSERT INTO education (year, degree, institution, description) VALUES (?, ?, ?, ?)");
            $stmt->execute(array_values($data));
            $message = '<div style="color: green; margin-bottom: 15px;">Education entry added successfully!</div>';
        }
    }

    // DELETE
    if ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM education WHERE id = ?");
            $stmt->execute([$id]);
            $message = '<div style="color: green; margin-bottom: 15px;">Education entry deleted successfully!</div>';
        }
    }
}

// Handle GET request for editing an item
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM education WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_item = $stmt->fetch();
}

// Fetch all education entries to display in the table
$items = $pdo->query("SELECT * FROM education ORDER BY year DESC")->fetchAll();
?>

<?php echo $message; ?>

<!-- Form for Adding or Editing an Entry -->
<h3><?php echo $edit_item ? 'Edit' : 'Add New'; ?> Education Entry</h3>
<form action="admin.php?page=education" method="post">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?php echo e($edit_item['id'] ?? ''); ?>">

    <div class="form-group">
        <label for="year">Year</label>
        <input type="text" id="year" name="year" value="<?php echo e($edit_item['year'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="degree">Degree / Certificate</label>
        <input type="text" id="degree" name="degree" value="<?php echo e($edit_item['degree'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="institution">Institution</label>
        <input type="text" id="institution" name="institution" value="<?php echo e($edit_item['institution'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Description (Optional)</label>
        <textarea id="description" name="description"><?php echo e($edit_item['description'] ?? ''); ?></textarea>
    </div>

    <button type="submit" class="btn"><?php echo $edit_item ? 'Update Entry' : 'Add Entry'; ?></button>
    <?php if ($edit_item): ?>
        <a href="admin.php?page=education" style="margin-left: 10px;">Cancel Edit</a>
    <?php endif; ?>
</form>

<hr style="margin: 40px 0;">

<!-- Table of Existing Entries -->
<h3>Manage Education Entries</h3>
<table>
    <thead>
        <tr>
            <th>Year</th>
            <th>Degree</th>
            <th>Institution</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($items)): ?>
            <tr>
                <td colspan="4" style="text-align: center;">No education entries found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo e($item['year']); ?></td>
                    <td><?php echo e($item['degree']); ?></td>
                    <td><?php echo e($item['institution']); ?></td>
                    <td class="actions">
                        <a href="admin.php?page=education&edit=<?php echo $item['id']; ?>">Edit</a>
                        <form action="admin.php?page=education" method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this entry?');">
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