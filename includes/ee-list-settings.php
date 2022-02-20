<?php // Simple File List Script: ee-list-settings.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_BASE_Log['RunTime'][] = 'Loading List Settings Page ...';

// Check for POST and Nonce
if( isset($_POST['eePost']) AND check_admin_referer( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce')) {	
	
	// List Style
	if(isset($_POST['eeShowListStyle'])) { 
		$eeShowListStyle = sanitize_text_field($_POST['eeShowListStyle']);
		if( $eeShowListStyle == 'Flex' ) { $eeSFL_Settings['ShowListStyle'] = 'Flex'; }
			elseif($eeShowListStyle == 'Tiles') { $eeSFL_Settings['ShowListStyle'] = 'Tiles'; }
				else { $eeSFL_Settings['ShowListStyle'] = 'Table'; }
	}
	
	
	// List Visibility
	if($_POST['eeShowList'] == 'YES') { $eeSFL_Settings['ShowList'] = 'YES'; } 
		elseif($_POST['eeShowList'] == 'USER') { $eeSFL_Settings['ShowList'] = 'USER'; } // Show only to logged in users
			 elseif($_POST['eeShowList'] == 'ADMIN') { $eeSFL_Settings['ShowList'] = 'ADMIN'; } // Show only to logged in Admins
				else { $eeSFL_Settings['ShowList'] = 'NO'; }	

		
		// YES/NO Checkboxes
	$eeCheckboxes = array(
		'AllowFrontManage'
		,'ShowFileThumb'
		,'ShowFileDate'
		,'ShowFileSize'
		,'ShowFileOpen'
		,'ShowFileDownload'
		,'ShowFileCopyLink'
		,'ShowFileDesc'
		,'GetUploaderDesc'
		,'ShowHeader'
		,'SmoothScroll'
		,'ShowSubmitterInfo'
		,'PreserveSpaces'
		,'ShowFileExtension'
		,'GenerateImgThumbs'
		,'GeneratePDFThumbs'
		,'GenerateVideoThumbs'
	);
		
	foreach( $eeCheckboxes as $eeTerm){ // "ee" is added in the function
		
		$eeSFL_Settings[$eeTerm] = eeSFL_BASE_ProcessCheckboxInput($eeTerm);
	}
	
	$eeTextInputs = array(
		'LabelThumb'
		,'LabelName'
		,'LabelDate'
		,'LabelSize'
		,'LabelDesc'
		,'LabelOwner'
	);
	foreach( $eeTextInputs as $eeTerm){
		$eeSFL_Settings[$eeTerm] = eeSFL_BASE_ProcessTextInput($eeTerm);
	}
	
	
	// Sort by Select Box	
	if(isset($_POST['eeSortBy'])) { 
		$eeSFL_Settings['SortBy'] = sanitize_text_field($_POST['eeSortBy']);
		if(!$eeSFL_Settings['SortBy']) { $eeSFL_Settings['SortBy'] = 'Name'; }
	}
	
	// Asc/Desc Checkbox
	if(@$_POST['eeSortOrder'] == 'Descending') { $eeSFL_Settings['SortOrder'] = 'Descending'; }
		else { $eeSFL_Settings['SortOrder'] = 'Ascending'; }
	
	// Show Date Type	
	if(isset($_POST['eeShowFileDateAs'])) { 
		$eeSFL_Settings['ShowFileDateAs'] = sanitize_text_field($_POST['eeShowFileDateAs']);
		if( $eeSFL_Settings['ShowFileDateAs'] == 'Modified' ) { $eeSFL_Settings['ShowFileDateAs'] = 'Modified'; }
			else { $eeSFL_Settings['ShowFileDateAs'] = 'Added';}
	}
	
	
	// Sort for Sanity
	ksort($eeSFL_Settings);
	
	// Update DB
	if( update_option('eeSFL_Settings_1', $eeSFL_Settings) ) {
		$eeConfirm = __('Settings Saved', 'ee-simple-file-list');
		$eeSFL_BASE_Log['RunTime'][] = $eeConfirm;
	} else {
		$eeSFL_BASE_Log['errors'][] = __('The Database Update Failed', 'ee-simple-file-list') . ' :-(';
	}
	
	delete_transient('eeSFL_FileList_1');
}

// Settings Display =========================================
	
if( count($eeSFL_BASE_Log['errors']) ) { 
	$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeSFL_BASE_Log['errors'], 'notice-error');
} elseif( $eeConfirm ) { 
	$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeConfirm, 'notice-success');
}

// Begin the Form	
$eeOutput .= '

<form action="' . admin_url() . '?page=' . $eeSFL_BASE->eePluginSlug . '&tab=settings&subtab=list_settings" method="post" id="eeSFL_Settings">
<input type="hidden" name="eePost" value="TRUE" />';	
		
$eeOutput .= wp_nonce_field( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce', TRUE, FALSE);

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
		
		<p><input class="eeFullWidth" type="text" name="eeFileListDir" value="" placeholder="' . ABSPATH . $eeSFL_Settings['FileListDir'] . '" disabled="disabled" /></p>
		
		<div class="eeNote">' . __('Upgrade to Pro', 'ee-simple-file-list') . '</a> &rarr; ' . __('The Pro Version allows you to define a custom file list directory. It must only be relative to the WordPress home directory.', 'ee-simple-file-list') . '</div>
	
</div>
		

<div class="eeColumns">		
		
	<!-- Left Column -->
	
	<div class="eeColLeft">
	
		<div class="eeSettingsTile">
		
		<h2>' . __('File List Style', 'ee-simple-file-list') . '</h2>
	
		<p><label for="eeShowListStyle">' . __('Display Style Type', 'ee-simple-file-list') . '</label>
		
		<select name="eeShowListStyle" id="eeShowListStyle">
		
			<option value="Table"';

			if($eeSFL_Settings['ShowListStyle'] == 'Table') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Standard Table Display', 'ee-simple-file-list') . '</option>
			
			<option value="Tiles"';

			if($eeSFL_Settings['ShowListStyle'] == 'Tiles') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Tiles Displayed in Columns', 'ee-simple-file-list') . '</option>
			
			<option value="Flex"';

			if($eeSFL_Settings['ShowListStyle'] == 'Flex') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Flexible List Display', 'ee-simple-file-list') . '</option>
		
		</select></p>
		<div class="eeNote">' . __('Determine who you will show the front-side list to.', 'ee-simple-file-list') . '</div>
		
		</div>
	
		
		<div class="eeSettingsTile">
		
		<h2>' . __('File List Access', 'ee-simple-file-list') . '</h2>
	
		<p><label for="eeShowList">' . __('Front-Side Display', 'ee-simple-file-list') . '</label>
		
		<select name="eeShowList" id="eeShowList">
		
			<option value="YES"';

			if($eeSFL_Settings['ShowList'] == 'YES') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Show to Everyone', 'ee-simple-file-list') . '</option>
			
			<option value="USER"';

			if($eeSFL_Settings['ShowList'] == 'USER') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Show to Only Logged in Users', 'ee-simple-file-list') . '</option>
			
			<option value="ADMIN"';

			if($eeSFL_Settings['ShowList'] == 'ADMIN') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Show to Only Logged in Admins', 'ee-simple-file-list') . '</option>
			
			<option value="NO"';

			if($eeSFL_Settings['ShowList'] == 'NO') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Hide the File List Completely', 'ee-simple-file-list') . '</option>
		
		</select></p>
		<div class="eeNote">' . __('Determine who you will show the front-side list to.', 'ee-simple-file-list') . '</div>
		
		</div>
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Front-End Management', 'ee-simple-file-list') . '</h2>
		
		<p><label for="eeAllowFrontManage">' . __('Allow File Management', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeAllowFrontManage" value="YES" id="eeAllowFrontManage"';
		
		if( $eeSFL_Settings['AllowFrontManage'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></p>
		
		<div class="eeNote">' . __('Allow file deletion, file renaming, editing descriptions and dates.', 'ee-simple-file-list') . '</div>
		<div class="eeNote"><a href="https://get.simplefilelist.com/" target="_blank">' .  __('Upgrade to Pro', 'ee-simple-file-list') . '</a> ' . __('Upgrade to Simple File List Pro and add the file access manager extension. This will allow access control for specific users and roles.', 'ee-simple-file-list') . '<br />
		</div>
			
		</div>
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('File Sorting and Order', 'ee-simple-file-list') . '</h2>	
			
		<p><label for="eeSortList">' . __('Sort By', 'ee-simple-file-list') . ':</label>
		
		<select name="eeSortBy" id="eeSortList">
		
			<option value="Name"';
			
			if($eeSFL_Settings['SortBy'] == 'Name') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('File Name', 'ee-simple-file-list') . '</option>
			
			
			<option value="Date"';
			
			if($eeSFL_Settings['SortBy'] == 'Date') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('Date File Added', 'ee-simple-file-list') . '</option>
			
			
			<option value="DateMod"';
			
			if($eeSFL_Settings['SortBy'] == 'DateMod') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('Date File Changed', 'ee-simple-file-list') . '</option>
			
			
			<option value="Size"';
			
			if($eeSFL_Settings['SortBy'] == 'Size') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('File Size', 'ee-simple-file-list') . '</option>
			
			
			<option value="Random"';
			
			if($eeSFL_Settings['SortBy'] == 'Random') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('Random', 'ee-simple-file-list') . '</option>
		
		</select></p>
			
		<p><label for="eeSortOrder">' . __('Reverse Order', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeSortOrder" value="Descending" id="eeSortOrder"';
		
		if( $eeSFL_Settings['SortOrder'] == 'Descending') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /> &darr; ' . __('Descending', 'ee-simple-file-list') . '</p>
		
		<div class="eeNote">' . __('Sort the list by name, date, file size, or randomly.', 'ee-simple-file-list') . ' ' . __('Check the box to reverse the sort order.', 'ee-simple-file-list') . '</div>	
		
		</div>
		
		
		
		<div class="eeSettingsTile">
			
		<h2>' . __('Thumbnail Generation', 'ee-simple-file-list') . '</h2>
		
		<p>' . __('You can choose to generate small representative images of large images, PDF files and videos files.', 'ee-simple-file-list') . '</p>
		
		<fieldset>
		
		<p><label for="eeGenerateImgThumbs">' . __('Image Thumbnails', 'ee-simple-file-list') . ':</label>
		
		<input id="eeGenerateImgThumbs" type="checkbox" name="eeGenerateImgThumbs" value="YES"';
		
		$eeSupported = get_option('eeSFL_Supported');
		if( !is_array($eeSupported) ) { $eeSupported = array(); }
		$eeMissing = array();
		
		if( $eeSFL_Settings['GenerateImgThumbs'] == 'YES' ) { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' />
			<div class="eeNote">' . __('Read an image file and create a small thumbnail image.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		
		<p><label for="eeGeneratePDFThumbs">' . __('PDF Thumbnails', 'ee-simple-file-list') . ':</label>
		
		<input id="eeGeneratePDFThumbs" type="checkbox" name="eeGeneratePDFThumbs" value="YES"';
		if( $eeSFL_Settings['GeneratePDFThumbs'] == 'YES' ) { $eeOutput .= ' checked="checked"'; }
		if( !isset($eeSFL_BASE_Env['ImkGs']) OR $eeSFL_BASE_Env['eeOS'] == 'WINDOWS' ) { $eeOutput .= ' disabled="disabled"'; }
		$eeOutput .= ' /> ';
		
		if( !in_array('ImageMagick' , $eeSupported) ) { 
			$eeMissing[] = __('Image Magick is Not Installed. PDF thumbnails cannot be created.', 'ee-simple-file-list-pro');
		}
		if( !in_array('GhostScript' , $eeSupported) ) { 
			$eeMissing[] = 'GhostScript is Not Installed. PDF thumbnails cannot be created.';
		}
		
		if( $eeSFL_BASE_Env['eeOS'] == 'WINDOWS' ) { 
			$eeMissing .= ' <em>Windows ' . __('not yet supported for PDF thumbnails.', 'ee-simple-file-list') . '</em>';
		}
		
		$eeOutput .= '</p>
		<div class="eeNote">' . __('Read a PDF file and create a representative thumbnail image based on the first page.', 'ee-simple-file-list');
		
		$eeOutput .= '</div>
		
		</fieldset>
		<fieldset>
		
		<p><label for="eeGenerateVideoThumbs">' . __('Video Thumbnails', 'ee-simple-file-list') . ':</label>
		
		<input id="eeGenerateVideoThumbs" type="checkbox" name="eeGenerateVideoThumbs" value="YES"';
		if( $eeSFL_Settings['GenerateVideoThumbs'] == 'YES' ) { $eeOutput .= ' checked="checked"'; }
		if( !isset($eeSFL_BASE_Env['ffMpeg']) ) { $eeOutput .= ' disabled="disabled"'; }
		$eeOutput .= ' /> '; 
		
		if( !isset($eeSFL_BASE_Env['ffMpeg']) ) { 
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
			
		$eeOutput .= '
		
		</div>
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Smooth-Scroll', 'ee-simple-file-list') . '</h2>
		
		<p><label for="eeSmoothScroll">' . __('Use Smooth-Scroll', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeSmoothScroll" value="YES" id="eeSmoothScroll"';
		
		if( $eeSFL_Settings['SmoothScroll'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></p>
		
		<div class="eeNote">' . __('Uses a JavaScript effect to scroll down to the top of the list after an action. This can be helpful if the list is not located close to the top of the page.', 'ee-simple-file-list') . '</div>
		
		</div>
		
		
		
	</div>
	
	
	
	
		
		
	<!-- Right Column -->
	
	<div class="eeColRight">
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('File Actions', 'ee-simple-file-list') . '</h2>
		
		
		
		<fieldset>
		<legend>' . __('Show Open Action', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Link', 'ee-simple-file-list') . '</label>
		<input type="checkbox" name="eeShowFileOpen" value="YES" id="eeShowFileOpen"';
		
		if( $eeSFL_Settings['ShowFileOpen'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></div>
		
		<div class="eeNote">' . __('Display the Open File link. If the browser cannot open the file, it will prompt the user to download.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('Show Download Action', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Link', 'ee-simple-file-list') . '</label>
		<input type="checkbox" name="eeShowFileDownload" value="YES" id="eeShowFileDownload"';
		
		if( $eeSFL_Settings['ShowFileDownload'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></div>
		
		<div class="eeNote">' . __('The browser will prompt the user to download the file.', 'ee-simple-file-list') . '</div>
		
		</fieldset>

		
		
		<fieldset>
		<legend>' . __('Show Copy Action', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Link', 'ee-simple-file-list') . '</label>
		<input type="checkbox" name="eeShowFileCopyLink" value="YES" id="eeShowFileCopyLink"';
		
		if( $eeSFL_Settings['ShowFileCopyLink'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></div>
		
		<div class="eeNote">' . __('Copies the file URL to the user clipboard.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		</div>
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('File List Display', 'ee-simple-file-list') . '</h2>
		
		<fieldset>
		<legend>' . __('File Thumbnail', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileThumb" value="YES" id="eeShowFileThumb"'; 
		if($eeSFL_Settings['ShowFileThumb'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelThumb" value="';
		if( isset($eeSFL_Settings['LabelThumb']) ) { $eeOutput .= stripslashes($eeSFL_Settings['LabelThumb']); } else { $eeOutput .= $eeSFL_BASE->DefaultListSettings['LabelThumb']; }
		$eeOutput .= '" size="32" /></div>
		
		<div class="eeNote">' . __('Show file thumbnail images.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Date', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileDate" value="YES" id="eeShowFileDate"'; 
		if($eeSFL_Settings['ShowFileDate'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelDate" value="';
		if( isset($eeSFL_Settings['LabelDate'])) { $eeOutput .= stripslashes($eeSFL_Settings['LabelDate']); } else { $eeOutput .= $eeSFL_BASE->DefaultListSettings['LabelDate']; }
		$eeOutput .= '" size="32" />
		
		<select name="eeShowFileDateAs" id="eeShowFileDateAs">
			<option value="">' . __('Date Type', 'ee-simple-file-list') . '</option>
			
			<option value="Added"';
			if($eeSFL_Settings['ShowFileDateAs'] == 'Added') { $eeOutput .= ' selected="selected"'; }
			$eeOutput .= '>' . __('Added', 'ee-simple-file-list') . '</option>
			
			<option value="Modified"';
			if($eeSFL_Settings['ShowFileDateAs'] == 'Modified') { $eeOutput .= ' selected="selected"'; }
			$eeOutput .= '>' . __('Modified', 'ee-simple-file-list-pro') . '</option>
		</select></div>
		
		<div class="eeNote">Show the file date, either last modified or added to the list.</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Size', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileSize" value="YES" id="eeShowFileSize"'; 
		if($eeSFL_Settings['ShowFileSize'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelSize" value="';
		if( isset($eeSFL_Settings['LabelSize']) ) { $eeOutput .= stripslashes($eeSFL_Settings['LabelSize']); } else { $eeOutput .= $eeSFL_BASE->DefaultListSettings['LabelSize']; }
		$eeOutput .= '" size="32" /></div>
				
		<div class="eeNote">' . __('Limit the file information to display on the front-side file list. Enter a custom label if needed.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Description', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileDesc" value="YES" id="eeShowFileDesc"'; 
		if($eeSFL_Settings['ShowFileDesc'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelDesc" value="';
		if( isset($eeSFL_Settings['LabelDesc']) ) { $eeOutput .= stripslashes($eeSFL_Settings['LabelDesc']); } else { $eeOutput .= $eeSFL_BASE->DefaultListSettings['LabelDesc']; }
		$eeOutput .= '" size="32" /></div>
				
		<div class="eeNote">' . __('Show a description of the file, which can include keywords and special characters not allowed within the file name.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Submitter', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowSubmitterInfo" value="YES" id="eeShowSubmitterInfo"'; 
		if($eeSFL_Settings['ShowSubmitterInfo'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelOwner" value="';
		
		// echo '<pre>'; print_r($eeSFL_BASE->DefaultListSettings); echo '</pre>'; exit;
		
		if( $eeSFL_Settings['LabelOwner'] ) { $eeOutput .= stripslashes($eeSFL_Settings['LabelOwner']); } else { $eeOutput .= $eeSFL_BASE->DefaultListSettings['LabelOwner']; }
		$eeOutput .= '" size="32" /></div>
				
		<div class="eeNote">' . __('Show the name of the user who uploaded the file on the front-end.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Extension', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileExtension" value="YES" id="eeShowFileExtension"'; 
		if($eeSFL_Settings['ShowFileExtension'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' /></div>
				
		<div class="eeNote">' . __('Show or hide the file extension.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('Preserve Spaces', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eePreserveSpaces" value="YES" id="eePreserveSpaces"'; 
		if($eeSFL_Settings['PreserveSpaces'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' /></div>
				
		<div class="eeNote">' . __('Spaces in file names are replaced with hyphens in order to make the URL legal.', 'ee-simple-file-list') . ' ' . 
			__('This setting will revert this action for display.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		</div>

	</div>
		
</div>


<div class="eeColInline eeSettingsTile">
				
	<input class="button" type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" />
			
</div>
		
</form>';
	
?>