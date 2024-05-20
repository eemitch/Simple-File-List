<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Listing Files in Tile View...';

// Bulk Editing
if( ($eeShowOps OR $eeSFL->eeListSettings['AllowBulkFileDownload'] == 'YES') AND !$eeShowingResults) {
	$eeOutput .= '<p class="eeSFL_BulkEdit eeCentered"><input type="checkbox" id="eeSFL_BulkEditAll" name="eeBulkEditAll" value="YES"/> 
	<label for="eeSFL_BulkEditAll">' . __('Select All', 'ee-simple-file-list') . '</label></p>';
}

$eeOutput .= '<section class="eeFiles eeSFL_Item">';
						
// Loop through array
foreach($eeSFL->eeDisplayFiles as $eeFileID => $eeFileArray) { // <<<---------------------------- BEGIN FILE LIST LOOP ----------------<<<
	
	// Populate our class properties for this file
	if( $eeSFL->eeSFL_ProcessFileArray($eeFileArray, $eeSFL_HideName, $eeSFL_HideType) ) {
		
		// Extension Check
		if($eeSFLA AND !is_admin()) { 
			if( $eeSFLA->eeSFLA_FileFirewall($eeFileArray) === FALSE ) { continue; } // Skip this file if FALSE
		}
		
		$eeOutput .= '
		
		<article id="eeSFL_FileID-' . $eeFileID . '" class="eeSFL_Item">';
		
		if($eeSFL->eeIsFolder) {
				$eeOutput .= '
				<span class="eeSFL_FilePath eeHide">' . $eeSFL->eeFilePath . '</span>';
		}
		
		// Proper Path for Search Results
		if(isset($_POST['eeSFLS_Searching'])) { 
			
			$eePathInfo = pathinfo($eeSFL->eeFilePath);
			$eePath = $eePathInfo['dirname'];
			if($eePath != '.') {
				$eeOutput .= '
				<span class="eeSFL_RealFilePath eeHide">' . $eePath . '/</span>';
			}
		}
			
		$eeOutput .= '
		<span class="eeSFL_RealFileName eeHide">' . esc_textarea($eeSFL->eeRealFileName) . '</span>
		<span class="eeSFL_FileNiceName eeHide">' . esc_textarea($eeSFL->eeFileNiceName) . '</span>
		<span class="eeSFL_FileMimeType eeHide">' . esc_textarea($eeSFL->eeFileMIME) . '</span>
		
		<h4 class="eeSFL_FileLink">';
		
		// Bulk Editing
		if( ($eeShowOps OR $eeSFL->eeListSettings['AllowBulkFileDownload'] == 'YES') AND !$eeShowingResults) {
			$eeOutput .= '
			<span class="eeSFL_BulkEdit">
				<input type="checkbox" id="eeSFL_BulkEdit_' . $eeFileID . '"  class="eeSFL_BulkEditCheck" name="eeBulkEdit" value="' . $eeFileID . '"/></span> ';
		}
		
		$eeOutput .= '<a class="eeSFL_FileName" href="' . esc_url($eeSFL->eeFileURL) .  '"';
			
		if($eeSFL->eeIsFile === TRUE) { $eeOutput .= ' target="_blank"'; }
			
		$eeOutput .= '>' . esc_textarea(stripslashes($eeSFL->eeFileName)) . '</a></h4>';
			
		// File Actions
		$eeOutput .= $eeSFL->eeSFL_ReturnFileActions($eeFileID, $eeFileArray);
		
		
		// Thumbnail
		if(is_admin() OR $eeSFL->eeListSettings['ShowFileThumb'] == 'YES') {
			if($eeSFL->eeFileThumbURL) { 
				$eeOutput .= '<div class="eeSFL_Thumbnail"><a href="' . esc_url($eeSFL->eeFileURL) .  '"';
			
			if($eeSFL->eeIsFile === TRUE) { $eeOutput .= ' target="_blank"'; }
			
			$eeOutput .= '><img src="' . esc_url($eeSFL->eeFileThumbURL) . '" width="64" height="64" alt="Thumb" /></a></div>';
			}
		}
		
		
		// File Description
		if($eeSFL->eeListSettings['ShowFileDesc'] == 'NO' AND !is_admin()) { $eeClass = 'eeHide'; }
		$eeOutput .= '<p class="eeSFL_FileDesc ' . $eeClass . '">' . esc_textarea(stripslashes($eeSFL->eeFileDescription)) . '</p>'; // Always here for JS
		
		// Submitter Info
		$eeShowIt = FALSE;
		if(is_admin() AND $eeSFL->eeEnvironment['wpUserID'] != $eeSFL->eeFileOwner) {
			$eeShowIt = TRUE;
		} elseif($eeSFL->eeListSettings['ShowSubmitterInfo'] == 'YES' ) { 
			if($eeSFL->eeEnvironment['wpUserID'] AND $eeSFL->eeEnvironment['wpUserID'] != $eeSFL->eeFileOwner) {
				$eeShowIt = TRUE;
			} elseif( !$eeSFL->eeEnvironment['wpUserID'] ) { // Not logged in
				$eeShowIt = TRUE;
			}
		}
		if($eeShowIt AND $eeSFL->eeFileSubmitterName) {
			$eeOutput .= '<p class="eeSFL_FileSubmitter"><span>' . esc_textarea($eeSFL->eeListSettings['LabelOwner']) . ': </span>
				<a href="mailto:' . esc_textarea($eeSFL->eeFileSubmitterEmail) . '">' . esc_textarea(stripslashes($eeSFL->eeFileSubmitterName)) . '</a></p>';
		}
		$eeShowIt = FALSE;
		
		$eeOutput .= '<div class="eeSFL_FileDetails">';

		
		// File Size
		if(is_admin() OR $eeSFL->eeListSettings['ShowFileSize'] == 'YES') {
		
			$eeOutput .= '<span class="eeSFL_FileSize">';
			
			if($eeSFL->eeIsFile) {
							
				$eeOutput .= esc_textarea($eeSFL->eeFileSize);
				
			} else {
				
				$eeOutput .= $eeSFL->eeItemCount . ' ' . __('Items', 'ee-simple-file-list');
				if($eeSFL->eeListSettings['ShowFolderSize'] == 'YES') { $eeOutput .= ' - ' . esc_textarea($eeSFL->eeFileSize); }
			}
			
			$eeOutput .= '</span>';
		}
		
		
		// File Modification Date
		if(is_admin() OR $eeSFL->eeListSettings['ShowFileDate'] == 'YES') {
			
			$eeOutput .= '<span class="eeSFL_FileDate">' . esc_textarea($eeSFL->eeFileDate) . '</span>';
		}
		
		
		$eeOutput .= '</div>
		
		</article>';
	
	}

}

$eeOutput .= '</section>';


?>