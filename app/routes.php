<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'SeriesController@index');

Route::get('import/collection', 'ImportController@collection');
Route::post('import/collection', 'ImportController@collectionSave');
Route::get('import/ajax/series', 'ImportController@ajaxNextSeries');

Route::resource('series', 'SeriesController');
Route::get('series/{id}/image', 'SeriesController@image');

Route::resource('manga', 'MangaController');
Route::get('manga/{id}/image', 'MangaController@image');

Route::resource('load', 'LoanController');

Route::put('cart/{id}', 'LoanCartController@add');
Route::post('cart/{id}', 'LoanCartController@update');
Route::delete('cart/{id}', 'LoanCartController@remove');