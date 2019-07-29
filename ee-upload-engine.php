<?php // Simple File List - ee-upload-engine.php - mitchellbennis@gmail.com
	
// This script is accessed via AJAX by ee-uploader.php
	
ini_set("log_errors", 1);
error_reporting (E_ALL);
ini_set ('display_errors', FALSE);
ini_set("error_log", "logs/ee-upload-error.log");

// The FILE object
if(empty($_FILES)) { 
	$eeSFL_Error = "Missing Input";
	trigger_error($eeSFL_Error, E_USER_ERROR);
	exit();
}

// The List ID
if(@$_POST['eeSFL_ID']) { $eeSFL->eeListID = filter_var($_POST['eeSFL_ID'], FILTER_VALIDATE_INT); } else { $eeSFL->eeListID = FALSE; }

if(!$eeSFL->eeListID) { 
	$eeSFL_Error = "Missing ID";
	trigger_error($eeSFL_Error, E_USER_ERROR);
	exit();
}


// The Upload Destination
if(@$_POST['eeSFL_FileListDirName']) {
	$eeSFL_FileListDir = filter_var($_POST['eeSFL_FileListDirName'], FILTER_SANITIZE_STRING);
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

// Get our options
$eeSFL_FileFormats = get_option('eeSFL-' . $eeSFL->eeListID . '-FileFormats');
$eeSFL_UploadMaxFileSize = get_option('eeSFL-' . $eeSFL->eeListID . '-UploadMaxFileSize');

// Check size
$eeSFL_FileSize = $_FILES['file']['size'];
$eeSFL_UploadMaxFileSize = $eeSFL_UploadMaxFileSize*1024*1024; // Convert MB to B

if($eeSFL_FileSize > $eeSFL_UploadMaxFileSize) {
	$eeSFL_Error = "File size is too large.";
	trigger_error($eeSFL_Error, E_USER_ERROR);
	exit();
}

// Our file destination.
$eeSFL_Path = ABSPATH . $eeSFL_FileListDir; // Need this for here

// Go...
if(is_dir($eeSFL_Path)) {
		
	// More Security
	$verifyToken = md5('unique_salt' . $_POST['timestamp']);
	
	if($_POST['token'] == $verifyToken) { 
		
		// Temp file
		$tempFile = $_FILES['file']['tmp_name'];
		
		// Clean up messy names
		
		$eeSFL_FileName = eeSFL_SanitizeFileName($_FILES['file']['name']);
		
		$eeSFL_PathParts = pathinfo($eeSFL_FileName);
		$eeSFL_FileNameAlone = $eeSFL_PathParts['filename'];
		$eeSFL_Extension = strtolower($eeSFL_PathParts['extension']);
		
		// Format Check
		$eeSFL_FileFormatsArray = array_map('trim', explode(',', $eeSFL_FileFormats));
		
		if(!in_array($eeSFL_Extension, $eeSFL_FileFormatsArray)) {
			$eeSFL_Error = 'File type not allowed: (' . $eeSFL_Extension . ')';
			trigger_error($eeSFL_Error, E_USER_ERROR);
			exit($eeSFL_Error);	
		}
		
		// Assemble full name
		$eeSFL_TargetFile = $eeSFL_Path . $eeSFL_FileNameAlone . '.' . $eeSFL_Extension;
		
		// trigger_error('Target File: ' . $eeSFL_TargetFile, E_USER_NOTICE);
		
		// Check if it already exists
		$eeSFL_TargetFile = eeSFL_CheckForDuplicateFile($eeSFL_Path, $eeSFL_FileNameAlone . '.' . $eeSFL_Extension);
	
		// Save the file
		if(move_uploaded_file($tempFile, $eeSFL_TargetFile)) {
			
			if(!is_file($eeSFL_TargetFile)) {
				$eeSFL_Error = 'Error - File System Error.'; // No good.
			} else {
				// SUCCESS
				exit('SUCCESS');
			}
		} else {
			$eeSFL_Error = 'Cannot move the uploaded file: ' . $eeSFL_TargetFile;
		}
	
	} else {
		
		$eeSFL_Error = 'Post Token does NOT match verification token';
	}
	
} else {
	$eeSFL_Error = 'Path Not Found: ' . $eeSFL_Path;
}

// Output
if($eeSFL_Error) {
	trigger_error($eeSFL_Error, E_USER_WARNING);
	exit();
}
	
?>