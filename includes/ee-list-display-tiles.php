<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails
	
$eeFileID = 0; // Assign an ID number to each Tile, aka Row

$eeSFL_BASE_Log['RunTime'][] = 'Listing Files in Tile View...';

$eeOutput .= '<section class="eeFiles">';
						
// Loop through array
foreach($eeSFL_Files as $eeFileKey => $eeFileArray) { // <<<---------------------------- BEGIN FILE LIST LOOP ----------------<<<
	
	// Populate our class properties for this file
	if( $eeSFL_BASE->eeSFL_ProcessFileArray($eeFileArray) === FALSE ) { continue; } // Skip This File
			
	if( $eeSFL_BASE->eeIsFile === TRUE ) {
		
		$eeFileID ++;
		
		$eeOutput .= '
		
		<article id="eeSFL_FileID-' . $eeFileID . '" class="eeSFL_Tile">';
		
		$eeOutput .= '
		
		<span class="eeSFL_RealFileName eeHide">' . $eeSFL_BASE->eeRealFileName . '</span>
		<span class="eeSFL_FileNiceName eeHide">' . $eeSFL_BASE->eeFileNiceName . '</span>
		
		<h4 class="eeSFL_FileLink">
			<a class="eeSFL_FileName" href="' . $eeSFL_BASE->eeFileURL .  '" target="_blank">' . stripslashes($eeSFL_BASE->eeFileName) . '</a></h4>';
			
		// File Actions
		$eeOutput .= $eeSFL_BASE->eeSFL_ReturnFileActions($eeFileID);
		
		
		// Thumbnail
		if($eeAdmin OR $eeSFL_Settings['ShowFileThumb'] == 'YES') {
			
			$eeOutput .= '<div class="eeSFL_Thumbnail">';
			
			if($eeSFL_BASE->eeFileThumbURL) { $eeOutput .= '<a href="' . $eeSFL_BASE->eeFileURL .  '"';
					
				$eeOutput .= '><img src="' . $eeSFL_BASE->eeFileThumbURL . '" width="64" height="64" alt="Thumb" /></a>'; }
			
			$eeOutput .= '</div>';
		}
		
		
		// Show File Description
		if($eeSFL_Settings['ShowFileDesc'] == 'NO' AND !$eeAdmin) { $eeClass = 'eeHide'; }
		$eeOutput .= '<p class="eeSFL_FileDesc ' . $eeClass . '">' . stripslashes($eeSFL_BASE->eeFileDescription) . '</p>'; // Always here for JS
		
		// Submitter Info
		if($eeAdmin OR $eeSFL_Settings['ShowSubmitterInfo'] == 'YES') {	
			if($eeSFL_BASE->eeFileSubmitterName) {
				$eeOutput .= '<small class="eeSFL_FileSubmitter"><span>' . $eeSFL_Settings['LabelOwner'] . ': </span>
					<a href="mailto:' . $eeSFL_BASE->eeFileSubmitterEmail . '">' . stripslashes($eeSFL_BASE->eeFileSubmitterName) . '</a></small>';
			}
		}
		
		$eeOutput .= '<div class="eeSFL_FileDetails">';

		
		// File Size
		if($eeAdmin OR $eeSFL_Settings['ShowFileSize'] == 'YES') {
		
			$eeOutput .= '<span class="eeSFL_FileSize">' . $eeSFL_BASE->eeFileSize . '</span>';
		}
		
		
		// File Modification Date
		if($eeAdmin OR $eeSFL_Settings['ShowFileDate'] == 'YES') {
			
			$eeOutput .= '<span class="eeSFL_FileDate">' . $eeSFL_BASE->eeFileDate . '</span>';
		}
		
		
		$eeOutput .= '</div>
		
		</article>';
	
	}

}

$eeOutput .= '</section>';


?>