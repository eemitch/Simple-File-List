<?php

/**
 * @package Element Engage - Simple File List
 */
/*
Plugin Name: Simple File List
Plugin URI: http://simplefilelist.com
Description: A Basic File List Manager with File Uploader
Author: Mitchell Bennis
Version: 4.3.3
Author URI: http://simplefilelist.com
License: GPLv2 or later
Text Domain: ee-simple-file-list
Domain Path: /languages
*/

$eeSFL_FREE_DevMode = TRUE; // TRUE/FALSE = Enables visible logging or not

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// SFL Versions
define('eeSFL_FREE_Version', '4.3.3'); // Plugin version - DON'T FORGET TO UPDATE ABOVE TOO !!!
define('eeSFL_FREE_DB_Version', '5.1'); // Database structure version - used for eeSFL_FREE_VersionCheck()
define('eeSFL_FREE_Cache_Version', eeSFL_FREE_Version); // Cache-Buster version for static files - used when updating CSS/JS

// LEGACY
if( !defined('eeSFL_Version') ) { define('eeSFL_Version', eeSFL_FREE_Version); } // Fix for Folder Extension Need, post 4.2.12

// Our Core
$eeSFL_FREE = FALSE; // Our main class
$eeSFL_Settings = array(); // All List Info
$eeSFL_FREE_Env = array(); // Environment
$eeSFL_FREE_ListRun = 1; // Count of lists per page
$eeSFL_FREE_UploadFormRun = FALSE; // Check if uploader form has run

// The Log - Written to wp_option -> eeSFL_Log
$eeSFL_FREE_Log = array('Simple File List is Loading...');
$eeSFL_FREE_Log[] = 'Version ' . eeSFL_FREE_Version . ' (DB ' . eeSFL_FREE_DB_Version . ')';
// Format: ['SFL'][] => 'runtime log entry'
//	['messages'][] = 'Message to the user'
//	['errors'][] = 'Error condition to user'


// simplefilelist_upload_job <<<----- File Upload Action Hooks (Ajax)
add_action( 'wp_ajax_simplefilelist_upload_job', 'simplefilelist_upload_job' );
add_action( 'wp_ajax_nopriv_simplefilelist_upload_job', 'simplefilelist_upload_job' );

// simplefilelist_edit_job <<<----- File Edit Action Hooks (Ajax)
add_action( 'wp_ajax_simplefilelist_edit_job', 'simplefilelist_edit_job' );
add_action( 'wp_ajax_nopriv_simplefilelist_edit_job', 'simplefilelist_edit_job' );


// Display Notice to Update Simple File List Pro
function eeSFL_FREE_ALERT() {
    
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
add_action( 'admin_notices', 'eeSFL_FREE_ALERT' );



// Plugin Setup
function eeSFL_FREE_Setup() {
	
	global $eeSFL_FREE, $eeSFL_FREE_Log, $eeSFL_Settings, $eeSFL_FREE_Env;
	
	$eeSFL_FREE_Log['SFL'][] = 'Running Setup...';
	
	// Get Functions
	$eeSFL_Nonce = wp_create_nonce('eeSFL_Functions'); // Security
	include_once(plugin_dir_path(__FILE__) . 'includes/ee-functions.php'); // Our Functions
	
	// Get Class
	if(!class_exists('eeSFL_FREE')) {
		$eeSFL_Nonce = wp_create_nonce('eeSFL_Class'); // Security
		require_once(plugin_dir_path(__FILE__) . 'includes/ee-class.php'); // Get the main class file
		$eeSFL_FREE = new eeSFL_FREE_MainClass(); // Initiate the SFL Class
		$eeSFL_FREE_Env = $eeSFL_FREE->eeSFL_GetEnv(); // Get the Environment Array
		
		eeSFL_FREE_VersionCheck(); // Update database if needed.
		
		// Get the lists
		$eeSFL_Settings = $eeSFL_FREE->eeSFL_GetSettings(); // Get this list
	}
	
	// If Sending Files
	if(@$_POST['eeSFL_Send']) { $eeSFL_FREE->eeSFL_SendFilesEmail(); }
	
	// Check/Create the Upload Folder
	if( !eeSFL_FREE_FileListDirCheck( $eeSFL_Settings['FileListDir'] ) ) { 
		$eeSFL_FREE_Log['errors'][] = 'The upload directory is acting up.';
	}
	
	eeSFL_FREE_Textdomain(); // Language Setup
	
	return TRUE;
}
add_action('init', 'eeSFL_FREE_Setup');



// Custom Hook
function eeSFL_FREE_UploadCompleted() {
    do_action('eeSFL_FREE_UploadCompleted'); // To be fired post-upload
}

function eeSFL_FREE_UploadCompletedAdmin() {
    do_action('eeSFL_FREE_UploadCompletedAdmin'); // To be fired post-upload
}


// Log Failed Emails
function eeSFL_FREE_action_wp_mail_failed($wp_error) {
    
    return error_log(print_r($wp_error, true));
}
add_action('wp_mail_failed', 'eeSFL_FREE_action_wp_mail_failed', 10, 1);



// Language Enabler
function eeSFL_FREE_Textdomain() {
    load_plugin_textdomain( 'ee-simple-file-list', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}


// Createing a New Post with Shortcode
function eeSFL_FREE_CreatePostwithShortcode() { 
	
	if(@$_POST['eeCreatePostType']) {
		
		global $eeSFL_FREE_Log;
		
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
add_action( 'wp_loaded', 'eeSFL_FREE_CreatePostwithShortcode' );



// Shortcode
function eeSFL_FREE_Shortcode($atts, $content = null) {
	
	// Basic Usage: [eeSFL]
    
    global $eeSFL_FREE, $eeSFL_FREE_DevMode, $eeSFL_FREE_Log, $eeSFL_FREE_Env, $eeSFL_Settings, $eeSFL_FREE_ListRun, $eeSFL_FREE_UploadFormRun;
	
	$eeAdmin = is_admin();
	if($eeAdmin) { return FALSE; } // Don't execute shortcode on page editor
    
    $eeSFL_ListNumber = $eeSFL_FREE_ListRun; // Legacy 03/20
    $eeForceSort = FALSE;
	
	$eeOutput = '';
	
	$eeSFL_FREE_Log['L' . $eeSFL_FREE_ListRun][] = 'Shortcode Loading: ' . get_permalink();

	if( $eeSFL_FREE_ListRun > 1 AND @$_GET['eeFront'] ) { return; }

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
	
		$eeSFL_FREE_Log['L' . $eeSFL_FREE_ListRun][] = 'Shortcode Attributes...';
		
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
		if($hidetype) { $eeSFL_HideType = strtolower($hidetype); } else { $eeSFL_HideType = FALSE; }
		if($hidename) { $eeSFL_HideName = strtolower($hidename); } else { $eeSFL_HideName = FALSE; }

		
	} else {
		$eeSFL_FREE_Log['L' . $eeSFL_FREE_ListRun][] = 'No Shortcode Attributes';
	}
	
	
	// Begin Front-Side List Display ==================================================================
	
	$eeOutput .= '<div class="eeSFL"';
	
	if($eeSFL_FREE_ListRun == 1) {$eeOutput .= ' id="eeSFL"'; } // 3/20 - Legacy for user CSS
	
	$eeOutput .= '>';
	
	// Upload Check
	$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
	include(WP_PLUGIN_DIR . '/' . $eeSFL_FREE->eePluginNameSlug . '/includes/ee-upload-check.php');
	
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
	
	if($eeSFL_Settings['AllowUploads'] != 'NO' AND !$eeSFL_FREE_UploadFormRun) {
		if(!@$_POST['eeSFL_Upload'] AND !@$_POST['eeSFLS_Searching']) {
			$eeSFL_Nonce = wp_create_nonce('eeInclude');
			include($eeSFL_FREE_Env['pluginDir'] . '/includes/ee-upload-form.php');
			$eeSFL_FREE_UploadFormRun = TRUE;
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
		include(WP_PLUGIN_DIR . '/' . $eeSFL_FREE->eePluginNameSlug . '/ee-list-display.php');
	}
	
	$eeOutput .= '</div>'; // Ends .eeSFL block
	
	$eeSFL_FREE_ListRun++;

	// Timer
	$eeSFL_Time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	$eeSFL_FREE_Log[] = 'Execution Time: ' . round($eeSFL_Time,3);
	
	if($eeSFL_FREE_DevMode) {
		if(@$_REQUEST) { $eeOutput .= '<pre>REQUEST ' . print_r($_REQUEST, TRUE) . '</pre>'; array_unshift($eeSFL_FREE_Log, $_REQUEST); }
		$eeOutput .= '<pre>Display File Array ' . print_r(@$eeSFL_Files, TRUE) . '</pre>';
		$eeOutput .= '<pre>Display List Settings ' . print_r($eeSFL_Settings, TRUE) . '</pre>';
		$eeOutput .= '<pre>Environment ' . print_r($eeSFL_FREE_Env, TRUE) . '</pre>';
		$eeOutput .= '<pre>Runtime Log ' . print_r($eeSFL_FREE_Log, TRUE) . '</pre>';
		$eeSFL_FREE->eeSFL_WriteLogData($eeSFL_FREE_Log);
	}
	
	// Give it back
	unset($eeSFL_Files);
	unset($eeSFL_FREE_Env);
	unset($eeSFL_Settings);
	unset($eeSFL_FREE_Log);
	
	return $eeOutput; // Output the page
}
add_shortcode( 'eeSFL', 'eeSFL_FREE_Shortcode' );



// Load Front-side <head>
function eeSFL_FREE_Enqueue() {
	
	global $eeSFL_Settings;
	
	// Register the style like this for a theme:
    wp_register_style( 'ee-simple-file-list-css', plugin_dir_url(__FILE__) . 'css/eeStyles.css', '', eeSFL_FREE_Cache_Version);
	wp_enqueue_style('ee-simple-file-list-css');
	
	// Javascript
	$deps = array('jquery'); // Requires jQuery
	
	$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
	$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		'eeEditText' => __('Edit', 'ee-simple-file-list'), // Edit link text
		'eeConfirmDeleteText' => __('Are you sure you want to delete this?', 'ee-simple-file-list'), // Delete confirmation
		'eeCancelText' => __('Cancel', 'ee-simple-file-list')
	);
	
	// Register Scripts
	wp_register_script( 'ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js' );
	// wp_register_script( 'ee-simple-file-list-js-foot', plugin_dir_url(__FILE__) . 'js/ee-footer.js' ); // Throws "jQuery Not Defined" error?
	
	// Enqueue
	wp_enqueue_script('ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js', $deps, eeSFL_FREE_Cache_Version, FALSE); // Head
	wp_enqueue_script('ee-simple-file-list-js-foot', plugin_dir_url(__FILE__) . 'js/ee-footer.js',$deps, eeSFL_FREE_Cache_Version, TRUE); // Footer
	
	// Uploader
	wp_register_script( 'ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'js/ee-uploader.js' );
	wp_enqueue_script('ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'js/ee-uploader.js',$deps, eeSFL_FREE_Cache_Version, TRUE);
	
	// Pass variables
	wp_localize_script( 'ee-simple-file-list-js-foot', 'eesfl_vars', $params ); // Footer

}
add_action( 'wp_enqueue_scripts', 'eeSFL_FREE_Enqueue' );



// Admin <head>
function eeSFL_FREE_AdminHead($eeHook) {

	$deps = array('jquery');
	
	$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
	$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		'eeEditText' => __('Edit', 'ee-simple-file-list'), // Edit link text
		'eeConfirmDeleteText' => __('Are you sure you want to delete this?', 'ee-simple-file-list'), // Delete confirmation
		'eeCancelText' => __('Cancel', 'ee-simple-file-list'),
		
		// Shortcode Builder
		'eeShowText' => __('Show', 'ee-simple-file-list'), 
		'eeHideText' => __('Hide', 'ee-simple-file-list')
	);
	
	// wp_die($eeHook);
    
    $eeHooks = array(
    	'toplevel_page_ee-simple-file-list', // toplevel_page_ee-simple-file-list
    	// 'simple-file-list_page_ee-simple-file-list',
    	// 'simple-file-list_page_ee-simple-file-list-settings',
    	'file-list_page_ee-simple-file-list-access'
    );
    
    if(in_array($eeHook, $eeHooks)) {
        
        // CSS
        wp_enqueue_style( 'ee-simple-file-list-css-front', plugins_url('css/eeStyles.css', __FILE__), '', eeSFL_FREE_Cache_Version );
        wp_enqueue_style( 'ee-simple-file-list-css-back', plugins_url('css/eeStyles-Back.css', __FILE__), '', eeSFL_FREE_Cache_Version );
        
        // Javascript
        wp_enqueue_script('ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js', $deps, eeSFL_FREE_Cache_Version, FALSE);
		wp_enqueue_script('ee-simple-file-list-js-back', plugin_dir_url(__FILE__) . 'js/ee-back.js', $deps, eeSFL_FREE_Cache_Version, FALSE);
        wp_enqueue_script('ee-simple-file-list-js-foot', plugin_dir_url(__FILE__) . 'js/ee-footer.js', $deps, eeSFL_FREE_Cache_Version, TRUE);
        wp_enqueue_script('ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'js/ee-uploader.js',$deps, eeSFL_FREE_Cache_Version, TRUE);
		
		wp_localize_script('ee-simple-file-list-js-head', 'eeSFL_JS', array( 'pluginsUrl' => plugins_url() ) ); // Needs expanding for alert boxes
		
		// Pass variables
		wp_localize_script( 'ee-simple-file-list-js-foot', 'eesfl_vars', $params ); // Footer
    }  
}
add_action('admin_enqueue_scripts', 'eeSFL_FREE_AdminHead');






// Ajax Handler
// Function name must be the same as the action name to work on front side ?
function simplefilelist_upload_job() {

	$eeResult = eeSFL_FREE_FileUploader();

	echo $eeResult;

	wp_die();

}	
add_action( 'wp_ajax_simplefilelist_upload_job', 'simplefilelist_upload_job' );


function simplefilelist_edit_job() {

	$eeResult = eeSFL_FREE_FileEditor();

	echo $eeResult;

	wp_die();

}	
add_action( 'wp_ajax_simplefilelist_edit_job', 'simplefilelist_edit_job' );



// File Upload Engine
function eeSFL_FREE_FileUploader() {
	
	// return 'TEST';
	
	global $eeSFL_FREE, $eeSFL_FREE_Log, $eeSFL_Settings;
	
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
			$eeSFL_FileName = eeSFL_FREE_SanitizeFileName($_FILES['file']['name']);
			
			eeSFL_FREE_DetectUpwardTraversal($eeSFL_FileUploadDir . $eeSFL_FileName); // Die if foolishness
			
			$eeSFL_PathParts = pathinfo($eeSFL_FileName);
			$eeSFL_FileNameAlone = $eeSFL_PathParts['filename'];
			$eeSFL_Extension = strtolower($eeSFL_PathParts['extension']); // We need to do this here and in eeSFL_ProcessUpload()
			
			// Format Check
			$eeSFL_FileFormatsArray = array_map('trim', explode(',', $eeSFL_Settings['FileFormats']));
			
			if(!in_array($eeSFL_Extension, $eeSFL_FileFormatsArray) OR in_array($eeSFL_Extension, $eeSFL_FREE->eeForbiddenTypes)) {
				return 'File type not allowed: (' . $eeSFL_Extension . ')';	
			}
			
			// Assemble FilePath
			$eeSFL_TargetFile = $eeSFL_FileUploadDir . $eeSFL_FileNameAlone . '.' . $eeSFL_Extension;
			
			// Check if it already exists
			$eeSFL_TargetFile = eeSFL_FREE_CheckForDuplicateFile($eeSFL_TargetFile);


			// Check if the name has changed
			if($_FILES['file']['name'] != basename($eeSFL_TargetFile)) {
				
				// Set a transient with the new name so we can get it in ProcessUpload() after the form is submitted
				$eeOldFilePath = str_replace($eeSFL_Settings['FileListDir'], '', $eeSFL_FileUploadDir . $_FILES['file']['name']); // Strip the FileListDir
				$eeNewFilePath = str_replace($eeSFL_Settings['FileListDir'], '', $eeSFL_TargetFile); // Strip the FileListDir
				set_transient('eeSFL-Renamed-' . $eeOldFilePath, $eeNewFilePath, 900); // Expires in 15 minutes
			}
			
			$eeTarget = ABSPATH . $eeSFL_TargetFile;
			
			// Save the file
			if( move_uploaded_file($eeTempFile, $eeTarget) ) {
				
				if(!is_file($eeTarget)) {
					return 'Error - File System Error.'; // No good.
				
				} else {
					
					// Check for currupt images
					if( in_array($eeSFL_Extension, $eeSFL_FREE->eeDynamicImageThumbFormats) ) {
						
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
					
					// Update the File Size
					$eeSFL_FREE->eeSFL_UpdateFileDetail($eeFileName, 'FileSize', $eeSFL_FileSize);
					
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
function eeSFL_FREE_FileEditor() {
	
	global $eeSFL_FREE, $eeSFL_FREE_Log, $eeSFL_Settings;
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
				$eeNewFileName = eeSFL_FREE_SanitizeFileName($eeNewFileName);
			}
			
			if($eeNewFileName) {
				
				$eePathParts = pathinfo($eeFileName);
				$eeOldExtension = strtolower($eePathParts['extension']); // Prevent changing file extension
				$eePathParts = pathinfo($eeNewFileName);
				$eeNewExtension = strtolower($eePathParts['extension']);
				if($eeOldExtension != $eeNewExtension) { 
					return "Changing File Extensions is Not Allowed";
				}
				
				eeSFL_FREE_DetectUpwardTraversal($eeSFL_Settings['FileListDir'] . $eeNewFileName); // Die if foolishness
				
				$eeNewFileName = eeSFL_FREE_CheckForDuplicateFile($eeSFL_Settings['FileListDir'] . $eeNewFileName); // Don't over-write
				
				$eeOldFilePath = ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFileName;
				$eeNewFilePath = ABSPATH . $eeNewFileName;
				
				if( !rename($eeOldFilePath, $eeNewFilePath) ) {
					
					return 'Could Not Rename ' . $eeOldFilePath . ' to ' . $eeNewFilePath;
				
				} else {
					
					$eeSFL_FREE->eeSFL_UpdateFileDetail($eeFileName, 'FilePath', $eeNewFileName);
					
					return 'SUCCESS';
				}
			
			} else { 
				return "Missing the New File Name";
			}
			
		} elseif($eeFileAction == 'Delete') {
			
			eeSFL_FREE_DetectUpwardTraversal($eeSFL_Settings['FileListDir'] . $eeFileName); // Die if foolishness
			
			$eeFilePath = ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFileName;
			
			if( strpos($eeFileName, '.') ) { // Gotta be a File - Looking for the dot rather than using is_file() for better speed
				
				if(unlink($eeFilePath)) {
					
					delete_transient('eeSFL_FileList_1');
					
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
			
			$eeSFL_FREE->eeSFL_UpdateFileDetail($eeFileName, 'FileDescription', $eeFileDesc);
		
			return 'SUCCESS';
			
		} else {
			
			return; // Nothing to do	
		}
	}
	
	// We should not be doing this
}








// Add Action Links to the Plugins Page
function eeSFL_FREE_ActionPluginLinks( $links ) {
	
	$eeLinks = array(
		'<a href="' . admin_url( 'admin.php?page=ee-simple-file-list' ) . '">' . __('Admin List', 'ee-simple-file-list') . '</a>',
		'<a href="' . admin_url( 'admin.php?page=ee-simple-file-list&tab=settings' ) . '">' . __('Settings', 'ee-simple-file-list') . '</a>'
	);
	return array_merge( $links, $eeLinks );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'eeSFL_FREE_ActionPluginLinks' );



// Admin Pages
function eeSFL_FREE_AdminMenu() {
	
	global $eeSFL_FREE, $eeSFL_Settings, $eeSFL_FREE_Env, $eeSFL_FREE_Log;
	
	// Only include when accessing the plugin admin pages
	if(@$_GET['page'] == $eeSFL_FREE->eePluginSlug) {
		
		$eeOutput = '<!-- Simple File List Admin -->';
		$eeSFL_FREE_Log['SFL'][] = 'Admin Menu Loading ...';
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include_once(WP_PLUGIN_DIR . '/' . $eeSFL_FREE->eePluginNameSlug . '/ee-admin-page.php'); // Admin's List Management Page
	}
	
	// Admin Menu Visability
	switch ($eeSFL_Settings['AdminRole']) {
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
		__($eeSFL_FREE->eePluginName, $eeSFL_FREE->eePluginSlug), // Page Title
		__($eeSFL_FREE->eePluginMenuTitle, $eeSFL_FREE->eePluginSlug), // Menu Title
		$eeCapability, // User status reguired to see the menu
		$eeSFL_FREE->eePluginSlug, // Slug
		'eeSFL_FREE_ManageLists', // Function that displays the menu page
		'dashicons-index-card' // Icon used
	);
	
}
add_action( 'admin_menu', 'eeSFL_FREE_AdminMenu' );



// Plugin Version Check
// We only run the update function if there has been a change in the database revision.
function eeSFL_FREE_VersionCheck() { 
		
	global $eeSFL_FREE_Log;
	
	$eeSFL_FREE_Log['SFL'][] = 'Checking DB Version...';
	
	$eeInstalled = get_option('eeSFL-FREE-DB-Version'); // Legacy
	if(!$eeInstalled ) { $eeInstalled = get_option('eeSFL_FREE_DB_Version'); } // Hip, now, and in-with-the-times.
	
	if( $eeInstalled < eeSFL_FREE_DB_Version ) { // OR !get_option('eeSFL_Settings_1')
		
		eeSFL_FREE_UpdateThisPlugin($eeInstalled); // Run the DB update process
	
	} else {
		
		$eeSFL_FREE_Log['SFL'][] = 'Database OK';
		
		return TRUE;
	}
}



// Perform DB Update
function eeSFL_FREE_UpdateThisPlugin($eeInstalled) {
	
	global $eeSFL_FREE, $eeSFL_FREE_Log, $eeSFL_FREE_Env;
		
	$eeSFL_FREE_Log['SFL'][] = 'Updating the Database...';
	
	if($eeInstalled) {
		
		if( version_compare( $eeInstalled, '4.2', '<') ) { 
			
			delete_transient('eeSFL_FileList-1'); // Force a re-scan because now we're storing a sorted file array.
		} 
		
		if(version_compare( $eeInstalled, '4.3', '<')) { // Renamed the DB option name: eeSFL-Settings to eeSFL_Settings_1 
	
			$eeSettings = get_option('eeSFL-Settings');
			update_option('eeSFL_Settings_1', $eeSettings[1]); // No more arrays inside arrays
			delete_option('eeSFL-Settings'); // Out with the old
			delete_option('eeSFL-FREE-DB-Version'); // Out with the old
			
			// Rename the File List array's option name
			$eeFiles = get_option('eeSFL-FileList-1');
			add_option('eeSFL_FileList_1', $eeFiles); // In with the new ...
			delete_option('eeSFL-FileList-1'); // Out with the old
			
			$eeArray = get_option('eeSFL-Log');
			add_option('eeSFL_FREE_Log', $eeArray);
			delete_option('eeSFL-Log'); // Out with the old
			
			delete_transient('eeSFL-1-FileListDirCheck');
		
			if(update_option('eeSFL_FREE_DB_Version', eeSFL_FREE_DB_Version)) { // NOTE - We changed to all underscores in option names in 4.3
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
	
	
	// New install or update from an old version, the older the better.
	
	$eeNewInstall = FALSE;
	
	$eeConfigDefault = $eeSFL_FREE->DefaultListSettings; // Get our default config
	
	// Look for previous versions
	$eeSFL_V2 = get_option('eeSFL');
	$eeSFL_V3 = get_option('eeSFL-1-ShowList');
	
	
	// Upgrade Simple File List ?
	if($eeSFL_V3) { // Updating from 3.x
		
		$eeSFL_FREE_Log['Updating'][] = 'Version 3.x Detected';
		
		// Get Existing Settings
		$eeShowList = get_option('eeSFL-1-ShowList');
		$eeShowFileThumb = get_option('eeSFL-1-ShowFileThumb');
		$eeShowFileDate = get_option('eeSFL-1-ShowFileDate');
		$eeShowFileOwner = get_option('eeSFL-1-ShowFileOwner');
		$eeShowFileSize = get_option('eeSFL-1-ShowFileSize');
		$eeSortBy = get_option('eeSFL-1-SortBy');
		$eeSortOrder = get_option('eeSFL-1-SortOrder');
		$eeShowFileActions = get_option('eeSFL-1-ShowFileActions');
		$eeShowHeader = get_option('eeSFL-1-ShowListHeader');
		$eeShowFileThumb = get_option('eeSFL-1-ShowFileThumb');
		$eeAllowFrontDelete = get_option('eeSFL-1-AllowFrontDelete');
		
		// Uploading
		$eeFileListDir = get_option('eeSFL-1-UploadDir'); // Now FileListDir
		$eeAllowUploads = get_option('eeSFL-1-AllowUploads');
		$eeFileFormats = get_option('eeSFL-1-FileFormats');
		$eeUploadLimit = get_option('eeSFL-1-UploadLimit');
		$eeUploadMaxFileSize = get_option('eeSFL-1-UploadMaxFileSize');
		$eeGetUploaderInfo = get_option('eeSFL-1-GetUploaderInfo');
	
	} elseif( $eeSFL_V2 ) { // Updating from 1.x or 2.x
		
		// SFL Version 1
		// eeAllowList=Yes|eeAllowUploads=Yes|ee_upload_max_filesize=64|eeFormats=jpg,jpeg,png,pdf,zip|eeAdminTo=name@email.com
		
		// SFL Version 2 added...
		// eeFileOwner=No|eeFileListDir=wp-content/uploads/simple-file-list|eeSortList=Name|eeSortOrder=|eeShowForm=Yes
		
		// Get the existing settings, so we can convert them.
		$eeSettings = explode('|', $eeSFL_V2);
		
		// Version 1 settings
		$eeSetting = @explode('=', $eeSettings[0]); // Show the File List
		if($eeSetting[1] != 'Yes') { $eeShowList = 'NO'; }
		
		$eeSetting = @explode('=', $eeSFL_Settings); // AllowUploads
		if($eeSetting[1] != 'Yes') { $eeAllowUploads = 'NO'; }
			else { $eeSFL_AllowUploads = 'YES'; }
		
		$eeSetting = @explode('=', $eeSettings[2]); // Upload Max File size
		if($eeSetting[1]) { $eeUploadMaxFileSize = $eeSetting[1]; } else { $eeUploadMaxFileSize = 8; }
		
		$eeSetting = @explode('=', $eeSettings[3]); // Formats
		if($eeSetting[1]) { $eeFileFormats = $eeSetting[1]; }
		
		$eeSetting = @explode('=', $eeSettings[4]); // TO Email
		if($eeSetting[1]) { $eeNotify = $eeSetting[1]; }
		
		
		if(count($eeSettings) > 5) { // Version 2 Additions
			
			$eeSFL_FREE_Log['Updating'][] = 'Version 2.x Detected';
			
			$eeSetting = @explode('=', $eeSettings[5]); // Track File Owner
			if(@$eeSetting[1] != 'Yes') { $eeTrackFileOwner = 'NO'; }
			
			$eeSetting = @explode('=', $eeSettings[6]); // Upload Dir
			if(@$eeSetting[1]) { $eeFileListDir = $eeSetting[1]; }
			
			$eeSetting = @explode('=', $eeSettings[7]); // Sort List By...
			if(@$eeSetting[1]) { $eeSortBy = $eeSetting[1]; }
			
			$eeSetting = @explode('=', $eeSettings[8]); // Sort order
			if(@$eeSetting[1]) { $eeSortOrder = $eeSetting[1]; }
			
			$eeSetting = @explode('=', $eeSettings[9]); // Show Uploader Info Form
			if(@$eeSetting[1] == 'Yes') { $eeGetUploaderInfo = 'YES'; } else { $eeGetUploaderInfo = 'NO'; }
	
		} else {
			
			$eeSFL_FREE_Log['Updating'][] = 'Version 1.x Detected';
		}	
	
	} else {
		
		$eeSFL_FREE_Log['Updating'][] = 'New Installation';
		$eeNewInstall = TRUE; 
	}
	
	
	// Name Changes
	if(@$eeAllowFrontDelete == 'YES') { $eeAllowFrontManage = 'YES'; } else { $eeAllowFrontManage = FALSE; }



	// Notification Changes
	$eeNotifyOld = get_option('eeSFL-Notify'); // Old way, no ID
	$eeNotifyNew = get_option('eeSFL-1-Notify'); // New way, with ID
	
	if( strpos($eeNotifyOld, '@') ) {
		$eeNotifyTo = $eeNotifyOld;
		$eeNotify = 'YES';
		// delete_option('eeSFL-Notify'); // Out with the old.
	} elseif( strpos($eeNotifyNew, '@') ) {
		$eeNotifyTo = $eeNotifyNew; // In with the new.
		$eeNotify = 'YES';
	} elseif(@$eeNotify) { // V2
		$eeNotifyTo = $eeNotify;
		$eeNotify = 'YES';
	} else {
		$eeNotify = $eeConfigDefault['Notify'];
		$eeNotifyTo = get_option('admin_email');
	}
	
	
	// Assign Default if No Value
	if(!@$eeShowList) { $eeShowList = $eeConfigDefault['ShowList']; }
	if(!@$eeShowFileThumb) { $eeShowFileThumb = $eeConfigDefault['ShowFileThumb']; }
	if(!@$eeShowFileDate) { $eeShowFileDate = $eeConfigDefault['ShowFileDate']; }
	if(!@$eeShowFileSize) { $eeShowFileSize = $eeConfigDefault['ShowFileSize']; }
	if(!@$eeShowFileActions) { $eeShowFileActions = $eeConfigDefault['ShowFileActions']; }
	if(!@$eeSortBy) { $eeSortBy = $eeConfigDefault['SortBy']; }
	if(!@$eeSortOrder) { $eeSortOrder = $eeConfigDefault['SortOrder']; }
	if(!@$eeShowHeader) { $eeShowHeader = $eeConfigDefault['ShowHeader']; }
	if(!@$eeAllowFrontManage) { $eeAllowFrontManage = $eeConfigDefault['AllowFrontManage']; }
	if(!@$eeFileFormats) { $eeFileFormats = $eeConfigDefault['FileFormats']; }
	if(!@$eeUploadLimit) { $eeUploadLimit = $eeConfigDefault['UploadLimit']; }
	if(!@$eeUploadMaxFileSize) { $eeUploadMaxFileSize = $eeSFL_FREE_Env['the_max_upload_size']; }
	if(!@$eeGetUploaderInfo) { $eeGetUploaderInfo = $eeConfigDefault['GetUploaderInfo']; }
	if(!@$eeAllowUploads) { $eeAllowUploads = $eeConfigDefault['AllowUploads']; }
	
	
	// The File List Directory ----------------
	
	// Create the File List Dir if Needed
	if(!@$eeFileListDir) {
		
		$eeFileListDir = $eeConfigDefault['FileListDir'];
		eeSFL_FREE_FileListDirCheck( $eeFileListDir ); // Create the File List Folder
	
	} else {
	
		// Check if FileListDir has a trailing slash...
		$eeLastChar = substr($eeSFL_FileListDir, -1);
		if($eeLastChar != '/') {  $eeFileListDir .= '/'; } // Add the slash, required for 3.1 +
		
		// Check if FileListDir has a leading slash
		if($eeFileListDir[0] == '/') {  $eeFileListDir = substr($eeFileListDir, 1); } // Omit the slash, required for 4 +
		
		eeSFL_FREE_FileListDirCheck( $eeFileListDir ); // Check the File List Folder
	}
	
	// Add First File
	if($eeNewInstall) { // Copy the instructions PDF to the file list folder
		
		$eeCopyFrom = dirname(__FILE__) . '/Simple-File-List.pdf';
		$eeCopyTo = ABSPATH . '/' . $eeFileListDir . 'Simple-File-List.pdf';
		copy($eeCopyFrom, $eeCopyTo);
	}
	
	
	// Create Settings Array --------------
	
	$eeSettings = array( // See $DefaultListSettings within ee-class.php for definitions
		
		'ListTitle' => 'Simple File List',
		'FileListDir' => $eeFileListDir,
		'ExpireTime' => $eeConfigDefault['ExpireTime'],
		'ShowList' => $eeShowList,
		'AdminRole' => $eeConfigDefault['AdminRole'],
		'ShowFileThumb' => $eeShowFileThumb,
		'ShowFileDate' => $eeShowFileDate,
		'ShowFileSize' => $eeShowFileSize,
		'SortBy' => $eeSortBy,
		'SortOrder' => $eeSortOrder,
		
		'LabelThumb' => $eeConfigDefault['LabelThumb'],
		'LabelName' => $eeConfigDefault['LabelName'],
		'LabelDate' => $eeConfigDefault['LabelDate'],
		'LabelSize' => $eeConfigDefault['LabelSize'],
		
		'AllowUploads' => $eeAllowUploads,
		'UploadLimit' => $eeUploadLimit,
		'UploadMaxFileSize' => $eeUploadMaxFileSize,
		'FileFormats' => $eeFileFormats,
		
		'PreserveSpaces' => $eeConfigDefault['PreserveSpaces'],
		'ShowFileDescription' => $eeConfigDefault['ShowFileDescription'],
		'ShowFileActions' => $eeShowFileActions,
		'ShowFileExtension' => $eeConfigDefault['ShowFileExtension'],
		'ShowHeader' => $eeShowHeader,
		'ShowUploadLimits' => $eeConfigDefault['ShowUploadLimits'],
		'GetUploaderInfo' => $eeGetUploaderInfo,
		'ShowSubmitterInfo' => $eeConfigDefault['ShowSubmitterInfo'],
		'AllowFrontSend' => $eeConfigDefault['AllowFrontSend'],
		'AllowFrontManage' => $eeAllowFrontManage,
		
		'Notify' => $eeNotify,
		'NotifyTo' => $eeNotifyTo,
		'NotifyCc' => '',
		'NotifyBcc' => '',
		'NotifyFrom' => get_option('admin_email'),
		'NotifyFromName' => '',
		'NotifySubject' => '',
		'NotifyMessage' => $eeSFL_FREE->eeNotifyMessageDefault
	);
	
	$eeSFL_FREE_Log['Updating'][] = $eeSettings;
	
	ksort($eeSettings); // Sort for sanity
	
	// Update the Option		
	update_option('eeSFL_Settings_1', $eeSettings);
	
	// Create first file list array
	$eeFilesArray = array();
	update_option('eeSFL_FileList_1', $eeFilesArray);	
	
	// Update the DB Version
	update_option('eeSFL_FREE_DB_Version', eeSFL_FREE_DB_Version);
	
	// TO DO in SFL 4.1 -- Delete ALL old options
	delete_option('eeSFL-Legacy'); // Don't need this anymore

	$eeSFL_FREE_Log['Updating'][] = 'Plugin Updated to database version: ' . eeSFL_FREE_DB_Version;
	
	// Write the log file to the Database
	$eeSFL_FREE->eeSFL_WriteLogData($eeSFL_FREE_Log);

}


// Plugin Activation ==========================================================
function eeSFL_FREE_Activate() {
	
	// TO DO - Check extension versions - Fail unless they are updated first.
	
	return TRUE; // All done, nothing to do here.	
}
register_activation_hook( __FILE__, 'eeSFL_FREE_Activate' );

?>