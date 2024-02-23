<?php // Simple File List Script: ee-list-display.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeClass = ''; // Meaning, CSS class
$eeURL = $eeSFL_BASE->eeSFL_GetThisURL();
$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = 'Loaded: ee-list-display';
$eeMessages = array('File List Loading');

if(!isset($eeSFL_HideName)) { $eeSFL_HideName = FALSE; }
if(!isset($eeSFL_HideType)) { $eeSFL_HideType = FALSE; }

// Who is accessing this list?
$eeThisUser = get_current_user_id();
$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - USER ID: ' . $eeThisUser ;

// echo '<pre>'; print_r($eeSFL_BASE->eeAllFiles); echo '</pre>'; exit;

if( empty($eeSFL_BASE->eeAllFiles) ) { // Might be Set in Admin
	$eeSFL_BASE->eeSFL_UpdateFileListArray();
}

// Save for later
$eeSFL_FileTotalCount = 0;
$eeSFL_FileTotalCount = count($eeSFL_BASE->eeAllFiles, 0);

// Check for Upload Job
if( $eeSFL_Uploaded ) {
	
	// echo '<pre>'; print_r($eeSFL_BASE->eeAllFiles); echo '</pre>'; exit;
	
	foreach( $eeSFL_BASE->eeAllFiles as $eeKey => $eeFileArray ) {
		
		if( in_array($eeFileArray['FilePath'], $eeSFLU_BASE->eeUploadedFiles) ) {
			$eeSFL_BASE->eeDisplayFiles[] = $eeFileArray;
		}
	}
	
	$eeSFL_BASE->eeAllFiles = $eeSFL_BASE->eeDisplayFiles;

	if(count($eeSFL_BASE->eeAllFiles) == 0) {
		$eeSFL_BASE->eeAllFiles = array();
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'][] = 'Upload Processing Error.';
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'][] = $eeSFLU_BASE->eeUploadedFiles;
	}
}


// DISPLAY ===================================================

$eeOutput .= '

<span id="eeSFL_FileListTop"><!-- Simple File List - File List Top --></span>

<div class="eeSFL" id="eeSFL">
<span class="eeHide" id="eeSFL_ID">1</span>';

// User Messaging
$eeOutput .= $eeSFL_BASE->eeSFL_ResultsNotification();

$eeOutput .= '

<script>
	eeSFL_ListID = 1;
	eeSFL_SubFolder = "/";
	const eeSFL_ThisURL = "' . esc_url($eeURL) . '";
	const eeSFL_PluginURL = "' . esc_url($eeSFL_BASE->eeEnvironment['pluginURL']) . '";
	const eeSFL_FileListDir = "' . esc_js($eeSFL_BASE->eeListSettings['FileListDir']) . '";
	const eeSFL_ShowListStyle = "' . esc_js($eeSFL_BASE->eeListSettings['ShowListStyle']) . '";
</script>
';

// Upload Confirmation
if(!$eeAdmin AND $eeSFL_Uploaded AND $eeSFL_BASE->eeListSettings['UploadConfirm'] == 'YES' AND $eeSFL_BASE->eeListRun == 1) {
	
	$eeOutput .= '
	
	<p><a href="' . eeSFL_BASE_AppendProperUrlOp($eeURL) . 'ee=1" class="button eeButton" id="eeSFL_BacktoFilesButton">&larr; ' . 
		__('Back to the Files', 'ee-simple-file-list') . '</a></p>
		
	';
}

// $eeSFL_BASE->eeAllFiles = array_values($eeSFL_BASE->eeAllFiles);

// echo '<pre>'; print_r($eeSFL_BASE->eeAllFiles); echo '</pre>';

if( !empty($eeSFL_BASE->eeAllFiles) ) {

	if($eeAdmin OR $eeSFL_BASE->eeListSettings['ShowListStyle'] == 'TABLE') {
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-list-display-table.php');
		
	} elseif($eeSFL_BASE->eeListSettings['ShowListStyle'] == 'TILES') {
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-list-display-tiles.php');
		
	} else {
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-list-display-flex.php');
		
	}

} else {
	
	$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = 'There are no files here :-(';
	
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


// Modal Input -------------------------
					
$eeOutput .= '
<span class="eeHide" id="eeSFL_Modal_FileID"></span>';

if($eeAdmin OR $eeSFL_BASE->eeListSettings['AllowFrontManage'] == 'YES') {
	
	$eeOutput .= '
	<span class="eeHide" id="eeSFL_ActionNonce">';
	if(is_admin() OR $eeSFL_BASE->eeListSettings['AllowFrontManage'] == 'YES') {
		$eeSFL_ActionNonce = wp_create_nonce('ee-sfl-manage-files');
		$eeOutput .= $eeSFL_ActionNonce;
	}
	$eeOutput .= '</span>
	
	<div class="eeSFL_Modal" id="eeSFL_Modal_EditFile">
	<div class="eeSFL_ModalBackground"></div>
	<div class="eeSFL_ModalBody">
	
		<button id="eeSFL_Modal_Manage_Close" class="eeSFL_ModalClose">&times;</button>
		
		<h1>' . __('Edit File', 'ee-simple-file-list') . '</h1>
		
		<p class="eeSFL_ModalFilePath eeHide"></p>
		
		<p class="eeSFL_ModalFileDetails">' . 
		__('Added', 'ee-simple-file-list-pro') . ': <span id="eeSFL_FileDateAdded" >???</span> | ' . 
		__('Changed', 'ee-simple-file-list-pro') . ': <span id="eeSFL_FileDateChanged" >???</span> | ' . 
		__('Size', 'ee-simple-file-list-pro') . ': <span id="eeSFL_FileSize">???</span>
		</p>
		
		<label for="eeSFL_FileNameNew">' . __('File Name', 'ee-simple-file-list') . '</label>
		<input type="text" id="eeSFL_FileNameNew" name="eeSFL_FileNameNew" value="??" size="64" />
		<small class="eeSFL_ModalNote">' . __('Change the name.', 'ee-simple-file-list') . ' ' . __('Some characters are not allowed. These will be automatically replaced.', 'ee-simple-file-list') . '</small>';
			
		$eeOutput .= '<label for="eeSFL_FileNiceNameNew">' . __('File Nice Name', 'ee-simple-file-list') . '</label>
		<input type="text" id="eeSFL_FileNiceNameNew" name="eeSFL_FileNiceNameNew" value="" size="64" />
		<small class="eeSFL_ModalNote">' . __('Enter a name that will be shown in place of the real file name.', 'ee-simple-file-list') . ' ' . __('You may use special characters not allowed in the file name.', 'ee-simple-file-list') . '</small>';
		
		$eeOutput .= '<label for="eeSFL_FileDescriptionNew">' . __('File Description', 'ee-simple-file-list') . '</label>
		<textarea cols="64" rows="3" id="eeSFL_FileDescriptionNew" name="eeSFL_FileDescriptionNew"></textarea>
		<small class="eeSFL_ModalNote">' . __('Add a description.', 'ee-simple-file-list') . ' ' . __('Use this field to describe this file and apply keywords for searching.', 'ee-simple-file-list') . '</small>
		
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
	
	
	// echo '<pre>'; print_r($eeSFL_BASE->eeAllFiles); echo '</pre>'; exit;
	
	
}
	
$eeSFL_BASE->eeEnvironment['FileLists'] = ''; // Remove to clean up display

$eeMessages[] = $eeURL;
$eeMessages[] = 'Listing ' . $eeSFL_BASE->eeFileCount . ' Items';
do_action('eeSFL_Hook_Loaded', $eeMessages);

?>