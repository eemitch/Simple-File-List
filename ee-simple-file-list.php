<?php

/**
 * @package Element Engage - Simple File List
 */
/*
Plugin Name: Simple File List
Plugin URI: http://simplefilelist.com
Description: A Basic File List Manager with File Uploader
Author: Mitchell Bennis
Version: 5.0.2
Author URI: http://simplefilelist.com
License: GPLv2 or later
Text Domain: ee-simple-file-list
Domain Path: /languages
*/

$eeSFL_BASE_DevMode = TRUE; // TRUE/FALSE = Enables visible logging or not

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// SFL Versions
define('eeSFL_BASE_Version', '5.0.2'); // Plugin version - DON'T FORGET TO UPDATE ABOVE TOO !!!
define('eeSFL_BASE_DB_Version', '4.7'); // Database structure version - used for eeSFL_BASE_VersionCheck()

// LEGACY
if( !defined('eeSFL_Version') ) { define('eeSFL_Version', eeSFL_BASE_Version); } // Fix for Folder Extension Need, post 4.2.12

// Our Core
$eeSFL_BASE = FALSE; // Our main class
$eeSFL_Settings = array(); // All List Info
$eeSFL_VarsForJS = array(); // Strings for JS
$eeSFL_BASE_Env = array(); // Environment
$eeSFL_BASE_ListRun = 1; // Count of lists per page
$eeSFL_BASE_UploadRun = 1; // Count the uploaders per page

// The Log - Written to wp_option -> eeSFL_Log
$eeSFL_BASE_Log = array('Simple File List is Loading...');
$eeSFL_BASE_Log[] = 'Version ' . eeSFL_BASE_Version . ' (DB ' . eeSFL_BASE_DB_Version . ')';
$eeSFL_BASE_Log['messages'] = array();
$eeSFL_BASE_Log['errors'] = array();


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


// Display Notice to Update Simple File List Pro
function eeSFL_BASE_ALERT() {
    
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$eeFolders = 'ee-simple-file-list-folders/ee-simple-file-list-folders.php';
	$eeSearch = 'ee-simple-file-list-folders/ee-simple-file-list-search.php';
	
	if( is_plugin_active($eeFolders) OR is_plugin_active($eeSearch) ) {
		
		$wpScreen = get_current_screen(); // Get the current screen
	 
	    if ( $wpScreen->id == 'dashboard' OR $wpScreen->id == 'plugins' OR $wpScreen->id == 'toplevel_page_ee-simple-file-list' ) {
	        
	        $eeOutput = '<div class="notice notice-error is-dismissible">
	            <p><strong>' . __('IMPORTANT', 'ee-simple-file-list') . '</strong><br />' . 
	            	__('Extensions are no longer supported for the free version of Simple File List.', 'ee-simple-file-list') . ' ' .
	            	__('Please upgrade to the Pro version.', 'ee-simple-file-list') . ' <a href="https://simplefilelist.com/upgrade-to-simple-file-list-pro/" target="_blank">' . __('Free Upgrade', 'ee-simple-file-list') . '</a></p>
	            </div>';
	            
	        echo $eeOutput;
	    }
    }
}
add_action( 'admin_notices', 'eeSFL_BASE_ALERT' );



// Plugin Setup
function eeSFL_BASE_Setup() {
	
	global $eeSFL_BASE, $eeSFL_BASE_Log, $eeSFL_Settings, $eeSFL_BASE_Env, $eeSFL_VarsForJS;
	
	$eeSFL_BASE_Log['RunTime'][] = 'Running Setup...';
	
	// Translation strings to pass to javascript as eesfl_vars
	$eeProtocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
	$eeSFL_VarsForJS = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $eeProtocol ),
		'eeEditText' => __('Edit', 'ee-simple-file-list'), // Edit link text
		'eeConfirmDeleteText' => __('Are you sure you want to delete this?', 'ee-simple-file-list'), // Delete confirmation
		'eeCancelText' => __('Cancel', 'ee-simple-file-list'),
		'eeCopyLinkText' => __('The Link Has Been Copied', 'ee-simple-file-list'),
		'eeUploadLimitText' => __('Upload Limit', 'ee-simple-file-list'),
		'eeFileTooLargeText' => __('This file is too large', 'ee-simple-file-list'),
		'eeFileNotAllowedText' => __('This file type is not allowed', 'ee-simple-file-list'),
		'eeUploadErrorText' => __('Upload Failed', 'ee-simple-file-list'),
		
		// Back-End Only
		'eeShowText' => __('Show', 'ee-simple-file-list'), // Shortcode Builder
		'eeHideText' => __('Hide', 'ee-simple-file-list')
	);
	
	// Get Functions
	$eeSFL_Nonce = wp_create_nonce('eeSFL_Functions'); // Security
	include_once(plugin_dir_path(__FILE__) . 'includes/ee-functions.php'); // Our Functions
	
	// Get Class
	if(!class_exists('eeSFL_BASE')) {
		$eeSFL_Nonce = wp_create_nonce('eeSFL_Class'); // Security
		require_once(plugin_dir_path(__FILE__) . 'includes/ee-class.php'); // Get the main class file
		$eeSFL_BASE = new eeSFL_BASE_MainClass(); // Initiate the SFL Class
		$eeSFL_BASE_Env = $eeSFL_BASE->eeSFL_GetEnv(); // Get the Environment Array
	}
		
	eeSFL_BASE_VersionCheck(); // Update database if needed.
		
	// Get the Settings
	$eeSFL_Settings = $eeSFL_BASE->eeSFL_GetSettings(); // Get this list
	
	// Check/Create the Upload Folder
	if( !eeSFL_BASE_FileListDirCheck( $eeSFL_Settings['FileListDir'] ) ) { 
		$eeSFL_BASE_Log['errors'][] = 'The upload directory is acting up.';
	}
	
	eeSFL_BASE_Textdomain(); // Language Setup
	
	return TRUE;
}
add_action('init', 'eeSFL_BASE_Setup');



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


// Createing a New Post with Shortcode
function eeSFL_BASE_CreatePostwithShortcode() { 
	
	if(@$_POST['eeCreatePostType']) {
		
		global $eeSFL_BASE_Log;
		
		$eeShortcode = FALSE;
		$eeCreatePostType = FALSE;
		$eePostTitle = FALSE;
		
		$eeCreatePostType = sanitize_text_field(@$_POST['eeCreatePostType']);
		$eeShortcode = sanitize_text_field(@$_POST['eeShortcode']);
		$eePostTitle = sanitize_text_field(@$_POST['eePostTitle']);
		
		if(!$eeShortcode) { $eeShortcode = '[eeSFL]'; }
		
		if(!$eePostTitle) {
			$eePostTitle = 'My Simple File List ' . $eeCreatePostType;
		}
			
		if(($eeCreatePostType == "Post" OR $eeCreatePostType == "Page") AND $eeShortcode) {
			
			// Create Post Object
			$eeNewPost = array(
				'post_type'		=> $eeCreatePostType,
				'post_title'    => $eePostTitle,
				'post_content'  => '<div>' . $eeShortcode . '</div>',
				'post_status'   => 'draft'
			);
			
			// Create Post
			$eeNewPostID = wp_insert_post( $eeNewPost );
			
			if($eeNewPostID) {
				
				header('Location: /?p=' . $eeNewPostID);
			}
			
			return TRUE;
		}
	}
}
add_action( 'wp_loaded', 'eeSFL_BASE_CreatePostwithShortcode' );



// Shortcode
function eeSFL_BASE_Shortcode($atts, $content = null) {
	
	// Basic Usage: [eeSFL]
    
    global $eeSFL_BASE, $eeSFL_BASE_DevMode, $eeSFL_BASE_Log, $eeSFL_BASE_Env, $eeSFL_Settings, $eeSFL_BASE_ListRun, $eeSFL_BASE_UploadRun;
	
	$eeAdmin = is_admin();
	if($eeAdmin) { return FALSE; } // Don't execute shortcode on page editor
    
    $eeSFL_ListNumber = $eeSFL_BASE_ListRun; // Legacy 03/20
    $eeForceSort = FALSE;
    $eeShowUploader = FALSE;
	
	$eeOutput = '';
	
	$eeSFL_BASE_Log['L' . $eeSFL_BASE_ListRun][] = 'Shortcode Loading: ' . get_permalink();

	if( $eeSFL_BASE_ListRun > 1 AND isset($_GET['eeFront']) ) { return; }

    // Over-Riding Shortcode Attributes
	if($atts) {
	
		$atts = shortcode_atts( array( // Use lowercase att names only
			'showlist' => '', // YES, ADMIN, USER or NO
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
			'showfolder' => '', // eeSFLF - OBSOLETE
		), $atts );
		
		extract($atts);
		
		// Don't show anything if a folder is defined.
		if( strlen($showfolder) >= 1 ) { return '<p style="color:red;font-weight:bold;">ERROR 95</p>'; }
	
		$eeSFL_BASE_Log['L' . $eeSFL_BASE_ListRun][] = 'Shortcode Attributes...';
		
		if($showlist) { $eeSFL_Settings['ShowList'] = $showlist; }
		if($allowuploads) { $eeSFL_Settings['AllowUploads'] = $allowuploads; }
		if($showthumb) { $eeSFL_Settings['ShowFileThumb'] = $showthumb; }
		if($showdate) { $eeSFL_Settings['ShowFileDate'] = $showdate; }
		if($showsize) { $eeSFL_Settings['ShowFileSize'] = $showsize; }
		if($showheader) { $eeSFL_Settings['ShowHeader'] = $showheader; }
		if($showactions) { $eeSFL_Settings['ShowFileActions'] = $showactions; }
		
		
		if($sortby OR $sortorder) { // Force a re-sort of the file list array if a shortcode attribute was used
			if( $sortby != $eeSFL_Settings['SortBy'] OR $sortorder != $eeSFL_Settings['SortOrder'] ) {
				$eeForceSort = TRUE;
				$eeSFL_Settings['SortBy'] = $sortby;
				$eeSFL_Settings['SortOrder'] = $sortorder;
			} else {
				$eeForceSort = FALSE;
			}
		}
		
		// Legacy
		if($hidetype) { $eeHideType = strtolower($hidetype); } else { $eeHideType = FALSE; }
		if($hidename) { $eeHideName = strtolower($hidename); } else { $eeHideName = FALSE; }

		
	} else {
		$eeSFL_BASE_Log['L' . $eeSFL_BASE_ListRun][] = 'No Shortcode Attributes';
	}
	
	
	
	
	// Begin Front-Side List Display ==================================================================
	
	$eeOutput .= '<div class="eeSFL"';
	
	if($eeSFL_BASE_ListRun == 1) { $eeOutput .= ' id="eeSFL"'; } // 3/20 - Legacy for user CSS
	
	$eeOutput .= '><!-- $eeSFL_BASE_ListRun = ' . $eeSFL_BASE_ListRun . ' -->';
	
	// Who Can Upload?
	switch ($eeSFL_Settings['AllowUploads']) {
	    case 'YES':
	        break; // Show It
	    case 'USER':
	        // Show It If...
	        if( get_current_user_id() ) { break; } else { $eeSFL_Settings['AllowUploads'] = 'NO'; }
	    case 'ADMIN':
	        // Show It If...
	        if(current_user_can('manage_options')) { break; } else { $eeSFL_Settings['AllowUploads'] = 'NO'; }
	        break;
		default:
			$eeSFL_Settings['AllowUploads'] = 'NO'; // Show Nothing
	}
	
	// Show the Upload Form or Not
	if($eeSFL_Settings['AllowUploads'] != 'NO' AND $eeSFL_BASE_UploadRun == 1) {
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		require_once($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-upload-check.php');
		
		if( $eeSFL_Settings['UploadConfirm'] == 'NO' ) { $eeShowUploader = TRUE; } 
		
		if( !isset($_POST['eeSFL_Upload']) ) { $eeShowUploader = TRUE; }
			
		if($eeShowUploader AND $eeSFL_Settings['UploadPosition'] == 'Above') {
			
			$eeSFL_Nonce = wp_create_nonce('eeInclude');
			require_once($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-upload-form.php');
			
			$eeSFL_BASE_UploadRun++;
		}
	}
	
	// Who Can View the List?
	switch ($eeSFL_Settings['ShowList']) {
	    case 'YES':
	        break; // Show It
	    case 'USER':
	        // Show It If...
	        if( get_current_user_id() ) { break; } else { $eeSFL_Settings['ShowList'] = 'NO'; }
	    case 'ADMIN':
	        // Show It If...
	        if(current_user_can('manage_options')) { break; } else { $eeSFL_Settings['ShowList'] = 'NO'; }
	        break;
		default:
			$eeSFL_Settings['ShowList'] = 'NO'; // Show Nothing
	}
	
	if($eeSFL_Settings['ShowList'] != 'NO') {
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include(WP_PLUGIN_DIR . '/' . $eeSFL_BASE->eePluginNameSlug . '/ee-list-display.php');
	}
	
	if($eeShowUploader AND $eeSFL_Settings['UploadPosition'] == 'Below') {
			
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		require_once($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-upload-form.php');
		
		$eeSFL_BASE_UploadRun++;
	}
	
	$eeOutput .= '</div>'; // Ends .eeSFL block
	
	// Smooth Scrolling is AWESOME!
	if( isset($_REQUEST['ee']) AND $eeSFL_Settings['SmoothScroll'] == 'YES' ) { 
		$eeOutput .= '<script>eeSFL_BASE_ScrollToIt();</script>'; }
	
	$eeSFL_BASE_ListRun++;

	// Timer
	$eeSFL_Time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	$eeSFL_BASE_Log[] = 'Execution Time: ' . round($eeSFL_Time,3);
	
	// $eeOutput .= $eeSFL_BASE->eeSFL_WriteLogData(); // Only adds output if DevMode is ON
	
	// Give it back
	unset($eeSFL_Files);
	unset($eeSFL_BASE_Env);
	unset($eeSFL_Settings);
	unset($eeSFL_BASE_Log);
	
	
	
	// exit( htmlentities($eeOutput) );
	
	return $eeOutput; // Output the page
}
add_shortcode( 'eeSFL', 'eeSFL_BASE_Shortcode' );



// Load Front-side <head>
function eeSFL_BASE_Enqueue() {
	
	global $eeSFL_Settings, $eeSFL_VarsForJS;
	
	
	// CSS
    wp_register_style( 'ee-simple-file-list-css', plugin_dir_url(__FILE__) . 'css/styles.css', '', eeSFL_BASE_Version);
    wp_enqueue_style('ee-simple-file-list-css');
    
    if($eeSFL_Settings['AllowUploads'] != 'NO') {
	    wp_register_style( 'ee-simple-file-list-css-upload', plugins_url('css/styles-upload-form.css', __FILE__), '', eeSFL_BASE_Version );
		wp_enqueue_style('ee-simple-file-list-css-upload');
    }
    
    // List Style
    if($eeSFL_Settings['ShowListStyle'] == 'Flex') {
    	wp_register_style( 'ee-simple-file-list-css-flex', plugins_url('css/styles-flex.css', __FILE__), '', eeSFL_BASE_Version );
		wp_enqueue_style('ee-simple-file-list-css-flex');
	} elseif($eeSFL_Settings['ShowListStyle'] == 'Tiles') {
    	wp_register_style( 'ee-simple-file-list-css-tiles', plugins_url('css/styles-tiles.css', __FILE__), '', eeSFL_BASE_Version );
		wp_enqueue_style('ee-simple-file-list-css-tiles');
	} else {
		wp_register_style( 'ee-simple-file-list-css-table', plugins_url('css/styles-table.css', __FILE__), '', eeSFL_BASE_Version );
		wp_enqueue_style('ee-simple-file-list-css-table');
	}
	
	// List Theme
    if($eeSFL_Settings['ShowListTheme'] == 'Dark') {
    	wp_register_style( 'ee-simple-file-list-css-theme-dark', plugins_url('css/styles-theme-dark.css', __FILE__), '', eeSFL_BASE_Version );
		wp_enqueue_style('ee-simple-file-list-css-theme-dark');
	} else {
		wp_register_style( 'ee-simple-file-list-css-theme-light', plugins_url('css/styles-theme-light.css', __FILE__), '', eeSFL_BASE_Version );
		wp_enqueue_style('ee-simple-file-list-css-theme-light');
	}
	
	// Javascript
	$deps = array('jquery'); // Requires jQuery
	
	// Register Scripts
	wp_register_script( 'ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js' );
	// wp_register_script( 'ee-simple-file-list-js-foot', plugin_dir_url(__FILE__) . 'js/ee-footer.js' ); // Throws "jQuery Not Defined" error?
	
	// Enqueue
	wp_enqueue_script('ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js', $deps, eeSFL_BASE_Version, FALSE); // Head
	wp_enqueue_script('ee-simple-file-list-js-foot', plugin_dir_url(__FILE__) . 'js/ee-footer.js',$deps, eeSFL_BASE_Version, TRUE); // Footer
	
	// Uploader
	wp_register_script( 'ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'js/ee-uploader.js' );
	wp_enqueue_script('ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'js/ee-uploader.js',$deps, eeSFL_BASE_Version, TRUE);
	
	// Pass variables
	wp_localize_script( 'ee-simple-file-list-js-foot', 'eesfl_vars', $eeSFL_VarsForJS ); // Footer

}
add_action( 'wp_enqueue_scripts', 'eeSFL_BASE_Enqueue' );



// Admin <head>
function eeSFL_BASE_AdminHead($eeHook) {

	global $eeSFL_Settings, $eeSFL_VarsForJS;
	
	$deps = array('jquery');
	
	// wp_die($eeHook);
    
    $eeHooks = array(
    	'toplevel_page_ee-simple-file-list', // toplevel_page_ee-simple-file-list
    	// 'simple-file-list_page_ee-simple-file-list',
    	// 'simple-file-list_page_ee-simple-file-list-settings',
    	'file-list_page_ee-simple-file-list-access'
    );
    
    if(in_array($eeHook, $eeHooks)) {
        
        // CSS
        wp_enqueue_style( 'ee-simple-file-list-css', plugins_url('css/styles.css', __FILE__), '', eeSFL_BASE_Version );
        
        // List Style
        if($eeSFL_Settings['ShowListStyle'] == 'Flex') {
        	wp_enqueue_style( 'ee-simple-file-list-css-flex', plugins_url('css/styles-flex.css', __FILE__), '', eeSFL_BASE_Version );
        } elseif($eeSFL_Settings['ShowListStyle'] == 'Tiles') {
	        wp_enqueue_style( 'ee-simple-file-list-css-tiles', plugins_url('css/styles-tiles.css', __FILE__), '', eeSFL_BASE_Version );
        } else {
	        wp_enqueue_style( 'ee-simple-file-list-css-table', plugins_url('css/styles-table.css', __FILE__), '', eeSFL_BASE_Version );
        }
        
        wp_enqueue_style( 'ee-simple-file-list-css-admin', plugins_url('css/admin5.css', __FILE__), '', eeSFL_BASE_Version );
        
        
        // Javascript
        wp_enqueue_script('ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js', $deps, eeSFL_BASE_Version, FALSE);
		wp_enqueue_script('ee-simple-file-list-js-back', plugin_dir_url(__FILE__) . 'js/ee-back.js', $deps, eeSFL_BASE_Version, FALSE);
        wp_enqueue_script('ee-simple-file-list-js-foot', plugin_dir_url(__FILE__) . 'js/ee-footer.js', $deps, eeSFL_BASE_Version, TRUE);
        wp_enqueue_script('ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'js/ee-uploader.js',$deps, eeSFL_BASE_Version, TRUE);
		
		wp_localize_script('ee-simple-file-list-js-head', 'eeSFL_JS', array( 'pluginsUrl' => plugins_url() ) ); // Needs expanding for alert boxes
		
		// Pass variables
		wp_localize_script( 'ee-simple-file-list-js-foot', 'eesfl_vars', $eeSFL_VarsForJS ); // Footer
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
	
	// return 'TEST';
	
	global $eeSFL_BASE, $eeSFL_BASE_Log, $eeSFL_Settings;
	
	// The FILE object
	if(empty($_FILES)) { return 'Missing File Input'; }
	
	if( !is_admin() ) { // Front-side protections
	
		// Who should be uploading?
		switch ($eeSFL_Settings['AllowUploads']) {
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
	
	// The Upload Destination - Relative to WP home dir
	if(@$_POST['eeSFL_FileUploadDir']) {
		
		$eeSFL_FileUploadDir = sanitize_text_field( urldecode($_POST['eeSFL_FileUploadDir']) );
		
		if(!$eeSFL_FileUploadDir) { return('Bad Upload Folder'); }
			
	} else { 
		return 'No Upload Folder Given';
	}
	
	// Check size
	$eeSFL_FileSize = filter_var($_FILES['file']['size'], FILTER_VALIDATE_INT);
	$eeSFL_UploadMaxFileSize = $eeSFL_Settings['UploadMaxFileSize']*1024*1024; // Convert MB to B
	
	if($eeSFL_FileSize > $eeSFL_UploadMaxFileSize) {
		return "File size is too large.";
	}
	
	// Go...
	if($eeSFL_FileUploadDir AND is_dir(ABSPATH . $eeSFL_FileUploadDir)) {
			
		if(wp_verify_nonce(@$_POST['ee-simple-file-list-upload'], 'ee-simple-file-list-upload')) {
			
			// Temp file
			$eeTempFile = $_FILES['file']['tmp_name'];
			
			// Clean up messy names
			$eeSFL_FileName = eeSFL_BASE_SanitizeFileName($_FILES['file']['name']);
			
			eeSFL_BASE_DetectUpwardTraversal($eeSFL_FileUploadDir . $eeSFL_FileName); // Die if foolishness
			
			$eeSFL_PathParts = pathinfo($eeSFL_FileName);
			$eeSFL_FileNameAlone = $eeSFL_PathParts['filename'];
			$eeSFL_Extension = strtolower($eeSFL_PathParts['extension']); // We need to do this here and in eeSFL_ProcessUpload()
			
			// Format Check
			$eeSFL_FileFormatsArray = array_map('trim', explode(',', $eeSFL_Settings['FileFormats']));
			
			if(!in_array($eeSFL_Extension, $eeSFL_FileFormatsArray) OR in_array($eeSFL_Extension, $eeSFL_BASE->eeForbiddenTypes)) {
				return 'File type not allowed: (' . $eeSFL_Extension . ')';	
			}
			
			// Assemble full path, checking if it already exists
			$eeSFL_FileName = eeSFL_BASE_CheckForDuplicateFile($eeSFL_FileName);
			$eeSFL_TargetFile = $eeSFL_FileUploadDir . $eeSFL_FileName;

			// Check if the name has changed
			if($_FILES['file']['name'] != $eeSFL_FileName) {
				
				// Set a transient with the new name so we can get it in ProcessUpload() after the form is submitted
				$eeOldFilePath = 'eeSFL-Renamed-' . str_replace($eeSFL_Settings['FileListDir'], '', $eeSFL_FileUploadDir . $_FILES['file']['name']); // Strip the FileListDir
				$eeOldFilePath = esc_sql(urlencode($eeOldFilePath));
				$eeNewFilePath = str_replace($eeSFL_Settings['FileListDir'], '', $eeSFL_TargetFile); // Strip the FileListDir
				set_transient($eeOldFilePath, $eeNewFilePath, 900); // Expires in 15 minutes
			}
			
			$eeTarget = ABSPATH . $eeSFL_TargetFile;
			
			// Save the file
			if( move_uploaded_file($eeTempFile, $eeTarget) ) {
				
				if(!is_file($eeTarget)) {
					return 'Error - File System Error.'; // No good.
				
				} else {
					
					// Check for currupt images
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
					
					return 'SUCCESS';
				}
				 
			} else {
				return 'Cannot save the uploaded file: ' . $eeSFL_TargetFile;
			}
		
		} else {
			
			return 'ERROR 98';
		}
		
	} else {
		return 'Upload Path Not Found: ' . $eeSFL_FileUploadDir;
	}
}



// File Editor Engine
function eeSFL_BASE_FileEditor() {
	
	global $eeSFL_BASE, $eeSFL_BASE_Log, $eeSFL_Settings;
	$eeFileName = '';
	$eeFileAction = '';
	
	// WP Security
	if( !check_ajax_referer( 'eeSFL_ActionNonce', 'eeSecurity' ) ) {
		return 'ERROR 98';	
	}
	
	// Check if we should be doing this
	if(is_admin() OR $eeSFL_Settings['AllowFrontManage'] == 'YES') {
		
		// The Action
		if(@$_POST['eeFileAction']) { 
			$eeFileAction = sanitize_text_field($_POST['eeFileAction']); 
		}
		if(!$eeFileAction) { 
			return "Missing the Action";
		}
				
		// The File Name
		if(@$_POST['eeFileName']) { 
			$eeFileName = sanitize_text_field($_POST['eeFileName']); 
		}
		if(!$eeFileName) { 
			return "Missing the Current File Name";
		}
		
		// Renaming
		if( strpos($eeFileAction, 'Rename') === 0 ) {
		
			// If Renaming
			if( strpos($eeFileAction, '|') ) {
				$eeArray = explode('|', $eeFileAction);
				$eeFileAction = $eeArray[0];
				$eeNewFileName = urldecode( $eeArray[1] );
				$eeNewFileName = eeSFL_BASE_SanitizeFileName($eeNewFileName);
			}
			
			if($eeNewFileName) {
				
				$eePathParts = pathinfo($eeFileName);
				$eeOldExtension = strtolower($eePathParts['extension']); // Prevent changing file extension
				$eePathParts = pathinfo($eeNewFileName);
				$eeNewExtension = strtolower($eePathParts['extension']);
				if($eeOldExtension != $eeNewExtension) { 
					return "Changing File Extensions is Not Allowed";
				}
				
				eeSFL_BASE_DetectUpwardTraversal($eeSFL_Settings['FileListDir'] . $eeNewFileName); // Die if foolishness
				
				$eeNewFileName = eeSFL_BASE_CheckForDuplicateFile($eeNewFileName); // Don't over-write
				
				$eeOldFilePath = ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFileName;
				$eeNewFilePath = ABSPATH . $eeSFL_Settings['FileListDir'] . $eeNewFileName;
				
				if( !rename($eeOldFilePath, $eeNewFilePath) ) {
					
					return 'Could Not Rename ' . $eeOldFilePath . ' to ' . $eeNewFilePath;
				
				} else {
					
					$eeSFL_BASE->eeSFL_UpdateFileDetail($eeFileName, 'FilePath', $eeNewFileName);
					
					$eeSFL_BASE->eeSFL_UpdateThumbnail($eeFileName, $eeNewFileName); // Rename the thumb
					
					return 'SUCCESS';
				}
			
			} else { 
				return "Missing the New File Name";
			}
			
		} elseif($eeFileAction == 'Delete') {
			
			eeSFL_BASE_DetectUpwardTraversal($eeSFL_Settings['FileListDir'] . $eeFileName); // Die if foolishness
			
			$eeFilePath = ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFileName;
			
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
			}	
		
		} elseif($eeFileAction == 'UpdateDesc') {
			
			// The Description
			if(filter_var(@$_POST['eeFileID'], FILTER_VALIDATE_INT) !== FALSE) { // Might be a zero
				$eeFileID = $_POST['eeFileID'];
			} else { 
				$eeFileID = 0;
			}
			
			// The Description
			if(@$_POST['eeFileDesc']) { 
				$eeFileDesc = sanitize_text_field($_POST['eeFileDesc']); 
			} else { 
				$eeFileDesc = '';
			}
			
			$eeSFL_BASE->eeSFL_UpdateFileDetail($eeFileName, 'FileDescription', $eeFileDesc);
		
			return 'SUCCESS';
			
		} else {
			
			return; // Nothing to do	
		}
	}
	
	// We should not be doing this
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
	
	global $eeSFL_BASE, $eeSFL_Settings, $eeSFL_BASE_Env, $eeSFL_BASE_Log;
	
	// Only include when accessing the plugin admin pages
	if(@$_GET['page'] == $eeSFL_BASE->eePluginSlug) {
		
		$eeOutput = '<!-- Simple File List Admin -->';
		$eeSFL_BASE_Log['RunTime'][] = 'Admin Menu Loading ...';
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include_once(WP_PLUGIN_DIR . '/' . $eeSFL_BASE->eePluginNameSlug . '/ee-admin-page.php'); // Admin's List Management Page
	}
	
	// The Admin Menu
	add_menu_page(
		__($eeSFL_BASE->eePluginName, $eeSFL_BASE->eePluginSlug), // Page Title
		__($eeSFL_BASE->eePluginMenuTitle, $eeSFL_BASE->eePluginSlug), // Menu Title
		'activate_plugins', // User status required to see the menu
		$eeSFL_BASE->eePluginSlug, // Slug
		'eeSFL_BASE_ManageLists', // Function that displays the menu page
		'dashicons-index-card' // Icon used
	);
	
}
add_action( 'admin_menu', 'eeSFL_BASE_AdminMenu' );



// Plugin Version Check
// We only run the update function if there has been a change in the database revision.
function eeSFL_BASE_VersionCheck() { 
		
	global $eeSFL_BASE_Log;
	
	$eeSFL_BASE_Log['RunTime'][] = 'Checking DB Version...';
	
	$eeInstalled = get_option('eeSFL-FREE-DB-Version'); // Legacy
	if(!$eeInstalled ) { $eeInstalled = get_option('eeSFL_BASE_DB_Version'); } // Hip, now, and in-with-the-times.
	
	if( $eeInstalled < eeSFL_BASE_DB_Version ) { // OR !get_option('eeSFL_Settings_1')
		
		eeSFL_BASE_UpdateThisPlugin($eeInstalled); // Run the DB update process
		
		update_option('eeSFL_BASE_DB_Version', eeSFL_BASE_DB_Version);
		
		return TRUE;
	
	} else {
		
		$eeSFL_BASE_Log['RunTime'][] = 'Database OK';
		
		return FALSE;
	}
}



// Install or Update to Newer Version
function eeSFL_BASE_UpdateThisPlugin($eeInstalled) {
	
	global $wpdb, $eeSFL_BASE, $eeSFL_BASE_Log, $eeSFL_BASE_Env;
	
	$eeSettings = array();
	
	// Things that may or may not be there
	$eeDefaultSettings = $eeSFL_BASE->DefaultListSettings;
	$eeOldOldSettings = get_option('eeSFL-1-ShowList'); // SFL 3.x
	$eeOldSettings = get_option('eeSFL-Settings'); // SFL 4.0
	$eeSettingsCurrent = get_option('eeSFL_Settings_1'); // SFL 4.1
	$wpAdminEmail = get_option('admin_email');
	
	if($eeOldOldSettings AND !$eeOldSettings) { // Upgrade from Simple File List 3.x
		
		$eeSFL_BASE_Log['Updating'][] = 'Version 3.x Detected';
		
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
	
	// If previous settings
	if( count($eeSettings) >= 1 ) {
		
		// Loop through the default settings and add new items
		foreach( $eeDefaultSettings as $eeKey => $eeValue) { 
				
			// Look for new items
			if( !array_key_exists($eeKey, $eeSettings) ) { 
			
				$eeSettings[$eeKey] = $eeValue; // Add the default item
			}
		}
		
		$eeSFL_BASE_Log['Updating']['List: 1'] = $eeSettings;
		
		$eePreCount = count($eeSettings);
		
		// Update File List Option Name, if needed - Rename the file list's option_name value
		if(get_option('eeSFL-FileList-1')) {
			$eeQuery = "UPDATE $wpdb->options SET option_name = 'eeSFL_FileList_1' WHERE option_name = 'eeSFL-FileList-1'";
			$wpdb->query( $eeQuery );
		}
		
		// Check the File List Directory
		eeSFL_BASE_FileListDirCheck( $eeSettings['FileListDir'] );	
	
	// New Installation
	} else {
		
		$eeSettings = $eeDefaultSettings;
		
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
		
	// Add if needed
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
	delete_option('eeSFL-FREE-DB-Version'); // Out with the old
	delete_option('eeSFL_BASE_DB_Version'); // Out with the old
	delete_option('eeSFLA-Settings'); // Out with the old
	delete_option('eeSFL-Legacy'); // Don't need this anymore
	
	$eeSFL_BASE_Log['Updating'][] = ' - Plugin database at version ' . eeSFL_BASE_DB_Version;
	
	// Write the log file to the Database
	$eeSFL_BASE->eeSFL_WriteLogData($eeSFL_BASE_Log);
	
	return TRUE;
}




// Plugin Activation ==========================================================
function eeSFL_BASE_Activate() {
	
	return TRUE; // All done, nothing to do here.	
}
register_activation_hook( __FILE__, 'eeSFL_BASE_Activate' );

?>