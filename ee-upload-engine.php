<?php // Simple File List - ee-upload-engine.php - mitchellbennis@gmail.com
	
// This script is accessed via AJAX by ee-upload-form.php
	
ini_set("log_errors", 1);
error_reporting (E_ALL);
ini_set ('display_errors', FALSE);
ini_set("error_log", "logs/ee-upload-error2.log");
$eeSFL_Error = FALSE;

// The FILE object
if(empty($_FILES)) { 
	$eeSFL_Error = "Missing Input";
	trigger_error($eeSFL_Error, E_USER_ERROR);
	exit();
}

// The List ID
if(@$_POST['eeSFL_ID']) { $eeSFL_ID = filter_var($_POST['eeSFL_ID'], FILTER_VALIDATE_INT); } else { $eeSFL_ID = FALSE; }

if(!$eeSFL_ID) { 
	$eeSFL_Error = "Missing ID";
	trigger_error($eeSFL_Error, E_USER_ERROR);
	exit();
}


// The Upload Destination
if(@$_POST['eeSFL_FileListDir']) {
	$eeSFL_FileListDir = filter_var($_POST['eeSFL_FileListDir'], FILTER_SANITIZE_STRING);
	$eeSFL_FileListDir = urldecode($eeSFL_FileListDir);
	
	if(!$eeSFL_FileListDir) { trigger_error('No Upload Folder !!!', E_USER_ERROR); exit(); }
		
} else { 
	trigger_error('No Upload Folder Given', E_USER_ERROR);
	exit();
}

// ---------------------------------

// Tie into Wordpress
define('WP_USE_THEMES', false);
$wordpress = getcwd() . '/../../../wp-blog-header.php';
require($wordpress);

// WP Security
function eeSFL_CheckNonce() {
	
	if( !check_ajax_referer( 'ee-simple-file-list-upload', 'ee-simple-file-list-upload-nonce', FALSE ) ) {
		trigger_error("WP AJAX Error", E_USER_ERROR);
		exit();
	}	
}
add_action( 'plugins_loaded', 'eeSFL_CheckNonce' );



// Check size
$eeSFL_FileSize = $_FILES['file']['size'];
$eeSFL_UploadMaxFileSize = $eeSFL_Config['UploadMaxFileSize']*1024*1024; // Convert MB to B

if($eeSFL_FileSize > $eeSFL_UploadMaxFileSize) {
	$eeSFL_Error = "File size is too large.";
	trigger_error($eeSFL_Error, E_USER_ERROR);
	exit();
}

// Go...
if(is_dir(ABSPATH . $eeSFL_FileListDir)) {
		
	// More Security
	$verifyToken = md5('unique_salt' . $_POST['eeSFL_Timestamp']);
	
	if($_POST['eeSFL_Token'] == $verifyToken) { 
		
		// Temp file
		$eeTempFile = $_FILES['file']['tmp_name'];
		
		// Clean up messy names
		$eeSFL_FileName = eeSFL_SanitizeFileName($_FILES['file']['name']);
		
		$eeSFL_PathParts = pathinfo($eeSFL_FileName);
		$eeSFL_FileNameAlone = $eeSFL_PathParts['filename'];
		$eeSFL_Extension = strtolower($eeSFL_PathParts['extension']); // We need to do this here and in eeSFL_ProcessUpload()
		
		// Format Check
		$eeSFL_FileFormatsArray = array_map('trim', explode(',', $eeSFL_Config['FileFormats']));
		
		if(!in_array($eeSFL_Extension, $eeSFL_FileFormatsArray)) {
			$eeSFL_Error = 'File type not allowed: (' . $eeSFL_Extension . ')';
			trigger_error($eeSFL_Error, E_USER_ERROR);
			exit($eeSFL_Error);	
		}
		
		// Assemble full name
		$eeSFL_TargetFile = $eeSFL_FileListDir . $eeSFL_FileNameAlone . '.' . $eeSFL_Extension;
		
		// Check if it already exists
		$eeSFL_TargetFile = eeSFL_CheckForDuplicateFile($eeSFL_TargetFile);
		
		// exit('Target: ' . $eeSFL_TargetFile);
		
		$eeTarget = ABSPATH . $eeSFL_TargetFile;
		
		// Save the file
		if( move_uploaded_file($eeTempFile, $eeTarget) ) {
			
			if(!is_file($eeTarget)) {
				$eeSFL_Error = 'Error - File System Error.'; // No good.
			}
			 
		} else {
			$eeSFL_Error = 'Cannot save the uploaded file: ' . $eeSFL_TargetFile;
		}
	
	} else {
		
		$eeSFL_Error = 'ERROR 99';
	}
	
} else {
	$eeSFL_Error = 'Upload Path Not Found: ' . $eeSFL_Path;
}

// Timer
$eeSFL_Time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
$eeSFL_Log[] = 'Execution Time: ' . round($eeSFL_Time,3);

// Errors ?
if($eeSFL_Error) { $eeSFL_Log['errors'] = $eeSFL_Error; }

// Write to the log file to the Database
$eeSFL->eeSFL_WriteLogData($eeSFL_Log);

// Are we good?
if($eeSFL_Error) { trigger_error($eeSFL_Error, E_USER_WARNING); } else { echo 'SUCCESS'; }

?>