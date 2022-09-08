<?php

use Nabre\Http\Controllers\Auth\AuthenticatedSessionController;
use Nabre\Http\Controllers\Auth\ConfirmablePasswordController;
use Nabre\Http\Controllers\Auth\EmailVerificationNotificationController;
use Nabre\Http\Controllers\Auth\EmailVerificationPromptController;
use Nabre\Http\Controllers\Auth\NewPasswordController;
use Nabre\Http\Controllers\Auth\PasswordResetLinkController;
use Nabre\Http\Controllers\Auth\RegisteredUserController;
use Nabre\Http\Controllers\Auth\VerifyEmailController;

Route::group(['middleware' => ['web']], function () {
    Route::get('/', function () {
        return view("Nabre::welcome");
    })->name('welcome');

    Route::get('/change-language/{locale}', function ($locale) {
        $array = array_unique(array_merge(array_values((array) config('app.available_locales')), (array)  config('app.locale'), (array) config('app.fallback_locale')));
        if (!in_array($locale, $array)) {
            $locale = config('app.fallback_locale') ?? 'en';
        }
        app()->setLocale($locale);

        if ($locale == config('app.locale')) {
            session()->forget('locale');
        } else {
            session()->put('locale', $locale);
        }

        return redirect()->back();
    })->name('set.lang');

    Route::name("nabre.")->group(function () {
        Route::middleware(['auth', 'verified'])->group(function () {
            Route::name("user.")->group(function () {
                Route::resource('user/dashboard', Nabre\Http\Controllers\User\DashController::class, ['key' => 'data'])->only(['index']);
                Route::resource('user/account', Nabre\Http\Controllers\User\AccountController::class, ['key' => 'data'])->only(['index', 'update']);
                Route::resource('user/contact', Nabre\Http\Controllers\User\ContactController::class, ['key' => 'data'])->only(['index', 'update']);
                Route::resource('user/settings', Nabre\Http\Controllers\User\SettingsController::class, ['key' => 'data'])->only(['index', 'store']);
            });

            Route::middleware(['role:manage'])->name("manage.")->group(function () {
                Route::resource('manage/dashboard', Nabre\Http\Controllers\Manage\DashController::class, ['key' => 'data'])->only(['index']);
                Route::resource('manage/contact', Nabre\Http\Controllers\Manage\ContactController::class, ['key' => 'data'])->only(['index', 'create', 'edit', 'destroy']);
                Route::post('manage/contact/{data}/user-generate', [Nabre\Http\Controllers\Manage\ContactController::class, 'userGenerate'])->name('contat.userGenerate');
            });

            Route::middleware(['role:admin'])->name("admin.")->group(function () {
                Route::resource('admin/settings', Nabre\Http\Controllers\Admin\SettingsController::class, ['key' => 'data'])->only(['index', 'store']);

                Route::name("users.")->group(function () {
                    Route::resource('admin/users/list', Nabre\Http\Controllers\Admin\Users\ListController::class, ['key' => 'data'])->only(['index', 'edit', 'create', 'destroy']);
                    Route::resource('admin/users/impersonate', Nabre\Http\Controllers\Admin\Users\ImpersonateController::class, ['key' => 'data'])->only(['index', 'edit'])->except('update');
                    Route::middleware(['role:builder'])->group(function () {
                        Route::resource('admin/users/permission', Nabre\Http\Controllers\Admin\Users\PermissionController::class, ['key' => 'data'])->only(['index', 'edit', 'create', 'destroy']);
                        Route::resource('admin/users/role', Nabre\Http\Controllers\Admin\Users\RoleController::class, ['key' => 'data'])->only(['index', 'edit']);
                    });
                });
            });

            Route::name('admin.users')->resource('admin/users/impersonate', Nabre\Http\Controllers\Admin\Users\ImpersonateController::class, ['key' => 'data'])->only(['create'])->except('store');

            Route::middleware(['role:builder'])->name("builder.")->group(function () {
                Route::resource('admin/builder/database', Nabre\Http\Controllers\Builder\DatabaseController::class, ['key' => 'data'])->only(['index', 'store']);

                Route::name("collections.")->group(function () {
                    Route::resource('admin/builder/collections/fields', Nabre\Http\Controllers\Builder\Collections\FieldsController::class, ['key' => 'data'])->only(['index', 'create', 'edit']);
                    Route::resource('admin/builder/collections/relations', Nabre\Http\Controllers\Builder\Collections\RelationsController::class, ['key' => 'data'])->only(['index', 'create', 'edit','destroy']);
                    Route::resource('admin/builder/collections/demo-console', Nabre\Http\Controllers\Builder\Collections\DemoController::class, ['key' => 'data'])->only(['index']);
                });

                Route::name("navigation.")->group(function () {
                    Route::resource('admin/builder/navigation/pages', Nabre\Http\Controllers\Builder\Navigation\PageController::class, ['key' => 'data'])->only(['index', 'edit', 'destroy']);
                    Route::name("menu.")->group(function () {
                        Route::resource('admin/builder/navigation/menu/auto', Nabre\Http\Controllers\Builder\Navigation\Menu\AutoController::class, ['key' => 'data'])->only(['index', 'edit', 'create', 'destroy']);
                        Route::resource('admin/builder/navigation/menu/custom', Nabre\Http\Controllers\Builder\Navigation\Menu\CustomController::class, ['key' => 'data'])->only(['index', 'edit', 'create', 'destroy']);
                    });
                });

                Route::name("settings.")->group(function () {
                    Route::resource('admin/builder/settings/variables', Nabre\Http\Controllers\Builder\Settings\VariablesController::class, ['key' => 'data'])->only(['index', 'edit']);
                    Route::resource('admin/builder/settings/form-field-type', Nabre\Http\Controllers\Builder\Settings\FormFieldTypeController::class, ['key' => 'data'])->only(['index', 'edit']);
                });
            });
        });
    });




    //if(config('setting.app.auth.register',0)){
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->middleware(['guest', 'registration'])
        ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('guest', 'registration');
    //}


    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest')
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->middleware('guest')
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->middleware('guest')
        ->name('password.reset');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.update');

    Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->middleware('auth')
        ->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['auth', 'signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');

    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->middleware('auth')
        ->name('password.confirm');

    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware('auth');

    Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
});
