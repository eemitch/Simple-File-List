<?php 
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

// This is the Main File List Display Page Used Both Front and Back
$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Running File List Display #' . $eeSFL->eeListRun;
$eeSFL_Include = wp_create_nonce(eeSFL_Include);
$eeClass = ''; // CSS Class
// $eeListPosition = FALSE; // eeSFLS
$eeShowOps = FALSE; // Assume No
$eeShowingResults = FALSE;
if(!isset($eeSFL_HideName)) { $eeSFL_HideName = FALSE; }
if(!isset($eeSFL_HideType)) { $eeSFL_HideType = FALSE; }
if($eeSFL->eeEnvironment['wpUserID']) { 
	$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'USER ID: ' . $eeSFL->eeEnvironment['wpUserID']; }

// What Are We Doing?
if(isset($_GET['eeSFL_ArchivePath']) AND isset($_GET['eeSFL_ArchiveListID'])) { // Pro Unzipping
	require_once(eeSFL_PluginDir . 'pro/ee-extract-process.php');
} else {
	if( empty($eeSFL->eeAllFiles) ) {
		if(defined('eeSFL_Pro')) {
			$eeSFL_Pro->eeSFL_GetFileList($eeForceSort); // Maybe Scan the Disk
		} else {
			$eeSFL->eeSFL_UpdateFileListArray(1); // Scan the Disk
		}
	}	
}

// PRO
if( defined('eeSFL_Pro') ) {  require_once(eeSFL_PluginDir . 'pro/ee-list-display-folder-process.php'); }

// Bulk Ops
require_once(eeSFL_PluginDir . 'base/ee-list-ops-bar-process.php');

// Check for Upload Job
if($eeSFL_Uploaded) { 
	
	foreach( $eeSFL->eeAllFiles as $eeKey => $eeFileArray ) {
		if( in_array($eeFileArray['FilePath'], $eeSFL_Upload->eeUploadedFiles) ) {
			$eeSFL->eeDisplayFiles[] = $eeFileArray;
		}
	}
	
	$eeSFL->eeAllFiles = $eeSFL->eeDisplayFiles;

	if(count($eeSFL->eeAllFiles) == 0) {
		$eeSFL->eeAllFiles = array();
		$eeSFL->eeLog['errors'][] = 'Upload Processing Error.';
		$eeSFL->eeLog['errors'][] = $eeSFL_Upload->eeUploadedFiles;
	}
}

// PRO
if( defined('eeSFL_Pro') ) {
	require_once(eeSFL_PluginDir . 'pro/ee-list-display-pre-process.php');
} else {
	foreach($eeSFL->eeAllFiles as $eeKey => $eeFileArray) {
		if(!strpos($eeFileArray['FilePath'], '/')) { // Omit these if any
			$eeSFL->eeDisplayFiles[] = $eeFileArray;
		}
	}
	$eeSFL->eeAllFiles = array();
}


// LIST DISPLAY ===================================================

$eeOutput .= '

<span id="eeSFL_FileListTop"><!-- Simple File List - File List Top --></span>

<div class="eeSFL"';
	
if($eeSFL->eeListRun == 1) {$eeOutput .= ' id="eeSFL"'; } // 3/20 - Legacy for user CSS
	
$eeOutput .= '>';

// User Messaging
$eeOutput .= $eeSFL_Messaging->eeSFL_ResultsNotification();


// Page Setup ----------
$eeOutput .= '
<span class="eeHide" id="eeSFL_ID">' . $eeSFL->eeListID . '</span>

<script>

	// BASE
	const eeSFL_ThisURL = "' . esc_url($eeSFL->eeURL) . '";
	const eeSFL_PluginURL = "' . esc_js($eeSFL->eeEnvironment['pluginURL']) . '";
	const eeSFL_FileListDir = "' . esc_js($eeSFL->eeListSettings['FileListDir']) . '";
	const eeSFL_ShowListStyle = "' . esc_js($eeSFL->eeListSettings['ShowListStyle']) . '";
	
	// PRO
	eeSFL_ListID = ' . esc_js($eeSFL->eeListID) . ';
	eeSFL_SubFolder = "' . esc_js($eeSFL->eeCurrentFolder) . '/' . '";
	const eeSFL_ShortcodeFolder = "' . esc_js($eeSFL->eeShortcodeFolder) . '/' . '";
	
</script>

';

// Uploaded Files
if(!is_admin() AND $eeSFL_Uploaded) {
	
	$eeOutput .= '<p class="eeSFL_ListMeta"><a href="' . eeSFL_AppendProperUrlOp($eeSFL->eeURL) . 
		'ee=1" class="button eeButton" id="eeSFL_BacktoFilesButton">&larr; ' . 
			__('Back to the Files', 'ee-simple-file-list') . '</a></p>';
		
	if($eeSFLE) {
		$eeSendFilesArray = $eeSFL->eeDisplayFiles; // Restrict to just what was uploaded
	}
}

if($eeSFLS) { require_once(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-search-form.php'); }


// Bulk Operations Bar
require_once(eeSFL_PluginDir . 'base/ee-list-ops-bar-display.php');

// PRO
if( defined('eeSFL_Pro') ) { require_once(eeSFL_PluginDir . 'pro/ee-list-display-pro.php'); }

$eeSFL->eeItemCount = count($eeSFL->eeDisplayFiles);
$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Listing ' . $eeSFL->eeItemCount . ' Items';

if( $eeSFL->eeItemCount ) {

	if(is_admin() OR $eeSFL->eeListSettings['ShowListStyle'] == 'TABLE') { // ---------------------- LIST DISPLAY ---------
		require_once(eeSFL_PluginDir . 'base/ee-list-display-table.php');
	} elseif($eeSFL->eeListSettings['ShowListStyle'] == 'TILES') {
		require_once(eeSFL_PluginDir . 'base/ee-list-display-tiles.php');
	} else {
		require_once(eeSFL_PluginDir . 'base/ee-list-display-flex.php');
	}
	
	if($eeSFLS) { require_once(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-pagination-display.php'); }
	
	
// No Files Found
} else {
	
	$eeSFL->eeLog['issues'][] = 'No Files Found';
	
	if(is_admin()) {
		$eeOutput .= '
		<div><p>&#8593; ' . __('Upload some files and they will appear here.', 'ee-simple-file-list') . '</p></div>';
	}
} 	

$eeOutput .= '

</div><!-- END SFL File List Bottom -->';

require_once(eeSFL_PluginDir . 'base/ee-list-display-modals.php');
require_once(eeSFL_PluginDir . 'base/ee-alert-modal.php');

// List Loaded
$eeMessages[] = $eeSFL->eeURL;
$eeMessages[] = 'Listing ' . $eeSFL->eeFileCount . ' Items';
do_action('eeSFL_Hook_Loaded', $eeMessages);

?>