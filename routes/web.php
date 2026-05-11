<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GarageDashboardController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\PlanPaymentController;
use App\Http\Controllers\SellerDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketplaceController::class, 'home'])->name('home');
Route::get('/browse-parts', [MarketplaceController::class, 'browse'])->name('parts.index');
Route::get('/parts/{product:slug}', [MarketplaceController::class, 'show'])->name('parts.show');
Route::post('/parts/{product:slug}/inquiries', [MarketplaceController::class, 'inquiry'])->name('parts.inquiry');
Route::get('/find-shops', [MarketplaceController::class, 'shops'])->name('shops.index');
Route::get('/find-garages', [MarketplaceController::class, 'garages'])->name('garages.index');
Route::get('/find-garages/{garage:slug}', [MarketplaceController::class, 'garageShow'])->name('garages.show');
Route::post('/garages/{garage}/reviews', [MarketplaceController::class, 'garageReview'])->name('garages.reviews.store');
Route::get('/pricing', [MarketplaceController::class, 'pricing'])->name('pricing');
Route::post('/mpesa/callback', [PlanPaymentController::class, 'callback'])->name('mpesa.callback');
Route::get('/sell-parts', [SellerDashboardController::class, 'create'])->name('sell');
Route::post('/sell-parts', [SellerDashboardController::class, 'store'])->name('seller.products.store');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/plans/{plan:slug}/select', [PlanPaymentController::class, 'select'])->name('plans.select');
    Route::get('/plans/{plan:slug}/pay', [PlanPaymentController::class, 'pay'])->name('plans.pay');
    Route::post('/plans/{plan:slug}/stk', [PlanPaymentController::class, 'stk'])->name('plans.stk');
    Route::get('/seller/dashboard', [SellerDashboardController::class, 'index'])->name('seller.dashboard');
    Route::get('/garage/dashboard', [GarageDashboardController::class, 'index'])->name('garage.dashboard');
    Route::put('/garage/profile', [GarageDashboardController::class, 'update'])->name('garage.profile.update');
    Route::post('/seller/shop', [SellerDashboardController::class, 'storeShop'])->name('seller.shop.store');
    Route::get('/seller/products/{product}/edit', [SellerDashboardController::class, 'edit'])->name('seller.products.edit');
    Route::put('/seller/products/{product}', [SellerDashboardController::class, 'update'])->name('seller.products.update');
    Route::delete('/seller/products/{product}', [SellerDashboardController::class, 'destroy'])->name('seller.products.destroy');
    Route::patch('/seller/products/{product}/sold', [SellerDashboardController::class, 'markSold'])->name('seller.products.sold');
    Route::patch('/admin/maintenance-mode', [AdminController::class, 'toggleMaintenanceMode'])->name('admin.maintenance-mode.update');
    Route::patch('/admin/site-settings', [AdminController::class, 'updateSiteSettings'])->name('admin.site-settings.update');
    Route::patch('/admin/category-icons', [AdminController::class, 'updateCategoryIcons'])->name('admin.category-icons.update');
    Route::patch('/admin/products/{product}/status', [AdminController::class, 'updateProductStatus'])->name('admin.products.status');
    Route::patch('/admin/garages/{garage}/public-verify', [AdminController::class, 'verifyGaragePublicListing'])->name('admin.garages.public-verify');
    Route::patch('/admin/garages/{garage}/public-revoke', [AdminController::class, 'revokeGaragePublicListing'])->name('admin.garages.public-revoke');
    Route::patch('/admin/users/{user}/verify', [AdminController::class, 'verifyUser'])->name('admin.users.verify');
    Route::patch('/admin/users/{user}/probation', [AdminController::class, 'toggleUserProbation'])->name('admin.users.probation');
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
});
