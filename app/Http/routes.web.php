<?php

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



//Vuejs Route
Route::get('/', function () {
    return view('layouts.app');
});

// Route for Deploy
//if (file_exists(__DIR__.'/controllers/DeployController.php')) {
    // Route::get('/deploy', 'DeployController@testDeploy');
    Route::post('/deploy', 'DeployController@deploy');
    Route::post('/clientslack', 'ClientSlackController@check');
    Route::get('/testDeploy','DeployController@testDeploy');
    
//}

//View storage files/images
Route::get('/storage/uploads/avatars/{image}', 'FileController@showAvatar');
Route::get('{expired_time}/document/{staff_document_id}/download/{file_name}', 'FileController@downloadDocumentSecured');
Route::get('/document/{staff_document_id}/download/{file_name}', 'FileController@downloadDocument');
Route::get('/storage/uploads/staff/documents/pages/{image}', 'FileController@showDocumentPage');
Route::get('/storage/app/public/uploads/staff/documents/pages/{image}', 'FileController@showDocumentPage');
Route::get('/storage/app/public/uploads/client/documents/pages/{image}', 'FileController@showClientDocumentPage');
Route::get('/storage/uploads/client/documents/pages/{image}', 'FileController@showClientDocumentPage');
// m2b-81
Route::get('/storage/app/public/uploads/staff/videos/{video}', 'FileController@showStaffVideo');
Route::get('/storage/app/public/uploads/staff/videos/temp/{video}', 'FileController@showStaffVidTemp');
// m2b-81
//Download a shared notes
Route::get('/sharednotes/{file_name}', 'FileController@downloadNotes');
Route::post('/sendNotification','EmailController@sendNotification');