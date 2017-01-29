<?php

Auth::routes();

Route::get('/', function() {
	return view('welcome');
});
Route::get('/home', 'HomeController@index');

Route::post('/purchases', ['as' => 'purchases.store', 'uses' => 'PurchasesController@store']);

Route::group(['prefix' => 'account', 'as' => 'account.', 'middleware' => 'auth'], function() {
	Route::get('billing', ['as' => 'billing.index', 'uses' => 'BillingController@index']);
	Route::get('billing/return', ['as' => 'billing.return', 'uses' => 'BillingController@return']);
	Route::post('billing/primary', ['as' => 'billing.primary', 'uses' => 'BillingController@primary']);
	Route::post('billing/remove', ['as' => 'billing.remove', 'uses' => 'BillingController@remove']);
	Route::post('billing', ['as' => 'billing.store', 'uses' => 'BillingController@store']);	
});