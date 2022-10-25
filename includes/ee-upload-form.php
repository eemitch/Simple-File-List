<?php  
// Simple File List Pro - Copyright 2022
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// License: EULA | https://simplefilelist.com/end-user-license-agreement/
// All changes to, modifications to, or re-uses of this script are prohibited without prior consent.

// Security	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

// Upload Form --------------------

$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Loading Upload Form ...';

if(isset($_REQUEST['eeFolder']) AND $eeSFL_BASE->eeListRun == 1) { // Adjust the path based on REQUEST arg
	$eeSFL_BASE->eeCurrentFolder = sanitize_text_field(urldecode($_REQUEST['eeFolder'])) . '/'; 
} elseif( strlen(@$eeSFL_BASE->eeShortcodeFolder) ) {
	$eeSFL_BASE->eeCurrentFolder = str_replace('&#34;', '', $eeSFL_BASE->eeShortcodeFolder) . '/'; // Fix for uploading to draft status page
} else {
	$eeSFL_BASE->eeCurrentFolder = FALSE;
}

// User Messaging
$eeOutput .= eeSFL_BASE_UserMessaging();
	
$eeOutput .= '

<!-- Simple File List Uploader -->
		
<form action="' . eeSFL_BASE_GetThisURL() . '" method="POST" enctype="multipart/form-data" name="eeSFL_UploadForm" id="eeSFL_UploadForm">

<input type="hidden" name="MAX_FILE_SIZE" value="' .(($eeSFL_BASE->eeListSettings['UploadMaxFileSize']*1024)*1024) . '" />
<input type="hidden" name="ee" value="1" />
<input type="hidden" name="eeSFL_Upload" value="TRUE" />
<input type="hidden" name="eeSFL_FileCount" value="" id="eeSFL_FileCount" />
<input type="hidden" name="eeSFL_FileList" value="" id="eeSFL_FileList" />';

if($eeSFL_BASE->eeEnvironment['wpUserID'] > 0) { $eeOutput .= '
<input type="hidden" name="eeSFL_FileOwner" value="' . $eeSFL_BASE->eeEnvironment['wpUserID'] . '" id="eeSFL_FileOwner" />'; }

if($eeSFL_BASE->eeCurrentFolder) { $eeOutput .= '
<input type="hidden" name="eeSFL_UploadFolder" value="' . urlencode($eeSFL_BASE->eeCurrentFolder) . '" id="eeSFL_UploadFolder" />'; }

$eeOutput .= wp_nonce_field( 'ee-simple-file-list-upload-form', 'ee-simple-file-list-upload-form-nonce', TRUE, FALSE);

$eeOutput .= '

<h2 class="eeSFL_UploadFilesTitle">' . __('Upload Files', 'ee-simple-file-list') . '</h2>

<div class="eeClearFix" id="eeSFL_FileDropZone" ondrop="eeSFL_BASE_DropHandler(event);" ondragover="eeSFL_BASE_DragOverHandler(event);">';
	
$eeName = ''; $eeEmail = '';

$wpUserObj = wp_get_current_user();

if($wpUserObj) {
	$eeName = $wpUserObj->first_name . ' ' . $wpUserObj->last_name;
	$eeEmail = $wpUserObj->user_email;
}

$eeOutput .= '

<div id="eeUploadInfoForm" class="eeClearFix">';
	
if(!$eeEmail AND $eeSFL_BASE->eeListSettings['GetUploaderInfo'] == 'YES') {
	
	$eeOutput .= '
	
	<label for="eeSFL_Name">' . __('Name', 'ee-simple-file-list') . ':</label>
	<input type="text" name="eeSFL_Name" value="" id="eeSFL_Name" size="64" maxlength="64" /> 
	
	<label for="eeSFL_Email">' . __('Email', 'ee-simple-file-list') . ':</label>
	<input type="text" name="eeSFL_Email" value="" id="eeSFL_Email" size="64" maxlength="128" />';
	
} else {
	
	$eeOutput .= '
		
	<input type="hidden" id="eeSFL_Name" name="eeSFL_Name" value="' . $eeName . '" />
	<input type="hidden" id="eeSFL_Email" name="eeSFL_Email" value="' . $eeEmail . '" />';
}

if($eeSFL_BASE->eeListSettings['GetUploaderDesc'] == 'YES' OR $eeAdmin) {
	
	$eeOutput .= '<label for="eeSFL_FileDesc">' . __('Description', 'ee-simple-file-list') . '</label>
	
	<textarea placeholder="' . __('Add a description (optional)', 'ee-simple-file-list') . '" name="eeSFL_FileDesc" id="eeSFL_FileDesc" rows="5" cols="64" maxlength="5012"></textarea>';
	
}
	
$eeOutput .= '</div>

<input type="file" name="eeSFL_FileInput" id="eeSFL_FileInput" onchange="eeSFL_BASE_FileInputHandler(event)" multiple />

<p id="eeSFL_FilesDrug"></p>

<script>
		
var eeSFL_FileUploadDir = "' . urlencode($eeSFL_BASE->eeListSettings['FileListDir'] . $eeSFL_BASE->eeCurrentFolder) . '";
var eeSFL_FileLimit = ' . $eeSFL_BASE->eeListSettings['UploadLimit'] . '; // Maximum number of files allowed
var eeSFL_UploadMaxFileSize = ' . (($eeSFL_BASE->eeListSettings['UploadMaxFileSize']*1024)*1024) . ';
var eeSFL_FileFormats = "' . str_replace(' ' , '', $eeSFL_BASE->eeListSettings['FileFormats']) . '"; // Allowed file extensions
var eeSFL_Nonce = "' . wp_create_nonce('ee-simple-file-list-upload') . '"; // Security
var eeSFL_UploadEngineURL = "' . admin_url( 'admin-ajax.php') . '";
			
</script>

<span id="eeSFL_UploadProgress"><em class="eeHide">' . __('Processing the Upload', 'ee-simple-file-list') . '</em></span>

<div id="eeSFL_FileUploadQueue"></div>

<button type="button" class="button" name="eeSFL_UploadGo" id="eeSFL_UploadGo" onclick="eeSFL_BASE_UploadProcessor(eeSFL_FileObjects);">' . __('Upload', 'ee-simple-file-list') . '</button>';

// if($eeEmail AND !$eeAdmin) { $eeOutput .= '<p>' . __('Submitter:', 'ee-simple-file-list') . ' ' . $eeName . ' (' . $eeEmail . ')</p>'; }

if($eeSFL_BASE->eeListSettings['ShowUploadLimits'] == 'YES') {

	$eeOutput .= '<p class="sfl_instuctions">' . __('File Limit', 'ee-simple-file-list') . ': ' . $eeSFL_BASE->eeListSettings['UploadLimit'] . ' ' . __('files', 'ee-simple-file-list') . '<br />
	
	' . __('Size Limit', 'ee-simple-file-list') . ': ' . $eeSFL_BASE->eeListSettings['UploadMaxFileSize'] . ' MB
	
	' . __('per file', 'ee-simple-file-list') . '.<br />
	
	' . __('Types Allowed', 'ee-simple-file-list') . ': ' . str_replace(',', ', ', $eeSFL_BASE->eeListSettings['FileFormats'])  . '<br />
	
	' . __('Drag-and-drop files here or use the Browse button.', 'ee-simple-file-list') . '</p>';

}

$eeOutput .= '

</div>

</form>';

?>