<?php // Simple File List Script: ee-list-display.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_Files = array();
$eeFileArray = FALSE;
$eeSFL_ListClass = 'eeSFL'; // The basic list's CSS class. Extensions might change this.
$eeClass = ''; // Meaning, CSS class
$eeSFL_AllowFrontManage = 'NO'; // Front-side freedom
$eeSendFilesArray = array(); // Used for the Add More Files in the Send Files overlay
$eeSFL_SendFile_AddFileArray = array(); // We use this for the Send File function
$eeUploadedFiles = FALSE;
$eeDateFormat = get_option('date_format');
$eeSFL_ActionNonce = wp_create_nonce('eeSFL_ActionNonce'); // Security for Ajax
global $eeSFL_FREE_ListRun;

$eeSFL_FREE_Log['RunTime'][] = 'Loaded: ee-list-display';

// echo '<pre>'; print_r($_POST); echo '</pre>';

// Scan the Disk
$eeSFL_Files = $eeSFL_FREE->eeSFL_UpdateFileListArray();

if(!is_array($eeSFL_Files)) { $eeSFL_Files = array(); } // PHP 8 Fix

// Save for later
$eeSFL_FileTotalCount = 0;
$eeSFL_ItemTotalCount = $eeSFL_FileTotalCount; //  + $eeSFL_FolderTotalCount
$eeSFL_FileTotalCount = count($eeSFL_Files);


// Check for Upload Job
if(count($eeSFL_FREE_Env['UploadedFiles'])) {
	
	foreach( $eeSFL_Files as $eeThisKey => $eeFileArray ) {
		
		if( in_array($eeFileArray['FilePath'], $eeSFL_FREE_Env['UploadedFiles']) ) {
			$eeUploadedFiles[] = $eeFileArray;
		}
	}
	
	if(count($eeUploadedFiles)) {
		$eeSFL_Files = $eeUploadedFiles;
	} else {
		$eeSFL_Files = array();
		$eeSFL_FREE_Log['errors'][] = 'Upload Processing Error.';
		$eeSFL_FREE_Log['errors'][] = $eeSFL_FREE_Env['UploadedFiles'];
	}	
}

if( $eeAdmin AND !is_array($eeUploadedFiles) AND count($eeSFL_FREE_Log['messages']) === 0 ) {
	$eeSFL_FREE_Log['messages'] = $eeUpSell;
}


// User Messaging
if(isset($eeSFL_FREE_Log['messages'])) {	
	if($eeSFL_FREE_Log['messages'] AND $eeSFL_FREE_ListRun == 1) { 
		$eeOutput .=  eeSFL_FREE_ResultsDisplay($eeSFL_FREE_Log['messages'], 'notice-success');
		$eeSFL_FREE_Log['messages'] = array(); // Clear
	}
}
if(isset($eeSFL_FREE_Log['errors'])) {		
	if($eeSFL_FREE_Log['errors']) { 
		$eeOutput .=  eeSFL_FREE_ResultsDisplay($eeSFL_FREE_Log['errors'], 'notice-error');
		$eeSFL_FREE_Log['errors'] = array(); // Clear
	}
}


// DISPLAY ===================================================

$eeOutput .= '

<!-- File List -->
<span class="eeSFL_Hide" id="eeSFL_ActionNonce">' . $eeSFL_ActionNonce . '</span>
<script>
	var eeSFL_PluginURL = "' . $eeSFL_FREE_Env['pluginURL'] . '";
	var eeSFL_FileListDir = "' . $eeSFL_Settings['FileListDir'] . '";
</script>
';

$eeURL = eeSFL_FREE_GetThisURL();

// Back-side Buttons
if($eeAdmin) {
	
	$eeOutput .= '<div id="eeAdminActions">
	
	<p class="eeRight">
	
		<span class="eeHide" id="eeSFL_UploadFilesButtonSwap">' . __('Cancel Upload', 'ee-simple-file-list') . '</span>
		<a href="#" class="button eeButton" id="eeSFL_UploadFilesButton">' . __('Upload Files', 'ee-simple-file-list') . '</a>
		
		<a href="#" class="button eeButton" id="eeSFL_ReScanButton">' . __('Re-Scan Files', 'ee-simple-file-list') . '</a>
	
		<a href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" class="button eeButton" >' . __('Create Folder', 'ee-simple-file-list') . '</a>

	</p>';
	
	// If showing just-uploaded files
	if($eeSFL_Uploaded) { 
		
		$eeOutput .= '
		
		<p class="eeSFL_ListMeta"><a href="' . eeSFL_FREE_AppendProperUrlOp($eeURL) . '" class="button eeButton" id="eeSFL_BacktoFilesButton">&larr; ' . __('Back to the Files', 'ee-simple-file-list') . '</a></p>';
	
	} else {
		
		// Calc Date Last Changed
		$eeArray = array();
		if(count($eeSFL_Files)) {
			foreach( $eeSFL_Files as $eeKey => $eeFileArray) { $eeArray[] = $eeFileArray['FileDateChanged']; }
			rsort($eeArray); // Most recent at the top	
		} else {
			$eeArray[] = date('Y-m-d H:i:s'); // today
		}
		
		$eeOutput .= '
		
		<p class="eeSFL_ListMeta">' . __('Files', 'ee-simple-file-list') . ': ' . $eeSFL_FileTotalCount . ' | '  . 
		__('Sorted by', 'ee-simple-file-list') . ': ' . ucwords($eeSFL_Settings['SortBy']) . ' (' . $eeSFL_Settings['SortOrder'] . ')<br />
		' . __('Last Changed', 'ee-simple-file-list') . ': ' . date_i18n( $eeDateFormat, strtotime( $eeArray[0] ) );
		
		$eeOutput .= '</p>';
	}
	
	$eeOutput .= '
	
		<br class="eeClearFix" />
	
	</div>';

} elseif($eeSFL_Uploaded AND $eeSFL_FREE_ListRun == 1) {
	
	$eeOutput .= '<p class="eeSFL_ListMeta"><a href="' . eeSFL_FREE_AppendProperUrlOp($eeURL) . 'ee=1" class="button eeButton" id="eeSFL_BacktoFilesButton">&larr; ' . 
		__('Back to the Files', 'ee-simple-file-list') . '</a></p>';
}

// $eeSFL_Files = array_values($eeSFL_Files);

// echo '<pre>'; print_r($eeSFL_Files); echo '</pre>';	


// TABLE HEAD ==================================================================================================

if( isset($eeSFL_Files) ) {
	
	if(!is_array($eeSFL_Files) OR !count($eeSFL_Files)) { return; } // Bail if no files
	
	$eeRowID = '0'; // Assign an ID number to each row
	
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

		
		$eeOutput .= '</tr></thead>';
	}						
	
	$eeOutput .= '<tbody>';
					
	$eeFileCount = 0; // Reset
	$eeRowID = 0;
	$eeListPosition = FALSE; // eeSFLS
	
	$eeSFL_FREE_Log['RunTime'][] = 'Listing Files...';
	
	if(is_array($eeSFL_Files)) {
							
		// Loop through array
		foreach($eeSFL_Files as $eeFileKey => $eeFileArray) { // <<<---------------------------- BEGIN FILE LIST LOOP ----------------<<<
			
			$eeRowID ++; // We start with one ...
			
			// Go
			if( is_array($eeFileArray)) {
	
				$eeFilePath = $eeFileArray['FilePath']; // Path relative to FileListDir
				$eeFileName = basename($eeFilePath); // Just the name
				$eeFileDate = date_i18n( $eeDateFormat, strtotime( $eeFileArray['FileDateChanged'] ) ); // The mod date, make nice per WP config
				$eeFileDateAdded = date_i18n( $eeDateFormat, strtotime( $eeFileArray['FileDateAdded'] ) );
				if($eeSFL_Settings['SortBy'] == 'Date') { $eeFileDate = $eeFileDateAdded; }
				$eeFileSize = eeSFL_FREE_FormatFileSize($eeFileArray['FileSize']); // The file size made nice too
				
				// Extension Check
				if(!$eeListPosition AND $eeListPosition !== 0) { // eeSFLS
					$eeListPosition = $eeFileKey; // Get the first key
				}
				
				// Ready, set...
				$eeIsFile = FALSE;
				$eeFileURL = FALSE;
				$eeFileExt = FALSE;
				$eeFileThumbURL = FALSE;
				
				// Deny Folder Listing
				if(strpos($eeFileArray['FilePath'], '/')) {
					$eeFileName = FALSE; // Will not show
				}
				
				// Go
				if($eeFileName) {
				
					$eeFileCount++; // Bump the file count
					
					if( strpos($eeFilePath, '.') ) { // This is a file
						
						$eeIsFile = TRUE;
						
						// $eeSFL_SendFile_AddFileArray[$eeFileCount] = $eeFilePath; // Used for Send -> Add Files
						
						$eeFileURL = $eeSFL_FREE_Env['wpSiteURL'] . $eeSFL_Settings['FileListDir'] . $eeFilePath; // Clickable URL
						
						$eeFileURL = str_replace('://', '\\:', $eeFileURL); // Save this
						$eeFileURL = str_replace('//', '/', $eeFileURL); // Remove double slashes
						$eeFileURL = str_replace('\\:', '://', $eeFileURL); // Restore that
						$eeFileExt = $eeFileArray['FileExt']; // Get Extension
					
					} else {
						
						$eeFilePath = FALSE;
					}
					
					
					// Skip names hidden via shortcode
					if(@$eeSFL_HideName) {
						
						$eeArray = explode(',', $eeSFL_HideName);
						
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
					if(@$eeSFL_HideType) {
						if(strpos($eeSFL_HideType, $eeFileExt) OR strpos($eeSFL_HideType, $eeFileExt) === 0 ) { 
							continue; // Go to next file
						}
					}
					
					
					// Start The List --------------------------------------------------------------
				
					$eeOutput .= '
					
					<tr id="eeSFL_RowID-' . $eeRowID . '">'; // Add an ID to use in javascript
					
					
					// Thumbnail
					if($eeAdmin OR $eeSFL_Settings['ShowFileThumb'] == 'YES') {
						
						$eeShowThumbImage = FALSE;
						
						if( in_array($eeFileExt,  $eeSFL_FREE->eeDynamicImageThumbFormats) AND $eeSFL_Settings['GenerateImgThumbs'] == 'YES' ) {
							$eeShowThumbImage = TRUE;
						}
						if( in_array($eeFileExt,  $eeSFL_FREE->eeDynamicVideoThumbFormats) AND isset($eeSFL_FREE_Env['ffMpeg']) AND $eeSFL_Settings['GenerateVideoThumbs'] == 'YES' ) {
							$eeShowThumbImage = TRUE;
						}
						if( $eeFileExt == 'pdf' AND isset($eeSFL_FREE_Env['ImkGs']) AND $eeSFL_Settings['GeneratePDFThumbs'] == 'YES' ) {
							$eeShowThumbImage = TRUE;
						}
						
						// Check Type
						if($eeShowThumbImage) { // Images use .jpg files

							$eePathParts = pathinfo($eeFilePath);
							$eeFileThumbURL = $eeSFL_Settings['FileListURL'];
							if($eePathParts['dirname']) { $eeFileThumbURL .= $eePathParts['dirname'] . '/'; }
							$eeFileThumbURL .= '.thumbnails/thumb_' . $eePathParts['filename'] . '.jpg';

						} else { // Others use our awesome .svg files
							
							if( !in_array($eeFileExt, $eeSFL_FREE->eeDefaultThumbFormats) ) {
								$eeDefaultThumb = '!default.svg'; // What the heck is this?
							} else {
								$eeDefaultThumb = $eeFileExt . '.svg';
							}
							
							$eeFileThumbURL = $eeSFL_FREE_Env['pluginURL'] . 'images/thumbnails/' . $eeDefaultThumb;
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
						
						$eeOutput .= '<span class="eeSFL_RealFileName eeSFL_Hide">' . $eeRealFileName . '</span>
						
						<p class="eeSFL_FileLink"><a class="eeSFL_FileName" href="' . $eeFileURL .  '" target="_blank">';
						
						// Strip the extension?
						if(!$eeAdmin AND $eeSFL_Settings['ShowFileExtension'] == 'NO') {
							$eeSFL_PathParts = pathinfo($eeFileName);
							$eeFileName = $eeSFL_PathParts['filename'];
						}
						
						// Replace hyphens with spaces?
						if(!$eeAdmin AND $eeSFL_Settings['PreserveSpaces'] == 'YES') {
							$eeFileName = eeSFL_FREE_PreserveSpaces($eeFileName); 
						}
						
						$eeOutput .= $eeFileName . '</a></p>';
						
						// Show File Description, or not.
						if(@$eeFileArray['FileDescription'] OR @$eeFileArray['SubmitterComments']) { 
							
							$eeClass = ''; // Show
							if(!@$eeFileArray['FileDescription']) {
								$eeFileArray['FileDescription'] = $eeFileArray['SubmitterComments']; // Show the submitter comment if no desc
							}
							
						} else {
							$eeClass = 'eeSFL_Hide';
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
							$eeFileActions = '
							
							<small class="eeSFL_ListFileActions">';
								
							// Open Action
							if($eeAdmin OR $eeSFL_Settings['ShowFileOpen'] == 'YES') {
							
								if(in_array($eeFileExt, $eeSFL_FREE->eeOpenableFileFormats)) {
									$eeFileActions .= '<a class="eeSFL_FileOpen" href="' . $eeFileURL . '" target="_blank">' . __('Open', 'ee-simple-file-list') . '</a> | ';
								}
							}
							
							// Download Action
							if($eeAdmin OR $eeSFL_Settings['ShowFileDownload'] == 'YES') {
							
								$eeFileActions .= '<a class="eeSFL_FileDownload" href="' . $eeFileURL . '" download="' . basename($eeFileURL) . '">' . __('Download', 'ee-simple-file-list') . '</a> | ';
							
							}
							
							// Copy Link Action
							if($eeAdmin OR $eeSFL_Settings['ShowFileCopyLink'] == 'YES') {
								
								$eeFileActions .= '<a class="eeSFL_CopyLinkToClipboard" onclick="eeSFL_FREE_CopyLinkToClipboard(\''  . $eeFileURL .   '\')" href="#">' . __('Copy Link', 'ee-simple-file-list') . '</a> | ';														
							
							}
							
							// Append Addition (admin or authorized) Actions
							if( ($eeAdmin OR $eeSFL_Settings['AllowFrontManage'] == 'YES') AND $eeSFL_FREE_ListRun == 1) {
								
								if($eeAdmin) { $eeFileActions .= '<br />'; }								
								
								$eeFileActions .= '<a href="" id="eeSFL_EditFile_' . $eeRowID . '" onclick="eeSFL_FREE_EditFile(' . $eeRowID . ')">' . 
								__('Edit', 'ee-simple-file-list') . '</a> | <a href="#" onclick="eeSFL_FREE_Delete(' . $eeRowID . ')">' . 
								__('Delete', 'ee-simple-file-list') . '</a>';
								
								if($eeAdmin) {
								
									$eeFileActions .= ' | 
									 <a class="eeDimmedLink" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Move', 'ee-simple-file-list') . '</a> | 
									 <a class="eeDimmedLink" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Users', 'ee-simple-file-list') . '</a> | 
									 <a class="eeDimmedLink" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Send', 'ee-simple-file-list') . '</a>';
									
								}
							
								// Strip trailing pipe if needed
								if(substr($eeFileActions, -2) == '| ') {
									$eeFileActions = substr($eeFileActions, 0, -3);
								}
									
								$eeFileActions .= '</small>'; // Close action links
							
								
							
								// Expanding Inputs
								if($eeAdmin OR $eeSFL_Settings['AllowFrontManage'] == 'YES') {
									
									// Javascript-powered Drop-Down Box
									$eeFileActions .= '<div class="eeSFL_EditFileWrap" id="eeSFL_EditFileWrap_' . $eeRowID . '">
									
									<h4>' . __('Edit Details', 'ee-simple-file-list') . '</h4>';
									
									$eeFileActions .= '<p><label for="eeSFL_NewFileName_' . $eeRowID . '">' . __('File Name', 'ee-simple-file-list') . '</label>
									<input required="required" type="text" class="eeNewFileName" name="eeNewFileName" value="' . $eeRealFileName . '" size="32" id="eeSFL_NewFileName_' . $eeRowID . '" />
										<a class="button" href="#" onclick="eeSFL_FREE_EditRename(' . $eeRowID . ')">' . __('Save', 'ee-simple-file-list') . '</a></p>
										
									<p><label for="eeSFL_FileDesc_' . $eeRowID . '">' . __('Description', 'ee-simple-file-list') . '</label>
										<span class="eeSFL_SavedDesc">' . @$eeFileArray['FileDescription'] . '</span>
										<input type="text" class="eeSFL_NewFileDesc" name="eeSFL_FileID_' . $eeFileKey . '" value="' . @$eeFileArray['FileDescription'] . '" size="32" id="eeSFL_FileDesc_' . $eeRowID . '" />
											<a class="button" href="#" onclick="eeSFL_FREE_EditDesc(' . $eeRowID . ')">' . __('Save', 'ee-simple-file-list') . '</a></p>
									
									<p class="eeCenter"><small>' . __('Added', 'ee-simple-file-list') . ': ' . date_i18n( $eeDateFormat, strtotime( $eeFileArray['FileDateAdded'] ) ) . '<br />
										' . __('Modified', 'ee-simple-file-list') . ': ' . date_i18n( $eeDateFormat, strtotime( $eeFileArray['FileDateChanged'] ) ) . '<br />';
									
									$eeFileActions .= __('Size', 'ee-simple-file-list') . ': ' . $eeFileSize . '</small></p>
										
									</div>';
								}
							
							} // END File Operations
							
							$eeOutput .= $eeFileActions;
					
						} // END FileActions	
					
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
			
				} // If $filename
		
			} // END IF is_array $eeFileArray
	
		} // END $eeSFL_Files loop
		
	} // END IF is_array $eeSFL_Files
	
	
	$eeOutput .= '
	
	</tbody></table>
	
	<p class="eeSFL_Hide">
			<span id="eeSFL_FilesCount">' . $eeFileCount . '</span>
		</p>'; // This allows javascript to access the info
	
	$eeSFL_FREE_Env['FileLists'] = ''; // Remove to clean up display
	
} else {
	
	$eeSFL_FREE_Log['RunTime'][] = 'There are no files here :-(';
	
	if($eeAdmin) {
		$eeOutput .= '<div id="eeSFL_noFiles"><p>&#8593; ' . __('Upload some files and they will appear here.', 'ee-simple-file-list') . '</p></div>';
	}
}

?>