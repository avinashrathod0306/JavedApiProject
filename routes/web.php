<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/backgrounds', function () {
    $data = \App\Models\Background::select('id','name','thumbImg','image')->get();

    return response()->json([
        'data' => $data
    ]);
});

Route::get('/categories', function () {
    $data = \App\Models\Category::select('category_id','category_name','category_list')->get();

    return response()->json([
        'thumbnail_bg' => $data
    ]);
});
