<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails
	
$eeFileCount = 0; // Reset
$eeRowID = 0; // Assign an ID number to each row

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
	
	$eeRowID ++; // We start with one ...
	
	// Go
	if( is_array($eeFileArray) ) {
		
		// echo '<pre>'; print_r($eeFileArray); echo '</pre>'; exit;
		
		// Deny Folder Listing
		if(strpos($eeFileArray['FilePath'], '/')) { continue; } // Skip it
		
		// Ready, set...
		$eeIsFile = FALSE;
		$eeFileURL = FALSE;
		$eeFileExt = FALSE;
		$eeFileThumbURL = FALSE;

		$eeFilePath = $eeFileArray['FilePath']; // Path relative to FileListDir
		$eeFileName = basename($eeFileArray['FilePath']); // Just the name
		
		// Date to Display
		if($eeSFL_Settings['ShowFileDateAs'] == 'Modified') {
			$eeFileDateMod = date_i18n( get_option('date_format'), strtotime( $eeFileArray['FileDateChanged'] ) ); // The mod date
			$eeFileDate = $eeFileDateMod;
		} else {
			$eeFileDateAdded = date_i18n( get_option('date_format'), strtotime( $eeFileArray['FileDateAdded'] ) );
			$eeFileDate = $eeFileDateAdded;
		}
		
		$eeFileSize = eeSFL_BASE_FormatFileSize($eeFileArray['FileSize']); // The file size made nice too
		
		if(strpos($eeFilePath, '.')) {
				
			$eeIsFile = TRUE;
			$eeFileCount++; // Bump the file count
			$eeFileURL = $eeSFL_BASE_Env['wpSiteURL'] . $eeSFL_Settings['FileListDir'] . $eeFilePath; // Clickable URL
			$eeFileExt = $eeFileArray['FileExt']; // Get Extension
			
			// Skip names hidden via shortcode - LEGACY
			if(isset($eeHideName)) {
				
				$eeArray = explode(',', $eeHideName);
				
				foreach( $eeArray as $eeKey => $eeValue ) {
					
					if( strtolower($eeFileName) ==  $eeValue . '.' . $eeFileExt ) { // Without extension
						continue(2); // Go to next file
					}
					
					if($eeValue == strtolower($eeFileName)) { // With extension
						continue(2); // Go to next file
					}
				}
			}
			
			
			// Skip types hidden via shortcode
			if(isset($eeHideType)) {
				if(strpos($eeHideType, $eeFileExt) OR strpos($eeHideType, $eeFileExt) === 0 ) { 
					continue; // Go to next file
				}
			}
			
			
			// Start The List --------------------------------------------------------------
		
			$eeOutput .= '
			
			<tr id="eeSFL_RowID-' . $eeRowID . '">'; // Add an ID to use in javascript
			
			
			// Thumbnail
			if($eeAdmin OR $eeSFL_Settings['ShowFileThumb'] == 'YES') {
				
				$eeShowThumbImage = FALSE;
				
				if( in_array($eeFileExt,  $eeSFL_BASE->eeDynamicImageThumbFormats) AND $eeSFL_Settings['GenerateImgThumbs'] == 'YES' ) {
					$eeShowThumbImage = TRUE;
				}
				if( in_array($eeFileExt,  $eeSFL_BASE->eeDynamicVideoThumbFormats) AND isset($eeSFL_BASE_Env['ffMpeg']) AND $eeSFL_Settings['GenerateVideoThumbs'] == 'YES' ) {
					$eeShowThumbImage = TRUE;
				}
				if( $eeFileExt == 'pdf' AND isset($eeSFL_BASE_Env['ImkGs']) AND $eeSFL_Settings['GeneratePDFThumbs'] == 'YES' ) {
					$eeShowThumbImage = TRUE;
				}
				
				// Check Type
				if($eeShowThumbImage) { // Images use .jpg files

					$eePathParts = pathinfo($eeFilePath);
					$eeFileThumbURL = $eeSFL_Settings['FileListURL'];
					if($eePathParts['dirname']) { $eeFileThumbURL .= $eePathParts['dirname'] . '/'; }
					$eeFileThumbURL .= '.thumbnails/thumb_' . $eePathParts['filename'] . '.jpg';

				} else { // Others use our awesome .svg files
					
					if( !in_array($eeFileExt, $eeSFL_BASE->eeDefaultThumbFormats) ) {
						$eeDefaultThumb = '!default.svg'; // What the heck is this?
					} else {
						$eeDefaultThumb = $eeFileExt . '.svg';
					}
					
					$eeFileThumbURL = $eeSFL_BASE_Env['pluginURL'] . 'images/thumbnails/' . $eeDefaultThumb;
				}
			
				$eeOutput .= '<td class="eeSFL_Thumbnail">';
				
				if($eeFileThumbURL) { $eeOutput .= '<a href="' . $eeFileURL .  '"';
						
					$eeOutput .= '><img src="' . $eeFileThumbURL . '" width="64" height="64" alt="Thumb" /></a>'; }
				
				$eeOutput .= '</td>';
			}
			
			
			
			
			
			
			// NAME
			$eeOutput .= '<td class="eeSFL_FileName">';
			
			if($eeFileURL) {
				
				$eeRealFileName = $eeFileName; // Save for editing
				
				$eeOutput .= '<span class="eeSFL_RealFileName eeHide">' . $eeRealFileName . '</span>
				
				<p class="eeSFL_FileLink"><a class="eeSFL_FileName" href="' . $eeFileURL .  '" target="_blank">';
				
				// Strip the extension?
				if(!$eeAdmin AND $eeSFL_Settings['ShowFileExtension'] == 'NO') {
					$eeSFL_PathParts = pathinfo($eeFileName);
					$eeFileName = $eeSFL_PathParts['filename'];
				}
				
				// Replace hyphens with spaces?
				if(!$eeAdmin AND $eeSFL_Settings['PreserveSpaces'] == 'YES') {
					$eeFileName = eeSFL_BASE_PreserveSpaces($eeFileName); 
				}
				
				$eeOutput .= $eeFileName . '</a></p>';
				
				// Show File Description, or not.
				if(@$eeFileArray['FileDescription'] OR @$eeFileArray['SubmitterComments']) { 
					
					$eeClass = ''; // Show
					if(!@$eeFileArray['FileDescription']) {
						$eeFileArray['FileDescription'] = $eeFileArray['SubmitterComments']; // Show the submitter comment if no desc
					}
					
				} else {
					$eeClass = 'eeHide';
				}
				
				
				// This is always here because for js access
				$eeOutput .= '<p class="eeSFL_FileDesc ' . $eeClass . '">';
				
				if($eeAdmin OR $eeSFL_Settings['ShowFileDescription'] == 'YES') { $eeOutput .= stripslashes(@$eeFileArray['FileDescription']); }
				 
				$eeOutput .= '</p>';
				
				
				// Submitter Info
				if(@$eeFileArray['SubmitterName']) {
						
					if($eeAdmin OR $eeSFL_Settings['ShowSubmitterInfo'] == 'YES') {
						
						if( $eeFileArray['FileOwner'] >= 1 ) {
							
							$wpUserData = get_userdata($eeFileArray['FileOwner']);
							if($wpUserData->user_email) {
								$eeFileArray['SubmitterEmail'] = $wpUserData->user_email;
								$eeFileArray['SubmitterName'] = $wpUserData->first_name . ' ' . $wpUserData->last_name;
							}
						}
						
						$eeOutput .= '<p class="eeSFL_FileSubmitter">
						
						' . __('Submitted by', 'ee-simple-file-list') . ': <a href="mailto:' . $eeFileArray['SubmitterEmail'] . '">' . $eeFileArray['SubmitterName'] . '</a></p>';
					}
				}
				
				// File Actions   ------------------------------------------------------------------------------------
				
				if($eeAdmin OR $eeSFL_Settings['ShowFileActions'] == 'YES') { // Always show to Admin
					
					// Construct
					$eeOutput .= '
					
					<small class="eeSFL_ListFileActions">';
						
					// Open Action
					if($eeAdmin OR $eeSFL_Settings['ShowFileOpen'] == 'YES') {
					
						if(in_array($eeFileExt, $eeSFL_BASE->eeOpenableFileFormats)) {
							$eeOutput .= '<a class="eeSFL_FileOpen" href="' . $eeFileURL . '" target="_blank">' . __('Open', 'ee-simple-file-list') . '</a>';
						}
					}
					
					// Download Action
					if($eeAdmin OR $eeSFL_Settings['ShowFileDownload'] == 'YES') {
					
						$eeOutput .= '<a class="eeSFL_FileDownload" href="' . $eeFileURL . '" download="' . basename($eeFileURL) . '">' . __('Download', 'ee-simple-file-list') . '</a>';
					
					}
					
					// Copy Link Action
					if($eeAdmin OR $eeSFL_Settings['ShowFileCopyLink'] == 'YES') {
						
						$eeOutput .= '<a class="eeSFL_CopyLinkToClipboard" onclick="eeSFL_BASE_CopyLinkToClipboard(\''  . $eeFileURL .   '\')" href="#">' . __('Copy Link', 'ee-simple-file-list') . '</a>';														
					
					}
					
					// Front-End Manage or Admin
					if( ($eeAdmin OR $eeSFL_Settings['AllowFrontManage'] == 'YES') AND $eeSFL_BASE_ListRun == 1) {
						
						// if($eeAdmin) { $eeFileActions .= '<br />'; }								
						
						$eeOutput .= '<a href="" id="eeSFL_EditFile_' . $eeRowID . '" onclick="eeSFL_BASE_EditFile(' . $eeRowID . ')">' . 
						__('Edit', 'ee-simple-file-list') . '</a><a href="#" onclick="eeSFL_BASE_Delete(' . $eeRowID . ')">' . 
						__('Delete', 'ee-simple-file-list') . '</a>';
						
						if($eeAdmin) {
						
							$eeOutput .= '
							 <a class="eeDisabledAction" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Move', 'ee-simple-file-list') . '</a>
							 <a class="eeDisabledAction" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Users', 'ee-simple-file-list') . '</a>
							 <a class="eeDisabledAction" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Send', 'ee-simple-file-list') . '</a>';
						}
							
						$eeOutput .= '</small>'; // Close File List Actions Links
						
						// File Details to Pass to the Editor
						$eeOutput .= '
						
						<span class="eeHide eeFileSize">' . $eeFileSize . '</span>
						<span class="eeHide eeFileDateAdded">' . date_i18n( get_option('date_format'), strtotime( $eeFileArray['FileDateAdded'] ) ) . '</span>
						<span class="eeHide eeFileDateChanged">' . date_i18n( get_option('date_format'), strtotime( $eeFileArray['FileDateChanged'] ) ) . '</span>';
					
					} // END File Operations
			
				} // END File Actions	
			
			$eeOutput .= '</td>';
			
			
			
			// File Size
			if($eeAdmin OR $eeSFL_Settings['ShowFileSize'] == 'YES') {
			
				$eeOutput .= '<td class="eeSFL_FileSize">' . $eeFileSize . '</td>';
			}
			
			
			// File Modification Date
			if($eeAdmin OR $eeSFL_Settings['ShowFileDate'] == 'YES') {
				
				$eeOutput .= '<td class="eeSFL_FileDate">' . $eeFileDate . '</td>';
			}
			
			$eeOutput .= '</tr>';
		
			} // END If $fileURL
		
		$eeRowID++; // Bump the ID
	
		}

	} // END $eeFilearray

} // END $eeSFL_Files loop


$eeOutput .= '

</tbody>

</table>';
	
?>