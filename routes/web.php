<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::post('axl-a', 'AlxAController@find')->name('alx-a.find');

Route::get('axl-b', 'AlxBController@dataExtractor')->name('alx-b.extract');

Route::get('listAlx', 'AlxListController@listData')->name('alx-b.list');

Route::get('axl-b-form', 'AlxBController@form')->name('alx-b.form');


Route::get('ultimatexablau', 'ContabilityController@cont')->name('contbility');
