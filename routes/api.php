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
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/enterpreneur','api\UserApiController@enterpreneurregister');
Route::post('/login', 'api\UserApiController@login');

Route::post('/createToken', 'api\StripeController@createToken');
Route::post('/Charge', 'api\StripeController@Charge');
Route::post('/PaymentIntent', 'api\StripeController@PaymentIntent');
Route::post('/createPaymentMethod', 'api\StripeController@createPaymentMethod');
Route::post('/new_one', 'api\StripeController@new_one');
Route::post('/second_one', 'api\StripeController@second_one');



Route::post('/createCustomer', 'api\StripeController@createCustomer');
Route::post('/generateCardToken', 'api\StripeController@generateCardToken');
Route::post('/createInvoiceAndPayWithDebitCard', 'api\StripeController@createInvoiceAndPayWithDebitCard');
Route::post('/card_chk', 'api\StripeController@card_chk');

Route::post('/register_new', 'api\UserApiController@register');
Route::post('/register1', 'api\UserApiController@update');
Route::post('/forgetpassword', 'api\UserApiController@forgetPassword');
Route::post('/sendotp', 'api\UserApiController@sendotp');
Route::post('/resendotp', 'api\UserApiController@resendotp');
Route::post('/checkotp', 'api\UserApiController@checkotp');

Route::get('/settings','api\UserApiController@settings'); 
Route::get('/sharedSettings','api\UserApiController@sharedSettings'); 

Route::get('/salon', 'api\UserApiController@singleSalon'); 
Route::get('/categories', 'api\UserApiController@categories');

Route::get('/coupon', 'api\UserApiController@allCoupon');

Route::get('/banners', 'api\UserApiController@banners'); 
Route::get('/offers', 'api\UserApiController@offers'); 

Route::post('/timeslot', 'api\UserApiController@timeSlot'); 
Route::post('/selectemp', 'api\UserApiController@selectEmp'); 

Route::post('usercategory','api\UserApiController@addusercategory');

Route::post('/media_upload','api\UserApiController@media_upload');


Route::middleware('auth:api')->group(function()
{
    Route::get('/profile', 'api\UserApiController@showUser');
    Route::post('/profile/edit', 'api\UserApiController@editUser');
    Route::post('/profile/address/add', 'api\UserApiController@addUserAddress'); 
    Route::get('/profile/address/remove/{id}', 'api\UserApiController@removeUserAddress'); 
    
    Route::post('/checkcoupon', 'api\UserApiController@checkCoupon');

    Route::post('/booking', 'api\UserApiController@booking');

    Route::get('/appointment', 'api\UserApiController@showAppointment');
    Route::get('/appointment/{id}', 'api\UserApiController@singleAppointment'); 
    Route::get('/appointment/cancel/{id}', 'api\UserApiController@cancelAppointment');

    Route::post('/addreview','api\UserApiController@addReview');
    Route::get('/deletereview/{id}','api\UserApiController@deleteReview');

    Route::post('/changepassword', 'api\UserApiController@changePassword');

    Route::get('/notification', 'api\UserApiController@notification');
    Route::get('/payment_gateway', 'api\UserApiController@payment_gateway');


    
});

