<?php 
// Simple File List Pro - Copyright 2024
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code are not advised and may not be supported.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

// Admin-Side Display
function eeSFL_BackEnd() {
	
	if( !is_admin() ) { return FALSE; } // If Not, See Ya Later Alligator
	$eeSFL_Include = wp_create_nonce(eeSFL_Include);
	
	global $eeSFL, $eeSFL_Environment, $eeSFL_Thumbs, $eeSFL_Upload, $eeSFL_Messaging;
	global $eeSFL_Pro, $eeSFL_Tasks, $eeSFLS, $eeSFLA, $eeSFLE; // Pro
	
	$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Loading Back-End Display ...';
	
	// Back-End Language Setup
	if(isset($_POST['eeLangOptionSubmit']) AND check_admin_referer('eeSFL_NonceLang', eeSFL_Nonce)) {
		if($_POST['eeLangOption'] == 'en_US') { update_option('eeSFL_Lang', 'en_US'); } 
			else { delete_option('eeSFL_Lang'); }
	}
	$eeSFL->eeLocaleSetting = get_option('eeSFL_Lang');
	
	if(isset($_REQUEST['eeFolder'])) { $eeSFL->eeCurrentFolder = sanitize_text_field($_REQUEST['eeFolder']); }
	
	$eeForceSort = FALSE; // Only used in shortcode
	$eeConfirm = FALSE;
	
	if($eeSFLA) { 
	    if( !get_option('eeSFLA_Settings') OR !isset($eeSFL->eeListSettings['Mode']) ) { // Check for this extension's first run
		    require_once(WP_PLUGIN_DIR . '/' . eeSFLA_PluginSlug . '/includes/eeSFLA_Installing.php'); // Show the List Mode Dialog
		    return;
		}
	}
	
	// The Admin Header
	require_once(eeSFL_PluginDir . 'base/ee-back-end-header.php');
	
	// Upsell to Pro
	if(defined('eeSFL_Base')) {
		if( is_admin() AND !$_POST AND empty($eeSFL->eeLog['messages']) ) {
			$eeSFL->eeLog['messages'][] = $eeUpSell;
		}
	}
	
	
	// New Feature Notice
	// if( get_option('eeSFL_Confirm') AND isset($eeSFL->eeListSettings['AllowFrontSend']) ) {
	// 	
	// 	if(!$eeSFLE AND $eeSFL->eeListSettings['AllowFrontSend'] == 'YES') {
	// 		
	// 		$eeDialog = '<p><strong>' . __('File Sending by Email has been removed to a separate extension plugin.', 'ee-simple-file-list') . ' ' . 
	// 		__('If you want to keep this feature, please download and install the new extension.', 'ee-simple-file-list') . '</p>
	// 		<a class="button" href="https://account.simplefilelist.com/files/download.php?file=ee-simple-file-list-email">' . __('Free Download', 'ee-simple-file-list') . '</a> 
	// 		 <a class="button" href="#" id="eeSFL_ConfirmDismiss">' . __('Dismiss', 'ee-simple-file-list') . '</a></strong>';
	// 	
	// 		 $eeSFL->eeLog['warnings'][] = $eeDialog;
	// 	
	// 	} else {
	// 		
	// 		unset( $eeSFL->eeListSettings['AllowFrontSend'] );
	// 		unset( $eeSFL->eeListSettings['BccFileSender'] );
	// 		update_option('eeSFL_Settings_' . $eeSFL->eeListID, $eeSFL->eeListSettings);
	// 		delete_option('eeSFL_Confirm');
	// 	}
	// }
	
	
	// TABS -------------
	
	// Get the new tab's query string value. We will only use values to display tabs that we are expecting.
	if( isset( $_GET[ 'tab' ] ) ) { $eeActiveTab = esc_js(sanitize_text_field($_GET[ 'tab' ])); } else { $eeActiveTab = 'list'; }
	
	$eeOutput .= '
	<h2 class="nav-tab-wrapper">';
	
	// Main Tabs -------
	
	// File List
	$eeOutput .= '
	<span class="nav-tab-wrapper-left">';
	
	if($eeSFLA) {
		$eeOutput .= '
		<a href="?page=' . eeSFL_PluginSlug . '&tab=access" class="nav-tab ';   
		if($eeActiveTab == 'access') {$eeOutput .= ' eeActiveTab '; }  
		$eeActiveTab == 'access' ? 'nav-tab-active' : ''; 
		$eeOutput .= $eeActiveTab . '">' . __('All File Lists', 'ee-simple-file-list') . '</a>';
	}
	
	if($eeActiveTab == 'list' OR $eeActiveTab == 'settings') {

		$eeOutput .= '
		<a href="?page=' . eeSFL_PluginSlug . '&tab=list&eeListID=' . $eeSFL->eeListID . '" class="nav-tab ';  
		if($eeActiveTab == 'list') {$eeOutput .= ' eeActiveTab '; }    
	    $eeActiveTab == 'list' ? 'nav-tab-active' : '';
	    $eeOutput .= $eeActiveTab . '">';
	    
	    if($eeSFLA) { $eeOutput .= stripslashes($eeSFL->eeListSettings['ListTitle']); } 
	    	else { $eeOutput .= __('File List', 'ee-simple-file-list'); }
	    
	    $eeOutput .= '</a>';
	    
	    // Settings
	    $eeOutput .= '
	    <a href="?page=' . eeSFL_PluginSlug . '&tab=settings&eeListID=' . $eeSFL->eeListID . '" class="nav-tab ';   
		if($eeActiveTab == 'settings') {$eeOutput .= ' eeActiveTab '; }  
	    $eeActiveTab == 'settings' ? 'nav-tab-active' : ''; 
	    $eeOutput .= $eeActiveTab . '">' . __('List Settings', 'ee-simple-file-list') . '</a>';
    
    } elseif($eeSFLA) {
	    
	    $eeOutput .= '
		<a href="?page=' . eeSFL_PluginSlug . '&tab=create" class="nav-tab ';   
		if($eeActiveTab == 'create') {$eeOutput .= ' eeActiveTab '; }  
		$eeActiveTab == 'create' ? 'nav-tab-active' : ''; 
		$eeOutput .= $eeActiveTab . '">' . __('Create List', 'ee-simple-file-list') . '</a>';
	    
	    $eeOutput .= '
		<a href="?page=' . eeSFL_PluginSlug . '&tab=access_settings" class="nav-tab ';   
		if($eeActiveTab == 'access_settings') {$eeOutput .= ' eeActiveTab '; }  
		$eeActiveTab == 'access_settings' ? 'nav-tab-active' : ''; 
		$eeOutput .= $eeActiveTab . '">' . __('Access Settings', 'ee-simple-file-list') . '</a>';
    }
    
    $eeOutput .= '
    </span>
    <span class="nav-tab-wrapper-right">';
	
	// Extensions
	// if(defined('eeSFL_Pro')) {
    // 	$eeOutput .= '
	// 	<a href="?page=' . eeSFL_PluginSlug . '&tab=settings&subtab=extension_settings&eeListID=' . $eeSFL->eeListID . '" class="nav-tab tabSupport ';   
	// 	if($eeActiveTab == 'extensions') {$eeOutput .= '  eeActiveTab '; }  
	// 	$eeActiveTab == 'extensions' ? 'nav-tab-active' : ''; 
	// 	$eeOutput .= $eeActiveTab . '">' . __('Pro Extensions', 'ee-simple-file-list') . '</a>';
	// }
	
	// Me
	$eeOutput .= '
	<a href="?page=' . eeSFL_PluginSlug . '&tab=author" class="nav-tab ';   
	if($eeActiveTab == 'author') {$eeOutput .= ' eeActiveTab '; }  
    $eeActiveTab == 'author' ? 'nav-tab-active' : ''; 
    $eeOutput .= $eeActiveTab . '">' . __('Author', 'ee-simple-file-list') . '</a>';
    
    // Link to Support Form
    $eeOutput .= '
    <a href="'. eeSFL_PluginSupportPage . '" class="nav-tab" target="_blank">' . __('Get Help', 'ee-simple-file-list') . ' &rarr;</a>
    </span>
	
	</h2>'; // END Main Tabs -------------------
    
    if($eeSFLA AND $eeActiveTab == 'settings') {
	    $eeOutput .= '<p id="eeSFLA_ListSettingsTitle">'. stripslashes($eeSFL->eeListSettings['ListTitle']) . ' | ' . __('Settings', 'ee-simple-file-list') . '</p>';
    }
     
    // Tab Content =============================================================
    
	if($eeSFLA AND $eeActiveTab == 'access') {
		require_once(WP_PLUGIN_DIR . '/' . eeSFLA_PluginSlug . '/eeSFLA_AllLists.php');
	} elseif($eeSFLA AND $eeActiveTab == 'create') {
		require_once(WP_PLUGIN_DIR . '/' . eeSFLA_PluginSlug . '/includes/eeSFLA_CreateListDisplay.php');
	} elseif($eeSFLA AND $eeActiveTab == 'access_settings') {
		require_once(WP_PLUGIN_DIR . '/' . eeSFLA_PluginSlug . '/includes/eeSFLA_GeneralSettings.php');
	} elseif($eeActiveTab == 'list') {
	
		// Upload Check
		$eeSFL_Uploaded = $eeSFL_Upload->eeSFL_UploadCheck($eeSFL->eeListRun);
		
		if( empty($eeSearchResultCount) ) {
		
			$eeOutput .= '
			<section class="eeSFL_Settings">
			<div id="uploadFilesDiv" class="eeSettingsTile eeAdminUploadForm">';
			
			// The Upload Form
			$eeOutput .= $eeSFL_Upload->eeSFL_UploadForm();
			
			$eeOutput .= '
			</div>
			
			<div class="eeSettingsTile">
			<div class="eeColInline">';
		
			// If showing just-uploaded files
			if($eeSFL_Uploaded) { 
				
				if(empty($eeSFL->eeCurrentFolder) AND isset($_REQUEST['eeFolder'])) { 
					$eeSFL->eeCurrentFolder = sanitize_text_field(urldecode($_REQUEST['eeFolder'])) . '/';
				}
				
				$eeOutput .= '
				<a href="?page=' . eeSFL_PluginSlug . '&eeListID=' . $eeSFL->eeListID . '&eeFolder=' . substr($eeSFL->eeCurrentFolder, 0, -1) . '" class="button eeButton" id="eeSFL_BacktoFilesButton">&larr; ' . __('Back to the Files', 'ee-simple-file-list') . '</a>';
			
			} else {
				
				$eeOutput .= '
				
				<div class="eeColHalfLeft">
				<a class="eeHide button eeFlex1" id="eeSFL_UploadFilesButtonSwap">' . __('Cancel Upload', 'ee-simple-file-list') . '</a>
				<a href="#" class="button eeFlex1" id="eeSFL_UploadFilesButton">' . __('Upload Files', 'ee-simple-file-list') . '</a>
				<a href="?page=' . eeSFL_PluginSlug . '&eeListID=' . $eeSFL->eeListID . '&eeFolder=' . $eeSFL->eeCurrentFolder . '&eeReScan=1" class="button eeFlex1" id="eeSFL_ReScanButton">' . __('Re-Scan Files', 'ee-simple-file-list') . '</a>
				</div>
				
				<div class="eeColHalfRight">';
				
				if(defined('eeSFL_Pro')) {
					$eeSFL_Pro->eeSFL_GetFileList($eeForceSort); // Maybe Scan the Disk
				} else {
					$eeSFL->eeSFL_UpdateFileListArray(1); // Scan the Disk
				}
				
				// Check Array and Get File Count
				if( !empty($eeSFL->eeAllFiles) ) { 
					
					if(defined('eeSFL_Pro')) {
						$eeSFL_Pro->eeSFL_CountFilesAndFolders();
					} else {
						$eeSFL->eeSFL_CountFiles();
					}

					// Calc Date Last Changed
					$eeArray = array();
					foreach( $eeSFL->eeAllFiles as $eeKey => $eeFileArray) { $eeArray[] = $eeFileArray['FileDateAdded']; }
					rsort($eeArray); // Most recent at the top	
					
					$eeOutput .= '
					<small>';
					
					if($eeSFLA) {
						$eeOutput .= '<strong>' . $eeSFL->eeListSettings['ListTitle'] . '</strong><br />';
						$eeOutput .= __('List Mode', 'ee-simple-file-list') . ': ' . $eeSFL->eeListSettings['Mode'] . '<br />';
					}
					
					$eeOutput .= $eeSFL->eeFileCount . ' ' . __('Files', 'ee-simple-file-list') . ' &amp; ' . $eeSFL->eeFolderCount . ' ' . __('Folders', 'ee-simple-file-list') .  ' - ' . __('Sorted by', 'ee-simple-file-list') . ' ' . ucwords($eeSFL->eeListSettings['SortBy']);
					
					if($eeSFL->eeListSettings['SortOrder'] == 'Ascending') { $eeOutput .= ' &uarr;'; } else { $eeOutput .= ' &darr;'; } 
					
					if(isset($eeArray[0])) {
						$eeOutput .= '<br />' . 
							__('Last Changed', 'ee-simple-file-list') . ': ' . date_i18n( get_option('date_format'), strtotime( $eeArray[0] ) ) . '</small>';
					}
					
					unset($eeArray);
				
				} else {
					$eeSFL->eeAllFiles = array();
				}
				
				$eeOutput .= '
				</div>';
			}
			
			$eeOutput .= '
				</div></div>	
			</section>';
		}
		
		require_once(eeSFL_PluginDir . 'base/ee-list-display.php'); // The File List
		
		$eeSFL->eeAllFiles = array();
		$eeSFL->eeDisplayFiles = array();	
	
	} elseif($eeActiveTab == 'settings') {
		
		// Sub Tabs
		if( isset( $_GET[ 'subtab' ] ) ) { $eeActiveSubTab = esc_js(sanitize_text_field($_GET['subtab'])); } 
			else { if($eeSFLA) {  $eeActiveSubTab = 'list_access'; } else { $eeActiveSubTab = 'list_settings'; } }
    	
    	$eeOutput .= '
    	
    	<h2 class="nav-tab-wrapper">
    	<span class="ee-nav-sub-tabs">';
		
		// Extension Check
		if($eeSFLA AND defined('eeSFL_Pro')) {
			if( $eeSFL->eeListSettings['MaxSize'] ) {
				$eeOutput .= '<a href="?page=' . eeSFL_PluginSlug . '&tab=settings&subtab=list_access&eeListID=' . $eeSFL->eeListID . '" class="nav-tab ';  
				if($eeActiveSubTab == 'list_access') {$eeOutput .= '  eeActiveTab ';}    
			    $eeActiveSubTab == 'list_access' ? 'nav-tab-active' : '';    
			    $eeOutput .= $eeActiveSubTab . '">' . __('List Access', 'ee-simple-file-list') . '</a>';
			} else {
				$eeSFLA = FALSE;
			}
		}
		
		// List Settings
		$eeOutput .= '<a href="?page=' . eeSFL_PluginSlug . '&tab=settings&subtab=list_settings&eeListID=' . $eeSFL->eeListID . '" class="nav-tab ';  
		if($eeActiveSubTab == 'list_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $eeActiveSubTab == 'list_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $eeActiveSubTab . '">' . __('File List', 'ee-simple-file-list') . '</a>';
	    
	    // Upload Settings
		$eeOutput .= '<a href="?page=' . eeSFL_PluginSlug . '&tab=settings&subtab=uploader_settings&eeListID=' . $eeSFL->eeListID . '" class="nav-tab ';  
		if($eeActiveSubTab == 'uploader_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $eeActiveSubTab == 'uploader_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $eeActiveSubTab . '">' . __('File Upload', 'ee-simple-file-list') . '</a>';
	    
	    // Notification Settings
		$eeOutput .= '<a href="?page=' . eeSFL_PluginSlug . '&tab=settings&subtab=email_settings&eeListID=' . $eeSFL->eeListID . '" class="nav-tab ';  
		if($eeActiveSubTab == 'email_settings') {$eeOutput .= '  eeActiveTab ';}    
	    $eeActiveSubTab == 'email_settings' ? 'nav-tab-active' : '';    
	    $eeOutput .= $eeActiveSubTab . '">' . __('Notification', 'ee-simple-file-list') . '</a>';
	    
	    // Extension Settings
		if(defined('eeSFL_Pro')) {	
			$eeOutput .= '<a href="?page=' . eeSFL_PluginSlug . '&tab=settings&subtab=extension_settings&eeListID=' . $eeSFL->eeListID . '" class="nav-tab ';  
			if($eeActiveSubTab == 'extension_settings') {$eeOutput .= '  eeActiveTab ';}    
			$eeActiveSubTab == 'extension_settings' ? 'nav-tab-active' : '';    
			$eeOutput .= $eeActiveSubTab . '">' . __('Extensions', 'ee-simple-file-list') . '</a>';
		}
		
	    // Tools
		$eeOutput .= '<a href="?page=' . eeSFL_PluginSlug . '&tab=settings&subtab=tools&eeListID=' . $eeSFL->eeListID . '" class="nav-tab ';  
		if($eeActiveSubTab == 'tools') {$eeOutput .= '  eeActiveTab ';}    
    	$eeActiveSubTab == 'tools' ? 'nav-tab-active' : '';    
    	$eeOutput .= $eeActiveSubTab . '">' . __('Tools', 'ee-simple-file-list') . '</a>';

	    $eeOutput .= '
	    </span>
	    </h2>
	    <section class="eeSFL_Settings">';
	    
		if($eeSFLA AND $eeActiveSubTab == 'list_access') { // Extension Check
			require_once(WP_PLUGIN_DIR . '/' . eeSFLA_PluginSlug . '/includes/eeSFLA_ListAccessSettingsDisplay.php');
		} elseif($eeActiveSubTab == 'uploader_settings') {
			require_once(eeSFL_PluginDir . 'base/ee-upload-settings.php'); // The Uploader Settings
		} elseif($eeActiveSubTab == 'email_settings') {
			require_once(eeSFL_PluginDir . 'base/ee-email-settings.php'); // The Notifications Settings
		} elseif(defined('eeSFL_Pro') AND $eeActiveSubTab == 'extension_settings') {
			require_once(eeSFL_PluginDir . 'pro/ee-extension-settings.php'); // Extension Settings
		} elseif($eeActiveSubTab == 'tools') { // Tools	
			require_once(eeSFL_PluginDir . 'base/ee-plugin-tools.php');
		}  else {
			require_once(eeSFL_PluginDir . 'base/ee-list-settings.php'); // The File List Settings
		}
		
		$eeOutput .= '
		</section>';
		
	} elseif(defined('eeSFL_Pro') AND $eeActiveTab == 'extensions') {
		require_once(eeSFL_PluginDir . 'base/ee-plugin-extensions.php');
	} else {
		require_once(eeSFL_PluginDir . 'base/ee-plugin-author.php');
	} // END Tab Content
	
	
	require_once(eeSFL_PluginDir . 'base/ee-back-end-footer.php');
	
	$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Admin SFL Display Completed';
		
	$eeOutput .= $eeSFL->eeSFL_WriteLogData(); // Only adds output if DevMode is ON

	// Output the page
	echo $eeOutput;
}

?>