<?php
require_once '../config.php';
$pageTitle = 'Manage Categories';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        if ($stmt->execute()) {
            setFlash('success', 'Category added successfully.');
        } else {
            setFlash('error', 'Failed to add category.');
        }
        $stmt->close();
        redirect('manage_categories.php');
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, is_active = ? WHERE id = ?");
        $stmt->bind_param("ssii", $name, $description, $isActive, $id);
        if ($stmt->execute()) {
            setFlash('success', 'Category updated successfully.');
        } else {
            setFlash('error', 'Failed to update category.');
        }
        $stmt->close();
        redirect('manage_categories.php');
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            setFlash('success', 'Category deleted successfully.');
        } else {
            setFlash('error', 'Cannot delete category with existing menu items.');
        }
        $stmt->close();
        redirect('manage_categories.php');
    }
}

$categories = $conn->query("SELECT c.*, COUNT(mi.id) as item_count FROM categories c LEFT JOIN menu_items mi ON c.id = mi.category_id GROUP BY c.id ORDER BY c.name");

$editCat = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editCat = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

include '../includes/admin_header.php';
?>

<h1 class="admin-page-title"><i class="fas fa-list"></i> Manage Categories</h1>

<div class="admin-layout">
    <div class="admin-form-card">
        <h3><?php echo $editCat ? 'Edit Category' : 'Add New Category'; ?></h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="<?php echo $editCat ? 'edit' : 'add'; ?>">
            <?php if ($editCat): ?>
                <input type="hidden" name="id" value="<?php echo $editCat['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" value="<?php echo $editCat ? htmlspecialchars($editCat['name']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"><?php echo $editCat ? htmlspecialchars($editCat['description']) : ''; ?></textarea>
            </div>

            <?php if ($editCat): ?>
            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="is_active" <?php echo $editCat['is_active'] ? 'checked' : ''; ?>>
                    <span>Active</span>
                </label>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $editCat ? 'Update' : 'Add Category'; ?>
                </button>
                <?php if ($editCat): ?>
                    <a href="manage_categories.php" class="btn btn-outline">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="admin-table-card">
        <h3>All Categories</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Items</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($cat = $categories->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td><?php echo htmlspecialchars(substr($cat['description'], 0, 50)); ?></td>
                        <td><?php echo $cat['item_count']; ?></td>
                        <td><span class="status-dot <?php echo $cat['is_active'] ? 'active' : 'inactive'; ?>"></span></td>
                        <td class="actions">
                            <a href="manage_categories.php?edit=<?php echo $cat['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this category and all its items?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
