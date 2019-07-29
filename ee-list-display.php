<?php // Simple File List - ee-list-display.php - mitchellbennis@gmail.com
	
// List files in the path defined within $eeSFL_FileListDir
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('That is Noncense! (' . basename(__FILE__) . ')' ); // Exit if nonce fails

// $eeSFL_Log[] = 'Loaded: ee-list-display';
// $eeSFL_Log[] = 'Listing File in: ' . $eeSFL_FileListDir;

if($eeSFLF) {
	if(!@$eeSFLF_ListFolder) { // If not already set up
		$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
		include(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_PathSetup.php');
	}
}

$eeSFL_Files = array(); // This will be our file list
$eeSFL_FileCount = 0;
$eeSFL_ListClass = 'eeSFL'; // The basic list's CSS class. Extensions might change this.

$eeSFL_FrontSideManage = 'NO'; // Front-side freedom <--- TO DO

// If Delete Files...
if(@$_POST['eeDeleteFile']) {

	foreach(@$_POST['eeDeleteFile'] as $eeSFL_Key => $eeSFL_File) {
		
		// Detect upward path traversal
		$realPath = realpath( $eeSFL_FileListDir ) . '/' . basename($eeSFL_File); // Defy traversal
		$userPath = realpath( $eeSFL_FileListDir . $eeSFL_File);  // This could be problematic
		
		if ($userPath === false OR strpos($userPath, $realPath) !== 0) { // Must match
		    $eeSFL_Log['errors'] = 'Error 99';
		    break; // Bad guy found, bail out.
		}
		
		if( strpos($eeSFL_File, '.') ) { // Gotta be a File - Looking for the dot rather than using is_file() for better speed
		
			if(unlink($eeSFL_Config['FileListDir'] . $eeSFL_File)) {
				
				$eeSFL_Msg = __('Deleted the File', 'ee-simple-file-list') . ' &rarr; ' . $eeSFL_File;
				$eeSFL_Log[] = $eeSFL_Msg;
				$eeSFL_Log['messages'][] = $eeSFL_Msg;
				
				$eeSFL_Thumb = $eeSFL_Config['FileListDir'] . '.thumbnails/thumb_' . $eeSFL_File;
				
				if(!is_file($eeSFL_Thumb)) { // Not found ?
					
					$eeExt = strrchr($eeSFL_File, '.'); // Get the extension
					$eeSFL_Thumb = str_replace($eeExt, '.png', $eeSFL_Thumb); // Change to PNG (video thumbs)
				}
				
				if($eeSFL_Thumb) {
					
					if(!unlink($eeSFL_Thumb)) {
						$eeSFL_Msg = __('Thumbnail File Delete Failed', 'ee-simple-file-list') . ':' . $eeSFL_File;
						$eeSFL_Log[] = $eeSFL_Msg;
						$eeSFL_Log['errors'][] = $eeSFL_Msg;
					}
				}	
				
			} else {
				$eeSFL_Msg = __('File Delete Failed', 'ee-simple-file-list') . ':' . $eeSFL_File;
				$eeSFL_Log[] = $eeSFL_Msg;
				$eeSFL_Log['messages'][] = $eeSFL_Msg;
			}
		}
	}
	
	// Re-index the File List
	$eeSFL_Files = $eeSFL->eeSFL_ListFiles( $eeSFL_Config['FileListDir'] , 'Re-Index', 1);
	
} // END Delete Processor




// If Renaming a File/Folder
if(@$_POST['eeNewFileName']) { 
	
	$eeOldFileName = filter_var($_POST['eeOldFileName'], FILTER_SANITIZE_STRING);
	$eeNewFileName = filter_var($_POST['eeNewFileName'], FILTER_SANITIZE_STRING);
	
	$eeNewFileName = eeSFL_SanitizeFileName($eeNewFileName);
	
	if($eeNewFileName) {
			
		$eeSFL_Log[] = 'Renaming: ' . $eeOldFileName . ' to ' . $eeNewFileName;
		
		if( !@rename($eeSFL_FileListDir . $eeOldFileName, $eeSFL_FileListDir . $eeNewFileName) ) {
			$eeSFL_Log['errors'][] = 'Could Not Rename ' . $eeOldFileName . ' to ' . $eeNewFileName;
		}
	}
}


// Extension Check
if($eeSFLF) { 
	
	// Create a new folder, if needed
	if(@$_POST['eeSFLF_NewFolderName']) { $eeSFLF->eeSFLF_CreateFolder( $eeSFL_Config['FileListDir'] ); }
	
	$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
	
	// Run the File/Folder Listing and Sorting Engines
	include(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_ListSetup.php');	

} else {

	// Default File Listing and Sorting Engines
	$eeSFL_Files = $eeSFL->eeSFL_ListFiles( $eeSFL_Config['FileListDir'] );
	$eeSFL_Files = $eeSFL->eeSFL_SortFiles($eeSFL_Config['FileListDir'] ,$eeSFL_Files, $eeSFL_Config['SortBy'], $eeSFL_Config['SortOrder']);
}

// Reset the array index
$eeSFL_Files = array_values($eeSFL_Files); // Nice Array
$eeSFL_FileTotalCount = count($eeSFL_Files); // How many?

// Extension Check
if($eeSFLS) {
	$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
	include(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-search-processor.php');
	$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
	include(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-pagination-processing.php');
}

$eeSFL_FileCount = count($eeSFL_Files); // Files on this page

// User Messaging	
if(@$eeSFL_Log['messages']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['messages'], 'updated'); // Add to the output -- TO DO - Make a function
	$eeSFL_Log['messages'] = array(); // Clear
}	
if(@$eeSFL_Log['errors']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['errors'], 'error'); // Add to the output
	$eeSFL_Log['errors'] = array(); // Clear
}


// DISPLAY ===================================================

// Listing the files, all sorted and ready to go!

$eeOutput .= '

<!-- Simple File List -->
<span class="eeSFL_Hide" id="eeSFL_ID">' . $eeSFL->eeListID . '</span>
';

if($eeSFLS) {
	$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
	include(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-search-form.php');
}

if($eeSFLF) {
	if($eeAdmin OR $eeSFL_Config['ShowBreadCrumb'] == 'YES') {
		if($eeListNumber == 1) {
			$eeOutput .= $eeSFLF_FunctionBar;
		}
	}
	
	$eeOutput .= '<span class="eeSFL_Hide" id="eeSFLF_ListFolder">' . $eeSFLF_ListFolder . '</span>
		<span class="eeSFL_Hide" id="eeSFLF_MoveNonce">' . $eeSFLF_MoveNonce . '</span>';
}

// Prepare a form so we can delete files
if($eeAdmin OR ($eeSFL_Config['AllowFrontManage'] == 'YES' AND $eeListNumber == 1 )) {

	$eeOutput .= '
	
	<form action="' . eeSFL_GetThisURL();	
	
	if($eeSFLF) {
		if($eeSFLF_ListFolder) {
			$eeOutput .= '&eeSFLF_ListFolder=' . urlencode($eeSFLF_ListFolder);
		}
	}
		
	$eeOutput .= '" method="POST" id="eeSFL_FilesForm">';
			
}

if($eeAdmin) {

	$eeOutput .= '<a href="#" class="button eeButton" id="uploadFilesButton">' . __('Upload Files', 'ee-simple-file-list') . '</a>
		<a href="?page=ee-simple-file-list&tab=list_settings" class="button eeButton">' . __('Settings', 'ee-simple-file-list') . '</a>';
}


// TABLE HEAD ==================================================================================================

if(count($eeSFL_Files)) { 
	
	$eeSFL_RowID = 0; // Assign an ID number to each row
	
	$eeOutput .= '<table class="eeFiles">';
	
	if($eeSFL_Config['ShowHeader'] == 'YES' OR $eeAdmin) { $eeOutput .= '<thead><tr>';
							
		if($eeSFL_Config['ShowFileThumb'] == 'YES') { $eeOutput .= '<th class="eeSFL_Thumbnail">' . __('Thumb', 'ee-simple-file-list') . '</th>'; }
		
		$eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileName">&#x25B3; ' . __('Name', 'ee-simple-file-list') . ' &#x25BD;</th>';
									
		if($eeSFL_Config['ShowFileSize'] == 'YES' OR $eeAdmin) { $eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileSize">&#x25B3; ' . __('Size', 'ee-simple-file-list') . ' &#x25BD;</th>'; }
									
		if($eeSFL_Config['ShowFileDate'] == 'YES' OR $eeAdmin) { $eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileDate">&#x25B3; ' . __('Date', 'ee-simple-file-list') . ' &#x25BD;</th>'; } 
		
		elseif($eeSFL_Config['AllowFrontManage'] == 'YES' AND $eeListNumber == 1 ) { $eeOutput .= '<th></th>'; }
		
		$eeOutput .= '</tr></thead>';
	}						
	
	$eeOutput .= '<tbody>'; // TABLE BODY == BEGIN FILE LIST =========================================================
						
	$eeSFL_FileCount = 0; // Reset
	
	$eeSFL_Log[] = 'Creating file file list display...';
	
	$eeListPosition = FALSE; // eeSFLS
							
	// Loop through array
	foreach($eeSFL_Files as $eeSFL_Key => $eeSFL_File) {
		
		if(!$eeListPosition AND $eeListPosition !== 0) { // eeSFLS
			$eeListPosition = $eeSFL_Key; // Get the first key
		}
		
		$eeSFL_IsFile = FALSE;
		$eeSFL_FileURL = FALSE;
		$eeSFL_IsFolder = FALSE;
		
		if($eeSFL_File) {
			
			if(strpos(basename($eeSFL_File), '.') !== 0) { // Don't display hidden items
			
				$eeSFL_FileCount++; // Bump the count
				
				if(!is_dir($eeSFL_Config['FileListDir'] . $eeSFL_File)) {
					
					$eeSFL_FileURL = $eeSFL_Config['FileListURL'] . str_replace('/', '', $eeSFL_File); // Clickable URL
					
					$eeExt = substr(strrchr($eeSFL_File,'.'), 1); // Get Extension
					
					$eeSFL_IsFile = TRUE;
				
				} elseif($eeSFLF) {
					
					$eeSFL_IsFolder = TRUE;
					$eeExt = 'folder';
					if(!$eeAdmin AND $eeListNumber > 1) { continue; } // Disable folder support for additional lists
					$eeSFL_FileURL = $eeSFLF->eeSFLF_GetFolderURL($eeSFL_File, $eeSFLF_ShortcodeFolder); // Extension required
					
				} else {
					
					$eeSFL_File = FALSE;
				}
				
				// $eeSFL_FileName = $eeSFL_File; // FileName is used for visible link, if we trim the owner info
				
				
				// Check for the actual file
				if($eeSFL_IsFile AND $eeAdmin) { // Only check URLs if in Admin area (speed tweak)
					$eeSFL_FileURL = eeSFL_UrlExists($eeSFL_FileURL); // Sets to FALSE if file not found.
				}
				
				// Add an ID to use in javascript
				$eeOutput .= "\n\r" . '<tr id="eeSFL_RowID-' . $eeSFL_RowID . '">';
				
				
				
				// Thumbnail
				if($eeSFL_Config['ShowFileThumb'] == 'YES') {
					
					// Create Thumbnail Path
					$eeIsImage = in_array($eeExt,  $eeSFL->eeDynamicImageThumbFormats);
					$eeIsVideo = in_array($eeExt,  $eeSFL->eeDynamicVideoThumbFormats);
					
					// Check if we will be using a dynamically created thumbnail
					if($eeIsImage OR $eeIsVideo) {
						
						$eeSFL_Thumb = $eeSFL->eeSFL_FileThumbnail($eeSFL_Config['FileListDir'] , $eeSFL_Config['FileListURL'] , $eeSFL_File); 
					
					} else { // Using File-Type Icon
						
						$eeSFL_Thumb = plugins_url() . '/simple-file-list/images/thumbnails/' . $eeExt . '.svg';
					}
				
					$eeOutput .= '<td class="eeSFL_Thumbnail">';
					
					if($eeSFL_Thumb) { $eeOutput .= '<a href="' . $eeSFL_FileURL .  '"';
						
						if(!$eeSFL_IsFolder) { $eeOutput .= ' target="_blank"'; }
							
						$eeOutput .= '><img src="' . $eeSFL_Thumb . '" width="64" height="64" /></a>'; }
					
					$eeOutput .= '</td>';
				}
				
				
				
				// NAME
				$eeOutput .= '<td class="eeSFL_FileName">';
				
				if($eeSFL_FileURL) {
					
					$eeOutput .= '<a class="eeSFL_FileName" href="' . $eeSFL_FileURL .  '"';
					if(!$eeSFL_IsFolder) {
						$eeOutput .= ' target="_blank"';
					}
					$eeOutput .= '>';
					
					// Extension Check
					if($eeSFLF) { // Show a small folder icon before the name if thumbs are not used.
						if($eeSFL_IsFolder AND $eeSFL_ShowThumb != 'YES') {
							$eeOutput .= '<b class="eeSFL_FolderIconSmall">' . $eeSFLF_FolderIcon . '</b> ';
						}
					}
					
					// Path display for searching in folders
					if($eeSFLF AND @$_POST['eeSFLS_Searching']) {
						$eeSFL_FileNameBase = trim( str_replace('/', ' / ', $eeSFL_FileName) );
					} else {
						$eeSFL_FileNameBase = basename($eeSFL_FileName);
					}
					
					$eeOutput .= $eeSFL_FileNameBase . '</a>';
					
					
					// File Actions
					if($eeSFL_IsFile) { //  ------------------------------------------------------------------------------------
						
						// Construct
						$eeFileActions = '<br />
						
						<small class="eeSFL_ListFileActions">
							
							<a href="' . $eeSFL_FileURL . '" target="_blank">' . __('Open', 'ee-simple-file-list') . '</a>
								
							| <a href="' . $eeSFL_FileURL . '" download="' . $eeSFL_FileNameBase  . '">' . __('Download', 'ee-simple-file-list') . '</a>';
						
						
						// Display
						if($eeSFL_Config['ShowFileActions'] == 'YES' OR $eeAdmin) {
							$eeOutput .= $eeFileActions; // Public Actions
						}
						
						
						// Append Addition (admin or authorized) Actions
						if($eeAdmin OR $eeSFL_Config['FrontSideManage'] == 'YES') { // Only Admins can rename
							
							$eeFileActions = ' | <a href="#" onclick="eeSFL_Rename(' . $eeSFL_RowID . ')">' . __('Rename', 'ee-simple-file-list') . '</a>
							
								 | <a href="#" onclick="eeSFL_Delete(' . $eeSFL_RowID . ')">' . __('Delete', 'ee-simple-file-list') . '</a>';
							
							if($eeSFLF) {
								$eeFileActions .= ' | <a id="eeSFLF_moveLink_' . $eeSFL_RowID . '" href="#" onclick="eeSFLF_MoveFileDisplay(' . $eeSFL_RowID . ')">' . __('Move', 'ee-simple-file-list-folders') . '</a>';
							}
							
							$eeOutput .= $eeFileActions;
						}
								
						$eeOutput .= '</small>';
						
						if($eeAdmin AND $eeSFLF) { $eeOutput .= $eeSFLF_FolderOptionsDisplay; } // Move-to-folder select box
						
					
					} else { // Folder
						
						if($eeAdmin) { // Only Admins can rename
							
							$eeOutput .= '<br /><small class="eeSFL_ListFileActions">
								<a href="#" onclick="eeSFL_Rename(' . $eeSFL_RowID . ')">' . __('Rename', 'ee-simple-file-list') . '</a>
								<a href="#" onclick="eeSFL_Delete(' . $eeSFL_RowID . ')">' . __('Delete', 'ee-simple-file-list') . '</a>
							</small>';
						}
						
					}
					
				} else { // File Read Error
					
					$eeOutput .= $eeSFL_File; // Mark as error, No link if not accessible
					
					$eeOutput .= '<br /><small>File URL Not Reachable</small>';
					
					$eeSFL_Log['errors'][] = __('File Not Found', 'ee-simple-file-list') . ': ' . $eeSFL_Config['FileListDir'] . $eeSFL_File;
				}
				
				$eeOutput .= '</td>';
				
				
				
				
				
				// File Size
				if($eeSFL_Config['ShowFileSize'] == 'YES' OR $eeAdmin) {
				
					$eeOutput .= '<td class="eeSFL_FileSize">';
					
					if($eeSFL_IsFile) {
						$eeFileSize = eeSFL_GetFileSize($eeSFL_Config['FileListDir'] . $eeSFL_File);
						$eeOutput .= $eeFileSize;
					} else {
						
						if($eeSFL_Config['ShowFolderSize'] == 'YES') {
							$eeOutput .= $eeSFLF->eeSFLF_GetFolderSize($eeSFL_Config['FileListDir'] . $eeSFL_File);
						} else {
							$eeOutput .= __('Folder', 'ee-simple-file-list');
						} 
					}
					
					$eeOutput .= '</td>';
				}
				
				
				
				
				
				// File Modification Date
				if($eeSFL_Config['ShowFileDate'] == 'YES' OR $eeAdmin) {
					
					$eeOutput .= '<td class="eeSFL_FileDate">';
					
					$eeFileTime = @filemtime($eeSFL_Config['FileListDir'] . $eeSFL_File);
					$eeModDate = date_i18n( get_option( 'date_format' ),  $eeFileTime);
					
					if($eeModDate) {
						$eeOutput .= $eeModDate;
					}
					 
					$eeOutput .= '</td>';
				}
				
				
				$eeOutput .= "</tr>\n";
			}
		}
		
		$eeSFL_RowID++; // Bump the ID	
			
	} // END loop
	
	
	$eeSFL_Msg = __('Number of Items', 'ee-simple-file-list') . ': ' . $eeSFL_FileCount . ' | '  . __('Sorted by', 'ee-simple-file-list') . ' ' . $eeSFL_Config['SortBy'];

	$eeOutput .= '</tbody></table>
	
	<p class="eeSFL_Hide">
			<span id="eeSFL_FileCount">' . $eeSFL_FileCount . '</span>
		</p>'; // This allows javascript to access the info
				
	$eeSFL_Log[] = $eeSFL_Msg;
	
	if($eeAdmin OR ($eeSFL_Config['AllowFrontManage'] == 'YES' AND $eeListNumber == 1 )) { 
		$eeOutput .= '<input type="submit" class="eeDeleteCheckedButton button eeRight" value="' . __('Delete Checked', 'ee-simple-file-list') . '" />';
	}
	
	if($eeAdmin) { $eeOutput .= '<p class="eeFileListInfo">' . $eeSFL_Msg . '</p>'; }

	if($eeAdmin OR ($eeSFL_Config['AllowFrontManage'] == 'YES' AND $eeListNumber == 1 )) { $eeOutput .= '</form>'; }
	
	
	// Pagination Controls
	if($eeSFLS) {
		$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
		include(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-pagination-display.php');
	}
 
} else {
	
	$eeSFL_Log[] = 'There are no files here :-(';
	
	if($eeAdmin) {
		$eeOutput .= '<div id="eeSFL_noFiles"><p>&#8593; ' . __('Upload some files and they will appear here.', 'ee-simple-file-list') . '</p></div>';
	}
}


?>