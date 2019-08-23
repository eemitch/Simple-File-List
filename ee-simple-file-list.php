<?php

/**
 * @package Element Engage - Simple File List
 */
/*
Plugin Name: Simple File List 4
Plugin URI: http://simplefilelist.com
Description: Full Featured File List with Front-Side File Uploading | <a href="https://simplefilelist.com/donations/simple-file-list-project/">Donate</a> | <a href="admin.php?page=ee-simple-file-list&tab=extensions">Add Features</a>
Author: Mitchell Bennis - Element Engage, LLC
Version: 4.0.5
Author URI: http://elementengage.com
License: GPLv2 or later
Text Domain: ee-simple-file-list
Domain Path: /languages
*/

$eeSFL_DevMode = TRUE; // Enables visible logging

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// SFL Versions
define('eeSFL_Version', '4.0.5'); // Plugin version - DON'T FORGET TO UPDATE ABOVE TOO !!!
define('eeSFL_DB_Version', '4.0.1'); // Database structure version - used for eeSFL_VersionCheck()
define('eeSFL_Cache_Version', '.1'); // Cache-Buster version for static files - used when updating CSS/JS

// Our Core
$eeSFL = FALSE; // Our main class
$eeSFL_Config = array(); // Database Info
$eeSFL_Env = array(); // Environment
$eeListNumber = 1; // Count of lists per page

// The Log - Written to wp_option -> eeSFL-Log
$eeSFL_Log = array('Simple File List is Loading...');
$eeSFL_Log[] = 'ABSPATH: ' . ABSPATH;
// Format: [] => 'log entry'
//	['messages'][] = 'Message to the user'
//	['errors'][] = 'Error condition to user'

// Supported Extensions
$eeSFL_Extensions = array( // Slugs
	'ee-simple-file-list-folders' // Folder Support
	,'ee-simple-file-list-search' // Search & Pagination
);
$eeSFLF = FALSE; $eeSFLS = FALSE; $eeSFLU = FALSE; // Coming Soon
$eeSFLF_ListFolder = FALSE;


// Plugin Setup
function eeSFL_Setup() {
	
	global $eeSFL, $eeSFL_Extensions, $eeSFL_Log, $eeSFL_Config, $eeSFL_Env;
	
	$eeSFL_Log[] = 'Running eeSFL_Setup...';

	// Get Functions
	$eeSFL_Nonce = wp_create_nonce('eeSFL_Functions'); // Security
	include_once(plugin_dir_path(__FILE__) . 'includes/ee-functions.php'); // Our Functions
	
	// Get Class
	if(!class_exists('eeSFL')) {
		$eeSFL_Nonce = wp_create_nonce('eeSFL_Class'); // Security
		require_once(plugin_dir_path(__FILE__) . 'includes/ee-class.php'); // Get the main class file
		$eeSFL = new eeSFL_MainClass(); // Initiate the SFL Class
		$eeSFL_Env = $eeSFL->eeSFL_GetEnv(); // Get the Environment Array
		$eeSFL_Config = $eeSFL->eeSFL_Config($eeSFL_Env, $eeSFL->eeListID); // Get the Configuration Array	
	}
	
	
	
	// If Sending Files
	if(@$_POST['eeSFL_Send']) { $eeSFL->eeSFL_SendFilesEmail($_POST); }
	
	
	
	wp_die('Fix Sorting in Class File');  // exit();
	
	
	
	
	eeSFL_VersionCheck(); // Update database if needed.
	
	if( !eeSFL_FileListDirCheck( $eeSFL_Config['FileListDir'] ) ) { // Check/Create the Upload Folder
		wp_die('The upload directory is acting up.', 'Error');
	}
	
	// Extension Checks ------------------------
	
	// A required resource...
	if(!function_exists('is_plugin_active')) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
	}
	
	// Legacy Support
	$eeSFL_UploadDir = $eeSFL_Config['FileListDir'];
	$eeSFL_UploadURL = $eeSFL_Config['FileListURL'];
	
	$eeSFL_Log[] = 'Checking for Extensions...';
	
	// Loop thru and set up
	foreach($eeSFL_Extensions as $key => $eeSFL_Extension) {
	
		if(is_plugin_active( $eeSFL_Extension . '/' . $eeSFL_Extension . '.php' )) { // Is the plugin active?
	
			if(file_exists(WP_PLUGIN_DIR . '/' . $eeSFL_Extension . '/ee-ini.php')) {
			
				$eeSFL_Log['extensions'] = $eeSFL_Extension;
				
				$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include');
				$eeSFL_Nonce = wp_create_nonce('eeSFL_Include');
				
				include_once(WP_PLUGIN_DIR . '/' . $eeSFL_Extension . '/ee-ini.php'); // Run initialization
			}
		}
	}
	
	eeSFL_Textdomain(); // Language Setup
	
	return TRUE;
}
add_action('init', 'eeSFL_Setup');


// Custom Hook
function eeSFL_UploadCompleted() {
    do_action('eeSFL_UploadCompleted');
}

// Log Failed Emails
function eeSFL_action_wp_mail_failed($wp_error) {
    return error_log(print_r($wp_error, true));
}
add_action('wp_mail_failed', 'eeSFL_action_wp_mail_failed', 10, 1);



// Language Enabler
function eeSFL_Textdomain() {
    load_plugin_textdomain( 'ee-simple-file-list', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}


// Createing a New Post with Shortcode
function eeSFL_CreatePostwithShortcode() { 
	
	global $eeSFL_Log;
	
	$eeShortcode = FALSE;
	$eeCreatePostType = FALSE;
	$eeCreatePostType = filter_var(@$_POST['eeCreatePostType'], FILTER_SANITIZE_STRING);
	$eeShortcode = filter_var(@$_POST['eeShortcode'], FILTER_SANITIZE_STRING);
		
	if(($eeCreatePostType == "Post" OR $eeCreatePostType == "Page") AND $eeShortcode) {
		
		// Create Post Object
		$eeNewPost = array(
			'post_type'		=> $eeCreatePostType,
			'post_title'    => 'My Simple File List ' . $eeCreatePostType,
			'post_content'  => '<p><em>Note that this ' . $eeCreatePostType . ' is in draft status</em></p><div>' . $eeShortcode . '</div>',
			'post_status'   => 'draft'
		);
 
		// Create Post
		$eeNewPostID = wp_insert_post( $eeNewPost );
		
		if($eeNewPostID) {
			
			$eeSFL_Log['p=' . $eeNewPostID][] = 'Creating new ' . $eeCreatePostType . ' with shortcode...';
			$eeSFL_Log['p=' . $eeNewPostID][] = $eeShortcode;
			
			header('Location: /?p=' . $eeNewPostID);
		}
		
		return TRUE;
	}
	
}
add_action( 'wp_loaded', 'eeSFL_CreatePostwithShortcode' );

// Page Output ============================================


// Shortcode
function eeSFL_Shortcode($atts, $content = null) {
	
	// Usage: [eeSFL]
    
    global $eeSFL, $eeSFL_DevMode, $eeSFL_Log, $eeSFL_Env, $eeSFL_Config, $eeListNumber; // Number of the list on same page
    global $eeSFLF, $eeSFLS; // Extensions
	
	$eeAdmin = is_admin(); // Better be FALSE
	
	$eeSFL_Log['L' . $eeListNumber][] = 'Shortcode Loading: ' . get_permalink();
    
    $eeSFL_Nonce = wp_create_nonce('eeInclude'); // Checked on the included pages

	if(!is_array($eeSFL_Config)) { return FALSE; }


    // Over-Riding Shortcode Attributes
	if($atts) {
	
		$atts = shortcode_atts( array( // Use lowercase att names only
			'showlist' => $eeSFL_Config['ShowList'], // defaults to DB settings
			'allowuploads' => $eeSFL_Config['AllowUploads'],
			'showthumb' => $eeSFL_Config['ShowThumb'],
			'showdate' => $eeSFL_Config['ShowFileDate'],
			'showsize' => $eeSFL_Config['ShowFileSize'],
			'showheader' => $eeSFL_Config['ShowListHeader'],
			'showactions' => $eeSFL_Config['ShowFileActions'],
			'showfolder' => '',
			'id' => ''
		), $atts );
		
		extract($atts);
	
		$eeSFL_Log['L' . $eeListNumber][] = 'Shortcode Attributes...';
		
		$eeSFL_Config['ShowList'] = $showlist;
		$eeSFL_Config['AllowUploads'] = $allowuploads;
		$eeSFL_Config['ShowFileThumb'] = $showthumb;
		$eeSFL_Config['ShowFileDate'] = $showdate;
		$eeSFL_Config['ShowFileSize'] = $showsize;
		$eeSFL_Config['ShowHeader'] = $showheader;
		$eeSFL_Config['ShowFileActions'] = $showactions;
		$eeSFLF_ShortcodeFolder = $showfolder;
		
		if($eeSFL_Config['ShowList'] != $showlist) { $eeSFL_Log['L' . $eeListNumber][] = 'showlist: ' . $showlist; }
		if($eeSFL_Config['AllowUploads'] != $allowuploads) { $eeSFL_Log['L' . $eeListNumber][] = 'allowuploads: ' . $allowuploads; }
		if($eeSFL_Config['ShowThumb'] != $showthumb) { $eeSFL_Log['L' . $eeListNumber][] = 'showthumb: ' . $showthumb; }
		if($eeSFL_Config['ShowFileDate'] != $showdate) { $eeSFL_Log['L' . $eeListNumber][] = 'showdate: ' . $showdate; }
		if($eeSFL_Config['ShowFileSize'] != $showsize) { $eeSFL_Log['L' . $eeListNumber][] = 'showsize: ' . $showsize; }
		if($eeSFL_Config['ShowHeader'] != $showheader) { $eeSFL_Log['L' . $eeListNumber][] = 'showheader: ' . $showheader; }
		if($eeSFL_Config['ShowFileActions'] != $showactions) { $eeSFL_Log['L' . $eeListNumber][] = 'showactions: ' . $showactions; }
		if($showfolder) { $eeSFL_Log['L' . $eeListNumber][] = 'showfolder: ' . $showfolder; }
		
	
	} else {
		$eeSFL_Log['L' . $eeListNumber][] = 'No Shortcode Attributes';
	}
	
	
	// Begin Front-Side List Display ==================================================================
	
	$eeOutput = '<div id="eeSFL">';
	// $eeSFL_Log['L' . $eeListNumber][] = 'Begin Frontside Output Buffer...';
	
	// Who Can Upload?
	switch ($eeSFL_Config['AllowUploads']) {
	    case 'YES':
	        break; // Show It
	    case 'USER':
	        // Show It If...
	        if( get_current_user_id() ) { break; } else { $eeSFL_Config['AllowUploads'] = 'NO'; }
	    case 'ADMIN':
	        // Show It If...
	        if(current_user_can('manage_options')) { break; } else { $eeSFL_Config['AllowUploads'] = 'NO'; }
	        break;
		default:
			$eeSFL_Config['AllowUploads'] = 'NO'; // Show Nothing
	}
	
	if($eeSFL_Config['AllowUploads'] != 'NO' AND $eeListNumber == 1 AND !@$_POST['eeSFLS_Searching']) {
		include(WP_PLUGIN_DIR . '/' . $eeSFL->eePluginNameSlug . '/includes/ee-upload-form.php');
	}
	
	// Who Can View the List?
	switch ($eeSFL_Config['ShowList']) {
	    case 'YES':
	        break; // Show It
	    case 'USER':
	        // Show It If...
	        if( get_current_user_id() ) { break; } else { $eeSFL_Config['ShowList'] = 'NO'; }
	    case 'ADMIN':
	        // Show It If...
	        if(current_user_can('manage_options')) { break; } else { $eeSFL_Config['ShowList'] = 'NO'; }
	        break;
		default:
			$eeSFL_Config['ShowList'] = 'NO'; // Show Nothing
	}
	if($eeSFL_Config['ShowList'] != 'NO') {
		
		include(WP_PLUGIN_DIR . '/' . $eeSFL->eePluginNameSlug . '/ee-list-display.php');
	}
	
	$eeOutput .= '</div>';
	
	$eeListNumber++;
	
	if(@$_REQUEST) {
		array_unshift($eeSFL_Log, $_REQUEST);
	}

	// Timer
	$eeSFL_Time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	$eeSFL_Log[] = 'Execution Time: ' . round($eeSFL_Time,3);
	
	// Logging
	$eeSFL->eeSFL_WriteLogData($eeSFL_Log);
	if($eeSFL_DevMode) {
		$eeOutput .= '<pre id="eeSFL_DevMode">Log File ' . print_r($eeSFL_Log, TRUE) . '</pre>';
	}
	
	return $eeOutput; // Output the page
}
add_shortcode( 'eeSFL', 'eeSFL_Shortcode' );




// Load Front-side <head>
function eeSFL_Enqueue() {
	
	// Register the style like this for a theme:
    wp_register_style( 'ee-simple-file-list-css', plugin_dir_url(__FILE__) . 'css/eeStyles.css', '', eeSFL_Cache_Version);
 
    // Enqueue the style:
    wp_enqueue_style('ee-simple-file-list-css');
	
	// Now with Javascript !
	$deps = array('jquery');
	wp_enqueue_script('ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js',$deps,eeSFL_Cache_Version,FALSE); // Head
	wp_enqueue_script('ee-simple-file-list-js-foot', plugin_dir_url(__FILE__) . 'js/ee-footer.js',$deps,eeSFL_Cache_Version,TRUE); // Footer
}
add_action( 'wp_enqueue_scripts', 'eeSFL_Enqueue' );





// Admin <head>
function eeSFL_AdminHead($eeHook) {
	
	global $eeCacheBuster;
        
    // wp_die($eeHook); // Use this to discover the hook for each page
    
    // https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
    
    $deps = array('jquery');
    
    $eeHooks = array(
    	'toplevel_page_ee-simple-file-list',
    	'simple-file-list_page_ee-simple-file-list',
    	'simple-file-list_page_ee-simple-file-list-settings'
    );
    
    if(in_array($eeHook, $eeHooks)) {
        wp_enqueue_style( 'ee-simple-file-list-css-front', plugins_url('css/eeStyles.css', __FILE__), '', eeSFL_Cache_Version );
        wp_enqueue_style( 'ee-simple-file-list-css-back', plugins_url('css/eeStyles-Back.css', __FILE__), '', eeSFL_Cache_Version );
        
        // Now with Javascript !
        wp_enqueue_script('ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/ee-head.js',$deps,eeSFL_Cache_Version,FALSE);
		wp_enqueue_script('ee-simple-file-list-js-back', plugin_dir_url(__FILE__) . 'js/ee-back.js',$deps,eeSFL_Cache_Version,FALSE);
        wp_enqueue_script('ee-simple-file-list-js-footer', plugin_dir_url(__FILE__) . 'js/ee-footer.js',$deps,eeSFL_Cache_Version,TRUE);
		
		wp_localize_script('ee-simple-file-list-js-head', 'eeSFL_JS', array( 'pluginsUrl' => plugins_url() ) );
    }
        
        
}
add_action('admin_enqueue_scripts', 'eeSFL_AdminHead');





// Add Action Links to the Plugins Page
function eeSFL_actionPluginLinks( $links ) {
	
	$eeLinks = array(
		'<a href="' . admin_url( 'admin.php?page=ee-simple-file-list' ) . '">' . __('File List', 'ee-simple-file-list') . '</a>',
		'<a href="' . admin_url( 'admin.php?page=ee-simple-file-list&tab=list_settings&subtab=uploader_settings' ) . '">' . __('Settings', 'ee-simple-file-list') . '</a>'
	);
	return array_merge( $links, $eeLinks );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'eeSFL_actionPluginLinks' );






// Admin Pages
function eeSFL_AdminMenu() {
	
	global $eeSFL, $eeSFL_Env, $eeSFL_Log; // , $eeSFL_DevMode, $eeSFL_Config;
	// global $eeSFLF, $eeSFLU, $eeSFLS, $eeEXT; // Extensions
	
	$eeOutput = '<!-- Simple File List Admin -->';
	$eeSFL_Log[] = 'Admin Menu Loading ...';
	
	$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
	include_once(WP_PLUGIN_DIR . '/' . $eeSFL->eePluginNameSlug . '/ee-admin-page.php'); // Admin's List Management Page
	
	// The Admin Menu
	add_menu_page(
		__($eeSFL->eePluginName, $eeSFL->eePluginSlug), // Page Title
		__($eeSFL->eePluginName, $eeSFL->eePluginSlug), // Menu Title
		'edit_posts', // User status reguired to see the menu
		'ee-simple-file-list', // Slug
		'eeSFL_ManageLists', // Function that displays the menu page
		'dashicons-index-card' // Icon used
	);
	
	// User Manager
	if(@$eeSFLU) { 
		
		$eeNonce = wp_create_nonce('eeSFLU'); // Security
		include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-users/includes/eeManager.php');
		
		add_submenu_page(
		'ee-simple-file-list', 
		__('User Manager', 'ee-simple-file-list-users'), 
		__('User Manager', 'ee-simple-file-list-users'),  
		'edit_users', 
		'ee-simple-file-list-users', 
		'eeSFLU_Manager'
		);
	}
}
add_action( 'admin_menu', 'eeSFL_AdminMenu' );







// Plugin Version Check
//   We only run the update function if there has been a change in the database structure.
function eeSFL_VersionCheck() { 
		
	global $eeSFL_Log;
	
	$eeSFL_DB_VersionInstalled = get_option('eeSFL-Version'); // We store the DB version in the DB, okay?
	
	if($eeSFL_DB_VersionInstalled < eeSFL_DB_Version OR !get_option('eeSFL-Settings') ) {
		
		eeSFL_UpdateThisPlugin(); // Run the DB update process
		$eeSFL_Log[] = '--> Updating Database: ' . $eeSFL_DB_VersionInstalled . ' to ' . eeSFL_DB_Version;

	}
}

// Perform DB Update
function eeSFL_UpdateThisPlugin() {
	
	global $eeSFL, $eeSFL_Log;
	
	$eeArray = $eeSFL->DefaultListSettings; // Shorten the name
	
	// Look for previous versions
	$eeSFL_V2 = get_option('eeSFL');
	$eeSFL_V3 = get_option('eeSFL-1-ShowList');
	
	// Upgrade Simple File List ?
	if($eeSFL_V3) { // Updating from 3.x
		
		// Get Existing Settings
		$eeSFL_ShowList = get_option('eeSFL-1-ShowList');
		$eeSFL_ShowFileThumb = get_option('eeSFL-1-ShowFileThumb');
		$eeSFL_ShowFileDate = get_option('eeSFL-1-ShowFileDate');
		$eeSFL_ShowFileOwner = get_option('eeSFL-1-ShowFileOwner');
		$eeSFL_ShowFileSize = get_option('eeSFL-1-ShowFileSize');
		$eeSFL_SortBy = get_option('eeSFL-1-SortBy');
		$eeSFL_SortOrder = get_option('eeSFL-1-SortOrder');
		$eeSFL_ShowFileActions = get_option('eeSFL-1-ShowFileActions');
		$eeSFL_ShowHeader = get_option('eeSFL-1-ShowListHeader');
		$eeSFL_ShowFileThumb = get_option('eeSFL-1-ShowFileThumb');
		$eeSFL_AllowFrontManage = get_option('eeSFL-1-AllowFrontManage');
		
		// Uploading
		$eeSFL_UploadDir = get_option('eeSFL-1-UploadDir'); // Now FileListDir
		$eeSFL_AllowUploads = get_option('eeSFL-1-AllowUploads');
		$eeSFL_FileFormats = get_option('eeSFL-1-FileFormats');
		$eeSFL_UploadLimit = get_option('eeSFL-1-UploadLimit');
		$eeSFL_UploadMaxFileSize = get_option('eeSFL-1-UploadMaxFileSize');
		$eeSFL_GetUploaderInfo = get_option('eeSFL-1-GetUploaderInfo');
		// $eeSFL_TrackFileOwner = get_option('eeSFL-1-TrackFileOwner');
	
	
	} elseif( $eeSFL_V2 ) { // Updating from 1.x or 2.x
		
		
		// SFL Version 1
		// eeAllowList=Yes|eeAllowUploads=Yes|ee_upload_max_filesize=64|eeFormats=jpg,jpeg,png,pdf,zip|eeAdminTo=name@email.com
		
		// SFL Version 2 added...
		// eeFileOwner=No|eeFileListDir=wp-content/uploads/simple-file-list|eeSortList=Name|eeSortOrder=|eeShowForm=Yes
		
		// Get the existing settings, so we can convert them.
		$eeSettings = explode('|', $eeSFL_V2);
		
		// Version 1 settings
		$eeSetting = @explode('=', $eeSettings[0]); // Show the File List
		if($eeSetting[1] != 'Yes') { $eeSFL_ShowList = 'NO'; }
		
		$eeSetting = @explode('=', $eeSettings[1]); // AllowUploads
		if($eeSetting[1] != 'Yes') { $eeSFL_AllowUploads = 'NO'; }
			else { $eeSFL_AllowUploads = 'YES'; }
		
		$eeSetting = @explode('=', $eeSettings[2]); // Upload Max File size
		if($eeSetting[1]) { $eeSFL_UploadMaxFileSize = $eeSetting[1]; } else { $eeSFL_UploadMaxFileSize = 8; }
		
		$eeSetting = @explode('=', $eeSettings[3]); // Formats
		if($eeSetting[1]) { $eeSFL_FileFormats = $eeSetting[1]; }
		
		$eeSetting = @explode('=', $eeSettings[4]); // TO Email
		if($eeSetting[1]) { $eeSFL_Notify = $eeSetting[1]; }
		
		
		if(count($eeSettings) > 5) { // Version 2 Additions
			
			$eeSetting = @explode('=', $eeSettings[5]); // Track File Owner
			if(@$eeSetting[1] != 'Yes') { $eeSFL_TrackFileOwner = 'NO'; }
			
			$eeSetting = @explode('=', $eeSettings[6]); // Upload Dir
			if(@$eeSetting[1]) { $eeSFL_FileListDir = $eeSetting[1]; }
			
			$eeSetting = @explode('=', $eeSettings[7]); // Sort List By...
			if(@$eeSetting[1]) { $eeSFL_SortBy = $eeSetting[1]; }
			
			$eeSetting = @explode('=', $eeSettings[8]); // Sort order
			if(@$eeSetting[1]) { $eeSFL_SortOrder = $eeSetting[1]; }
			
			$eeSetting = @explode('=', $eeSettings[9]); // Show Uploader Info Form
			if(@$eeSetting[1] != 'Yes') { $eeSFL_GetUploaderInfo = 'NO'; }
	
		}	
	
		// Check if FileListDir has a trailing slash...
		$eeLastChar = substr($eeSFL_FileListDir, -1);
		if($eeLastChar != '/') {  $eeSFL_FileListDir .= '/'; } // Add the slash, required for 3.1 +

	}
	
	// Name Changes
	if($eeSFL_UploadDir) { $eeSFL_FileListDir = $eeSFL_UploadDir; } else { $eeSFL_FileListDir = FALSE; }
	if($eeSFL_AllowFrontManage == 'YES') { $eeSFL_AllowFrontManage = 'YES'; } else { $eeSFL_AllowFrontManage = 'NO'; }

	$eeNotifyOld = get_option('eeSFL-Notify'); // Old way, no ID
	$eeNotifyNew = get_option('eeSFL-1-Notify'); // New way, with ID
	if($eeNotifyOld) {
		$eeSFL_Notify = $eeNotifyOld;
		delete_option('eeSFL-Notify'); // Out with the old.
	} elseif($eeNotifyNew) {
		$eeSFL_Notify = $eeNotifyNew; // In with the new.
	}
	
	delete_option('eeSFL-Legacy'); // Don't need this anymore
	
	// Assign Default if No Value
	if(!$eeSFL_ShowList) { $eeSFL_ShowList = $eeArray['ShowList']; }
	if(!$eeSFL_ShowFileThumb) { $eeSFL_ShowFileThumb = $eeArray['ShowFileThumb']; }
	if(!$eeSFL_ShowFileDate) { $eeSFL_ShowFileDate = $eeArray['ShowFileDate']; }
	if(!$eeSFL_ShowFileSize) { $eeSFL_ShowFileSize = $eeArray['ShowFileSize']; }
	if(!$eeSFL_ShowFileActions) { $eeSFL_ShowFileActions = $eeArray['ShowFileActions']; }
	if(!$eeSFL_SortBy) { $eeSFL_SortBy = $eeArray['SortBy']; }
	if(!$eeSFL_SortOrder) { $eeSFL_SortOrder = $eeArray['SortOrder']; }
	if(!$eeSFL_ShowHeader) { $eeSFL_ShowHeader = $eeArray['ShowHeader']; }
	if(!$eeSFL_AllowFrontManage) { $eeSFL_AllowFrontManage = $eeArray['AllowFrontManage']; }
	
	// Uploading
	if(!$eeSFL_AllowUploads) { $eeSFL_AllowUploads = $eeArray['AllowUploads']; }
	
	// Create the File List Dir if Needed
	if(!$eeSFL_FileListDir) {
		$wp_UploadDirArray = wp_upload_dir(); // The Wordpress Upload Location
		$wp_UploadDir = $wp_UploadDirArray['basedir']; // Get the full directory path
		$wp_UploadDir  = str_replace(ABSPATH, '/', $wp_UploadDir) . '/'; // Make relative to WP root for saving
		$eeSFL_FileListDir = $wp_UploadDir . 'simple-file-list/'; // The default upload location, relative to WP home dir
		eeSFL_FileListDirCheck(ABSPATH . $eeSFL_FileListDir); // Check/Create the Upload Folder
	}
	
	// Upload Settings
	if(!$eeSFL_FileFormats) { $eeSFL_FileFormats = $eeArray['FileFormats']; }
	if(!$eeSFL_UploadLimit) { $eeSFL_UploadLimit = $eeArray['UploadLimit']; }
	if(!$eeSFL_UploadMaxFileSize) { $eeSFL_UploadMaxFileSize = substr(ini_get('upload_max_filesize'), 0, -1); }
	if(!$eeSFL_GetUploaderInfo) { $eeSFL_GetUploaderInfo = $eeArray['GetUploaderInfo']; }
	if(!$eeSFL_Notify) { $eeSFL_Notify = get_option('admin_email'); }
	
	// Create Settings Array
	$eeArray = array( // An array of file list settings arrays
		
		1 => array(
			'ListTitle' => 'Simple File List', // List Title
			'FileListDir' => $eeSFL_FileListDir, // List Directory Name (just that)
			'ShowList' => $eeSFL_ShowList, // Show the File List (YES, ADMIN, USER, NO)
			'ShowFileNiceName' => 'NO', // Display the File's Nice Name (YES or NO)
			'ShowFileThumb' => $eeSFL_ShowFileThumb, // Display the File Thumbnail Column (YES or NO)
			'ShowFileDate' => $eeSFL_ShowFileDate, // Display the File Date Column (YES or NO)
			'ShowFileSize' => $eeSFL_ShowFileSize, // Display the File Size Column (YES or NO)
			'ShowFileDescription' => 'YES', // Display the File Description (YES or NO)
			'ShowFileActions' => $eeSFL_ShowFileActions, // Display the File Action Links Section (below each file name) (YES or NO)
			'ShowHeader' => $eeSFL_ShowHeader, // Show the File List's Table Header (YES or NO)
			'SortBy' => $eeSFL_SortBy, // Sort By (NAME, DATE, SIZE, RANDOM)
			'SortOrder' => $eeSFL_SortOrder, // Descending or Ascending
			'AllowFrontManage' => $eeSFL_AllowFrontManage, // Allow front-side users to manage files (YES or NO)
			'AllowUploads' => $eeSFL_AllowUploads, // Allow File Uploads (YES, ADMIN, USER, NO)
			'UploadLimit' => $eeSFL_UploadLimit, // Limit Files Per Upload Job (Quantity)
			'UploadMaxFileSize' => $eeSFL_UploadMaxFileSize, // Maximum Size per File (MB)
			'FileFormats' => $eeSFL_FileFormats, // Allowed Formats
			'GetUploaderInfo' => $eeSFL_GetUploaderInfo, // Show the Info Form
			'Notify' => $eeSFL_Notify, // Send Upload Nitification Email Here
			'Updated' => date('Y-m-d H:i:s') // Time/Date of Last File Upload
		)
	);
	
	// Update the Option		
	update_option('eeSFL-Settings', $eeArray);	
	
	// Update the Version
	update_option('eeSFL-Version', eeSFL_DB_Version);
	
	// TO DO
	
	// Delete old options
	
	
	$eeSFL_Log[] = 'Plugin Updated to database version: ' . eeSFL_DB_Version;
	
	
}


// Plugin Activation ==========================================================

function eeSFL_Activate() {
	
	// TO DO - Ceck extension versions - Fail unless they are updated first.
	
	return TRUE; // All done, nothing to do here.	
}
register_activation_hook( __FILE__, 'eeSFL_Activate' );

?>