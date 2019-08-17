<?php // Simple File List - ee-file-engine.php - mitchellbennis@gmail.com
	
// This script is accessed via AJAX by ee-list-display.php
	
ini_set("log_errors", 1);
error_reporting (E_ALL);
ini_set ('display_errors', FALSE);
ini_set("error_log", "logs/ee-file-error.log");


// The List ID
if(@$_POST['eeSFL_ID']) { $eeSFL_ID = filter_var($_POST['eeSFL_ID'], FILTER_VALIDATE_INT); } else { $eeSFL_ID = FALSE; }

if(!$eeSFL_ID) { 
	$eeSFL_Error = "Missing ID";
	trigger_error($eeSFL_Error, E_USER_ERROR);
	exit();
}


// The File Path
if(@$_POST['eeFilePath']) { 
	$eeFilePath = urldecode( filter_var($_POST['eeFilePath'], FILTER_SANITIZE_STRING) ); 
} else { 
	$eeSFL_Error = "Missing the File";
	trigger_error($eeSFL_Error, E_USER_ERROR);
	exit();
}

// The Action
if(@$_POST['eeFileAction']) { 
	$eeFileAction = filter_var($_POST['eeFileAction'], FILTER_SANITIZE_STRING); 
} else { 
	$eeSFL_Error = "Missing the Action";
	trigger_error($eeSFL_Error, E_USER_ERROR);
	exit();
}

// If Renaming
if( strpos($eeFileAction, '|') ) {
	$eeArray = explode('|', $eeFileAction);
	$eeFileAction = $eeArray[0];
	$eeNewFileName = urldecode( $eeArray[1] );
}


// ---------------------------------

// Tie into Wordpress
define('WP_USE_THEMES', false); // Just the core please
$wordpress = getcwd() . '/../../../wp-blog-header.php'; // Starting at this plugin's home dir
require($wordpress); // Get all that wonderfullness



// WP Security
function eeSFL_CheckNonce() {
	
	if( !check_ajax_referer( 'ee-simple-file-list-upload', 'ee-simple-file-list-upload-nonce', FALSE ) ) {
		trigger_error("WP AJAX Error", E_USER_ERROR);
		exit();
	}	
}
add_action( 'plugins_loaded', 'eeSFL_CheckNonce' );

// Detect upward path traversal
$eeUserPath = ABSPATH . dirname($eeFilePath);  // This could be problematic with things like ../
$eeRealPath = realpath( ABSPATH . dirname($eeFilePath) ); // Expunge the badness and then compare...

if ($eeUserPath != $eeRealPath) { // They must match
    $eeSFL_Log['errors'] = 'Error 99'; // The infamous Error 99
    exit('Error 99 :-('); // Bad guy found, bail out :-(
}

if($eeFileAction == 'Rename') {
		
	if($eeNewFileName) {
	
		// If Renaming a File/Folder
		$eeNewFileName = dirname($eeFilePath) . '/' . eeSFL_SanitizeFileName( basename($eeNewFileName) );
	
		$eeString = 'Renaming: ' . $eeFilePath . ' to ' . $eeNewFileName;
			
		$eeSFL_Log[] = $eeString;
		
		if( !rename(ABSPATH . $eeFilePath, ABSPATH . $eeNewFileName) ) {
			
			$eeSFL_Log['errors'][] = 'Could Not Rename ' . $eeOldFileName . ' to ' . $eeNewFileName;
		
		} else {
			
			// Re-index the File List
			$eeSFL->eeSFL_createFileListArray($eeSFL->eeListID, $eeSFL_Config['FileListDir'], 'Re-Index');
		}
	}
	
} elseif($eeFileAction == 'Delete') {
	
	if( strpos($eeFilePath, '.') ) { // Gotta be a File - Looking for the dot rather than using is_file() for better speed
		
		if(unlink(ABSPATH . $eeFilePath)) {
			
			$eeSFL_Msg = __('Deleted the File', 'ee-simple-file-list') . ' &rarr; ' . basename($eeFilePath);
			$eeSFL_Log[] = $eeSFL_Msg;
			$eeSFL_Log['messages'][] = $eeSFL_Msg;
			
		} else {
			$eeSFL_Msg = __('File Delete FAILED', 'ee-simple-file-list') . ':' . $eeSFL_File;
			$eeSFL_Log['errors'] = $eeSFL_Msg;
			$eeSFL_Log['messages'][] = $eeSFL_Msg;
		}
	} else {
		
		// Delete Folder
		
		
	}
}

// Re-index the File List
$eeSFL_Files = $eeSFL->eeSFL_createFileListArray($eeSFL_ID, $eeSFL_Config['FileListDir'], 'Re-Index');

// Timer
$eeSFL_Time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
$eeSFL_Log[] = 'Execution Time: ' . round($eeSFL_Time,3);

// Write to the log file to the Database
$eeSFL->eeSFL_WriteLogData($eeSFL_Log); 


echo 'SUCCESS';

?>