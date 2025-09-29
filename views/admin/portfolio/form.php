<style>
    .form-group { margin-bottom: 20px; }
    label { display: block; font-weight: 600; margin-bottom: 5px; }
    input[type="text"], textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    textarea {
        min-height: 150px;
        resize: vertical;
    }
    .btn-save { display: inline-block; background-color: #007bff; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
    .btn-save:hover { background-color: #0056b3; }
    .current-image { max-width: 200px; display: block; margin-top: 10px; }
</style>

<form action="<?= BASE_URL ?>admin/portfolio/save" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= e($item['id']) ?>">
    <input type="hidden" name="existing_image" value="<?= e($item['image']) ?>">

    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= e($item['title']) ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= e($item['description']) ?></textarea>
    </div>

    <div class="form-group">
        <label for="image">Image</label>
        <input type="file" id="image" name="image" accept="image/*">
        <?php if (!empty($item['image'])): ?>
            <p><strong>Current Image:</strong></p>
            <img src="<?= BASE_URL ?>public/uploads/<?= e($item['image']) ?>" alt="Current Image" class="current-image">
            <p><small>Uploading a new image will replace the current one.</small></p>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn-save">Save Item</button>
</form>