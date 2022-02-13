<?php // Simple File List Script: ee-admin-page.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_BASE_Log['RunTime'][] = 'Loaded: ee-admin-page';

// Admin-Side Display
function eeSFL_BASE_ManageLists() {
	
	global $eeSFL_BASE, $eeSFL_BASE_Log, $eeSFL_BASE_DevMode, $eeSFL_Settings, $eeSFL_BASE_Env, $eeSFL_BASE_ListRun;
	
	$eeSFL_Files = FALSE;
	$eeForceSort = FALSE; // Only used in shortcode
	
	$eeAdmin = is_admin(); // Should be TRUE here
	if(!$eeAdmin) { return FALSE; }
	
	$eeSFL_Nonce = wp_create_nonce('eeInclude');
	include('includes/ee-admin-header.php');

	// Get the new tab's query string value. We will only use values to display tabs that we are expecting.
	if( isset( $_GET[ 'tab' ] ) ) { $active_tab = sanitize_text_field($_GET[ 'tab' ]); } else { $active_tab = 'file_list'; }
	
	$eeOutput .= '
	<h2 class="nav-tab-wrapper">';
	
	// Main Tabs -------
	
	// File List
	$eeOutput .= '
	
	<span class="nav-tab-wrapper-left">
	
	<a href="?page=' . $eeSFL_BASE->eePluginSlug . '&tab=file_list" class="nav-tab ';  
	if($active_tab == 'file_list') {$eeOutput .= ' eeActiveTab '; }    
    $active_tab == 'file_list' ? 'nav-tab-active' : '';
    $eeOutput .= $active_tab . '">' . __('File List', 'ee-simple-file-list') . '</a>';
    
	
	// Settings
    $eeOutput .= '
    <a href="?page=' . $eeSFL_BASE->eePluginSlug . '&tab=settings" class="nav-tab ';   
	if($active_tab == 'settings') {$eeOutput .= ' eeActiveTab '; }  
    $active_tab == 'settings' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Settings', 'ee-simple-file-list') . '</a>';
    
    
    // Shortcode Builder
    $eeOutput .= '
    <a href="?page=' . $eeSFL_BASE->eePluginSlug . '&tab=shortcode" class="nav-tab ';  
	if($active_tab == 'shortcode') {$eeOutput .= ' eeActiveTab '; }   
    $active_tab == 'support' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Create Shortcode', 'ee-simple-file-list') . '</a>
    
    </span>
    <span class="nav-tab-wrapper-right">
    ';
    
    // You should Buy the PRO Version
    $eeOutput .= '
    <a href="?page=' . $eeSFL_BASE->eePluginSlug . '&tab=pro" class="nav-tab ';   
	if($active_tab == 'pro') {$eeOutput .= ' eeActiveTab '; }  
    $active_tab == 'pro' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Pro Version', 'ee-simple-file-list') . '</a>';
    
    // Author
    $eeOutput .= '
    <a href="?page=' . $eeSFL_BASE->eePluginSlug . '&tab=author" class="nav-tab ';   
	if($active_tab == 'author') {$eeOutput .= ' eeActiveTab '; }  
    $active_tab == 'author' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Author', 'ee-simple-file-list') . '</a>';
    
    // Link to Support Form
    $eeOutput .= '
    <a href="https://simplefilelist.com/get-support/" class="nav-tab" target="_blank">' . __('Get Help', 'ee-simple-file-list') . ' &rarr;</a>
    
    </span>
    
    </h2>'; // END Main Tabs   
    
    
    // Tab Content =============================================================
    
	if($active_tab == 'file_list') {
	
/*
		
		
		$eeOutput .= '<div id="uploadFilesDiv">';
		
		// Upload Check
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-upload-check.php');
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-upload-form.php'); // The Uploader
		
		$eeOutput .= '</div>';
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_BASE_Env['pluginDir'] . 'ee-list-display.php'); // The File List	
		
		
*/	
	
	} elseif($active_tab == 'settings') {
		
		// Sub Tabs
		if( isset( $_GET[ 'subtab' ] ) ) { $active_subtab = sanitize_text_field($_GET['subtab']); } else { $active_subtab = 'list_settings'; }
	    	
    	$eeOutput .= '
	    
	    <article class="eeSFL_Settings">
    	
    	<h2 class="nav-tab-wrapper">';
		
		// List Settings
		$eeOutput .= '<a href="?page=' . $eeSFL_BASE->eePluginSlug . '&tab=settings&subtab=list_settings" class="nav-tab ';  
		if($active_subtab == 'list_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'list_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('File List Settings', 'ee-simple-file-list') . '</a>';
	    
	    // Uploader Settings
		$eeOutput .= '<a href="?page=' . $eeSFL_BASE->eePluginSlug . '&tab=settings&subtab=uploader_settings" class="nav-tab ';  
		if($active_subtab == 'uploader_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'uploader_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('File Upload Settings', 'ee-simple-file-list') . '</a>';
	    
	    // Notifications Settings
		$eeOutput .= '<a href="?page=' . $eeSFL_BASE->eePluginSlug . '&tab=settings&subtab=email_settings" class="nav-tab ';  
		if($active_subtab == 'email_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'email_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('Notification Settings', 'ee-simple-file-list') . '</a>';
	    
	    // END Subtabs
	    $eeOutput .= '</h2>'; 
	    
		// Sub-Tab Content
		if($active_subtab == 'uploader_settings') {
			
			$eeSFL_Nonce = wp_create_nonce('eeInclude');
			include($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-upload-settings.php'); // The Uploader Settings
		
		} elseif($active_subtab == 'email_settings') {
			
			$eeSFL_Nonce = wp_create_nonce('eeInclude');
			include($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-email-settings.php'); // The Notifications Settings
		
		} else {
			
			$eeSFL_Nonce = wp_create_nonce('eeInclude');
			include($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-list-settings.php'); // The File List Settings
		}
		
		$eeOutput .= '
		
		</article>';
		
	} elseif($active_tab == 'shortcode') { // Shortcode Builder Tab Display...
			
		// Get the instructions page
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-shortcode-builder.php');
	
	
	} elseif($active_tab == 'pro') { // Instructions Tab Display...
			
		// Get the sales page
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-get-pro.php');
	
	
	} elseif($active_tab == 'help') { // Email Support Tab Display...
		
		$eePlugin = $eeSFL_BASE->eePluginName;
			
		// Get the support page
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_BASE_Env['pluginDir'] . 'support/ee-get-help.php');
	
	
	} else { // Author
					
		// Get the support page
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_BASE_Env['pluginDir'] . 'includes/ee-plugin-author.php');
		
	} // END Tab Content
	
	
	$eeSFL_Nonce = wp_create_nonce('eeInclude');
	include('includes/ee-admin-footer.php');
	
	// Timer
	$eeSFL_Time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	$eeSFL_BASE_Log[] = 'Execution Time: ' . round($eeSFL_Time,3);
	
	// Logging
	$eeOutput .= $eeSFL_BASE->eeSFL_WriteLogData(); // Only adds output if DevMode is ON

	// Output the page
	echo $eeOutput;
}

?>