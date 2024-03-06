<?php // Simple File List Script: ee-functions.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Functions' ) ) exit('ERROR 98'); // Exit if nonce fails


// Get Elapsed Time
function eeSFL_BASE_noticeTimer() { // LEGACY 6.1.11 and Under
	global $eeSFL_BASE;
	$eeSFL_BASE->eeSFL_NOW();
}



function eeSFL_BASE_CheckSupported() {
	
	global $eeSFL_BASE;
	
	$eeSFL_BASE->eeLog['notice'][] = $eeSFL_BASE->eeSFL_NOW() . ' - Checking Supported ...';

	
	// Check for supported technologies
	$eeSupported = array();

    // Check for ffMpeg
    if(function_exists('shell_exec')) {
	    
		$eeSupported[] = 'Shell';
		
		if(shell_exec('ffmpeg -version')) {
			$eeSupported[] = 'ffMpeg';
			$eeSFL_Log[eeSFL_Go]['Supported'][] = 'Supported: ffMpeg';
		} else {
			$eeSFL_BASE->eeLog['notice'][] = '---> shell_exec("ffMpeg") FAILED';
		}
    } else {
	    $eeSFL_BASE->eeLog['notice'][] = '---> shell_exec() NOT SUPPORTED'; 
    }
    
    if($eeSFL_BASE->eeEnvironment['eeOS'] != 'WINDOWS') {
		
		// Check for ImageMagick
		$phpExt = 'imagick'; 
		if(extension_loaded($phpExt)) {
			$eeSupported[] = 'ImageMagick';
			$eeSFL_BASE->eeLog['Supported'][] = 'Supported: ImageMagick';
		}
		
		// Check for GhostScript
		if($eeSFL_BASE->eeEnvironment['eeOS'] == 'LINUX') { // TO DO - Make it work for IIS
		
			if(function_exists('exec')) {
			
				$phpExt = 'gs'; // <<<---- This will be different for Windows
				if(exec($phpExt . ' --version') >= 1.0) { // <<<---- This will be different for Windows too
					$eeSupported[] = 'GhostScript';
					$eeSFL_BASE->eeLog['Supported'][] = 'Supported: GhostScript';
				}
			} else {
				$eeSFL_BASE->eeLog['notice'][] = '---> exec() NOT SUPPORTED'; 
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
		
		$eeSFL_BASE->eeLog['errors'][] = __('Bad Directory Given', 'ee-simple-file-list') . ': ' . $eeFileListDir;
		
		return FALSE;
	}
		
	$eeSFL_BASE->eeLog['notice'][] = $eeSFL_BASE->eeSFL_NOW() . ' - Checking: ' . $eeFileListDir;
		
	if( !is_dir(ABSPATH . $eeFileListDir) ) { // Directory Changed or New Install
	
		$eeSFL_BASE->eeLog['notice'][] = $eeSFL_BASE->eeSFL_NOW() . ' - New Install or Directory Change...';
		
		if(!is_writable( ABSPATH . $eeFileListDir ) ) {
			
			$eeSFL_BASE->eeLog['notice'][] = $eeSFL_BASE->eeSFL_NOW() . ' - No Directory Found. Creating ...';
			
			if ($eeSFL_BASE->eeEnvironment['eeOS'] == 'WINDOWS') {
			    
			    if( !mkdir(ABSPATH . $eeFileListDir) ) {
				    
				    $eeSFL_BASE->eeLog['errors'][] = __('Cannot Create Windows Directory:', 'ee-simple-file-list') . ': ' . $eeFileListDir;
				}
			
			} elseif($eeSFL_BASE->eeEnvironment['eeOS'] == 'LINUX') {
			    
			    if( !mkdir(ABSPATH . $eeFileListDir , 0755) ) { // Linux - Need to set permissions
				    
				    $eeSFL_BASE->eeLog['errors'][] = __('Cannot Create Linux Directory:', 'ee-simple-file-list') . ': ' . $eeFileListDir;
				}
			} else {
				
				$eeSFL_BASE->eeLog['errors'][] = __('ERROR: Could not detect operating system', 'ee-simple-file-list');
				return FALSE;
			}
			
			if(!is_writable( ABSPATH . $eeFileListDir )) {
				$eeSFL_BASE->eeLog['errors'][] = __('Cannot create the upload directory', 'ee-simple-file-list') . ': ' . $eeFileListDir;
				$eeSFL_BASE->eeLog['errors'][] = __('Please check directory permissions', 'ee-simple-file-list');
			
				return FALSE;
			
			} else {
				
				$eeCopyMaunalFile = TRUE;
				
				$eeSFL_BASE->eeLog['notice'][] = $eeSFL_BASE->eeSFL_NOW() . ' - The File List Dir Has Been Created!';
				$eeSFL_BASE->eeLog['notice'][] = $eeFileListDir;
			}
		
		} else {
			$eeSFL_BASE->eeLog['notice'][] = $eeSFL_BASE->eeSFL_NOW() . ' - FileListDir Looks Good';
		}
		
	} 
	
	// Check index.html, create if needed.	
	if( strlen($eeFileListDir) >= 2 ) {	
		
		$eeFile = ABSPATH . $eeFileListDir . 'index.html'; // Disallow direct file indexing.
		
		if(!is_file($eeFile)) {
			
			if($eeHandle = fopen($eeFile, "a+")) {
				
				if(!is_readable($eeFile)) {
				    
					$eeSFL_BASE->eeLog['warnings'][] = __('WARNING! Could not write file', 'ee-simple-file-list') . ': index.html';
					$eeSFL_BASE->eeLog['warnings'][] = __('Please upload a blank index file to this location to prevent unauthorized access.', 'ee-simple-file-list');
					$eeSFL_BASE->eeLog['warnings'][] = ABSPATH . '/' . $eeFileListDir;
					
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







// Yes or No Settings Checkboxes
function eeSFL_BASE_ProcessCheckboxInput($eeTerm) {
	
	$eeValue = sanitize_text_field(@$_POST['ee' . $eeTerm]);
	
	if($eeValue == 'YES') { return 'YES'; } else { return 'NO'; }
}



// Sanitize Form Text Inputs 
function eeSFL_BASE_ProcessTextInput($eeTerm, $eeType = 'text') {
	
	$eeValue = '';
	
	if($eeType == 'email') {
		
		$eeValue = filter_var(sanitize_email(@$_POST['ee' . $eeTerm]), FILTER_VALIDATE_EMAIL);
	
	} elseif($eeType == 'textarea') {
		
		$eeValue = esc_textarea(sanitize_textarea_field( @$_POST['ee' . $eeTerm] ));
		
	} else {
		
		$eeValue = @strip_tags(@$_POST['ee' . $eeTerm]);
		$eeValue = esc_textarea(sanitize_text_field($eeValue));
	}
	
	return $eeValue;
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

?>