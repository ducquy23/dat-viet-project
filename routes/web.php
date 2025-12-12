<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\PartnerLoginController;

// Đăng nhập/Đăng xuất đối tác (qua modal)
Route::post('/dang-nhap', [PartnerLoginController::class, 'login'])->name('partner.login.submit');
Route::post('/dang-xuat', [PartnerLoginController::class, 'logout'])->name('partner.logout');

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tin-dang', [HomeController::class, 'index'])->name('listings.index'); // Alias cho filter form

// Tìm kiếm
Route::get('/tim-kiem', [SearchController::class, 'index'])->name('search');

// Danh mục
Route::get('/danh-muc', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/danh-muc/{slug}', [CategoryController::class, 'show'])->name('categories.show');

// Tin đăng
Route::get('/tin-dang/{slug}', [ListingController::class, 'show'])->name('listings.show');
Route::get('/danh-muc/{categorySlug}/tin-dang', [ListingController::class, 'category'])->name('listings.category');
Route::post('/tin-dang', [ListingController::class, 'store'])->name('listings.store')->middleware('auth:partner');
Route::get('/tin-cua-toi', [ListingController::class, 'myListings'])->name('listings.my-listings')->middleware('auth:partner');

// API routes cho AJAX
Route::prefix('api')->group(function () {
  // Yêu thích tin đăng (có thể cần đăng nhập)
  Route::post('/listings/{id}/favorite', [ListingController::class, 'toggleFavorite'])->name('api.listings.favorite');

  // Liên hệ
  Route::post('/listings/{id}/contact', [ListingController::class, 'contact'])->name('api.listings.contact');

  // Lấy quận/huyện theo thành phố
  Route::get('/districts', [ApiController::class, 'getDistricts'])->name('api.districts');

  // Track click quảng cáo
  Route::post('/ads/{id}/click', [ApiController::class, 'trackAdClick'])->name('api.ads.click');

  // Lấy tin đăng cho map
  Route::get('/listings/map', [ApiController::class, 'getListingsForMap'])->name('api.listings.map');
  
  // Lấy chi tiết tin đăng
  Route::get('/listings/{id}', [ApiController::class, 'getListing'])->name('api.listings.show');
});
