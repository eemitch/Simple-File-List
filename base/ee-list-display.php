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
// $eeShowOps = FALSE; // Assume No
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

// echo '<pre>'; print_r($eeSFL->eeAllFiles); echo '</pre>'; exit;

// PRO
if( defined('eeSFL_Pro') ) {  require_once(eeSFL_PluginDir . 'pro/ee-list-display-folder-process.php'); }

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
} elseif(empty($eeSFL->eeDisplayFiles)) {
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

// Show File Ops or Not
if( is_admin() ) { $eeShowOps = TRUE; } 
	else { if( $eeSFL->eeListSettings['AllowFrontManage'] == 'YES' AND $eeSFL->eeListRun == 1 ) { 
		$eeShowOps = TRUE; }	
}

// Never show these things after upload or search
if( isset($_POST['eeSFL_Upload']) ) { 
	$eeShowingResults = TRUE;
	$eeShowOps = FALSE;
	$eeSFL->eeListSettings['AllowFrontManage'] = 'NO';
	$eeSFL->eeListSettings['AllowBulkFileDownload'] = 'NO'; // PRO
}

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

</div><!-- END SFL File List Bottom -->

<!-- BEGIN SFL Modals -->';

// Modal Inputs -------------------------
					
$eeOutput .= '
<span class="eeHide" id="eeSFL_Modal_FileID"></span>';

if($eeSFLE) { require_once(WP_PLUGIN_DIR . '/ee-simple-file-list-email/includes/ee-list-display-email-1.php'); } // PRO

if(is_admin() OR $eeSFL->eeListSettings['AllowFrontManage'] == 'YES') {

	$eeOutput .= '
	<span class="eeHide" id="eeSFL_EditNonce">' . wp_create_nonce(eeSFL_Nonce) . '</span>
	
	<div class="eeSFL_Modal" id="eeSFL_Modal_EditFile">
	<div class="eeSFL_ModalBackground"></div>
	<div class="eeSFL_ModalBody">
	
		<button class="eeSFL_ModalClose">&times;</button>
		
		<h1>' . __('Edit Item', 'ee-simple-file-list') . '</h1>
		
		<p class="eeSFL_ModalFilePath eeHide"></p>
		
		<p class="eeSFL_ModalFileDetails">' . 
		__('Added', 'ee-simple-file-list') . ': <span id="eeSFL_FileDateAdded" >???</span> | ' . 
		__('Changed', 'ee-simple-file-list') . ': <span id="eeSFL_FileDateChanged" >???</span> | ' . 
		__('Size', 'ee-simple-file-list') . ': <span id="eeSFL_FileSize">???</span>
		</p>
		
		<label for="eeSFL_FileNameNew">' . __('Item Name', 'ee-simple-file-list') . '</label>
		<input type="text" id="eeSFL_FileNameNew" name="eeSFL_FileNameNew" value="??" size="64" />
		<small class="eeSFL_ModalNote">' . __('Change the name.', 'ee-simple-file-list') . ' ' . __('Some characters are not allowed. These will be automatically replaced.', 'ee-simple-file-list') . '</small>';
			
		$eeOutput .= '<label for="eeSFL_FileNiceNameNew">' . __('File Nice Name', 'ee-simple-file-list') . '</label>
		<input type="text" id="eeSFL_FileNiceNameNew" name="eeSFL_FileNiceNameNew" value="" size="64" />
		<small class="eeSFL_ModalNote">' . __('Enter a name that will be shown in place of the real file name.', 'ee-simple-file-list') . ' ' . __('You may use special characters not allowed in the file name.', 'ee-simple-file-list') . '</small>';
		
		$eeOutput .= '<label for="eeSFL_FileDescriptionNew">' . __('Item Description', 'ee-simple-file-list') . '</label>
		<textarea cols="64" rows="3" id="eeSFL_FileDescriptionNew" name="eeSFL_FileDescriptionNew"></textarea>
		<small class="eeSFL_ModalNote">' . __('Add a description.', 'ee-simple-file-list') . ' ' . __('Use this field to describe this item and apply keywords for searching.', 'ee-simple-file-list') . '</small>
		
		<h4>' . __('Item Date Added', 'ee-simple-file-list') . '</h4>
		
		<div class="eeSFL_DateNew">
		<label>' . __('Year', 'ee-simple-file-list') . '<input min="1970" max="' . date('Y') . '" type="number" name="eeSFL_FileDateAddedYearNew" value="" id="eeSFL_FileDateAddedYearNew" /></label>
		<label>' . __('Month', 'ee-simple-file-list') . '<input min="1" max="12" type="number" name="eeSFL_FileDateAddedMonthNew" value="" id="eeSFL_FileDateAddedMonthNew" /></label>
		<label>' . __('Day', 'ee-simple-file-list') . '<input min="1" max="31" type="number" name="eeSFL_FileDateAddedDayNew" value="" id="eeSFL_FileDateAddedDayNew" /></label>
		</div>
		<small class="eeSFL_ModalNote">' . __('Change the date added to the list.', 'ee-simple-file-list') . '</small>
		
		<h4>' . __('Item Date Changed', 'ee-simple-file-list') . '</h4>
		
		<div class="eeSFL_DateNew">
		<label>' . __('Year', 'ee-simple-file-list') . '<input min="1970" max="' . date('Y') . '" type="number" name="eeSFL_FileDateChangedYearNew" value="" id="eeSFL_FileDateChangedYearNew" /></label>
		<label>' . __('Month', 'ee-simple-file-list') . '<input min="1" max="12" type="number" name="eeSFL_FileDateChangedMonthNew" value="" id="eeSFL_FileDateChangedMonthNew" /></label>
		<label>' . __('Day', 'ee-simple-file-list') . '<input min="1" max="31" type="number" name="eeSFL_FileDateChangedDayNew" value="" id="eeSFL_FileDateChangedDayNew" /></label>
		</div>
		<small class="eeSFL_ModalNote">' . __('Change date the file was last modified.', 'ee-simple-file-list') . '</small>
		
		<button class="button" onclick="eeSFL_FileEditSaved()">' . __('Save', 'ee-simple-file-list') . '</button>

	</div>
	</div>';
	
	// PRO
	if( defined('eeSFL_Pro') ) { require_once(eeSFL_PluginDir . 'pro/ee-list-display-move-modal.php'); }
	if($eeSFLA) { require_once(WP_PLUGIN_DIR . '/ee-simple-file-list-access/includes/ee-list-display-access-2.php'); } // PRO
}

$eeOutput .= '

<!-- End SFL Modals -->';

// List Loaded
$eeMessages[] = $eeSFL->eeURL;
$eeMessages[] = 'Listing ' . $eeSFL->eeFileCount . ' Items';
do_action('eeSFL_Hook_Loaded', $eeMessages);

?>