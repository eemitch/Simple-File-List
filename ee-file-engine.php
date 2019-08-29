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

// Are we in a Folder?
if(@$_POST['eeListFolder']) { 
	$eeListFolder = urldecode( filter_var($_POST['eeListFolder'], FILTER_SANITIZE_STRING) ); 
} else {
	$eeListFolder = '';
}

// exit($eeFileAction . '(' . $eeSFL_ID . ')');


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



if( strpos($eeFileAction, 'Rename') === 0 ) {

	// If Renaming
	if( strpos($eeFileAction, '|') ) {
		$eeArray = explode('|', $eeFileAction);
		$eeFileAction = $eeArray[0];
		$eeNewFileName = urldecode( $eeArray[1] );
		$eeNewFileName = eeSFL_SanitizeFileName($eeNewFileName);
	}
		
	// The File Name
	if(@$_POST['eeFileOld']) { 
		$eeOldFileName = filter_var( $_POST['eeFileOld'], FILTER_SANITIZE_STRING ); 
	} else { 
		$eeSFL_Error = "Missing the Current File Name";
		trigger_error($eeSFL_Error, E_USER_ERROR);
		exit($eeSFL_Error);
	}
	
	if($eeNewFileName) {
		
		eeSFL_DetectUpwardTraversal($eeSFL_Config['FileListDir'] . $eeNewFileName); // Die if foolishness
		
		$eeFullPath = ABSPATH . $eeSFL_Config['FileListDir'] . $eeListFolder;
		$eeOldFilePath = $eeFullPath . $eeOldFileName;
		$eeNewFilePath = $eeFullPath . $eeNewFileName;
	
		$eeString = 'Renaming: ' . $eeListFolder . $eeOldFileName . ' to ' . $eeListFolder . $eeNewFileName;
		
		// echo $eeString; exit;
		
		if( !rename($eeOldFilePath, $eeNewFilePath) ) {
			
			$eeSFL_Log['errors'][] = 'Could Not Rename ' . $eeOldFileName . ' to ' . $eeNewFileName;
		
		} else {
			
			$eeSFL->eeSFL_UpdateFileDetail($eeSFL_ID, $eeListFolder . $eeOldFileName, 'FilePath', $eeListFolder . $eeNewFileName);

			// Re-index the File List
			$eeSFL_Files = $eeSFL->eeSFL_UpdateFileListArray($eeSFL_ID);
		}
	
	} else { 
		$eeSFL_Error = "Missing the New File Name";
		trigger_error($eeSFL_Error, E_USER_ERROR);
		exit($eeSFL_Error);
	}
	
} elseif($eeFileAction == 'Delete') {
	
	// The File Path
	if(@$_POST['eeFileName']) { 
		$eeFileName = filter_var( $_POST['eeFileName'], FILTER_SANITIZE_STRING ); 
	} else { 
		$eeSFL_Error = "Missing the File to Delete";
		trigger_error($eeSFL_Error, E_USER_ERROR);
		exit($eeSFL_Error);
	}
		
	eeSFL_DetectUpwardTraversal($eeSFL_Config['FileListDir'] . $eeListFolder . $eeFileName); // Die if foolishness
	
	$eeFilePath = ABSPATH . $eeSFL_Config['FileListDir'] . $eeListFolder . $eeFileName;
	
	if( strpos($eeFileName, '.') ) { // Gotta be a File - Looking for the dot rather than using is_file() for better speed
		
		if(unlink($eeFilePath)) {
			
			$eeSFL_Msg = __('Deleted the File', 'ee-simple-file-list') . ' &rarr; ' . $eeListFolder . $eeFileName;
			$eeSFL_Log[] = $eeSFL_Msg;
			$eeSFL_Log['messages'][] = $eeSFL_Msg;
			
		} else {
			$eeSFL_Msg = __('File Delete FAILED', 'ee-simple-file-list') . ':' . $eeListFolder . $eeFileName;
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
	if(filter_var($_POST['eeFileID'], FILTER_VALIDATE_INT) !== FALSE) { // Might be a zero
		$eeFileID = $_POST['eeFileID'];
	} else { 
		$eeFileID = 0;
	}
	
	// The Description
	if(@$_POST['eeFileDesc']) { 
		$eeFileDesc = filter_var($_POST['eeFileDesc'], FILTER_SANITIZE_STRING); 
	} else { 
		$eeFileDesc = '';
	}
	
	$eeSFL->eeSFL_UpdateFileDetail($eeSFL_ID, 'FileDescription', $eeFileDesc);
	
	// exit($eeFileDesc . '(' . $eeFileID . ')');
	
	
	// Get the file array
	$eeFileArray = get_option('eeSFL-FileList-' . $eeSFL_ID);
	
	foreach( $eeFileArray as $eeKey => $eeThisFileArray ) {
		
		if($eeKey == $eeFileID) {
					
				// exit( $eeFileArray[$eeFileID]['FilePath'] . ' - ' . $eeFileDesc . '(' . $eeFileID . ')');
				
				$eeFileArray[$eeFileID]['FileDescription'] = $eeFileDesc;
		}
	}
		
	// Save the updated array
	$eeFileArray = update_option('eeSFL-FileList-' . $eeSFL_ID, $eeFileArray);
	
	
} else {
	
	echo 'Nothing to do'; exit;
	
}

// Timer
$eeSFL_Time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
$eeSFL_Log[] = 'Execution Time: ' . round($eeSFL_Time,3);

// Write to the log file to the Database
$eeSFL->eeSFL_WriteLogData($eeSFL_Log); 


echo 'SUCCESS';

?>