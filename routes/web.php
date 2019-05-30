<?php
Route::get('/', function () { return redirect('/admin/home'); });

// Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('auth.login');
$this->post('login', 'Auth\LoginController@login')->name('auth.login');
$this->post('logout', 'Auth\LoginController@logout')->name('auth.logout');

// Change Password Routes...
$this->get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
$this->patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.password.reset');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset')->name('auth.password.reset');

// Registration Routes..
$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('auth.register');
$this->post('register', 'Auth\RegisterController@register')->name('auth.register');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/home', 'HomeController@index');

    Route::resource('members', 'Admin\MembersController');
    Route::post('members_mass_destroy', ['uses' => 'Admin\MembersController@massDestroy', 'as' => 'members.mass_destroy']);
    Route::get('get_subscriptions', ['uses' => 'Admin\SubscribersController@getByMember', 'as' => 'members.get_subscriptions']);

    Route::resource('schemes', 'Admin\SchemesController');
    Route::put('schemes/{id}/start', 'Admin\SchemesController@start')->name('schemeStart');
    Route::post('schemes_mass_destroy', ['uses' => 'Admin\SchemesController@massDestroy', 'as' => 'schemes.mass_destroy']);

    Route::post('subscriber/scheme/{scheme_id}', ['uses' => 'Admin\SubscribersController@update', 'as' => 'subscribe']);

    Route::post('schemes/{id}/auction', ['uses' => 'Admin\AuctionsController@save', 'as' => 'auction.save']);
    Route::get('schemes/{id}/auction/{auctionid}/generate', ['uses' => 'Admin\AuctionsController@generate', 'as' => 'auction.generate']);
    Route::get('schemes/{id}/auction/{auctionid}/skip', ['uses' => 'Admin\AuctionsController@skip', 'as' => 'auction.skip']);
    Route::put('schemes/{id}/auction/bidwon', ['uses' => 'Admin\AuctionsController@awardBid', 'as' => 'auction.bidwon']);

    Route::put('schemes/{id}/installments', ['uses' => 'Admin\InstallmentsController@loadData', 'as' => 'schemes.installments']);
    Route::get('get_pending_installments', ['uses' => 'Admin\InstallmentsController@getBySubscriber', 'as' => 'installments.get_by_subscriber']);

    Route::post('payinstallment', ['uses' => 'Admin\LedgerEntriesController@addPayment', 'as' => 'payinstallment']);
    Route::post('add_scheme_expense', ['uses' => 'Admin\LedgerEntriesController@addSchemeExpense', 'as' => 'add_scheme_expense']);
    Route::post('payments_data', ['uses' => 'Admin\PaymentsController@loadData', 'as' => 'payments.load_data']);

    Route::resource('roles', 'Admin\RolesController');
    Route::post('roles_mass_destroy', ['uses' => 'Admin\RolesController@massDestroy', 'as' => 'roles.mass_destroy']);


    Route::resource('users', 'Admin\UsersController');
    Route::post('users_mass_destroy', ['uses' => 'Admin\UsersController@massDestroy', 'as' => 'users.mass_destroy']);
    Route::resource('ledger_categories', 'Admin\LedgerCategoriesController');
    Route::post('ledger_categories_mass_destroy', ['uses' => 'Admin\LedgerCategoriesController@massDestroy', 'as' => 'ledger_categories.mass_destroy']);
    Route::resource('ledger_entries', 'Admin\LedgerEntriesController');
    Route::get('ledgerdelete/{id}', ['uses' => 'Admin\LedgerEntriesController@destroy', 'as' => 'ledgerdelete']);

    Route::post('ledger_entries_mass_destroy', ['uses' => 'Admin\LedgerEntriesController@massDestroy', 'as' => 'ledger_entries.mass_destroy']);
    Route::resource('monthly_reports', 'Admin\MonthlyReportsController');
    Route::resource('currencies', 'Admin\CurrenciesController');
    Route::post('currencies_mass_destroy', ['uses' => 'Admin\CurrenciesController@massDestroy', 'as' => 'currencies.mass_destroy']);
    Route::post('currencies_restore/{id}', ['uses' => 'Admin\CurrenciesController@restore', 'as' => 'currencies.restore']);
    Route::delete('currencies_perma_del/{id}', ['uses' => 'Admin\CurrenciesController@perma_del', 'as' => 'currencies.perma_del']);




});
