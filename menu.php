<?php
require_once 'config.php';
$pageTitle = 'Our Menu';

// Get categories
$catQuery = "SELECT * FROM categories WHERE is_active = 1 ORDER BY name";
$catResult = $conn->query($catQuery);

// Filter by category
$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Build menu query
$menuQuery = "SELECT mi.*, c.name as category_name FROM menu_items mi 
              JOIN categories c ON mi.category_id = c.id 
              WHERE mi.is_available = 1";
if ($selectedCategory > 0) {
    $menuQuery .= " AND mi.category_id = $selectedCategory";
}
$menuQuery .= " ORDER BY c.name, mi.name";
$menuResult = $conn->query($menuQuery);

include 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-utensils"></i> Our Menu</h1>
        <p>Discover the rich flavors of Ethiopian cuisine</p>
    </div>
</section>

<section class="section menu-page-section">
    <div class="container">
        <!-- Category Filter -->
        <div class="filter-bar">
            <a href="menu.php" class="filter-btn <?php echo $selectedCategory === 0 ? 'active' : ''; ?>">All</a>
            <?php if ($catResult): while ($cat = $catResult->fetch_assoc()): ?>
                <a href="menu.php?category=<?php echo $cat['id']; ?>" 
                   class="filter-btn <?php echo $selectedCategory === (int)$cat['id'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endwhile; endif; ?>
        </div>

        <!-- Search -->
        <div class="search-container mb-2">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="liveSearch" placeholder="Search menu items..." autocomplete="off">
                <div class="search-results" id="searchResults"></div>
            </div>
        </div>

        <!-- Menu Grid -->
        <div class="menu-grid" id="menuGrid">
            <?php if ($menuResult && $menuResult->num_rows > 0): ?>
                <?php while ($item = $menuResult->fetch_assoc()): ?>
                <div class="menu-card" data-category="<?php echo $item['category_id']; ?>">
                    <div class="menu-card-image">
                        <img src="<?php echo SITE_URL; ?>/images/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             class="menu-card-img"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="menu-img-placeholder" style="display:none;">
                            <i class="fas fa-drumstick-bite"></i>
                        </div>
                        <span class="menu-card-category"><?php echo htmlspecialchars($item['category_name']); ?></span>
                        <?php if ($item['is_featured']): ?>
                            <span class="menu-card-badge">Featured</span>
                        <?php endif; ?>
                    </div>
                    <div class="menu-card-body">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="menu-card-footer">
                            <span class="menu-price"><?php echo formatPrice($item['price']); ?></span>
                            <?php if (isLoggedIn() && !isAdmin()): ?>
                                <button class="btn btn-sm btn-primary add-to-cart" data-id="<?php echo $item['id']; ?>">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            <?php elseif (!isLoggedIn()): ?>
                                <a href="login.php" class="btn btn-sm btn-outline">Login to Order</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-items">
                    <i class="fas fa-search"></i>
                    <p>No menu items found in this category.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script src="<?php echo SITE_URL; ?>/js/search.js"></script>
<script src="<?php echo SITE_URL; ?>/js/cart.js"></script>

<?php include 'includes/footer.php'; ?>
