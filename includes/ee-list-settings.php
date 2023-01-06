<?php // Simple File List Script: ee-list-settings.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = 'Loading List Settings Page ...';

// Check for POST and Nonce
if( isset($_POST['eePost']) AND check_admin_referer( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce')) {
		
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
		,'PreserveName'
		,'ShowFileExtension'
		,'GenerateImgThumbs'
		,'GeneratePDFThumbs'
		,'GenerateVideoThumbs'
	);
		
	foreach( $eeCheckboxes as $eeTerm){ // "ee" is added in the function
		$eeSFL_BASE->eeListSettings[$eeTerm] = eeSFL_BASE_ProcessCheckboxInput($eeTerm);
	}
	
	// Text or Select Inputs
	$eeTextInputs = array(
		'LabelThumb'
		,'LabelName'
		,'LabelDate'
		,'LabelSize'
		,'LabelDesc'
		,'LabelOwner'
		,'ShowList'
		,'AdminRole'
		,'ShowListStyle'
		,'ShowListTheme'
		,'SortBy'
		,'ShowFileDateAs'
	);
	
	foreach( $eeTextInputs as $eeTerm){
		$eeSFL_BASE->eeListSettings[$eeTerm] = eeSFL_BASE_ProcessTextInput($eeTerm);
	}
	
	
	if(isset($_POST['eeSortOrder'])) {
		$eeSFL_BASE->eeListSettings['SortOrder'] = 'Descending';
	} else {
		$eeSFL_BASE->eeListSettings['SortOrder'] = 'Ascending';
	}
	
	
	// Sort for Sanity
	ksort($eeSFL_BASE->eeListSettings);
	
	// Update DB
	update_option('eeSFL_Settings_1', $eeSFL_BASE->eeListSettings);
	$eeConfirm = __('Settings Saved', 'ee-simple-file-list');
	$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = $eeConfirm;
	
}

// Settings Display =========================================
	
if( count($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors']) ) { 
	$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'], 'notice-error');
} elseif( $eeConfirm ) { 
	$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeConfirm, 'notice-success');
}

// Begin the Form	
$eeOutput .= '

<form action="' . $eeURL . '" method="post" id="eeSFL_Settings">
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
		
		<p><input class="eeFullWidth" type="text" name="eeFileListDir" value="" placeholder="' . ABSPATH . $eeSFL_BASE->eeListSettings['FileListDir'] . '" disabled="disabled" /></p>
		
		<div class="eeNote">' . __('Upgrade to Pro', 'ee-simple-file-list') . '</a> &rarr; ' . __('The Pro Version allows you to define a custom file list directory. It must only be relative to the WordPress home directory.', 'ee-simple-file-list') . '</div>
	
</div>
		

<div class="eeColumns">		
		
	<!-- Left Column -->
	
	<div class="eeColLeft">
	
		<div class="eeSettingsTile">
		
		<h2>' . __('File List Access', 'ee-simple-file-list') . '</h2>
	
		<fieldset>
			
			<legend>' . __('Front-End Display', 'ee-simple-file-list-pro') . '</legend>
		
			<div><label for="eeShowList">' . __('Show To', 'ee-simple-file-list-pro') . '</label>
			
			<select name="eeShowList" id="eeShowList">
			
				<option value="YES"';
	
				if($eeSFL_BASE->eeListSettings['ShowList'] == 'YES') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Everyone', 'ee-simple-file-list-pro') . '</option>
				
				<option value="USER"';
	
				if($eeSFL_BASE->eeListSettings['ShowList'] == 'USER') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Only Logged in Users', 'ee-simple-file-list-pro') . '</option>
				
				<option value="ADMIN"';
	
				if($eeSFL_BASE->eeListSettings['ShowList'] == 'ADMIN') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Only Logged in Admins', 'ee-simple-file-list-pro') . '</option>
				
				<option value="NO"';
	
				if($eeSFL_BASE->eeListSettings['ShowList'] == 'NO') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Hide Completely', 'ee-simple-file-list-pro') . '</option>
			
			</select></div>
			<div class="eeNote">' . __('Determine who you will show the front-side list to.', 'ee-simple-file-list-pro') . '</div>
			
			</fieldset>
			<fieldset>
			
			<legend>' . __('Back-End Access', 'ee-simple-file-list-pro') . '</legend>
			
			<div><label for="eeAdminRole">' . __('Choose Role', 'ee-simple-file-list-pro') . '</label>
			
			<select name="eeAdminRole" id="eeAdminRole">
			
				<option value="1"'; // 1

				if($eeSFL_BASE->eeListSettings['AdminRole'] == '1') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Subscribers and Above', 'ee-simple-file-list-pro') . '</option>
				
				
				<option value="2"'; // 2

				if($eeSFL_BASE->eeListSettings['AdminRole'] == '2') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Contributers and Above', 'ee-simple-file-list-pro') . '</option>
				
				
				<option value="3"'; // 3

				if($eeSFL_BASE->eeListSettings['AdminRole'] == '3') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Authors and Above', 'ee-simple-file-list-pro') . '</option>
				
				
				<option value="4"'; // 4

				if($eeSFL_BASE->eeListSettings['AdminRole'] == '4') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Editors and Above', 'ee-simple-file-list-pro') . '</option>
				
				
				<option value="5"'; // 5

				if($eeSFL_BASE->eeListSettings['AdminRole'] == '5') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Admins Only', 'ee-simple-file-list-pro') . '</option>
			
			</select></div>
			
			<div class="eeNote">' . __('Determine who can access the back-side settings.', 'ee-simple-file-list-pro') . '</div>
			
			</fieldset>
			
			<fieldset>
		
			<legend>' . __('Front-End Management', 'ee-simple-file-list-pro') . '</legend>
			
			<div><label for="eeAllowFrontManage">' . __('Allow', 'ee-simple-file-list-pro') . ':</label>
			<input type="checkbox" name="eeAllowFrontManage" value="YES" id="eeAllowFrontManage"';
			
			if( $eeSFL_BASE->eeListSettings['AllowFrontManage'] == 'YES') { $eeOutput .= ' checked="checked"'; }
			
			$eeOutput .= ' /></div>
			
			<div class="eeNote">' . __('Allow file deletion, file renaming, editing descriptions and dates.', 'ee-simple-file-list-pro') . '</div>
			<div class="eeNote"><a href="https://get.simplefilelist.com/" target="_blank">' .  __('Upgrade to PRO', 'ee-simple-file-list-pro') . '</a> ' . __('Upgrade to allow greater file management control for specific users and roles.', 'ee-simple-file-list-pro') . '<br />
			</div>
		
		</div>
		
		
				
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('File List Style', 'ee-simple-file-list') . '</h2>
	
		
		<fieldset>
		<legend>File List Type</legend>
		
		<p><label for="eeShowListStyle">' . __('Style', 'ee-simple-file-list') . '</label>
		
		<select name="eeShowListStyle" id="eeShowListStyle">
		
			<option value="TABLE"';

			if($eeSFL_BASE->eeListSettings['ShowListStyle'] == 'TABLE') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Standard Table Display', 'ee-simple-file-list') . '</option>
			
			<option value="TILES"';

			if($eeSFL_BASE->eeListSettings['ShowListStyle'] == 'TILES') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Tiles Displayed in Columns', 'ee-simple-file-list') . '</option>
			
			<option value="FLEX"';

			if($eeSFL_BASE->eeListSettings['ShowListStyle'] == 'FLEX') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Flexible List Display', 'ee-simple-file-list') . '</option>
		
		</select></p>
		<div class="eeNote">' . __('Choose the style of the file list: Table, Tiles or Flex.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		
		<fieldset>
		<legend>File List Theme</legend>
		
		<p><label for="eeShowListTheme">' . __('Show', 'ee-simple-file-list') . '</label>
		
		<select name="eeShowListTheme" id="eeShowListTheme">
		
			<option value="LIGHT"';

			if($eeSFL_BASE->eeListSettings['ShowListTheme'] == 'LIGHT') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Light Theme', 'ee-simple-file-list') . '</option>
			
			<option value="DARK"';

			if($eeSFL_BASE->eeListSettings['ShowListTheme'] == 'DARK') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Dark Theme', 'ee-simple-file-list') . '</option>
			
			<option value="None"';

			if($eeSFL_BASE->eeListSettings['ShowListTheme'] == 'None') { $eeOutput .= ' selected'; }
			
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
			
			if($eeSFL_BASE->eeListSettings['SortBy'] == 'Name') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('File Name', 'ee-simple-file-list') . '</option>
			
			
			<option value="Date"';
			
			if($eeSFL_BASE->eeListSettings['SortBy'] == 'Date') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('Date File Added', 'ee-simple-file-list') . '</option>
			
			
			<option value="DateChanged"';
			
			if($eeSFL_BASE->eeListSettings['SortBy'] == 'DateChanged') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('Date File Changed', 'ee-simple-file-list') . '</option>
			
			
			<option value="Size"';
			
			if($eeSFL_BASE->eeListSettings['SortBy'] == 'Size') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('File Size', 'ee-simple-file-list') . '</option>
			
			
			<option value="Random"';
			
			if($eeSFL_BASE->eeListSettings['SortBy'] == 'Random') { $eeOutput .=  ' selected'; }
			
			$eeOutput .= '>' . __('Random', 'ee-simple-file-list') . '</option>
		
		</select></p>
			
		<p><label for="eeSortOrder">' . __('Reverse Order', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeSortOrder" value="Descending" id="eeSortOrder"';
		
		if( $eeSFL_BASE->eeListSettings['SortOrder'] == 'Descending') { $eeOutput .= ' checked="checked"'; }
		
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
		
		if( $eeSFL_BASE->eeListSettings['GenerateImgThumbs'] == 'YES' ) { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' />
			<div class="eeNote">' . __('Read an image file and create a small thumbnail image.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		
		<p><label for="eeGeneratePDFThumbs">' . __('PDF Thumbnails', 'ee-simple-file-list') . ':</label>
		
		<input id="eeGeneratePDFThumbs" type="checkbox" name="eeGeneratePDFThumbs" value="YES"';
		if( $eeSFL_BASE->eeListSettings['GeneratePDFThumbs'] == 'YES' ) { $eeOutput .= ' checked="checked"'; }
		if( !isset($eeSFL_BASE->eeEnvironment['ImkGs']) OR $eeSFL_BASE->eeEnvironment['eeOS'] == 'WINDOWS' ) { $eeOutput .= ' disabled="disabled"'; }
		$eeOutput .= ' /> ';
		
		if( !in_array('ImageMagick' , $eeSupported) ) { 
			$eeMissing[] = __('Image Magick is Not Installed. PDF thumbnails cannot be created.', 'ee-simple-file-list');
		}
		if( !in_array('GhostScript' , $eeSupported) ) { 
			$eeMissing[] = 'GhostScript is Not Installed. PDF thumbnails cannot be created.';
		}
		
		if( $eeSFL_BASE->eeEnvironment['eeOS'] == 'WINDOWS' ) { 
			$eeMissing .= ' <em>Windows: ' . __('Not yet supported for PDF thumbnails.', 'ee-simple-file-list') . '</em>';
		}
		
		$eeOutput .= '</p>
		<div class="eeNote">' . __('Read a PDF file and create a representative thumbnail image based on the first page.', 'ee-simple-file-list');
		
		$eeOutput .= '</div>
		
		</fieldset>
		<fieldset>
		
		<p><label for="eeGenerateVideoThumbs">' . __('Video Thumbnails', 'ee-simple-file-list') . ':</label>
		
		<input id="eeGenerateVideoThumbs" type="checkbox" name="eeGenerateVideoThumbs" value="YES"';
		if( $eeSFL_BASE->eeListSettings['GenerateVideoThumbs'] == 'YES' ) { $eeOutput .= ' checked="checked"'; }
		if( !isset($eeSFL_BASE->eeEnvironment['ffMpeg']) ) { $eeOutput .= ' disabled="disabled"'; }
		$eeOutput .= ' /> '; 
		
		if( !isset($eeSFL_BASE->eeEnvironment['ffMpeg']) ) { 
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
		
		
		
	</div>
	
	
	
	
		
		
	<!-- Right Column -->
	
	<div class="eeColRight">
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('File Actions', 'ee-simple-file-list') . '</h2>
		
		
		
		<fieldset>
		<legend>' . __('Show Open Action', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Link', 'ee-simple-file-list') . '</label>
		<input type="checkbox" name="eeShowFileOpen" value="YES" id="eeShowFileOpen"';
		
		if( $eeSFL_BASE->eeListSettings['ShowFileOpen'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></div>
		
		<div class="eeNote">' . __('Display the Open File link. If the browser cannot open the file, it will prompt the user to download.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('Show Download Action', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Link', 'ee-simple-file-list') . '</label>
		<input type="checkbox" name="eeShowFileDownload" value="YES" id="eeShowFileDownload"';
		
		if( $eeSFL_BASE->eeListSettings['ShowFileDownload'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></div>
		
		<div class="eeNote">' . __('The browser will prompt the user to download the file.', 'ee-simple-file-list') . '</div>
		
		</fieldset>

		
		
		<fieldset>
		<legend>' . __('Show Copy Action', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Link', 'ee-simple-file-list') . '</label>
		<input type="checkbox" name="eeShowFileCopyLink" value="YES" id="eeShowFileCopyLink"';
		
		if( $eeSFL_BASE->eeListSettings['ShowFileCopyLink'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></div>
		
		<div class="eeNote">' . __('Copies the file URL to the user clipboard.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		</div>
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('File List Display', 'ee-simple-file-list') . '</h2>
		
		<fieldset>
		<legend>' . __('File Thumbnail', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileThumb" value="YES" id="eeShowFileThumb"'; 
		if($eeSFL_BASE->eeListSettings['ShowFileThumb'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelThumb" value="';
		if( isset($eeSFL_BASE->eeListSettings['LabelThumb']) ) { $eeOutput .= stripslashes($eeSFL_BASE->eeListSettings['LabelThumb']); } else { $eeOutput .= $eeSFL_BASE->DefaultListSettings['LabelThumb']; }
		$eeOutput .= '" size="32" /></div>
		
		<div class="eeNote">' . __('Show file thumbnail images.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Date', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileDate" value="YES" id="eeShowFileDate"'; 
		if($eeSFL_BASE->eeListSettings['ShowFileDate'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input class="eeFortyPercent" type="text" name="eeLabelDate" value="';
		if( isset($eeSFL_BASE->eeListSettings['LabelDate'])) { $eeOutput .= stripslashes($eeSFL_BASE->eeListSettings['LabelDate']); } else { $eeOutput .= $eeSFL_BASE->DefaultListSettings['LabelDate']; }
		$eeOutput .= '" size="32" />
		
		<select name="eeShowFileDateAs" id="eeShowFileDateAs">
			<option value="">' . __('Date Type', 'ee-simple-file-list') . '</option>
			
			<option value="Added"';
			if($eeSFL_BASE->eeListSettings['ShowFileDateAs'] == 'Added') { $eeOutput .= ' selected="selected"'; }
			$eeOutput .= '>' . __('Added', 'ee-simple-file-list') . '</option>
			
			<option value="Changed"';
			if($eeSFL_BASE->eeListSettings['ShowFileDateAs'] == 'Changed') { $eeOutput .= ' selected="selected"'; }
			$eeOutput .= '>' . __('Changed', 'ee-simple-file-list') . '</option>
		</select></div>
		
		<div class="eeNote">Show the file date, either last changed or when added to the list.</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Size', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileSize" value="YES" id="eeShowFileSize"'; 
		if($eeSFL_BASE->eeListSettings['ShowFileSize'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelSize" value="';
		if( isset($eeSFL_BASE->eeListSettings['LabelSize']) ) { $eeOutput .= stripslashes($eeSFL_BASE->eeListSettings['LabelSize']); } else { $eeOutput .= $eeSFL_BASE->DefaultListSettings['LabelSize']; }
		$eeOutput .= '" size="32" /></div>
				
		<div class="eeNote">' . __('Limit the file information to display on the front-side file list. Enter a custom label if needed.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Description', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileDesc" value="YES" id="eeShowFileDesc"'; 
		if($eeSFL_BASE->eeListSettings['ShowFileDesc'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelDesc" value="';
		if( isset($eeSFL_BASE->eeListSettings['LabelDesc']) ) { $eeOutput .= stripslashes($eeSFL_BASE->eeListSettings['LabelDesc']); } else { $eeOutput .= $eeSFL_BASE->DefaultListSettings['LabelDesc']; }
		$eeOutput .= '" size="32" /></div>
				
		<div class="eeNote">' . __('Show a description of the file, which can include keywords and special characters not allowed within the file name.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Submitter', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowSubmitterInfo" value="YES" id="eeShowSubmitterInfo"'; 
		if($eeSFL_BASE->eeListSettings['ShowSubmitterInfo'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' />
		<input type="text" name="eeLabelOwner" value="';
		
		// echo '<pre>'; print_r($eeSFL_BASE->DefaultListSettings); echo '</pre>'; exit;
		
		if( $eeSFL_BASE->eeListSettings['LabelOwner'] ) { $eeOutput .= stripslashes($eeSFL_BASE->eeListSettings['LabelOwner']); } else { $eeOutput .= $eeSFL_BASE->DefaultListSettings['LabelOwner']; }
		$eeOutput .= '" size="32" /></div>
				
		<div class="eeNote">' . __('Show the name of the user who uploaded the file on the front-end.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		<fieldset>
		<legend>' . __('Table Header', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowHeader" value="YES" id="eeShowHeader"'; 
		if($eeSFL_BASE->eeListSettings['ShowHeader'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' /></div>
				
		<div class="eeNote">' . __('Show or hide the file table header.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('File Extension', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eeShowFileExtension" value="YES" id="eeShowFileExtension"'; 
		if($eeSFL_BASE->eeListSettings['ShowFileExtension'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' /></div>
				
		<div class="eeNote">' . __('Show or hide the file extension.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		<legend>' . __('Preserve File Name', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show', 'ee-simple-file-list') . '</label><input type="checkbox" name="eePreserveName" value="YES" id="eePreserveName"'; 
		if($eeSFL_BASE->eeListSettings['PreserveName'] == 'YES') { $eeOutput .= ' checked'; }
		$eeOutput .= ' /></div>
				
		<div class="eeNote">' . __('Files with illegal characters are renamed to ensure good URLs.', 'ee-simple-file-list') . ' ' . 
			__('This setting will preserve and show the original name as the Nice Name.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		</div>
		
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Smooth-Scroll', 'ee-simple-file-list') . '</h2>
		
		<p><label for="eeSmoothScroll">' . __('Use Smooth-Scroll', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeSmoothScroll" value="YES" id="eeSmoothScroll"';
		
		if( $eeSFL_BASE->eeListSettings['SmoothScroll'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></p>
		
		<div class="eeNote">' . __('Uses a JavaScript effect to scroll down to the top of the list after an action. This can be helpful if the list is not located close to the top of the page.', 'ee-simple-file-list') . '</div>
		
		</div>
		
		

	</div>
		
</div>


<div class="eeColInline eeSettingsTile">
				
	<input class="button" type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" />
			
</div>
		
</form>';
	
?>