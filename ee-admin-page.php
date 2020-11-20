<?php // Simple File List Script: ee-admin-page.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_FREE_Log['SFL'][] = 'Loaded: ee-admin-page';

// Admin-Side Display
function eeSFL_FREE_ManageLists() {
	
	global $eeSFL_FREE, $eeSFL_FREE_Log, $eeSFL_FREE_DevMode, $eeSFL_FREE_Config, $eeSFL_FREE_Env, $eeSFL_FREE_ListRun;
	
	$eeSFL_Files = FALSE;
	$eeForceSort = FALSE; // Only used in shortcode
	
	$eeAdmin = is_admin(); // Will be TRUE here
	
	// Process the Config Array
	if(!is_array($eeSFL_FREE_Config) OR !is_array($eeSFL_FREE_Env)) {
		$eeSFL_FREE_Log['errors'][] = 'No SFL Configuration';
		return FALSE;
	}
	
	// Begin Output
	$eeOutput = '
	
	<div class="eeSFL_Admin wrap">
	<div class="eeSFL" id="eeSFL">'; 
	
	if($eeSFL_FREE_DevMode) { $eeOutput .= '<p class="eeAlert">' . __('DEVELOPMENT MODE ON', 'ee-simple-file-list') . '</p>'; }
	
	// Get the new tab's query string value. We will only use values to display tabs that we are expecting.
	if( isset( $_GET[ 'tab' ] ) ) { $active_tab = sanitize_text_field($_GET[ 'tab' ]); } else { $active_tab = 'file_list'; }
	
	$eeOutput .= '<h2 class="nav-tab-wrapper">';
	
	// Main Tabs -------
	
	// File List
	$eeOutput .= '<a href="?page=' . $eeSFL_FREE->eePluginSlug . '&tab=file_list" class="nav-tab tabList ';  
	if($active_tab == 'file_list') {$eeOutput .= '  eeActiveTab ';}    
    $active_tab == 'file_list' ? 'nav-tab-active' : '';
    $eeOutput .= $active_tab . '">' . __('File List', 'ee-simple-file-list') . '</a>';
    
    // Author
    $eeOutput .= '<a href="?page=' . $eeSFL_FREE->eePluginSlug . '&tab=author" class="nav-tab tabSupport ';   
	if($active_tab == 'author') {$eeOutput .= '  eeActiveTab '; }  
    $active_tab == 'author' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Author', 'ee-simple-file-list') . '</a>';
    
	// The Help / Email Form Page
    $eeOutput .= '<a href="https://simplefilelist.com/get-support/" class="nav-tab tabSupport">' . __('Get Help', 'ee-simple-file-list') . '</a>';
    
    // Settings
    $eeOutput .= '<a href="?page=' . $eeSFL_FREE->eePluginSlug . '&tab=settings" class="nav-tab tabSettings ';   
	if($active_tab == 'settings') {$eeOutput .= '  eeActiveTab '; }  
    $active_tab == 'settings' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Settings', 'ee-simple-file-list') . '</a>';
    
    // Shortcode Builder
    $eeOutput .= '<a href="?page=' . $eeSFL_FREE->eePluginSlug . '&tab=shortcode" class="nav-tab tabSettings ';  
	if($active_tab == 'shortcode') {$eeOutput .= '  eeActiveTab '; }   
    $active_tab == 'support' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Create Shortcode', 'ee-simple-file-list') . '</a>';
    
    // You should Buy the PRO Version
    $eeOutput .= '<a href="?page=' . $eeSFL_FREE->eePluginSlug . '&tab=pro" class="nav-tab tabSupport ';   
	if($active_tab == 'pro') {$eeOutput .= '  eeActiveTab '; }  
    $active_tab == 'pro' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Pro Version', 'ee-simple-file-list') . '</a>';
    
    $eeOutput .= '</h2>'; // END Main Tabs   
    
    
    // Tab Content =============================================================
    
	if($active_tab == 'file_list') {
	
		$eeOutput .= '<div id="uploadFilesDiv">';
		
		// Upload Check
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_FREE_Env['pluginDir'] . '/includes/ee-upload-check.php');
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_FREE_Env['pluginDir'] . 'includes/ee-upload-form.php'); // The Uploader
		
		$eeOutput .= '</div>';
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_FREE_Env['pluginDir'] . 'ee-list-display.php'); // The File List		
	
	} elseif($active_tab == 'settings') {
		
		// Sub Tabs
		if( isset( $_GET[ 'subtab' ] ) ) { $active_subtab = sanitize_text_field($_GET['subtab']); } else { $active_subtab = 'list_settings'; }
	    	
    	$eeOutput .= '<p class="eeTitle">' . __('File List Settings', 'ee-simple-file-list') . '</p>
    	
    	<h2 class="nav-tab-wrapper">';
		
		// List Settings
		$eeOutput .= '<a href="?page=' . $eeSFL_FREE->eePluginSlug . '&tab=settings&subtab=list_settings" class="nav-tab ';  
		if($active_subtab == 'list_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'list_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('File List Settings', 'ee-simple-file-list') . '</a>';
	    
	    // Uploader Settings
		$eeOutput .= '<a href="?page=' . $eeSFL_FREE->eePluginSlug . '&tab=settings&subtab=uploader_settings" class="nav-tab ';  
		if($active_subtab == 'uploader_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'uploader_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('File Upload Settings', 'ee-simple-file-list') . '</a>';
	    
	    // Notifications Settings
		$eeOutput .= '<a href="?page=' . $eeSFL_FREE->eePluginSlug . '&tab=settings&subtab=email_settings" class="nav-tab ';  
		if($active_subtab == 'email_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'email_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('Notification Settings', 'ee-simple-file-list') . '</a>';
	    
	    $eeOutput .= '</h2>'; // END Subtabs
	    
		if($active_subtab == 'uploader_settings') {
			
			$eeSFL_Nonce = wp_create_nonce('eeInclude');
			include($eeSFL_FREE_Env['pluginDir'] . 'includes/ee-upload-settings.php'); // The Uploader Settings
		
		} elseif($active_subtab == 'email_settings') {
			
			$eeSFL_Nonce = wp_create_nonce('eeInclude');
			include($eeSFL_FREE_Env['pluginDir'] . 'includes/ee-email-settings.php'); // The Notifications Settings
		
		} else {
			
			$eeSFL_Nonce = wp_create_nonce('eeInclude');
			include($eeSFL_FREE_Env['pluginDir'] . 'includes/ee-list-settings.php'); // The File List Settings
		}
		
	} elseif($active_tab == 'shortcode') { // Shortcode Builder Tab Display...
			
		// Get the instructions page
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_FREE_Env['pluginDir'] . 'includes/ee-shortcode-builder.php');
	
	
	} elseif($active_tab == 'pro') { // Instructions Tab Display...
			
		// Get the sales page
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_FREE_Env['pluginDir'] . 'includes/ee-get-pro.php');
	
	
	} elseif($active_tab == 'help') { // Email Support Tab Display...
		
		$eePlugin = $eeSFL_FREE->eePluginName;
			
		// Get the support page
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_FREE_Env['pluginDir'] . 'support/ee-get-help.php');
	
	
	} else { // Author
					
		// Get the support page
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_FREE_Env['pluginDir'] . 'includes/ee-plugin-author.php');
		
	} // END Tab Content
	
	
	$eeSFL_Nonce = wp_create_nonce('eeInclude');
	include('includes/ee-admin-footer.php');
	
	$eeOutput .= '</div></div>';
	
	// Timer
	$eeSFL_Time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	$eeSFL_FREE_Log[] = 'Execution Time: ' . round($eeSFL_Time,3);
	
	// Logging
	if($eeSFL_FREE_DevMode) {
		if(@$_REQUEST) { $eeOutput .= '<pre>REQUEST ' . print_r($_REQUEST, TRUE) . '</pre>'; array_unshift($eeSFL_FREE_Log, $_REQUEST); }
		$eeOutput .= '<pre>Display File Array ' . print_r(@$eeSFL_Files, TRUE) . '</pre>';
		$eeOutput .= '<pre>Display List Settings ' . print_r($eeSFL_FREE_Config, TRUE) . '</pre>';
		$eeOutput .= '<pre>Environment ' . print_r($eeSFL_FREE_Env, TRUE) . '</pre>';
		$eeOutput .= '<pre>Runtime Log ' . print_r($eeSFL_FREE_Log, TRUE) . '</pre>';
		$eeSFL_FREE->eeSFL_WriteLogData($eeSFL_FREE_Log);
	}

	// Output the page
	echo $eeOutput;
}

?>