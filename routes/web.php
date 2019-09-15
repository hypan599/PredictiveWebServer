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

Route::get('/', 'Home\FileManagerController@get_file_list');

Route::get('about', function () {
    return view('about');
});

Route::get('output', function () {
    return view('output');
});

Route::get('download/{jobname}', 'Home\FileManagerController@download');

Route::get('start_ajax', 'Home\FileManagerController@ajax_analysis');

Route::get('analysis/{status}', 'Home\FileManagerController@get_file_list');

Route::post('FileManager/file_upload', 'Home\FileManagerController@upload');

Route::post('FileManager/file_downloadOrDelete', 'Home\FileManagerController@downloadOrDelete');

Route::get('FileManager/{status}', 'Home\FileManagerController@index');
