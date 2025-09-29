<style>
    .actions a { margin-right: 10px; text-decoration: none; }
    .btn-add { display: inline-block; background-color: #28a745; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 4px; margin-bottom: 20px; }
    .btn-add:hover { background-color: #218838; }
    .btn-edit { color: #007bff; }
    .btn-delete { color: #dc3545; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px; border: 1px solid #e0e0e0; text-align: left; }
    th { background-color: #f8f9fa; }
    .thumbnail { max-width: 100px; max-height: 60px; object-fit: cover; }
</style>

<a href="<?= BASE_URL ?>admin/portfolio/add" class="btn-add">Add New Portfolio Item</a>

<table>
    <thead>
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($items)): ?>
            <tr>
                <td colspan="3" style="text-align:center;">No portfolio items found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= BASE_URL ?>public/uploads/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" class="thumbnail">
                        <?php endif; ?>
                    </td>
                    <td><?= e($item['title']) ?></td>
                    <td class="actions">
                        <a href="<?= BASE_URL ?>admin/portfolio/edit/<?= e($item['id']) ?>" class="btn-edit">Edit</a>
                        <a href="<?= BASE_URL ?>admin/portfolio/delete/<?= e($item['id']) ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>