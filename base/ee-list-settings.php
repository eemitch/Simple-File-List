<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

// Check for POST and Nonce
if(!empty($_POST['eePost']) AND check_admin_referer('eeSFL_Nonce', eeSFL_Nonce)) {
	
	$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Updating the List Settings';
	
	// List Title
	$eeString = sanitize_text_field($_POST['eeListTitle']);
	if($eeString) { $eeSFL->eeListSettings['ListTitle'] = $eeString; }
	
	if($eeSFLA) {
		require_once(WP_PLUGIN_DIR . '/' . eeSFLA_PluginSlug . '/includes/eeSFLA_ListSettingsProcess.php');
	} elseif( isset($_POST['eeFileListDir']) ) {
	
		if( $_POST['eeFileListDir'] != $eeSFL->eeListSettings['FileListDir'] ) {
			
			$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Changing the File List Directory';
			
			$eeFileListDir = eeSFL_ValidateFileListDir($_POST['eeFileListDir']); // Sanitize and Validate the Path
			
			if($eeFileListDir) {
				
				if( $eeSFL_Environment->eeSFL_FileListDirCheck($eeFileListDir) ) { // Check / Create the Dir
			
					$eeSFL->eeListSettings['FileListDir'] = $eeFileListDir;
					
				} else {
					
					$eeSFL->eeLog['warnings'][] = $eeSFL_DirCheck;
					$eeSFL->eeLog['warnings'][] = __('Cannot create the file directory. Reverting to default.', 'ee-simple-file-list');
					$eeSFL->eeListSettings['FileListDir'] = $eeSFL->eeFileListDefaultDir;
				}
				
			} else {
				
				$eeSFL->eeLog['warnings'][] = __('Not Saved', 'ee-simple-file-list');
				$eeSFL->eeLog['warnings'][] = __('Choose a different file list directory.', 'ee-simple-file-list');
				$eeSFL->eeListSettings['FileListDir'] = $eeSFL->eeFileListDefaultDir;
			}
		}
		
		if(isset($_POST['eeShowList'])) {
			
			$eeShowList = sanitize_text_field($_POST['eeShowList']);
		
			if($eeShowList == 'YES') { $eeSFL->eeListSettings['ShowList'] = 'YES'; } 
				elseif($eeShowList == 'USER') { $eeSFL->eeListSettings['ShowList'] = 'USER'; } // Show only to logged in users
				 elseif($eeShowList == 'ADMIN') { $eeSFL->eeListSettings['ShowList'] = 'ADMIN'; } // Show only to logged in Admins
					else { $eeSFL->eeListSettings['ShowList'] = 'NO'; }
					
			$eeSFL_Environment->eeSFL_LimitDirAccess($eeSFL->eeListSettings['ShowList']);
		} 
		
		if(isset($_POST['eeAdminRole'])) {
		
			if($_POST['eeAdminRole'] == '1') { $eeSFL->eeListSettings['AdminRole'] = '1'; } 
				elseif($_POST['eeAdminRole'] == '3') { $eeSFL->eeListSettings['AdminRole'] = '3'; } 
					elseif($_POST['eeAdminRole'] == '4') { $eeSFL->eeListSettings['AdminRole'] = '4'; } 
						elseif($_POST['eeAdminRole'] == '5') { $eeSFL->eeListSettings['AdminRole'] = '5'; }
								else { $eeSFL->eeListSettings['AdminRole'] = '2'; } // Default to Contributors				
		}	
	}
	
		
	// YES/NO Checkboxes
	$eeCheckboxes = array(
		'ShowFileThumb'
		,'ShowFileDate'
		,'ShowFileSize'
		,'ShowFileOpen'
		,'ShowFileDownload'
		,'ShowFileCopyLink'
		,'ShowFileDesc'
		,'ShowHeader'
		,'SmoothScroll'
		,'ShowSubmitterInfo'
		,'PreserveName'
		,'ShowFileExtension'
		,'GenerateImgThumbs'
		,'GeneratePDFThumbs'
		,'GenerateVideoThumbs'
		,'AllowFolderDownload'
		,'AllowBulkFileDownload'
		,'ShowBreadCrumb'
		,'FoldersFirst'
		,'ShowFolderSize',
		'AudioEnabled',
		'AllowFrontManage'
	);
	
	// if(!$eeSFLA) { $eeCheckboxes[] = 'AllowFrontManage'; } // Otherwise this moves to the File Access Settings tab
	
	foreach( $eeCheckboxes as $eeTerm ) { // "ee" is added in the function
		$eeSFL->eeListSettings[$eeTerm] = eeSFL_ProcessCheckboxInput($eeTerm);
	}
	
	$eeTextInputs = array(
		'LabelThumb'
		,'LabelName'
		,'LabelDate'
		,'LabelSize'
		,'LabelDesc'
		,'LabelOwner'
		,'AdminRole'
		,'ShowListStyle'
		,'ShowListTheme'
		,'SortBy'
		,'ShowFileDateAs'
		,'AudioHeight'
	);
	
	foreach( $eeTextInputs as $eeTerm ) {
		$eeSFL->eeListSettings[$eeTerm] = eeSFL_ProcessTextInput($eeTerm);
	}
	
	if(!empty($_POST['eeSortOrder'])) {
		$eeSFL->eeListSettings['SortOrder'] = 'Descending';
	} else {
		$eeSFL->eeListSettings['SortOrder'] = 'Ascending';
	}
	
	if( defined('eeSFL_Pro') ) { require_once(eeSFL_PluginDir . 'pro/ee-list-settings-display-process-pro.php'); }
	
	// Update DB
	if( empty($eeSFL->eeLog['errors']) AND empty($eeSFL->eeLog['warnings']) ) {
	
		// Sort for Sanity
		ksort($eeSFL->eeListSettings);
		
		update_option('eeSFL_Settings_' . $eeSFL->eeListID, $eeSFL->eeListSettings);
		
		$eeSFL->eeSFL_UpdateFileListArray(); // Re-Populate $eeSFL->eeAllFiles
		
		$eeSFL->eeLog['messages'][] = __('List Settings Saved', 'ee-simple-file-list');
	
	}
}

// Settings Display =========================================

$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Loading: List Settings';
	
// User Messaging
$eeOutput .= $eeSFL_Messaging->eeSFL_ResultsNotification();

// Begin the Form	
$eeOutput .= '

<form action="' . $eeSFL->eeSFL_GetThisURL() . '" method="post" id="eeSFL_Settings">
<input type="hidden" name="eePost" value="TRUE" />
<input type="hidden" name="eeListID" value="' . $eeSFL->eeListID . '" />';	
		
$eeOutput .= wp_nonce_field('eeSFL_Nonce', eeSFL_Nonce, TRUE, FALSE);

$eeOutput .= '<div class="eeColInline eeSettingsTile">
				
	<div class="eeColHalfLeft">
	
		<h1>' . __('File List Settings', 'ee-simple-file-list') . '</h1>
		<a class="" href="https://simplefilelist.com/file-list-settings/" target="_blank">' . __('Instructions', 'ee-simple-file-list') . '</a>
	
	</div>
	
	<div class="eeColHalfRight">
	
		<input class="button" type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" />
	
	</div>

</div>
		
<div class="eeColFull eeSettingsTile">
		
	<h2>' . __('List Location', 'ee-simple-file-list') . '</h2>
		
		<fieldset>
		
		<p><label class="eeBlock" for="eeListTitle">' . __('File List Name', 'ee-simple-file-list-access') . '</label>
		<input type="text" name="eeListTitle" value="' . esc_textarea(stripslashes($eeSFL->eeListSettings['ListTitle'])) . '" class="eeFullWidth" id="eeListTitle" /></p>
		<div class="eeNote">' . __('The name of this file list.', 'ee-simple-file-list-access') . '</div>
		
		</fieldset>
		<fieldset>
		
		<p><label class="eeBlock" for="eeFileListDir">' . __('File List Directory', 'ee-simple-file-list') . '</label>';
				
		if($eeSFLA) {		
			
			$eeOutput .= '
			
			<input class="eeFullWidth" disabled="disabled" type="text" name="eeFileListDirDisabled" value="' . esc_textarea($eeSFL->eeListSettings['FileListDir']) . '" id="eeFileListDir" />
			<input type="hidden" name="eeFileListDir" value="' . esc_textarea($eeSFL->eeListSettings['FileListDir']) . '" /></p>';
			
			if($eeSFL->eeListID == 1) {
				$eeOutput .= '<div class="eeNote">' . __('To change this setting, deactivate the plugin', 'ee-simple-file-list') . ' File Access Manager.</div>';
			} else {
				$eeOutput .= '<div class="eeNote">' . __('This cannot be changed. Create a new list instead.', 'ee-simple-file-list') . '</div>';
			}
							
		} else {
			
			$eeOutput .= '
			
			<input class="eeFullWidth" type="text" name="eeFileListDir" value="' . esc_textarea($eeSFL->eeListSettings['FileListDir']) . '" id="eeFileListDir" /></p>
			<div class="eeNote">' . __('This must be relative to your Wordpress home folder.', 'ee-simple-file-list') . ' (ABSPATH)<br />
				* ' . __('Default Location', 'ee-simple-file-list') . ': <em>wp-content/uploads/simple-file-list/</em><br />
				* ' . __('The directory you enter will be created if it does not exist.', 'ee-simple-file-list') . '<br />
				* ' . __('If you change this later, information such as file descriptions will be lost.', 'ee-simple-file-list') . '
			</div>';
		}
	
$eeOutput .= '

</fieldset>

</div>
		

<div class="eeColumns">		
		
	<!-- Left Column -->
	
	<div class="eeColLeft">
	
		<div class="eeSettingsTile">
		
		<h2>' . __('File List Access', 'ee-simple-file-list') . '</h2>';
		
		// This moves to the File Access Settings tab if extension is installed
		if(!$eeSFLA)  {
			
			$eeOutput .= '<fieldset>
			
			<legend>' . __('Front-End Display', 'ee-simple-file-list') . '</legend>
		
			<div><label for="eeShowList">' . __('Show To', 'ee-simple-file-list') . '</label>
			
			<select name="eeShowList" id="eeShowList">
			
				<option value="YES"';
	
				if($eeSFL->eeListSettings['ShowList'] == 'YES') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Everyone', 'ee-simple-file-list') . '</option>
				
				<option value="USER"';
	
				if($eeSFL->eeListSettings['ShowList'] == 'USER') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Only Logged in Users', 'ee-simple-file-list') . '</option>
				
				<option value="ADMIN"';
	
				if($eeSFL->eeListSettings['ShowList'] == 'ADMIN') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Only Logged in Admins', 'ee-simple-file-list') . '</option>
				
				<option value="NO"';
	
				if($eeSFL->eeListSettings['ShowList'] == 'NO') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Hide Completely', 'ee-simple-file-list') . '</option>
			
			</select></div>
			<div class="eeNote">' . __('Determine who you will show the front-side list to.', 'ee-simple-file-list') . '</div>
			
			</fieldset>
			<fieldset>
			
			<legend>' . __('Back-End Access', 'ee-simple-file-list') . '</legend>
			
			<div><label for="eeAdminRole">' . __('Choose Role', 'ee-simple-file-list') . '</label>
			
			<select name="eeAdminRole" id="eeAdminRole">
			
				<option value="1"'; // 1

				if($eeSFL->eeListSettings['AdminRole'] == '1') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Subscribers and Above', 'ee-simple-file-list') . '</option>
				
				
				<option value="2"'; // 2

				if($eeSFL->eeListSettings['AdminRole'] == '2') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Contributers and Above', 'ee-simple-file-list') . '</option>
				
				
				<option value="3"'; // 3

				if($eeSFL->eeListSettings['AdminRole'] == '3') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Authors and Above', 'ee-simple-file-list') . '</option>
				
				
				<option value="4"'; // 4

				if($eeSFL->eeListSettings['AdminRole'] == '4') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Editors and Above', 'ee-simple-file-list') . '</option>
				
				
				<option value="5"'; // 5

				if($eeSFL->eeListSettings['AdminRole'] == '5') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Admins Only', 'ee-simple-file-list') . '</option>
			
			</select></div>
			
			<div class="eeNote">' . __('Determine who can access the back-side settings.', 'ee-simple-file-list') . '</div>
			
			</fieldset>
			
			<fieldset>
		
			<legend>' . __('Front-End Management', 'ee-simple-file-list') . '</legend>
			
			<div><label for="eeAllowFrontManage">' . __('Allow', 'ee-simple-file-list') . '</label>
			<input type="checkbox" name="eeAllowFrontManage" value="YES" id="eeAllowFrontManage"';
			
			if( $eeSFL->eeListSettings['AllowFrontManage'] == 'YES') { $eeOutput .= ' checked="checked"'; }
			
			$eeOutput .= ' /></div>
			
			<div class="eeNote">' . __('Allow file deletion, file renaming, editing descriptions and dates.', 'ee-simple-file-list') . '</div>
			
			</fieldset>';
				
		} else {
			
			$eeOutput .= '
			
			<p>' . __('These settings have moved to the List Access Settings tab.', 'ee-simple-file-list') . '</p> 
			<a class="button" href="' . $eeSFL->eeSFL_GetThisURL(FALSE) . '?page=ee-simple-file-list-pro&tab=settings&subtab=list_access&eeListID=' . $eeSFL->eeListID . '">' . __('Go There', 'ee-simple-file-list') . '</a>';
		}
		
		
		
		
		$eeOutput .= '</div>
		
		<div class="eeSettingsTile">
		
		<h2>' . __('File List Style', 'ee-simple-file-list') . '</h2>
	
		
		<fieldset>
		<legend>File List Type</legend>
		
		<p><label for="eeShowListStyle">' . __('Style', 'ee-simple-file-list') . '</label>
		
		<select name="eeShowListStyle" id="eeShowListStyle">
		
			<option value="TABLE"';

			if($eeSFL->eeListSettings['ShowListStyle'] == 'TABLE') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Standard Table Display', 'ee-simple-file-list') . '</option>
			
			<option value="TILES"';

			if($eeSFL->eeListSettings['ShowListStyle'] == 'TILES') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Tiles Displayed in Columns', 'ee-simple-file-list') . '</option>
			
			<option value="FLEX"';

			if($eeSFL->eeListSettings['ShowListStyle'] == 'FLEX') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Flexible List Display', 'ee-simple-file-list') . '</option>
		
		</select></p>
		<div class="eeNote">' . __('Choose the style of the file list: Table, Tiles or Flex.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		
		<fieldset>
		<legend>File List Theme</legend>
		
		<p><label for="eeShowListTheme">' . __('Show', 'ee-simple-file-list') . '</label>
		
		<select name="eeShowListTheme" id="eeShowListTheme">
		
			<option value="LIGHT"';

			if($eeSFL->eeListSettings['ShowListTheme'] == 'LIGHT') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Light Theme', 'ee-simple-file-list') . '</option>
			
			<option value="DARK"';

			if($eeSFL->eeListSettings['ShowListTheme'] == 'DARK') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Dark Theme', 'ee-simple-file-list') . '</option>
			
			<option value="NONE"';

			if($eeSFL->eeListSettings['ShowListTheme'] == 'NONE') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('No Theme', 'ee-simple-file-list') . '</option>
		
		</select></p>
		<div class="eeNote">' . __('Choose the color theme of the file list', 'ee-simple-file-list') . ': Light, Dark, or None.'  . __('This will rely upon your theme colors', 'ee-simple-file-list') . '</div>
		
		</fieldset>

		
		</div>
	
		
		
		
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('File Sorting and Order', 'ee-simple-file-list') . '</h2>	
			
		<p><label for="eeSortList">' . __('Sort By', 'ee-simple-file-list') . ':</label>
		
		<select name="eeSortBy" id="eeSortList">
		
			<option value="Name"';
			
			if($eeSFL->eeListSettings['SortBy'] == 'Name') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('File Name', 'ee-simple-file-list') . '</option>
			
			
			<option value="Added"';
			
			if($eeSFL->eeListSettings['SortBy'] == 'Added') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('Date File Added', 'ee-simple-file-list') . '</option>
			
			
			<option value="Changed"';
			
			if($eeSFL->eeListSettings['SortBy'] == 'Changed') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('Date File Changed', 'ee-simple-file-list') . '</option>
			
			
			<option value="Size"';
			
			if($eeSFL->eeListSettings['SortBy'] == 'Size') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('File Size', 'ee-simple-file-list') . '</option>
			
			
			<option value="Random"';
			
			if($eeSFL->eeListSettings['SortBy'] == 'Random') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('Random', 'ee-simple-file-list') . '</option>
		
		</select></p>
			
		<p><label for="eeSortOrder">' . __('Reverse Order', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeSortOrder" value="Descending" id="eeSortOrder"';
		
		if( $eeSFL->eeListSettings['SortOrder'] == 'Descending') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /> &darr; ' . __('Descending', 'ee-simple-file-list') . '</p>
		
		<div class="eeNote">' . __('Sort the list by name, date, file size, or randomly.', 'ee-simple-file-list') . ' ' . __('Check the box to reverse the sort order.', 'ee-simple-file-list') . '</div>	
		
		</div>';
		
		
		
		
		
		$eeOutput .= '<div class="eeSettingsTile">
			
		<h2>' . __('Thumbnail Generation', 'ee-simple-file-list') . '</h2>
		
		<p>' . __('You can choose to generate small representative images of large images, PDF files and videos files.', 'ee-simple-file-list') . '</p>
		
		<fieldset>
		
		<p><label for="eeGenerateImgThumbs">' . __('Image Thumbnails', 'ee-simple-file-list') . ':</label>
		
		<input id="eeGenerateImgThumbs" type="checkbox" name="eeGenerateImgThumbs" value="YES"';
		
		$eeSupported = get_option('eeSFL_Supported');
		if( !is_array($eeSupported) ) { $eeSupported = array(); }
		$eeMissing = array();
		
		if( $eeSFL->eeListSettings['GenerateImgThumbs'] == 'YES' ) { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' />
			<div class="eeNote">' . __('Read an image file and create a small thumbnail image.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		
		
		<fieldset>
		
		<p><label for="eeGeneratePDFThumbs">' . __('PDF Thumbnails', 'ee-simple-file-list') . ':</label>
		
		<input id="eeGeneratePDFThumbs" type="checkbox" name="eeGeneratePDFThumbs" value="YES"';
		if( $eeSFL->eeListSettings['GeneratePDFThumbs'] == 'YES' ) { $eeOutput .= ' checked="checked"'; }
		if( !isset($eeSFL->eeEnvironment['thumbsPDF']) OR $eeSFL->eeEnvironment['eeOS'] == 'WINDOWS' ) { $eeOutput .= ' disabled="disabled"'; }
		$eeOutput .= ' /> ';
		
		if( !in_array('ImageMagick' , $eeSupported) ) { 
			$eeMissing[] = __('Image Magick is Not Installed. PDF thumbnails cannot be created.', 'ee-simple-file-list');
		}
		if( !in_array('GhostScript' , $eeSupported) ) { 
			$eeMissing[] = 'GhostScript is Not Installed. PDF thumbnails cannot be created.';
		}
		
		if( $eeSFL->eeEnvironment['eeOS'] == 'WINDOWS' ) { 
			$eeMissing[] .= ' <em>Windows: ' . __('Not yet supported for PDF thumbnails.', 'ee-simple-file-list') . '</em>';
		}
		
		$eeOutput .= '</p>
		<div class="eeNote">' . __('Read a PDF file and create a representative thumbnail image based on the first page.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		
		<p><label for="eeGenerateVideoThumbs">' . __('Video Thumbnails', 'ee-simple-file-list') . ':</label>
		
		<input id="eeGenerateVideoThumbs" type="checkbox" name="eeGenerateVideoThumbs" value="YES"';
		if( $eeSFL->eeListSettings['GenerateVideoThumbs'] == 'YES' ) { $eeOutput .= ' checked="checked"'; }
		if( !isset($eeSFL->eeEnvironment['thumbsVIDEO']) ) { $eeOutput .= ' disabled="disabled"'; }
		$eeOutput .= ' /> '; 
		
		if( !isset($eeSFL->eeEnvironment['thumbsVIDEO']) ) { 
			$eeMissing[] = __('Video thumbnails will not be created because ffMpeg is not Installed.', 'ee-simple-file-list');
		}
				 	 
		$eeOutput .= '</p>
		
		<div class="eeNote">' . __('Read a video file and create a representative thumbnail image at the 1 second mark.', 'ee-simple-file-list') . '</div>';
		
		if(count($eeMissing)) {
			
			$eeOutput .= '
			<br />
			<div class="eeNote">';
			
			foreach( $eeMissing as $eeKey => $eeValue) {
				$eeOutput .= '<small>&rarr; ' . $eeValue . '</small><br />';
			}
			
			$eeOutput .= '</div>';
		}	
			
		$eeOutput .= '</fieldset>
		
		</div>
		
		
		
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Media Player', 'ee-simple-file-list') . '</h2>
		
		<fieldset>
		
			<legend>' . __('Enable Media Player', 'ee-simple-file-list') . '</legend>
			
			<div>
			<label for="eeAudioEnabled">' . __('Enabled', 'ee-simple-file-list') . ':</label>
			<input type="checkbox" name="eeAudioEnabled" id="eeAudioEnabled" value="YES" ';
			
			if($eeSFL->eeListSettings['AudioEnabled'] == 'YES') { $eeOutput .= 'checked="checked"'; }
			
			$eeOutput .= ' />
			</div>
			
			<div class="eeNote">' . __('Show the audio player beneath the file name', 'ee-simple-file-list') . '</div>
			
			
		</fieldset>
			
		<fieldset>
		
			<legend>' . __('Choose the Audio Player Height', 'ee-simple-file-list') . '</legend>	
			
			<div><label for="eeAudioHeight">' . __('Height', 'ee-simple-file-list') . ':</label>
			<input type="number" name="eeAudioHeight" id="eeAudioHeight" value="' . $eeSFL->eeListSettings['AudioHeight'] . '" />
			</div>
			
			<div class="eeNote">' . __('Define the height of the audio player in pixels.', 'ee-simple-file-list') . ' ' . __('Set to zero to ignore this value.', 'ee-simple-file-list') . '</div>
			
		</fieldset>
		
		</div>
		
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Smooth-Scroll', 'ee-simple-file-list') . '</h2>
		
		<p><label for="eeSmoothScroll">' . __('Use Smooth-Scroll', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeSmoothScroll" value="YES" id="eeSmoothScroll"';
		
		if( $eeSFL->eeListSettings['SmoothScroll'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></p>
		
		<div class="eeNote">' . __('Uses a JavaScript effect to scroll down to the top of the list after an action. This can be helpful if the list is not located close to the top of the page.', 'ee-simple-file-list') . '</div>
		
		</div>
		
		
	</div>
	
	
	
	
		
		
	<!-- Right Column -->
	
	<div class="eeColRight">';
	
		if( defined('eeSFL_Pro') ) {
			require_once(eeSFL_PluginDir . 'pro/ee-list-settings-display-pro.php');
		}
		
		$eeOutput .= '<div class="eeSettingsTile">
		
		<h2>' . __('File Actions', 'ee-simple-file-list') . '</h2>
		
		<fieldset>
		<legend>' . __('Show Bulk File Download', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Checkboxes', 'ee-simple-file-list') . '</label>
		<input type="checkbox" name="eeAllowBulkFileDownload" value="YES" id="eeAllowBulkFileDownload"';
		
		if( $eeSFL->eeListSettings['AllowBulkFileDownload'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></div>
		
		<div class="eeNote">' . __('Display checkboxes to allow the user to download multiple files as a ZIP archive.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('Show Open Action', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Action', 'ee-simple-file-list') . '</label>
		<input type="checkbox" name="eeShowFileOpen" value="YES" id="eeShowFileOpen"';
		
		if( $eeSFL->eeListSettings['ShowFileOpen'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></div>
		
		<div class="eeNote">' . __('Display the Open File link. If the browser cannot open the file, it will prompt the user to download.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('Show Download Action', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Action', 'ee-simple-file-list') . '</label>
		<input type="checkbox" name="eeShowFileDownload" value="YES" id="eeShowFileDownload"';
		
		if( $eeSFL->eeListSettings['ShowFileDownload'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></div>
		
		<div class="eeNote">' . __('The browser will prompt the user to download the file.', 'ee-simple-file-list') . '</div>
		
		</fieldset>

		
		
		<fieldset>
		<legend>' . __('Show Copy Action', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Action', 'ee-simple-file-list') . '</label>
		<input type="checkbox" name="eeShowFileCopyLink" value="YES" id="eeShowFileCopyLink"';
		
		if( $eeSFL->eeListSettings['ShowFileCopyLink'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></div>
		
		<div class="eeNote">' . __('Copies the file URL to the user clipboard.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		</div>
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('File List Display', 'ee-simple-file-list') . '</h2>
		
		<fieldset>
		<legend>' . __('File Thumbnail', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileThumb" value="YES" id="eeShowFileThumb"'; 
		if($eeSFL->eeListSettings['ShowFileThumb'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelThumb" value="';
		if( isset($eeSFL->eeListSettings['LabelThumb']) ) { $eeOutput .= esc_textarea(stripslashes($eeSFL->eeListSettings['LabelThumb'])); }
		$eeOutput .= '" /></div>
		
		<div class="eeNote">' . __('Show file thumbnail images.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Name', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input checked disabled type="checkbox" name="eeShowFileName" value="YES" id="eeShowFileName" />
		<input type="text" name="eeLabelName" value="';
		if( isset($eeSFL->eeListSettings['LabelName']) ) { $eeOutput .= esc_textarea(stripslashes($eeSFL->eeListSettings['LabelName'])); }
		$eeOutput .= '" /></div>
		
		<div class="eeNote">' . __('Show file name. This cannot be disabled.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Date', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileDate" value="YES" id="eeShowFileDate"'; 
		if($eeSFL->eeListSettings['ShowFileDate'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input class="eeFortyPercent" type="text" name="eeLabelDate" value="';
		if( isset($eeSFL->eeListSettings['LabelDate'])) { $eeOutput .= esc_textarea(stripslashes($eeSFL->eeListSettings['LabelDate'])); }
		$eeOutput .= '" />
		
		<select name="eeShowFileDateAs" id="eeShowFileDateAs">
			<option value="">' . __('Date Type', 'ee-simple-file-list') . '</option>
			
			<option value="Added"';
			if($eeSFL->eeListSettings['ShowFileDateAs'] == 'Added') { $eeOutput .= ' selected="selected"'; }
			$eeOutput .= '>' . __('Added', 'ee-simple-file-list') . '</option>
			
			<option value="Changed"';
			if($eeSFL->eeListSettings['ShowFileDateAs'] == 'Changed') { $eeOutput .= ' selected="selected"'; }
			$eeOutput .= '>' . __('Changed', 'ee-simple-file-list') . '</option>
		</select></div>
		
		<div class="eeNote">Show the file date, either last changed or when added to the list.</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Size', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileSize" value="YES" id="eeShowFileSize"'; 
		if($eeSFL->eeListSettings['ShowFileSize'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelSize" value="';
		if( isset($eeSFL->eeListSettings['LabelSize']) ) { $eeOutput .= esc_textarea(stripslashes($eeSFL->eeListSettings['LabelSize'])); }
		$eeOutput .= '" /></div>
				
		<div class="eeNote">' . __('Limit the file information to display on the front-side file list. Enter a custom label if needed.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Description', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileDesc" value="YES" id="eeShowFileDesc"'; 
		if($eeSFL->eeListSettings['ShowFileDesc'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelDesc" value="';
		if( isset($eeSFL->eeListSettings['LabelDesc']) ) { $eeOutput .= esc_textarea(stripslashes($eeSFL->eeListSettings['LabelDesc'])); }
		$eeOutput .= '" /></div>
				
		<div class="eeNote">' . __('Show a description of the file, which can include keywords and special characters not allowed within the file name.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Submitter', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowSubmitterInfo" value="YES" id="eeShowSubmitterInfo"'; 
		if($eeSFL->eeListSettings['ShowSubmitterInfo'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelOwner" value="';
		
		// echo '<pre>'; print_r($eeSFL->eeDefaultListSettings); echo '</pre>'; exit;
		
		if( $eeSFL->eeListSettings['LabelOwner'] ) { $eeOutput .= esc_textarea(stripslashes($eeSFL->eeListSettings['LabelOwner'])); }
		$eeOutput .= '" /></div>
				
		<div class="eeNote">' . __('Show the name of the user who uploaded the file on the front-end.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		<fieldset>
		<legend>' . __('Table Header', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowHeader" value="YES" id="eeShowHeader"'; 
		if($eeSFL->eeListSettings['ShowHeader'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' /></div>
				
		<div class="eeNote">' . __('Show or hide the file table header.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Extension', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileExtension" value="YES" id="eeShowFileExtension"'; 
		if($eeSFL->eeListSettings['ShowFileExtension'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' /></div>
				
		<div class="eeNote">' . __('Show or hide the file extension.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('Preserve File Name', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eePreserveName" value="YES" id="eePreserveName"'; 
		if($eeSFL->eeListSettings['PreserveName'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' /></div>
				
		<div class="eeNote">' . __('Files with illegal characters are renamed to ensure good URLs.', 'ee-simple-file-list') . ' ' . 
			__('This setting will preserve and show the original name as the Nice Name.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		</div>
		
	</div>
		
</div>


<div class="eeColInline eeSettingsTile">
				
	<input class="button" type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" />
			
</div>
		
</form>';
	
?>