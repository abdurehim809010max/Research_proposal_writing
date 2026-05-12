<?php
require_once 'config.php';
$pageTitle = 'Home';

// Fetch featured menu items
$featuredQuery = "SELECT mi.*, c.name as category_name FROM menu_items mi 
                  JOIN categories c ON mi.category_id = c.id 
                  WHERE mi.is_featured = 1 AND mi.is_available = 1 
                  LIMIT 8";
$featuredResult = $conn->query($featuredQuery);

// Fetch categories
$catQuery = "SELECT * FROM categories WHERE is_active = 1";
$catResult = $conn->query($catQuery);

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero" id="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title animate-fadeIn">Welcome to <span><?php echo SITE_NAME; ?></span></h1>
        <p class="hero-subtitle animate-fadeIn"><?php echo SITE_TAGLINE; ?> - Where Every Meal Tells a Story</p>
        <p class="hero-text animate-fadeIn">Experience the warmth and rich flavors of Ethiopian tradition. From our signature Doro Wot to the freshest Buna ceremony, every dish is crafted with love and authenticity.</p>
        <div class="hero-buttons animate-fadeIn">
            <a href="menu.php" class="btn btn-primary btn-lg">Explore Our Menu</a>
            <a href="reservations.php" class="btn btn-outline btn-lg">Book a Table</a>
        </div>
    </div>
    <div class="hero-scroll">
        <a href="#about"><i class="fas fa-chevron-down"></i></a>
    </div>
</section>

<!-- About Section -->
<section class="section about-section" id="about">
    <div class="container">
        <div class="section-header">
            <h2>About Us</h2>
            <p>Bringing the Heart of Ethiopia to Your Plate</p>
        </div>
        <div class="about-grid">
            <div class="about-content">
                <h3>Our Story</h3>
                <p>Founded in the heart of Addis Ababa, Habesha Kitchen is more than just a restaurant - it's a celebration of Ethiopian culture, community, and cuisine. Our recipes have been passed down through generations, each dish carrying the warmth and tradition of Ethiopian hospitality.</p>
                <p>We source our spices directly from local Ethiopian farms, ensuring the authentic flavors of berbere, mitmita, and other traditional seasonings shine through in every dish.</p>
                <div class="about-features">
                    <div class="about-feature">
                        <i class="fas fa-leaf"></i>
                        <h4>Fresh Ingredients</h4>
                        <p>Locally sourced, organic ingredients</p>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-heart"></i>
                        <h4>Made with Love</h4>
                        <p>Traditional recipes, authentic taste</p>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-users"></i>
                        <h4>Community First</h4>
                        <p>Supporting local farmers and communities</p>
                    </div>
                </div>
            </div>
            <div class="about-image">
                <div class="about-img-placeholder">
                    <i class="fas fa-store"></i>
                    <p>Habesha Kitchen Interior</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Menu Section -->
<section class="section featured-section" id="featured">
    <div class="container">
        <div class="section-header">
            <h2>Featured Dishes</h2>
            <p>Our Chef's Recommended Selections</p>
        </div>

        <!-- Live Search Bar -->
        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="liveSearch" placeholder="Search our menu..." autocomplete="off">
                <div class="search-results" id="searchResults"></div>
            </div>
        </div>

        <div class="menu-grid" id="featuredGrid">
            <?php if ($featuredResult && $featuredResult->num_rows > 0): ?>
                <?php while ($item = $featuredResult->fetch_assoc()): ?>
                <div class="menu-card">
                    <div class="menu-card-image">
                        <div class="menu-img-placeholder">
                            <i class="fas fa-drumstick-bite"></i>
                        </div>
                        <span class="menu-card-category"><?php echo htmlspecialchars($item['category_name']); ?></span>
                    </div>
                    <div class="menu-card-body">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="menu-card-footer">
                            <span class="menu-price"><?php echo formatPrice($item['price']); ?></span>
                            <?php if (isLoggedIn() && !isAdmin()): ?>
                                <button class="btn btn-sm btn-primary add-to-cart" data-id="<?php echo $item['id']; ?>">
                                    <i class="fas fa-cart-plus"></i> Add
                                </button>
                            <?php elseif (!isLoggedIn()): ?>
                                <a href="login.php" class="btn btn-sm btn-outline">Login to Order</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-items">No featured items available at the moment.</p>
            <?php endif; ?>
        </div>
        <div class="text-center mt-2">
            <a href="menu.php" class="btn btn-primary">View Full Menu</a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Our Menu Categories</h2>
            <p>Explore Our Diverse Ethiopian Cuisine</p>
        </div>
        <div class="categories-grid">
            <?php if ($catResult && $catResult->num_rows > 0): ?>
                <?php while ($cat = $catResult->fetch_assoc()): ?>
                <a href="menu.php?category=<?php echo $cat['id']; ?>" class="category-card">
                    <div class="category-icon">
                        <?php
                        $icons = [
                            'Traditional Ethiopian' => 'fa-pepper-hot',
                            'Grilled & Roasted' => 'fa-fire',
                            'Vegetarian/Fasting' => 'fa-seedling',
                            'Beverages' => 'fa-mug-hot',
                            'Desserts & Snacks' => 'fa-cookie-bite',
                            'Breakfast' => 'fa-sun'
                        ];
                        $icon = $icons[$cat['name']] ?? 'fa-utensils';
                        ?>
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                    <p><?php echo htmlspecialchars($cat['description']); ?></p>
                </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="section stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <i class="fas fa-smile"></i>
                <h3 class="stat-number" data-count="5000">0</h3>
                <p>Happy Customers</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-utensils"></i>
                <h3 class="stat-number" data-count="50">0</h3>
                <p>Menu Items</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-award"></i>
                <h3 class="stat-number" data-count="15">0</h3>
                <p>Years of Service</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-star"></i>
                <h3 class="stat-number" data-count="4">0</h3>
                <p>Star Rating</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section testimonials-section">
    <div class="container">
        <div class="section-header">
            <h2>What Our Guests Say</h2>
            <p>Real Reviews from Our Valued Customers</p>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p>"The best Doro Wot I've ever had outside of my grandmother's kitchen. Truly authentic Ethiopian flavors!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar"><i class="fas fa-user-circle"></i></div>
                    <div>
                        <h4>Meron Tadesse</h4>
                        <span>Regular Customer</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p>"Amazing atmosphere and incredible food. The coffee ceremony is a must-experience. We come here every weekend!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar"><i class="fas fa-user-circle"></i></div>
                    <div>
                        <h4>Daniel Girma</h4>
                        <span>Food Enthusiast</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                </div>
                <p>"Perfect for group dining. The Beyaynetu platter is incredible - so many flavors in one plate. Highly recommended!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar"><i class="fas fa-user-circle"></i></div>
                    <div>
                        <h4>Fatima Abdi</h4>
                        <span>Local Blogger</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Experience Ethiopian Cuisine?</h2>
            <p>Book your table now and enjoy an unforgettable dining experience at Habesha Kitchen</p>
            <div class="cta-buttons">
                <a href="reservations.php" class="btn btn-primary btn-lg">Reserve a Table</a>
                <a href="tel:+251911000000" class="btn btn-outline btn-lg"><i class="fas fa-phone"></i> Call Us</a>
            </div>
        </div>
    </div>
</section>

<script src="<?php echo SITE_URL; ?>/js/search.js"></script>

<?php include 'includes/footer.php'; ?>
