# Habesha Kitchen - Restaurant Management System

A fully functional dynamic web application for managing an Ethiopian restaurant. Built with vanilla **HTML, CSS, JavaScript, PHP, and MySQL** for the Web Programming Technologies course project.

---

## System Overview & Objectives

**Habesha Kitchen** is a restaurant management system designed for the Ethiopian context. It allows customers to browse the menu, place orders, make table reservations, and contact the restaurant. Administrators can manage menu items, categories, orders, reservations, users, and contact messages through a dedicated admin panel.

### Key Features

**User Features:**
- Browse menu with category filtering
- **Live search** (AJAX/Fetch - asynchronous) across menu items
- Shopping cart with add/remove/update (AJAX)
- Place orders (dine-in, takeaway, delivery)
- Make table reservations
- View order history and reservation status
- User profile management with password change
- Contact form submission
- **Real-time email validation** during signup (AJAX)
- Password strength indicator
- Client-side form validation

**Admin Features:**
- Dashboard with statistics (total orders, revenue, users, etc.)
- Full CRUD for menu items and categories
- Order management with status updates (AJAX)
- Reservation management (confirm, cancel, complete)
- User management (view, change role, delete)
- Contact message management (read, delete)
- Search and filter functionality across all sections

**Security:**
- Password hashing (`password_hash` / `password_verify`)
- Prepared statements (SQL injection prevention) via `mysqli`
- Input sanitization (`htmlspecialchars`, `trim`, `stripslashes`)
- Session-based authentication
- Role-based access control (user/admin)

---

## Database Design

### Entity Relationship Diagram (ERD)

```
┌──────────┐     ┌────────────┐     ┌──────────────┐
│  users   │     │ categories │     │ menu_items   │
├──────────┤     ├────────────┤     ├──────────────┤
│ id (PK)  │     │ id (PK)    │     │ id (PK)      │
│ full_name│     │ name       │────>│ category_id  │
│ email    │     │ description│     │ name         │
│ phone    │     │ image      │     │ description  │
│ password │     │ is_active  │     │ price        │
│ role     │     │ created_at │     │ image        │
│ address  │     └────────────┘     │ is_available │
│ created_at│                       │ is_featured  │
│ updated_at│                       │ created_at   │
└──────────┘                        └──────────────┘
     │                                     │
     │  ┌────────────┐  ┌──────────────┐   │
     │  │  orders    │  │ order_items  │   │
     │  ├────────────┤  ├──────────────┤   │
     └─>│ id (PK)    │  │ id (PK)      │   │
        │ user_id(FK)│─>│ order_id(FK) │   │
        │ total_amount│  │ menu_item_id │<──┘
        │ status     │  │ quantity     │
        │ order_type │  │ unit_price   │
        │ delivery_  │  │ subtotal     │
        │  address   │  └──────────────┘
        │ notes      │
        │ created_at │
        └────────────┘
     │
     │  ┌──────────────────┐    ┌───────────────────┐
     │  │  reservations    │    │ contact_messages  │
     │  ├──────────────────┤    ├───────────────────┤
     └─>│ id (PK)          │    │ id (PK)           │
        │ user_id (FK)     │    │ name              │
        │ reservation_date │    │ email             │
        │ reservation_time │    │ subject           │
        │ guests           │    │ message           │
        │ special_requests │    │ is_read           │
        │ status           │    │ created_at        │
        │ created_at       │    └───────────────────┘
        └──────────────────┘
```

### Table Relationships
- **categories → menu_items**: One-to-Many (one category has many items)
- **users → orders**: One-to-Many (one user places many orders)
- **orders → order_items**: One-to-Many (one order has many items)
- **menu_items → order_items**: One-to-Many (one menu item can appear in many order items)
- **users → reservations**: One-to-Many (one user makes many reservations)

**Total: 7 tables with 5+ relationships (exceeds minimum requirement of 5 tables)**

---

## Project Structure

```
restaurant-management-system/
├── index.php                  # Landing page (big main page)
├── config.php                 # Database config & helper functions
├── login.php                  # User login page
├── signup.php                 # User registration page
├── logout.php                 # Logout handler
├── menu.php                   # Menu browsing with filtering
├── cart.php                   # Shopping cart page
├── checkout.php               # Order checkout handler
├── reservations.php           # Make reservation page
├── my_orders.php              # User order history
├── my_reservations.php        # User reservation history
├── profile.php                # User profile management
├── contact.php                # Contact us page
├── includes/
│   ├── header.php             # Shared site header/nav
│   ├── footer.php             # Shared site footer
│   ├── admin_header.php       # Admin panel header/sidebar
│   └── admin_footer.php       # Admin panel footer
├── admin/
│   ├── index.php              # Admin dashboard
│   ├── manage_menu.php        # CRUD menu items
│   ├── manage_categories.php  # CRUD categories
│   ├── manage_orders.php      # Manage orders
│   ├── manage_reservations.php# Manage reservations
│   ├── manage_users.php       # Manage users
│   └── manage_contacts.php    # View contact messages
├── api/
│   ├── search.php             # AJAX live search endpoint
│   ├── cart.php               # AJAX cart operations
│   ├── check_email.php        # AJAX email availability
│   └── update_order_status.php# AJAX order status update
├── css/
│   ├── style.css              # Main styles
│   ├── admin.css              # Admin panel styles
│   └── responsive.css         # Media queries (mobile-friendly)
├── js/
│   ├── main.js                # Validation, DOM manipulation
│   ├── search.js              # AJAX live search
│   ├── cart.js                # AJAX cart functionality
│   └── admin.js               # Admin panel interactions
├── database/
│   └── restaurant_db.sql      # Database schema + sample data
└── README.md                  # Project documentation
```

---

## Setup Instructions

### Prerequisites
- **XAMPP** / **WAMP** / **LAMP** (PHP 7.4+, MySQL 5.7+, Apache)
- Web browser (Chrome, Firefox, etc.)

### Installation Steps

1. **Clone/Extract** the project into your web server directory:
   ```
   # For XAMPP
   Copy to: C:\xampp\htdocs\restaurant-management-system

   # For LAMP/Linux
   Copy to: /var/www/html/restaurant-management-system
   ```

2. **Import the database:**
   - Open **phpMyAdmin** (http://localhost/phpmyadmin)
   - Click "Import" tab
   - Select `database/restaurant_db.sql`
   - Click "Go" to import

   OR via command line:
   ```bash
   mysql -u root -p < database/restaurant_db.sql
   ```

3. **Update database configuration** (if needed):
   - Open `config.php`
   - Update `DB_HOST`, `DB_USER`, `DB_PASS` if different from defaults
   - Update `SITE_URL` to match your local setup

4. **Access the application:**
   ```
   http://localhost/restaurant-management-system/
   ```

### Demo Credentials

| Role  | Email              | Password  |
|-------|-------------------|-----------|
| Admin | admin@habesha.com  | password  |
| User  | abebe@example.com  | password  |
| User  | tigist@example.com | password  |

---

## Features Implemented

| # | Feature | Technology |
|---|---------|-----------|
| 1 | Responsive landing page | HTML, CSS (Flexbox/Grid) |
| 2 | Mobile-friendly navigation | CSS Media Queries, JS |
| 3 | User registration with validation | PHP, JS, MySQL |
| 4 | User login with sessions | PHP Sessions |
| 5 | Password hashing | PHP `password_hash()` |
| 6 | Live search (AJAX) | JavaScript Fetch API |
| 7 | Real-time email validation (AJAX) | JavaScript Fetch API |
| 8 | Shopping cart (AJAX) | JavaScript Fetch API |
| 9 | Menu browsing with categories | PHP, MySQL |
| 10 | Order placement & checkout | PHP, MySQL Transactions |
| 11 | Table reservations | PHP, MySQL |
| 12 | User profile management | PHP, MySQL |
| 13 | Contact form | PHP, MySQL |
| 14 | Admin dashboard with stats | PHP, MySQL |
| 15 | CRUD: Menu items | PHP, MySQL |
| 16 | CRUD: Categories | PHP, MySQL |
| 17 | CRUD: Orders (status update) | PHP, MySQL, AJAX |
| 18 | CRUD: Reservations | PHP, MySQL |
| 19 | CRUD: Users (role management) | PHP, MySQL |
| 20 | Contact message management | PHP, MySQL |
| 21 | Input sanitization | PHP `htmlspecialchars()` |
| 22 | SQL injection prevention | MySQLi Prepared Statements |
| 23 | Password strength indicator | JavaScript |
| 24 | Counter animation | JavaScript IntersectionObserver |
| 25 | Flash message system | PHP Sessions |

---

## Technologies Used

- **Frontend:** HTML5, CSS3 (Flexbox, Grid, Media Queries), Vanilla JavaScript (ES5+)
- **Backend:** PHP 7.4+ (Sessions, MySQLi)
- **Database:** MySQL 5.7+ (InnoDB, Foreign Keys, Transactions)
- **Icons:** Font Awesome 6.5
- **Fonts:** Google Fonts (Playfair Display, Open Sans)
- **No frameworks used** (vanilla PHP, JS, CSS only)

---

## Course Requirements Checklist

- [x] Clean, responsive user interface (HTML/CSS - mobile friendly)
- [x] Client-side interactivity (JavaScript - form validation, DOM manipulation, fetch/AJAX)
- [x] Server-side logic (PHP - sessions, request handling, business logic)
- [x] Data persistence (MySQL - 7 interconnected tables, CRUD operations)
- [x] Security awareness (SQL injection prevention, input sanitization, password hashing)
- [x] At least 5 database tables with relationships (7 tables, 5+ relationships)
- [x] At least one async JavaScript feature (live search, cart, email check, order status)
- [x] No heavy frameworks (vanilla PHP, JS, CSS)
- [x] Uses `mysqli` prepared statements (not deprecated `mysql_*`)

---

*Developed for the Web Programming Technologies Course - Ethiopian Context (Habesha Kitchen)*
