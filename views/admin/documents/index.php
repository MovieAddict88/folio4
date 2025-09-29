<style>
    .upload-form { margin-bottom: 30px; }
    .upload-form input[type="file"] { border: 1px solid #ccc; padding: 8px; border-radius: 4px; }
    .btn-upload { background-color: #28a745; color: #fff; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
    .btn-upload:hover { background-color: #218838; }
    .password-form { display: flex; align-items: center; }
    .password-form input { margin-right: 8px; width: 150px; padding: 5px; }
    .password-form button { padding: 5px 10px; }
    .status-protected { color: #28a745; font-weight: bold; }
    .status-unprotected { color: #ffc107; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px; border: 1px solid #e0e0e0; text-align: left; }
    th { background-color: #f8f9fa; }
    .actions a { color: #dc3545; text-decoration: none; }
</style>

<h3>Upload New Document</h3>
<div class="upload-form">
    <form action="<?= BASE_URL ?>admin/documents/upload" method="POST" enctype="multipart/form-data">
        <input type="file" name="document" required>
        <button type="submit" class="btn-upload">Upload</button>
    </form>
</div>

<hr style="margin: 30px 0;">

<h3>Manage Documents</h3>
<table>
    <thead>
        <tr>
            <th>Filename</th>
            <th>Password Protection</th>
            <th>Date Uploaded</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($documents)): ?>
            <tr>
                <td colspan="4" style="text-align:center;">No documents found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><?= e($doc['original_filename']) ?></td>
                    <td>
                        <form action="<?= BASE_URL ?>admin/documents/set_password/<?= e($doc['id']) ?>" method="POST" class="password-form">
                            <input type="text" name="password" placeholder="Enter new password...">
                            <button type="submit">Set/Update</button>
                        </form>
                         <small>
                            Status:
                            <?php if (!empty($doc['password'])): ?>
                                <span class="status-protected">Protected</span> (Leave input blank and submit to remove password)
                            <?php else: ?>
                                <span class="status-unprotected">Not Protected</span>
                            <?php endif; ?>
                        </small>
                    </td>
                    <td><?= e(date('M j, Y, g:i a', strtotime($doc['created_at']))) ?></td>
                    <td class="actions">
                        <a href="<?= BASE_URL ?>admin/documents/delete/<?= e($doc['id']) ?>" onclick="return confirm('Are you sure you want to delete this document?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>