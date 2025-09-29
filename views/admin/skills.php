<?php
// Ensure the user is logged in and functions are available
if (!isset($_SESSION['user_id'])) {
    exit('Access Denied');
}
require_once __DIR__ . '/../../app/functions.php';

$pdo = pdo();
$message = '';

// Handle POST requests (add or delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD NEW SKILL
    if ($action === 'add') {
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? '';
        $level = $_POST['level'] ?? 80; // Default level

        if ($name && $type) {
            $stmt = $pdo->prepare("INSERT INTO skills (name, type, level) VALUES (?, ?, ?)");
            $stmt->execute([$name, $type, $level]);
            $message = '<div style="color: green; margin-bottom: 15px;">Skill added successfully!</div>';
        }
    }

    // DELETE SKILL
    if ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
            $stmt->execute([$id]);
            $message = '<div style="color: green; margin-bottom: 15px;">Skill deleted successfully!</div>';
        }
    }
}

// Fetch all skills to display
$skills = $pdo->query("SELECT * FROM skills ORDER BY type, name")->fetchAll();
$soft_skills = array_filter($skills, fn($skill) => $skill['type'] === 'soft');
$hard_skills = array_filter($skills, fn($skill) => $skill['type'] === 'hard');

?>

<?php echo $message; ?>

<!-- Form for Adding a New Skill -->
<h3>Add New Skill</h3>
<form action="admin.php?page=skills" method="post" style="max-width: 600px;">
    <input type="hidden" name="action" value="add">
    <div class="form-group">
        <label for="name">Skill Name</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="type">Skill Type</label>
        <select id="type" name="type" required>
            <option value="soft">Soft Skill</option>
            <option value="hard">Hard Skill</option>
        </select>
    </div>
    <div class="form-group">
        <label for="level">Proficiency Level (%)</label>
        <input type="number" id="level" name="level" min="1" max="100" value="80">
    </div>
    <button type="submit" class="btn">Add Skill</button>
</form>

<hr style="margin: 40px 0;">

<!-- Tables of Existing Skills -->
<div style="display: flex; gap: 40px; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 300px;">
        <h3>Soft Skills</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Level</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($soft_skills)): ?>
                    <tr><td colspan="3" style="text-align: center;">No soft skills found.</td></tr>
                <?php else: ?>
                    <?php foreach ($soft_skills as $skill): ?>
                        <tr>
                            <td><?php echo e($skill['name']); ?></td>
                            <td><?php echo e($skill['level']); ?>%</td>
                            <td class="actions">
                                <form action="admin.php?page=skills" method="post" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $skill['id']; ?>">
                                    <button type="submit" class="btn-danger" style="background:none; border:none; color:red; cursor:pointer; padding:0; font-size: inherit;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="flex: 1; min-width: 300px;">
        <h3>Hard Skills</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Level</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                 <?php if (empty($hard_skills)): ?>
                    <tr><td colspan="3" style="text-align: center;">No hard skills found.</td></tr>
                <?php else: ?>
                    <?php foreach ($hard_skills as $skill): ?>
                        <tr>
                            <td><?php echo e($skill['name']); ?></td>
                            <td><?php echo e($skill['level']); ?>%</td>
                            <td class="actions">
                                <form action="admin.php?page=skills" method="post" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $skill['id']; ?>">
                                    <button type="submit" class="btn-danger" style="background:none; border:none; color:red; cursor:pointer; padding:0; font-size: inherit;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>