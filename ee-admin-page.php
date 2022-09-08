<?php // Simple File List Script: ee-admin-page.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_FREE_Log['RunTime'][] = 'Loaded: ee-admin-page';

// Admin-Side Display
function eeSFL_FREE_ManageLists() {
	
	global $eeSFL_FREE, $eeSFL_FREE_Log, $eeSFL_FREE_DevMode, $eeSFL_Settings, $eeSFL_FREE_Env, $eeSFL_FREE_ListRun;	 
	
	$eeSFL_Files = FALSE;
	$eeForceSort = FALSE; // Only used in shortcode
	
	$eeAdmin = is_admin(); // Will be TRUE here
	
	// Process the Config Array
	if(!is_array($eeSFL_Settings) OR !is_array($eeSFL_FREE_Env)) {
		$eeSFL_FREE_Log['errors'][] = 'No SFL Configuration';
		return FALSE;
	}

	// Please Buy the Pro Version
	$eeUpSell = '<div id="eeGoProBanner">
		
	<a href="https://get.simplefilelist.com/index.php?eeExtension=ee-simple-file-list-pro&pr=free" class="button" target="_blank">' . __('Upgrade Now', 'ee-simple-file-list') . ' &rarr;</a>
	
	<small><a href="https://demo.simple-file-list.com/?pr=free" class="eeRight" target="_blank">Try the Demo</a></small>
		
	<p><strong>' . __('Upgrade to Pro', 'ee-simple-file-list') . '</strong> - ' . __('Add sub-folder support, bulk file editing, send files by email, directory location customization and more.', 'ee-simple-file-list') . ' ' . __('Plus, add extensions for larger file lists and flexible user management.', 'ee-simple-file-list') . ' ' . __('The low cost is just once per domain.', 'ee-simple-file-list') . '</p>
		
	</div>';
	
	// Begin Output
	$eeOutput = '
	
	<div class="eeSFL_Admin wrap">
	<div class="eeSFL" id="eeSFL">';
	
	if($eeSFL_FREE_DevMode) { $eeOutput .= '<p class="eeAlert">' . __('DEVELOPMENT MODE ON', 'ee-simple-file-list') . '</p>'; }
	
	// Get the new tab's query string value. We will only use values to display tabs that we are expecting.
	if( isset( $_GET[ 'tab' ] ) ) { $active_tab = esc_js(sanitize_text_field($_GET[ 'tab' ])); } else { $active_tab = 'file_list'; }
	
	$eeOutput .= '<h2 class="nav-tab-wrapper">';
	
	// Main Tabs -------
	
	// File List
	$eeOutput .= '<a href="?page=' . $eeSFL_FREE->eePluginSlug . '&tab=file_list" class="nav-tab tabList ';  
	if($active_tab == 'file_list') {$eeOutput .= '  eeActiveTab ';}    
    $active_tab == 'file_list' ? 'nav-tab-active' : '';
    $eeOutput .= $active_tab . '">' . __('File List', 'ee-simple-file-list') . '</a>';
    
	// Link to Support Form
    $eeOutput .= '<a href="https://simplefilelist.com/get-support/" class="nav-tab tabSupport" target="_blank">' . __('Get Help', 'ee-simple-file-list') . ' &rarr;</a>';
    
    // Author
    $eeOutput .= '<a href="?page=' . $eeSFL_FREE->eePluginSlug . '&tab=author" class="nav-tab tabSupport ';   
	if($active_tab == 'author') {$eeOutput .= '  eeActiveTab '; }  
    $active_tab == 'author' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Author', 'ee-simple-file-list') . '</a>';
    
    // Settings
    $eeOutput .= '<a href="?page=' . $eeSFL_FREE->eePluginSlug . '&tab=settings" class="nav-tab tabSettings ';   
	if($active_tab == 'settings') {$eeOutput .= '  eeActiveTab '; }  
    $active_tab == 'settings' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Settings', 'ee-simple-file-list') . '</a>';
    
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
		include($eeSFL_FREE_Env['pluginDir'] . 'includes/ee-upload-check.php');
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_FREE_Env['pluginDir'] . 'includes/ee-upload-form.php'); // The Uploader
		
		$eeOutput .= '</div>';
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_FREE_Env['pluginDir'] . 'ee-list-display.php'); // The File List		
	
	} elseif($active_tab == 'settings') {
		
		// Sub Tabs
		if( isset( $_GET[ 'subtab' ] ) ) { $active_subtab = esc_js(sanitize_text_field($_GET['subtab'])); } else { $active_subtab = 'list_settings'; }
	    	
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
	$eeOutput .= $eeSFL_FREE->eeSFL_WriteLogData(); // Only adds output if DevMode is ON

	// Output the page
	echo $eeOutput;
}

?>