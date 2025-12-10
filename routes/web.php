<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\TokenVerificationMiddleware;


//frontend
Route::get('/', [HomeController::class, 'homePage']);
Route::get('/dashboard', [DashboardController::class, 'dashboardPage'])->name('dashboardPage')->middleware(TokenVerificationMiddleware::class);
Route::get('/categoryPage', [CategoryController::class, 'categoryPage'])->name('categoryPage');
Route::get('/customerPage', [CustomerController::class, 'customerPage'])->name('customerPage');
Route::get('/productPage', [ProductController::class, 'productPage'])->name('productPage');

Route::get('/userRegistration', [UserController::class, 'userRegistrationPage']);
Route::get('/userLogin', [UserController::class, 'userloginPage']);
Route::get('/resetPassword', [UserController::class, 'restPasswordPage']);
Route::get('/sendOtp', [UserController::class, 'sendOtpPage']);
Route::get('/verifyOtp', [UserController::class, 'verifyOtpPage']);
Route::get('/userProfile', [UserController::class, 'profilePage']);


//Backend
//user Route
Route::post('/user-registration', [UserController::class, 'userRegistration']);
Route::post('/user-login', [UserController::class, 'userLogin']);
Route::get('/logout', [UserController::class, 'logout']);
Route::post('/send-otp', [UserController::class, 'sendOTP']);
Route::post('/verify-otp', [UserController::class, 'verifyOTP']);
Route::post('/reset-password', [UserController::class, 'resetPassword'])->middleware(TokenVerificationMiddleware::class);
Route::get('/user-profile', [UserController::class, 'userProfile'])->middleware(TokenVerificationMiddleware::class);
Route::post('/user-profile-update', [UserController::class, 'updateUserProfile'])->middleware(TokenVerificationMiddleware::class);


//Category Api
Route::get('/list-category', [CategoryController::class, 'categoryList'])->middleware(TokenVerificationMiddleware::class);
Route::post('/create-category', [CategoryController::class, 'CreateCategory'])->middleware(TokenVerificationMiddleware::class);
Route::post('/delete-category', [CategoryController::class, 'CategoryDelete'])->middleware(TokenVerificationMiddleware::class);
Route::post('/category-by-id', [CategoryController::class, 'CategoryByID'])->middleware(TokenVerificationMiddleware::class);
Route::post('/update-category', [CategoryController::class, 'CategoryUpdate'])->middleware(TokenVerificationMiddleware::class);


//Customer Page


//Customer Api
Route::post('/customer-create', [CustomerController::class, 'CustomerCreate'])->middleware(TokenVerificationMiddleware::class);
Route::get('/customer-list', [CustomerController::class, 'CustomerList'])->middleware(TokenVerificationMiddleware::class);
Route::post('/customer-delete', [CustomerController::class, 'CustomerDelete'])->middleware(TokenVerificationMiddleware::class);
Route::post('/customer-by-id', [CustomerController::class, 'CustomerByID'])->middleware(TokenVerificationMiddleware::class);
Route::post('/customer-update', [CustomerController::class, 'CustomerUpdate'])->middleware(TokenVerificationMiddleware::class);

//Product API
Route::post('/product-create', [ProductController::class, 'CreateProduct'])->middleware(TokenVerificationMiddleware::class);
Route::get('/product-list', [ProductController::class, 'ProductList'])->middleware(TokenVerificationMiddleware::class);
Route::post('/product-by-id', [ProductController::class, 'ProductByID'])->middleware(TokenVerificationMiddleware::class);
Route::post('/product-delete', [ProductController::class, 'ProductDelete'])->middleware(TokenVerificationMiddleware::class);
Route::post('/product-update', [ProductController::class, 'ProductUpdate'])->middleware(TokenVerificationMiddleware::class);

//Invoice API
Route::post('/invoice-create', [InvoiceController::class, 'InvoiceCreate'])->middleware(TokenVerificationMiddleware::class);
Route::get('/invoice-select', [InvoiceController::class, 'invoiceSelect'])->middleware(TokenVerificationMiddleware::class);
Route::post('/invoice-details', [InvoiceController::class, 'invoiceDetails'])->middleware(TokenVerificationMiddleware::class);
Route::post('/invoice-delete', [InvoiceController::class, 'invoiceDelete'])->middleware(TokenVerificationMiddleware::class);


//Dashboard summery
Route::get('/summary', [DashboardController::class, 'summary'])->middleware(TokenVerificationMiddleware::class);
Route::get('/sales-report', [DashboardController::class, 'salesReport'])->middleware(TokenVerificationMiddleware::class);

