<?php

/**
 * @package Element Engage - Simple File List
 */
/*
Plugin Name: Simple File List
Plugin URI: http://simplefilelist.com
Description: A Basic File List Manager with File Uploader
Author: Mitchell Bennis
Version: 6.0.4
Author URI: http://simplefilelist.com
License: GPLv2 or later
Text Domain: ee-simple-file-list
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// CONSTANTS
define('eeSFL_BASE_DevMode', FALSE);
define('eeSFL_BASE_Version', '6.0.4'); // Plugin version
define('eeSFL_BASE_PluginName', 'Simple File List');
define('eeSFL_BASE_PluginSlug', 'ee-simple-file-list');
define('eeSFL_BASE_PluginDir', 'simple-file-list');
define('eeSFL_BASE_FileListDefaultDir', 'simple-file-list/'); // Default Upload Directory
define('eeSFL_BASE_PluginMenuTitle', 'File List');
define('eeSFL_BASE_PluginWebPage', 'https://simplefilelist.com');
define('eeSFL_BASE_AddOnsURL', 'https://get.simplefilelist.com/index.php');
define('eeSFL_BASE_AdminEmail', 'admin@simplefilelist.com');
define('eeSFL_BASE_Go', date('Y-m-d h:m:s') ); // Log Entry Key

// Our Core
$eeSFL_BASE = FALSE; // Our main class
$eeSFL_BASE_VarsForJS = array(); // Strings for JS

// simplefilelist_upload_job <<<----- File Upload Action Hooks (Ajax)
add_action( 'wp_ajax_simplefilelist_upload_job', 'simplefilelist_upload_job' );
add_action( 'wp_ajax_nopriv_simplefilelist_upload_job', 'simplefilelist_upload_job' );

// simplefilelist_edit_job <<<----- File Edit Action Hooks (Ajax)
add_action( 'wp_ajax_simplefilelist_edit_job', 'simplefilelist_edit_job' );
add_action( 'wp_ajax_nopriv_simplefilelist_edit_job', 'simplefilelist_edit_job' );


// Prevent All in One SEO plugin from parsing SFL
add_filter( 'aioseo_conflicting_shortcodes', 'eeSFL_BASE_aioseo_filter_conflicting_shortcodes' );

function eeSFL_BASE_aioseo_filter_conflicting_shortcodes( $conflictingShortcodes ) {
   $conflictingShortcodes = array_merge( $conflictingShortcodes, [
		'Simple File List Pro' => '[eeSFL]',
		'Simple File List Search' => '[eeSFLS]'
   ] );
   return $conflictingShortcodes;
}

// Custom Hook
function eeSFL_BASE_UploadCompleted() {
    do_action('eeSFL_BASE_UploadCompleted'); // To be fired post-upload
}

function eeSFL_BASE_UploadCompletedAdmin() {
    do_action('eeSFL_BASE_UploadCompletedAdmin'); // To be fired post-upload
}


// Language Enabler
function eeSFL_BASE_Textdomain() {
	load_plugin_textdomain( 'ee-simple-file-list', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}



// Plugin Setup
function eeSFL_BASE_Setup() {
	
	global $eeSFL_BASE, $eeSFL_BASE_VarsForJS;
	
	// Translation strings to pass to javascript as eesfl_vars
	$eeProtocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
	$eeSFL_BASE_VarsForJS = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $eeProtocol ),
		'eeEditText' => __('Edit', 'ee-simple-file-list'), // Edit link text
		'eeConfirmDeleteText' => __('Are you sure you want to delete this?', 'ee-simple-file-list'), // Delete confirmation
		'eeCancelText' => __('Cancel', 'ee-simple-file-list'),
		'eeCopyLinkText' => __('The Link Has Been Copied', 'ee-simple-file-list'),
		'eeUploadLimitText' => __('Upload Limit', 'ee-simple-file-list'),
		'eeFileTooLargeText' => __('This file is too large', 'ee-simple-file-list'),
		'eeFileNotAllowedText' => __('This file type is not allowed', 'ee-simple-file-list'),
		'eeUploadErrorText' => __('Upload Failed', 'ee-simple-file-list'),
		'eeFilesSelected' =>  __('Files Selected', 'ee-simple-file-list'),
		
		// Back-End Only
		'eeShowText' => __('Show', 'ee-simple-file-list'), // Shortcode Builder
		'eeHideText' => __('Hide', 'ee-simple-file-list')
	);
	
	// Get Class
	if(!class_exists('eeSFL_BASE')) {
		
		// Get Functions File
		$eeSFL_Nonce = wp_create_nonce('eeSFL_Functions'); // Security
		include_once(plugin_dir_path(__FILE__) . 'includes/ee-functions.php'); // Our Functions
		
		// Main Class
		$eeSFL_Nonce = wp_create_nonce('eeSFL_Class'); // Security
		require_once(plugin_dir_path(__FILE__) . 'includes/ee-class.php'); 
		$eeSFL_BASE = new eeSFL_BASE_MainClass(); // CREATE INSTANCE OF SIMPLE FILE LIST CLASS
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Simple File List is Loading...';
		
		// Initialize the Log
		$eeSFL_StartTime = round( microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 3); // Starting Time
		$eeSFL_MemoryUsedStart = memory_get_usage(); // This is where things start happening
		
		// Populate the Environment Array
		$eeSFL_BASE->eeSFL_GetEnv();
		
		// Install or Update if Needed.
		if( is_admin() ) { eeSFL_BASE_VersionCheck(); }
		
		// Populate the Settings Array
		$eeSFL_BASE->eeSFL_GetSettings(1);
		
		// echo '<pre>'; print_r($eeSFL_BASE->eeListSettings); echo '</pre>';
		// echo '<pre>'; print_r($eeSFL_BASE->eeLog); echo '</pre>'; exit;
	}
	
	eeSFL_BASE_Textdomain(); // Language Setup
	
	return TRUE;
}
add_action('init', 'eeSFL_BASE_Setup');




// Shortcode
function eeSFL_BASE_FrontEnd($atts, $content = null) { // Shortcode Usage: [eeSFL]
	
	global $eeSFL_BASE, $eeSFL_BASE_VarsForJS;
    
    $eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Shortcode Function Loading ...';
	$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_GetThisURL();
	
	$eeAdmin = is_admin();
	if($eeAdmin) { return; } // Don't execute shortcode on page editor
    
    $eeSFL_ListNumber = $eeSFL_BASE->eeListRun; // Legacy 03/20
    $eeForceSort = FALSE;
	
	$eeOutput = '';

	if( $eeSFL_BASE->eeListRun > 1 AND @$_GET['eeFront'] ) { return; }

    // Over-Riding Shortcode Attributes
	if($atts) {
	
		$atts = shortcode_atts( array( // Use lowercase att names only
			'showlist' => '', // YES, ADMIN, USER or NO
			'style' => '', // TABLE, TILES or FLEX
			'theme' => '', // LIGHT, DARK or NONE
			'allowuploads' => '', // YES, ADMIN, USER or NO
			'showthumb' => '', // YES or NO
			'showdate' => '', // YES or NO
			'showsize' => '', // YES or NO
			'showheader' => '', // YES or NO
			'showactions' => '', // YES or NO
			'sortby' => '', // Name, Date, Size, or Random
			'sortorder' => '', // Descending or Ascending
			'hidetype' => '', // Hide file types
			'hidename' => '', // Hide the name matches
			'getdesc' => '', // YES or NO to show the upload description input
			'getinfo' => '', // YES or NO to show the upload user info inputs
			'frontmanage' => '' // Allow Front Manage or Not
		), $atts );		
		
		// Show the Shortcode in the Log
		$eeShortcode = '[eeSFL';
		$eeShortcodeAtts = array_filter($atts);
		foreach( $eeShortcodeAtts as $eeAtt => $eeValue) { $eeShortcode = ' ' . $eeAtt . '=' . $eeValue; }
		$eeShortcode = ']';
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Attributes: ' . implode(', ', array_filter($atts));
		
		extract($atts);
		
		if($showlist) { $eeSFL_BASE->eeListSettings['ShowList'] = strtoupper($showlist); }
		if($allowuploads) { $eeSFL_BASE->eeListSettings['AllowUploads'] = strtoupper($allowuploads); }
		if($showthumb) { $eeSFL_BASE->eeListSettings['ShowFileThumb'] = strtoupper($showthumb); }
		if($showdate) { $eeSFL_BASE->eeListSettings['ShowFileDate'] = strtoupper($showdate); }
		if($showsize) { $eeSFL_BASE->eeListSettings['ShowFileSize'] = strtoupper($showsize); }
		if($showheader) { $eeSFL_BASE->eeListSettings['ShowHeader'] = strtoupper($showheader); }
		if($showactions) { $eeSFL_BASE->eeListSettings['ShowFileActions'] = strtoupper($showactions); }
		if($getinfo) { $eeSFL_BASE->eeListSettings['GetUploaderInfo'] = strtoupper($getinfo); }
		if($frontmanage) { $eeSFL_BASE->eeListSettings['AllowFrontManage'] = strtoupper($frontmanage); }
		
		
		// Force a re-sort of the file list array if a shortcode attribute was used
		if($sortby OR $sortorder) { 
			if( $sortby != $eeSFL_BASE->eeListSettings['SortBy'] OR $sortorder != $eeSFL_BASE->eeListSettings['SortOrder'] ) {
				$eeForceSort = TRUE;
				$eeSFL_BASE->eeListSettings['SortBy'] = strtoupper($sortby);
				$eeSFL_BASE->eeListSettings['SortOrder'] = strtoupper($sortorder);
			} else {
				$eeForceSort = FALSE;
			}
		}
		
		// LEGACY - Info Not Published
		if($hidetype) { $eeSFL_HideType = strtolower($hidetype); } else { $eeSFL_HideType = FALSE; }
		if($hidename) { $eeSFL_HideName = strtolower($hidename); } else { $eeSFL_HideName = FALSE; }
		
	} else {
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = 'No Shortcode Attributes';
	}
	
	// Javascript

	$eeDependents = array('jquery'); // Requires jQuery
/*	wp_enqueue_style('ee-simple-file-list-css');
	wp_enqueue_script('ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js', $eeDependents, eeSFL_BASE_Version); // Head
	wp_enqueue_script('ee-simple-file-list-js-foot', plugin_dir_url(__FILE__) . 'js/ee-footer.js', $eeDependents, eeSFL_BASE_Version, TRUE); // Footer
	wp_localize_script( 'ee-simple-file-list-js-foot', 'eesfl_vars', $eeSFL_BASE_VarsForJS );
*/
    
    
    if($eeSFL_BASE->eeListSettings['AllowFrontManage'] != 'NO') {
    	wp_enqueue_script('ee-simple-file-list-js-edit-file', plugin_dir_url(__FILE__) . 'js/ee-edit-file.js', $eeDependents, eeSFL_BASE_Version, TRUE);
	}
	
	// List Theme CSS
    if($eeSFL_BASE->eeListSettings['ShowListTheme'] == 'DARK') {
		wp_enqueue_style('ee-simple-file-list-css-theme-dark');
	} elseif($eeSFL_BASE->eeListSettings['ShowListTheme'] == 'LIGHT') {		
		wp_enqueue_style('ee-simple-file-list-css-theme-light');
	}
    
    // List Style CSS
    if($eeSFL_BASE->eeListSettings['ShowListStyle'] == 'FLEX') { 	
		wp_enqueue_style('ee-simple-file-list-css-flex');		
	} elseif($eeSFL_BASE->eeListSettings['ShowListStyle'] == 'TILES') {    	
		wp_enqueue_style('ee-simple-file-list-css-tiles');		
	} else {		
		wp_enqueue_style('ee-simple-file-list-css-table');
	}
	
	// Upload Check
	$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
	include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-upload-check.php');
		
	
	// Begin Front-End List Display ==================================================================
	
	// Who Can Upload?
	switch ($eeSFL_BASE->eeListSettings['AllowUploads']) {
	    case 'YES':
	        break; // Show It
	    case 'USER':
	        // Show It If...
	        if( get_current_user_id() ) { break; } else { $eeSFL_BASE->eeListSettings['AllowUploads'] = 'NO'; }
	    case 'ADMIN':
	        // Show It If...
	        if(current_user_can('manage_options')) { break; } else { $eeSFL_BASE->eeListSettings['AllowUploads'] = 'NO'; }
	        break;
		default:
			$eeSFL_BASE->eeListSettings['AllowUploads'] = 'NO'; // Show Nothing
	}
	
	$eeShowUploadForm = FALSE;
	
	if($eeSFL_BASE->eeListSettings['AllowUploads'] != 'NO' AND !$eeSFL_BASE->eeUploadFormRun) {
		
		wp_enqueue_style('ee-simple-file-list-css-upload');
		wp_enqueue_script('ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'js/ee-uploader.js', $eeDependents , eeSFL_BASE_Version, TRUE);
		$eeSFL_UploadFormRun = TRUE;
		$eeShowUploadForm = TRUE;
	}
	
	if($eeSFL_BASE->eeListSettings['AllowUploads'] != 'NO' AND !$eeSFL_Uploaded AND $eeSFL_BASE->eeListSettings['UploadPosition'] == 'Above') {
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_BASE->eeEnvironment['pluginDir'] . '/includes/ee-upload-form.php');
	}	
		
	// Who Can View the List?
	switch ($eeSFL_BASE->eeListSettings['ShowList']) {
	    case 'YES':
	        break; // Show It
	    case 'USER':
	        // Show It If...
	        if( get_current_user_id() ) { break; } else { $eeSFL_BASE->eeListSettings['ShowList'] = 'NO'; }
	    case 'ADMIN':
	        // Show It If...
	        if(current_user_can('manage_options')) { break; } else { $eeSFL_BASE->eeListSettings['ShowList'] = 'NO'; }
	        break;
		default:
			$eeSFL_BASE->eeListSettings['ShowList'] = 'NO'; // Show Nothing
	}
	
	if($eeSFL_BASE->eeListSettings['ShowList'] != 'NO') {
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'ee-list-display.php');
	}
	
	if($eeSFL_BASE->eeListSettings['AllowUploads'] != 'NO' AND !$eeSFL_Uploaded AND $eeSFL_BASE->eeListSettings['UploadPosition'] == 'Below') {
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_BASE->eeEnvironment['pluginDir'] . '/includes/ee-upload-form.php');
	}
	
	// Smooth Scrolling is AWESOME!
	if( isset($_REQUEST['ee']) AND $eeSFL_BASE->eeListSettings['SmoothScroll'] == 'YES' ) { 
		$eeOutput .= '<script>eeSFL_BASE_ScrollToIt();</script>'; }
	
	$eeSFL_BASE->eeListRun++;
	
	$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - SFL Display Completed';
	
	$eeOutput .= $eeSFL_BASE->eeSFL_WriteLogData(); // Only adds output if DevMode is ON
	
	// Give it back
	$eeSFL_BASE->eeAllFiles = array();
	
	return $eeOutput; // Output the Display
}
add_shortcode( 'eeSFL', 'eeSFL_BASE_FrontEnd' );




function eeSFL_BASE_RegisterAssets() {
	
	// Register All CSS
    wp_register_style( 'ee-simple-file-list-css', plugin_dir_url(__FILE__) . 'css/styles.css', '', eeSFL_BASE_Version);
	wp_register_style( 'ee-simple-file-list-css-theme-dark', plugins_url('css/styles-theme-dark.css', __FILE__), '', eeSFL_BASE_Version );
	wp_register_style( 'ee-simple-file-list-css-theme-light', plugins_url('css/styles-theme-light.css', __FILE__), '', eeSFL_BASE_Version );
    wp_register_style( 'ee-simple-file-list-css-flex', plugins_url('css/styles-flex.css', __FILE__), '', eeSFL_BASE_Version );
    wp_register_style( 'ee-simple-file-list-css-tiles', plugins_url('css/styles-tiles.css', __FILE__), '', eeSFL_BASE_Version );
	wp_register_style( 'ee-simple-file-list-css-table', plugins_url('css/styles-table.css', __FILE__), '', eeSFL_BASE_Version );
	wp_register_style( 'ee-simple-file-list-css-upload', plugins_url('css/styles-upload-form.css', __FILE__), '', eeSFL_BASE_Version );
	
	// Register JavaScripts
	wp_register_script( 'ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js' );
	wp_register_script( 'ee-simple-file-list-js-footer', plugin_dir_url(__FILE__) . 'js/ee-footer.js' );
	wp_register_script( 'ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'js/ee-uploader.js' );
	wp_register_script( 'ee-simple-file-list-js-edit-file', plugin_dir_url(__FILE__) . 'js/ee-edit-file.js' );
	
}
add_action( 'init', 'eeSFL_BASE_RegisterAssets' );



function eeSFL_BASE_Enqueue() {
	
	global $eeSFL_BASE_VarsForJS;
	
	$eeDependents = array('jquery'); // Requires jQuery
	wp_enqueue_style('ee-simple-file-list-css');
	wp_enqueue_script('ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js', $eeDependents, eeSFL_BASE_Version); // Head
	wp_enqueue_script('ee-simple-file-list-js-foot', plugin_dir_url(__FILE__) . 'js/ee-footer.js', $eeDependents, eeSFL_BASE_Version, TRUE); // Footer
	wp_localize_script( 'ee-simple-file-list-js-foot', 'eesfl_vars', $eeSFL_BASE_VarsForJS );
}

add_action( 'wp_enqueue_scripts', 'eeSFL_BASE_Enqueue' );




// Admin <head>
function eeSFL_BASE_AdminHead($eeHook) {

	global $eeSFL_BASE, $eeSFL_BASE_VarsForJS;
	
	$deps = array('jquery');
	
	// wp_die($eeHook); // Check the hook
    $eeHooks = array('toplevel_page_ee-simple-file-list');
    
    if(in_array($eeHook, $eeHooks)) {
        
        // CSS
        wp_enqueue_style( 'ee-simple-file-list-css', plugins_url('css/styles.css', __FILE__), '', eeSFL_BASE_Version );
        
        // List Style
        if($eeSFL_BASE->eeListSettings['ShowListStyle'] == 'Flex') {
        	wp_enqueue_style( 'ee-simple-file-list-css-flex', plugins_url('css/styles-flex.css', __FILE__), '', eeSFL_BASE_Version );
        } elseif($eeSFL_BASE->eeListSettings['ShowListStyle'] == 'Tiles') {
	        wp_enqueue_style( 'ee-simple-file-list-css-tiles', plugins_url('css/styles-tiles.css', __FILE__), '', eeSFL_BASE_Version );
        } else {
	        wp_enqueue_style( 'ee-simple-file-list-css-table', plugins_url('css/styles-table.css', __FILE__), '', eeSFL_BASE_Version );
        }
        
        // Admin Styles
        wp_enqueue_style( 'ee-simple-file-list-css-admin', plugins_url('css/admin5.css', __FILE__), '', eeSFL_BASE_Version );
        
        
        // Javascript
        wp_enqueue_script('ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js', $deps, eeSFL_BASE_Version, FALSE);
		wp_enqueue_script('ee-simple-file-list-js-back', plugin_dir_url(__FILE__) . 'js/ee-back.js', $deps, eeSFL_BASE_Version, FALSE);
        wp_enqueue_script('ee-simple-file-list-js-foot', plugin_dir_url(__FILE__) . 'js/ee-footer.js', $deps, eeSFL_BASE_Version, TRUE);
        wp_enqueue_script('ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'js/ee-uploader.js',$deps, eeSFL_BASE_Version, TRUE);
        wp_enqueue_script('ee-simple-file-list-js-edit-file', plugin_dir_url(__FILE__) . 'js/ee-edit-file.js',$deps, eeSFL_BASE_Version, TRUE);
		
		// Pass variables
		wp_localize_script('ee-simple-file-list-js-head', 'eeSFL_JS', array( 'pluginsUrl' => plugins_url() ) );
		wp_localize_script( 'ee-simple-file-list-js-foot', 'eesfl_vars', $eeSFL_BASE_VarsForJS );
    }  
}
add_action('admin_enqueue_scripts', 'eeSFL_BASE_AdminHead');






// Ajax Handler
// Function name must be the same as the action name to work on front side ?
function simplefilelist_upload_job() {

	$eeResult = eeSFL_BASE_FileUploader();

	echo $eeResult;

	wp_die();

}	
add_action( 'wp_ajax_simplefilelist_upload_job', 'simplefilelist_upload_job' );


function simplefilelist_edit_job() {

	$eeResult = eeSFL_BASE_FileEditor();

	echo $eeResult;

	wp_die();

}	
add_action( 'wp_ajax_simplefilelist_edit_job', 'simplefilelist_edit_job' );



// File Upload Engine
function eeSFL_BASE_FileUploader() {
	
	global $eeSFL_BASE;
	
	// The FILE object
	if(empty($_FILES)) { 
		return 'Missing File Input';
	}
	
	if( !is_admin() ) { // Front-side protections
	
		// Who should be uploading?
		switch ($eeSFL_BASE->eeListSettings['AllowUploads']) {
		    case 'YES':
		        break; // Allow it, even if it's dangerous.
		    case 'USER':
		        // Allow it if logged in at all
		        if( get_current_user_id() ) { break; } else { return 'ERROR 97'; }
		    case 'ADMIN':
		        // Allow it if admin only.
		        if(current_user_can('manage_options')) { break; } else { return 'ERROR 97'; }
		        break;
			default: // Don't allow at all
				return 'ERROR 97';
		}
	} 
	
	// Get this List's Settings
	$eeSFL_BASE->eeSFL_GetSettings(1);	
	$eeSFL_FileUploadDir = $eeSFL_BASE->eeListSettings['FileListDir'];

	// Check size
	$eeSFL_FileSize = filter_var($_FILES['file']['size'], FILTER_VALIDATE_INT);
	$eeSFL_UploadMaxFileSize = $eeSFL_BASE->eeListSettings['UploadMaxFileSize']*1024*1024; // Convert MB to B
	
	if($eeSFL_FileSize > $eeSFL_UploadMaxFileSize) {
		return "File size is too large.";
	}
	
	// Go...
	if(is_dir(ABSPATH . $eeSFL_FileUploadDir)) {
			
		if(wp_verify_nonce(@$_POST['ee-simple-file-list-upload'], 'ee-simple-file-list-upload')) {
			
			// Temp file
			$eeTempFile = $_FILES['file']['tmp_name'];
			
			// Clean up messy names
			$eeSFL_FileName = eeSFL_BASE_SanitizeFileName($_FILES['file']['name']);
			
			// Check if it already exists
			if($eeSFL_BASE->eeListSettings['AllowOverwrite'] == 'NO') { 
				$eeSFL_FileName = eeSFL_BASE_CheckForDuplicateFile($eeSFL_FileUploadDir . $eeSFL_FileName);
			}
			
			eeSFL_BASE_DetectUpwardTraversal($eeSFL_FileUploadDir . $eeSFL_FileName); // Die if foolishness
			
			$eeSFL_PathParts = pathinfo($eeSFL_FileName);
			$eeSFL_FileNameAlone = $eeSFL_PathParts['filename'];
			$eeSFL_Extension = strtolower($eeSFL_PathParts['extension']); // We need to do this here and in eeSFL_ProcessUpload()
			
			// Format Check
			$eeSFL_FileFormatsArray = array_map('trim', explode(',', $eeSFL_BASE->eeListSettings['FileFormats']));
			
			if(!in_array($eeSFL_Extension, $eeSFL_FileFormatsArray) OR in_array($eeSFL_Extension, $eeSFL_BASE->eeForbiddenTypes)) {
				return 'File type not allowed: (' . $eeSFL_Extension . ')';	
			}
			
			// Assemble FilePath
			$eeSFL_TargetFile = $eeSFL_FileUploadDir . $eeSFL_FileNameAlone . '.' . $eeSFL_Extension;
			
			// Check if the name has changed
			if($_FILES['file']['name'] != $eeSFL_FileName) {
				
				// Set a transient with the new name so we can get it in ProcessUpload() after the form is submitted
				$eeOldFilePath = 'eeSFL-Renamed-' . str_replace($eeSFL_BASE->eeListSettings['FileListDir'], '', $eeSFL_FileUploadDir . $_FILES['file']['name']); // Strip the FileListDir
				$eeOldFilePath = esc_sql(urlencode($eeOldFilePath));
				$eeNewFilePath = str_replace($eeSFL_BASE->eeListSettings['FileListDir'], '', $eeSFL_TargetFile); // Strip the FileListDir
				set_transient($eeOldFilePath, $eeNewFilePath, 900); // Expires in 15 minutes
			}
			
			$eeTarget = ABSPATH . $eeSFL_TargetFile;
			
			// return $eeTarget;
			
			// Save the file
			if( move_uploaded_file($eeTempFile, $eeTarget) ) {
				
				if(!is_file($eeTarget)) {
					return 'Error - File System Error.'; // No good.
				} else {
					
					// Check for corrupt images
					if( in_array($eeSFL_Extension, $eeSFL_BASE->eeDynamicImageThumbFormats) ) {
						
						$eeString = implode('...', getimagesize($eeTarget) );
						
						if(!strpos($eeString, 'width=') OR !strpos($eeString, 'height=')) { // Make sure it's really an image
							
							unlink($eeTarget);
							
							return 'ERROR 99';
						}
					}
					
					// Update the File Date
					$eeDate = sanitize_text_field($_POST['eeSFL_FileDate']);
					$eeDate = strtotime($eeDate);
					if($eeDate) {
						touch($eeTarget, $eeDate);  // Do nothing if bad date
					}
					
					// Build Image thumbs right away right away. We'll set other types to use the background job within eeSFL_ProcessUpload()
					if($eeSFL_BASE->eeListSettings['ShowFileThumb'] == 'YES') {
						if( in_array($eeSFL_Extension, $eeSFL_BASE->eeDynamicImageThumbFormats) ) {
				
							$eeSFL_TargetFile = str_replace($eeSFL_BASE->eeListSettings['FileListDir'], '', $eeSFL_TargetFile); // Strip the FileListDir
							$eeSFL_BASE->eeSFL_CheckThumbnail($eeSFL_TargetFile, $eeSFL_BASE->eeListSettings);
						}
					}
					
					return 'SUCCESS';
				}
				 
			} else {
				return 'Cannot save the uploaded file: ' . $eeSFL_TargetFile;
			}
		
		} else {
			
			return 'ERROR 98 - FileUploader';
		}
		
	} else {
		return 'Upload Path Not Found: ' . $eeSFL_FileUploadDir;
	}
}



// File Editor Engine
function eeSFL_BASE_FileEditor() {
	
	// All POST values used shall be expected
	
	global $eeSFL_BASE;
	
	$eeFileNameNew = FALSE;
	$eeFileNiceNameNew = FALSE;
	$eeFileDescriptionNew = FALSE;
	$eeFileAction = FALSE;
	
	// WP Security
	if( !check_ajax_referer( 'eeSFL_ActionNonce', 'eeSecurity' ) ) { return 'ERROR 98';	}
	
	// Check if we should be doing this
	if(is_admin() OR $eeSFL_BASE->eeListSettings['AllowFrontManage'] == 'YES') {
		
		// The Action
		if( strlen($_POST['eeFileAction']) ) { $eeFileAction = sanitize_text_field($_POST['eeFileAction']); } 
		if( !$eeFileAction ) { return "Missing the Action"; }
		
		// The Current File Name
		if( strlen($_POST['eeFileName']) ) { $eeFileName = sanitize_text_field($_POST['eeFileName']); }
		if(!$eeFileName) { return "Missing the File Name"; }
		
		// Folder Path - PRO ONLY
		
		// Delete the File
		if($eeFileAction == 'Delete') {
			
			eeSFL_BASE_DetectUpwardTraversal($eeSFL_BASE->eeListSettings['FileListDir'] . $eeFileName); // Die if foolishness
			
			$eeFilePath = ABSPATH . $eeSFL_BASE->eeListSettings['FileListDir'] . $eeFileName;
			
			if( strpos($eeFileName, '.') ) { // Gotta be a File - Looking for the dot rather than using is_file() for better speed
				
				if(unlink($eeFilePath)) {
					
					// Remove the item from the array
					$eeAllFilesArray = get_option('eeSFL_FileList_1'); // Get the full list
					
					foreach( $eeAllFilesArray as $eeKey => $eeThisFileArray){
						if($eeThisFileArray['FilePath'] == $eeFileName) {
							unset($eeAllFilesArray[$eeKey]);
							break;
						}
					}
					
					update_option('eeSFL_FileList_1', $eeAllFilesArray);
					
					$eeSFL_BASE->eeSFL_UpdateThumbnail($eeFileName, FALSE); // Delete the thumb
					
					return 'SUCCESS';
					
				} else {
					return __('File Delete Failed', 'ee-simple-file-list') . ':' . $eeFileName;
				}
			
			} else {
				return __('Item is Not a File', 'ee-simple-file-list') . ':' . $eeFileName;
			}	
		
		} elseif($eeFileAction == 'Edit') {
			
			// The Nice Name - Might be empty
			if($_POST['eeFileNiceNameNew'] != 'false') {
				$eeFileNiceNameNew = trim(sanitize_text_field($_POST['eeFileNiceNameNew']));
				if(!$eeFileNiceNameNew) { $eeFileNiceNameNew = ''; } 
				$eeSFL_BASE->eeSFL_UpdateFileDetail($eeFileName, 'FileNiceName', $eeFileNiceNameNew);
			}
			
			
			
			// The Description - Might be empty
			if($_POST['eeFileDescNew'] != 'false') {
			
				$eeFileDescriptionNew = trim(sanitize_text_field($_POST['eeFileDescNew']));
				
				if(!$eeFileDescriptionNew) { $eeFileDescriptionNew = ''; }
				
				$eeSFL_BASE->eeSFL_UpdateFileDetail($eeFileName, 'FileDescription', $eeFileDescriptionNew);
			}

			
			
			// Date Modified - PRO ONLY
		
			
			
			// New File Name? - Rename Last
			if( strlen($_POST['eeFileNameNew']) >= 3 ) { 
				
				$eeFileNameNew = sanitize_text_field($_POST['eeFileNameNew']);
				$eeFileNameNew  = urldecode( $eeFileNameNew );
				$eeFileNameNew  = eeSFL_BASE_SanitizeFileName( $eeFileNameNew );
				
				if( strlen($eeFileNameNew) >= 3 ) { // a.b
				
					// Prevent changing file extension
					$eePathParts = pathinfo( $eeFileName );
					$eeOldExtension = strtolower( $eePathParts['extension'] ); 
					$eePathParts = pathinfo( $eeFileNameNew );
					$eeNewExtension = strtolower( $eePathParts['extension'] );
					if($eeOldExtension != $eeNewExtension) { return "Changing File Extensions is Not Allowed"; }
				
					// Die if foolishness
					eeSFL_BASE_DetectUpwardTraversal($eeSFL_BASE->eeListSettings['FileListDir'] . $eeFileNameNew ); 
					
					// Check for Duplicate File
					$eeFileNameNew  = eeSFL_BASE_CheckForDuplicateFile( $eeSFL_BASE->eeListSettings['FileListDir'] . $eeFileNameNew );
					
					// Rename File On Disk
					$eeOldFilePath = ABSPATH . $eeSFL_BASE->eeListSettings['FileListDir'] . $eeFileName;
					$eeNewFilePath = ABSPATH . $eeSFL_BASE->eeListSettings['FileListDir'] . $eeFileNameNew;
					
					if(!is_file($eeOldFilePath)) {
						return __('File Not Found', 'ee-simple-file-list') . ': ' . basename($eeOldFilePath);
					}
					
					if( !rename($eeOldFilePath, $eeNewFilePath) ) {
						
						return __('Could Not Change the Name', 'ee-simple-file-list') . ' ' . $eeOldFilePath . ' ' . __('to', 'ee-simple-file-list') . ' ' . $eeNewFilePath;
					
					} else {
						
						$eeSFL_BASE->eeSFL_UpdateFileDetail($eeFileName, 'FilePath', $eeFileNameNew );
						
						$eeSFL_BASE->eeSFL_UpdateThumbnail($eeFileName, $eeFileNameNew ); // Rename the thumb
					}
				
				} else {
					return __('Invalid New File Name', 'ee-simple-file-list');
				}
			}
			
			return 'SUCCESS';
			
		} else { // End Editing
			
			return; // Nothing to do	
		}
	}
	
	// We should not be doing this
	return;
}




// Add Action Links to the Plugins Page
function eeSFL_BASE_ActionPluginLinks( $links ) {
	
	$eeLinks = array(
		'<a href="' . admin_url( 'admin.php?page=ee-simple-file-list' ) . '">' . __('Admin List', 'ee-simple-file-list') . '</a>',
		'<a href="' . admin_url( 'admin.php?page=ee-simple-file-list&tab=settings' ) . '">' . __('Settings', 'ee-simple-file-list') . '</a>'
	);
	return array_merge( $links, $eeLinks );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'eeSFL_BASE_ActionPluginLinks' );




// Admin Pages
function eeSFL_BASE_AdminMenu() {
	
	global $eeSFL_BASE;
	
	// Only include when accessing the plugin admin pages
	if( isset($_GET['page']) ) {
		
		$eeOutput = '<!-- Simple File List Admin -->';
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Admin Menu Loading ...';
			
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include_once($eeSFL_BASE->eeEnvironment['pluginDir'] . 'ee-admin-page.php'); // Admin's List Management Page

	}
	
	// Admin Menu Visability
	if(!isset($eeSFL_BASE->eeListSettings['AdminRole'])) { // First Run
		$eeSFL_BASE->eeListSettings['AdminRole'] = 5;
	}
	
	switch ($eeSFL_BASE->eeListSettings['AdminRole']) {
	    case 1:
	        $eeCapability = 'read';
	        break;
	    case 2:
	        $eeCapability = 'edit_posts';
	        break;
	    case 3:
	        $eeCapability = 'publish_posts';
	        break;
	    case 4:
	        $eeCapability = 'edit_others_pages';
	        break;
	    case 5:
	        $eeCapability = 'activate_plugins';
	        break;
		default:
			$eeCapability = 'edit_posts';
	}
	
	// The Admin Menu
	add_menu_page(
		__(eeSFL_BASE_PluginName, eeSFL_BASE_PluginSlug), // Page Title - Defined at the top of this file
		__(eeSFL_BASE_PluginMenuTitle, eeSFL_BASE_PluginSlug), // Menu Title
		$eeCapability, // User status reguired to see the menu
		eeSFL_BASE_PluginSlug, // Slug
		'eeSFL_BASE_BackEnd', // Function that displays the menu page
		'dashicons-index-card' // Icon used
	);
	
}
add_action( 'admin_menu', 'eeSFL_BASE_AdminMenu' );




// Plugin Version Check
// We only run the update function if there has been a change in the database revision.
function eeSFL_BASE_VersionCheck() { 
		
	global $wpdb, $eeSFL_BASE;
	
	$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Checking DB Version...';
	
	$eeInstalled = get_option('eeSFL_FREE_DB_Version'); // Legacy
	if(!$eeInstalled ) { $eeInstalled = get_option('eeSFL_BASE_Version'); } // Hip, now, and in-with-the-times.
		
	$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - ' . $eeInstalled . ' is Installed';
	
	if( $eeInstalled AND version_compare($eeInstalled, eeSFL_BASE_Version, '>=')  ) {
		
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Version is Up-To-Date';
		
		return TRUE;
	
	} else { // Not Installed or Up-To-Date
		
		$eeSettings = array();
		
		// Things that may or may not be there
		$eeOldOldSettings = get_option('eeSFL-1-ShowList'); // SFL 3.x
		$eeOldSettings = get_option('eeSFL-Settings'); // SFL 4.0
		$eeSettingsCurrent = get_option('eeSFL_Settings_1'); // SFL 4.1
		$wpAdminEmail = get_option('admin_email');
		
		if($eeOldOldSettings AND !$eeOldSettings) { // Upgrade from Simple File List 3.x
			
			$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Version 3.x Detected';
			
			// Get Existing Settings
			$eeSettings['ShowList'] = get_option('eeSFL-1-ShowList');
			delete_option('eeSFL-1-ShowList');
			$eeSettings['ShowFileThumb'] = get_option('eeSFL-1-ShowFileThumb');
			delete_option('eeSFL-1-ShowFileThumb');
			$eeSettings['ShowFileDate'] = get_option('eeSFL-1-ShowFileDate');
			delete_option('eeSFL-1-ShowFileDate');
			$eeSettings['ShowFileOwner'] = get_option('eeSFL-1-ShowFileOwner');
			delete_option('eeSFL-1-ShowFileOwner');
			$eeSettings['ShowFileSize'] = get_option('eeSFL-1-ShowFileSize');
			delete_option('eeSFL-1-ShowFileSize');
			$eeSettings['SortBy'] = get_option('eeSFL-1-SortBy');
			delete_option('eeSFL-1-SortBy');
			$eeSettings['SortOrder'] = get_option('eeSFL-1-SortOrder');
			delete_option('eeSFL-1-SortOrder');
			$eeSettings['ShowFileActions'] = get_option('eeSFL-1-ShowFileActions');
			delete_option('eeSFL-1-ShowFileActions');
			$eeSettings['ShowHeader'] = get_option('eeSFL-1-ShowListHeader');
			delete_option('eeSFL-1-ShowListHeader');
			$eeSettings['ShowFileThumb'] = get_option('eeSFL-1-ShowFileThumb');
			delete_option('eeSFL-1-ShowFileThumb');
			$eeSettings['AllowFrontManage'] = get_option('eeSFL-1-AllowFrontDelete');
			delete_option('eeSFL-1-AllowFrontDelete');
			$eeSettings['FileListDir'] = get_option('eeSFL-1-UploadDir');
			delete_option('eeSFL-1-UploadDir');
			$eeSettings['AllowUploads'] = get_option('eeSFL-1-AllowUploads');
			delete_option('eeSFL-1-AllowUploads');
			$eeSettings['FileFormats'] = get_option('eeSFL-1-FileFormats');
			delete_option('eeSFL-1-FileFormats');
			$eeSettings['UploadLimit'] = get_option('eeSFL-1-UploadLimit');
			delete_option('eeSFL-1-UploadLimit');
			$eeSettings['UploadMaxFileSize'] = get_option('eeSFL-1-UploadMaxFileSize');
			delete_option('eeSFL-1-UploadMaxFileSize');
			$eeSettings['GetUploaderInfo'] = get_option('eeSFL-1-GetUploaderInfo');
			delete_option('eeSFL-1-GetUploaderInfo');
			$eeSettings['NotifyTo'] = get_option('eeSFL-1-Notify');
			delete_option('eeSFL-1-Notify');
		
		} elseif( is_array($eeOldSettings) ) { // The Old Way - All lists in one array
			
			$eeSettings = $eeOldSettings[1];
			add_option('eeSFL_Settings_1', $eeSettings); // Create the new option, if needed.
			delete_option('eeSFL-Settings'); // Out with the old
			unset($eeOldSettings);
		
		} elseif( is_array($eeSettingsCurrent) ) { // The Current Way, 4.1 and up
			
			$eeSettings = $eeSettingsCurrent;
		
		} else {
			
			// New Install
		}
		
		// If Updating
		if( !empty($eeSettings) ) {
			
			$eeSettings = array_merge($eeSFL_BASE->DefaultListSettings, $eeSettings);
			
			// These are now uppercase
			$eeSettings['ShowListStyle'] = strtoupper($eeSettings['ShowListStyle']);
			$eeSettings['ShowListTheme'] = strtoupper($eeSettings['ShowListTheme']);
			
			// $eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'] = $eeSettings;
			
			// Check the File List Directory
			eeSFL_BASE_FileListDirCheck( $eeSettings['FileListDir'] );
			
			// Update File List Option Name, if needed - Rename the file list's option_name value
			if(get_option('eeSFL-FileList-1')) {
				$eeQuery = "UPDATE $wpdb->options SET option_name = 'eeSFL_FileList_1' WHERE option_name = 'eeSFL-FileList-1'";
				$wpdb->query( $eeQuery );
			}
			
			$eeLog = get_option('eeSFL-Log');
			if($eeLog) {
				add_option('eeSFL_BASE_Log', $eeLog); // In with the new
				delete_option('eeSFL-Log'); // Out with the old
			}
					
			delete_transient('eeSFL-1-FileListDirCheck');
			delete_transient('eeSFL_FileList_1');
			delete_transient('eeSFL_FileList-1'); // DB 4.2 and earlier
			delete_option('eeSFL-Version'); // Out with the old
			delete_option('eeSFL-DB-Version'); // Out with the old
			delete_option('eeSFL_FREE_DB_Version'); // Out with the old
			delete_option('eeSFL_FREE_Log'); // Out with the old
			delete_option('eeSFLA-Settings'); // Out with the old
			delete_option('eeSFL-Legacy'); // Don't need this anymore
		
		
		// New Installation
		} else {
		
			$eeSettings = $eeSFL_BASE->DefaultListSettings;
			
			// Check the File List Directory
			eeSFL_BASE_FileListDirCheck( $eeSettings['FileListDir'] );
			
			// Create first file list array
			$eeFilesArray = array();
			update_option('eeSFL_FileList_1', $eeFilesArray);
			
			// Add First File
			$eeCopyFrom = dirname(__FILE__) . '/Simple-File-List.pdf';
			$eeCopyTo = ABSPATH . '/' . $eeSettings['FileListDir'] . 'Simple-File-List.pdf';
			copy($eeCopyFrom, $eeCopyTo);
		
		}
		
		// Add Default Values
		if(!$eeSettings['NotifyTo']) {
			$eeSettings['NotifyTo'] = $wpAdminEmail;
		}
		if(!$eeSettings['NotifyFrom']) {
			$eeSettings['NotifyFrom'] = $wpAdminEmail;
		}
		if(!$eeSettings['NotifyMessage']) {
			$eeSettings['NotifyMessage'] = $eeSFL_BASE->eeNotifyMessageDefault;
		}
		
		// Update Database
		ksort($eeSettings); // Sort for sanity
		update_option('eeSFL_Settings_1' , $eeSettings);
		
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Plugin Version now at ' . eeSFL_BASE_Version;
		
		// Write the log file to the Database
		$eeSFL_BASE->eeSFL_WriteLogData($eeSFL_BASE->eeLog);
		
		update_option('eeSFL_BASE_Version', eeSFL_BASE_Version);
			
		return TRUE;
	
	}
}




// Plugin Activation ==========================================================
function eeSFL_BASE_Activate() {
	
	return TRUE; // All done, nothing to do here.	
}
register_activation_hook( __FILE__, 'eeSFL_BASE_Activate' );

?>