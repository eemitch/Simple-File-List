<?php // Simple File List Script: ee-functions.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Functions' ) ) exit('ERROR 98'); // Exit if nonce fails


// User Messaging
function eeSFL_BASE_UserMessaging() {

	global $eeSFL_BASE;
	
	$eeOutput = '';
	
	if(isset($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'])) {		
		if($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors']) { 
			$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'], 'notice-error');
			$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = $eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'];
			$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'] = array(); // Clear
		}
	}
	
	if(isset($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['warnings'])) {		
		if($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['warnings']) { 
			$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['warnings'], 'notice-warning');
			$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = $eeSFL_BASE->eeLog[eeSFL_BASE_Go]['warnings'];
			$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['warnings'] = array(); // Clear
		}
	}
	
	if(isset($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['messages'])) {	
		if($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['messages'] AND $eeSFL_BASE->eeListRun == 1) { 
			$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['messages'], 'notice-success');
			$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = $eeSFL_BASE->eeLog[eeSFL_BASE_Go]['messages'];
			$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['messages'] = array(); // Clear
		}
	}
	
	return $eeOutput;
}


// Get Elapsed Time
function eeSFL_BASE_noticeTimer() {
	
	global $eeSFL_BASE_StartTime, $eeSFL_BASE_MemoryUsedStart; // Time SFL got going
	
	$eeTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]; // Time Right Now
	
	$eeTime = $eeTime - $eeSFL_BASE_StartTime; // Actual Time Elapsed
	
	$eeTime = number_format($eeTime, 3); // Format to 0.000
	
	$eeMemory = eeSFL_BASE_FormatFileSize(memory_get_usage() - $eeSFL_BASE_MemoryUsedStart);
	
	return $eeTime . ' S | ' . $eeMemory;
}





// Get Actual Max Upload Size
function eeSFL_BASE_ActualUploadMax() {
	
	$eeEnv = array();
	
	$eeEnv['upload_max_filesize'] = substr(ini_get('upload_max_filesize'), 0, -1); // PHP Limit (Strip off the "M")
	$eeEnv['post_max_size'] = substr(ini_get('post_max_size'), 0, -1); // PHP Limit (Strip off the "M")
	
	// Check which is smaller, upload size or post size.
	if ($eeEnv['upload_max_filesize'] <= $eeEnv['post_max_size']) { 
		return $eeEnv['upload_max_filesize'];
	} else {
		return $eeEnv['post_max_size'];
	}
}


function eeSFL_BASE_CheckSupported() {
	
	global $eeSFL_BASE;
	
	// Check for supported technologies
	$eeSupported = array();

    // Check for ffMpeg
    if(function_exists('shell_exec')) {
	    
		if(shell_exec('ffmpeg -version')) {
			$eeSupported[] = 'ffMpeg';
			$eeSFL_Log['Supported'][] = 'Supported: ffMpeg';
		}
    } else {
	    $eeSFL_BASE->eeLog[eeSFL_BASE_Go]['Trouble'] = '---> shell_exec() NOT SUPPORTED'; 
    }
    
    if($eeSFL_BASE->eeEnvironment['eeOS'] != 'WINDOWS') {
		
		// Check for ImageMagick
		$phpExt = 'imagick'; 
		if(extension_loaded($phpExt)) {
			$eeSupported[] = 'ImageMagick';
			$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['Supported'][] = 'Supported: ImageMagick';
		}
		
		// Check for GhostScript
		if($eeSFL_BASE->eeEnvironment['eeOS'] == 'LINUX') { // TO DO - Make it work for IIS
		
			if(function_exists('shell_exec')) {
			
				$phpExt = 'gs'; // <<<---- This will be different for Windows
				if(shell_exec($phpExt . ' --version') >= 1.0) { // <<<---- This will be different for Windows too
					$eeSupported[] = 'GhostScript';
					$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['Supported'][] = 'Supported: GhostScript';
				}
			}
		}
	}
	
	// echo '<pre>'; print_r($eeSupported); echo '</pre>'; exit;
	
	if(count($eeSupported)) {
		update_option('eeSFL_Supported', $eeSupported);
	} else {
		update_option('eeSFL_Supported', array('None'));
	}
	
	return TRUE;
	
	
}


// Detect upward path traversal
function eeSFL_BASE_DetectUpwardTraversal($eeFilePath) {

	global $eeSFL_BASE;
	
	if($eeSFL_BASE->eeEnvironment['eeOS'] == 'LINUX') {
	
		$eeFilePath = str_replace('//', '/', $eeFilePath); // Strip double slashes, which will cause failure
		$eeUserPath = ABSPATH . dirname($eeFilePath);  // This could be problematic with things like ../
		$eeRealPath = realpath( ABSPATH . dirname($eeFilePath) ); // Expunge the badness and then compare...
		
		if ($eeUserPath != $eeRealPath) { // They must match
		    wp_die('Error 99 :-( ' . $eeUserPath . ' != ' . $eeRealPath); // Bad guy found, bail out :-( // Bad guy found, bail out :-(
		}
		
		return TRUE;
	
	} else {

		$eeFilePath = urldecode($eeFilePath);
		
		if(strpos($eeFilePath, '..') OR strpos($eeFilePath, '..') === 0) {
			wp_die('Error 99 :-( ' . $eeFilePath); // Bad guy found, bail out :-( // Bad guy found, bail out :-(
		}
			
		return TRUE;
	}
}




// LEGACY - Convert hyphens to spaces for display only
function eeSFL_BASE_PreserveSpaces($eeFileName) {
	
	$eeFileName = str_replace('-', ' ', $eeFileName);
	
	return $eeFileName;
}





// Add the correct URL argument operator, ? or &
function eeSFL_BASE_AppendProperUrlOp($eeURL) {
	
	if ( strpos($eeURL, '?') ) {
		$eeURL .= '&';
	} else {
		$eeURL .= '?';
	}
	
	return $eeURL;
}





// Check for the Upload Directory, Create if Needed
function eeSFL_BASE_FileListDirCheck($eeFileListDir) {
	
	global $eeSFL_BASE;
	$eeCopyMaunalFile = FALSE;
	
	if(!$eeFileListDir OR substr($eeFileListDir, 0, 1) == '/' OR strpos($eeFileListDir, '../') ) { 
		
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'][] = __('Bad Directory Given', 'ee-simple-file-list') . ': ' . $eeFileListDir;
		
		return FALSE;
	}
		
	$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Checking: ' . $eeFileListDir;
		
	if( !is_dir(ABSPATH . $eeFileListDir) ) { // Directory Changed or New Install
	
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - New Install or Directory Change...';
		
		if(!is_writable( ABSPATH . $eeFileListDir ) ) {
			
			$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - No Directory Found. Creating ...';
			
			if ($eeSFL_BASE->eeEnvironment['eeOS'] == 'WINDOWS') {
			    
			    if( !mkdir(ABSPATH . $eeFileListDir) ) {
				    
				    $eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'][] = __('Cannot Create Windows Directory:', 'ee-simple-file-list') . ': ' . $eeFileListDir;
				}
			
			} elseif($eeSFL_BASE->eeEnvironment['eeOS'] == 'LINUX') {
			    
			    if( !mkdir(ABSPATH . $eeFileListDir , 0755) ) { // Linux - Need to set permissions
				    
				    $eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'][] = __('Cannot Create Linux Directory:', 'ee-simple-file-list') . ': ' . $eeFileListDir;
				}
			} else {
				
				$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'][] = __('ERROR: Could not detect operating system', 'ee-simple-file-list');
				return FALSE;
			}
			
			if(!is_writable( ABSPATH . $eeFileListDir )) {
				$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'][] = __('Cannot create the upload directory', 'ee-simple-file-list') . ': ' . $eeFileListDir;
				$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'][] = __('Please check directory permissions', 'ee-simple-file-list');
			
				return FALSE;
			
			} else {
				
				$eeCopyMaunalFile = TRUE;
				
				$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - The File List Dir Has Been Created!';
				$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = $eeFileListDir;
			}
		
		} else {
			$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - FileListDir Looks Good';
		}
		
	} 
	
	// Check index.html, create if needed.	
	if( strlen($eeFileListDir) >= 2 ) {	
		
		$eeFile = ABSPATH . $eeFileListDir . 'index.html'; // Disallow direct file indexing.
		
		if(!is_file($eeFile)) {
			
			if($eeHandle = fopen($eeFile, "a+")) {
				
				if(!is_readable($eeFile)) {
				    
					$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['warnings'][] = __('WARNING! Could not write file', 'ee-simple-file-list') . ': index.html';
					$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['warnings'][] = __('Please upload a blank index file to this location to prevent unauthorized access.', 'ee-simple-file-list');
					$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['warnings'][] = ABSPATH . '/' . $eeFileListDir;
					
				} else {
					
					// Write nice content to the file
					$eeString = file_get_contents( $eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-index-template.html' );
					fwrite($eeHandle, $eeString);
					fclose($eeHandle);
				}
			}
		}
		
		if($eeCopyMaunalFile === TRUE) {
			
			// Copy the Manual to the new directory, so there's at least one file.
			$eeCopyFrom = $eeSFL_BASE->eeEnvironment['pluginDir'] . 'Simple-File-List.pdf';
			$eeCopyTo = ABSPATH . '/' . $eeFileListDir . 'Simple-File-List.pdf';
			copy($eeCopyFrom, $eeCopyTo);
		}
	}
	
	return TRUE; // Looks Good
	
}



// Post-process an upload job
function eeSFL_BASE_ProcessUpload() {
	
	global $eeSFL_BASE;
	$eeFileNiceName = FALSE;
	
	// echo '<pre>'; print_r($_POST); echo '</pre>'; exit;
	
	$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Function Called: eeSFL_BASE_ProcessUpload()';
	
	if(strpos($_POST['eeSFL_FileList'], ']')) {
		
		$eeFileListString = stripslashes($_POST['eeSFL_FileList']);
		$eeFileListArray = json_decode($eeFileListString);
		
		if(!is_array($eeFileListArray)) { return FALSE; }
	
	} else {
		return FALSE;
	}
	
	if(isset($_POST['eeSFL_FileCount'])) {
		
		$eeFileCount = filter_var($_POST['eeSFL_FileCount'], FILTER_VALIDATE_INT);
		
		if( $eeFileCount != count($eeFileListArray) ) { return FALSE; }
		
	} else {
		return FALSE;
	}
	
	
	if( isset($_POST['eeSFL_UploadFolder']) ) { 
		
		$eeSFL_UploadFolder = sanitize_text_field( urldecode($_POST['eeSFL_UploadFolder']) );
	
	} else { $eeSFL_UploadFolder = FALSE; }
	
	
	if($eeFileCount) {
		
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = $eeFileCount . ' Files Uploaded';
		
		// Check for Nonce
		if(check_admin_referer( 'ee-simple-file-list-upload-form', 'ee-simple-file-list-upload-form-nonce')) {
			
			// Loop thru and look for any renaming done in the Uploader Engine
			foreach( $eeFileListArray as $eeKey => $eeFileName) {
				
				$eeFileName = urlencode($eeSFL_UploadFolder . $eeFileName); // Tack on the sub-folder
				$eeNewName = get_transient('eeSFL-Renamed-' . $eeFileName); // The value will include the sub-folder
				
				if($eeNewName) {
					$eeFileNiceName = urldecode(basename($eeFileName));
					$eeFileListArray[$eeKey] = urldecode($eeNewName);
					delete_transient('eeSFL-Renamed-' . $eeFileName);
				} else {
					$eeFileListArray[$eeKey] = urldecode($eeFileName);
				}
			}
			
			$eeSFL_BASE->eeEnvironment['UploadedFiles'] = $eeFileListArray; // Used for the Results Page
			
			
			// echo '<p>Old Name: ' . $eeFileName . '<br />';
			// echo 'New Name: ' . $eeNewName . '</p>';
			// echo '<pre>'; print_r($eeSFL_BASE->eeEnvironment['UploadedFiles']); echo '</pre>'; exit;
			
			
			// Notification
			if( count($eeFileListArray) ) {
				
				$eeUploadJob = ''; // This will be what happened
				
				// Semantics
				if($eeFileCount > 1) { 
					$eeUploadJob .= $eeFileCount . ' ' . __('Files Uploaded', 'ee-simple-file-list');	
				} else {
					$eeUploadJob .= __('File Uploaded', 'ee-simple-file-list');
				}
				$eeUploadJob .= ":" . PHP_EOL . PHP_EOL;
				
				// Get the existing array
				if(empty($eeSFL_BASE->eeAllFiles)) {
					$eeSFL_BASE->eeAllFiles = get_option('eeSFL_FileList_1');
				}
				
				// Loop through the uploaded files
				if(count($eeFileListArray)) {
								
					$eeFound = FALSE;
					
					foreach($eeFileListArray as $eeFile) { 
						
						$eeFile = sanitize_text_field($eeFile);
						
						if( is_file(ABSPATH . $eeSFL_BASE->eeListSettings['FileListDir'] . $eeFile) ) { // Check to be sure the file is there
							
							$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Creating File Array: ' . $eeFile;
							
							if($eeSFL_BASE->eeListSettings['AllowOverwrite'] == 'YES') { // Look for existing file array
								
								foreach( $eeSFL_BASE->eeAllFiles as $eeKey => $eeThisFileArray ) {
									$eeFound = FALSE;
									if($eeThisFileArray['FilePath'] == $eeFile) { $eeFound = TRUE; break; }
								}
								
								if($eeFound) {
									$eeNewFileArray = $eeSFL_BASE->eeSFL_BuildFileArray($eeFile, $eeThisFileArray);
								} else {
									$eeNewFileArray = $eeSFL_BASE->eeSFL_BuildFileArray($eeFile); // Path relative to FileListDir
								}
							} else { // Build a new file array
								$eeNewFileArray = $eeSFL_BASE->eeSFL_BuildFileArray($eeFile); // Path relative to FileListDir
							}
							
							// If sanitized, use original is Nice Name
							if($eeFileNiceName) { $eeNewFileArray['FileNiceName'] = $eeFileNiceName; }
							
							// Set these if available
							if( isset($_POST['eeSFL_FileOwner']) ) { // Expecting a number
								
								if( is_numeric($_POST['eeSFL_FileOwner']) ) {
									$eeNewFileArray['FileOwner'] = $_POST['eeSFL_FileOwner']; // Logged-in owner
								}
							
							} else { // Don't need this if we have the owner's ID
								
								if( isset($_POST['eeSFL_Name'])) {
									
									$eeString = sanitize_text_field($_POST['eeSFL_Name']);
									
									if($eeString) {
										
										$eeNewFileArray['SubmitterName'] = $eeString; // Who uploaded the file
									}
								}
								
								if( isset($_POST['eeSFL_Email'])) {
									
									$eeString = filter_var( sanitize_email($_POST['eeSFL_Email']), FILTER_VALIDATE_EMAIL);
									
									if($eeString) {
										
										$eeNewFileArray['SubmitterEmail'] = $eeString; // Their email
									}
								}
							}
							
							if( isset($_POST['eeSFL_FileDesc'])) {
								
								$eeString = sanitize_text_field($_POST['eeSFL_FileDesc']);
								
								if($eeString) {
									
									$eeNewFileArray['FileDescription'] = $eeString; // A short description of the file
									$eeNewFileArray['SubmitterComments'] = $eeString; // What they said
								}
							}
							
							$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = '——> Done';
							$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = $eeNewFileArray;
							
							$eeNewFileArray = array_filter($eeNewFileArray); // Remove empty elements
							
							// To add or modify
							if($eeFound) { 
								$eeSFL_BASE->eeAllFiles[$eeKey] = $eeNewFileArray; // Updating current file array
							} else {
								$eeSFL_BASE->eeAllFiles[] = $eeNewFileArray; // Append this file array to the big one
							}
							
							// If in a folder, update the folder dates
							if($eeSFL_UploadFolder) {
									
								$eePathPieces = explode('/', $eeSFL_UploadFolder);
								$eePartPaths = '';
								if(is_array($eePathPieces)) {
									foreach( $eePathPieces as $eePart ) {
										if($eePart) {
											$eePartPaths .= $eePart . '/';
											$eeSFL_BASE->eeSFL_UpdateFileDetail($eePartPaths, 'FileDateAdded', date("Y-m-d H:i:s") );
										}
									}
								}
							}
							
							// Notification Info
							$eeFileURL = $eeSFL_BASE->eeListSettings['FileListURL'] . $eeFile;
							
							$eeUploadJob .=  $eeFile . " (" . eeSFL_BASE_FormatFileSize($eeNewFileArray['FileSize']) . ")" . PHP_EOL;
							$eeUploadJob .=  $eeFileURL . PHP_EOL . PHP_EOL;
						}
					}
					
					$eeSFL_BASE->eeSFL_SortFiles($eeSFL_BASE->eeListSettings['SortBy'], $eeSFL_BASE->eeListSettings['SortOrder']);
					
					// Save the new array
					update_option('eeSFL_FileList_1', $eeSFL_BASE->eeAllFiles);
						
					$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['messages'][] = __('File Upload Complete', 'ee-simple-file-list');
					
					if( is_admin() ) {
						
						return TRUE;
					
					} else  {
						
						// Upload Email Notice
						if($eeSFL_BASE->eeListSettings['Notify'] == 'YES') {
							
							// Send the Email Notification
							$eeSFL_BASE->eeSFL_NotificationEmail($eeUploadJob);
							return TRUE;
							
						} else {
							return TRUE; // No notice wanted
						}
					}
					
				} else {
					$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'][] = 'ERROR 96';
					return FALSE;
				}
			}
			
		} else {
			wp_die('ERROR 98 - ProcessUpload');
		}
	
	} else {
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'][] = 'ERROR 96';
		return FALSE;
	}
}





// Return the size of a file in a nice format.
// Accepts a path or filesize in bytes
function eeSFL_BASE_GetFileSize($eeSFL_File) {  
    
    if( is_numeric($eeSFL_File) ) {
		$bytes = $eeSFL_File;
	} elseif(is_file(ABSPATH . $eeSFL_File)) {
		$bytes = filesize(ABSPATH . $eeSFL_File);	
	} else {
		return FALSE;
	}
	    
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;
    $precision = 2;
   
    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';
 
    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';
 
    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';
 
    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';
 
    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}




// Make sure the file name is acceptable
function eeSFL_BASE_SanitizeFileName($eeSFL_FileName) {
	
	// Make sure file has an extension
	$eeSFL_PathParts = pathinfo($eeSFL_FileName);
	$eeSFL_FileNameAlone = str_replace('.', '_', $eeSFL_PathParts['filename']); // Get rid of dots
	$eeSFL_Extension = strtolower($eeSFL_PathParts['extension']);
	$eeSFL_FileName = sanitize_file_name( $eeSFL_FileNameAlone . '.' . $eeSFL_Extension );
    
    return $eeSFL_FileName;
}



// Yes or No Settings Checkboxes
function eeSFL_BASE_ProcessCheckboxInput($eeTerm) {
	
	$eeValue = sanitize_text_field(@$_POST['ee' . $eeTerm]);
	
	if($eeValue == 'YES') { return 'YES'; } else { return 'NO'; }
}



// Settings Text Inputs 
function eeSFL_BASE_ProcessTextInput($eeTerm, $eeType = 'text') {
	
	$eeValue = '';
	
	if($eeType == 'email') {
		
		$eeValue = filter_var(sanitize_email(@$_POST['ee' . $eeTerm]), FILTER_VALIDATE_EMAIL);
	
	} elseif($eeType == 'textarea') {
		
		$eeValue = sanitize_textarea_field( @$_POST['ee' . $eeTerm] );
		
	} else {
		
		$eeValue = strip_tags(@$_POST['ee' . $eeTerm]);
		$eeValue = sanitize_text_field($eeValue);
	}
	
	return $eeValue;
}




// Check if a file already exists, then number it so file will not be over-written.
function eeSFL_BASE_CheckForDuplicateFile($eeSFL_FilePathAdded) { // Path from ABSPATH
	
	global $eeSFL_BASE;
	
	$eePathInfo = pathinfo($eeSFL_FilePathAdded);
	$eeFileName = $eePathInfo['basename'];
	$eeNameOnly = $eePathInfo['filename'];
	$eeExtension = strtolower($eePathInfo['extension']);
	$eeDir = dirname($eeSFL_FilePathAdded) . '/';
	$eeFolderPath = str_replace($eeSFL_BASE->eeListSettings['FileListDir'], '', $eeDir);
	$eeCopyLimit = 1000; // File copies limit
	
	if(empty($eeSFL_BASE->eeAllFiles)) {
		$eeSFL_BASE->eeAllFiles = get_option('eeSFL_FileList_1');
	}
	
	foreach($eeSFL_BASE->eeAllFiles as $eeFileArray) { // Loop through file array and look for a match.
		
		if( $eeFolderPath . $eeFileName == $eeFileArray['FilePath'] ) { // Duplicate found
		
			$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Duplicate Item Found: ' . $eeFolderPath . $eeFileName;
			
			if( is_file(ABSPATH . $eeSFL_FilePathAdded) ) { // Confirm the file is really there
				
				for ($i = 1; $i <= $eeCopyLimit; $i++) { // Look for existing copies
					
					$eeFileName = $eeNameOnly . '_' . $i . '.' . $eeExtension; // Indicate the copy number
					
					if(!is_file(ABSPATH . $eeDir . $eeFileName)) { break; } // We're done.
				}							
			}
		}
	}
	
	return 	$eeFileName; // Return the new file name
}




// Return the general size of a file in a nice format.
function eeSFL_BASE_FormatFileSize($eeFileSizeBytes) {  
    
    $bytes = $eeFileSizeBytes;
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;
    $precision = 2;
   
    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';
 
    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';
 
    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';
 
    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';
 
    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}




// The form submission results bar at the top of the admin pages
function eeSFL_BASE_ResultsDisplay($eeSFL_Results, $eeResultType) { // error, updated, etc...
	
	$eeReturn = '<div class="notice ';
	
	if(is_admin()) {
		$eeReturn .= $eeResultType;
	} else {
		$eeReturn .= 'eeResult';
	}
	
	$eeReturn .= ' is-dismissible"><p>';
	$eeReturn .= eeSFL_BASE_MessageDisplay($eeSFL_Results); // Parse the message array
	$eeReturn .= '</p></div>';
	
	return $eeReturn;
}




// Problem Display / Error reporting
function eeSFL_BASE_MessageDisplay($eeSFL_Message) {
	
	$eeReturn = '';
	
	$eeAdmin = is_admin();
	
	if(is_array($eeSFL_Message)) {
		
		if(!$eeAdmin) { $eeReturn .= '<div id="eeMessageDisplay">' . PHP_EOL; }
		
		$eeReturn .= '<ul>' . PHP_EOL; // Loop through $eeSFL_Messages array
		foreach($eeSFL_Message as $key => $value) { 
			if(is_array($value)) {
				foreach ($value as $value2) {
					$eeReturn .= "<li>$value2</li>" . PHP_EOL;
				}
			} else {
				$eeReturn .= "<li>$value</li>" . PHP_EOL;
			}
		}
		$eeReturn .= "</ul>" . PHP_EOL;
		
		if(!$eeAdmin) { $eeReturn .= '</div>' . PHP_EOL; }
		
		return $eeReturn;
		
	} else {
		return $eeSFL_Message;
	}
}



// Return a formatted header string
function eeSFL_BASE_ReturnHeaderString($eeFrom, $eeCc = FALSE, $eeBcc = FALSE) {
	
	$eeAdminEmail = get_option('admin_email');
	
	$eeHeaders = 'From: ' . get_option('blogname') . ' < ' . $eeAdminEmail . ' >'  . PHP_EOL;
	
	if($eeCc) { $eeHeaders .= "CC: " . $eeCc . PHP_EOL; }
	
	if($eeBcc) { $eeHeaders .= "BCC: " . $eeBcc . PHP_EOL; }
	
	if( !filter_var($eeFrom, FILTER_VALIDATE_EMAIL) ) {
		$eeFrom = $eeAdminEmail;
	}
	
	$eeHeaders .= "Return-Path: " . $eeAdminEmail . PHP_EOL . 
		"Reply-To: " . $eeFrom . PHP_EOL;
	
	return $eeHeaders;

}




// Process a raw input of email addresses
// Can be a single address or a comma sep list
function eeSFL_BASE_ProcessEmailString($eeString) {
	
	$eeString = sanitize_text_field($eeString);
	
	if( strpos($eeString, ',') ) { // More than one address?
		
		$eeArray = explode(',', $eeString);
		
		$eeAddresses = ''; // Reset
		
		foreach( $eeArray as $eeEmail) {
			
			$eeEmail = filter_var(sanitize_email($eeEmail), FILTER_VALIDATE_EMAIL);
			
			if($eeEmail) {
				
				$eeAddresses .= $eeEmail . ','; // Reassemble validated addresses
			}
		}
		
		$eeAddresses = substr($eeAddresses, 0, -1); // Strip the last comma
	
	} else {
		
		$eeAddresses = filter_var(sanitize_email($eeString), FILTER_VALIDATE_EMAIL);
	}
	
	if( strpos($eeAddresses, '@') ) {
		
		return $eeAddresses;
		
	} else {
		
		return FALSE;
	}
}





// Get what's in the address bar
function eeSFL_BASE_GetThisURL($eeInclude_Request_URI = TRUE) {
	
	// Protocal
	$thisUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://";

	// Host
	$thisUrl .= $_SERVER['HTTP_HOST'];
	
	// Arguments
	if($eeInclude_Request_URI) {
		$thisUrl .= $_SERVER['REQUEST_URI']; // ?this=that&that=this
	}
	 
	return $thisUrl;
}

?>