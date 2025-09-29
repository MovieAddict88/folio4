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
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'external_link' => $_POST['external_link'],
            'category_tags' => $_POST['category_tags']
        ];

        // Handle image uploads
        $image_album = $_POST['existing_images'] ? json_decode($_POST['existing_images'], true) : [];
        if (isset($_FILES['image_album']) && !empty($_FILES['image_album']['name'][0])) {
            $upload_dir = APP_ROOT . '/public/uploads/projects/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            foreach ($_FILES['image_album']['tmp_name'] as $key => $tmp_name) {
                $filename = time() . '_' . basename($_FILES['image_album']['name'][$key]);
                $target_file = $upload_dir . $filename;
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $image_album[] = 'uploads/projects/' . $filename;
                }
            }
        }
        $data['image_album'] = json_encode($image_album);

        if ($id) { // Update existing item
            $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, external_link=?, category_tags=?, image_album=? WHERE id=?");
            $stmt->execute(array_values(array_merge($data, ['id' => $id])));
            $message = '<div style="color: green; margin-bottom: 15px;">Project updated successfully!</div>';
        } else { // Insert new item
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, external_link, category_tags, image_album) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(array_values($data));
            $message = '<div style="color: green; margin-bottom: 15px;">Project added successfully!</div>';
        }
    }

    // DELETE
    if ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        if ($id) {
            // Optional: Also delete associated image files from server
            $stmt = $pdo->prepare("SELECT image_album FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            $album_json = $stmt->fetchColumn();
            if ($album_json) {
                $album_files = json_decode($album_json, true);
                foreach ($album_files as $file) {
                    $full_path = APP_ROOT . '/public/' . $file;
                    if (file_exists($full_path)) {
                        unlink($full_path);
                    }
                }
            }

            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            $message = '<div style="color: green; margin-bottom: 15px;">Project deleted successfully!</div>';
        }
    }
}

// Handle GET request for editing an item
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_item = $stmt->fetch();
}

// Fetch all project entries to display in the table
$items = $pdo->query("SELECT id, title, category_tags FROM projects ORDER BY id DESC")->fetchAll();
?>

<?php echo $message; ?>

<!-- Form for Adding or Editing a Project -->
<h3><?php echo $edit_item ? 'Edit' : 'Add New'; ?> Project</h3>
<form action="admin.php?page=projects" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?php echo e($edit_item['id'] ?? ''); ?>">

    <div class="form-group">
        <label for="title">Project Title</label>
        <input type="text" id="title" name="title" value="<?php echo e($edit_item['title'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" required><?php echo e($edit_item['description'] ?? ''); ?></textarea>
    </div>
    <div class="form-group">
        <label for="image_album">Image Album (can select multiple)</label>
        <input type="file" id="image_album" name="image_album[]" multiple>
        <?php
        $current_images = [];
        if (!empty($edit_item['image_album'])) {
            $current_images = json_decode($edit_item['image_album'], true);
        }
        ?>
        <div class="image-previews" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
            <?php if (!empty($current_images)): ?>
                <?php foreach ($current_images as $image_path): ?>
                    <?php if (file_exists(APP_ROOT . '/public/' . $image_path)): ?>
                    <div class="preview-container">
                        <img src="<?php echo e($image_path); ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                        <a href="#" class="remove-image" data-path="<?php echo e($image_path); ?>" style="color: red; text-decoration: none; display: block; text-align: center;">Remove</a>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <input type="hidden" name="existing_images" id="existing_images_input" value='<?php echo e($edit_item['image_album'] ?? '[]'); ?>'>
        <p><small>Uploading new files will add to the album. Use "Remove" to delete existing images before saving.</small></p>
    </div>
    <div class="form-group">
        <label for="external_link">External Link (e.g., GitHub, Live Demo)</label>
        <input type="text" id="external_link" name="external_link" value="<?php echo e($edit_item['external_link'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label for="category_tags">Category Tags (comma-separated)</label>
        <input type="text" id="category_tags" name="category_tags" value="<?php echo e($edit_item['category_tags'] ?? ''); ?>">
    </div>

    <button type="submit" class="btn"><?php echo $edit_item ? 'Update Project' : 'Add Project'; ?></button>
    <?php if ($edit_item): ?>
        <a href="admin.php?page=projects" style="margin-left: 10px;">Cancel Edit</a>
    <?php endif; ?>
</form>

<hr style="margin: 40px 0;">

<!-- Table of Existing Projects -->
<h3>Manage Projects</h3>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const removeImageLinks = document.querySelectorAll('.remove-image');
    const existingImagesInput = document.getElementById('existing_images_input');

    removeImageLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const imagePathToRemove = this.getAttribute('data-path');
            const previewContainer = this.parentElement;

            // Remove the image from the DOM
            previewContainer.style.display = 'none';

            // Update the hidden input value
            let currentImages = JSON.parse(existingImagesInput.value);
            let updatedImages = currentImages.filter(path => path !== imagePathToRemove);
            existingImagesInput.value = JSON.stringify(updatedImages);
        });
    });
});
</script>
<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Tags</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($items)): ?>
            <tr>
                <td colspan="3" style="text-align: center;">No projects found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo e($item['title']); ?></td>
                    <td><?php echo e($item['category_tags']); ?></td>
                    <td class="actions">
                        <a href="admin.php?page=projects&edit=<?php echo $item['id']; ?>">Edit</a>
                        <form action="admin.php?page=projects" method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this project?');">
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