<?php // Simple File List Script: ee-admin-page.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_Log['Admin'][] = 'Loaded: ee-admin-page';

// Admin-Side Display
function eeSFL_ManageLists() {
	
	global $eeSFL, $eeSFL_Log, $eeSFL_DevMode, $eeSFL_Config, $eeSFL_Env, $eeSFL_ListNumber;
	global $eeSFLF, $eeSFLS; // Extensions
	
	$eeAdmin = is_admin(); // Will be TRUE here
	
	$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security (Only one file included per load)
	
	// Process the Config Array
	if(!is_array($eeSFL_Config) OR !is_array($eeSFL_Env)) {
		$eeSFL_Log['errors'] = 'No SFL Configuration';
		return FALSE;
	}
	
	// Begin Output
	$eeOutput = '
	
	<div class="eeSFL_Admin wrap">
	<div id="eeSFL">'; 
	
	if($eeSFL_DevMode) { $eeOutput .= '<p class="eeAlert">' . __('DEVELOPMENT MODE ON', 'ee-simple-file-list') . '</p>'; }
	
	// Get the new tab's query string value
	if( isset( $_GET[ 'tab' ] ) ) { $active_tab = $_GET[ 'tab' ]; } else { $active_tab = 'file_list'; }
	
	$eeOutput .= '<h2 class="nav-tab-wrapper">';
	
	// Main Tabs -------
	
	// File List
	$eeOutput .= '<a href="?page=' . $eeSFL->eePluginSlug . '&tab=file_list" class="nav-tab tabList ';  
	if($active_tab == 'file_list') {$eeOutput .= '  eeActiveTab ';}    
    $active_tab == 'file_list' ? 'nav-tab-active' : '';    
    $eeOutput .= $active_tab . '">' . __('File List', 'ee-simple-file-list') . '</a>';
    
    // Author
    $eeOutput .= '<a href="?page=' . $eeSFL->eePluginSlug . '&tab=author" class="nav-tab tabSupport ';   
	if($active_tab == 'author') {$eeOutput .= '  eeActiveTab '; }  
    $active_tab == 'author' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Author', 'ee-simple-file-list') . '</a>';
    
	// The Help / Email Form Page
    $eeOutput .= '<a href="?page=' . $eeSFL->eePluginSlug . '&tab=help" class="nav-tab tabSupport ';   
	if($active_tab == 'help') {$eeOutput .= '  eeActiveTab '; }  
    $active_tab == 'help' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Get Help', 'ee-simple-file-list') . '</a>';
    
    // Settings
    $eeOutput .= '<a href="?page=' . $eeSFL->eePluginSlug . '&tab=settings" class="nav-tab tabSettings ';   
	if($active_tab == 'settings') {$eeOutput .= '  eeActiveTab '; }  
    $active_tab == 'settings' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Settings', 'ee-simple-file-list') . '</a>';
    
    // Get Extensions
    if(!$eeSFLF OR !$eeSFLS) {
	    $eeOutput .= '<a href="?page=' . $eeSFL->eePluginSlug . '&tab=extensions" class="nav-tab tabSupport ';   
		if($active_tab == 'extensions') {$eeOutput .= '  eeActiveTab '; }  
	    $active_tab == 'extensions' ? 'nav-tab-active' : ''; 
	    $eeOutput .= $active_tab . '">' . __('Add Features', 'ee-simple-file-list') . '</a>';
    }
    
    // Shortcode Builder
    $eeOutput .= '<a href="?page=' . $eeSFL->eePluginSlug . '&tab=shortcode" class="nav-tab tabSupport ';  
	if($active_tab == 'shortcode') {$eeOutput .= '  eeActiveTab '; }   
    $active_tab == 'support' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Create Shortcode', 'ee-simple-file-list') . '</a>';
    
    $eeOutput .= '</h2>'; // END Main Tabs    
    
    
    // Tab Content =============================================================
    
	if($active_tab == 'file_list') {
	
		$eeOutput .= '<div id="uploadFilesDiv">';
		
		include($eeSFL_Env['pluginDir'] . 'includes/ee-upload-form.php'); // The Uploader
		
		$eeOutput .= '</div>';
		
		include($eeSFL_Env['pluginDir'] . 'ee-list-display.php'); // The File List		
	
	} elseif($active_tab == 'settings') {
	
		// Sub Tabs
		if( isset( $_GET[ 'subtab' ] ) ) { $active_subtab = $_GET[ 'subtab' ]; } else { $active_subtab = 'list_settings'; }
		
		$eeOutput .= '
    
		<p class="eeTitle">' . __('Main File List Settings', 'ee-simple-file-list') . '</p>
    
    	<h2 class="nav-tab-wrapper">';
		
		// List Settings
		$eeOutput .= '<a href="?page=' . $eeSFL->eePluginSlug . '&tab=settings&subtab=list_settings" class="nav-tab ';  
		if($active_subtab == 'list_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'list_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('File List Settings', 'ee-simple-file-list') . '</a>';
	    
	    // Uploader Settings
		$eeOutput .= '<a href="?page=' . $eeSFL->eePluginSlug . '&tab=settings&subtab=uploader_settings" class="nav-tab ';  
		if($active_subtab == 'uploader_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'uploader_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('File Upload Settings', 'ee-simple-file-list') . '</a>';
	    
	    // Display Settings
		$eeOutput .= '<a href="?page=' . $eeSFL->eePluginSlug . '&tab=settings&subtab=display_settings" class="nav-tab ';  
		if($active_subtab == 'display_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'display_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('Display Settings', 'ee-simple-file-list') . '</a>';
	    
	    // Notifications Settings
		$eeOutput .= '<a href="?page=' . $eeSFL->eePluginSlug . '&tab=settings&subtab=email_settings" class="nav-tab ';  
		if($active_subtab == 'email_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'email_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('Notification Settings', 'ee-simple-file-list') . '</a>';
	    
	    $eeOutput .= '</h2>'; // END Subtabs
	    
		if($active_subtab == 'uploader_settings') {
			
			include($eeSFL_Env['pluginDir'] . 'includes/ee-upload-settings.php'); // The Uploader Settings
		
		} elseif($active_subtab == 'display_settings') {
			
			include($eeSFL_Env['pluginDir'] . 'includes/ee-list-display-settings.php'); // The List Display Settings
		
		} elseif($active_subtab == 'email_settings') {
			
			include($eeSFL_Env['pluginDir'] . 'includes/ee-email-settings.php'); // The Notifications Settings
		
		} else {
			
			include($eeSFL_Env['pluginDir'] . 'includes/ee-list-settings.php'); // The File List Settings
		}
		
	} elseif($active_tab == 'extensions') { // Instructions Tab Display...
			
		// Get the sales page
		include($eeSFL_Env['pluginDir'] . 'support/ee-get-extensions.php');
	
	
	} elseif($active_tab == 'shortcode') { // Shortcode Builder Tab Display...
			
		// Get the instructions page
		include($eeSFL_Env['pluginDir'] . 'support/ee-shortcode-builder.php');
	
	
	} elseif($active_tab == 'help') { // Email Support Tab Display...
		
		$eePlugin = $eeSFL->eePluginName;
			
		// Get the support page
		include($eeSFL_Env['pluginDir'] . 'support/ee-get-help.php');
	
	
	} else { // Author
					
		// Get the support page
		include($eeSFL_Env['pluginDir'] . 'support/ee-plugin-author.php');
		
	} // END Tab Content
	
	$eeSFL_Log['Config'][] = $eeSFL_Config;
	
	$eeOutput .= '<div id="eeAdminFooter">
	
			<fieldset><p id="eeFooterImportant" class="eeHide">' . __('IMPORTANT: Allowing the public to upload files to your web server comes with risk.', 'ee-simple-file-list') . ' ' .  
			__('Please go to Upload Settings and ensure that you only use the file types that you absolutely need.', 'ee-simple-file-list') . ' ' .  
			__('Open each file submitted carefully.', 'ee-simple-file-list') . '</p>';
				
			$eeOutput .= '<p class="eeRight">
			<a class="button" href="https://simplefilelist.com/docs/" target="_blank">' . __('Plugin Documentation', 'ee-simple-file-list') . '</a>
				<a target="_blank" class="button" href="https://wordpress.org/support/plugin/simple-file-list/reviews/">' . __('Review Plugin', 'ee-simple-file-list') . '</a></p>
					<a href="' . $eeSFL->eePluginWebPage . '" target="_blank">' . __('Plugin Website', 'ee-simple-file-list') . '</a> | 
							<a href="https://simplefilelist.com/give-feedback/" target="_blank">' . __('Give Feedback', 'ee-simple-file-list') . '</a> | 
								<a href="#" id="eeFooterImportantLink">' . __('Caution', 'ee-simple-file-list') . '</a>';
								
			if( !@count($eeSFL_Env['installed']) ) {
				$eeOutput .= ' | <a href="https://simplefilelist.com/donations/simple-file-list-project/">' . __('Buy Me Lunch', 'ee-simple-file-list') . '</a>';
			}
			
			$eeOutput .= '<br />
			
			' . __('Plugin Version', 'ee-simple-file-list') . ': ' . eeSFL_Version . ' | DB: ' . eeSFL_DB_Version . ' | CB: ' . eeSFL_Cache_Version;
			
			if( @defined('eeSFLF_Version') ) { $eeOutput .= '<br />
				
				' . __('Folder Extension', 'ee-simple-file-list') . ': ' . eeSFLF_Version;
			}
			
			if( @defined('eeSFLS_Version') ) { $eeOutput .= '<br />
				
				' . __('Search Extension', 'ee-simple-file-list') . ': ' . eeSFLS_Version;
			}
			
			$eeOutput .= '
				
			</fieldset>
		</div>
	</div>
</div>'; // END #eeSFL
	
	$eeSFL_Log['Admin'][] = 'Displaying Tab: ' . $active_tab . ' ' . @$active_subtab;
	
	if(@$_POST) {
		array_unshift($eeSFL_Log, $_POST); // Add POST to the beginning of the log
	}

	// Timer
	$eeSFL_Time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	$eeSFL_Log[] = 'Execution Time: ' . round($eeSFL_Time,3);
	
	// Write the log file to the Database
	$eeSFL->eeSFL_WriteLogData($eeSFL_Log); 
	
	// Visual Logging
	if($eeSFL_DevMode) { // Display the log at the bottom of the page.
		$eeOutput .= '<pre id="eeSFL_DevMode">Log File ' . print_r( array_filter($eeSFL_Log), TRUE) . '</pre>';
	}

	// Output the page
	echo $eeOutput;
}

?>