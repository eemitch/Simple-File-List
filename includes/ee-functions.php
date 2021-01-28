<?php // Simple File List Script: ee-functions.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Functions' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_FREE_Log['SFL'][] = 'Loaded: ee-functions';


// Detect upward path traversal
function eeSFL_FREE_DetectUpwardTraversal($eeFilePath) {

	global $eeSFL_FREE_Env;
	
	if($eeSFL_FREE_Env['eeOS'] == 'LINUX') {
	
		$eeUserPath = ABSPATH . dirname($eeFilePath);  // This could be problematic with things like ../
		$eeRealPath = realpath( ABSPATH . dirname($eeFilePath) ); // Expunge the badness and then compare...
		
		if ($eeUserPath != $eeRealPath) { // They must match
		    wp_die('Error 99 :-('); // Bad guy found, bail out :-(
		}
		
		return TRUE;
	
	} else {

		$eeFilePath = urldecode($eeFilePath);
		
		if(strpos($eeFilePath, '..') OR strpos($eeFilePath, '..') === 0) {
			wp_die('Error 99 :-('); // Bad guy found, bail out :-(
		}
			
		return TRUE;
	}
}




// Convert hyphens to spaces for display only
function eeSFL_FREE_PreserveSpaces($eeFileName) {
	
	$eeFileName = str_replace('-', ' ', $eeFileName);
	
	return $eeFileName;
}





// Add the correct URL argument operator, ? or &
function eeSFL_FREE_AppendProperUrlOp($eeURL) {
	
	if ( strpos($eeURL, '?') ) {
		$eeURL .= '&';
	} else {
		$eeURL .= '?';
	}
	
	return $eeURL;
}





// Check for the Upload Directory, Create if Needed
function eeSFL_FREE_FileListDirCheck($eeFileListDir) {
	
	if(!$eeFileListDir) { return FALSE; }
	
	global $eeSFL_FREE_Log, $eeSFL_FREE, $eeSFL_FREE_Env;
	
	// Check if FileListDir has a trailing slash...
	$eeLastChar = substr($eeFileListDir, -1);
	if($eeLastChar != '/') {  $eeFileListDir .= '/'; } // Trailing slash required
	
	// Set some standards
	if(strpos($eeFileListDir, '.') === 0 OR strpos($eeFileListDir, 'p-admin') OR strpos($eeFileListDir, 'p-includes') ) {
		$eeSFL_FREE_Log['errors'][] = 'This File List Location is Not Allowed: ' . $eeFileListDir;
		return FALSE;
	}
	
	// Check Transient First
	if( !is_dir(ABSPATH . $eeFileListDir) ) { // Directory Changed or New Install
	
		$eeSFL_FREE_Log['SFL'][] = 'New Install or Directory Change...';
		
		if( !is_writable( ABSPATH . $eeFileListDir ) ) {
			
			$eeSFL_FREE_Log['SFL'][] = 'No Directory Found';
			$eeSFL_FREE_Log['SFL'][] = 'Creating new file list directory ...';
			
			if ($eeSFL_FREE_Env['eeOS'] == 'WINDOWS') {
			    
			    if( !mkdir(ABSPATH . $eeFileListDir) ) { // Linux - Need to set permissions
				    
				    $eeSFL_FREE_Log['errors'][] = 'Cannot Create Windows Directory: ' . $eeFileListDir;
				}
			
			} elseif($eeSFL_FREE_Env['eeOS'] == 'LINUX') {
			    
			    if( !mkdir(ABSPATH . $eeFileListDir , 0755) ) { // Linux - Need to set permissions
				    
				    $eeSFL_FREE_Log['errors'][] = 'Cannot Create Linux Directory: ' . $eeFileListDir;
				}
			} else {
				$eeSFL_FREE_Log['errors'][] = 'ERROR: Could not detect operating system';
				return FALSE;
			}
			
			if(!is_writable( ABSPATH . $eeFileListDir )) {
				$eeSFL_FREE_Log['errors'][] = 'Cannot create the upload directory: ' . $eeFileListDir;
				$eeSFL_FREE_Log['errors'][] = 'Please check directory permissions';
				
				return FALSE;
			
			} else {
				
				$eeSFL_FREE_Log['SFL'][] = '"FileListDir" Has Been Created!';
				$eeSFL_FREE_Log['SFL'][] = $eeFileListDir;
			}
		
		} else {
			$eeSFL_FREE_Log['SFL'][] = 'FileListDir Looks Good';
		}
		
		// Check index.html, create if needed.	
		if( strlen($eeFileListDir) >= 2 ) {	
			
			$eeFile = ABSPATH . $eeFileListDir . 'index.html'; // Disallow direct file indexing.
			
			if(!is_file($eeFile)) {
				
				if($handle = @fopen($eeFile, "a+")) {
					
					if(!@is_readable($eeFile)) {
					    
						$eeSFL_FREE_Log['errors'][] = 'ERROR: Could not write index.html';
						
						return FALSE;
						
					} else {
						
						// Write nice content to the file
						$eeString = file_get_contents( plugin_dir_path(__FILE__) . 'ee-index-template.html' );
						fwrite($handle, $eeString);
						fclose($handle);
					}
				}
			
			}
		}
		
		return TRUE; // SUCCESS
		
	} else {
		
		return TRUE; // Looks Good
	}
}



// Post-process an upload job
function eeSFL_FREE_ProcessUpload() {
	
	global $eeSFL_FREE, $eeSFL_Settings, $eeSFL_FREE_Env, $eeSFL_FREE_Log;
	
	$eeSFL_FREE_Log['SFL'][] = 'Function Called: eeSFL_ProcessUpload()';
	
	$eeFileCount = filter_var(@$_POST['eeSFL_FileCount'], FILTER_VALIDATE_INT);
	
	if($eeFileCount) {
	
		$eeSFL_FREE_Log['SFL'][] = $eeFileCount . ' Files Uploaded';
		
		$eeFileList = sanitize_text_field( stripslashes($_POST['eeSFL_FileList'] )); // Expecting a comma delimited list
		
		// Check for Nonce
		if(check_admin_referer( 'ee-simple-file-list-upload', 'ee-simple-file-list-upload-nonce')) {
			
			$eeArray = json_decode($eeFileList);
			
			// Loop thru and look for any renaming done in the Uploader Engine
			foreach( $eeArray as $eeKey => $eeFileName) {
				
				$eeNewName = get_transient('eeSFL-Renamed-' . $eeFileName);
				
				if($eeNewName) {
					$eeArray[$eeKey] = $eeNewName;
					delete_transient('eeSFL-Renamed-' . $eeFileName);
				} else {
					$eeArray[$eeKey] = $eeFileName;
				}
			}
			
			$eeSFL_FREE_Env['UploadedFiles'] = $eeArray;
			
			
			// Notification
			if( count($eeArray) ) {
				
				$eeUploadJob = ''; // This will be what happened
				
				// Semantics
				if($eeFileCount > 1) { 
					$eeUploadJob .= $eeFileCount . ' ' . __('Files uploaded', 'ee-simple-file-list');	
				} else {
					$eeUploadJob .= __('File uploaded', 'ee-simple-file-list');
				}
				$eeUploadJob .= ":" . PHP_EOL . PHP_EOL;
				
				
				// Add the file to the existing array
				$eeFileArrayWorking = get_option('eeSFL_FileList_1');
				
				// Loop through the uploaded files
				if(count($eeArray)) {
					
					foreach($eeArray as $eeKey => $eeFile) { 
						
						$eeFile = sanitize_text_field($eeFile);
								
						$eeFound = FALSE;
						
						if( is_file(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFile) ) { // Check to be sure the file is there
							
							if($eeSFL_Settings['AllowOverwrite'] == 'YES') { // Look for existing file array
								
								foreach( $eeFileArrayWorking as $eeThisKey => $eeThisFileArray ) {
									$eeFound = FALSE;
									if($eeThisFileArray['FilePath'] == $eeFile) { $eeFound = TRUE; break; }
								}
								
								if($eeFound) {
									$eeNewFileArray = $eeThisFileArray;
								} else {
									$eeNewFileArray = $eeSFL_FREE->eeSFL_BuildFileArray($eeFile); // Path relative to FileListDir
								}
							} else { // Build a new file array
								$eeNewFileArray = $eeSFL_FREE->eeSFL_BuildFileArray($eeFile); // Path relative to FileListDir
							}
							
							// echo '<pre>'; print_r($eeNewFileArray); echo '</pre>'; exit;
						
							// Set these if available
							if( is_numeric(@$_POST['eeSFL_FileOwner']) ) { // Expecting a number
								
								$eeNewFileArray['FileOwner'] = $_POST['eeSFL_FileOwner']; // Logged-in owner
							
							} else {
								
								if( isset($_POST['eeSFL_Name'])) {
								
									$eeString = sanitize_text_field(@$_POST['eeSFL_Name']);
									
									if($eeString) {
										
										$eeNewFileArray['SubmitterName'] = $eeString; // Who uploaded the file
									}
								}
								
								if( isset($_POST['eeSFL_Email'])) {
									
									$eeString = filter_var( sanitize_email(@$_POST['eeSFL_Email']), FILTER_VALIDATE_EMAIL);
									
									if($eeString) {
										
										$eeNewFileArray['SubmitterEmail'] = $eeString; // Their email
									}
								}
							}
							
							if( isset($_POST['eeSFL_Comments'])) {
								
								$eeString = sanitize_text_field(@$_POST['eeSFL_Comments']);
								
								if($eeString) {
									
									$eeNewFileArray['FileDescription'] = $eeString; // A short description of the file
									$eeNewFileArray['SubmitterComments'] = $eeString; // What they said
								}
							}
							
							$eeSFL_FREE_Log['SFL'][] = '——> Done';
							$eeSFL_FREE_Log['SFL'][] = $eeNewFileArray;
							
							// To add or modify
							if($eeFound) { 
								$eeFileArrayWorking[$eeKey] = $eeNewFileArray; // Updating current file array
							} else {
								$eeFileArrayWorking[] = $eeNewFileArray; // Append this file array to the big one
								$eeFileArrayWorking = $eeSFL_FREE->eeSFL_SortFiles($eeFileArrayWorking, $eeSFL_Settings['SortBy'], $eeSFL_Settings['SortOrder']);
							}
							
							// Create thumbnail if needed
							if( in_array($eeNewFileArray['FileExt'], $eeSFL_FREE->eeDynamicImageThumbFormats) OR in_array($eePathParts['extension'], $eeSFL_FREE->eeDynamicVideoThumbFormats) ) {
					
								$eeSFL_FREE->eeSFL_CheckThumbnail($eeFile);
							}
							
							// Notification Info (FREE)
							$eeUploadJob .=  $eeFile . PHP_EOL . 
								$eeSFL_Settings['FileListURL'] . $eeFile . PHP_EOL . 
									"(" . eeSFL_FREE_FormatFileSize($eeNewFileArray['FileSize']) . ")" . PHP_EOL . PHP_EOL;
						}
					}
					
					// Save the new array
					update_option('eeSFL_FileList_1', $eeFileArrayWorking);
					
					// delete_transient('eeSFL_FileList_1'); // Force a re-scan
						
					$eeSFL_FREE_Log['messages'][] = __('File Upload Complete', 'ee-simple-file-list');
					
					if( is_admin() ) {
						
						return TRUE;
					
					} else  {
						
						// Send Email Notice
						if($eeSFL_Settings['Notify'] == 'YES') {
							
							// Send the Email Notification
							$eeSFL_FREE->eeSFL_NotificationEmail($eeUploadJob);
							return TRUE;
							
						} else {
							return TRUE; // No notice wanted
						}
					}
					
				} else {
					$eeSFL_FREE_Log['errors'][] = 'ERROR 96';
					return FALSE;
				}
			}
			
		} else {
			wp_die('ERROR 98');
		}
	
	} else {
		$eeSFL_FREE_Log['errors'][] = 'ERROR 96';
		return FALSE;
	}
}





// Return the size of a file in a nice format.
// Accepts a path or filesize in bytes
function eeSFL_FREE_GetFileSize($eeSFL_File) {  
    
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
function eeSFL_FREE_SanitizeFileName($eeSFL_FileName) {
	
	// Make sure file has an extension
	$eeSFL_PathParts = pathinfo($eeSFL_FileName);
	$eeSFL_FileNameAlone = str_replace('.', '_', $eeSFL_PathParts['filename']); // Get rid of dots
	$eeSFL_Extension = @strtolower($eeSFL_PathParts['extension']);
	$eeSFL_FileName = sanitize_file_name( $eeSFL_FileNameAlone . '.' . $eeSFL_Extension );
    
    return $eeSFL_FileName;
}



// Yes or No Settings Checkboxes
function eeSFL_FREE_ProcessCheckboxInput($eeTerm) {
	
	$eeValue = sanitize_text_field(@$_POST['ee' . $eeTerm]);
	
	if($eeValue == 'YES') { return 'YES'; } else { return 'NO'; }
}



// Settings Text Inputs 
function eeSFL_FREE_ProcessTextInput($eeTerm, $eeType = 'text') {
	
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
function eeSFL_FREE_CheckForDuplicateFile($eeSFL_FilePathAdded) { // Path from WP home
	
	global $eeSFL_FREE, $eeSFL_FREE_Log;
	
	$eeSFL_Settings = $eeSFL_FREE->eeSFL_GetSettings();
	
	if($eeSFL_Settings['AllowOverwrite'] == 'YES') { // Overwriting files allowed
		return $eeSFL_FilePathAdded;
	}
	
	$eeCopyLimit = 100; // File copies limit
		
	$eePathParts = pathinfo($eeSFL_FilePathAdded);
	$eeNameOnly = $eePathParts['filename'];
	$eeExtension = strtolower($eePathParts['extension']);
	
	$eeSFL_Files = get_option('eeSFL_FileList_1'); // Our array of file info
	
	if($eeSFL_Files) {
	
		foreach($eeSFL_Files as $eeArray) { // Loop through file array and look for a match.
			
			// Check if duplicate
			if( $eeSFL_FilePathAdded == $eeArray['FilePath'] ) { // Duplicate found
			
				if( is_file(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeSFL_FilePathAdded) ) { // Confirm the file is really there
					
					for ($i = 1; $i <= $eeCopyLimit; $i++) { // Look for existing copies
						
						$eeSFL_FilePathAdded = $eeNameOnly . '_' . $i . '.' . $eeExtension; // Indicate the copy number
						
						if(!is_file(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeSFL_FilePathAdded)) { break; } // If no copy is there, we're done.
					}							
				}
			}
		}
	}
		
	return 	$eeSFL_FilePathAdded; // Return the new file name and path
}




// Return the general size of a file in a nice format.
function eeSFL_FREE_FormatFileSize($eeFileSizeBytes) {  
    
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
function eeSFL_FREE_ResultsDisplay($eeSFL_Results, $eeResultType) { // error, updated, etc...
	
	$eeReturn = '<div class="notice ';
	
	if(is_admin()) {
		$eeReturn .= $eeResultType;
	} else {
		$eeReturn .= 'eeResult';
	}
	
	$eeReturn .= ' is-dismissible"><p>';
	$eeReturn .= eeSFL_FREE_MessageDisplay($eeSFL_Results); // Parse the message array
	$eeReturn .= '</p></div>';
	
	return $eeReturn;
}




// Problem Display / Error reporting
function eeSFL_FREE_MessageDisplay($eeSFL_Message) {
	
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
function eeSFL_FREE_ReturnHeaderString($eeFrom, $eeCc = FALSE, $eeBcc = FALSE) {
	
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
function eeSFL_FREE_ProcessEmailString($eeString) {
	
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
function eeSFL_FREE_GetThisURL($eeInclude_Request_URI = TRUE) {
	
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