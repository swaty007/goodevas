<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

//Route::get('/', [LandingController::class, 'index'])->name('welcome');
Route::get('/', function () {
    return redirect()->route('craftable-pro.products.index');
});

// Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::craftablePro('admin');

Route::middleware('craftable-pro-middlewares')->prefix('admin')->name('craftable-pro.')->group(function () {
    Route::get('products', [App\Http\Controllers\CraftablePro\ProductController::class, 'index'])->name('products.index');
    Route::get('income-products', [App\Http\Controllers\CraftablePro\ProductController::class, 'indexIncome'])->name('products.index-income');
    Route::get('api-products', [App\Http\Controllers\CraftablePro\ProductController::class, 'indexApiProducts'])->name('products.index-api');
    Route::get('products/create', [App\Http\Controllers\CraftablePro\ProductController::class, 'create'])->name('products.create');
    Route::post('products', [App\Http\Controllers\CraftablePro\ProductController::class, 'store'])->name('products.store');
    Route::get('products/export', [App\Http\Controllers\CraftablePro\ProductController::class, 'export'])->name('products.export');
    Route::get('products/edit/{product}', [App\Http\Controllers\CraftablePro\ProductController::class, 'edit'])->name('products.edit');
    Route::match(['put', 'patch'], 'products/{product}', [App\Http\Controllers\CraftablePro\ProductController::class, 'update'])->name('products.update');
    Route::match(['put', 'patch'], 'products-income/{product}/{warehouse}', [App\Http\Controllers\CraftablePro\ProductController::class, 'updateIncome'])->name('products.update-income');
    Route::delete('products/{product}', [App\Http\Controllers\CraftablePro\ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('products/bulk-destroy', [App\Http\Controllers\CraftablePro\ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');

    Route::get('product-types', [App\Http\Controllers\CraftablePro\ProductTypeController::class, 'index'])->name('product-types.index');
    Route::get('product-types/create', [App\Http\Controllers\CraftablePro\ProductTypeController::class, 'create'])->name('product-types.create');
    Route::post('product-types', [App\Http\Controllers\CraftablePro\ProductTypeController::class, 'store'])->name('product-types.store');
    Route::get('product-types/export', [App\Http\Controllers\CraftablePro\ProductTypeController::class, 'export'])->name('product-types.export');
    Route::get('product-types/edit/{productType}', [App\Http\Controllers\CraftablePro\ProductTypeController::class, 'edit'])->name('product-types.edit');
    Route::match(['put', 'patch'], 'product-types/{productType}', [App\Http\Controllers\CraftablePro\ProductTypeController::class, 'update'])->name('product-types.update');
    Route::delete('product-types/{productType}', [App\Http\Controllers\CraftablePro\ProductTypeController::class, 'destroy'])->name('product-types.destroy');
    Route::post('product-types/bulk-destroy', [App\Http\Controllers\CraftablePro\ProductTypeController::class, 'bulkDestroy'])->name('product-types.bulk-destroy');

    Route::get('ysells', [App\Http\Controllers\CraftablePro\YsellController::class, 'index'])->name('ysells.index');
    Route::get('ysells/create', [App\Http\Controllers\CraftablePro\YsellController::class, 'create'])->name('ysells.create');
    Route::post('ysells', [App\Http\Controllers\CraftablePro\YsellController::class, 'store'])->name('ysells.store');
    Route::get('ysells/export', [App\Http\Controllers\CraftablePro\YsellController::class, 'export'])->name('ysells.export');
    Route::get('ysells/edit/{ysell}', [App\Http\Controllers\CraftablePro\YsellController::class, 'edit'])->name('ysells.edit');
    Route::match(['put', 'patch'], 'ysells/{ysell}', [App\Http\Controllers\CraftablePro\YsellController::class, 'update'])->name('ysells.update');
    Route::delete('ysells/{ysell}', [App\Http\Controllers\CraftablePro\YsellController::class, 'destroy'])->name('ysells.destroy');
    Route::post('ysells/bulk-destroy', [App\Http\Controllers\CraftablePro\YsellController::class, 'bulkDestroy'])->name('ysells.bulk-destroy');

    Route::get('activities', [App\Http\Controllers\ActivityController::class, 'index'])->name('activity.index');

    Route::get('warehouses', [App\Http\Controllers\CraftablePro\WarehouseController::class, 'index'])->name('warehouses.index');
    Route::get('warehouses/create', [App\Http\Controllers\CraftablePro\WarehouseController::class, 'create'])->name('warehouses.create');
    Route::post('warehouses', [App\Http\Controllers\CraftablePro\WarehouseController::class, 'store'])->name('warehouses.store');
    Route::get('warehouses/export', [App\Http\Controllers\CraftablePro\WarehouseController::class, 'export'])->name('warehouses.export');
    Route::get('warehouses/edit/{warehouse}', [App\Http\Controllers\CraftablePro\WarehouseController::class, 'edit'])->name('warehouses.edit');
    Route::match(['put', 'patch'], 'warehouses/{warehouse}', [App\Http\Controllers\CraftablePro\WarehouseController::class, 'update'])->name('warehouses.update');
    Route::delete('warehouses/{warehouse}', [App\Http\Controllers\CraftablePro\WarehouseController::class, 'destroy'])->name('warehouses.destroy');
    Route::post('warehouses/bulk-destroy', [App\Http\Controllers\CraftablePro\WarehouseController::class, 'bulkDestroy'])->name('warehouses.bulk-destroy');
});
