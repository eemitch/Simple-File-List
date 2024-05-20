<?php 
// Simple File List Pro - Copyright 2024
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code are not advised and may not be supported.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

// Font-End Display
function eeSFL_FrontEnd($atts, $content = null) { // Shortcode Usage: [eeSFL]
	
	if(is_admin()) { return; } // Don't execute shortcode on page editor
	
	if(has_filter('wpautop')) {
		remove_filter( 'the_content', 'wpautop' ); // This will break SFL
	}
	
	if( get_option('eeSFL_Registration') == 'NO' ) { return; }
	
	global $eeSFL_UploadFormRun;  // TO-DO - Convert to props or get rid of these
	global $eeSFL, $eeSFL_Environment, $eeSFL_Upload, $eeSFL_Messaging, $eeSFL_VarsForJS;
	global $eeSFL_Pro, $eeSFL_Tasks, $eeSFLS, $eeSFLA, $eeSFLE; // Pro
	
	$eeSFL_Include = wp_create_nonce(eeSFL_Include);
	$eeForceSort = FALSE;
	$eeSFL_Uploaded = FALSE;
	
	$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Shortcode Function Loading ...';
	$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'URL: ' . $eeSFL->eeSFL_GetThisURL();
	
	$eeOutput = '';

	// Over-Riding Shortcode Attributes
	if($atts) {
	
		$atts = shortcode_atts( array( // Use lowercase att names only
			'list' => '1',
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
			'frontmanage' => '', // Allow Front Manage or Not
			'folder' => '', // Folder path from FileListDir
			'showfolder' => '', // LEGACY < 6
			'paged' => '', // eeSFLS - YES or NO to paginate the list
			'filecount' => '', // eeSFLS - Number of files per page
			'search' => '' // eeSFLS - YES or NO to show the search form
		), $atts );
		
		
		// Show the Shortcode in the Log
		$eeShortcode = '[eeSFL';
		$eeShortcodeAtts = array_filter($atts);
		foreach( $eeShortcodeAtts as $eeAtt => $eeValue) { $eeShortcode .= ' ' . $eeAtt . '="' . $eeValue . '"'; }
		$eeShortcode .= ']';
		$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Shortcode: ' . $eeShortcode;
	
		$eeOutput .= '
		<!-- Shortcode: ' . $eeShortcode . ' List Run: #' . $eeSFL->eeListRun . ' -->';
		
		extract($atts);
		
		if($eeSFLA) {
			require_once(WP_PLUGIN_DIR . '/ee-simple-file-list-access/includes/ee-front-end-pre-process.php');
		} elseif($list > 1) { // If eeSFLA is deactivated, other lists won't error
			return;
		} else { 
			if($showlist) { $eeSFL->eeListSettings['ShowList'] = strtoupper($showlist); }
			if($allowuploads) { $eeSFL->eeListSettings['AllowUploads'] = strtoupper($allowuploads); }
		}		
		
		if($style) { $eeSFL->eeListSettings['ShowListStyle'] = strtoupper($style); }
		if($theme) { $eeSFL->eeListSettings['ShowListTheme'] = strtoupper($theme); }
		if($showthumb) { $eeSFL->eeListSettings['ShowFileThumb'] = strtoupper($showthumb); }
		if($showdate) { $eeSFL->eeListSettings['ShowFileDate'] = strtoupper($showdate); }
		if($showsize) { $eeSFL->eeListSettings['ShowFileSize'] = strtoupper($showsize); }
		if($showheader) { $eeSFL->eeListSettings['ShowHeader'] = strtoupper($showheader); }
		if($showactions) { $eeSFL->eeListSettings['ShowFileActions'] = strtoupper($showactions); }
		if($getdesc) { $eeSFL->eeListSettings['GetUploaderDesc'] = strtoupper($getdesc); }
		if($getinfo) { $eeSFL->eeListSettings['GetUploaderInfo'] = strtoupper($getinfo); }
		if($frontmanage) { $eeSFL->eeListSettings['AllowFrontManage'] = strtoupper($frontmanage); }
		
		if($hidename) { $eeSFL_HideName = $hidename; } else { $eeSFL_HideName = FALSE; }
		if($hidetype) { $eeSFL_HideType = strtolower($hidetype); } else { $eeSFL_HideType = FALSE; }
		
		// Force a re-sort of the file list array if a shortcode attribute was used
		if($sortby OR $sortorder) {
			
			if( $sortby != $eeSFL->eeListSettings['SortBy'] OR $sortorder != $eeSFL->eeListSettings['SortOrder'] ) {
				$eeForceSort = TRUE;
				$eeSFL->eeListSettings['SortBy'] = ucwords($sortby);
				$eeSFL->eeListSettings['SortOrder'] = ucwords($sortorder);
			} else {
				$eeForceSort = FALSE;
			}
		}
		
		if(defined('eeSFL_Pro')) {
			if($folder) { $showfolder = $folder; } // Both "folder" and "showfolder" will work.
			if($showfolder) { // LEGACY
				$showfolder = str_replace('//', '/', $showfolder); // Clean up
				$showfolder = str_replace('.', '', $showfolder); 
				$eeSFL->eeShortcodeFolder = $showfolder;
			}
			
			if($paged) { $eeSFL->eeListSettings['EnablePagination'] = strtoupper($paged); }
			if($filecount) { $eeSFL->eeListSettings['FilesPerPage'] = $filecount; }
			if($search) { $eeSFL->eeListSettings['EnableSearch'] = strtoupper($search); }
		}
	}
	
	$eeDependents = array('jquery'); // Requires jQuery
	
	if($eeSFL->eeListRun == 1) {
		
		if($eeSFL->eeListSettings['AllowFrontManage'] != 'NO') {
			wp_enqueue_script('ee-simple-file-list-js-edit-file', eeSFL_PluginURL . 'js/ee-edit-file.js', $eeDependents, eeSFL_CacheBuster, TRUE);
		}
		
		if(defined('eeSFL_Pro') AND $eeSFL->eeListSettings['AllowFrontManage'] != 'NO') {
			wp_enqueue_script('ee-simple-file-list-pro', eeSFL_PluginURL . 'pro/js/ee-pro.js', $eeDependents, eeSFL_CacheBuster, TRUE);
		}
		
		// List Theme CSS
		if($eeSFL->eeListSettings['ShowListTheme'] == 'DARK') {
			wp_enqueue_style('ee-simple-file-list-css-theme-dark');
		} elseif($eeSFL->eeListSettings['ShowListTheme'] == 'LIGHT') {		
			wp_enqueue_style('ee-simple-file-list-css-theme-light');
		}
		
		// List Style CSS
		if($eeSFL->eeListSettings['ShowListStyle'] == 'FLEX') { 	
			wp_enqueue_style('ee-simple-file-list-css-flex');		
		} elseif($eeSFL->eeListSettings['ShowListStyle'] == 'TILES') {    	
			wp_enqueue_style('ee-simple-file-list-css-tiles');		
		} else {		
			wp_enqueue_style('ee-simple-file-list-css-table');
		}
		
		// Upload Check
		$eeSFL_Uploaded = $eeSFL_Upload->eeSFL_UploadCheck($eeSFL->eeListRun);
	
	}
	
	if(empty($allowuploads) AND $eeSFLA) { $eeSFL->eeListSettings['AllowUploads'] = $eeSFLA->eeSFLA_UploadsFirewall(); }
	if(empty($showlist) AND $eeSFLA) { $eeSFL->eeListSettings['ShowList'] = $eeSFLA->eeSFLA_ListFirewall(); }
	if(empty($allowfrontmanage) AND $eeSFLA) { $eeSFL->eeListSettings['AllowFrontManage'] = $eeSFLA->eeSFLA_ManangementFirewall(); }
	if($eeSFLA) { $eeSFL->eeListSettings['AllowCopyToList'] = $eeSFLA->eeSFLA_CopyFileFirewall(); }
	
	
	// Begin Front-End List Display ==================================================================
	
	// if($eeSFL->eeListRun == 2) { exit($eeSFL->eeListSettings['ShowList']); }
	
	// Who Can Upload?
	switch ($eeSFL->eeListSettings['AllowUploads']) {
		case 'YES':
			break; // Show It
		case 'USER':
			// Show It If...
			if( $eeSFL->eeEnvironment['wpUserID'] ) { break; } else { $eeSFL->eeListSettings['AllowUploads'] = 'NO'; }
		case 'ADMIN':
			// Show It If...
			if(current_user_can(eeSFL_AdminPrivileges)) { break; } else { $eeSFL->eeListSettings['AllowUploads'] = 'NO'; }
			break;
		default:
			$eeSFL->eeListSettings['AllowUploads'] = 'NO'; // Show Nothing
	}
	
	
	$eeShowUploadForm = FALSE;
	
	if(!$eeSFL_Uploaded AND $eeSFL->eeListSettings['AllowUploads'] != 'NO' AND !$eeSFL_UploadFormRun AND !isset($_POST['eeSFLS_Searching'])) {
		
		wp_enqueue_style('ee-simple-file-list-css-upload');
		wp_enqueue_script('ee-simple-file-list-js-uploader', eeSFL_PluginURL . 'uploader/ee-uploader.js', $eeDependents , eeSFL_CacheBuster, TRUE);
		$eeSFL_UploadFormRun = TRUE;
		$eeShowUploadForm = TRUE;
	}
	
	if($eeShowUploadForm AND $eeSFL->eeListSettings['UploadPosition'] == 'Above') {
		$eeOutput .= $eeSFL_Upload->eeSFL_UploadForm();
	}
	
	// Who Can View the List?
	switch ($eeSFL->eeListSettings['ShowList']) {
		case 'YES':
			break; // Show It
		case 'MAYBE':
			break; // Show It By File (eeSFLA)
		case 'USER':
			// Show It If...
			if( $eeSFL->eeEnvironment['wpUserID'] ) { break; } else { $eeSFL->eeListSettings['ShowList'] = 'NO'; }
		case 'ADMIN':
			// Show It If...
			if(current_user_can(eeSFL_AdminPrivileges)) { break; } else { $eeSFL->eeListSettings['ShowList'] = 'NO'; }
			break;
		default:
			$eeSFL->eeListSettings['ShowList'] = 'NO'; // Show Nothing
	}
	
	if($eeSFL->eeListSettings['ShowList'] != 'NO') {
		
		$eeSFL_Include = wp_create_nonce(eeSFL_Include);
		require_once(eeSFL_PluginDir . 'base/ee-list-display.php');
	}
	
	if($eeShowUploadForm AND $eeSFL->eeListSettings['UploadPosition'] == 'Below') {
		$eeOutput .= $eeSFL_Upload->eeSFL_UploadForm();
	}
	
	// Smooth Scrolling is AWESOME!
	if( isset($_REQUEST['ee']) AND $eeSFL->eeListSettings['SmoothScroll'] == 'YES' ) { 
		$eeOutput .= '<script>eeSFL_ScrollToIt();</script>'; }
	
	$eeSFL->eeListRun++;
	
	$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'SFL Display Completed';
	
	$eeOutput .= $eeSFL->eeSFL_WriteLogData();
	
	// Give it back
	$eeSFL->eeAllFiles = array();
	$eeSFL->eeDisplayFiles = array();
	
	return $eeOutput; // Output the Display
}

?>