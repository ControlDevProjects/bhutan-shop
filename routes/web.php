<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\{DashboardController, ProductController as AdminPC, OrderController as AdminOC, AttributeController, CategoryController, EmployeeController, SettingController, StockController};
use App\Http\Controllers\Frontend\{ProductController, CartController, CheckoutController, OrderController, ProfileController, HomeController};
use Illuminate\Support\Facades\Route;

// ── AUTH ──────────────────────────────────────────────────────────────────
Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');
Route::get('/register', [LoginController::class,'showRegister'])->name('register');
Route::post('/register',[LoginController::class,'register'])->name('register.post');

// ── FRONTEND ──────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/products', [ProductController::class,'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class,'show'])->name('products.show');

// Cart (no auth needed)
Route::prefix('cart')->name('cart.')->group(function() {
    Route::get('/',  [CartController::class,'index'])->name('index');
    Route::post('/add', [CartController::class,'add'])->name('add');
    Route::put('/{key}', [CartController::class,'update'])->name('update');
    Route::delete('/{key}', [CartController::class,'remove'])->name('remove');
    Route::get('/count', [CartController::class,'count'])->name('count');
    Route::get('/quick-view/{productId}', [CartController::class,'quickView'])->name('quick-view');
});

// Checkout & Orders (auth required)
Route::middleware(['auth.role:customer,admin,employee'])->group(function() {
    Route::get('/checkout', [CheckoutController::class,'index'])->name('checkout.index');
    Route::post('/checkout',[CheckoutController::class,'store'])->name('checkout.store');
    Route::get('/checkout/{order}/payment',[CheckoutController::class,'payment'])->name('checkout.payment');
    Route::post('/checkout/{order}/verify',[CheckoutController::class,'verifyPayment'])->name('checkout.verify');

    Route::prefix('my')->name('orders.')->group(function() {
        Route::get('/orders', [OrderController::class,'index'])->name('index');
        Route::get('/orders/{order}', [OrderController::class,'show'])->name('show');
        Route::get('/orders/{order}/invoice', [OrderController::class,'invoice'])->name('invoice');
        Route::post('/orders/{order}/cancel',[OrderController::class,'cancel'])->name('cancel');
    });

    Route::get('/profile', [ProfileController::class,'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class,'update'])->name('profile.update');
});

// ── ADMIN ─────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth.role:admin,employee'])->group(function() {
    Route::get('/', [DashboardController::class,'index'])->name('dashboard');

    // Products
    Route::get('/products', [AdminPC::class,'index'])->name('products.index');
    Route::get('/products/create', [AdminPC::class,'create'])->name('products.create');
    Route::post('/products', [AdminPC::class,'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminPC::class,'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminPC::class,'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminPC::class,'destroy'])->name('products.destroy');
    Route::post('/products/{product}/variants', [AdminPC::class,'storeVariant'])->name('products.variants.store');
    Route::put('/products/{product}/variants/{variant}', [AdminPC::class,'updateVariant'])->name('products.variants.update');
    Route::delete('/products/{product}/variants/{variant}', [AdminPC::class,'destroyVariant'])->name('products.variants.destroy');
    Route::get('/products/{product}/stock-logs', [AdminPC::class,'stockLogs'])->name('products.stock-logs');
    Route::get('/products/{product}/variant-data', [AdminPC::class,'variantData'])->name('products.variant-data');

    // Stock Management
    Route::get('/stock', [StockController::class,'index'])->name('stock.index');
    Route::get('/stock/{product}', [StockController::class,'show'])->name('stock.show');
    Route::post('/stock/{product}/update', [StockController::class,'update'])->name('stock.update');

    // Orders
    Route::get('/orders', [AdminOC::class,'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOC::class,'show'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [AdminOC::class,'invoice'])->name('orders.invoice');
    Route::post('/orders/{order}/status', [AdminOC::class,'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/pay', [AdminOC::class,'markPaid'])->name('orders.pay');
    Route::post('/orders/{order}/assign', [AdminOC::class,'assignEmployee'])->name('orders.assign');

    // Attributes
    Route::get('/attributes', [AttributeController::class,'index'])->name('attributes.index');
    Route::post('/attributes', [AttributeController::class,'store'])->name('attributes.store');
    Route::put('/attributes/{attribute}', [AttributeController::class,'update'])->name('attributes.update');
    Route::delete('/attributes/{attribute}', [AttributeController::class,'destroy'])->name('attributes.destroy');
    Route::post('/attributes/{attribute}/options', [AttributeController::class,'storeOption'])->name('attributes.options.store');
    Route::delete('/attribute-options/{option}', [AttributeController::class,'destroyOption'])->name('attributes.options.destroy');
    Route::get('/attributes/{attribute}/options', [AttributeController::class,'getOptions'])->name('attributes.options.get');

    // Categories
    Route::resource('/categories', CategoryController::class);

    // Employees (admin only)
    Route::middleware(['auth.role:admin'])->group(function() {
        Route::get('/employees', [EmployeeController::class,'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class,'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class,'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [EmployeeController::class,'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class,'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class,'destroy'])->name('employees.destroy');

        Route::get('/settings', [SettingController::class,'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class,'update'])->name('settings.update');
    });
});

// Wishlist
use App\Http\Controllers\Frontend\WishlistController;
Route::get('/wishlist', [WishlistController::class,'index'])->name('wishlist.index')->middleware('auth.role:customer,admin,employee');
Route::post('/wishlist/toggle', [WishlistController::class,'toggle'])->name('wishlist.toggle')->middleware('auth.role:customer,admin,employee');
Route::delete('/wishlist/{productId}', [WishlistController::class,'remove'])->name('wishlist.remove')->middleware('auth.role:customer,admin,employee');
Route::get('/wishlist/ids', [WishlistController::class,'ids'])->name('wishlist.ids');

// Reviews
use App\Http\Controllers\Frontend\ReviewController;
Route::post('/products/{productId}/reviews', [ReviewController::class,'store'])->name('reviews.store')->middleware('auth.role:customer,admin,employee');
Route::delete('/products/{productId}/reviews', [ReviewController::class,'destroy'])->name('reviews.destroy')->middleware('auth.role:customer,admin,employee');
