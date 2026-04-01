<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\User\CodeController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\DepositControlller;
use App\Http\Controllers\User\TransactionControlller;
use App\Http\Controllers\PWAController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

Route::get('/clear', function () {
    $output = new \Symfony\Component\Console\Output\BufferedOutput();
    Artisan::call('optimize:clear', array(), $output);
    return $output->fetch();
})->name('/clear');

Route::get('queue-work', function () {
    return Illuminate\Support\Facades\Artisan::call('schedule:run');
})->name('queue.work');

Route::get('schedule-run', function () {
    return Illuminate\Support\Facades\Artisan::call('schedule:run');
})->name('cron');

Route::get('migrate', function () {
    return Illuminate\Support\Facades\Artisan::call('migrate');
});


Route::get('/manifest.json', [PWAController::class, 'manifestJson'])->name('manifest');
Route::get('/offline.html', [PWAController::class, 'offline']);

Route::post('uid-checker/check', function (Request $request) {
    $response = Http::get('http://103.187.23.82:81/xp-opu', [
        'uid' => $request->id,
    ]);
    return response()->json($response->json());
})->name('uidcheck');

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/topup/{slug}', [HomeController::class, 'topup'])->name('topup');
Route::get('/page/{slug}', [HomeController::class, 'page'])->name('page');
Route::get('/get-popup', [HomeController::class, 'getPopups'])->name('popup');

Route::group(['middleware' => ['auth'], 'as' => 'user.'], function () {
    // Deposit
    Route::get('/add-funds', [DepositControlller::class, 'index'])->name('addfunds');
    Route::post('/deposit/addfund', [DepositControlller::class, 'addFund'])->name('deposit.addfund');
    Route::get('/deposit/pay', [DepositControlller::class, 'payNow'])->name('deposit.pay');

    // Order
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/codes', [CodeController::class, 'index'])->name('codes');
    Route::post('/topup/buynow', [OrderController::class, 'addOrder'])->name('topup.buynow');
    Route::get('/order/pay', [OrderController::class, 'payNow'])->name('order.pay');


    // User
    Route::get('/account', [UserController::class, 'account'])->name('account');
    Route::post('/account/update', [UserController::class, 'update'])->name('account.update');

    // Transaction
    Route::get('/transactions', [TransactionControlller::class, 'index'])->name('transactions');

    // Payment Gateway
    Route::match(['get', 'post'], '/deposit/{trx}/{gateway}', [PaymentController::class, 'depositIpn'])->name('deposit.ipn');
    Route::match(['get', 'post'], '/order/{trx}/{gateway}', [PaymentController::class, 'orderIpn'])->name('order.ipn');
    Route::match(['get', 'post'], '/deposit/cancel', [PaymentController::class, 'depositCancel'])->name('deposit.cancel');
    Route::match(['get', 'post'], '/order/cancel', [PaymentController::class, 'orderCancel'])->name('order.cancel');
    Route::match(['get', 'post'], '/code/cancel', [PaymentController::class, 'codeCancel'])->name('code.cancel');
});

require __DIR__ . '/auth.php';
