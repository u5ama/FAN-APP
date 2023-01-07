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
Route::prefix('v1')->name('api.v1.')->namespace('Api\V1')->group(function(){

    // User Register
    Route::post('register', 'UserController@register')->name('register');

    //Categories
    Route::get('categories', 'PlayerController@categories')->name('categories');

    //Schools
    Route::get('player_category', 'PlayerController@playerCategory')->name('player_category');

    //Teams
    Route::post('player_team', 'PlayerController@playerTeam')->name('player_team');

    //Recommendation
    Route::post('recommend_school', 'ReportProblemController@recommendSchool')->name('recommend_school');

    // User Login
    Route::post('login', 'UserController@loginUser')->name('login');

    // Menu list
    Route::get('getMenu', 'HomeController@getMenu')->name('getMenu');

    // Social Links
    Route::get('socialLinks', 'SocialLinkController');

    // Pages
    Route::get('allPages', 'PageController@allPages')->name('allPages');
    Route::get('page/{id}', 'PageController@index')->name('index');

    Route::group(['middleware' => ['jwt.verify']], function(){
            // User Profile Image
            Route::post('profile_image', 'UserController@profileImage')->name('profile_image');

            // User ID Card Image
            Route::post('player_id_card', 'UserController@userIDCard')->name('player_id_card');

            // user edit profile
            Route::post('editProfile', 'UserController@editProfile')->name('editProfile');

            // log out
            Route::get('logout', 'UserController@logout')->name('logout');

            //search Fan
            Route::get('search_player', 'UserController@SearchPlayer')->name('search_player');

            //Players
            Route::get('players', 'PlayerController@allPlayers')->name('players');

            //Get Player by Category
            Route::get('get_player', 'PlayerController@getPlayerByCategory')->name('get_player');

            //Get Player Info
            Route::get('player_info', 'PlayerController@getPlayerInfo')->name('player_info');

            //Player Gifts
            Route::get('player_gifts', 'UserController@getPlayerGifts')->name('player_gifts');

            //Gifts
            Route::get('all_gifts', 'PlayerController@allGifts')->name('all_gifts');

            //Redeem Gifts
            Route::post('redeem_gift', 'PlayerController@redeemGift')->name('redeem_gift');

            //Buy Gift
            Route::post('buy_gift', 'UserController@buyGift')->name('buy_gift');

            //get gifts sent
            Route::get('gifts_sent', 'UserController@giftsSent')->name('gifts_sent');

            //get gifts received
            Route::get('gifts_received', 'PlayerController@giftsReceived')->name('gifts_received');

            //get wallet
            Route::get('getWallet', 'CustomerManagementController@getCustomerWallet')->name('getWallet');

            //add new wallet
            Route::post('addWallet', 'CustomerManagementController@addCustomerWallet')->name('addWallet');

            //delete wallet
            Route::post('deleteWallet', 'CustomerManagementController@deleteCustomerWallet')->name('deleteWallet');

            //get notifications
            Route::get('get_notifications', 'UserController@getGiftsNotifications')->name('get_notifications');

            //Post Notifications
            Route::post('add_notifications', 'UserController@postNotifications')->name('add_notifications');
        });
});
