<?php
use App\Http\Controllers\MailController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
})->middleware('verified');
//Route::get('/home', function () {
//    $routeName = auth()->user() && (auth()->user()->is_user || auth()->user()->is_cfo) ? 'admin.loanApplications.index' : 'admin.home';
//    if (session('status')) {
//        return redirect()->route($routeName)->with('status', session('status'));
//    }
//
//    return redirect()->route($routeName);
//});
Auth::routes();

//Auth::routes(['verify' => true]);

Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

// Admin

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
//    Route::get('/', 'HomeContoller@build') ->name('home');
    Route::get('/incomplete', 'HomeController@incomplete')->name('incomplete');


    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Statuses
    Route::delete('statuses/destroy', 'StatusesController@massDestroy')->name('statuses.massDestroy');
    Route::resource('statuses', 'StatusesController');

    // Loan Applications
    Route::delete('loan-applications/destroy', 'LoanApplicationsController@massDestroy')->name('loan-applications.massDestroy');
    Route::get('loan-applications/{loan_application}/analyze', 'LoanApplicationsController@showAnalyze')->name('loan-applications.showAnalyze');
    Route::post('loan-applications/{loan_application}/analyze', 'LoanApplicationsController@analyze')->name('loan-applications.analyze');
    Route::get('loan-applications/{loan_application}/send', 'LoanApplicationsController@showSend')->name('loan-applications.showSend');
    Route::post('loan-applications/{loan_application}/send', 'LoanApplicationsController@send')->name('loan-applications.send');
    Route::resource('loan-applications', 'LoanApplicationsController');

    // Comments
    Route::delete('comments/destroy', 'CommentsController@massDestroy')->name('comments.massDestroy');
    Route::resource('comments', 'CommentsController');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
// Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
    }
});

Route::get('/send',[\App\Http\Controllers\Admin\LoanApplicationsController::class,'store']);
Route::get('/send',[\App\Http\Controllers\Admin\LoanApplicationsController::class,'analyze']);
Route::get('/send',[\App\Http\Controllers\Admin\LoanApplicationsController::class,'send']);


Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');



//
//use App\Http\Controllers\MailController;
//
//Route::redirect('/', '/login');
//Route::get('/home', function () {
//    if (session('status')) {
//        return redirect()->route('admin.loan-applications.index')->with('status', session('status'));
//    }
//
//    return redirect()->route('admin.loan-applications.index');
//});
//
//Auth::routes();
//// Admin
//
//Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
//    Route::get('/', 'HomeController@index')->name('home');
//    Route::get('/', 'HomeContoller@build') ->name('home');
//    // Permissions
//    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
//    Route::resource('permissions', 'PermissionsController');
//
//    // Roles
//    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
//    Route::resource('roles', 'RolesController');
//
//    // Users
//    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
//    Route::resource('users', 'UsersController');
//
//    // Statuses
//    Route::delete('statuses/destroy', 'StatusesController@massDestroy')->name('statuses.massDestroy');
//    Route::resource('statuses', 'StatusesController');
//
//    // Loan Applications
//    Route::delete('loan-applications/destroy', 'LoanApplicationsController@massDestroy')->name('loan-applications.massDestroy');
//    Route::get('loan-applications/{loan_application}/analyze', 'LoanApplicationsController@showAnalyze')->name('loan-applications.showAnalyze');
//    Route::post('loan-applications/{loan_application}/analyze', 'LoanApplicationsController@analyze')->name('loan-applications.analyze');
//    Route::get('loan-applications/{loan_application}/send', 'LoanApplicationsController@showSend')->name('loan-applications.showSend');
//    Route::post('loan-applications/{loan_application}/send', 'LoanApplicationsController@send')->name('loan-applications.send');
//    Route::resource('loan-applications', 'LoanApplicationsController');
//
//    // Comments
//    Route::delete('comments/destroy', 'CommentsController@massDestroy')->name('comments.massDestroy');
//    Route::resource('comments', 'CommentsController');
//});
//Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
//// Change password
//    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
//        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
//        Route::post('password', 'ChangePasswordController@update')->name('password.update');
//    }
//});
//
//Route::get('/send',[\App\Http\Controllers\Admin\LoanApplicationsController::class,'store']);
//Route::get('/send',[\App\Http\Controllers\Admin\LoanApplicationsController::class,'analyze']);
//Route::get('/send',[\App\Http\Controllers\Admin\LoanApplicationsController::class,'send']);
//
