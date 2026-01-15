<?php

// ============================================================================
// IMPORTS
// ============================================================================

// Framework
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

// Authentication Controllers
use App\Http\Controllers\SessionController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

// User Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SupportCostumerController;

// Feature Controllers
use App\Http\Controllers\GetVoucherController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\EarnController;
use App\Http\Controllers\ResellerController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\AdminVoucherController;
use App\Http\Controllers\Admin\SupportmeController;
use App\Http\Controllers\Admin\MakarantaController;

// WebAuthn / Biometric
use Laragear\WebAuthn\WebAuthn;
use App\Http\Controllers\WebAuthn\WebAuthnRegisterController;
use App\Http\Controllers\WebAuthn\WebAuthnLoginController;


// ============================================================================
// PUBLIC ROUTES
// ============================================================================

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::view('/test', 'test');

Route::get('/healthz', function () {
    return response('ok', 200);
});


// ============================================================================
// PAYMENT WEBHOOKS (Public)
// ============================================================================

Route::post('/webhook/paymentpoint', [PaymentController::class, 'webhook'])
    ->name('payment.webhook');

Route::post('/payment/initialize', [PaymentController::class, 'initialize'])
    ->name('payment.initialize');


// ============================================================================
// AUTHENTICATION ROUTES (Guest Middleware)
// ============================================================================

Route::middleware('guest')->group(function () {
    // Registration
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('/login', [SessionController::class, 'create'])
        ->name('login');
    Route::post('/login', [SessionController::class, 'store']);

    // Password Reset
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});


// ============================================================================
// BIOMETRIC/WEBAUTHN ROUTES (Auth Required)
// ============================================================================

Route::middleware(['auth'])->group(function () {
    // WebAuthn Registration
    Route::post('/biometric/register/options', [WebAuthnRegisterController::class, 'options'])
        ->name('biometric.register.options');
    Route::post('/biometric/register', [WebAuthnRegisterController::class, 'register'])
        ->name('biometric.register');

    // WebAuthn Login
    Route::post('/biometric/login/options', [WebAuthnLoginController::class, 'options'])
        ->name('biometric.login.options');
    Route::post('/biometric/login', [WebAuthnLoginController::class, 'login'])
        ->name('biometric.login');

    // Biometric Toggle & Registration View
    Route::post('/profile/biometric/toggle', [PinController::class, 'toggleBiometric'])
        ->name('biometric.toggle');
    Route::get('/biometric/register', [PinController::class, 'showBiometricRegister'])
        ->name('biometric.register.view');
});


// ============================================================================
// USER ROUTES (Auth + Security Middleware)
// ============================================================================

Route::middleware(['auth', 'emergency', 'applock', 'trackactivity'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('dashboard');

    // ========================================================================
    // PIN Management
    // ========================================================================

    Route::get('/create-pin', [PinController::class, 'create'])
        ->name('pin.create');
    Route::post('/create-pin', [PinController::class, 'store'])
        ->name('pin.store');

    Route::get('/authorize', [PinController::class, 'showLockScreen'])
        ->name('pin.authorize');
    Route::post('/authorize', [PinController::class, 'showLockScreenCheck'])
        ->name('pin.authorize.check');

    Route::get('/transaction/enter-pin', [PinController::class, 'showPinPage'])
        ->name('pin.show');
    Route::post('/transaction/verify-pin', [PinController::class, 'verifyTransactionPin'])
        ->name('transaction.pin.verify');

    // ========================================================================
    // Profile
    // ========================================================================

    Route::view('/profile/index', 'profile.index')
        ->name('profile');

    // ========================================================================
    // Transactions & Notifications
    // ========================================================================

    Route::get('/transactions/index', [TransactionController::class, 'index'])
        ->name('transactions.index');

    Route::get('notifications/index', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::get('notifications/count', [NotificationController::class, 'count'])
        ->name('notifications.count');
    Route::get('notifications/show/{id}', [NotificationController::class, 'show'])
        ->name('notifications.show');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.readAll');

    // ========================================================================
    // Wallet & Payment
    // ========================================================================

    Route::post('/wallet/create-virtual-account', [WalletController::class, 'createVirtualAccount'])
        ->name('wallet.createVirtualAccount');
    Route::post('/paymentpoint/webhook', [PaymentController::class, 'handle']);
    Route::get('/wallet/accno', [WalletController::class, 'acc'])
        ->name('user.accno');

    // ========================================================================
    // Voucher / GetVoucher
    // ========================================================================

    Route::get('/getVoucher/buy', [GetVoucherController::class, 'create'])
        ->name('getVoucher.buy');
    Route::get('/getVoucher/receipt/{id}', [GetVoucherController::class, 'receipt'])
        ->name('getVoucher.receipt');

    Route::post('/voucher/select', [GetVoucherController::class, 'redirectToPinConfirmation'])
        ->name('voucher.select');
    Route::post('/voucher/final-store', [GetVoucherController::class, 'finalStore'])
        ->name('voucher.store');

    // ========================================================================
    // Reseller / Voucher Profiles
    // ========================================================================

    Route::resource('voucher-profiles', ResellerController::class)
        ->names('reseller.upgrade');

    // ========================================================================
    // Earn Section
    // ========================================================================

    Route::get('/earn/index', [EarnController::class, 'index'])
        ->name('earn.index');

    // Azkar Routes
    Route::get('/earn/morningAzkar', [EarnController::class, 'morningAzkar'])
        ->name('earn.morningAzkar');
    Route::get('/earn/eveningAzkar', [EarnController::class, 'eveningAzkar'])
        ->name('earn.eveningAzkar');
    Route::post('/earn/azkar/claim', [EarnController::class, 'claim'])
        ->name('azkar.claim');

    // Friday & Makaranta Routes
    Route::get('/earn/friday/{shafi?}', [EarnController::class, 'friday'])
        ->name('earn.friday');

    Route::get('/earn/makaranta/index', [EarnController::class, 'makaranta'])
        ->name('earn.makaranta.index');
    Route::get('/earn/makaranta/darasi/{course?}', [EarnController::class, 'darasi'])
        ->name('makaranta.darasi');
    Route::get('/earn/makaranta/karanta/{pageId}', [EarnController::class, 'karanta'])
        ->name('makaranta.karanta');
    Route::get('/earn/makaranta/{course}/{file}', [EarnController::class, 'sauraro'])
        ->name('makaranta.sauraro');
    Route::post('/earn/makaranta/quiz/{pageId}', [EarnController::class, 'submitQuiz'])
        ->name('quiz.submit');

    // ========================================================================
    // Support / Help
    // ========================================================================

    Route::get('/help/index', [SupportController::class, 'index'])
        ->name('help.index');

    // ========================================================================
    // Logout
    // ========================================================================

    Route::post('/logout', [SessionController::class, 'destroy'])
        ->name('logout');
});


// ============================================================================
// ADMIN ROUTES (Auth + Admin + Security Middleware)
// ============================================================================

Route::middleware(['auth', 'admin', 'applock', 'trackactivity'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->name('admin.dashboard');

    // ========================================================================
    // User Management
    // ========================================================================

    // View & Edit Users
    Route::get('/admin/user/viewUser', [AdminUserController::class, 'view'])
        ->name('viewUser');
    Route::get('/admin/users/{id}/edit', [AdminUserController::class, 'edit'])
        ->name('User.edit');
    Route::patch('/admin/users/{id}/edit', [AdminUserController::class, 'update'])
        ->name('User.update');
    Route::delete('/admin/users/viewUser/{id}', [AdminUserController::class, 'destroy'])
        ->name('viewUser.delete');

    // User Upgrade Management
    Route::get('/admin/user/manual-upgrade', [AdminUserController::class, 'adminUpgradeView'])
        ->name('admin.reseller-view');
    Route::post('/admin/user/manual-upgrade', [AdminUserController::class, 'manualUpgrade'])
        ->name('admin.reseller.upgrade');

    // User Wallet Management
    Route::get('/admin/user/walletManage', [AdminUserController::class, 'walletView'])
        ->name('wallets.manage');
    Route::put('/admin/user/walletManage/{wallet}/updateFund', [AdminUserController::class, 'updateWallet'])
        ->name('wallets.updateFund');

    // User Password & PIN Management
    Route::get('/admin/user/changepassword', [AdminUserController::class, 'displaychangepassword'])
        ->name('display.change.password');
    Route::post('/admin/user/changepassword', [AdminUserController::class, 'updatechangePassword'])
        ->name('update.change.Password');

    Route::get('/admin/user/changPin', [AdminUserController::class, 'changePin'])
        ->name('admin.user.changePin');
    Route::post('/admin/user/changPin', [AdminUserController::class, 'updatePin'])
        ->name('update.changePin');

    // Block/Unblock Users
    Route::get('admin/users/block', [AdminUserController::class, 'blockForm'])
        ->name('admin.users.blockForm');
    Route::post('admin/users/block-toggle', [AdminUserController::class, 'toggleBlock'])
        ->name('admin.users.toggleBlock');

    // ========================================================================
    // Admin Settings & Notifications
    // ========================================================================

    Route::get('Settings/notify', [AdminSettingsController::class, 'notify'])
        ->name('Snotify');
    Route::post('Settings/notifystore', [AdminSettingsController::class, 'notifystore'])
        ->name('Snotifystore');

    // ========================================================================
    // Support Management
    // ========================================================================

    Route::get('/admin/appContacts', [AdminSettingsController::class, 'contactView'])
        ->name('admin.settings.appContacts');
    Route::post('/admin/appContacts/store', [AdminSettingsController::class, 'storeTitleQuestion'])
        ->name('admin.settings.store');
    Route::post('/admin/appContacts/sub/store', [AdminSettingsController::class, 'storeSubQuestion'])
        ->name('settings.sub.store');

    // ========================================================================
    // Rewards Management
    // ========================================================================

    Route::get('rewards', [AdminSettingsController::class, 'rewardIndex'])
        ->name('rewards.index');
    Route::get('rewards/{for}/edit', [AdminSettingsController::class, 'rewardEdit'])
        ->name('rewards.edit');
    Route::put('rewards/{for}', [AdminSettingsController::class, 'rewardUpdate'])
        ->name('rewards.update');

    // ========================================================================
    // Emergency Mode
    // ========================================================================

    Route::get('toggle-emergency', [AdminSettingsController::class, 'toggleEmergency'])
        ->name('admin.settings.emergency');
    Route::post('toggle', [AdminSettingsController::class, 'toggle'])
        ->name('toggleE');
    Route::post('log-emergency', [AdminSettingsController::class, 'logEmergency'])
        ->name('admin.settings.logEmergency');

    // ========================================================================
    // Transactions
    // ========================================================================

    Route::get('/admin/transactions/all', [AdminTransactionController::class, 'all'])
        ->name('T.all');
    Route::get('/admin/transactions/processings', [AdminTransactionController::class, 'processings'])
        ->name('T.processings');

    // ========================================================================
    // Voucher & Reseller Management
    // ========================================================================

    Route::resource('voucher-profiles', AdminVoucherController::class)
        ->names('admin.voucher_profiles');

    Route::get('/admin/router-settings/select', [AdminVoucherController::class, 'selectReseller'])
        ->name('VoucherSettings.selectReseller');

    Route::get('/admin/resellers/{reseller}/router-settings', [AdminVoucherController::class, 'viewWanPort'])
        ->name('VoucherSettings.addWanPort');

    Route::put('/admin/resellers/{reseller}/router-settings', [AdminVoucherController::class, 'addWanPort'])
        ->name('admin.router-settings.update');

    Route::post('/admin/resellers/{reseller}/router-settings/reset', [AdminVoucherController::class, 'resetCounts'])
        ->name('admin.router-settings.reset');

    Route::get('/admin/routers/online', [AdminVoucherController::class, 'online'])
        ->name('admin.routers.online');

    // ========================================================================
    // Commission Management
    // ========================================================================

    Route::get('/resellers', [AdminVoucherController::class, 'CommissionIndex'])
        ->name('admin.Commission.index');

    Route::get('/resellers/{reseller}/edit', [AdminVoucherController::class, 'CommissionEdit'])
        ->name('admin.Commission.edit');

    Route::put('/resellers/{reseller}', [AdminVoucherController::class, 'CommissionUpdate'])
        ->name('admin.Commission.update');

    // ========================================================================
    // API Routes (Sanctum Auth)
    // ========================================================================

    Route::post('/api/vouchers/purchase', [GetVoucherController::class, 'purchase'])
        ->middleware('auth:sanctum');

    Route::post('/api/user/upgrade-to-reseller', [ResellerController::class, 'requestUpgrade'])
        ->middleware('auth:sanctum');
});