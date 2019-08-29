<?php // Simple File List - General Plugin Functions - mitch@elementengage.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Functions' ) ) exit('That is Noncense! (' . basename(__FILE__) . ')' ); // Exit if nonce fails

$eeSFL_Log[] = 'Loaded: ee-functions';




// Detect upward path traversal
function eeSFL_DetectUpwardTraversal($eeFilePath) {

	global $eeSFL_Log;
	
	$eeUserPath = ABSPATH . dirname($eeFilePath);  // This could be problematic with things like ../
	$eeRealPath = realpath( ABSPATH . dirname($eeFilePath) ); // Expunge the badness and then compare...
	
	if ($eeUserPath != $eeRealPath) { // They must match
	    $eeSFL_Log['errors'] = 'Error 99'; // The infamous Error 99
	    wp_die('Error 99 :-('); // Bad guy found, bail out :-(
	}

}





// Check for the Upload Folder, Create if Needed
function eeSFL_FileListDirCheck($eeFileListDir) {
	
	global $eeSFL_Log, $eeSFL;
	
	// Set some standards
	if(strpos($eeFileListDir, '.') === 0 OR strpos($eeFileListDir, 'p-admin') OR strpos($eeFileListDir, 'p-includes') ) {
		$eeSFL_Log['errors'][] = 'This File List Location is Not Allowed: ' . $eeFileListDir;
		return FALSE;
	}
	
	$eeSFL_FileListDirCheck = get_transient('eeSFL-' . $eeSFL->eeListID . '-FileListDirCheck');
	
	// Check Transient First
	if( strlen($eeSFL_FileListDirCheck) AND $eeFileListDir == $eeSFL_FileListDirCheck) {
		
		return TRUE; // OKAY, No Change
		
	} elseif( strlen($eeFileListDir) ) { // Transient Expired or Dir Changed
	
		$eeSFL_Log[] = 'Transient Expired or Folder Change...';
		
		if( !is_writable( ABSPATH . $eeFileListDir ) ) {
			
			$eeSFL_Log[] = 'No Directory Found.';
			$eeSFL_Log[] = 'Creating Upload Directory ...';
			
			// Environment Detection
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			    $eeSFL_Log[] = 'Windows detected.';
			    mkdir( ABSPATH . $eeFileListDir ); // Windows
			} else {
			    $eeSFL_Log[] = 'Linux detected.';
			    if(!mkdir( ABSPATH . $eeFileListDir , 0755)) { // Linux - Need to set permissions
				    $eeSFL_Log['errors'][] = 'Cannot Create: ' . $eeFileListDir;
				}
			}
			
			if(!is_writable( ABSPATH . $eeFileListDir )) {
				$eeSFL_Log['errors'][] = 'ERROR: I could not create the upload directory: ' . $eeFileListDir;
				
				return FALSE;
			
			} else {
				
				$eeSFL_Log[] = 'FileListDir Has Been Created!';
				$eeSFL_Log[] = $eeFileListDir;
			}
		} else {
			$eeSFL_Log[] = 'FileListDir Looks Good';
		}
		
		// Check index.html, create if needed.
				
		$eeFile = ABSPATH . $eeFileListDir . 'index.html'; // Disallow direct file indexing.
		
		if($handle = @fopen($eeFile, "a+")) {
			
			if(!@is_readable($eeFile)) {
			    
				$eeSFL_Log['errors'][] = 'ERROR: Could not write index.html';
				
				return FALSE;
				
			} else {
				
				fclose($handle);
				
				// $eeSFL_Log[] = 'index.html is in place.';
			}
		}
		
		
	
		// Set Transient
		set_transient('eeSFL-' . $eeSFL->eeListID . '-FileListDirCheck', $eeFileListDir, 86400); // 1 Expires in Day

		return TRUE;
		
	} else {
		
		return FALSE;
	}
	
}







// Post-process an upload job
function eeSFL_ProcessUpload($eeSFL_ID) {
	
	global $eeSFL, $eeSFL_Config, $eeSFL_Log;
	
	$eeOutput = FALSE;
	
	$eeFileCount = filter_var(@$_POST['eeSFL_FileCount'], FILTER_VALIDATE_INT);
	$eeSFLF_UploadFolder = filter_var(@$_POST['eeSFLF_UploadFolder'], FILTER_SANITIZE_STRING);
	
	if($eeFileCount) {
		
		// Re-index the File List
		$eeFiles = $eeSFL->eeSFL_UpdateFileListArray($eeSFL_ID);
	
		$eeSFL_Log[] = 'Post-processinging Upload Job ...';
		$eeSFL_Log[] = $eeFileCount . ' Files';
		
		$eeFileList = stripslashes($_POST['eeSFL_FileList']);
		
		// Check for Nonce
		if(check_admin_referer( 'ee-simple-file-list-upload', 'ee-simple-file-list-upload-nonce')) {
			
			$eeArray = json_decode($eeFileList);
			$eeNewArray = array(); // For our lowered-case extensions
			
			// Drop file extensions to lowercase
			foreach( $eeArray as $eeFile) {  // We need to do this here and in ee-upload-engine.php
				$eeArray = explode('.', $eeFile);
				$eeNewArray[] = $eeArray[0] . '.' . strtolower($eeArray[1]);
			}
			$eeArray = $eeNewArray;
			
			
			// Notification
			if( count($eeArray) ) {
				
				$eeUploadJob = array(); // This will be what happened
				$eeUploadJob['Message'] = __('You should know that', 'ee-simple-file-list') . ' ';
				
				// Semantics
				if($eeFileCount > 1) { 
					$eeUploadJob['Message'] .= $eeFileCount . ' ' . __('files have', 'ee-simple-file-list');	
				} else {
					$eeUploadJob['Message'] .= __('a file has', 'ee-simple-file-list');
				}
				$eeUploadJob['Message'] .= ' ' . __('been uploaded to your website', 'ee-simple-file-list') . "." . PHP_EOL . PHP_EOL;
				
				// Loop through the uploaded files
				if(count($eeArray)) {
					
					foreach($eeArray as $eeFile) { 
						
						$eeFile = eeSFL_SanitizeFileName($eeFile);
						
						// Notification
						$eeUploadJob['Message'] .=  $eeSFLF_UploadFolder . $eeFile . PHP_EOL . 
							$eeSFL_Config['FileListURL'] . $eeSFLF_UploadFolder . $eeFile . 
								"(" . eeSFL_GetFileSize( $eeSFL_Config['FileListDir'] . $eeSFLF_UploadFolder . $eeFile ) . ")" . PHP_EOL . PHP_EOL;
					
								
						// Add submitter data to file list
						if($eeSFL_Config['GetUploaderInfo'] == 'YES') {
						
							$eeSFL_Log[] = 'Adding submitter info...';
							
							foreach( $eeFiles as $eeKey => $eeArray2) {
								
								// $eeSFL_Log[] = 'Does ' . $eeArray2['FilePath'] . ' == ' . $eeSFLF_UploadFolder . $eeFile;
								
								if( $eeArray2['FilePath'] ==  $eeSFLF_UploadFolder . $eeFile) {
									
									// $eeSFL_Log[] = 'MATCH';
									
									$eeSFL->eeSFL_UpdateFileDetail($eeSFL_ID, $eeFile, 'SubmitterName', filter_var($_POST['eeSFL_Name'], FILTER_SANITIZE_STRING));
									$eeSFL->eeSFL_UpdateFileDetail($eeSFL_ID, $eeFile, 'SubmitterEmail', filter_var($_POST['eeSFL_Email'], FILTER_VALIDATE_EMAIL));
									$eeSFL->eeSFL_UpdateFileDetail($eeSFL_ID, $eeFile, 'SubmitterComments', filter_var($_POST['eeSFL_Comments'], FILTER_SANITIZE_STRING));
								}
							}
						}
					}
						
					$eeSFL_Log['messages'][] = __('File Upload Complete', 'ee-simple-file-list');
					
					if( is_admin() ) {
						$eeOutput = TRUE;
					} else  {
						$eeOutput = $eeSFL->eeSFL_AjaxEmail( $eeUploadJob, $eeSFL_Config['Notify'] );// Send Email Notice
					}
				
				} else {
					$eeSFL_Log['errors'][] = 'Bad File Array';
					$eeSFL_Log['errors'][] = $eeUploadJob;
					return FALSE;
				}
			}
			
		} else {
			wp_die();
		}
	
	} else {
		$eeSFL_Log['errors'][] = 'No ID or Files';
		return FALSE;
	}
	
	return $eeOutput; // All done.
}






// Return the general size of a file in a nice format.
function eeSFL_GetFileSize($eeSFL_File) {  
    
    $bytes = filesize(ABSPATH . $eeSFL_File);
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





// Check if a file's URL is reachable
function eeSFL_UrlExists($eeSFL_FileURL) {
	
	global $eeSFL_Log, $eeSFL;
	
	$parts = parse_url($eeSFL_FileURL);
	
	if($parts) { 
	 
		// CURL
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_URL, $eeSFL_FileURL);
		
		// Set a User Agent for logs
		curl_setopt($connection, CURLOPT_USERAGENT, 'EE-Simple-File-List');
		curl_setopt($connection, CURLOPT_RETURNTRANSFER,1);
		
		// Don't follow redirects
		curl_setopt($connection, CURLOPT_FOLLOWLOCATION, 0); 
		
		// Timeout?
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($connection, CURLOPT_TIMEOUT, 2);
		     
		// Get the header
		curl_setopt($connection, CURLOPT_NOBODY, true);
		curl_setopt($connection, CURLOPT_HEADER, true);
		     
		// Handle HTTPS links
		if($parts['scheme'] == 'https'){
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST,  2);
			curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
		}
		 
		// Get the response
		$response = curl_exec($connection);
		curl_close($connection);
		
		$responseArray = explode(' ', $response);
		
		if(@$responseArray[1] == 200){
		
			$success = TRUE;
		
		} else { 
		  
		  	$success = FALSE;
		  	$eeSFL_Log[] = array($response); // No code found
		}
	
	} else {
		$response = '[Bad URL]';
		$code = filter_var($eeSFL_FileURL, FILTER_SANITIZE_STRING);
	}
	
	// Success ?
	if($success) {
		
		// $eeSFL_Log[] = 'File Return Code: ' . $response;
	  
	  	return $eeSFL_FileURL; // Yep
	
	} else { // Nope - Write errors to log file
	
		$eeSFL_Log['errors'][] = 'File Read Error - Could not read the file :-(';
		$eeSFL_Log['errors'][] = 'Response: ' . @$response;
		$eeSFL_Log['errors'][] = 'Code: ' . @$code;
		
		return FALSE;
	} 
}




// Make sure the file name is acceptable
function eeSFL_SanitizeFileName($eeSFL_FileName) {
	
	$eeSFL_FileName = sanitize_file_name( $eeSFL_FileName );
    
    return $eeSFL_FileName;
}



// Yes or No Settings Checkboxes
function eeSFL_ProcessCheckboxInput($eeTerm) {
	
	$eeValue = filter_var(@$_POST['ee' . $eeTerm], FILTER_SANITIZE_STRING);
	
	if($eeValue == 'YES') { return 'YES'; } else { return 'NO'; }
	
}




// Check if a file already exists, then number it so file will not be over-written.
function eeSFL_CheckForDuplicateFile($eeSFL_FilePathAdded) { // Full path from WP root
	
	global $eeSFL_Config;
	$eeCopyLimit = 1000; // File copies limit
	$eeDir = dirname($eeSFL_FilePathAdded) . '/';
		
	$eePathParts = pathinfo($eeSFL_FilePathAdded);
	$eeNameOnly = $eePathParts['filename'];
	$eeExtension = strtolower($eePathParts['extension']);
	
	$eeSFL_Files = get_option('eeSFL-FileList-' . $eeSFL_Config['ID']); // Our array of file info
	
	foreach($eeSFL_Files as $eeArray) { // Loop through file array and look for a match.
		
		$eeFilePath = $eeArray['FilePath']; // Get the /folder/name.ext
		
		// Check Transient
		if( $eeSFL_FilePathAdded == $eeSFL_Config['FileListDir'] . $eeFilePath ) { // Duplicate found
		
			if( is_file(ABSPATH . $eeSFL_FilePathAdded) ) { // Confirm the file is really there
				
				for ($i = 1; $i <= $eeCopyLimit; $i++) { // Look for existing copies
					
					$eeSFL_FilePathAdded = $eeDir . $eeNameOnly . '_(' . $i . ').' . $eeExtension; // Indicate the copy number
					
					if(!is_file(ABSPATH . $eeSFL_FilePathAdded)) { break; } // If no copy is there, we're done.
				}							
			}
		}
	}
		
	return 	$eeSFL_FilePathAdded; // Return the new file name and path
}




// Return the general size of a file in a nice format.
function eeSFL_FormatFileSize($eeFileSizeBytes) {  
    
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
function eeSFL_ResultsDisplay($eeSFL_Results, $eeResultType) { // error, updated, etc...
	
	$eeReturn = '<div class="';
	
	if(is_admin()) {
		$eeReturn .= $eeResultType;
	} else {
		$eeReturn .= 'eeResult';
	}
	
	$eeReturn .= '"><p>';
	$eeReturn .= eeSFL_MessageDisplay($eeSFL_Results); // Parse the message array
	$eeReturn .= '</p></div>';
	
	return $eeReturn;
}




// Problem Display / Error reporting
function eeSFL_MessageDisplay($eeSFL_Message) {
	
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
function eeSFL_ReturnHeaderString($eeFrom, $eeCc = FALSE, $eeBcc = FALSE) {
	
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
function eeSFL_ProcessEmailString($eeString) {
	
	$eeString = filter_var($eeString, FILTER_SANITIZE_STRING);
	
	if( strpos($eeString, ',') ) { // More than one address?
		
		$eeArray = explode(',', $eeString);
		
		$eeAddresses = ''; // Reset
		
		foreach( $eeArray as $eeEmail) {
			
			$eeEmail = filter_var($eeEmail, FILTER_VALIDATE_EMAIL);
			
			if($eeEmail) {
				
				$eeAddresses .= $eeEmail . ','; // Reassemble validated addresses
			}
		}
		
		$eeAddresses = substr($eeAddresses, 0, -1); // Strip the last comma
	
	} else {
		
		$eeAddresses = filter_var($eeString, FILTER_VALIDATE_EMAIL);
	}
	
	if( strpos($eeAddresses, '@') ) {
		
		return $eeAddresses;
		
	} else {
		
		return FALSE;
	}
}







// Get what's in the address bar
function eeSFL_GetThisURL() {
	
	$thisUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

/*
	$eeProtocol = strtolower($_SERVER['SERVER_PROTOCOL']);
	if(strpos($eeProtocol, 'ttps') == 1) { $eeProtocol = 'https'; } else { $eeProtocol = 'http'; }
	$eeHost = $_SERVER['HTTP_HOST'];
	$eeScript = $_SERVER['SCRIPT_NAME'];
	$eeParams = $_SERVER['QUERY_STRING'];
	 
	$thisUrl = $eeProtocol . '://' . $eeHost . $eeScript . '?' . $eeParams;
*/
	 
	return $thisUrl;

}

?>