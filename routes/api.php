<?php

Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {

});


Route::post('schemes/{id}/ledger', ['uses' => 'Admin\LedgerEntriesController@ledgerByScheme', 'as' => 'schemes.ledger']);
Route::post('members/{id}/subscriptions', ['uses' => 'Admin\SubscribersController@getDataByMember', 'as' => 'members.subscriptions']);
Route::post('members/{id}/installments', ['uses' => 'Admin\InstallmentsController@getDataByMember', 'as' => 'members.installments']);
Route::post('members/{id}/payments', ['uses' => 'Admin\LedgerEntriesController@getDataByMember', 'as' => 'members.payments']);
Route::post('load_members', ['uses' => 'Admin\MembersController@loadData', 'as' => 'load_members']);
Route::post('load_schemes', ['uses' => 'Admin\SchemesController@loadData', 'as' => 'load_schemes']);
Route::post('load_ledger', ['uses' => 'Admin\LedgerEntriesController@loadData', 'as' => 'load_ledger']);

Route::post('installments_ajax_data_by_scheme', ['uses' => 'Admin\InstallmentsController@loadData', 'as' => 'installments.ajax_load_data_by_scheme']);
