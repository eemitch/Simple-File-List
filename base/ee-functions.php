<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');

// Plugin Setup
function eeSFL_Setup() {
	
	global $eeSFL, $eeSFL_Environment, $eeSFL_Upload, $eeSFL_Thumbs, $eeSFL_Messaging, $eeSFL_VarsForJS;
	global $eeSFL_Pro, $eeSFLS, $eeSFLA, $eeSFLE, $eeSFL_Extensions;
	
	// A required resource...
	if(!function_exists('is_plugin_active')) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
	}
	
	// Deactivate the Free version if Pro
	if(defined('eeSFL_Pro')) { 
		$eePlugin = 'simple-file-list/ee-simple-file-list.php';
		if( is_plugin_active($eePlugin) ) { deactivate_plugins($eePlugin); }
	}
	
	
	// Get Class
	if(!class_exists('eeSFL_MainClass')) {
		
		$eeSFL_Include = wp_create_nonce(eeSFL_Include);
		
		// Main Class
		require_once(plugin_dir_path(__FILE__) . '/ee-class.php'); 
		$eeSFL = new eeSFL_MainClass();
		
		// Establish the current URL string
		$eeSFL->eeURL = $eeSFL->eeSFL_GetThisURL(); 
		
		// Initialize the Log
		$eeSFL->eeSFL_StartTime = round( microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 3); // Starting Time
		$eeSFL->eeSFL_StartMemory = memory_get_usage(); // This is where things start happening
		$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Simple File List is Loading...';
		
		// Load Supporting Classes ------------------------
		
		// Environment Class			
		require_once(plugin_dir_path(__FILE__) . 'ee-class-environment.php');
		$eeSFL_Environment = new eeSFL_Environment($eeSFL);
		
		// The WordPress ROOT - BETA
		$eeRootPath = $eeSFL_Environment->eeSFL_GetRootPath(); // Sets the Constant eeSFL_ABSPATH
		
		// Thumbnail Class			
		require_once(plugin_dir_path(__FILE__) . 'ee-class-thumbs.php');
		$eeSFL_Thumbs = new eeSFL_Thumbnails($eeSFL);
		
		// Upload Class
		require_once(plugin_dir_path(__FILE__) . 'ee-class-uploads.php'); 
		$eeSFL_Upload = new eeSFL_Uploads($eeSFL);
		
		// Messaging Class
		require_once(plugin_dir_path(__FILE__) . 'ee-class-messaging.php');
		$eeSFL_Messaging = new eeSFL_Messaging($eeSFL);
		
		// Pro Class
		if(defined('eeSFL_Pro')) {			
			require_once(plugin_dir_path(__FILE__) . '../pro/ee-functions-pro.php');
			require_once(plugin_dir_path(__FILE__) . '../pro/support/ee-support.php');
			require_once(plugin_dir_path(__FILE__) . '../pro/ee-class-pro.php');
			$eeSFL_Pro = new eeSFL_ProClass($eeSFL);
			if( isset($_REQUEST['eeListID']) ) {
				$eeSFL->eeListID = filter_var($_REQUEST['eeListID'], FILTER_VALIDATE_INT);
				if( !is_numeric($eeSFL->eeListID) ) { $eeSFL->eeListID = 1; }
			}
			$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Loading List #' . $eeSFL->eeListID;	
		}
		
		// Populate the Settings Array
		$eeSFL->eeSFL_GetSettings($eeSFL->eeListID);
		
		// Populate the Environment Array
		$eeSFL_Environment->eeSFL_ScanEnvironment();
		
		// echo '<pre>'; print_r($eeSFL->eeListSettings); echo '</pre>';
		// echo '<pre>'; print_r($eeSFL->eeLog); echo '</pre>'; exit;
		
		// Extension Initialization
		if(defined('eeSFL_Pro')) { eeSFL_InitializeExtensions(); }
		
		// Language Setup
		if(!is_admin() OR !$eeSFL->eeLocaleSetting OR $eeSFL->eeLocaleSetting != 'en_US') {
			eeSFL_Textdomain();
			if($eeSFLS) { eeSFLS_Textdomain(); }
			if($eeSFLA) { eeSFLA_Textdomain(); }
			if($eeSFLE) { eeSFLE_Textdomain(); }
			if($eeSFL) { eeSFL_Textdomain(); }
		}
		
		// Extension Check
		if( $eeSFLE AND isset($_POST['eeSFLE_Send']) ) { $eeSFLE->eeSFLE_SendFilesEmail(); } // Sending Files
		
		// Install or Update if Needed.
		if( is_admin() ) { eeSFL_VersionCheck(); }
		
		
		// Translation strings to pass to javascript as eesfl_vars
		$eeProtocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
		
		$eeSFL_VarsForJS = array(
			'ajaxurl' => admin_url( 'admin-ajax.php', $eeProtocol ), // AJAX
			
			'eeCopyLinkText' => __('The Link Has Been Copied', 'ee-simple-file-list'),
			
			// Item Editing
			'eeEditText' => __('Edit', 'ee-simple-file-list'), // Edit link text
			'eeConfirmDeleteText' => __('Are you sure you want to delete this?', 'ee-simple-file-list'), // Delete confirmation
			'eeCancelText' => __('Cancel', 'ee-simple-file-list'),
			
			// File Uploading
			'eeUploadLimitText' => __('Upload Limit', 'ee-simple-file-list'),
			'eeFileTooLargeText' => __('This file is too large', 'ee-simple-file-list'),
			'eeFileNoSizeText' => __('This file is empty', 'ee-simple-file-list'),
			'eeFileNotAllowedText' => __('This file type is not allowed', 'ee-simple-file-list'),
			'eeUploadErrorText' => __('Upload Failed', 'ee-simple-file-list'),
			'eePleaseWaitText' => __('Please Wait', 'ee-simple-file-list'),
			'eeFilesSelected' =>  __('Files Selected', 'ee-simple-file-list'),
			
			// Media Player
			'eePlayLabel' => __('Play', 'ee-simple-file-list-media'),
			'eeBrowserWarning' => __('Browser is Not Compatible', 'ee-simple-file-list-media'),
			'eeAudioEnabled' => $eeSFL->eeListSettings['AudioEnabled'],
			'eeAudioHeight' => $eeSFL->eeListSettings['AudioHeight'],
			
			// Back-End Only
			'eeShowText' => __('Show', 'ee-simple-file-list'), // Shortcode Builder
			'eeHideText' => __('Hide', 'ee-simple-file-list'),
			
			// Pro Only
			'eeChooseFolderText' => __('Choose Folder', 'ee-simple-file-list'),
			'eeMainFolderText' => __('Main Folder', 'ee-simple-file-list'),
			'eeExtractConfirm1' => __('Are You Sure?', 'ee-simple-file-list'),
			'eeExtractConfirm2' => __('The ZIP file will be extracted to this folder.', 'ee-simple-file-list'),
			'eeExtractConfirm3' => __('This may take some time.', 'ee-simple-file-list'),
			
			// Extensions
			'eeChooseListText' => __('Choose List', 'ee-simple-file-list'),
		);
		
		// Front-End Display
		include_once(eeSFL_PluginDir . 'base/ee-front-end.php');
		add_shortcode( 'eeSFL', 'eeSFL_FrontEnd' );
		
		$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'List ID = ' . $eeSFL->eeListID . ' --> Setup Loaded.';
		
	}
	
	return TRUE;
}





// The Admin Menu
function eeSFL_AdminMenu() {
	
	global $eeSFL, $eeSFLA;
	
	// Only include when accessing the plugin admin pages
	if( isset($_GET['page']) ) {
		
		$eeOutput = '<!-- Simple File List Admin -->';
		$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Admin Menu Loading ...';
			
		$eeSFL_Include = wp_create_nonce(eeSFL_Include); // Security
		include_once(eeSFL_PluginDir . 'base/ee-back-end.php'); // Admin's List Management Page

	}
	
	// Admin Menu Visibility
	$eeCapability = 'activate_plugins';
		
	
	if(!isset($eeSFL->eeListSettings['AdminRole'])) { // First Run
		$eeSFL->eeListSettings['AdminRole'] = 5;
	}
	
	if(empty($eeSFLA)) {
		switch ($eeSFL->eeListSettings['AdminRole']) {
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
	}
	
	
	// The Admin Menu
	add_menu_page(
		__(eeSFL_PluginName, eeSFL_PluginSlug), // Page Title - Defined at the top of this file
		__(eeSFL_PluginMenuTitle, eeSFL_PluginSlug), // Menu Title
		$eeCapability, // User status required to see the menu
		eeSFL_PluginSlug, // Slug
		'eeSFL_BackEnd', // Function that displays the menu page
		'dashicons-index-card' // Icon used
	);
}


// Ajax Handlers
// Function name must be the same as the action name to work on front side ?

// Confirmation
function simplefilelist_confirm() {
	delete_option('eeSFL_Confirm');
	wp_die();
}

// Edit an Item
function simplefilelist_edit_job() {
	global $eeSFL;
	$eeResult = $eeSFL->eeSFL_ItemEditor();
	echo $eeResult;
	wp_die();
}

// Upload a File
function simplefilelist_upload_job() {
	global $eeSFL_Upload;
	$eeResult = $eeSFL_Upload->eeSFL_FileUploader();
	echo $eeResult;
	wp_die();
}


// Language Enabler
function eeSFL_Textdomain() {
	load_plugin_textdomain( 'ee-simple-file-list', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}



// Display Notice that Multisite is unsupported. 
function eeSFL_ALERT() {
	
	if( is_multisite() ) {
		
		// Get the current screen
		$wpScreen = get_current_screen();
	 
		if ( $wpScreen->id == 'dashboard' OR $wpScreen->id == 'plugins' OR $wpScreen->id == 'toplevel_page_ee-simple-file-list-pro' ) {
			
			$eeOutput = '<div class="notice notice-warning is-dismissible eeSFL_Alert">
				<p><strong>' . __('MULTISITE ENABLED', 'ee-simple-file-list') . '</strong><br />
					' . __('Simple File List Pro is not compatible with Multisite.', 'ee-simple-file-list') . ' <br />
					' . __('This configuration is unsupported.', 'ee-simple-file-list') . '</p>
				</div>';
				
			echo $eeOutput;
		}
	}
}



// Register Our Styles and Scripts to be used later
function eeSFL_RegisterAssets() {
	
	global $eeSFL_Pro;
	
	$eeDependents = array('jquery'); // Requires jQuery
	
	// Register All CSS
	wp_register_style( 'ee-simple-file-list-css', eeSFL_PluginURL . 'css/styles.css', '', eeSFL_CacheBuster);
	wp_register_style( 'ee-simple-file-list-css-theme-dark', eeSFL_PluginURL . 'css/styles-theme-dark.css', '', eeSFL_CacheBuster );
	wp_register_style( 'ee-simple-file-list-css-theme-light', eeSFL_PluginURL . 'css/styles-theme-light.css', '', eeSFL_CacheBuster );
	wp_register_style( 'ee-simple-file-list-css-flex', eeSFL_PluginURL . 'css/styles-flex.css', '', eeSFL_CacheBuster );
	wp_register_style( 'ee-simple-file-list-css-tiles', eeSFL_PluginURL . 'css/styles-tiles.css', '', eeSFL_CacheBuster );
	wp_register_style( 'ee-simple-file-list-css-table', eeSFL_PluginURL . 'css/styles-table.css', '', eeSFL_CacheBuster );
	wp_register_style( 'ee-simple-file-list-css-upload', eeSFL_PluginURL . 'css/styles-upload-form.css', '', eeSFL_CacheBuster );
	
	// Register JavaScripts
	wp_register_script( 'ee-simple-file-list-js-head', eeSFL_PluginURL . 'js/ee-head.js' );
	wp_register_script( 'ee-simple-file-list-js-footer', eeSFL_PluginURL . 'js/ee-footer.js' );
	wp_register_script( 'ee-simple-file-list-js-edit-file', eeSFL_PluginURL . 'js/ee-edit-file.js' );
	wp_register_script( 'ee-simple-file-list-js-uploader', eeSFL_PluginURL . 'js/ee-uploader.js' );
	
	if(defined('eeSFL_Pro')) {
		wp_enqueue_style( 'ee-simple-file-list-css', eeSFL_PluginURL . 'pro/css/ee-style-pro-back.css', '', eeSFL_CacheBuster );
		wp_register_script( 'ee-simple-file-list-pro', eeSFL_PluginURL . '/pro/js/ee-pro.js' );
	}
}



// Load Front-End Resources
function eeSFL_Enqueue() {
	
	global $eeSFL_VarsForJS;
	
	$eeDependents = array('jquery'); // Requires jQuery
	wp_enqueue_style('ee-simple-file-list-css');
	wp_enqueue_script('ee-simple-file-list-js-head', eeSFL_PluginURL . 'js/ee-head.js', $eeDependents, eeSFL_CacheBuster);
	wp_enqueue_script('ee-simple-file-list-js-foot', eeSFL_PluginURL . 'js/ee-footer.js', $eeDependents, eeSFL_CacheBuster, TRUE);
	wp_localize_script( 'ee-simple-file-list-js-foot', 'eesfl_vars', $eeSFL_VarsForJS );
	
	// Pass variables
	wp_localize_script( 'ee-simple-file-list-js-foot', 'eesfl_vars', $eeSFL_VarsForJS );
}







// Simple File List CRON Jobs
function eeSFL_CronSchedule($schedules) {
	$eeTime = ini_get('max_execution_time') + 30;
	if(!isset($schedules["eeMX"])) { $schedules['eeMX'] = array('interval' => $eeTime, 'display' => __('Max Execution Time + 30s')); }
	if(!isset($schedules["eeScanHour"])) { $schedules['eeScanHour'] = array('interval' => 3600, 'display' => __('SFL - Scan Each Hour')); }
	if(!isset($schedules["eeScanDay"])) { $schedules['eeScanDay'] = array('interval' => 86400, 'display' => __('SFL - Scan Each Day')); }
	return $schedules;
}





// Address All-in-One SEO Conflict
function eeSFL_aioseo_filter_conflicting_shortcodes( $conflictingShortcodes ) {
   	$conflictingShortcodes = array_merge( $conflictingShortcodes, [
	   'Simple File List Pro' => '[eeSFL]',
	   'Simple File List Search' => '[eeSFLS]'
	]);
	return $conflictingShortcodes;
}


// Add Action Links to the Plugins Page
function eeSFL_ActionPluginLinks( $links ) {
	
	$eeLinks = array(
		'<a href="' . admin_url( 'admin.php?page=ee-simple-file-list-pro' ) . '">' . __('Admin List', 'ee-simple-file-list') . '</a>',
		'<a href="' . admin_url( 'admin.php?page=ee-simple-file-list-pro&tab=settings' ) . '">' . __('Settings', 'ee-simple-file-list') . '</a>'
	);
	return array_merge( $links, $eeLinks );
}


// Upon Deactivation...
function eeSFL_Deactivate() {
	
	// Unschedule Cron Jobs
	$eeTasks = get_option('eeSFL_Tasks');
	
	foreach( $eeTasks as $eeID => $eeTask ) {
		
		$eeTimestamp = wp_next_scheduled( 'eeSFL_Background_ReIndex_Hook_' . $eeID, array($eeID) );
		wp_unschedule_event( $eeTimestamp, 'eeSFL_Background_ReIndex_Hook_' . $eeID, array($eeID) );
		
		$eeTimestamp = wp_next_scheduled( 'eeSFL_Background_GenerateThumbs_Hook_' . $eeID, array($eeID) );
		wp_unschedule_event( $eeTimestamp, 'eeSFL_Background_GenerateThumbs_Hook_' . $eeID, array($eeID) );
	}
	
}



// GENERAL FUNCTIONS ----------------------------------------------

// Add the correct URL argument operator, ? or &
function eeSFL_AppendProperUrlOp($eeURL) {
	
	if ( strpos($eeURL, '?') ) {
		$eeURL .= '&';
	} else {
		$eeURL .= '?';
	}
	
	return $eeURL;
}



// Yes or No Settings Checkboxes
function eeSFL_ProcessCheckboxInput($eeTerm) {
	
	$eeValue = sanitize_text_field(@$_POST['ee' . $eeTerm]);
	
	if($eeValue == 'YES') { return 'YES'; } else { return 'NO'; }
}



// Settings Text Inputs 
function eeSFL_ProcessTextInput($eeTerm, $eeType = 'text') {
	
	$eeValue = '';
	
	if($eeType == 'email') {
		
		$eeValue = filter_var(sanitize_email(@$_POST['ee' . $eeTerm]), FILTER_VALIDATE_EMAIL);
	
	} elseif($eeType == 'textarea') {
		
		$eeValue = esc_textarea(sanitize_textarea_field( @$_POST['ee' . $eeTerm] ));
		
	} elseif(isset($_POST['ee' . $eeTerm])) {
		$eeValue = strip_tags($_POST['ee' . $eeTerm]);
		$eeValue = esc_textarea(sanitize_text_field($eeValue));	
	}
	
	return $eeValue;
}



// Return a formatted header string
function eeSFL_ReturnHeaderString($eeFrom, $eeCc = FALSE, $eeBcc = FALSE) {
	
	$eeAdminEmail = get_option('admin_email');
	
	$eeHeaders = 'From: ' . get_option('blogname') . ' < ' . $eeAdminEmail . ' >'  . PHP_EOL;
	
	if($eeCc) { $eeHeaders .= "CC: " . $eeCc . PHP_EOL; }
	
	if($eeBcc) { $eeHeaders .= "BCC: " . $eeBcc . PHP_EOL; }
	
	if( !filter_var($eeFrom, FILTER_VALIDATE_EMAIL) ) {
		$eeFrom = is_admin();
	}
	
	$eeHeaders .= "Return-Path: " . $eeAdminEmail . PHP_EOL . 
		"Reply-To: " . $eeFrom . PHP_EOL;
	
	return $eeHeaders;

}



// Process a raw input of email addresses
// Can be a single address or a comma sep list
function eeSFL_ProcessEmailString($eeString) {
	
	$eeString = sanitize_text_field($eeString);
	
	if( strpos($eeString, ',') ) { // More than one address?
		
		$eeArray = explode(',', $eeString);
		
		$eeAddresses = ''; // Reset
		
		foreach( $eeArray as $eeEmail) {
			
			$eeEmail = filter_var(sanitize_email($eeEmail), FILTER_VALIDATE_EMAIL);
			
			if($eeEmail) {
				
				$eeAddresses .= $eeEmail . ','; // Reassemble validated addresses
			}
		}
		
		$eeAddresses = substr($eeAddresses, 0, -1); // Strip the last comma
	
	} else {
		
		$eeAddresses = filter_var(sanitize_email($eeString), FILTER_VALIDATE_EMAIL);
	}
	
	if( strpos($eeAddresses, '@') ) {
		
		return $eeAddresses;
		
	} else {
		
		return FALSE;
	}
}


function eeSFL_NonceError($eeFile, $eeAction) {
	
	$eeOutput = '
	<h1>' . __('WordPress Nonce Failure', 'ee-simple-file-list') . '</h1>
	<p>' . __('Please back up and refresh the page. Then try your action again.', 'ee-simple-file-list') . '<br /><br />
	' . __('File', 'ee-simple-file-list'). ': ' . basename($eeFile) . '<br />
	' . __('Type', 'ee-simple-file-list'). ': ' . $eeAction . '</p>';
	
	wp_die($eeOutput);
}


// Convert the extension slug to the prefix
function eeSFL_SlugToPrefix($eeSlug) {

	$eeParts = explode('-', $eeSlug);
	$eeOutput = 'ee';

	for ($ee = 1; $ee < count($eeParts); $ee++) {
		$eeOutput .= strtoupper($eeParts[$ee][0]);
	}
	
	return $eeOutput;
}



// Send the Optional Usage Report - Users Must Opt-In to This
function eeSFL_OptInReportGenerator() {
	
	if(get_option('eeSFL_ReportsOptIn') != 'YES' OR get_transient('eeSFL_ReportsOptInSent')) { 
		return FALSE;
	}
	
	global $eeSFL, $eeSFL_Environment;
	
	// Assemble the Message
	$eeData = array();
	$eeData['plugin'] = eeSFL_PluginName;
	$eeData['domain'] = get_site_url();
	$eeData['admin'] = get_option('admin_email');
	
	// ENVIRONMENT
	
	if(defined('eeSFL_Pro')) { 
		global $eeSFL_Pro, $eeSFLS, $eeSFLA, $eeSFLM;
		$eeData['version'] = eeSFL_ThisPluginVersion;
		$eeSFL_Pro->eeSFL_CountFilesAndFolders();
		$eeSFL->eeEnvironment['files'] = $eeSFL->eeFileCount;
		$eeSFL->eeEnvironment['folders'] = $eeSFL->eeFolderCount;
		$eeSFL->eeEnvironment['size'] = $eeSFL_Environment->eeSFL_GetFileSize($eeSFL_Pro->eeSFL_GetFolderSize($eeSFL->eeListSettings['FileListDir']));
		if($eeSFLS) { $eeSFL->eeEnvironment['search'] = 'Active';
		} elseif(is_dir(WP_PLUGIN_DIR . '/ee-simple-file-list-search')) { $eeSFL->eeEnvironment['search'] = 'Inactive'; }
		if($eeSFLM) { $eeSFL->eeEnvironment['email'] = 'Active';
		} elseif(is_dir(WP_PLUGIN_DIR . '/ee-simple-file-list-email')) { $eeSFL->eeEnvironment['email'] = 'Inactive'; }
		if($eeSFLA) { $eeSFL->eeEnvironment['access'] = 'Active'; $eeSFL->eeListSettings['ID'] = $eeSFL->eeListID;
		} elseif(is_dir(WP_PLUGIN_DIR . '/ee-simple-file-list-access')) { $eeSFL->eeEnvironment['access'] = 'Inactive'; }
	} else { 
		$eeData['version'] = eeSFL_BASE_Version;
		$eeSFL->eeSFL_CountFiles();
		$eeSFL->eeEnvironment['files'] = $eeSFL->eeFileCount;
		$eeSFL->eeEnvironment['folders'] = 0;
		$eeSFL->eeEnvironment['size'] = $eeSFL_Environment->eeSFL_GetFileSize($eeSFL->eeListSettings['FileListDir']);
	}
	
	$eeSFL->eeEnvironment['locale'] = get_locale();
	$eeSFL->eeEnvironment['language'] = get_option('eeSFL_Lang');
	$eeData['environment'] = $eeSFL->eeEnvironment;
	
	// SETTINGS
	
	// Remove Sensitive Stuff
	unset($eeSFL->eeListSettings['NotifyFrom']); // Remove these...
	unset($eeSFL->eeListSettings['NotifyFromName']);
	unset($eeSFL->eeListSettings['NotifySubject']);
	unset($eeSFL->eeEnvironment['pluginDir']);
	unset($eeSFL->eeEnvironment['wpUploadDir']);
	
	$eeSettingsToSend = array('ListID' => $eeSFL->eeListID);
	$eeSettingsToSend = array_merge($eeSettingsToSend, $eeSFL->eeListSettings);
	
	// Replace Addresses with Counts
	$eeArray = explode(',', $eeSFL->eeListSettings['NotifyTo']);
	$eeSettingsToSend['NotifyTo'] = count($eeArray) . ' Addresses'; // Replace addresses with counts
	$eeArray = explode(',', $eeSFL->eeListSettings['NotifyCc']);
	$eeSettingsToSend['NotifyCc'] = count($eeArray) . ' Addresses';
	$eeArray = explode(',', $eeSFL->eeListSettings['NotifyBcc']);
	$eeSettingsToSend['NotifyBcc'] = count($eeArray) . ' Addresses';
	
	// Replace Message with Char Count
	$eeSettingsToSend['NotifyMessage'] = strlen($eeSettingsToSend['NotifyMessage']) . ' Characters';
	$eeData['settings'] = $eeSettingsToSend;
	
	// Parse the Log and Look for errors and warnings
	$eeLogs = get_option('eeSFL_TheLog');
	foreach($eeLogs as $eeDate => $eeLog) {
		foreach($eeLog as $eeKey => $eeEntry) {
			if($eeKey == 'errors' OR $eeKey == 'warnings') {
				$eeData['trouble'][$eeDate][$eeKey] = $eeEntry;
			}
		}
	}
	
	// echo '<pre>'; print_r($eeSettingsToSend); echo '</pre>'; exit;
	
	// Send This Info Home
	$eeJsonData = wp_json_encode($eeData);
	$eeEndpointUrl = 'https://simplefilelist.com/eeOptInRecv/index.php';
	
	$eeResponse = wp_remote_post($eeEndpointUrl, array(
		'method' => 'POST',
		'timeout' => 2,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(
			'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
			'X-Content-Type-Options' => 'nosniff',
			'X-Frame-Options' => 'SAMEORIGIN'
		),
		'body' => array('body' => $eeJsonData),
		'cookies' => array()
	));
	
	// Handle the Response
	if (is_wp_error($eeResponse)) {
		$eeSFL->eeLog['issues'][] = $eeSFL->eeSFL_NOW() . 'Opt-In Reciever Connection Failure';
		$eeSFL->eeLog['issues'][] = $eeSFL->eeSFL_NOW() . $eeResponse->get_error_message();
	} else {
		$eeResponseBody = wp_remote_retrieve_body($eeResponse);
		$eeResponseCode = wp_remote_retrieve_response_code($eeResponse);
		if($eeResponseCode != 200) {
			$eeSFL->eeLog['issues'][] = $eeSFL->eeSFL_NOW() . 'Opt-In Reciever Failure: ' . $eeResponseCode . ' Error';
			$eeSFL->eeLog['issues'][] = $eeSFL->eeSFL_NOW() . esc_textarea($eeResponseBody);
		} elseif($eeResponseBody == 'SUCCESS') {
			set_transient('eeSFL_ReportsOptInSent', 'YES', 2592000); // 30 Days
			$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Opt-In Report Sent';
		} else {
			$eeSFL->eeLog['issues'][] = $eeSFL->eeSFL_NOW() . 'Opt-In Reporting Failure';
			$eeSFL->eeLog['issues'][] = $eeSFL->eeSFL_NOW() . esc_textarea($eeResponseBody);
		}
	}
}


// LEGACY - Get Elapsed Time
// function eeSFL_noticeTimer() { // 6.1.12 and under
// 	global $eeSFL; // Time SFL got going
// 	return $eeSFL->eeSFL_NOW();
// }

// LEGACY - Convert hyphens to spaces for display only - Under 6.0
// function eeSFL_PreserveSpaces($eeFileName) {
// 	$eeFileName = str_replace('-', ' ', $eeFileName);
// 	return $eeFileName;
// }

?>