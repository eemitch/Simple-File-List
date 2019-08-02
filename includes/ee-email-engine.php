<?php // Simple File List - ee-email-engine.php - mitchellbennis@gmail.com
	
// This script is accessed via AJAX
	
ini_set("log_errors", 1);
error_reporting (E_ALL);
ini_set ('display_errors', FALSE);
ini_set("error_log", "../logs/ee-email-error.log");

if(@$_POST['eeSFL_Body']) { 
	
	$eeSFL_Body = $_POST['eeSFL_Body'];
	
	// $eeSFL_Body = json_decode($eeSFL_Body);
	
	$eeSFL_Body = filter_var($eeSFL_Body, FILTER_SANITIZE_STRING);
	$eeSFL_Body = strip_tags($eeSFL_Body);
	$eeSFL_Body = htmlspecialchars_decode($eeSFL_Body, ENT_QUOTES);
	$eeSFL_Body = strip_tags($eeSFL_Body);
	$eeSFL_Body = stripslashes($eeSFL_Body);
	
	if(!$eeSFL_Body) {
		trigger_error("eeSFL_Body is NULL", E_USER_ERROR);
		exit;
	}
	
	$eeSFL_Notify = filter_var($_POST['eeSFL_Notify'], FILTER_SANITIZE_STRING);
	
	if(!$eeSFL_Notify) {
		trigger_error("eeSFL_Notify is NULL", E_USER_ERROR);
		exit;
	}

} else {  
	
	trigger_error("Missing eeSFL_Body", E_USER_ERROR);
	exit;
}

// Tie into Wordpress
define('WP_USE_THEMES', FALSE);
$wordpress = getcwd() . '/../../../../wp-blog-header.php';
if(!is_file($wordpress)) { trigger_error("No Wordpress", E_USER_ERROR); exit; }
require($wordpress);

// WP Security
function eeSFL_CheckNonce() {
	
	if( !check_ajax_referer( 'ee-simple-file-list-email', 'ee-simple-file-list-email-nonce', FALSE ) ) {
		trigger_error("WP AJAX Error", E_USER_ERROR);
		wp_die();
	}	
}
add_action( 'plugins_loaded', 'eeSFL_CheckNonce' );

$verifyToken = md5('eeSFL_' . $_POST['eeSFL_Timestamp']);
	
if($_POST['eeSFL_Token'] == $verifyToken) { // Security

	$eeSFL_AdminEmail = get_option('admin_email');
	
	$eeSFL_Headers = "From: " . __('Simple File List', 'ee-simple-file-list') . " <$eeSFL_AdminEmail>\n\rReturn-Path: $eeSFL_AdminEmail\n\rReply-To: $eeSFL_AdminEmail";
	$eeSFL_Subject = __('File Upload Notice', 'ee-simple-file-list');
	
	if(strpos($eeSFL_Notify, ',')) {
		$eeArray = explode(',', $eeSFL_Notify); // Many
	} else {
		$eeArray = array($eeSFL_Notify); // Just one
	}
	
	foreach( $eeArray as $eeEmail) {
		
		if(!wp_mail($eeEmail, $eeSFL_Subject, $eeSFL_Body, $eeSFL_Headers)) { // Email Notice
			trigger_error('Mail Did Not Send to ' . $eeSFL_Notify, E_USER_ERROR);
			exit;
		}
	}
	
	exit('SENT');
	
		
} else {
	trigger_error('Token Failure', E_USER_ERROR);
}
	
?>