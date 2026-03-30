<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', Search::class)->name('search');
Route::view('/cars', 'cars')->name('cars');
// Route::get('/cars/{slug}', CarDetails::class)->name('car.details');

Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
// Route::post('/contact', [ContactFormController::class, 'submit'])->name('contact.submit');
Route::view('/testimonials', 'testimonials')->name('testimonials');
Route::view('/inspection-reports', 'inspection')->name('inspection');
Route::view('/shipping-clearing', 'shipping')->name('shipping');
Route::view('/vehicle-history', 'history')->name('history');
Route::view('/trade-in-program', 'tradein')->name('tradein');
Route::view('/car-importation', 'importation')->name('importation');
Route::view('/advisory', 'advisory')->name('advisory');
Route::view('/privacy-policy', 'privacy')->name('privacy');
Route::view('/terms-of-service', 'terms')->name('terms');
