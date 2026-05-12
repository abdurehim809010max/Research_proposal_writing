<?php
require_once '../config.php';
$pageTitle = 'Manage Menu';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = sanitize($_POST['name']);
        $categoryId = (int)$_POST['category_id'];
        $description = sanitize($_POST['description']);
        $price = (float)$_POST['price'];
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

        $stmt = $conn->prepare("INSERT INTO menu_items (category_id, name, description, price, is_available, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdii", $categoryId, $name, $description, $price, $isAvailable, $isFeatured);
        if ($stmt->execute()) {
            setFlash('success', 'Menu item added successfully.');
        } else {
            setFlash('error', 'Failed to add menu item.');
        }
        $stmt->close();
        redirect('manage_menu.php');
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = sanitize($_POST['name']);
        $categoryId = (int)$_POST['category_id'];
        $description = sanitize($_POST['description']);
        $price = (float)$_POST['price'];
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

        $stmt = $conn->prepare("UPDATE menu_items SET category_id = ?, name = ?, description = ?, price = ?, is_available = ?, is_featured = ? WHERE id = ?");
        $stmt->bind_param("issdiii", $categoryId, $name, $description, $price, $isAvailable, $isFeatured, $id);
        if ($stmt->execute()) {
            setFlash('success', 'Menu item updated successfully.');
        } else {
            setFlash('error', 'Failed to update menu item.');
        }
        $stmt->close();
        redirect('manage_menu.php');
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            setFlash('success', 'Menu item deleted successfully.');
        } else {
            setFlash('error', 'Failed to delete menu item.');
        }
        $stmt->close();
        redirect('manage_menu.php');
    }
}

// Fetch data
$categories = $conn->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
$menuItems = $conn->query("SELECT mi.*, c.name as category_name FROM menu_items mi JOIN categories c ON mi.category_id = c.id ORDER BY c.name, mi.name");

// For edit mode
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editItem = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

include '../includes/admin_header.php';
?>

<h1 class="admin-page-title"><i class="fas fa-utensils"></i> Manage Menu Items</h1>

<div class="admin-layout">
    <!-- Add/Edit Form -->
    <div class="admin-form-card">
        <h3><?php echo $editItem ? 'Edit Menu Item' : 'Add New Menu Item'; ?></h3>
        <form method="POST" action="" id="menuForm">
            <input type="hidden" name="action" value="<?php echo $editItem ? 'edit' : 'add'; ?>">
            <?php if ($editItem): ?>
                <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Item Name</label>
                <input type="text" id="name" name="name" value="<?php echo $editItem ? htmlspecialchars($editItem['name']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php 
                    $categories->data_seek(0);
                    while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($editItem && $editItem['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price (<?php echo CURRENCY; ?>)</label>
                <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $editItem ? $editItem['price'] : ''; ?>" required>
            </div>

            <div class="form-group form-check-group">
                <label class="form-check">
                    <input type="checkbox" name="is_available" <?php echo (!$editItem || $editItem['is_available']) ? 'checked' : ''; ?>>
                    <span>Available</span>
                </label>
                <label class="form-check">
                    <input type="checkbox" name="is_featured" <?php echo ($editItem && $editItem['is_featured']) ? 'checked' : ''; ?>>
                    <span>Featured</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $editItem ? 'Update Item' : 'Add Item'; ?>
                </button>
                <?php if ($editItem): ?>
                    <a href="manage_menu.php" class="btn btn-outline">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Items Table -->
    <div class="admin-table-card">
        <div class="table-header">
            <h3>All Menu Items (<?php echo $menuItems->num_rows; ?>)</h3>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Available</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($item = $menuItems->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                        <td><?php echo formatPrice($item['price']); ?></td>
                        <td><span class="status-dot <?php echo $item['is_available'] ? 'active' : 'inactive'; ?>"></span></td>
                        <td><?php echo $item['is_featured'] ? '<i class="fas fa-star text-warning"></i>' : '-'; ?></td>
                        <td class="actions">
                            <a href="manage_menu.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this item?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
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
