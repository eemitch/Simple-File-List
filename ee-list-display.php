<?php // Simple File List - ee-list-display.php - mitchellbennis@gmail.com
	
// List files in the path defined within $eeSFL_FileListDir
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('That is Noncense! (' . basename(__FILE__) . ')' ); // Exit if nonce fails

global $eeSFLF_ListFolder;

// $eeSFL_Log[] = 'Loaded: ee-list-display';
// $eeSFL_Log[] = 'Listing File in: ' . $eeSFL_FileListDir;

if($eeSFLF) {
	if(!@$eeSFLF_ListFolder) { // If not already set up
		$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
		include(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_PathSetup.php');
	}
}

// Get the File List Transient
$eeSFL_Files = $eeSFL->eeSFL_createFileListArray($eeSFL->eeListID, $eeSFL_Config['FileListDir'], FALSE);
$eeSFL_Log['fileTransient'] = $eeSFL_Files;
$eeSFL_ListClass = 'eeSFL'; // The basic list's CSS class. Extensions might change this.
$eeSFL_AllowFrontManage = 'NO'; // Front-side freedom <--- TO DO


// Extension Check
if($eeSFLF) { 
	
	// Create a new folder, if needed
	if(@$_POST['eeSFLF_NewFolderName']) { $eeSFLF->eeSFLF_CreateFolder( $eeSFL_Config['FileListDir'] ); }
	
	// Run the File/Folder Listing and Sorting Engines
	$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
	include(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_ListSetup.php');

} else { // Default Sort <<<----  TO DO

	// $eeSFL_Files = $eeSFL->eeSFL_SortFiles($eeSFL_Files, $eeSFL_Config['SortBy'], $eeSFL_Config['SortOrder']);
}

// Getting Ready...
$eeSFL_Files = array_values($eeSFL_Files); // Reset Keys

// Extension Check
if($eeSFLS) {
	$eeSFL_FileTotalCount = count($eeSFL_Files); // Before search
	$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
	include(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-search-processor.php'); // Run the Search Processor
	$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
	include(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-pagination-processing.php'); // Run Pagination Processing
}

$eeSFL_FileCount = count($eeSFL_Files); // How Many Here?

// User Messaging	
if(@$eeSFL_Log['messages']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['messages'], 'updated'); // Add to the output -- TO DO - Make a function
	$eeSFL_Log['messages'] = array(); // Clear
}	
if(@$eeSFL_Log['errors']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['errors'], 'error'); // Add to the output
	$eeSFL_Log['errors'] = array(); // Clear
}

$eeSFL_ActionNonce = wp_create_nonce('eeSFL_ActionNonce'); // Security for Ajax







// DISPLAY ===================================================

// Listing the files, all sorted and ready to go!

$eeOutput .= '

<!-- File List -->
<span class="eeSFL_Hide" id="eeSFL_ID">' . $eeSFL->eeListID . '</span>
<span class="eeSFL_Hide" id="eeSFL_ActionNonce">' . $eeSFL_ActionNonce . '</span>
<script>
	var eeSFL_PluginURL = "' . $eeSFL_Env['pluginURL'] . '";
	var eeSFL_FileListPath = "' . urlencode($eeSFL_Config['FileListDir'] . $eeSFLF_ListFolder) . '";
</script>
';

if($eeSFLS) {
	$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
	include(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-search-form.php');
}



if($eeSFLF) {
	
	
	// MOVE this to eeSFLF an INCLUDE <<<---- TO DO
	
	if($eeAdmin OR $eeSFL_Config['ShowBreadCrumb'] == 'YES') {
		if($eeListNumber == 1) {
			$eeOutput .= $eeSFLF_FunctionBar;
		}
	}
	
	$eeOutput .= '<span class="eeSFL_Hide" id="eeSFLF_ListFolder">' . $eeSFLF_ListFolder . '</span>
		<span class="eeSFL_Hide" id="eeSFLF_MoveNonce">' . $eeSFLF_MoveNonce . '</span>';
}

if($eeAdmin) {

	$eeOutput .= '<a href="#" class="button eeButton" id="uploadFilesButton">' . __('Upload Files', 'ee-simple-file-list') . '</a>';
}


// TABLE HEAD ==================================================================================================

if(count($eeSFL_Files)) { 
	
	$eeRowID = 0; // Assign an ID number to each row
	
	$eeOutput .= '<table class="eeFiles">';
	
	if($eeSFL_Config['ShowHeader'] == 'YES' OR $eeAdmin) { $eeOutput .= '<thead><tr>';
							
		if($eeSFL_Config['ShowFileThumb'] == 'YES') { $eeOutput .= '<th class="eeSFL_Thumbnail">' . __('Thumb', 'ee-simple-file-list') . '</th>'; }
		
		$eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileName">' . __('Name', 'ee-simple-file-list') . '</th>';
									
		if($eeSFL_Config['ShowFileSize'] == 'YES' OR $eeAdmin) { $eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileSize">' . __('Size', 'ee-simple-file-list') . '</th>'; }
									
		if($eeSFL_Config['ShowFileDate'] == 'YES' OR $eeAdmin) { $eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileDate">' . __('Date', 'ee-simple-file-list') . '</th>'; } 
		
		elseif($eeSFL_Config['AllowFrontManage'] == 'YES' AND $eeListNumber == 1 ) { $eeOutput .= '<th></th>'; }
		
		$eeOutput .= '</tr></thead>';
	}						
	
	$eeOutput .= '<tbody>';
					
	$eeFileCount = 0; // Reset
	$eeDateFormat = get_option('date_format');
	$eeListPosition = FALSE; // eeSFLS
	
	$eeSFL_Log[] = 'Listing Files...';
							
	// Loop through array
	foreach($eeSFL_Files as $eeFileKey => $eeFileInfo) {
		
		// Go
		if( strpos($eeFileInfo, '|') ) {
		
			$eeFileArray = explode('|', $eeFileInfo); // Make array
			$eeFilePath = $eeFileArray[0]; // Path relative to FileListDir
			$eeFileName = basename($eeFilePath); // Just the name
			$eeFileDate = date_i18n( $eeDateFormat, strtotime( $eeFileArray[1] ) ); // The mod date, make nice per WP config
			$eeFileSize = eeSFL_FormatFileSize($eeFileArray[2]); // The file size made nice too
			
			// Extension Check
			if(!$eeListPosition AND $eeListPosition !== 0) { // eeSFLS
				$eeListPosition = $eeFileKey; // Get the first key
			}
			
			// Ready, set...
			$eeIsFile = FALSE;
			$eeIsFolder = FALSE;
			$eeFileURL = FALSE;
			$eeFileExt = FALSE;
			$eeFileThumbURL = FALSE;
			
			// Go
			if($eeFileName) {
			
				$eeFileCount++; // Bump the count
				
				if( strpos($eeFilePath, '.') ) { // This is a file
					
					$eeIsFile = TRUE;
					
					$eeFileURL = $eeSFL_Config['FileListURL'] . $eeFileName; // Clickable URL
					$eeFileURL = str_replace('://', '\\:', $eeFileURL); // Save this
					$eeFileURL = str_replace('//', '/', $eeFileURL); // Remove double slashes
					$eeFileURL = str_replace('\\:', '://', $eeFileURL); // Restore that
					
					$eeFileExt = substr(strrchr($eeFileName,'.'), 1); // Get Extension
				
				} elseif($eeSFLF) {
					
					$eeIsFolder = TRUE;
					$eeFileExt = 'folder';
					if(!$eeAdmin AND $eeListNumber > 1) { continue; } // Disable folder support for additional lists
					$eeFileURL = $eeSFLF->eeSFLF_GetFolderURL($eeFilePath, $eeSFLF_ShortcodeFolder); // Extension required
					
				} else {
					
					$eeFilePath = FALSE;
				}
				
				
				// Check for the first file on the admin side.
				if($eeIsFile AND $eeAdmin AND $eeFileCount = 1 ) {
					
					// exit($eeFileURL);
					
					$eeFileURL = eeSFL_UrlExists($eeFileURL); // Sets to FALSE if file not found.
					
					if(!$eeFileURL) {
						$eeError = 'First file is not reachable';
						$eeSFL_Log['errors'][] = $eeError;
					}
				}
				
				// Add an ID to use in javascript
				$eeOutput .= "\n\r" . '<tr id="eeSFL_RowID-' . $eeRowID . '">';
				
				
				// Start The List --------------------------------------------------------------
				
				
				// Thumbnail
				if($eeSFL_Config['ShowFileThumb'] == 'YES') {
					
					// Create Thumbnail Path
					$eeIsImage = in_array($eeFileExt,  $eeSFL->eeDynamicImageThumbFormats);
					$eeIsVideo = in_array($eeFileExt,  $eeSFL->eeDynamicVideoThumbFormats);
					
					// Check Type
					if($eeIsImage OR $eeIsVideo) { // Images use .jpg files
						
						$eePathParts = pathinfo($eeSFL_Config['FileListDir'] . $eeFilePath);
						$eeFileNameOnly = $eePathParts['filename'];
						
						$eeFileThumbURL = $eeSFL_Config['FileListURL'] . '.thumbnails/thumb_' . $eeFileNameOnly . '.jpg';
					
					} else { // Others use our .svg files
						
						$eeFileThumbURL = $eeSFL_Env['wpPluginsURL'] . $eeSFL->eePluginNameSlug . '/images/thumbnails/' . $eeFileExt . '.svg';
					}
				
					$eeOutput .= '<td class="eeSFL_Thumbnail">';
					
					if($eeFileThumbURL) { $eeOutput .= '<a href="' . $eeFileURL .  '"';
						
						if(!$eeIsFolder) { $eeOutput .= ' target="_blank"'; }
							
						$eeOutput .= '><img src="' . $eeFileThumbURL . '" width="64" height="64" /></a>'; }
					
					$eeOutput .= '</td>';
				}
				
				
				
				// NAME
				$eeOutput .= '<td class="eeSFL_FileName">';
				
				if($eeFileURL) {
					
					$eeOutput .= '<p class="eeSFL_FileLink"><a class="eeSFL_FileName" href="' . $eeFileURL .  '"';
					if(!$eeIsFolder) {
						$eeOutput .= ' target="_blank"';
					}
					$eeOutput .= '>';
					
					
					// Extension Check
					if($eeSFLF) { // Show a small folder icon before the name if thumbs are not used.
						
						if($eeIsFolder AND $eeSFL_Config['ShowFileThumb'] != 'YES') {
							$eeOutput .= '<b class="eeSFL_FolderIconSmall">' . $eeSFLF_FolderIcon . '</b> '; // <<<--------- MOVE TO INCLUDE
						}
					
						// Path display for searching in folders
						if(@$_POST['eeSFLS_Searching']) {
							$eeFileName = trim( str_replace('/', ' / ', $eeFilePath) );
						}
					}
					
					
					
					$eeOutput .= $eeFileName . '</a></p>';
					
					
					// File Actions
					if($eeIsFile) { //  ------------------------------------------------------------------------------------
						
						// Construct
						$eeFileActions = '
						
							<div class="eeSFL_FileRenameEntry">
							<span class="eeSFL_Hide eeOldFileName">' . $eeFileName . '</span>
							<input required="required" type="text" class="eeNewFileName" name="eeNewFileName" value="' . $eeFileName . '" size="32" />
							<input type="submit" value="Rename" class="button" onclick=\'eeSFL_FileAction(' . $eeRowID . ',"Rename")\' />
							<a href="#" class="button" onclick="eeSFL_CancelRename()">Cancel</a></div>
						
							<small class="eeSFL_ListFileActions">
							
							<a href="' . $eeFileURL . '" target="_blank">' . __('Open', 'ee-simple-file-list') . '</a>
								
							| <a href="' . $eeFileURL . '" download="' . $eeFileName  . '">' . __('Download', 'ee-simple-file-list') . '</a>';
						
						
						// File Actions Display
						if($eeSFL_Config['ShowFileActions'] == 'YES' OR $eeAdmin) {
							$eeOutput .= $eeFileActions; // Always show to Admin
						}
						
						// Append Addition (admin or authorized) Actions
						if($eeAdmin OR $eeSFL_Config['AllowFrontManage'] == 'YES') {
							
							$eeFileActions = ' | <a href="#" onclick="eeSFL_Rename(' . $eeRowID . ')">' . __('Rename', 'ee-simple-file-list') . '</a>
							
								 | <a href="#" onclick="eeSFL_Delete(' . $eeRowID . ')">' . __('Delete', 'ee-simple-file-list') . '</a>';
								 
								 
								 
							
							if($eeSFLF) {
								$eeFileActions .= ' | <a id="eeSFLF_moveLink_' . $eeRowID . '" href="#" onclick="eeSFLF_MoveFileDisplay(' . $eeRowID . ')">' . __('Move', 'ee-simple-file-list-folders') . '</a>';
							}
							
							$eeOutput .= $eeFileActions;
						}
								
						$eeOutput .= '</small>';
						
						if($eeAdmin AND $eeSFLF) { $eeOutput .= $eeSFLF_FolderOptionsDisplay; } // Move-to-folder select box
						
					
					} else { // Folder
						
						if($eeAdmin) { // Only Admins can rename
							
							$eeOutput .= '<br /><small class="eeSFL_ListFileActions">
								<a href="#" onclick="eeSFL_Rename(' . $eeRowID . ')">' . __('Rename', 'ee-simple-file-list') . '</a>
								<a href="#" onclick="eeSFL_Delete(' . $eeRowID . ')">' . __('Delete', 'ee-simple-file-list') . '</a>
							</small>';
						}
						
					}
					
				}
				
				$eeOutput .= '</td>';
				
				
				
				
				
				// File Size
				if($eeSFL_Config['ShowFileSize'] == 'YES' OR $eeAdmin) {
				
					$eeOutput .= '<td class="eeSFL_FileSize">';
					
					if($eeIsFile) {
						
						$eeOutput .= $eeFileSize;
						
					} else {
						
						if($eeSFL_Config['ShowFolderSize'] == 'YES') {
							$eeOutput .= $eeSFLF->eeSFLF_GetFolderSize($eeSFL_Config['FileListDir'] . $eeFilePath);
						} else {
							$eeOutput .= __('Folder', 'ee-simple-file-list');
						} 
					}
					
					$eeOutput .= '</td>';
				}
				
				
				
				
				
				// File Modification Date
				if($eeSFL_Config['ShowFileDate'] == 'YES' OR $eeAdmin) {
					
					$eeOutput .= '<td class="eeSFL_FileDate">' . $eeFileDate . '</td>';
				}
				
				
				$eeOutput .= "</tr>\n";
			}
		}
		
		$eeRowID++; // Bump the ID	
			
	} // END loop
	
	
	$eeMsg = __('Number of Items', 'ee-simple-file-list') . ': ' . $eeFileCount . ' | '  . __('Sorted by', 'ee-simple-file-list') . ' ' . $eeSFL_Config['SortBy'];
	$eeSFL_Log[] = $eeMsg;
	
	$eeOutput .= '</tbody></table>
	
	<p class="eeSFL_Hide">
			<span id="eeSFL_FileCount">' . $eeFileCount . '</span>
		</p>'; // This allows javascript to access the info
	
	if($eeAdmin OR $eeSFL_Config['AllowFrontManage']) { $eeOutput .= '<p class="eeFileListInfo">' . $eeMsg . '</p>'; }
	
	
	// Pagination Controls
	if($eeSFLS) {
		$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
		include(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-pagination-display.php');
	}
 
} else {
	
	$eeSFL_Log[] = 'There are no files here :-(';
	
	if($eeAdmin OR $eeSFL_Config['AllowFrontManage']) {
		$eeOutput .= '<div id="eeSFL_noFiles"><p>&#8593; ' . __('Upload some files and they will appear here.', 'ee-simple-file-list') . '</p></div>';
	}
}


?>