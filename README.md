# 🐉 Bhutan E-Commerce Platform — Complete Laravel Application

> Full-featured e-commerce platform with Customer/Employee/Admin roles, Razorpay payments, order management, variant products, and stock logging.

---

## 🚀 Quick Setup

```bash
# 1. Create Laravel project
composer create-project laravel/laravel bhutan-shop
cd bhutan-shop

# 2. Copy all files from this package into the project

# 3. Configure .env
cp .env.example .env
php artisan key:generate
```

**`.env` settings:**
```env
DB_DATABASE=bhutan_shop
DB_USERNAME=root
DB_PASSWORD=secret

# Razorpay (get from razorpay.com)
RAZORPAY_KEY_ID=rzp_test_xxxxxxxxxxxx
RAZORPAY_KEY_SECRET=xxxxxxxxxxxxxxxx
```

```bash
# 4. Run migrations and seed
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

---

## 🔑 Default Login Credentials

| Role     | Email                        | Password   |
|----------|------------------------------|------------|
| Admin    | admin@bhutanshop.com         | admin123   |
| Employee | employee@bhutanshop.com      | emp123     |
| Customer | customer@bhutanshop.com      | cust123    |

---

## 📌 URLs

| Area         | URL                               |
|--------------|-----------------------------------|
| Storefront   | http://localhost:8000             |
| Login        | http://localhost:8000/login       |
| Admin Panel  | http://localhost:8000/admin       |
| My Orders    | http://localhost:8000/my/orders   |

---

## ✅ Features

### 🔐 Authentication & Roles
- **Admin** — Full access: products, orders, categories, attributes, employees
- **Employee** — Can manage products and update order statuses
- **Customer** — Browse, cart, checkout, track orders, profile

### 🛍️ Products
- **Simple Products** — Single price, limited or unlimited stock, up to 3 images
- **Variant Products** — Manual variant creation, per-variant price/stock/images
- Auto-generated SKU and variant names
- Low stock alerts (< 5 units)
- Featured product support
- Stock + price change logging in `stock_logs`

### 🛒 Shopping
- Session-based cart (works without login)
- Add to cart from product detail with variant selection
- Quantity control
- Free shipping on orders over BTN 5,000

### 📦 Orders
- Full order lifecycle: pending → confirmed → processing → packed → shipped → out_for_delivery → delivered
- Cancellation support
- Order timeline/status logs
- Customer cancel before processing

### 💳 Payments
- **COD (Cash on Delivery)** — Admin marks paid after delivery
- **Razorpay** — Online payment with signature verification
- Payment status: pending / paid / failed / refunded

### 👷 Admin Order Management
- Update status at any stage
- Add notes to status changes
- Assign orders to employees
- Mark COD orders as paid
- Auto-mark paid on delivery for COD

### 📊 Dashboard
- Total orders, revenue, products, customers
- Pending orders counter in sidebar
- Low stock alerts
- Order status breakdown chart
- Recent orders feed

---

## 📁 Key Files

```
app/
├── Http/Controllers/
│   ├── Admin/
│   │   ├── DashboardController.php    # Stats & recent data
│   │   ├── ProductController.php      # Full CRUD + variants
│   │   ├── OrderController.php        # Status + pay + assign
│   │   ├── AttributeController.php    # Attributes & options
│   │   ├── CategoryController.php     # Category CRUD
│   │   └── EmployeeController.php     # Staff accounts
│   ├── Auth/
│   │   └── LoginController.php        # Login + register + logout
│   └── Frontend/
│       ├── ProductController.php      # List + detail
│       ├── CartController.php         # Session cart
│       ├── CheckoutController.php     # Checkout + Razorpay
│       ├── OrderController.php        # My orders
│       └── ProfileController.php      # Edit profile
├── Models/
│   ├── Product.php     # price_display, total_stock, is_low_stock
│   ├── Variant.php
│   ├── Order.php       # Status colors, canBeCancelled
│   ├── OrderItem.php
│   ├── OrderStatusLog.php
│   ├── User.php        # Roles: admin/employee/customer
│   ├── StockLog.php
│   ├── Attribute.php / AttributeOption.php
│   └── Category.php
├── Services/
│   ├── ProductService.php   # Slug, SKU, images, stock logging
│   ├── OrderService.php     # Create order, update status, mark paid
│   └── CartService.php      # Session-based cart
└── Http/Middleware/
    └── RoleMiddleware.php   # Role-based access control

resources/views/
├── admin/
│   ├── layouts/app.blade.php          # Sidebar + topbar
│   ├── dashboard/index.blade.php      # Stats dashboard
│   ├── products/{index,create,edit,stock-logs}.blade.php
│   ├── orders/{index,show}.blade.php
│   ├── attributes/index.blade.php
│   ├── categories/{index,create,edit}.blade.php
│   └── employees/{index,create,edit}.blade.php
└── frontend/
    ├── layouts/app.blade.php          # Header + footer + cart count
    ├── products/{index,show}.blade.php
    ├── cart/index.blade.php
    ├── checkout/{index,payment}.blade.php
    ├── orders/{index,show}.blade.php
    └── auth/{login,register,profile}.blade.php
```

---

## ⚙️ Config

### Middleware Registration (bootstrap/app.php or Kernel.php)
```php
// Laravel 11 (bootstrap/app.php):
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias(['auth.role' => \App\Http\Middleware\RoleMiddleware::class]);
})

// Laravel 10 (app/Http/Kernel.php):
protected $routeMiddleware = [
    'auth.role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

### Services Config (config/services.php)
```php
'razorpay' => [
    'key_id'     => env('RAZORPAY_KEY_ID'),
    'key_secret' => env('RAZORPAY_KEY_SECRET'),
],
```

### Pagination (AppServiceProvider.php)
```php
use Illuminate\Pagination\Paginator;
Paginator::useBootstrapFive(); // or useTailwind()
```
