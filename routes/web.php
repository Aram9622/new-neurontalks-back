<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\SitemapController;
use App\Http\Controllers\NewsletterUnsubscribeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::get('/newsletter/unsubscribe/{subscription}', NewsletterUnsubscribeController::class)
    ->middleware('signed')
    ->name('newsletter.unsubscribe');

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
    return 'Storage link has been created.';
});
