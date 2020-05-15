<?php

use App\ShoppingList;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'ShoppinglistController@index');

Route::get('/shoppinglists', 'ShoppinglistController@index');

Route::get('/shoppinglists/{id}', 'ShoppinglistController@show');