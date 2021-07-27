<?php  // Simple File List Script: ee-upload-form.php | Author: Mitchell Bennis | support@simplefilelist.com

// Security	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_UploadNonce = wp_create_nonce('ee-simple-file-list-upload'); // Checked in the upload engine.
global $eeSFL_FREE_ListRun;
$eeSFL_FREE_Log['RunTime'][] = 'Loaded: ee-uploader';
$eeSFL_FREE_Log['RunTime'][] = 'Uploading to...';
$eeSFL_FREE_Log['RunTime'][] = $eeSFL_Settings['FileListDir'];

// File limit fallback
if(!$eeSFL_Settings['UploadLimit']) { $eeSFL_Settings['UploadLimit'] = $eeSFL_FREE->eeDefaultUploadLimit; }

// User Messaging	
if(@$eeSFL_FREE_Log['messages'] AND $eeSFL_FREE_ListRun == 1) { 
	$eeOutput .=  eeSFL_FREE_ResultsDisplay($eeSFL_FREE_Log['messages'], 'notice-success');
	$eeSFL_FREE_Log['messages'] = ''; // Clear
}	
if(@$eeSFL_FREE_Log['errors']) { 
	$eeOutput .=  eeSFL_FREE_ResultsDisplay($eeSFL_FREE_Log['errors'], 'notice-error');
	$eeSFL_FREE_Log['errors'] = ''; // Clear
}
	

if(strlen($eeSFL_Settings['FileListDir']) > 1) {
	
	$eeOutput .= '
	
	<!-- Simple File List Uploader -->
			
		<form action="' . eeSFL_FREE_GetThisURL() . '" method="POST" enctype="multipart/form-data" name="eeSFL_UploadForm" id="eeSFL_UploadForm">
		
			<input type="hidden" name="MAX_FILE_SIZE" value="' .(($eeSFL_Settings['UploadMaxFileSize']*1024)*1024) . '" />
			<input type="hidden" name="ee" value="1" />
			<input type="hidden" name="eeSFL_Upload" value="TRUE" />
			<input type="hidden" name="eeSFL_FileCount" value="" id="eeSFL_FileCount" />
			<input type="hidden" name="eeSFL_FileList" value="" id="eeSFL_FileList" />';
		
		if($eeSFL_FREE_Env['wpUserID'] > 0) { $eeOutput .= '<input type="hidden" name="eeSFL_FileOwner" value="' . $eeSFL_FREE_Env['wpUserID'] . '" id="eeSFL_FileOwner" />'; }

		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-upload', 'ee-simple-file-list-upload-nonce', TRUE, FALSE);
	
		$eeOutput .= '
		
		<h2 class="eeSFL_UploadFilesTitle">' . __('Upload Files', 'ee-simple-file-list') . '</h2>
		
		<div id="eeSFL_FileDropZone" ondrop="eeSFL_FREE_DropHandler(event);" ondragover="eeSFL_FREE_DragOverHandler(event);">';
		
		if($eeSFL_Settings['GetUploaderInfo'] == 'YES' OR $eeAdmin) { $eeOutput .= $eeSFL_FREE->eeSFL_UploadInfoForm(); }
		
		$eeSFL_FileFormats = str_replace(' ' , '', $eeSFL_Settings['FileFormats']); // Strip spaces
	    
	    $eeOutput .= '<input type="file" name="eeSFL_FileInput" id="eeSFL_FileInput" onchange="eeSFL_FREE_FileInputHandler(event)" multiple />
		
		<p id="eeSFL_FilesDrug" class="eeHide"></p>
		
		<br class="eeClearFix" />
		
		<script>
		
			var eeSFL_FileUploadDir = "' . urlencode($eeSFL_Settings['FileListDir']) . '";
			var eeSFL_FileLimit = ' . $eeSFL_Settings['UploadLimit'] . '; // Maximum number of files allowed
			var eeSFL_UploadMaxFileSize = ' . (($eeSFL_Settings['UploadMaxFileSize']*1024)*1024) . ';
			var eeSFL_FileFormats = "' . str_replace(' ' , '', $eeSFL_Settings['FileFormats']) . '"; // Allowed file extensions
			var eeSFL_Nonce = "' . $eeSFL_UploadNonce . '"; // Security
			var eeSFL_UploadEngineURL = "' . admin_url( 'admin-ajax.php') . '";
			
		</script>
		
		<span id="eeSFL_UploadProgress"><em>' . __('Processing the Upload', 'ee-simple-file-list') . '</em></span>
		
		<button type="button" class="button eeButton" name="eeSFL_UploadGo" id="eeSFL_UploadGo" onclick="eeSFL_FREE_UploadProcessor(eeSFL_FileObjects);">' . __('Upload', 'ee-simple-file-list') . '</button>';
		
		if($eeSFL_Settings['ShowUploadLimits'] == 'YES' OR $eeAdmin) {
		
			$eeOutput .= '<p class="sfl_instuctions">' . __('File Limit', 'ee-simple-file-list') . ': ' . $eeSFL_Settings['UploadLimit'] . ' ' . __('files', 'ee-simple-file-list') . '<br />
			
			' . __('Size Limit', 'ee-simple-file-list') . ': ' . $eeSFL_Settings['UploadMaxFileSize'] . ' MB
			
			' . __('per file', 'ee-simple-file-list') . '.<br />
			
			' . __('Types Allowed', 'ee-simple-file-list') . ': ' . str_replace(',', ', ', $eeSFL_Settings['FileFormats'])  . '<br />
			
			' . __('Drag-and-drop files here or use the Browse button.', 'ee-simple-file-list') . '</p>';
		
		} else {
			
			$eeOutput .= '
		
			<br class="eeClearFix" />';
		}
		
		$eeOutput .= '
		
		</div>
	
	</form>';
	
	
} else {
	$eeOutput .= __('No upload directory configured.', 'ee-simple-file-list');
	$eeSFL_FREE_Log['errors'][] = 'No upload directory configured.';
}

?>