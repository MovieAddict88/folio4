<?php
// Portfolio Logic
$message = '';
$message_type = '';
$edit_item = null;

// Handle Delete
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    // First, get the image path to delete the file
    $stmt = $pdo->prepare("SELECT image FROM portfolio_items WHERE id = ?");
    $stmt->execute([$id_to_delete]);
    $item = $stmt->fetch();
    if ($item && !empty($item['image']) && file_exists(PUBLIC_PATH . $item['image'])) {
        unlink(PUBLIC_PATH . $item['image']);
    }

    $delete_stmt = $pdo->prepare("DELETE FROM portfolio_items WHERE id = ?");
    if ($delete_stmt->execute([$id_to_delete])) {
        $message = 'Portfolio item deleted successfully!';
        $message_type = 'success';
    } else {
        $message = 'Failed to delete item.';
        $message_type = 'danger';
    }
}


// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $project_url = $_POST['project_url'] ?? '';
    $current_image = $_POST['current_image'] ?? '';
    $image = $current_image;

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = UPLOADS_PATH;
        $file_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (in_array($image_file_type, ['jpg', 'png', 'jpeg', 'gif'])) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image = 'uploads/' . $file_name;
                    if (!empty($current_image) && file_exists(PUBLIC_PATH . $current_image)) {
                        unlink(PUBLIC_PATH . $current_image);
                    }
                }
            } else {
                 $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                 $message_type = 'danger';
            }
        }
    }

    if(empty($message)) {
        if ($id) { // Update
            $stmt = $pdo->prepare("UPDATE portfolio_items SET title=?, description=?, image=?, project_url=? WHERE id=?");
            $stmt->execute([$title, $description, $image, $project_url, $id]);
            $message = 'Item updated successfully!';
            $message_type = 'success';
        } else { // Insert
            $stmt = $pdo->prepare("INSERT INTO portfolio_items (title, description, image, project_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $image, $project_url]);
            $message = 'Item added successfully!';
            $message_type = 'success';
        }
    }
}

// Handle Edit Request
if (isset($_GET['edit'])) {
    $id_to_edit = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM portfolio_items WHERE id = ?");
    $stmt->execute([$id_to_edit]);
    $edit_item = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all portfolio items
$portfolio_items = $pdo->query("SELECT * FROM portfolio_items ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="h2 mb-4">Manage Portfolio</h1>

<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type; ?>">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <h5><?php echo $edit_item ? 'Edit' : 'Add New'; ?> Portfolio Item</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=portfolio" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $edit_item['id'] ?? ''; ?>">
            <input type="hidden" name="current_image" value="<?php echo $edit_item['image'] ?? ''; ?>">

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($edit_item['title'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($edit_item['description'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="project_url" class="form-label">Project URL (optional)</label>
                <input type="url" class="form-control" id="project_url" name="project_url" value="<?php echo htmlspecialchars($edit_item['project_url'] ?? ''); ?>">
            </div>
             <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input class="form-control" type="file" id="image" name="image" <?php echo !$edit_item ? 'required' : ''; ?>>
                <?php if ($edit_item && $edit_item['image']): ?>
                    <div class="mt-2">
                        <img src="../<?php echo htmlspecialchars($edit_item['image']); ?>" width="100" alt="Current Image">
                    </div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $edit_item ? 'Update' : 'Add'; ?> Item</button>
             <?php if ($edit_item): ?>
                <a href="index.php?page=portfolio" class="btn btn-secondary">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Existing Items</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>URL</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($portfolio_items as $item): ?>
                    <tr>
                        <td><img src="../<?php echo htmlspecialchars($item['image']); ?>" width="80" alt="<?php echo htmlspecialchars($item['title']); ?>"></td>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($item['project_url']); ?>" target="_blank">Link</a></td>
                        <td>
                            <a href="index.php?page=portfolio&edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                            <a href="index.php?page=portfolio&delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>