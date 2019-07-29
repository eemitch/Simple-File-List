<?php // Simple File List Support - mitch@elementengage.com
	
// Rev 03.08.19 
	
// Accessed via http://website.com/wp-content/plugins/simple-file-list/logs/index.php?eePIN=2006

// The point of this script is to allow me, Mitch, and only me, to access all of the basic info and error data in one page.
// I believe in good service and support.

// Must have the proper PIN in order to access the page
$eePIN = filter_var(@$_GET['eePIN'], FILTER_VALIDATE_INT);

// Must come from EE
$eeReferer = filter_var($_SERVER['HTTP_REFERER'], FILTER_SANITIZE_STRING);
$eeRefererMust = 'elementengage.com/simple-file-list-wordpress-plugin/support';

if($eePIN == 2006 AND strpos($eeReferer, $eeRefererMust) ) { // PIN and Referer must match
		
	// Attempt to turn on basic PHP logging...
	ini_set('display_errors', TRUE);
	error_reporting(E_ALL);
	
	// Get what's in this address bar
	$eeProtocol = strtolower($_SERVER['SERVER_PROTOCOL']);
	if(strpos($eeProtocol, 'ttps') == 1) { $eeProtocol = 'https'; } else { $eeProtocol = 'http'; }
	$eeHost = $_SERVER['HTTP_HOST'];
	
	// Add the rest...
	$eeThisWP = $eeProtocol . '://' . $eeHost;
	$eeThisURL = $eeThisWP . '/wp-content/plugins/simple-file-list/logs/';
	
	// My log file names
	$eeUploadLog = 'ee-upload-error.log';
	$eeEmailLog = 'ee-email-error.log';
	
	// Read My Error Log Files
	$eeUploadLogContent = file_get_contents($eeThisURL . $eeUploadLog);
	$eeEmailLogContent = file_get_contents($eeThisURL . $eeEmailLog);
	
	// Tie into Wordpress
	define('WP_USE_THEMES', false);
	$wordpress = getcwd() . '/../../../../wp-blog-header.php';
	require($wordpress);
	
	// Get Database Log
	$eeLogFile = get_option('eeSFL-Log'); // Used below

	// PHP Log
	$eeLog = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/error_log';
	if(@filesize($eeLog) > 10) {
		$phpErrors = @file_get_contents($eeLog);
	} else {
		$phpErrors = FALSE;
	}
	
	// Wordpress Log
	$eeLog = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/debug.log';
	if(is_readable($eeLog)) {
		$wpErrors = @file_get_contents($eeLog);
	} else {
		$wpErrors = 'No Wordpress Error Log File :-(';
	}
	
	// Page Setup
	$eeTitle = 'Simple File List Support';
		
	?><!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $eeTitle; ?></title>

<style type="text/css">

* {
	margin: 1.5em;
}

body {
	width: 75%;
	margin-left: auto;
	margin-right: auto;
}

h1, h2, h3, p {
	text-align: left;
}

p.log, iframe {
	text-align: left;
	padding: 1em;
	border: 1px dashed #666;
	margin: 1em 0 2em 0;
}
p.log {
	height: 300px;
	overflow: scroll;
}

#eeLogFile {
	height: 500px;
	padding: 10px;
	overflow: scroll;
	border: 1px dashed #666;
}

</style>

</head>
<body>
	
	<p><a target="_blank" style="float:right;" href="#"><?php echo $eeProtocol . '://' . $eeHost; ?></a></p>
	
	<h1><?php echo $eeTitle; ?></h1>
	
	<?php // My plugin log files...
	
	// The Main Log File ?>
	
	<h3>The Main Log File</h3>
	
	<?php
		
	echo '<div id="eeLogFile"><pre>'; print_r($eeLogFile); echo '</pre></div>';	
		
	// Upload Errors
	if(!$eeUploadLogContent) {
	
		echo '<h3 style="color:green;">&#x2714; No Uploader Errors</h3>';
		
	} else { 
		
		echo '<h3 style="color:red;">Uploader Errors!</h4>';
		echo '<p class="log">' . $eeUploadLogContent . '</p>';
	}	
	
	// Email Errors
	if(!$eeEmailLogContent) {
	
		echo '<h3 style="color:green;">&#x2714; No Email Errors</h3>';
		
	} else { 
		
		echo '<h3 style="color:red;">Email Errors!</h4>';
		echo '<p class="log">' . $eeEmailLogContent . '</p>';
	}
		
		
	// The Server Environment
	
	// PHP Errors
	if(!$phpErrors) {
	
		echo '<h3 style="color:green;">&#x2714; No PHP Errors</h3>';
		
	} else { 
		
		echo '<h3 style="color:red;">PHP Errors!</h3>';
		echo '<p class="log">' . nl2br($phpErrors) . '</p>';
	}
	
	
	// WP Errors
	if(!$wpErrors) {
	
		echo '<h3 style="color:green;">&#x2714; No Wordpress Errors</h3>';
		
	} else { 
		
		echo '<h3 style="color:red;">Wordpress Errors!</h3>';
		echo '<p class="log">' . nl2br($wpErrors) . '</p>';
	}

	
	echo '<br /><br /><h2>PHP Environment</h2>';
		
	// Get Environment Info
	phpinfo(); ?>
	
	
</body>
</html><?php
	
} // End PIN check
	
?>