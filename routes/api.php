<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(
    [
        'middleware'=>'api',
        'namespace'=>'App\Http\Controllers',
        'prefix'=>'auth'
    ],
    function($router){
        Route::post('{type}/login','LoginController@login');
        Route::post('{type}/register','RegisterController@register');
        Route::get('/authentication_error', function(Request $request) {
            return response()->json([
                'status'=>false,
                'message' => 'Authentication error!'
            ], 401);
        })->name("authentication_error");
        Route::get('/unauthorization', function(Request $request) {
            return response()->json([
                'status'=>false,
                'message' => 'Unauthentication!'
            ], 401);
        })->name("unauthorization");
    }

);

Route::group(
    [
        'middleware'=>'auth:api',
        'namespace'=>'App\Http\Controllers',
        'prefix'=>'auth'
    ],
    function($router){
       
        Route::post('logout','AuthController@logout');
        Route::post('profile','AuthController@profile');
        
    }

);
Route::group(
    [
        'middleware'=>'auth:api',
        'namespace'=>'App\Http\Controllers',
        'prefix'=>'invoices'
    ],
    function($router){
       
        Route::get('','InvoiceController@index' );
        Route::post('add','InvoiceController@store' );
        Route::put('update/{invoice}','InvoiceController@update' );
        Route::put('send/{invoice}','InvoiceController@send' );
        Route::delete('delete/{invoice}','InvoiceController@destroy');
        Route::get('get/{invoice}','InvoiceController@show' );
        
    }

);



