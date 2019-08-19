<?php  // Simple File List - ee-upload-form.php - mitchellbennis@gmail.com

// Security	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('That is Noncense! (' . basename(__FILE__) . ')' ); // Exit if nonce fails
$eeSFL_UploadNonce = wp_create_nonce('ee-simple-file-list-upload'); // Checked in the upload engine.

$eeSFL_Log[] = 'Loaded: ee-uploader';

// Extension Check
if($eeSFLF) {
	if(!@$eeSFLF_ListFolder) { // If not already set up
		$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
		include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_PathSetup.php'); // Run Setup
	}
}

$eeSFL_Log[] = 'Uploading to...';
$eeSFL_Log[] = $eeSFL_Config['FileListDir'];

// Check for an upload job, then run notification routine.
if(@$_POST['eeSFL_Upload']) {
	
	$eeSFL_ID = filter_var(@$_POST['eeSFL_ID'], FILTER_VALIDATE_INT);
	
	if($eeSFL_ID OR $eeSFL_ID === 0) {
		$eeOutput .= eeSFL_ProcessUpload($eeSFL_ID);
	}
	
	if(!$eeAdmin) {
		eeSFL_UploadCompleted(); // Action Hook: eeSFL_UploadCompleted
	}	
}




// File limit fallback
if(!$eeSFL_Config['UploadLimit']) { $eeSFL_Config['UploadLimit'] = $eeSFL->eeDefaultUploadLimit; }

// User Messaging	
if(@$eeSFL_Log['messages']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['messages'], 'updated'); // Add to the output
	$eeSFL_Log['messages'] = ''; // Clear
}	
if(@$eeSFL_Log['errors']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['errors'], 'error'); // Add to the output
	$eeSFL_Log['errors'] = ''; // Clear
}
	


if(@$eeSFL_Config['FileListDir']) {
	
	$eeOutput .= '
	
	<!-- Simple File List Uploader -->
			
		<form action="" method="POST" enctype="multipart/form-data" name="eeSFL_UploadForm" id="eeSFL_UploadForm">
		
			<input type="hidden" name="MAX_FILE_SIZE" value="' .(($eeSFL_Config['UploadMaxFileSize']*1024)*1024) . '" />
			<input type="hidden" name="eeSFL_Upload" value="TRUE" />
			<input type="hidden" name="eeSFL_ID" value="' . $eeSFL_Config['ID'] . '" id="eeSFL_ID" />
			<input type="hidden" name="eeSFL_FileCount" value="" id="eeSFL_FileCount" />
			<input type="hidden" name="eeSFL_FileList" value="" id="eeSFL_FileList" />';
		
		if($eeSFLF) { $eeOutput .= '
			<input type="hidden" name="eeSFLF_UploadFolder" value="' . urlencode($eeSFLF_ListFolder) . '" id="eeSFLF_UploadFolder" />';
		}
		if($eeAdmin) {
			$eeOutput .= '
			<a href="?page=ee-simple-file-list&tab=list_settings&subtab=uploader_settings" class="button eeRight">' . __('Upload Settings', 'ee-simple-file-list') . '</a>';
		}
		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-upload', 'ee-simple-file-list-upload-nonce', TRUE, FALSE);
	
		$eeOutput .= '
		
		<h2>' . __('Upload Files', 'ee-simple-file-list') . '</h2>
		
		<div id="eeSFL_FileDropZone" ondrop="eeSFL_DropHandler(event);" ondragover="eeSFL_DragOverHandler(event);">';
		
		if($eeSFL_Config['GetUploaderInfo'] == 'YES' AND !$eeAdmin) { $eeOutput .= $eeSFL->eeSFL_UploadInfoForm(); }
		
		$eeSFL_FileFormats = str_replace(' ' , '', $eeSFL_Config['FileFormats']); // Strip spaces
		
		// Security
		$eeSFL_Timestamp = time();
		$eeSFL_TimestampMD5 = md5('unique_salt' . $eeSFL_Timestamp);
	    
		$eeOutput .= '<input type="file" name="eeSFL_FileInput" id="eeSFL_FileInput" onchange="eeSFL_FileInputHandler(event)" multiple />
		
		<br class="eeClearFix" />
		
		<script type="text/javascript">
		
			var eeSFL_ListID = ' . $eeSFL_Config['ID'] . ';
			var eeSFL_FileListDir = "' . $eeSFL_Config['FileListDir'] . '";
			var eeSFL_FileLimit = ' . $eeSFL_Config['UploadLimit'] . '; // Maximum number of files allowed
			var eeSFL_UploadMaxFileSize = ' . (($eeSFL_Config['UploadMaxFileSize']*1024)*1024) . ';
			var eeSFL_FileFormats = "' . $eeSFL_Config['FileFormats'] . '"; // Allowed file extensions
			var eeSFL_TimeStamp = "' . $eeSFL_Timestamp . '"; // Security
			var eeSFL_TimeStampMD5 = "' . $eeSFL_TimestampMD5 . '"; // Security
			var eeSFL_UploadEngineURL = "' . $eeSFL_Env['pluginURL'] . '/ee-upload-engine.php";
			
		</script>
		
		<script src="' . $eeSFL_Env['pluginURL'] . '/js/ee-uploader.js?v=' . eeSFL_Cache_Version . '"></script>
		
		<span id="eeSFL_UploadProgress"><em>' . __('Processing the Upload', 'ee-simple-file-list') . '</em></span>
		
		<button type="button" name="eeSFL_UploadGo" id="eeSFL_UploadGo" onclick="eeUploadProcessor(eeSFL_FileObjects);">' . __('Upload', 'ee-simple-file-list') . '</button>
		
		<p class="sfl_instuctions">' . __('File Limit', 'ee-simple-file-list') . ': ' . $eeSFL_Config['UploadLimit'] . ' ' . __('files', 'ee-simple-file-list') . '<br />
		
		' . __('Size Limit', 'ee-simple-file-list') . ': ' . $eeSFL_Config['UploadMaxFileSize'] . ' MB
		
		' . __('per file', 'ee-simple-file-list') . '.<br />' . __('Drag-and-drop files here or use the Browse button.', 'ee-simple-file-list') . '</p>
		
		<br class="eeClearFix" />
		
		</div>
	
	</form>';
	
	
} else {
	$eeOutput .= __('No upload directory configured.', 'ee-simple-file-list');
	$eeSFL_Log['errors'] = 'No upload directory configured.';
}