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
    return view('layouts.frontend.home.index');
});

Auth::routes(); //The method routes(); is implemented in core of the framework in the file /vendor/laravel/framework/src/Illuminate/Routing/Router.php public function auth()

Route::get('/home', 'HomeController@index')->name('home');
 
//facebook login
Route::get('/redirect', 'SocialController@redirect');
Route::get('auth/{provider}/callback', 'SocialController@callback');

//Google login
Route::get('/redirect/google', 'SocialAuthGoogleController@redirect')->name('google');
Route::get('/callback', 'SocialAuthGoogleController@callback');

Route::name('showMenus')->get('/', 'MenuController@showMenus');

//verify user after registration
Auth::routes(['verify' => true]); //untuk menjalankan kode ini tambahkan implements MustVerifyEmail di User.php

//student register page
Route::get('/student-regis', 'StudentRegistrarsController@register')->name('student-regis');
Route::post('/student-regis/store', 'StudentRegistrarsController@store')->name('student-registrars.store');

//father register page
Route::get('/father-regis', 'FatherRegistrarsController@register')->name('father-regis');
Route::post('/father-regis/store', 'FatherRegistrarsController@store')->name('father-registrars.store');

//mother register page
Route::get('/mother-regis', 'MotherRegistrarsController@register')->name('mother-regis');
Route::post('/mother-regis/store', 'MotherRegistrarsController@store')->name('mother-registrars.store');

Route::group(['middleware' => ['auth']], function() {
    //Manage Role
    Route::resource('roles','RoleController');

    //Manage Users
    Route::get('/users/adminPDF', 'UserController@downloadPDF')->name('users.pdf');
    Route::get('/users/downloadExcel', 'UserController@downloadExcel')->name('users.excel');
    Route::get('/users/downloadWord', 'UserController@downloadWord')->name('users.word');
    Route::get('/users/activeAdminPDF', 'UserController@downloadActiveAdminPDF')->name('users.pdfactiveadmin');
    Route::get('/users/downloadActiveExcel', 'UserController@downloadActiveExcel')->name('users.activeexcel');
    Route::get('/users/activeAdminWord', 'UserController@downloadActiveAdminWord')->name('users.wordactiveadmin');
    Route::get('/users/inactiveAdminPDF', 'UserController@downloadInactiveAdminPDF')->name('users.pdfinactiveadmin');
    Route::get('/users/downloadInactiveExcel', 'UserController@downloadInactiveExcel')->name('users.inactiveexcel');
    Route::get('/users/inactiveAdminWord', 'UserController@downloadInactiveAdminWord')->name('users.wordinactiveadmin');
    Route::get('users/trash', 'UserController@trash')->name('users.trash');
    Route::get('/user/{id}/restore', 'UserController@restore')->name('users.restore'); 
    Route::get('usersadminRestoreAll', 'UserController@restoreMultiple');
    Route::delete('/user/{id}/delete-permanent', 'UserController@deletePermanent')->name('users.delete-permanent');
    Route::get('usersadminDeleteAll', 'UserController@deleteMultiple');
    Route::name('users.active')->get('/users/active', 'UserController@active');
    Route::name('users.inactive')->get('/users/inactive', 'UserController@inactive');
    Route::get('/user/{id}/activate', 'UserController@activate')->name('users.activate');  
    Route::get('/user/{id}/deactivate', 'UserController@deactivate')->name('user.deactivate'); 
    Route::get('usersadminDeactivateAll', 'UserController@deactivateMultiple');
    Route::get('usersadminActivateAll', 'UserController@activateMultiple');
    Route::get('searchrole', 'UserController@ajaxSearch');
    Route::get('/user/{id}/edit',  ['as' => 'user.edit', 'uses' => 'UserController@edit']); //for breadcrumbs
    Route::resource('users','UserController');
    Route::get('usersadminTrashAll', 'UserController@destroyMultiple'); //for multiple trash

    //Profile
    Route::get('/profile/{username}', 'ProfileUserController@show')->name('show.applicant');
    Route::get('saveForm', 'ProfileUserController@update');
    Route::get('/usersadmin/{id}/avatar', 'ProfileUserController@deleteAvatar')->name('delete.avatar');
    Route::get('password/change', 'ProfileUserController@changePassword');
    Route::post('password/change', 'ProfileUserController@postChangePassword');
    Route::resource("profile", 'ProfileUserController'); //tidak boleh diletakkan di urutan pertama agar route ke view bekerja. Kode ini akan mengenerate otomatis route edit, index, store, create, edit, destroy, update, show

    //Menus 
    Route::get('wmenuindex', array('as' => 'menus.index', 'uses'=>'MenuController@index'));
    $path = rtrim(config('menu.route_path'));
    Route::post($path . '/addcustommenu', array('as' => 'haddcustommenu', 'uses' => 'MenuController@addcustommenu'));
    Route::post($path . '/deleteitemmenu', array('as' => 'hdeleteitemmenu', 'uses' => 'MenuController@deleteitemmenu'));
    Route::post($path . '/deletemenug', array('as' => 'hdeletemenug', 'uses' => 'MenuController@deletemenug'));
    Route::post($path . '/createnewmenu', array('as' => 'hcreatenewmenu', 'uses' => 'MenuController@createnewmenu'));
    Route::post($path . '/generatemenucontrol', array('as' => 'hgeneratemenucontrol', 'uses' => 'MenuController@generatemenucontrol'));
    Route::post($path . '/updateitem', array('as' => 'hupdateitem', 'uses' => 'MenuController@updateitem'));

    //student-registrars index page 
    Route::get('student-registrarsTrashAll', 'StudentRegistrarsController@destroyMultiple'); //for multiple trash
    Route::get('student-registrars/trash', 'StudentRegistrarsController@trash')->name('student-registrars.trash');
    Route::get('/student-registrars/{id}/restore', 'StudentRegistrarsController@restore')->name('student-registrars.restore'); 
    Route::get('student-registrarsRestoreAll', 'StudentRegistrarsController@restoreMultiple');
    Route::delete('/student-registrars/{id}/delete-permanent', 'StudentRegistrarsController@deletePermanent')->name('student-registrars.delete-permanent');
    Route::get('student-registrarsDeleteAll', 'StudentRegistrarsController@deleteMultiple');
    Route::name('student-registrars.pending')->get('/student-registrars/pending', 'StudentRegistrarsController@pending');
    Route::name('student-registrars.showrejected')->get('/student-registrars/show-rejected', 'StudentRegistrarsController@showRejected');
    Route::get('student-registrarsRejectAll', 'StudentRegistrarsController@rejectMultiple'); 
    Route::get('/student-registrars/{id}/reject', 'StudentRegistrarsController@reject')->name('student-registrars.reject'); 
    Route::get('/student-registrars/{id}/hold', 'StudentRegistrarsController@hold')->name('student-registrars.hold'); 
    Route::get('student-registrarsHoldAll', 'StudentRegistrarsController@holdMultiple'); 
    Route::get('student-registrars/index', 'StudentRegistrarsController@index')->name('student-registrars.index');
    Route::get('/student-registrars/{id}/show', 'StudentRegistrarsController@show')->name('student-registrars.show');
    Route::delete('/student-registrars/{id}/trash', 'StudentRegistrarsController@destroy')->name('student-registrars.destroy'); 
    Route::get('/student-registrars/show-eligible', 'StudentRegistrarsController@showEligible')->name('student-registrars.showeligible');
    Route::get('/student-registrars/{id}/eligible', 'StudentRegistrarsController@eligible')->name('student-registrars.eligible'); 
    Route::get('student-registrarsEligibleAll', 'StudentRegistrarsController@eligibleMultiple'); 
    Route::get('/student-registrars/show-approved', 'StudentRegistrarsController@showApproved')->name('student-registrars.showapproved');
    Route::get('/student-registrars/{id}/approve', 'StudentRegistrarsController@approve')->name('student-registrars.approve'); 
    Route::get('student-registrarsApproveAll', 'StudentRegistrarsController@approveMultiple'); 
    Route::get('/student-registrars/{id}/cancel', 'StudentRegistrarsController@cancel')->name('student-registrars.cancel'); 
    Route::get('student-registrarsCancelAll', 'StudentRegistrarsController@cancelMultiple'); 
    Route::get('/student-registrars/{id}/rollback', 'StudentRegistrarsController@rollback')->name('student-registrars.rollback'); 
    Route::get('student-registrarsRollbackAll', 'StudentRegistrarsController@rollbackMultiple'); 

    //father-registrars index page
    Route::get('father-registrars/index', 'FatherRegistrarsController@index')->name('father-registrars.index');
    Route::get('/father-registrars/{id}/show', 'FatherRegistrarsController@show')->name('father-registrars.show');
    Route::name('father-registrars.pending')->get('/father-registrars/pending', 'FatherRegistrarsController@pending');
    Route::name('father-registrars.showapproved')->get('/father-registrars/show-approved', 'FatherRegistrarsController@showApproved');
    Route::name('father-registrars.showrejected')->get('/father-registrars/show-rejected', 'FatherRegistrarsController@showRejected');
    Route::get('father-registrars/trash', 'FatherRegistrarsController@trash')->name('father-registrars.trash');
    Route::name('father-registrars.showeligible')->get('/father-registrars/show-eligible', 'FatherRegistrarsController@showEligible');
    Route::get('/father-registrars/{id}/restore', 'FatherRegistrarsController@restore')->name('father-registrars.restore'); 
    
    //mother-registrars index page
    Route::get('mother-registrars/index', 'MotherRegistrarsController@index')->name('mother-registrars.index');
    Route::get('/mother-registrars/{id}/show', 'MotherRegistrarsController@showMother')->name('mother-registrars.show');
    Route::name('mother-registrars.pending')->get('/mother-registrars/pending', 'MotherRegistrarsController@pending');
    Route::name('mother-registrars.showapproved')->get('/mother-registrars/show-approved', 'MotherRegistrarsController@showApproved');
    Route::name('mother-registrars.showrejected')->get('/mother-registrars/show-rejected', 'MotherRegistrarsController@showRejected');
    Route::get('mother-registrars/trash', 'MotherRegistrarsController@trash')->name('mother-registrars.trash');
    Route::name('mother-registrars.showeligible')->get('/mother-registrars/show-eligible', 'MotherRegistrarsController@showEligible');
    Route::get('/mother-registrars/{id}/restore', 'MotherRegistrarsController@restore')->name('mother-registrars.restore'); 
});