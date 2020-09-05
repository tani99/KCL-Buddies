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

Route::view('/', 'welcome')->name('welcome');

Route::get('home', 'UserWebpageController@userIndex')->name('user.home');
Route::get('profile', 'UserWebpageController@userProfile')->name('user.profile.index');
Route::post('profile', 'UserWebpageController@saveUserProfile')->name('user.profile.save');
Route::delete('profile', 'UserWebpageController@deleteProfilePicture')->name('user.profile.delete_profile_picture');
Route::get('admin/home', 'UserWebpageController@adminIndex')->name('admin.home');

Route::post('admin/user', 'UserController@updateCredentials')->name('admin.user.update_credentials');
Route::get('admin/users', 'UserController@index')->name('admin.users.index');
Route::get('admin/users/create', 'UserController@create')->name('admin.users.create');
Route::post('admin/users', 'UserController@store')->name('admin.users.store');
Route::get('admin/users/email', 'EmailController@emailUsersPage')->name('admin.users.email.index');
Route::post('admin/users/email', 'EmailController@emailUsers')->name('admin.users.email.send');
Route::get('admin/users/{user_id}', 'UserController@show')->name('admin.users.show');
Route::get('admin/users/{user_id}/edit', 'UserController@edit')->name('admin.users.edit');
Route::put('admin/users/{user_id}', 'UserController@update')->name('admin.users.update');
Route::delete('admin/users/{user_id}', 'UserController@destroy')->name('admin.users.destroy');
Route::get('admin/users/{user_id}/ban', 'UserController@toggleBan')->name('admin.users.toggle_ban');

Route::resource('schemes', 'SchemeController');
Route::delete('schemes/{scheme_id}/icon', 'SchemeController@deleteIcon')->name('schemes.delete_icon');

Route::get('apply/{join_code}', 'SchemeController@applyJoinCode')->name('schemes.apply_join_code');
Route::post('schemes/apply', 'SchemeController@apply')->name('schemes.apply');
Route::post('schemes/join', 'SchemeController@join')->name('schemes.join');
Route::post('schemes/{scheme_id}/reset/{user_type_id}', 'SchemeController@resetJoinCode')->name('schemes.reset_join_code');

Route::get('schemes/{scheme_id}/pair', 'SchemePairingController@store')->name('schemes.pairs.store');
Route::get('schemes/{scheme_id}/pairings', 'SchemePairingController@index')->name('schemes.pairs.index');
Route::get('schemes/{scheme_id}/pairings/view', 'SchemePairingController@userIndex')->name('schemes.pairs.user_index');
Route::delete('schemes/{scheme_id}/pairings/{pairing_id}', 'SchemePairingController@destroy')->name('schemes.pairs.destroy');
Route::delete('schemes/{scheme_id}/pairings', 'SchemePairingController@destroyAll')->name('schemes.pairs.destroy_all');

Route::get('schemes/{scheme_id}/users', 'SchemeUserController@index')->name('schemes.users.index');
Route::get('schemes/{scheme_id}/users/approve', 'SchemeUserController@approveAll')->name('schemes.users.approve_all');
Route::get('schemes/users/approve/{scheme_user_id}', 'SchemeUserController@approve')->name('schemes.users.approve');
Route::get('schemes/{scheme_id}/users/kick', 'SchemeUserController@kickAll')->name('schemes.users.kick_all');
Route::get('schemes/users/kick/{scheme_user_id}', 'SchemeUserController@kick')->name('schemes.users.kick');
Route::get('schemes/users/ban/{scheme_user_id}', 'SchemeUserController@ban')->name('schemes.users.ban_existing');
Route::get('schemes/{scheme_id}/unban/{user_id}', 'SchemeUserController@unban')->name('schemes.users.unban');
Route::get('schemes/{scheme_id}/preferences', 'SchemeController@preferences')->name('schemes.preferences.edit');
Route::post('schemes/{scheme_id}/preferences', 'SchemeController@updatePreferences')->name('schemes.preferences.update');

Route::get('schemes/{scheme_id}/questions', 'SchemeQuestionController@index')->name('schemes.questions.index');
Route::get('schemes/{scheme_id}/questions/edit', 'SchemeQuestionController@edit')->name('schemes.questions.edit');
Route::post('schemes/{scheme_id}/questions', 'SchemeQuestionController@update')->name('schemes.questions.update');
Route::post('schemes/{scheme_id}/questions/weightings', 'SchemeQuestionController@updateWeightings')->name('schemes.questions.update_weightings');

Route::get('schemes/{scheme_id}/email', 'EmailController@emailSchemeUsersPage')->name('schemes.email.users.index');
Route::post('schemes/{scheme_id}/email', 'EmailController@emailSchemeUsers')->name('schemes.email.users.send');

Route::get('rules/{scheme_id}', 'SchemeRuleController@index')->name('rules.index');
Route::get('rules/{scheme_id}/edit', 'SchemeRuleController@edit')->name('rules.edit');
Route::put('rules/{scheme_id}', 'SchemeRuleController@update')->name('rules.update');

Route::get('signin', 'Auth\OAuth2\MSFTAuthController@userLogin')->name('signin');
Route::get('authorize', 'Auth\OAuth2\MSFTAuthController@retrieveToken');

// Authentication Routes...
Route::get('login', function (){
    return redirect('/');
})->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
