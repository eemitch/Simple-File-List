<?php

/**
 * @package Element Engage - Simple File List
 */
/*
Plugin Name: Simple File List
Plugin URI: http://simplefilelist.com
Description: A Basic File List Manager with File Uploader
Author: Mitchell Bennis
Version: 6.1.2
Author URI: http://simplefilelist.com
License: GPLv2 or later
Text Domain: ee-simple-file-list
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// CONSTANTS
define('eeSFL_BASE_DevMode', FALSE);
define('eeSFL_BASE_Version', '6.1.2'); // Plugin version
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
$eeSFL_BASE = FALSE; // Our Main Class
$eeSFLU_BASE = FALSE; // Our Upload Class
$eeSFL_BASE_VarsForJS = array(); // Strings to Pass to JavaScript

// Supported Extensions
$eeSFL_BASE_Extensions = array( // Slug => Required Version
	'ee-simple-file-list-media' => '1' // AV File Playback
);

// Extension Objects
$eeSFLM = FALSE; // Media Player

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
	
	global $eeSFL_BASE, $eeSFLU_BASE, $eeSFL_BASE_VarsForJS, $eeSFL_BASE_Extensions;
	
	// A required resource...
	if(!function_exists('is_plugin_active')) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
	}
	
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
		$eeSFL_Nonce = wp_create_nonce('eeSFL_Functions');
		include_once(plugin_dir_path(__FILE__) . 'includes/ee-functions.php');
		
		// Main Class
		$eeSFL_Nonce = wp_create_nonce('eeSFL_Class');
		require_once(plugin_dir_path(__FILE__) . 'includes/ee-class.php'); 
		$eeSFL_BASE = new eeSFL_BASE_MainClass();
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Simple File List is Loading...';
		
		// Upload Class
		$eeSFL_Nonce = wp_create_nonce('eeSFL_Class');
		require_once(plugin_dir_path(__FILE__) . 'uploader/ee-class-uploads.php'); 
		$eeSFLU_BASE = new eeSFL_BASE_UploadClass();
		
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
	
	// Language Setup
	if(isset($_POST['eeLangOptionSubmit'])) {
	
		if($_POST['eeLangOption'] == 'en_US') {
			update_option('eeSFL_Lang', 'en_US');
		} else {
			delete_option('eeSFL_Lang');
		}
	}
	
	$eeLocaleSetting = get_option('eeSFL_Lang');
	
	if(!is_admin() OR !$eeLocaleSetting OR $eeLocaleSetting != 'en_US') {
		eeSFL_BASE_Textdomain(); 
	}
	
	
	// Extensions
	if(isset($eeSFL_BASE_Extensions)) {
	
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Checking for Extensions ...';
	
		// Loop thru and set up
		foreach($eeSFL_BASE_Extensions as $eeSFL_Extension => $eeReqVersion) {
		
			if( is_plugin_active( $eeSFL_Extension . '/' . $eeSFL_Extension . '.php' ) ) { // Is the extension active?
		
				// Check for old plugins
				$eeVersionFile = WP_PLUGIN_DIR . '/' . $eeSFL_Extension . '/version.txt';
				
				if(file_exists($eeVersionFile)) {
					
					$eeVersion = file_get_contents($eeVersionFile);
					
					if(version_compare( $eeVersion , $eeReqVersion, '>=')) {
						
						$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['active'][] = $eeSFL_Extension; // Need this for later
				
						$eeSFL_Nonce = wp_create_nonce('eeSFL_Include'); // Used in all extension INI files
				
						include_once(WP_PLUGIN_DIR . '/' . $eeSFL_Extension . '/ee-ini.php'); // Run initialization
					
					} else {
					
						$eeERROR = '<strong>' . $eeSFL_Extension . ' &larr; ' . __('EXTENSION DISABLED', 'ee-simple-file-list') . '</strong><br />' . 
							__('Please go to Plugins and update the extension to the latest version.', 'ee-simple-file-list');
						
						if( is_admin() AND @$_GET['page'] == 'ee-simple-file-list') {
							$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'][] = $eeERROR;
						}
					}
					
				} else {
					
					continue; // Ignore really old extensions
				}	
			}
		}
	}
	
	
	
	
	
	
	return TRUE;
}
add_action('init', 'eeSFL_BASE_Setup');




// Shortcode
function eeSFL_BASE_FrontEnd($atts, $content = null) { // Shortcode Usage: [eeSFL]
	
	global $eeSFL_BASE, $eeSFLU_BASE, $eeSFL_BASE_VarsForJS;
    
    $eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Shortcode Function Loading ...';
	$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = $eeSFL_BASE->eeSFL_GetThisURL();
	
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
	
		$eeOutput .= '
		<!-- Shortcode: ' . $eeShortcode . ' List Run: #' . $eeSFL_BASE->eeListRun . ' -->';
		
		extract($atts);
		
		if($showlist) { $eeSFL_BASE->eeListSettings['ShowList'] = strtoupper($showlist); }
		if($style) { $eeSFL_BASE->eeListSettings['ShowListStyle'] = strtoupper($style); }
		if($theme) { $eeSFL_BASE->eeListSettings['ShowListTheme'] = strtoupper($theme); }
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
				$eeSFL_BASE->eeListSettings['SortBy'] = ucwords($sortby);
				$eeSFL_BASE->eeListSettings['SortOrder'] = ucwords($sortorder);
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
	$eeSFL_Uploaded = $eeSFLU_BASE->eeSFL_UploadCheck($eeSFL_BASE->eeListRun);
		
	
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
		wp_enqueue_script('ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'uploader/ee-uploader.js', $eeDependents , eeSFL_BASE_Version, TRUE);
		$eeSFL_UploadFormRun = TRUE;
		$eeShowUploadForm = TRUE;
	}
	
	if($eeSFL_BASE->eeListSettings['AllowUploads'] != 'NO' AND !$eeSFL_Uploaded AND $eeSFL_BASE->eeListSettings['UploadPosition'] == 'Above') {
		$eeOutput .= $eeSFLU_BASE->eeSFL_UploadForm();
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
		$eeOutput .= $eeSFLU_BASE->eeSFL_UploadForm();
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
	wp_register_script( 'ee-simple-file-list-js-edit-file', plugin_dir_url(__FILE__) . 'js/ee-edit-file.js' );
	wp_register_script( 'ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'uploader/ee-uploader.js' );
	
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
        wp_enqueue_script('ee-simple-file-list-js-uploader', plugin_dir_url(__FILE__) . 'uploader/ee-uploader.js',$deps, eeSFL_BASE_Version, TRUE);
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

	global $eeSFLU_BASE;
	
	$eeResult = $eeSFLU_BASE->eeSFL_FileUploader();

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
		if( strlen($_POST['eeFileName']) ) { $eeFileName = esc_textarea(sanitize_text_field($_POST['eeFileName'])); }
		if(!$eeFileName) { return "Missing the File Name"; }
		
		// Folder Path - PRO ONLY
		
		// Delete the File
		if($eeFileAction == 'Delete') {
			
			$eeSFL_BASE->eeSFL_DetectUpwardTraversal($eeSFL_BASE->eeListSettings['FileListDir'] . $eeFileName); // Die if foolishness
			
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
				$eeFileNiceNameNew = trim(esc_textarea(sanitize_text_field($_POST['eeFileNiceNameNew'])));
				if(!$eeFileNiceNameNew) { $eeFileNiceNameNew = ''; } 
				$eeSFL_BASE->eeSFL_UpdateFileDetail($eeFileName, 'FileNiceName', $eeFileNiceNameNew);
			}
			
			
			
			// The Description - Might be empty
			if($_POST['eeFileDescNew'] != 'false') {
			
				$eeFileDescriptionNew = trim(esc_textarea(sanitize_text_field($_POST['eeFileDescNew'])));
				
				if(!$eeFileDescriptionNew) { $eeFileDescriptionNew = ''; }
				
				$eeSFL_BASE->eeSFL_UpdateFileDetail($eeFileName, 'FileDescription', $eeFileDescriptionNew);
			}

			
			
			// Date Modified - PRO ONLY
		
			
			
			// New File Name? - Rename Last
			if( strlen($_POST['eeFileNameNew']) >= 3 ) { 
				
				$eeFileNameNew = sanitize_text_field($_POST['eeFileNameNew']);
				$eeFileNameNew  = urldecode( $eeFileNameNew );
				$eeFileNameNew  = $eeSFL_BASE->eeSFL_SanitizeFileName( $eeFileNameNew );
				
				if( strlen($eeFileNameNew) >= 3 ) { // a.b
				
					// Prevent changing file extension
					$eePathParts = pathinfo( $eeFileName );
					$eeOldExtension = strtolower( $eePathParts['extension'] ); 
					$eePathParts = pathinfo( $eeFileNameNew );
					$eeNewExtension = strtolower( $eePathParts['extension'] );
					if($eeOldExtension != $eeNewExtension) { return "Changing File Extensions is Not Allowed"; }
				
					// Die if foolishness
					$eeSFL_BASE->eeSFL_DetectUpwardTraversal($eeSFL_BASE->eeListSettings['FileListDir'] . $eeFileNameNew ); 
					
					// Check for Duplicate File
					$eeFileNameNew  = $eeSFL_BASE->eeSFL_CheckForDuplicateFile( $eeSFL_BASE->eeListSettings['FileListDir'] . $eeFileNameNew );
					
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
		
			// 6.1
			if($eeSettings['SortBy'] == 'Date') { $eeSettings['SortBy'] = 'Added'; }
			if($eeSettings['SortBy'] == 'DateMod') { $eeSettings['SortBy'] = 'Changed'; }
			
			// These are now uppercase
			$eeSettings['ShowListStyle'] = strtoupper($eeSettings['ShowListStyle']);
			$eeSettings['ShowListTheme'] = strtoupper($eeSettings['ShowListTheme']);
			
			// Check for problematic leading slash
			if(substr($eeSettings['FileListDir'], 0, 1) == '/') {
				$eeSettings['FileListDir'] = substr($eeSettings['FileListDir'], 1);
			}
			
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