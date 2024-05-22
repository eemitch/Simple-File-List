<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

$eeProceed = FALSE;

// The File List Operations Bar Processor
if( isset($_POST['eeSFL_ListOpsBarGo']) AND check_admin_referer('eeSFL_Nonce', eeSFL_Nonce)) {
	
	$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Process an Operations Bar Submission...';
		
	$eeZipObject = FALSE; // Zip File Object if Downloading
	
	if(defined('eeSFL_Pro') AND $_POST['eeSFL_FileOpsAction'] == 'Folder') { // Create a Folder
		
		$eeSFL_Pro->eeSFL_CreateFolderProcess($_POST['eeSFL_NewFolderName']);
	
	} else { // Bulk File Operations
		
		$eeString = preg_replace("/[^0-9,]/", "", $_POST['eeSFL_FileOpsFiles']); // Only numbers and commas
		// $eeString = substr($eeString, 1); // Strip the leading comma
		$eeFileIDs = explode(',', $_POST['eeSFL_FileOpsFiles']);
		
		// If Downloading, initialize the zip file
		if($_POST['eeSFL_FileOpsAction'] == 'Download') {
			
			$eeSFL->eeLog['notice']['Download'] = $eeSFL->eeSFL_NOW() . 'Downloading Files';
			
			// Define the file to be within WP Uploads Dir
			$eeZipFileName = eeSFL_TempDir . $eeSFL_Environment->eeSFL_SanitizeFileName($_POST['eeSFL_ZipFileName']);
			$eeZipFileURL = eeSFL_TempURL . $eeSFL_Environment->eeSFL_SanitizeFileName($_POST['eeSFL_ZipFileName']);
							
			// Check for ZIP extension
			if(substr($eeZipFileName, -4) != '.zip') { $eeZipFileName .= '.zip'; } // Add if needed
			if(substr($eeZipFileURL, -4) != '.zip') { $eeZipFileURL .= '.zip'; } // Add if needed
			
			// Delete if already exists
			if(is_file($eeZipFileName)) { unlink($eeZipFileName); }
			
			// Create ZIP Archive
			$eeZipObject = new ZipArchive;
			if ($eeZipObject->open($eeZipFileName, ZipArchive::CREATE) !== TRUE) {
				$eeSFL->eeLog['errors'][] = __('The ZIP file cannot be created.', 'ee-simple-file-list');	
			}
		}
		
		// Bulk Ops
		if( is_array($eeFileIDs) AND !$eeSFL->eeLog['errors'] ) {
			
			foreach( $eeFileIDs as $eeThisID) { // Loop thru the checked files
				
				if( is_numeric($eeThisID) ) { // Might be zero or null
					
					// Loop through and find this file
					foreach($eeSFL->eeAllFiles as $eeKey => $eeFileArray) {
							
						if($eeKey == $eeThisID) {
							
							// The full path
							$eePath = eeSFL_ABSPATH . $eeSFL->eeListSettings['FileListDir'] . $eeFileArray['FilePath'];
				
							if($_POST['eeSFL_FileOpsAction'] == 'Download') {
								
								if( is_object($eeZipObject) ) { 
								
									if(defined('eeSFL_Pro') AND $eeFileArray['FileExt'] == 'folder') {
										$eeThisFolder = $eeSFL_Pro->eeSFL_GetItemsBelow($eeFileArray['FilePath']);
										
										foreach($eeThisFolder as $eeThisItem) {
											$eePath = eeSFL_ABSPATH . $eeSFL->eeListSettings['FileListDir'] . $eeThisItem['FilePath'];
											$eeSFL->eeLog['notice']['Download'] = $eeSFL->eeSFL_NOW() . 'Adding File: ' . $eePath . ' --> ' . $eeThisItem['FilePath'];
											$eeZipObject->addFile($eePath, $eeThisItem['FilePath']);
										}	
									
									} else {
										$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Adding File: ' . $eePath . ' ——> ' . $eeFileArray['FilePath'] . '<br />';
										$eeZipObject->addFile($eePath, $eeFileArray['FilePath']);
									}
								}
							
							} elseif($_POST['eeSFL_FileOpsAction'] == 'Delete') {
								
								$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Deleting File: ' . $eeFileArray['FilePath'];
				
								unset($eeSFL->eeAllFiles[$eeKey]); // Remove the array from the array
								if(count($eeSFL->eeAllFiles) < 1) { $eeSFL->eeAllFiles = array(); $eeFileArray = array(); } // Reset if empty
								
								if($eeFileArray['FileExt'] == 'folder') { $eeSFL_Pro->eeSFL_DeleteFolder($eePath); } // Delete Folder
									else { if(unlink($eePath)) { $eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'File Deleted: ' . basename($eePath); } // Delete File
								}
								
							} elseif($_POST['eeSFL_FileOpsAction'] == 'Description') {
								
								$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Adding Description to Files...';
								
								// Sanitize input and add to the file array. 
								$eeString = substr( sanitize_text_field($_POST['eeSFL_Description']), 0 , 1024 );
								if($eeString) {
									$eeSFL->eeAllFiles[$eeKey]['FileDescription'] = $eeString;
									$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Description Added';
								}
							
							} elseif($_POST['eeSFL_FileOpsAction'] == 'Move') {
							
								if($eeSFL_Pro->eeSFL_MoveToProcessor($eeFileArray) === FALSE) {
									continue;
								}
								
							} elseif($_POST['eeSFL_FileOpsAction'] == 'Copy') {
								
								$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Copying Files...';
								
								// TO DO
								
							} elseif($_POST['eeSFL_FileOpsAction'] == 'Grant') {
								
								$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Granting Access to Files...';
								
								// TO DO
							}
						}
					}
				}
			}
		}
		
		
		
		// Write the new array to the database
		if(count($eeSFL->eeLog['errors']) < 1) {
			update_option('eeSFL_FileList_' . $eeSFL->eeListID, $eeSFL->eeAllFiles); 
		}
		
		// All files are added, so close the zip file.			
		if($eeZipObject) {
			$eeZipObject->close();
			$eeSFL->eeLog['messages'][] = __('Download File', 'ee-simple-file-list') . ' &rarr; <a class="button" href="' . $eeZipFileURL . '" download="' . basename($eeZipFileName) . '">' . basename($eeZipFileName) . '</a></strong>';
		} 
	}
}

?>