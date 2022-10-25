<?php // Simple File List Script: ee-admin-page.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = 'Loaded: ee-admin-page';

// Admin-Side Display
function eeSFL_BASE_BackEnd() {
	
	global $eeSFL_BASE;
	
	$eeConfirm = FALSE;
	$eeForceSort = FALSE; // Only used in shortcode
	$eeURL = eeSFL_BASE_GetThisURL();
	
	$eeAdmin = is_admin(); // Should be TRUE here
	if(!$eeAdmin) { return FALSE; }
	
	$eeSFL_Nonce = wp_create_nonce('eeInclude');
	include('includes/ee-admin-header.php');

	// Upsell to Pro
	if( $eeAdmin AND !$_POST AND count($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['messages']) === 0 ) {
		$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['messages'][] = $eeUpSell;
	}

	// Get the new tab's query string value. We will only use values to display tabs that we are expecting.
	if( isset( $_GET[ 'tab' ] ) ) { $active_tab = sanitize_text_field($_GET[ 'tab' ]); } else { $active_tab = 'file_list'; }
	
	$eeOutput .= '
	<h2 class="nav-tab-wrapper">';
	
	// Main Tabs -------
	
	// File List
	$eeOutput .= '
	
	<span class="nav-tab-wrapper-left">
	
	<a href="?page=' . eeSFL_BASE_PluginSlug . '&tab=file_list" class="nav-tab ';  
	if($active_tab == 'file_list') {$eeOutput .= ' eeActiveTab '; }    
    $active_tab == 'file_list' ? 'nav-tab-active' : '';
    $eeOutput .= $active_tab . '">' . __('File List', 'ee-simple-file-list') . '</a>';
    
	
	// Settings
    $eeOutput .= '
    <a href="?page=' . eeSFL_BASE_PluginSlug . '&tab=settings" class="nav-tab ';   
	if($active_tab == 'settings') {$eeOutput .= ' eeActiveTab '; }  
    $active_tab == 'settings' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('List Settings', 'ee-simple-file-list') . '</a>
    
    <a href="?page=' . eeSFL_BASE_PluginSlug . '&tab=pro" class="nav-tab ';   
	if($active_tab == 'pro') {$eeOutput .= ' eeActiveTab '; }  
    $active_tab == 'pro' ? 'nav-tab-active' : ''; 
    $eeOutput .= $active_tab . '">' . __('Upgrade Version', 'ee-simple-file-list') . '</a>
    
    
    </span>
    <span class="nav-tab-wrapper-right">
    
    
    <a href="?page=' . eeSFL_BASE_PluginSlug . '&tab=author" class="nav-tab ';   
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
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		require_once($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-upload-check.php');
	
		// Get the File Array
		$eeSFL_BASE->eeSFL_UpdateFileListArray();
		
		// echo '<pre>'; print_r($eeSFL_BASE->eeAllFiles); echo '</pre>';
		// echo '<pre>'; print_r($eeSFL_BASE->eeLog); echo '</pre>'; exit;
		
		$eeOutput .= '
		
		<section class="eeSFL_Settings">
		
		
		<div id="uploadFilesDiv" class="eeSettingsTile eeAdminUploadForm">';
		
		// The Upload Form
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-upload-form.php');
		
		$eeOutput .= '</div>
		
		
		<div class="eeSettingsTile">
		
			<div class="eeColInline">';
		
			// If showing just-uploaded files
			if($eeSFL_Uploaded AND $eeSFL_BASE->eeListSettings['UploadConfirm'] == 'YES') { 
				
				$eeOutput .= '
				
				<a href="' . $eeURL . '" class="button eeButton" id="eeSFL_BacktoFilesButton">&larr; ' . __('Back to the Files', 'ee-simple-file-list') . '</a>';
			
			} else {
				
				$eeOutput .= '
				
				<div class="eeColHalfLeft">
			
				<a class="eeHide button eeFlex1" id="eeSFL_UploadFilesButtonSwap">' . __('Cancel Upload', 'ee-simple-file-list') . '</a>
				<a href="#" class="button eeFlex1" id="eeSFL_UploadFilesButton">' . __('Upload Files', 'ee-simple-file-list') . '</a>
				<a href="#" class="button eeFlex1" id="eeSFL_ReScanButton">' . __('Re-Scan Files', 'ee-simple-file-list') . '</a>
				<a href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" class="button eeFlex1" >' . __('Create Folder', 'ee-simple-file-list') . '</a>
				
				</div>
				
				<div class="eeColHalfRight">';
				
				// Check Array and Get File Count
				if(is_array($eeSFL_BASE->eeAllFiles)) { 
					
					$eeFileCount = count($eeSFL_BASE->eeAllFiles);
					
					// Calc Date Last Changed
					$eeArray = array();
					foreach( $eeSFL_BASE->eeAllFiles as $eeKey => $eeFileArray) { $eeArray[] = $eeFileArray['FileDateAdded']; }
					rsort($eeArray); // Most recent at the top	
					
					$eeOutput .= '<small>' . $eeFileCount . ' ' . __('Files', 'ee-simple-file-list') . ' - ' . __('Sorted by', 'ee-simple-file-list') . ' ' . ucwords($eeSFL_BASE->eeListSettings['SortBy']);
					
					if($eeSFL_BASE->eeListSettings['SortBy'] == 'Ascending') { $eeOutput .= ' &uarr;'; } else { $eeOutput .= ' &darr;'; } 
					
					$eeOutput .= '<br />' . 
					__('Last Changed', 'ee-simple-file-list') . ': ' . date_i18n( get_option('date_format'), strtotime( $eeArray[0] ) ) . '</small>';
					
					unset($eeArray);
				
				} else { 
					$eeSFL_BASE->eeAllFiles = array('' => ''); // No files found :-(
				}
				
				$eeOutput .= '</div>';
			}
			
			$eeOutput .= '
			
			</div>
		</div>
		</section>';
		
		$eeSFL_Nonce = wp_create_nonce('eeInclude'); // Security
		include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'ee-list-display.php'); // The File List	
		
			
	
	} elseif($active_tab == 'settings') {
		
		// Sub Tabs
		if( isset( $_GET[ 'subtab' ] ) ) { $active_subtab = sanitize_text_field($_GET['subtab']); } else { $active_subtab = 'list_settings'; }
	    	
    	$eeOutput .= '
    	
    	<h2 class="nav-tab-wrapper">
    	<div class="ee-nav-sub-tabs">';
		
		// List Settings
		$eeOutput .= '<a href="?page=' . eeSFL_BASE_PluginSlug . '&tab=settings&subtab=list_settings" class="nav-tab ';  
		if($active_subtab == 'list_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'list_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('File List Settings', 'ee-simple-file-list') . '</a>';
	    
	    // Uploader Settings
		$eeOutput .= '<a href="?page=' . eeSFL_BASE_PluginSlug . '&tab=settings&subtab=uploader_settings" class="nav-tab ';  
		if($active_subtab == 'uploader_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'uploader_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('File Upload Settings', 'ee-simple-file-list') . '</a>';
	    
	    // Notifications Settings
		$eeOutput .= '<a href="?page=' . eeSFL_BASE_PluginSlug . '&tab=settings&subtab=email_settings" class="nav-tab ';  
		if($active_subtab == 'email_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $active_subtab == 'email_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $active_subtab . '">' . __('Notification Settings', 'ee-simple-file-list') . '</a>';
	    
	    // END Subtabs
	    $eeOutput .= '
	    
	    </div>
	    </h2>
	    
	    <section class="eeSFL_Settings">'; 
	    
		// Sub-Tab Content
		if($active_subtab == 'uploader_settings') {
			
			$eeSFL_Nonce = wp_create_nonce('eeInclude');
			include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-upload-settings.php'); // The Uploader Settings
		
		} elseif($active_subtab == 'email_settings') {
			
			$eeSFL_Nonce = wp_create_nonce('eeInclude');
			include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-email-settings.php'); // The Notifications Settings
		
		} else {
			
			$eeSFL_Nonce = wp_create_nonce('eeInclude');
			include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-list-settings.php'); // The File List Settings			
		}
		
		$eeOutput .= '
		
		</section>';
		
	} elseif($active_tab == 'pro') { // Instructions Tab Display...
			
		// Get the sales page
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-get-pro.php');
	
	
	} elseif($active_tab == 'help') { // Email Support Tab Display...
		
		$eePlugin = eeSFL_PluginName;
			
		// Get the support page
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'support/ee-get-help.php');
	
	
	} else { // Author
					
		// Get the support page
		$eeSFL_Nonce = wp_create_nonce('eeInclude');
		include($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-plugin-author.php');
		
	} // END Tab Content
	
	
	$eeSFL_Nonce = wp_create_nonce('eeInclude');
	include('includes/ee-admin-footer.php');
	
	// Timer
	$eeSFL_Time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	$eeSFL_BASE->eeLog[] = 'Execution Time: ' . round($eeSFL_Time,3);
	
	// Logging
	$eeOutput .= $eeSFL_BASE->eeSFL_WriteLogData(); // Only adds output if DevMode is ON

	// Output the page
	echo $eeOutput;
}

?>