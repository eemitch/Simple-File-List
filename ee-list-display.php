<?php // Simple File List Script: ee-list-display.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeFileArray = FALSE;
$eeSFL_ListClass = 'eeSFL'; // The basic list's CSS class. Extensions might change this.
$eeClass = ''; // Meaning, CSS class
$eeSFL_AllowFrontManage = 'NO'; // Front-side freedom
$eeUploadedFiles = FALSE;
$eeSFL_ActionNonce = wp_create_nonce('eeSFL_ActionNonce'); // Security for Ajax
$eeURL = eeSFL_BASE_GetThisURL();
global $eeSFL_BASE_ListRun;

$eeSFL_BASE_Log['RunTime'][] = 'Loaded: ee-list-display';

if( !isset($eeSFL_Files) ) { // Scan the Disk
	$eeSFL_Files = $eeSFL_BASE->eeSFL_UpdateFileListArray();
}

// Save for later
$eeSFL_FileTotalCount = 0;
$eeSFL_FileTotalCount = count($eeSFL_Files, 0);

// Check for Upload Job
if( isset($_POST['eeSFL_Upload']) ) {
	
	foreach( $eeSFL_Files as $eeThisKey => $eeFileArray ) {
		
		if( in_array($eeFileArray['FilePath'], $eeSFL_BASE_Env['UploadedFiles']) ) {
			$eeUploadedFiles[] = $eeFileArray;
		}
	}

	if($eeSFL_Settings['UploadConfirm'] == 'YES') {
		
		// Show Only File(s) Just Uploaded
		$eeSFL_Files = $eeUploadedFiles;
		
	} else {
		
		// Refresh
		$eeSFL_Files = $eeSFL_BASE->eeSFL_UpdateFileListArray();
		$eeSFL_FileTotalCount = count($eeSFL_Files, 0);
	}
	
	if(count($eeUploadedFiles) < 1) {
		
		$eeSFL_Files = array();
		$eeSFL_BASE_Log['errors'][] = __('Upload Processing Error. Files may not have been added properly.', 'ee-simple-file-list');
	}	
}

// User Messaging
if(isset($eeSFL_BASE_Log['messages'])) {	
	if($eeSFL_BASE_Log['messages'] AND $eeSFL_BASE_ListRun == 1) { 
		$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeSFL_BASE_Log['messages'], 'notice-success');
		$eeSFL_BASE_Log['messages'] = array(); // Clear
	}
}
if(isset($eeSFL_BASE_Log['errors'])) {		
	if($eeSFL_BASE_Log['errors']) { 
		$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeSFL_BASE_Log['errors'], 'notice-error');
		$eeSFL_BASE_Log['errors'] = array(); // Clear
	}
}


// DISPLAY ===================================================

$eeOutput .= '

<!-- File List -->

<div class="eeSFL" id="eeSFL">

<span class="eeHide" id="eeSFL_ActionNonce">' . $eeSFL_ActionNonce . '</span>

<script>
	var eeSFL_PluginURL = "' . $eeSFL_BASE_Env['pluginURL'] . '";
	var eeSFL_FileListDir = "' . $eeSFL_Settings['FileListDir'] . '";
</script>
';

// Upload Confirmation
if(!$eeAdmin AND $eeUploadedFiles AND $eeSFL_Settings['UploadConfirm'] == 'YES' AND $eeSFL_BASE_ListRun == 1) {
	
	$eeOutput .= '
	
	<p><a href="' . eeSFL_BASE_AppendProperUrlOp($eeURL) . 'ee=1" class="button eeButton" id="eeSFL_BacktoFilesButton">&larr; ' . 
		__('Back to the Files', 'ee-simple-file-list') . '</a></p>
		
	';
}

// $eeSFL_Files = array_values($eeSFL_Files);

// echo '<pre>'; print_r($eeSFL_Files); echo '</pre>';

if( is_array($eeSFL_Files) ) {
	
	if(!count($eeSFL_Files)) { return; } // Bail if no files

	if($eeSFL_Settings['ShowListStyle'] == 'Tiles') {
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-list-display-tiles.php');
		
	} elseif($eeSFL_Settings['ShowListStyle'] == 'Flex') {
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-list-display-flex.php');
		
	} else {
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-list-display-table.php');
		
	}

} else {
	
	$eeSFL_BASE_Log['RunTime'][] = 'There are no files here :-(';
	
	if($eeAdmin) {
		$eeOutput .= '<div>
		
		<p>&#8593; ' . __('Upload some files and they will appear here.', 'ee-simple-file-list') . '</p>
		
		</div>
		
		';
	}
}

// This allows javascript to access the count
$eeOutput .= '

<p class="eeHide"><span id="eeSFL_FilesCount">' . $eeSFL_BASE->eeFileCount . '</span></p>

</div><!-- END .eeSFL -->';

// Modal Input
if($eeAdmin OR $eeSFL_Settings['AllowFrontManage'] == 'YES') {
							
	$eeOutput .= '
	
	<div class="eeSFL_Modal" id="eeSFL_Modal_Manage">
	<div class="eeSFL_ModalBackground"></div>
	<div class="eeSFL_ModalBody">
	
		<button id="eeSFL_Modal_Manage_Close" class="eeSFL_ModalClose">&times;</button>
		
		<h1>' . __('Edit File', 'ee-simple-file-list') . '</h1>
		
		<p class="eeSFL_ModalFileDetails">File ID: <span class="eeSFL_Modal_Manage_FileID">???</span> | ' . 
		__('Added', 'ee-simple-file-list') . ': <span id="eeSFL_FileDateAdded" >???</span> | ' . 
		__('Changed', 'ee-simple-file-list') . ': <span id="eeSFL_FileDateChanged" >???</span> | ' . 
		__('Size', 'ee-simple-file-list') . ': <span id="eeSFL_FileSize">???</span>
		</p>
		
		<label for="eeSFL_FileNameNew">' . __('File Name', 'ee-simple-file-list') . '</label>
		<input type="text" id="eeSFL_FileNameNew" name="eeSFL_FileNameNew" value="??" size="64" />
		<small class="eeSFL_ModalNote">' . __('Change the name.', 'ee-simple-file-list') . ' ' . __('Some characters are not allowed. These will be automatically replaced.', 'ee-simple-file-list') . '</small>
		
		<label for="eeSFL_FileNiceNameNew">' . __('File Nice Name', 'ee-simple-file-list') . '</label>
		<input type="text" id="eeSFL_FileNiceNameNew" name="eeSFL_FileNiceNameNew" value="" size="64" />
		<small class="eeSFL_ModalNote">' . __('Enter a name that will be shown in place of the real file name.', 'ee-simple-file-list') . ' ' . __('You may use special characters not allowed in the file name.', 'ee-simple-file-list') . '</small>
		
		<label for="eeSFL_FileDescriptionNew">' . __('Description', 'ee-simple-file-list') . '</label>
		<textarea cols="64" rows="3" id="eeSFL_FileDescriptionNew" name="eeSFL_FileDescriptionNew"></textarea>
		<small class="eeSFL_ModalNote">' . __('Add a description.', 'ee-simple-file-list') . ' ' . __('Use this field to describe this file and apply keywords for searching.', 'ee-simple-file-list') . '</small>
		
		<button class="button" onclick="eeSFL_BASE_FileEditSaved()">' . __('Save', 'ee-simple-file-list') . '</button>

	</div>
	</div>';
}
	
$eeSFL_BASE_Env['FileLists'] = ''; // Remove to clean up display

?>