<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

// TABLE HEAD ==================================================================================================

$eeOutput .= '<table class="eeFiles">';

if($eeSFL->eeListSettings['ShowHeader'] == 'YES' OR is_admin()) { $eeOutput .= '<thead><tr>';
	
	// Bulk Editing
	if( ($eeShowOps OR $eeSFL->eeListSettings['AllowBulkFileDownload'] == 'YES') AND !$eeShowingResults) {
		$eeOutput .= '
		<th class="eeSFL_BulkEdit">
			<input type="checkbox" id="eeSFL_BulkEditAll" name="eeBulkEditAll" value="YES"/></th>';
	}
						
	if(is_admin() OR $eeSFL->eeListSettings['ShowFileThumb'] == 'YES') { 
		
		$eeOutput .= '
		<th class="eeSFL_Thumbnail">';
		
		if($eeSFL->eeListSettings['LabelThumb']) { $eeOutput .= stripslashes($eeSFL->eeListSettings['LabelThumb']); } 
			else { $eeOutput .= __('Thumb', 'ee-simple-file-list'); }
		
		$eeOutput .= '
		</th>';
	}
	
	
	$eeOutput .= '
	<th class="eeSFL_FileName">';
		
	if($eeSFL->eeListSettings['LabelName']) { $eeOutput .= stripslashes($eeSFL->eeListSettings['LabelName']); } 
		else { $eeOutput .= __('Name', 'ee-simple-file-list'); }
	
	$eeOutput .= '
	</th>';
	
	
	if(is_admin() OR $eeSFL->eeListSettings['ShowFileSize'] == 'YES') { 
		
		$eeOutput .= '
		<th class="eeSFL_FileSize">';
		
		if($eeSFL->eeListSettings['LabelSize']) { $eeOutput .= stripslashes($eeSFL->eeListSettings['LabelSize']); } 
			else { $eeOutput .= __('Size', 'ee-simple-file-list'); }
		
		$eeOutput .= '
		</th>';
	}
	
	
	if(is_admin() OR $eeSFL->eeListSettings['ShowFileDate'] == 'YES') { 
		
		$eeOutput .= '
		<th class="eeSFL_FileDate">';
		
		if($eeSFL->eeListSettings['LabelDate']) { $eeOutput .= stripslashes($eeSFL->eeListSettings['LabelDate']); } 
			else { $eeOutput .= __('Date', 'ee-simple-file-list'); }
		
		if( is_admin() ) { $eeOutput .= ' <small>(' . $eeSFL->eeListSettings['ShowFileDateAs'] . ')</small>'; }
		
		$eeOutput .= '
		</th>';
	}

	
	$eeOutput .= '
	</tr>
	
	</thead>';
}						

$eeOutput .= '

<tbody>';
				

$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Listing Files in Table View...';

// echo '<pre>'; print_r($eeSFL->eeAllFiles); echo '</pre>'; exit;
						
// Loop through array
foreach($eeSFL->eeDisplayFiles as $eeFileID => $eeFileArray) { // <<<---------------------------- BEGIN FILE LIST LOOP ----------------<<<	 
	
	// Populate our class properties for this file
	if( $eeSFL->eeSFL_ProcessFileArray($eeFileArray, $eeSFL_HideName, $eeSFL_HideType) ) {
		
		// Extension Check
		if($eeSFLA AND !is_admin() ) { 
			if( $eeSFLA->eeSFLA_FileFirewall($eeFileArray) === FALSE ) { continue; } // Skip this file if FALSE
		}
		
		// Start The List --------------------------------------------------------------
	
		$eeOutput .= '
		
		<tr id="eeSFL_FileID-' . $eeFileID . '" class="eeSFL_Item">';
		
		// Bulk Editing
		if($eeShowOps OR $eeSFL->eeListSettings['AllowBulkFileDownload'] == 'YES') {
			$eeOutput .= '
			<td class="eeSFL_BulkEdit">';
			$eeOutput .= '
			<input type="checkbox" id="eeSFL_BulkEdit_' . $eeFileID . '"  class="eeSFL_BulkEditCheck" name="eeBulkEdit" value="' . $eeFileID . '"/>
			</td>';
		}
		
		
		// Thumbnail
		if(is_admin() OR $eeSFL->eeListSettings['ShowFileThumb'] == 'YES') {
			
			$eeOutput .= '
			<td class="eeSFL_Thumbnail">';
			
			if($eeSFL->eeFileThumbURL) { $eeOutput .= '<a href="' . esc_url($eeSFL->eeFileURL) .  '"';
			
				if($eeSFL->eeIsFile === TRUE) { $eeOutput .= ' target="_blank"'; }
				
				$eeOutput .= '><img src="' . esc_url($eeSFL->eeFileThumbURL) . '" width="64" height="64" alt="Thumb" /></a>'; }
			
				$eeOutput .= '</td>';
		}
		
		
		// NAME
		$eeOutput .= '
		<td class="eeSFL_FileNameCell eeSFL_FileName">';
		
		if($eeSFL->eeFileURL) {
			
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
			
			<p class="eeSFL_FileLink">';
			
			if($eeSFL->eeListSettings['ShowFileThumb'] == 'NO' AND $eeSFL->eeIsFolder) { $eeOutput .= '&#128193; '; }
			
			// Extension Check
			if( isset($_POST['eeSFLS_Searching']) ) { $eeOutput .= $eeSFLS->eeSFLS_DisplaySearchPath($eeSFL->eeFilePath); }
				
			$eeOutput .= '<a class="eeSFL_FileName" href="' . $eeSFL->eeFileURL .  '"';
			
			if($eeSFL->eeIsFile === TRUE) { $eeOutput .= ' target="_blank"'; }
			
			$eeOutput .= '>' . esc_textarea(stripslashes($eeSFL->eeFileName)) . '</a></p>';
			
			// Show File Description
			if(!is_admin() AND $eeSFL->eeListSettings['ShowFileDesc'] == 'NO') { $eeClass = 'eeHide'; }
			
			// This is always here in case of editing, but hidden if empty
			$eeOutput .= '
			<p class="eeSFL_FileDesc ' . $eeClass . '">' . esc_textarea(stripslashes($eeSFL->eeFileDescription)) . '</p>';
			
			
			// Submitter Info
			$eeShowIt = FALSE;
			if(is_admin() AND $eeSFL->eeEnvironment['wpUserID'] != $eeSFL->eeFileOwner) {
				$eeShowIt = TRUE;
			} elseif($eeSFL->eeListSettings['ShowSubmitterInfo'] == 'YES' ) { 
				if($eeSFL->eeEnvironment['wpUserID'] != $eeSFL->eeFileOwner) {
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
			
			
			// File Actions
			$eeOutput .= $eeSFL->eeSFL_ReturnFileActions($eeFileID, $eeFileArray);
			
			
		
		$eeOutput .= '
		</td>';
		
		
		
		// File Size
		if(is_admin() OR $eeSFL->eeListSettings['ShowFileSize'] == 'YES') {
			
			$eeOutput .= '
			<td class="eeSFL_FileSize">';
			
			if($eeSFL->eeIsFile) {
							
				$eeOutput .= esc_textarea($eeSFL->eeFileSize);
				
			} else {
				
				$eeOutput .= '<span class="eeSFL_Count">' . $eeSFL->eeItemCount . '</span> ' . __('Items', 'ee-simple-file-list');
				if($eeSFL->eeListSettings['ShowFolderSize'] == 'YES') { $eeOutput .= '<br />' . esc_textarea($eeSFL->eeFileSize); }
			}
		
			$eeOutput .= '</td>';
		}
		
		
		// File Modification Date
		if(is_admin() OR $eeSFL->eeListSettings['ShowFileDate'] == 'YES') {
			
			$eeOutput .= '<td class="eeSFL_FileDate"><span class="eeSFL_FileDateDisplayed">' . esc_textarea($eeSFL->eeFileDate) . '</span></td>';
		}
		
		$eeOutput .= '
		</tr>';
	
		} // END If URL

	}

} // END $eeSFL->eeDisplayFiles loop


$eeOutput .= '

</tbody>

</table>';
	
?>