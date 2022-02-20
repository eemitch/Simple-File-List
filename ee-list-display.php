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

// Save for later
$eeSFL_FileTotalCount = 0;
$eeSFL_ItemTotalCount = $eeSFL_FileTotalCount; //  + $eeSFL_FolderTotalCount
$eeSFL_FileTotalCount = count($eeSFL_Files,0);


// Check for Upload Job
if(count($eeSFL_BASE_Env['UploadedFiles'])) {
	
	foreach( $eeSFL_Files as $eeThisKey => $eeFileArray ) {
		
		if( in_array($eeFileArray['FilePath'], $eeSFL_BASE_Env['UploadedFiles']) ) {
			$eeUploadedFiles[] = $eeFileArray;
		}
	}
	
	if(count($eeUploadedFiles)) {
		
		// Show Only File(s) Just Uploaded
		if($eeSFL_Settings['UploadConfirm'] == 'YES') { $eeSFL_Files = $eeUploadedFiles; }
		
	} else {
		$eeSFL_Files = array();
		$eeSFL_BASE_Log['errors'][] = 'Upload Processing Error.';
		$eeSFL_BASE_Log['errors'][] = $eeSFL_BASE_Env['UploadedFiles'];
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
if($eeSFL_Files == $eeUploadedFiles AND $eeSFL_BASE_ListRun == 1) {
	
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

<p class="eeHide"><span id="eeSFL_FilesCount">' . $eeFileCount . '</span></p>

</div>

<!-- END #eeSFL -->'; 
	
$eeSFL_BASE_Env['FileLists'] = ''; // Remove to clean up display

?>