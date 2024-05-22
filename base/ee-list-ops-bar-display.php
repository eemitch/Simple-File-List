<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

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
	$eeSFL->eeListSettings['AllowBulkFileDownload'] = 'NO';
}


// Bulk Operations
if( $eeShowOps AND !$eeShowingResults ) {
	
	// Bulk Edit / Folder Creation Input Display
	$eeOutput .= '<div class="eeSFL_ListOpsBar">
	
	<form action="' . $eeSFL->eeURL . '" method="POST">
	
	<input type="hidden" name="ee" value="1" />
	<input type="hidden" name="eeListID" value="' . $eeSFL->eeListID . '" />
	<input type="hidden" id="eeSFL_FileOpsFiles" name="eeSFL_FileOpsFiles" value="" />';
	
	$eeOutput .= wp_nonce_field('eeSFL_Nonce', eeSFL_Nonce, TRUE, FALSE);
		
	$eeOutput .= '
	
	<select id="eeSFL_FileOpsAction" name="eeSFL_FileOpsAction">';
	
	if( defined('eeSFL_Pro') ) { $eeOutput .= $eeSFL_Pro->eeSFL_CreateFolderInputDisplay(); }
		
	$eeOutput .= '
		<option value="Description">' . __('Apply Description', 'ee-simple-file-list') . '</option>
		<option value="Download">' . __('Download Items', 'ee-simple-file-list') . '</option>
		<option value="Delete" class="eeWarning">' . __('Delete Items', 'ee-simple-file-list') . '</option>';
		// if(is_admin() AND $eeSFLA) { $eeOutput .= '<option value="Copy">' . __('Copy Items To List', 'ee-simple-file-list') . '</option>'; }
		// if(is_admin() AND $eeSFLA) { $eeOutput .= '<option value="Grant">' . __('Grant Access to Items', 'ee-simple-file-list') . '</option>'; }
		
	$eeOutput .= '
	
	</select>';
	
	if( defined('eeSFL_Pro') ) { $eeOutput .= $eeSFL_Pro->eeSFL_MoveToInputDisplay(); }
	
	$eeOutput .= '
	<span class="eeHide" id="eeSFL_ZipFileName">' . __('File Archive', 'ee-simple-file-list') . ' ' . date('Y-m-d-H-i') . '</span>
	<span class="eeHide" id="eeSFL_DeleteText">' . __('Delete all of the selected items.', 'ee-simple-file-list') . '</span>
	<span class="eeHide" id="eeSFL_DescriptionPlaceholder">' . __('Insert description text here', 'ee-simple-file-list') . '</span>
		
	<input class="button" type="submit" id="eeSFL_ListOpsBarGo" name="eeSFL_ListOpsBarGo" value="' . __('GO', 'ee-simple-file-list') . '" />
		
	<br class="eeClearFix" />
	
	</form>
	</div>';


} elseif($eeSFL->eeListSettings['AllowBulkFileDownload'] == 'YES') { // Bulk Downloading Only
	
	// Bulk Edit / Folder Creation Input Display
	$eeOutput .= '<div class="eeSFL_BulkDownloadBar">
	
	<form action="' . $eeSFL->eeURL . '" method="POST">
	
	<input type="hidden" name="ee" value="1" />
	<input type="hidden" name="eeListID" value="' . $eeSFL->eeListID . '" />
	<input type="hidden" name="eeSFL_FileOpsAction" value="Download" />
	<input type="hidden" id="eeSFL_FileOpsFiles" name="eeSFL_FileOpsFiles" value="" />';
	
	$eeOutput .= wp_nonce_field('eeSFL_Nonce', eeSFL_Nonce, TRUE, FALSE);
		
	$eeOutput .= '<label for="eeSFL_FileOpsActionInput">' . __('Download Files', 'ee-simple-file-list') . '</label><input required type="text" id="eeSFL_FileOpsActionInput" name="eeSFL_ZipFileName" value="' . __('File Archive', 'ee-simple-file-list') . ' ' . date('Y-m-d-H-i') . '" />
	
	<input class="button" type="submit" id="eeSFL_ListOpsBarGo" name="eeSFL_ListOpsBarGo" value="' . __('GO', 'ee-simple-file-list') . '" />
		
	<br class="eeClearFix" />
	
	</form>
	</div>';
	
}


?>