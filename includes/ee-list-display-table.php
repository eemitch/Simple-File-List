<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails
	
$eeFileID = 0; // Assign an ID number to each row

// TABLE HEAD ==================================================================================================

$eeOutput .= '<table class="eeFiles">';

if($eeSFL_BASE->eeListSettings['ShowHeader'] == 'YES' OR $eeAdmin) { $eeOutput .= '<thead><tr>';
						
	if($eeAdmin OR $eeSFL_BASE->eeListSettings['ShowFileThumb'] == 'YES') { 
		
		$eeOutput .= '<th class="eeSFL_Thumbnail">';
		
		if($eeSFL_BASE->eeListSettings['LabelThumb']) { $eeOutput .= stripslashes($eeSFL_BASE->eeListSettings['LabelThumb']); } 
			else { $eeOutput .= __('Thumb', 'ee-simple-file-list'); }
		
		$eeOutput .= '</th>';
	}
	
	
	$eeOutput .= '<th class="eeSFL_FileName">';
		
	if($eeSFL_BASE->eeListSettings['LabelName']) { $eeOutput .= stripslashes($eeSFL_BASE->eeListSettings['LabelName']); } 
		else { $eeOutput .= __('Name', 'ee-simple-file-list'); }
	
	$eeOutput .= '</th>';
	
	
	if($eeAdmin OR $eeSFL_BASE->eeListSettings['ShowFileSize'] == 'YES') { 
		
		$eeOutput .= '<th class="eeSFL_FileSize">';
		
		if($eeSFL_BASE->eeListSettings['LabelSize']) { $eeOutput .= stripslashes($eeSFL_BASE->eeListSettings['LabelSize']); } 
			else { $eeOutput .= __('Size', 'ee-simple-file-list'); }
		
		$eeOutput .= '</th>';
	}
	
	
	if($eeAdmin OR $eeSFL_BASE->eeListSettings['ShowFileDate'] == 'YES') { 
		
		$eeOutput .= '<th class="eeSFL_FileDate">';
		
		if($eeSFL_BASE->eeListSettings['LabelDate']) { $eeOutput .= stripslashes($eeSFL_BASE->eeListSettings['LabelDate']); } 
			else { $eeOutput .= __('Date', 'ee-simple-file-list'); }
		
		$eeOutput .= '</th>';
	}

	
	$eeOutput .= '</tr>
	
	</thead>';
}						

$eeOutput .= '

<tbody>';
				

$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = 'Listing Files in Table View...';
						
// Loop through array
foreach($eeSFL_BASE->eeAllFiles as $eeFileKey => $eeFileArray) { // <<<---------------------------- BEGIN FILE LIST LOOP ----------------<<<	 
	
	// echo '<pre>'; print_r($eeFileArray); echo '</pre>'; exit;
	
	// Populate our class properties for this file
	if( $eeSFL_BASE->eeSFL_ProcessFileArray($eeFileArray) === FALSE ) { continue; } // Skip This File
			
	if( $eeSFL_BASE->eeIsFile === TRUE ) {
			
		$eeFileID ++;
		
		// Start The List --------------------------------------------------------------
	
		$eeOutput .= '
		
		<tr class="eeSFL_Item" id="eeSFL_FileID-' . $eeFileID . '">'; // Add an ID to use in javascript
		
		
		// Thumbnail
		if($eeAdmin OR $eeSFL_BASE->eeListSettings['ShowFileThumb'] == 'YES') {
			
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
			<span class="eeSFL_FileMimeType eeHide">' . $eeSFL_BASE->eeFileMIME . '</span>
			
			<p class="eeSFL_FileLink"><a class="eeSFL_FileName" href="' . $eeSFL_BASE->eeFileURL .  '" target="_blank">' . stripslashes($eeSFL_BASE->eeFileName) . '</a></p>';
			
			
			
			// Show File Description
			if(!$eeAdmin AND $eeSFL_BASE->eeListSettings['ShowFileDesc'] == 'NO') { $eeClass = 'eeHide'; }
			
			// This is always here in case of editing, but hidden if empty
			$eeOutput .= '<p class="eeSFL_FileDesc ' . $eeClass . '">' . stripslashes($eeSFL_BASE->eeFileDescription) . '</p>';
			
			
			// Submitter Info
			$eeShowIt = FALSE;
			if($eeAdmin AND $eeThisUser != $eeSFL_BASE->eeFileOwner) {
				$eeShowIt = TRUE;
			} elseif($eeSFL_BASE->eeListSettings['ShowSubmitterInfo'] == 'YES' ) { 
				if($eeThisUser AND $eeThisUser != $eeSFL_BASE->eeFileOwner) {
					$eeShowIt = TRUE;
				} elseif( !$eeThisUser ) { // Not logged in
					$eeShowIt = TRUE;
				}
			}
			if($eeShowIt AND $eeSFL_BASE->eeFileSubmitterName) {
				$eeOutput .= '<p class="eeSFL_FileSubmitter"><span>' . $eeSFL_BASE->eeListSettings['LabelOwner'] . ': </span>
					<a href="mailto:' . $eeSFL_BASE->eeFileSubmitterEmail . '">' . stripslashes($eeSFL_BASE->eeFileSubmitterName) . '</a></p>';
			}
			$eeShowIt = FALSE;
			
			
			// File Actions
			$eeOutput .= $eeSFL_BASE->eeSFL_ReturnFileActions($eeFileID);
			
			
		
		$eeOutput .= '</td>';
		
		
		
		// File Size
		if($eeAdmin OR $eeSFL_BASE->eeListSettings['ShowFileSize'] == 'YES') {
		
			$eeOutput .= '<td class="eeSFL_FileSize">' . $eeSFL_BASE->eeFileSize . '</td>';
		}
		
		
		// File Modification Date
		if($eeAdmin OR $eeSFL_BASE->eeListSettings['ShowFileDate'] == 'YES') {
			
			$eeOutput .= '<td class="eeSFL_FileDate">' . $eeSFL_BASE->eeFileDate . '</td>';
		}
		
		$eeOutput .= '</tr>';
	
		} // END If $fileURL
	
	$eeFileID++; // Bump the ID

	}

} // END $eeSFL_BASE->eeAllFiles loop


$eeOutput .= '

</tbody>

</table>';
	
?>