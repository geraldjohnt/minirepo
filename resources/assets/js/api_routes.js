import {APP_URL} from '../js/config.js';

export const API_PREFIX = `${APP_URL}/api`;

export const USER_RESOURCE = `${API_PREFIX}/user`;
export const USER_AUTHENTICATE = `${USER_RESOURCE}/authenticate`;
export const USER_GET_TOKEN = `${USER_RESOURCE}/token`;
export const USER_FORGOT_PASSWORD = `${USER_RESOURCE}/forgot-password`;
export const USER_RESET_PASSWORD = `${USER_RESOURCE}/reset-password`;
export const USER_EMAIL_FEEDBACK = `${USER_RESOURCE}/email/feedback`;

export const USER_GET_MEMO = `${API_PREFIX}/user/memo`;
export const USER_UPDATE_MEMO = `${API_PREFIX}/user/update/memo`;

export const ADMIN_RESOURCE = `${API_PREFIX}/admin`;
export const ADMIN_MANAGE_STAFF_RESOURCE = `${ADMIN_RESOURCE}/manage-staff`;
export const ADMIN_NOTICES_RESOURCE = `${ADMIN_RESOURCE}/notices`;
export const ADMIN_EMAIL_CREDENTIALS = `${ADMIN_RESOURCE}/email/credentials`;

export const STAFF_RESOURCE = `${API_PREFIX}/staff`;
export const STAFF_CREATE_NOTES = `${API_PREFIX}/staff/create-notes`;
export const STAFF_GET_RESOURCE = `${API_PREFIX}/staff/get-staff`;
export const STAFF_CHECK_PEER_ID = `${API_PREFIX}/staff/check-peer-id`;
export const STAFF_START_MEETING_RESOURCE = `${API_PREFIX}/staff/start-meeting`;
export const STAFF_STOP_MEETING_RESOURCE = `${API_PREFIX}/staff/stop-meeting`;
export const STAFF_DOCUMENTS_RESOURCE = `${STAFF_RESOURCE}/documents`;
export const STAFF_NEGOTATIONS_RESOURCE = `${STAFF_RESOURCE}/negotations`;
export const STAFF_NOTICES_RESOURCE = `${STAFF_RESOURCE}/notices`;
export const STAFF_NOTICES_READ_RESOURCE = `${API_PREFIX}/staff/notices-read`;
export const STAFF_ALL_DOCUMENTS =  `${API_PREFIX}/staff/get-documents`;
export const STAFF_DELETE_FROM_STORAGE =  `${API_PREFIX}/staff/destroy-storage-document`;

// m2b-81
export const STAFF_MERGE_AUDIO_SCREEN_FILES = `${API_PREFIX}/staff/merge-audio-screen-files`;
export const STAFF_CREATE_SCREEN_FILES = `${API_PREFIX}/staff/create-screen-files`;
export const STAFF_CREATE_AUDIO_FILES = `${API_PREFIX}/staff/create-audio-files`;
export const STAFF_GET_AUDIO_SCREEN_NAMES = `${API_PREFIX}/staff/get-audio-screen-names`;
export const STAFF_CHECK_VIDEO = `${API_PREFIX}/staff/check-video-url`;
export const STAFF_DECRYPT_DAT = `${API_PREFIX}/staff/decrypt-dat-url`;
export const STAFF_REMOVE_VIDEO_FILE = `${API_PREFIX}/staff/remove-video-file`;
export const STAFF_FETCH_TOTAL_DISK_USAGE = `${API_PREFIX}/staff/fetch-total-disk-usage`;
// m2b-81

// FOLDERS
export const STAFF_CREATE_FOLDER = `${API_PREFIX}/staff/create-folder`;
export const STAFF_GET_FOLDERS = `${API_PREFIX}/staff/get-folders`;
export const STAFF_GET_FOLDER = `${API_PREFIX}/staff/get-folder`;
export const STAFF_GET_FOLDER_CONTENT = `${API_PREFIX}/staff/get-folder-content`;
export const STAFF_UPDATE_FOLDER = `${API_PREFIX}/staff/update-folder`;
export const STAFF_DELETE_FOLDER = `${API_PREFIX}/staff/destroy-folder`;
export const STAFF_DOC_MOVE = `${API_PREFIX}/staff/move-doc`;

export const CHECK_ERR = `${API_PREFIX}/staff/check-err`;
export const CLIENT_UPLOAD = `${API_PREFIX}/client/upload-document`;
export const CLIENT_EMPTY_DOCUMENTS = `${API_PREFIX}/client/empty-document`;

export const user_service = {
	USER_RESOURCE,
	USER_AUTHENTICATE,
	USER_GET_TOKEN,
	USER_FORGOT_PASSWORD,
	USER_RESET_PASSWORD,
	USER_GET_MEMO,
	USER_UPDATE_MEMO
}

export const staff_service = {
	STAFF_RESOURCE,
	STAFF_CREATE_NOTES,
	STAFF_GET_RESOURCE,
	STAFF_CHECK_PEER_ID,
	STAFF_START_MEETING_RESOURCE,
	STAFF_STOP_MEETING_RESOURCE,
	STAFF_DOCUMENTS_RESOURCE,
	STAFF_NEGOTATIONS_RESOURCE,
	STAFF_NOTICES_RESOURCE,
	STAFF_CREATE_FOLDER,
	STAFF_GET_FOLDERS,
	STAFF_GET_FOLDER,
	STAFF_UPDATE_FOLDER,
	STAFF_DELETE_FOLDER,
	STAFF_GET_FOLDER_CONTENT,
	STAFF_DOC_MOVE,
	STAFF_ALL_DOCUMENTS,
	STAFF_NOTICES_READ_RESOURCE,
	CHECK_ERR,
	STAFF_DELETE_FROM_STORAGE,
	STAFF_MERGE_AUDIO_SCREEN_FILES,
	STAFF_CREATE_SCREEN_FILES,
	STAFF_CREATE_AUDIO_FILES,
	STAFF_GET_AUDIO_SCREEN_NAMES,
	STAFF_CHECK_VIDEO,
	STAFF_DECRYPT_DAT,
	STAFF_REMOVE_VIDEO_FILE,
	STAFF_FETCH_TOTAL_DISK_USAGE		
}
