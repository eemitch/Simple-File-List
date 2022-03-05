<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails
	
$eeFileID = 0; // Assign an ID number to each row

// TABLE HEAD ==================================================================================================

$eeOutput .= '<table class="eeFiles">';

if($eeSFL_Settings['ShowHeader'] == 'YES' OR $eeAdmin) { $eeOutput .= '<thead><tr>';
						
	if($eeAdmin OR $eeSFL_Settings['ShowFileThumb'] == 'YES') { 
		
		$eeOutput .= '<th class="eeSFL_Thumbnail">';
		
		if(@$eeSFL_Settings['LabelThumb']) { $eeOutput .= stripslashes($eeSFL_Settings['LabelThumb']); } 
			else { $eeOutput .= __('Thumb', 'ee-simple-file-list'); }
		
		$eeOutput .= '</th>';
	}
	
	
	$eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileName">';
		
	if($eeSFL_Settings['LabelName']) { $eeOutput .= stripslashes($eeSFL_Settings['LabelName']); } 
		else { $eeOutput .= __('Name', 'ee-simple-file-list'); }
	
	$eeOutput .= '</th>';
	
	
	if($eeAdmin OR $eeSFL_Settings['ShowFileSize'] == 'YES') { 
		
		$eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileSize">';
		
		if(@$eeSFL_Settings['LabelSize']) { $eeOutput .= stripslashes($eeSFL_Settings['LabelSize']); } 
			else { $eeOutput .= __('Size', 'ee-simple-file-list'); }
		
		$eeOutput .= '</th>';
	}
	
	
	if($eeAdmin OR $eeSFL_Settings['ShowFileDate'] == 'YES') { 
		
		$eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileDate">';
		
		if(@$eeSFL_Settings['LabelDate']) { $eeOutput .= stripslashes($eeSFL_Settings['LabelDate']); } 
			else { $eeOutput .= __('Date', 'ee-simple-file-list'); }
		
		$eeOutput .= '</th>';
	}

	
	$eeOutput .= '</tr>
	
	</thead>';
}						

$eeOutput .= '

<tbody>';
				

$eeSFL_BASE_Log['RunTime'][] = 'Listing Files...';
						
// Loop through array
foreach($eeSFL_Files as $eeFileKey => $eeFileArray) { // <<<---------------------------- BEGIN FILE LIST LOOP ----------------<<<
	
	$eeFileID ++; // We start with one ...
	
	// Populate our class properties for this file
	if( $eeSFL_BASE->eeSFL_ProcessFileArray($eeFileArray) === FALSE ) { continue; } // Skip This File
			
	if( $eeSFL_BASE->eeIsFile === TRUE ) {
			
		// Start The List --------------------------------------------------------------
	
		$eeOutput .= '
		
		<tr class="eeSFL_Row" id="eeSFL_FileID-' . $eeFileID . '">'; // Add an ID to use in javascript
		
		
		// Thumbnail
		if($eeAdmin OR $eeSFL_Settings['ShowFileThumb'] == 'YES') {
			
			$eeOutput .= '<td class="eeSFL_Thumbnail">';
			
			if($eeSFL_BASE->eeFileThumbURL) { $eeOutput .= '<a href="' . $eeSFL_BASE->eeFileURL .  '"';
					
				$eeOutput .= '><img src="' . $eeSFL_BASE->eeFileThumbURL . '" width="64" height="64" alt="Thumb" /></a>'; }
			
			$eeOutput .= '</td>';
		}
		
		
		// NAME
		$eeOutput .= '<td class="eeSFL_FileName">';
		
		if($eeSFL_BASE->eeFileURL) {
			
			$eeOutput .= '
			
			<span class="eeSFL_RealFileName eeHide">' . $eeSFL_BASE->eeRealFileName . '</span>
			<span class="eeSFL_FileNiceName eeHide">' . $eeSFL_BASE->eeFileNiceName . '</span>
			
			<p class="eeSFL_FileLink"><a class="eeSFL_FileName" href="' . $eeSFL_BASE->eeFileURL .  '" target="_blank">' . stripslashes($eeSFL_BASE->eeFileName) . '</a></p>';
			
			
			
			// Show File Description
			if(!$eeAdmin OR $eeSFL_Settings['ShowFileDesc'] == 'NO') {
				$eeClass = 'eeHide';
			}
			if(!$eeSFL_BASE->eeFileDescription) { // This is always here in case of editing, but hidden if empty
				$eeOutput .= '<p class="eeSFL_FileDesc ' . $eeClass . '">' . $eeSFL_BASE->eeFileDescription . '</p>';
			}
			
			
			// Submitter Info
			if($eeAdmin OR $eeSFL_Settings['ShowSubmitterInfo'] == 'YES') {	
				if($eeSFL_BASE->eeFileSubmitterName) {
					$eeOutput .= '<p class="eeSFL_FileSubmitter">' . __('Submitted by', 'ee-simple-file-list') . ': 
						<a href="mailto:' . $eeSFL_BASE->eeFileSubmitterEmail . '">' . $eeSFL_BASE->eeFileSubmitterName . '</a></p>';
				}
			}
			
			
			
			// File Actions   ------------------------------------------------------------------------------------
			
			if($eeAdmin OR $eeSFL_Settings['ShowFileActions'] == 'YES') { // Always show to Admin
				
				// Construct
				$eeOutput .= '
				
				<small class="eeSFL_ListFileActions">';
					
				// Open Action
				if($eeAdmin OR $eeSFL_Settings['ShowFileOpen'] == 'YES') {
				
					if(in_array($eeSFL_BASE->eeFileExt, $eeSFL_BASE->eeOpenableFileFormats)) {
						$eeOutput .= '<a class="eeSFL_FileOpen" href="' . $eeSFL_BASE->eeFileURL . '" target="_blank">' . __('Open', 'ee-simple-file-list') . '</a>';
					}
				}
				
				// Download Action
				if($eeAdmin OR $eeSFL_Settings['ShowFileDownload'] == 'YES') {
				
					$eeOutput .= '<a class="eeSFL_FileDownload" href="' . $eeSFL_BASE->eeFileURL . '" download="' . basename($eeSFL_BASE->eeFileURL) . '">' . __('Download', 'ee-simple-file-list') . '</a>';
				
				}
				
				// Copy Link Action
				if($eeAdmin OR $eeSFL_Settings['ShowFileCopyLink'] == 'YES') {
					
					$eeOutput .= '<a class="eeSFL_CopyLinkToClipboard" onclick="eeSFL_BASE_CopyLinkToClipboard(\''  . $eeSFL_BASE->eeFileURL .   '\')" href="#">' . __('Copy Link', 'ee-simple-file-list') . '</a>';														
				
				}
				
				// Front-End Manage or Admin
				if( ($eeAdmin OR $eeSFL_Settings['AllowFrontManage'] == 'YES') AND $eeSFL_BASE_ListRun == 1) {							
					
					$eeOutput .= '
					
					<a href="#" onclick="eeSFL_BASE_OpenEditModal(' . $eeFileID . ')">' . __('Edit', 'ee-simple-file-list') . '</a>
					
					<a href="#" onclick="eeSFL_BASE_DeleteFile(' . $eeFileID . ')">' . __('Delete', 'ee-simple-file-list') . '</a>';
					
					if($eeAdmin) {
					
						$eeOutput .= '
						 <a class="eeDisabledAction" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Move', 'ee-simple-file-list') . '</a>
						 <a class="eeDisabledAction" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Users', 'ee-simple-file-list') . '</a>
						 <a class="eeDisabledAction" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Send', 'ee-simple-file-list') . '</a>';
					}
						
					$eeOutput .= '</small>'; // Close File List Actions Links
					
					// File Details to Pass to the Editor
					$eeOutput .= '
					
					<span class="eeHide eeSFL_FileSize">' . $eeSFL_BASE->eeFileSize . '</span>
					<span class="eeHide eeSFL_FileDateAdded">' . $eeSFL_BASE->eeFileDateAdded . '</span>
					<span class="eeHide eeSFL_FileDateChanged">' . $eeSFL_BASE->eeFileDateChanged . '</span>';
				
				} // END File Operations
		
			} // END File Actions	
		
		$eeOutput .= '</td>';
		
		
		
		// File Size
		if($eeAdmin OR $eeSFL_Settings['ShowFileSize'] == 'YES') {
		
			$eeOutput .= '<td class="eeSFL_FileSize">' . $eeSFL_BASE->eeFileSize . '</td>';
		}
		
		
		// File Modification Date
		if($eeAdmin OR $eeSFL_Settings['ShowFileDate'] == 'YES') {
			
			$eeOutput .= '<td class="eeSFL_FileDate">' . $eeSFL_BASE->eeFileDate . '</td>';
		}
		
		$eeOutput .= '</tr>';
	
		} // END If $fileURL
	
	$eeFileID++; // Bump the ID

	}

} // END $eeSFL_Files loop


$eeOutput .= '

</tbody>

</table>';
	
?>