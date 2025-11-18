<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SupportCostumerController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\GetVoucherController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EarnController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\SupportmeController;
use App\Http\Controllers\Admin\MakarantaController;
use App\Http\Controllers\Admin\AdminVoucherController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::view('/test', 'test');
Route::get('/healthz', function(){ return response('ok', 200); });
Route::post('/webhook/paymentpoint', [PaymentController::class, 'webhook'])->name('payment.webhook');
Route::post('/payment/initialize', [PaymentController::class, 'initialize'])->name('payment.initialize'); // if you call PaymentPoint to initialize

Route::match(['get', 'post'], '/simulate-webhook', function () {
    $fakeData = [
        "notification_status" => "payment_successful",
        "transaction_id" => "TEST123456",
        "amount_paid" => 1000,
        "settlement_amount" => 995,
        "settlement_fee" => 5,
        "transaction_status" => "success",
        "sender" => [
            "name" => "John Doe",
            "account_number" => "****1234",
            "bank" => "PalmPay"
        ],
        "receiver" => [
            "name" => "FAHAX WALLET",
            "account_number" => "6679854996",
            "bank" => "PalmPay"
        ],
        "customer" => [
            "name" => "Ahmad Saadu",
            "email" => "test@example.com",
            "phone" => "07012345678",
            "customer_id" => "U123456"
        ],
        "description" => "Simulated test payment.",
        "timestamp" => now()->toISOString()
    ];

    // Send it to your webhook route
    // $response = Http::post(url('/webhook/paymentpoint'), $fakeData);
    $response = Http::post('https://http://127.0.0.1:8000/webhook/paymentpoint', $fakeData);

    return $response->json();
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class,'dashboard'])->name('dashboard');

     // PIN creation / confirmation (after registration)
    Route::get('/create-pin', [PinController::class, 'create'])->name('pin.create');
    Route::post('/create-pin', [PinController::class, 'store'])->name('pin.store');

     // App-lock authorization (when session expired/locked)
    Route::get('/authorize', [PinController::class, 'showLockScreen'])->name('pin.authorize');
    Route::post('/authorize', [PinController::class, 'showLockScreenCheck'])->name('pin.authorize.check');

          // WebAuthn / Biometric placeholders (optional)
    Route::post('/webauthn/register', [PinController::class, 'webauthnRegister'])->name('webauthn.register');
    Route::post('/webauthn/authenticate', [PinController::class, 'webauthnAuthenticate'])->name('webauthn.authenticate');

    Route::view('/profile/index', 'profile.index')->name('profile');
    //getVocher route
    Route::get('/wallet/accno', [DashboardController::class, 'acc'])->name('user.accno');
    Route::get('/getVoucher/buy', [GetVoucherController::class, 'create'])->name('getVoucher.buy');
    Route::get('/getVoucher/receipt/{id}', [GetVoucherController::class, 'receipt'])->name('getVoucher.receipt');


    Route::post('/voucher/select', [GetVoucherController::class, 'redirectToPinConfirmation'])->name('voucher.select');
    Route::post('/voucher/final-store', [GetVoucherController::class, 'finalStore'])->name('voucher.store');

    // Transaction PIN verification (AJAX)
    Route::get('/transaction/enter-pin', [PinController::class, 'showPinPage'])->name('pin.show');
    Route::post('/transaction/verify-pin', [PinController::class, 'verifyTransactionPin'])->name('transaction.pin.verify');

    //Reseller Routes
    Route::resource('voucher-profiles', ResellerController::class)
        ->names('reseller.upgrade');

    //Earn section
    Route::get('/earn/index',[EarnController::class, 'index'])->name('earn.index');
    Route::get('/earn/morningAzkar',[EarnController::class, 'morningAzkar'])->name('earn.morningAzkar');
    Route::get('/earn/eveningAzkar',[EarnController::class, 'eveningAzkar'])->name('earn.eveningAzkar');
    // Allow optional page parameter for Friday view (defaults handled in controller)
    Route::get('/earn/friday/{shafi?}', [EarnController::class, 'friday'])->name('earn.friday');
    Route::get('/earn/makaranta/index',[EarnController::class, 'makaranta'])->name('earn.makaranta.index');
    Route::get('/earn/makaranta/darasi/{course?}',[EarnController::class, 'darasi'])->name('makaranta.darasi');


    // Put the more specific karanta route before the generic course/{file} route
     Route::get('/earn/makaranta/karanta/{pageId}', [EarnController::class, 'karanta'])->name('makaranta.karanta');
    Route::get('/earn/makaranta/{course}/{file}', [EarnController::class, 'sauraro'])->name('makaranta.sauraro');
    Route::post('/earn/makaranta/quiz/{pageId}', [EarnController::class, 'submitQuiz'])->name('quiz.submit');

    // Notifications
    Route::post('notifications/{id}/read', [NotificationController::class,'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class,'markAllRead'])->name('notifications.readAll');
    Route::get('notifications/index', [NotificationController::class,'index'])->name('notifications.index');
    Route::get('notifications/show/{id}', [NotificationController::class,'show'])->name('notifications.show');

    // Route::view('/transactions/index', 'index')->name('transactions.index');
    Route::get('/transactions/index', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/help/index', [SupportController::class, 'index'])->name('help.index');


    //PaymentPoint Routes
    // Route::post('/api/payment/webhook', [PaymentWebhookController::class, 'handleWebhook']);
    // Route::post('/webhook/paymentpoint', [PaymentController::class, 'webhook'])->name('payment.webhook');
    // Route::post('/pay/initiate', [PaymentController::class, 'initialize'])->name('payment.initialize');

    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
});


Route::middleware('guest')->group(function (){
    // Registration
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');


    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Admin User Management - View All Users
    Route::get('/admin/user/viewUser', [AdminUserController::class, 'view'])->name('viewUser');
    Route::get('/admin/users/{id}/edit', [AdminUserController::class, 'edit'])->name('User.edit');
    Route::PATCH('/admin/users/{id}/edit', [AdminUserController::class, 'update'])->name('User.update');
    Route::DELETE('/admin/users/viewUser/{id}', [AdminUserController::class, 'destroy'])->name('viewUser.delete');

    //ADMIN UPGRADE USER MANAGEMENT
    Route::get('/admin/user/manual-upgrade', [AdminUserController::class, 'adminUpgradeView'])->name('admin.reseller-view');
    Route::post('/admin/user/manual-upgrade', [AdminUserController::class, 'manualUpgrade'])->name('admin.reseller.upgrade');



    // Admin User Wallet Management
    Route::get('/admin/user/walletManage', [AdminUserController::class, 'walletView'])->name('wallets.manage');
    Route::put('/admin/user/walletManage/{wallet}/updateFund', [AdminUserController::class, 'updateWallet'])->name('wallets.updateFund');

    // Admin User Password Management
    Route::get('/admin/user/changepassword', [AdminUserController::class, 'displaychangepassword'])->name('display.change.password');
    Route::post('/admin/user/changepassword', [AdminUserController::class, 'updatechangePassword'])->name('update.change.Password');

    // Admin Settings Notify Users
    Route::get('Settings/notify', [AdminSettingsController::class, 'notify'])->name('Snotify');
    Route::post('Settings/notifystore', [AdminSettingsController::class, 'notifystore'])->name('Snotifystore');

    // Admin Support Management
    Route::get('/admin/appContacts', [AdminSettingsController::class, 'contactView'])->name('admin.settings.appContacts');
    Route::post('/admin/appContacts/store', [AdminSettingsController::class, 'storeTitleQuestion'])->name('admin.settings.store');
    Route::post('/admin/appContacts/sub/store', [AdminSettingsController::class, 'storeSubQuestion'])->name('settings.sub.store');

    // Admin transactions
    Route::get('/admin/transactions/all', [AdminTransactionController::class, 'all'])->name('T.all');
    Route::get('/admin/transactions/processings', [AdminTransactionController::class, 'processings'])->name('T.processings');

    Route::post('/api/vouchers/purchase', [GetVoucherController::class,'purchase'])->middleware('auth:sanctum');
    Route::post('/api/user/upgrade-to-reseller', [ResellerController::class,'requestUpgrade'])->middleware('auth:sanctum');


    //voucher profiles management
    Route::resource('voucher-profiles', AdminVoucherController::class)
        ->names('admin.voucher_profiles');
        // ->only(['index', 'store', 'update', 'destroy']);
});


// Route::post('/webhook/paymentpoint', function (Request $request) {
//     Log::info('Webhook received:', $request->all());

//     return response()->json(['status' => 'Webhook received successfully']);
// });



// Route::post('/webhook/paymentpoint', function (Request $request) {
//     $secret = env('PAYMENTPOINT_SECRET'); // Your secret key from dashboard

//     $signature = $request->header('Paymentpoint-Signature');
//     $payload = $request->getContent();

//     // Compute signature
//     $calculated = hash_hmac('sha256', $payload, $secret);

//     if (!hash_equals($calculated, $signature)) {
//         Log::warning('Invalid PaymentPoint signature', ['payload' => $payload]);
//         return response('Invalid signature', 400);
//     }

//     // Decode JSON
//     $data = json_decode($payload, true);
//     Log::info('Webhook received', $data);

//     // Example: update transaction record in DB here
//     // Transaction::where('transaction_id', $data['transaction_id'])->update([...]);

//     return response('Webhook received successfully', 200);
// });
