Agri-Market Management System (AMMS)

A web-based agricultural marketplace platform connecting smallholder farmers and buyers through a digital platform. Built with PHP, MySQL, HTML5, CSS3, and vanilla JavaScript.

 Project Overview

AMMS is a comprehensive digital marketplace that:
Enables farmers to list agricultural products and manage orders
Allows buyers to search, browse, and purchase fresh products
Provides administrators with complete system management and analytics
Improves market access and transaction transparency
Supports mobile-friendly interface for accessibility

**Technology Stack**

Backend: PHP (Procedural)
Database: MySQL
Frontend: HTML5, CSS3, Vanilla JavaScript
Server: Apache (XAMPP compatible)
Authentication: PHP Sessions with password hashing
Security: Prepared statements, output sanitization, session verification

**System Requirements**

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server
- XAMPP (or similar LAMP stack)
- Modern web browser with JavaScript enabled

##  Installation & Setup

### Step 1: Extract Project Files

Extract the AMMS project to your XAMPP htdocs directory:
```
C:\xampp\htdocs\amms\


### Step 2: Create Database

1. **Start XAMPP** - Start Apache and MySQL services
2. Open phpMyAdmin:**
   - Navigate to `http://localhost/phpmyadmin`
   - Login with default credentials (user: root, no password)

3. **Create Database:**
   - Copy all SQL commands from `database/amms.sql`
   - Execute in phpMyAdmin SQL tab
   - OR use the MySQL command line:
   ```sql
   source database/amms.sql
   ```

### Step 3: Configure Database Connection

The database connection is configured in `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'amms');
```

If your MySQL has a password, update `DB_PASS` value accordingly.

### Step 4: Access the Application

1. Start Apache and MySQL in XAMPP
2. Open browser and navigate to:
   ```
   http://localhost/amms/
   ```

##  User Roles & Credentials

### Default Admin Account
- **Email:** admin@amms.local
- **Password:** admin123
- **Role:** Administrator

### Test Accounts (Create via Registration)
1. **Farmer Account:** Register with role "Farmer"
2. **Buyer Account:** Register with role "Buyer"

##  Project Structure

```
amms/
├── index.php                 # Home page
├── config/
│   └── config.php           # Database configuration
├── database/
│   └── schema.sql           # Database schema
├── includes/
│   ├── auth.php             # Authentication class
│   ├── Product.php          # Product management class
│   ├── Order.php            # Order management class
│   └── Admin.php            # Admin operations class
├── pages/
│   ├── login.php            # Login page
│   ├── register.php         # Registration page
│   ├── logout.php           # Logout handler
│   │
│   ├── farmer-dashboard.php # Farmer dashboard
│   ├── farmer-products.php  # Farmer product management
│   ├── farmer-product-edit.php
│   ├── farmer-product-delete.php
│   ├── farmer-orders.php    # Farmer incoming orders
│   ├── farmer-order-details.php
│   │
│   ├── buyer-dashboard.php  # Buyer dashboard
│   ├── buyer-browse.php     # Browse and search products
│   ├── buyer-product-view.php
│   ├── buyer-order-create.php
│   ├── buyer-orders.php     # Buyer order history
│   ├── buyer-order-details.php
│   │
│   ├── admin-dashboard.php  # Admin dashboard
│   ├── admin-users.php      # User management
│   ├── admin-products.php   # Product management
│   ├── admin-orders.php     # Order management
│   └── admin-reports.php    # System reports
│
└── assets/
    └── css/
        └── style.css        # Main stylesheet
```

## Security Features

### Implemented
- ✅ Password hashing using `password_hash()` and `password_verify()`
- ✅ SQL injection prevention with prepared statements
- ✅ XSS protection through output sanitization with `htmlspecialchars()`
- ✅ Session-based authentication
- ✅ Role-based access control (RBAC)
- ✅ Ownership verification for user resources
- ✅ Session timeout (30 minutes)
- ✅ Client-side and server-side validation

### Best Practices
- Never display raw database errors to users
- All user input is validated on both client and server
- Database queries use parameterized statements
- Session data is properly managed

##  Core Features

### 1. Authentication & User Management
- User registration with email verification concept
- Secure login with session management
- Three user roles: Admin, Farmer, Buyer
- User profile management
- Account status management (Active/Inactive)

### 2. Product Management (CRUD)
- **Farmers can:**
  - Add products with details (name, category, quantity, price)
  - Edit existing products
  - Delete products
  - View product list
  - Manage product status

- **Admin can:**
  - View all products
  - Monitor product listings
  - Access product analytics

### 3. Search & Filter
- Search products by name (wildcards)
- Filter by category
- Filter by price range (min/max)
- Filter by availability status
- AJAX-enabled search (vanilla JavaScript fetch)
- Real-time results

### 4. Orders & Transactions
- **Buyers can:**
  - Place orders for products
  - View complete order history
  - Track order status
  - View payment status

- **Farmers can:**
  - View incoming orders
  - Update order status (pending → confirmed → shipped → delivered)
  - Confirm payment receipt
  - View buyer contact information

- **System generates:**
  - Unique order numbers
  - Order summaries with line items
  - Transaction history
  - Revenue tracking

### 5. Admin Dashboard
- System statistics (users, products, orders, revenue)
- User management and status control
- Product monitoring
- Order management
- Sales reports
- User analytics by role

##  Database Schema

### Users Table
```sql
id, name, email, password, phone, location, role (admin/farmer/buyer), 
status (active/inactive), created_at, updated_at
```

### Products Table
```sql
id, farmer_id, name, description, category, quantity, unit, price, 
status (available/unavailable/out_of_stock), created_at, updated_at
```

### Orders Table
```sql
id, buyer_id, farmer_id, order_number, total_amount, 
payment_status (pending/paid/cancelled), 
order_status (pending/confirmed/shipped/delivered/cancelled), 
created_at, updated_at
```

### Order Items Table
```sql
id, order_id, product_id, quantity, unit_price, total_price, created_at
```

##  User Interface Features

### Responsive Design
- Mobile-friendly layout (breakpoints: 768px, 480px)
- Flexible grid system
- Touch-friendly buttons and forms
- Optimized navigation for small screens

### User-Friendly Interface
- Clean, modern design
- Intuitive navigation
- Clear call-to-action buttons
- Helpful alerts and messages
- Form validation feedback
- Consistent color scheme
- Professional typography

### Low-Literacy Ready
- Simple language in labels and messages
- Clear visual hierarchy
- Icons for quick understanding
- Minimal text density
- Color-coded badges for status

##  Key Pages

### Public Pages
- **Home Page** - Overview and feature highlights
- **Login** - User authentication
- **Register** - New user signup

### Farmer Pages
- Dashboard with statistics
- Product management
- Incoming orders
- Order details and management

### Buyer Pages
- Dashboard with order summary
- Product browsing with search/filter
- Shopping and order placement
- Order tracking

### Admin Pages
- Dashboard with system statistics
- User management and control
- Product oversight
- Order management
- Sales reports

##  Configuration

### Database Connection
File: `config/config.php`
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Add password if needed
define('DB_NAME', 'amms');
```

### Session Settings
```php
define('SESSION_TIMEOUT', 1800); // 30 minutes
```

### Application Settings
```php
define('APP_NAME', 'Agri-Market Management System');
define('APP_URL', 'http://localhost/amms');
define('SITE_TIMEZONE', 'UTC');
```

## Workflow Examples

### Farmer Workflow
1. Register as Farmer
2. Login to dashboard
3. Add products (name, category, quantity, unit, price)
4. View incoming orders from buyers
5. Update order status
6. Confirm payment receipt
7. Access sales reports

### Buyer Workflow
1. Register as Buyer
2. Login to dashboard
3. Browse available products
4. Search and filter products
5. Place order with desired quantity
6. Track order status
7. View order history

### Admin Workflow
1. Login with admin account
2. Access dashboard
3. Monitor system statistics
4. Manage user accounts
5. Review products
6. Monitor orders
7. Generate sales reports

##  Form Validation

### Client-Side (JavaScript)
- Email format validation
- Required field checks
- Number validation
- Quantity and price validation
- Password strength checks
- Confirmation matching

### Server-Side (PHP)
- Input sanitization
- Type checking
- Range validation
- Email format verification
- Duplicate prevention
- Ownership verification

##  Troubleshooting

### Database Connection Error
- Verify MySQL is running in XAMPP
- Check database credentials in `config/config.php`
- Ensure database 'amms' exists
- Check database user permissions

### Login Not Working
- Verify user was registered correctly
- Check email for typos
- Confirm user account is active
- Clear browser cookies/session

### Products Not Showing
- Check farmer has added products
- Verify product status is "available"
- Check product quantity > 0
- Verify farmer account is active

### Orders Not Creating
- Check product quantity is sufficient
- Verify buyer and farmer accounts are active
- Check if product is available
- Review server error logs

##  Support

For issues or questions:
1. Check the README thoroughly
2. Review database schema in `database/schema.sql`
3. Check browser console for JavaScript errors
4. Review XAMPP error logs
5. Verify file permissions

## License

This project is created for educational purposes.

##  Acknowledgments

Built as a comprehensive solution for connecting agricultural producers and consumers, improving market efficiency and transparency.

---

**Version:** 1.0  
**Last Updated:** January 2026  
**Status:** Ready for deployment
