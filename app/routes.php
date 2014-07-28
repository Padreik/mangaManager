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
Route::get('import/series', 'ImportController@series');
Route::post('import/series', 'ImportController@seriesSave');
Route::get('import/manga', 'ImportController@manga');
Route::post('import/manga', 'ImportController@mangaSave');

Route::resource('series', 'SeriesController');
Route::get('series/{id}/image', 'SeriesController@image');

Route::resource('manga', 'MangaController', array('except' => array('create')));
Route::get('manga/create/{series_id}', 'MangaController@create');
Route::get('manga/{id}/image', 'MangaController@image');

Route::get('loan/history', 'LoanController@history');
Route::resource('loan', 'LoanController');

Route::resource('return', 'ReturnController', array('except' => array('create')));
Route::get('return/create/{borrower_id}', 'ReturnController@create');

Route::put('cart/{id}', 'LoanCartController@add');
Route::post('cart/{id}', 'LoanCartController@update');
Route::delete('cart/{id}', 'LoanCartController@remove');






Validator::resolver(function($translator, $data, $rules, $messages) {
    return new \pgirardnet\Manga\CustomValidator($translator, $data, $rules, $messages);
});