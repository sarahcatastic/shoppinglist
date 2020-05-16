<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('shoppinglists', 'ShoppinglistController@index');

// get ShoppingListById
Route::get('shoppinglist/{id}', 'ShoppinglistController@findShoppinglistById');

// get ShoppinglisByUser
Route::get('shoppinglists/myLists}', 'ShoppinglistController@findShoppinglistsByCreator');

// save new Shoppinglist
Route::post('shoppinglist', 'ShoppinglistController@save');

//update shoppinglist
Route::put('shoppinglist/{id}', 'ShoppinglistController@edit');

Route::put('shoppinglist/{id}/addComment', 'ShoppinglistController@comment');

Route::delete('shoppinglist/{id}', 'ShoppinglistController@delete');

Route::put('shoppinglist/{id}/update', 'ShoppinglistController@updateVolunteer');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// without login
Route::group(['middleware' => ['api','cors']], function () {
  //  Route::post('auth/register', 'Auth\RegisterController@create');
    Route::post('auth/login', 'Auth\ApiAuthController@login');

    // get User
    Route::get('auth/getUser/{id}', 'Auth\ApiAuthController@getAuthUser');
});

Route::group(['middleware' => ['api', 'cors', 'auth.jwt']], function () {

// get ShoppingListById

    Route::put('shoppinglist/{id}/addComment', 'ShoppinglistController@addComment');
    Route::get('user/{id}', 'ShoppinglistController@getUsernameById');
    Route::get('user/adress/{id}', 'ShoppinglistController@getAdressOfUserById');
    Route::get('comment/{id}', 'ShoppinglistController@getCreatorOfComment');

    Route::post('auth/logout', 'Auth\ApiAuthController@logout');
});

// Creator rights
Route::group(['middleware' => ['api', 'cors', 'auth.jwt', 'creator.jwt']], function () {

    // save new Shoppinglist
    Route::post('shoppinglist', 'ShoppinglistController@save');
    Route::get('shoppinglist/{id}', 'ShoppinglistController@findShoppinglistById');
    //update shoppinglist
    Route::put('shoppinglist/{id}/edit', 'ShoppinglistController@edit');
    Route::put('shoppinglist/{id}/send', 'ShoppinglistController@sendShoppinglist');
    Route::delete('shoppinglist/{id}', 'ShoppinglistController@delete');
    // get ShoppinglisByUser
    Route::get('shoppinglists/myLists', 'ShoppinglistController@findShoppinglistsByCreator');
});

// Volunteer rights
Route::group(['middleware' => ['api', 'cors', 'auth.jwt', 'volunteer.jwt']], function () {
    Route::get('shoppinglists/all', 'ShoppinglistController@index');
    Route::get('/shoppinglists/all/{id}', 'ShoppinglistController@findShoppinglistById');
    Route::get('/shoppinglists/myshops/{id}', 'ShoppinglistController@findShoppinglistById');
    Route::get('shoppinglists/myShops', 'ShoppinglistController@findShoppinglistsByVolunteer');
    Route::put('shoppinglists/all/{id}/book', 'ShoppinglistController@bookShoppinglist');
    Route::put('shoppinglists/all/{id}/close', 'ShoppinglistController@closeShoppinglist');
    Route::put('shoppinglist/{id}/update', 'ShoppinglistController@updateVolunteer');
});