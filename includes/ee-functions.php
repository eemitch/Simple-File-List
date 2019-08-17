<?php // Simple File List - General Plugin Functions - mitch@elementengage.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Functions' ) ) exit('That is Noncense! (' . basename(__FILE__) . ')' ); // Exit if nonce fails

$eeSFL_Log[] = 'Loaded: ee-functions';




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
function eeSFL_ProcessUpload($eeSFL_UploadURL, $eeSFL_FileListDir, $eeSFL_Notify) {
	
	global $eeSFL, $eeSFL_Log;
	
	$eeSFL_FileCount = filter_var($_POST['eeFileCount'], FILTER_VALIDATE_INT);
	
	if($eeSFL_FileCount) { 
	
		$eeSFL_Log[] = 'Post-processing Upload...';
		$eeSFL_Log[] = $eeSFL_FileCount . ' Files';
		
		$eeSFL_FileList = stripslashes($_POST['eeFileList']);
		
		// Check for Nonce
		if(check_admin_referer( 'ee-simple-file-list-upload', 'ee-simple-file-list-upload-nonce')) {
			
			$eeSFL_FileArray = json_decode($eeSFL_FileList);
			$eeNewArray = array(); // For our lowered-case extensions
			
			// Drop file extensions to lowercase
			foreach( $eeSFL_FileArray as $eeFile){
				$eeArray = explode('.', $eeFile);
				$eeNewArray[] = $eeArray[0] . '.' . strtolower($eeArray[1]);
			}
			$eeSFL_FileArray = $eeNewArray;
			
			// Files have been uploaded
			if(is_array($eeSFL_FileArray)) {
				
				$eeSFL_UploadJob = array(); // This will be what happened
				
				// Notification
				$eeSFL_UploadJob['Message'] = __('You should know that', 'ee-simple-file-list') . ' ';
				
				// Semantics
				if($eeSFL_FileCount > 1) { 
					$eeSFL_UploadJob['Message'] .= $eeSFL_FileCount . ' ' . __('files have', 'ee-simple-file-list');	
				} else {
					$eeSFL_UploadJob['Message'] .= __('a file has', 'ee-simple-file-list');
				}
				$eeSFL_UploadJob['Message'] .= ' ' . __('been uploaded to your website', 'ee-simple-file-list') . ".\n\n";
				
				// Loop through the uploaded files
				if(count($eeSFL_FileArray)) {
					
					foreach($eeSFL_FileArray as $eeSFL_File) { 
						
						$eeSFL_File = eeSFL_SanitizeFileName($eeSFL_File);
						
						// Notification
						$eeSFL_UploadJob['Message'] .=  $eeSFL_File . "\n" . 
							$eeSFL_UploadURL . $eeSFL_File . 
								"\n(" . @eeSFL_GetFileSize(ABSPATH . $eeSFL_FileListDir . $eeSFL_File) . ")\n\n\n";
					}
						
					$eeSFL_Log['messages'][] = __('File Upload Complete', 'ee-simple-file-list');
					
					// Send Email Notice
					$eeOutput = $eeSFL->eeSFL_AjaxEmail($eeSFL_UploadJob, $eeSFL_Notify);
					
					// Re-index the File List
					$eeSFL_Files = $eeSFL->eeSFL_createFileListArray($eeSFL->eeListID, $eeSFL_FileListDir, TRUE);
					
					return $eeOutput; // All done.
					
					
				} else {
					$eeSFL_Log['errors'][] = 'Bad File Array';
					$eeSFL_Log['errors'][] = $eeSFL_UploadJob;
					return FALSE;
				}
			}
			
		} else {
			exit;
		}
	
	} else {
		$eeSFL_Log['errors'][] = 'No files?';
		return FALSE;
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
		  	$eeSFL_Log = array($response); // No code found
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
			
	global $eeSFL_Config;
	
	if(@$_POST['eeShowList'] != 'NO') {
		$eeValue = filter_var(@$_POST['ee' . $eeTerm], FILTER_SANITIZE_STRING);
		if($eeValue == 'YES') { $eeSFL_Config[$eeTerm] = 'YES'; } 
			else { $eeSFL_Config[$eeTerm] = 'NO'; }
	}
}




// Check if a file already exists, then number it so file will not be over-written.
function eeSFL_CheckForDuplicateFile($eeSFL_DirPath, $eeSFL_TargetFile) {
	
	global $eeSFL_Config;
	
	$eeSFL_Files = $eeSFL_Config['eeSFL_Files'];
	$eeSFL_Files = get_transient('eeSFL-' . $eeSFL->eeListID . '-Files');
	
	// Check Transient First
	if(in_array($eeSFL_TargetFile, $eeSFL_Files) ) {
		
		if(is_file(ABSPATH . $eeSFL_DirPath . $eeSFL_TargetFile)) { // Confirm the file is really there
			
			// Get the file extension
			$eeSFL_Dot = strrpos($eeSFL_TargetFile, '.');
			$eeSFL_Extension = strtolower(substr($eeSFL_TargetFile, $eeSFL_Dot+1));
			
			// Append a version to the name
			$eeSFL_FilePath = substr($eeSFL_TargetFile, 0, $eeSFL_Dot);
			
			$eeSFL_CopyLimit = 1000; // Copy limit
			
			for ($i = 1; $i <= $eeSFL_CopyLimit; $i++) {
				
				$eeSFL_TargetFile = $eeSFL_FilePath . '_(' . $i . ').' . $eeSFL_Extension; // Indicate the copy number
				
				if(!is_file(ABSPATH . $eeSFL_DirPath . $eeSFL_TargetFile)) { break; }
			}							
		}
	}
		
	return 	$eeSFL_DirPath . $eeSFL_TargetFile;
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
		
		if(!$eeAdmin) { $eeReturn .= '<div id="eeMessageDisplay">'; }
		
		$eeReturn .= '<ul>'; // Loop through $eeSFL_Messages array
		foreach($eeSFL_Message as $key => $value) { 
			if(is_array($value)) {
				foreach ($value as $value2) {
					$eeReturn .= "<li>$value2</li>\n";
				}
			} else {
				$eeReturn .= "<li>$value</li>\n";
			}
		}
		$eeReturn .= "</ul>\n";
		
		if(!$eeAdmin) { $eeReturn .= '</div>'; }
		
		return $eeReturn;
		
	} else {
		return $eeSFL_Message;
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