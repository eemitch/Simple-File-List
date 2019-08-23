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
	exit($eeSFL_Error);
}

// The Action
if(@$_POST['eeFileAction']) { 
	$eeFileAction = filter_var($_POST['eeFileAction'], FILTER_SANITIZE_STRING); 
} else { 
	$eeSFL_Error = "Missing the Action";
	trigger_error($eeSFL_Error, E_USER_ERROR);
	exit($eeSFL_Error);
}


// Tie into Wordpress
define('WP_USE_THEMES', false); // Just the core please
$wordpress = getcwd() . '/../../../wp-blog-header.php'; // Starting at this plugin's home dir
require($wordpress); // Get all that wonderfullness



// WP Security
function eeSFL_CheckNonce() {
	
	if( !check_ajax_referer( 'ee-simple-file-list-upload', 'ee-simple-file-list-upload-nonce', FALSE ) ) {
		$eeSFL_Error = "WP AJAX Error";
		trigger_error($eeSFL_Error, E_USER_ERROR);
		exit($eeSFL_Error);
	}	
}
add_action( 'plugins_loaded', 'eeSFL_CheckNonce' );


if($eeFileAction == 'Rename') {
		
	// The File Path
	if(@$_POST['eeFilePath']) { 
		$eeFilePath = urldecode( filter_var($_POST['eeFilePath'], FILTER_SANITIZE_STRING) ); 
	} else { 
		$eeSFL_Error = "Missing the File";
		trigger_error($eeSFL_Error, E_USER_ERROR);
		exit($eeSFL_Error);
	}
	
	eeSFL_DetectUpwardTraversal($eeFilePath); // Die if foolishness
	
	// If Renaming
	if( strpos($eeFileAction, '|') ) {
		$eeArray = explode('|', $eeFileAction);
		$eeFileAction = $eeArray[0];
		$eeNewFileName = urldecode( $eeArray[1] );
	}
	
	if($eeNewFileName) {
	
		// If Renaming a File/Folder
		$eeNewFileName = dirname($eeFilePath) . '/' . eeSFL_SanitizeFileName( basename($eeNewFileName) );
	
		$eeString = 'Renaming: ' . $eeFilePath . ' to ' . $eeNewFileName;
			
		$eeSFL_Log[] = $eeString;
		
		if( !rename(ABSPATH . $eeFilePath, ABSPATH . $eeNewFileName) ) {
			
			$eeSFL_Log['errors'][] = 'Could Not Rename ' . $eeOldFileName . ' to ' . $eeNewFileName;
		
		}
	}
	
	// Re-index the File List
	$eeSFL_Files = $eeSFL->eeSFL_UpdateFileListArray($eeSFL_ID);
	
	
} elseif($eeFileAction == 'Delete') {
		
	// The File Path
	if(@$_POST['eeFilePath']) { 
		$eeFilePath = urldecode( filter_var($_POST['eeFilePath'], FILTER_SANITIZE_STRING) ); 
	} else { 
		$eeSFL_Error = "Missing the File";
		trigger_error($eeSFL_Error, E_USER_ERROR);
		exit($eeSFL_Error);
	}
	
	eeSFL_DetectUpwardTraversal($eeFilePath); // Die if foolishness
	
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
	
	// Re-index the File List
	$eeSFL_Files = $eeSFL->eeSFL_UpdateFileListArray($eeSFL_ID);
	
	
	
} elseif($eeFileAction == 'UpdateDesc') {
	
	// The Description
	if($_POST['eeFileID'] OR @$_POST['eeFileID'] === 0) { 
		$eeFileID = filter_var($_POST['eeFileID'], FILTER_VALIDATE_INT); 
	} else { 
		$eeSFL_Error = "Missing the File ID";
		trigger_error($eeSFL_Error, E_USER_ERROR);
		exit($eeSFL_Error);
	}
	
	// The Description
	if(@$_POST['eeFileDesc']) { 
		$eeFileDesc = filter_var($_POST['eeFileDesc'], FILTER_SANITIZE_STRING); 
	} else { 
		$eeSFL_Error = "Missing the Description";
		trigger_error($eeSFL_Error, E_USER_ERROR);
		exit($eeSFL_Error);
	}
	
	// Get the file array
	$eeFileArray = get_option('eeSFL-FileList-' . $eeSFL_ID);
	
	foreach( $eeFileArray as $eeKey => $eeThisFileArray ) {
		
		if($eeKey == $eeFileID) {
					
				$eeFileArray[$eeFileID]['FileDescription'] = $eeFileDesc;
		}
	}
		
	// Save the updated array
	$eeFileArray = update_option('eeSFL-FileList-' . $eeSFL_ID, $eeFileArray);
	
	
} else {
	
	echo 'Nothing to do';
	
}

// Timer
$eeSFL_Time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
$eeSFL_Log[] = 'Execution Time: ' . round($eeSFL_Time,3);

// Write to the log file to the Database
$eeSFL->eeSFL_WriteLogData($eeSFL_Log); 


echo 'SUCCESS';

?>