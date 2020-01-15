<?php

/*
|--------------------------------------------------------------------------
| API Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function($api) {
	
	$api->post('user/authenticate', 'App\Http\Controllers\Auth\AuthController@authenticateAdminOrStaff');
	$api->get('user/forgot-password', 'App\Http\Controllers\Auth\PasswordController@forgotPass');
	$api->post('user/reset-password', 'App\Http\Controllers\Auth\PasswordController@resetPass');

	$api->post('admin/authenticate', 'App\Http\Controllers\Auth\AuthController@authenticateAdmin');
	$api->post('staff/authenticate', 'App\Http\Controllers\Auth\AuthController@authenticateStaff');

	$api->post('client/upload-document', 'App\Http\Controllers\ClientDocumentController@uploadDocument');
	$api->post('client/empty-document', 'App\Http\Controllers\ClientDocumentController@removeDocuments');
});

$api->version('v1', ['middleware' => 'api.auth'], function ($api) {

	$api->get('user', 'App\Http\Controllers\Auth\AuthController@show');
	$api->get('user/token', 'App\Http\Controllers\Auth\AuthController@getToken');
	$api->post('user/email/feedback', 'App\Http\Controllers\EmailController@sendFeedback');
	/**
     * Memo Controller
     */
	$api->get('user/memo', 'App\Http\Controllers\UserMemoController@getMemo');
	$api->post('user/update/memo', 'App\Http\Controllers\UserMemoController@updateMemo');
	/**
     * Admin API
     */
	$api->group(['middleware' => 'auth.admin'], function ($api) {

		$api->get('users', 'App\Http\Controllers\Auth\AuthController@index');
		
		$api->post('admin', 'App\Http\Controllers\AdminController@_update');
		$api->post('admin/email/credentials', 'App\Http\Controllers\EmailController@sendCredentials');

		$api->get('admin/staff/getlogin','App\Http\Controllers\AdminManageStaffController@getLogin');
		$api->get('admin/staff/setStatus','App\Http\Controllers\AdminManageStaffController@setStatus');
                $api->get('admin/staff/getProcess','App\Http\Controllers\AdminManageStaffController@getProcess');


		$api->resource('admin/manage-staff', 'App\Http\Controllers\AdminManageStaffController',
			['only' => ['index', 'show', 'store', 'update', 'destroy']]
		);
		$api->resource('admin/notices', 'App\Http\Controllers\NoticeController',
			['only' => ['index', 'show', 'store', 'update', 'destroy']]
		);
	});

	/**
     * Staff API
     */
    
	$api->group(['middleware' => 'auth.staff'], function ($api) {

		$api->post('staff', 'App\Http\Controllers\StaffController@_update');
		
		$api->get('staff/get-staff', 'App\Http\Controllers\StaffController@getStaff');
		$api->get('staff/check-peer-id', 'App\Http\Controllers\StaffController@checkPeerId');
		$api->get('staff/start-meeting', 'App\Http\Controllers\StaffController@startMeeting');
		$api->get('staff/stop-meeting', 'App\Http\Controllers\StaffController@stopMeeting');
		
		$api->post('staff/create-notes', 'App\Http\Controllers\StaffController@createNotes');

		// m2b-81
		$api->post('staff/merge-audio-screen-files', 'App\Http\Controllers\StaffController@mergeAudioScreenFiles');
		$api->post('staff/create-screen-files', 'App\Http\Controllers\StaffController@createScreenFiles');
		$api->post('staff/create-audio-files', 'App\Http\Controllers\StaffController@createAudioFiles');
		$api->post('staff/get-audio-screen-names', 'App\Http\Controllers\StaffController@getAudioScreenNames');
		$api->post('staff/check-video-url', 'App\Http\Controllers\StaffController@checkVideoUrl');	
		$api->post('staff/decrypt-dat-url', 'App\Http\Controllers\StaffController@decryptDatUrl');	
		$api->post('staff/remove-video-file', 'App\Http\Controllers\StaffController@removeVideoFile');	
		$api->post('staff/fetch-total-disk-usage', 'App\Http\Controllers\StaffController@fetchTotalDiskUsage');	
		// m2b-81			

		// Folders
		$api->get('staff/get-folders', 'App\Http\Controllers\StaffFolderController@retrieve');
		$api->post('staff/get-folder', 'App\Http\Controllers\StaffFolderController@getFolder');
		$api->post('staff/create-folder', 'App\Http\Controllers\StaffFolderController@store');
		$api->post('staff/update-folder', 'App\Http\Controllers\StaffFolderController@update');
		$api->post('staff/destroy-folder', 'App\Http\Controllers\StaffFolderController@destroy');

		$api->resource('staff/documents', 'App\Http\Controllers\StaffDocumentController',
			['only' => ['index', 'show', 'store', 'update', 'destroy']]
		);
		$api->post('staff/destroy-storage-document', 'App\Http\Controllers\FileController@deleteFromStorage');
		
		$api->get('staff/get-documents', 'App\Http\Controllers\StaffDocumentController@getAllDocuments');
		$api->get('staff/get-folder-content', 'App\Http\Controllers\StaffDocumentController@showFolderContent');
		$api->post('staff/move-doc', 'App\Http\Controllers\StaffFolderController@moveDocument');

		$api->resource('staff/negotations', 'App\Http\Controllers\StaffNegotationController',
			['only' => ['index', 'show', 'store', 'update']]
		);
		
		$api->resource('staff/notices', 'App\Http\Controllers\StaffNoticeController',
			['only' => ['index', 'show']]
		);
		$api->post('staff/notices-read', 'App\Http\Controllers\StaffNoticeReadController@update');
		$api->post('staff/check-err', 'App\Http\Controllers\StaffNoticeReadController@check');
	});
});