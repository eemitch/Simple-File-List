<?php  // Simple File List Script: ee-upload-form.php | Author: Mitchell Bennis | support@simplefilelist.com

// Security	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails


// Check for an upload job, then run notification routine.
if(isset($_POST['eeSFL_Upload'])) {
	
	$eeSFL_BASE_Log['RunTime'][] = 'Processing Upload Job...';
	
	$eeSFL_Uploaded = TRUE; // Show the results page
	
	eeSFL_BASE_ProcessUpload();
	
	if($eeAdmin) {
		eeSFL_BASE_UploadCompletedAdmin(); // Action Hook: eeSFL_UploadCompletedAdmin  <-- Admin side
	} else {
		eeSFL_BASE_UploadCompleted(); // Action Hook: eeSFL_UploadCompleted <-- Front side
	}
	
} else {
	$eeSFL_Uploaded = FALSE; // Show the regular list
}

// Upload Form --------------------

global $eeSFL_BASE_ListRun;
$eeSFL_UploadNonce = wp_create_nonce('ee-simple-file-list-upload'); // Checked in the upload engine.

$eeSFL_BASE_Log['RunTime'][] = 'Loaded: ee-uploader';
$eeSFL_BASE_Log['RunTime'][] = 'Uploading to...';
$eeSFL_BASE_Log['RunTime'][] = $eeSFL_Settings['FileListDir'];

// File limit fallback
if(!$eeSFL_Settings['UploadLimit']) { $eeSFL_Settings['UploadLimit'] = $eeSFL_BASE->eeDefaultUploadLimit; }

/*
echo '<pre>'; print_r($eeSFL_BASE_Log); echo '</pre>'; exit;

// User Messaging	
if(count($eeSFL_BASE_Log['messages']) AND $eeSFL_BASE_ListRun == 1) { 
	$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeSFL_BASE_Log['messages'], 'notice-success');
	$eeSFL_BASE_Log['messages'] = ''; // Clear
}	
if(count($eeSFL_BASE_Log['errors'])) { 
	$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeSFL_BASE_Log['errors'], 'notice-error');
	$eeSFL_BASE_Log['errors'] = ''; // Clear
}
*/
	

if(strlen($eeSFL_Settings['FileListDir']) > 1) {
	
	$eeOutput .= '
	
	<!-- Simple File List Uploader -->
			
		<form action="' . eeSFL_BASE_GetThisURL() . '" method="POST" enctype="multipart/form-data" name="eeSFL_UploadForm" id="eeSFL_UploadForm">
		
			<input type="hidden" name="MAX_FILE_SIZE" value="' .(($eeSFL_Settings['UploadMaxFileSize']*1024)*1024) . '" />
			<input type="hidden" name="ee" value="1" />
			<input type="hidden" name="eeSFL_Upload" value="TRUE" />
			<input type="hidden" name="eeSFL_FileCount" value="" id="eeSFL_FileCount" />
			<input type="hidden" name="eeSFL_FileList" value="" id="eeSFL_FileList" />';
		
		if($eeSFL_BASE_Env['wpUserID'] > 0) { $eeOutput .= '<input type="hidden" name="eeSFL_FileOwner" value="' . $eeSFL_BASE_Env['wpUserID'] . '" id="eeSFL_FileOwner" />'; }

		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-upload', 'ee-simple-file-list-upload-nonce', TRUE, FALSE);
	
		$eeOutput .= '
		
		<h2 class="eeSFL_UploadFilesTitle">' . __('Upload Files', 'ee-simple-file-list') . '</h2>
		
		<div class="eeClearFix" id="eeSFL_FileDropZone" ondrop="eeSFL_BASE_DropHandler(event);" ondragover="eeSFL_BASE_DragOverHandler(event);">';
		
		if($eeSFL_Settings['GetUploaderInfo'] == 'YES' OR $eeAdmin) { 
			
			$eeName = ''; $eeEmail = '';
		
			$wpUserObj = wp_get_current_user();
			
			if($wpUserObj) {
				$eeName = $wpUserObj->first_name . ' ' . $wpUserObj->last_name;
				$eeEmail = $wpUserObj->user_email;
			}
			
			$eeOutput .= '
			
			<div id="eeUploadInfoForm" class="eeClearFix">';
				
			if(!$eeEmail) {
				
				$eeOutput .= '
				
				<label for="eeSFL_Name">' . __('Name', 'ee-simple-file-list') . ':</label>
				<input type="text" name="eeSFL_Name" value="" id="eeSFL_Name" size="64" maxlength="64" /> 
				
				<label for="eeSFL_Email">' . __('Email', 'ee-simple-file-list') . ':</label>
				<input type="text" name="eeSFL_Email" value="" id="eeSFL_Email" size="64" maxlength="128" />
				
				'; }
				
				$eeOutput .= '<label for="eeSFL_Comments">' . __('Description', 'ee-simple-file-list') . '</label>';
				
				$eeOutput .= '<textarea placeholder="' . __('Add a description (optional)', 'ee-simple-file-list') . '" name="eeSFL_Comments" id="eeSFL_Comments" rows="5" cols="64" maxlength="5012"></textarea>';
				
				if($eeEmail AND !$eeAdmin) { $eeOutput .= '<p>' . __('Submitter:', 'ee-simple-file-list') . ' ' . $eeName . ' (' . $eeEmail . ')</p>'; }
				
				if($eeEmail) {
					
					$eeOutput .= '
					
					<input type="hidden" id="eeSFL_Name" name="eeSFL_Name" value="' . $eeName . '" />
					
					<input type="hidden" id="eeSFL_Email" name="eeSFL_Email" value="' . $eeEmail . '" />';
				}
				
				$eeOutput .= '</div>';	
			
			
		}
		
		$eeSFL_FileFormats = str_replace(' ' , '', $eeSFL_Settings['FileFormats']); // Strip spaces
	    
	    $eeOutput .= '<input type="file" name="eeSFL_FileInput" id="eeSFL_FileInput" onchange="eeSFL_BASE_FileInputHandler(event)" multiple />
		
		<p id="eeSFL_FilesDrug"></p>
		
		<script>
		
			var eeSFL_FileUploadDir = "' . urlencode($eeSFL_Settings['FileListDir']) . '";
			var eeSFL_FileLimit = ' . $eeSFL_Settings['UploadLimit'] . '; // Maximum number of files allowed
			var eeSFL_UploadMaxFileSize = ' . (($eeSFL_Settings['UploadMaxFileSize']*1024)*1024) . ';
			var eeSFL_FileFormats = "' . str_replace(' ' , '', $eeSFL_Settings['FileFormats']) . '"; // Allowed file extensions
			var eeSFL_Nonce = "' . $eeSFL_UploadNonce . '"; // Security
			var eeSFL_UploadEngineURL = "' . admin_url( 'admin-ajax.php') . '";
			
		</script>
		
		<span id="eeSFL_UploadProgress"><em>' . __('Processing the Upload', 'ee-simple-file-list') . '</em></span>
		
		<input class="button" type="reset" value="Clear All" /><button type="button" class="button" name="eeSFL_UploadGo" id="eeSFL_UploadGo" onclick="eeSFL_BASE_UploadProcessor(eeSFL_FileObjects);">' . __('Upload', 'ee-simple-file-list') . '</button>';
		
		if($eeSFL_Settings['ShowUploadLimits'] == 'YES') {
		
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
	$eeSFL_BASE_Log['errors'][] = 'No upload directory configured.';
}

?>