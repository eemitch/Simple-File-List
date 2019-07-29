<?php // Simple File List - ee-list-settings.php - mitchellbennis@gmail.com
	
	// tab=list_settings
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('That is Noncense! (' . basename(__FILE__) . ')' ); // Exit if nonce fails

$eeSFL_Log[] = 'Loading List Settings Page ...';
	
// Check for POST and Nonce
if(@$_POST['eePost'] AND check_admin_referer( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce')) {
	
	if($_POST['eeShowList'] == 'YES') { $eeSFL_Config['ShowList'] = 'YES'; } 
		elseif($_POST['eeShowList'] == 'USER') { $eeSFL_Config['ShowList'] = 'USER'; } // Show only to logged in users
		 elseif($_POST['eeShowList'] == 'ADMIN') { $eeSFL_Config['ShowList'] = 'ADMIN'; } // Show only to logged in Admins
			else { $eeSFL_Config['ShowList'] = 'NO'; }
			
	if($eeSFL_Config['ShowList'] == 'YES') { // Only update if showing the list
		
		if(@$_POST['eeShowFileThumb'] == 'YES') { $eeSFL_Config['ShowFileThumb'] = 'YES'; } 
			else { $eeSFL_Config['ShowFileThumb'] = 'NO'; }
		
		if(@$_POST['eeShowFileDate'] == 'YES') { $eeSFL_Config['ShowFileDate'] = 'YES'; } 
			else { $eeSFL_Config['ShowFileDate'] = 'NO'; }
		
		if(@$_POST['eeShowFileSize'] == 'YES') { $eeSFL_Config['ShowFileSize'] = 'YES'; } 
			else { $eeSFL_Config['ShowFileSize'] = 'NO'; }
		
		if(@$_POST['eeAllowFrontManage'] == 'YES') { $eeSFL_Config['AllowFrontManage'] = 'YES'; } 
			else { $eeSFL_Config['AllowFrontManage'] = 'NO'; }
	
		if(@$_POST['eeSortBy']) { $eeSFL_Config['SortBy'] = filter_var($_POST['eeSortBy'], FILTER_SANITIZE_STRING); }
			else { $eeSFL_Config['SortBy'] = 'Name'; }
		
		if(@$_POST['eeSortOrder'] == 'Descending') { $eeSFL_Config['SortOrder'] = 'Descending'; }
			else { $eeSFL_Config['SortOrder'] = 'Ascending'; }
		
		if(@$_POST['eeShowFileActions'] == 'YES') { $eeSFL_Config['ShowFileActions'] = 'YES'; } 
			else { $eeSFL_Config['ShowFileActions'] = 'NO'; }
		
		if(@$_POST['eeShowHeader'] == 'YES') { $eeSFL_Config['ShowHeader'] = 'YES'; } else { $eeSFL_Config['ShowHeader'] = 'NO'; }
	
	}
	
	
	// Get all the settings
	$eeSettings = get_option('eeSFL-Settings');
	
	// Update this sub-array
	$eeSettings[$eeSFL->eeListID] = $eeSFL_Config;
	
	// Update DB
	update_option('eeSFL-Settings', $eeSettings );
	
	
	// Extension Processing
	if($eeSFLF) {
		if(!@$eeSFLF_ListFolder) { // If not already set up
			$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
			include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_ListSettingsProcess.php');
		}
	}
	
	if($eeSFLS) {
		$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
		include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-pagination-settings-process.php');
	}
	
	$eeSFL_Confirm = __('List Settings Saved', 'ee-simple-file-list');
}

// Settings Display =========================================
	
$eeOutput .= '<div class="eeSFL_Admin">';
	
if(@$eeSFL_Log['errors']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['errors'], 'error'); // TO DO - Make this a Function
} elseif(@$eeSFL_Confirm) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Confirm, 'updated');
}

// Begin the Form	
$eeOutput .= '<form action="' . $_SERVER['PHP_SELF'] . '?page=' . $eeSFL_Page . '&tab=list_settings&subtab=list_settings" method="post" id="eeSFL_Settings">
		<input type="hidden" name="eePost" value="TRUE" />';	
		
		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce' );
		
		$eeOutput .= '<fieldset>
		
			<h2>' . __('File List Settings', 'ee-simple-file-list') . '</h2>
			
			<label for="eeShowList">' . __('File List Display', 'ee-simple-file-list') . '</label>
			
			<select name="eeShowList" id="eeShowList">
			
				<option value="YES"';

				if($eeSFL_Config['ShowList'] == 'YES') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Show to Everyone', 'ee-simple-file-list') . '</option>
				
				<option value="USER"';

				if($eeSFL_Config['ShowList'] == 'USER') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Show to Only Logged in Users', 'ee-simple-file-list') . '</option>
				
				<option value="ADMIN"';

				if($eeSFL_Config['ShowList'] == 'ADMIN') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Show to Only Logged in Admins', 'ee-simple-file-list') . '</option>
				
				<option value="NO"';

				if($eeSFL_Config['ShowList'] == 'NO') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Hide the File List Completely', 'ee-simple-file-list') . '</option>
			
			</select>
			
			<br class="eeClearFix" />
			<div class="eeNote">' . __('You can use the uploader without showing the file list.', 'ee-simple-file-list') . '</div>';
				
			if($eeSFL_Config['ShowList'] != 'NO') {
			
				$eeOutput .= '<h3>' . __('Columns to Show', 'ee-simple-file-list') . '</h3>
				
				<label class="eeNoClear" for="eeShowFileThumb">' . __('Show Thumbnail', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowFileThumb" value="YES" id="eeShowFileThumb"'; 
				if(get_option('eeSFL-' . $eeSFL->eeListID . '-ShowFileThumb') == 'YES') { $eeOutput .= ' checked'; }
				$eeOutput .= ' />
				
				<label class="eeNoClear" for="eeShowFileDate">' . __('Show File Date', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowFileDate" value="YES" id="eeShowFileDate"'; 
				if(get_option('eeSFL-' . $eeSFL->eeListID . '-ShowFileDate') == 'YES') { $eeOutput .= ' checked'; }
				$eeOutput .= ' /> 
				
				<label class="eeNoClear" for="eeShowFileSize">' . __('Show File Size', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowFileSize" value="YES" id="eeShowFileSize"'; 
				if(get_option('eeSFL-' . $eeSFL->eeListID . '-ShowFileSize') == 'YES') { $eeOutput .= ' checked'; }
				$eeOutput .= ' />';
				
				$eeOutput .= '<div class="eeNote">' . __('Limit the columns of file details to display on the front-side file list.', 'ee-simple-file-list') . '</div>
					
				<h3>' . __('File Sorting and Order', 'ee-simple-file-list') . '</h3>	
				
				<label for="eeSortList">' . __('Sort By', 'ee-simple-file-list') . ':</label><select name="eeSortBy" id="eeSortList">
				
						<option value="Name"';
						
						if($eeSFL_Config['SortBy'] == 'Name') { $eeOutput .=  'selected'; }
						
						$eeOutput .= '>' . __('File Name', 'ee-simple-file-list') . '</option>
						<option value="Date"';
						
						if($eeSFL_Config['SortBy'] == 'Date') { $eeOutput .=  'selected'; }
						
						$eeOutput .= '>' . __('File Date', 'ee-simple-file-list') . '</option>
						<option value="Size"';
						
						if($eeSFL_Config['SortBy'] == 'Size') { $eeOutput .=  'selected'; }
						
						$eeOutput .= '>' . __('File Size', 'ee-simple-file-list') . '</option>
						<option value="Random"';
						
						if($eeSFL_Config['SortBy'] == 'Random') { $eeOutput .=  'selected'; }
						
						$eeOutput .= '>' . __('Random', 'ee-simple-file-list') . '</option>
					</select> 
				<div class="eeNote">' . __('Sort the list by name, date, file size, or randomly.', 'ee-simple-file-list') . '</div>
					
				<br class="eeClearFix" />
					
				<label for="eeSortOrder">' . __('Reverse Order', 'ee-simple-file-list') . ':</label>
				<input type="checkbox" name="eeSortOrder" value="Descending" id="eeSortOrder"';
				
				if(get_option('eeSFL-' . $eeSFL->eeListID . '-SortOrder') == 'Descending') { $eeOutput .= ' checked="checked"'; }
				
				$eeOutput .= ' /> <p>&darr; ' . __('Descending', 'ee-simple-file-list') . '</p>
				
				<div class="eeNote">' . __('Check this box to reverse the default sort order.', 'ee-simple-file-list') . '<br />
					' . __('The list is sorted Ascending by default', 'ee-simple-file-list') . ': A to Z, ' . __('Small to Large', 'ee-simple-file-list') . ', ' . __('Old to New', 'ee-simple-file-list') . '</div>
					
				<br class=eeClearFix />
					
				<h3>' . __('File List Display', 'ee-simple-file-list') . '</h3>	
				
				
				<label for="eeShowListHeader">' . __('Show Header', 'ee-simple-file-list') . ':</label>
				<input type="checkbox" name="eeShowListHeader" value="YES" id="eeShowListHeader"';
				
				if(get_option('eeSFL-' . $eeSFL->eeListID . '-ShowListHeader') == 'YES') { $eeOutput .= ' checked="checked"'; }
				
				$eeOutput .= ' />
				
				<div class="eeNote">' . __('Show file list\'s table header or not.', 'ee-simple-file-list') . '</div>
				
				<br class=eeClearFix />
				
				
				<label for="eeShowFileActions">' . __('Show File Actions', 'ee-simple-file-list') . ':</label>
				<input type="checkbox" name="eeShowFileActions" value="YES" id="eeShowFileActions"';
				
				if(get_option('eeSFL-' . $eeSFL->eeListID . '-ShowFileActions') == 'YES') { $eeOutput .= ' checked="checked"'; }
				
				$eeOutput .= ' /> <p>' . __('Open | Download', 'ee-simple-file-list') . '</p>
				
				<div class="eeNote">' . __('Show file action links below each file name on the front-side list', 'ee-simple-file-list') . '</div>
				
				<br class=eeClearFix />
				
				
				<label for="eeAllowFrontManage">' . __('Allow Front Delete', 'ee-simple-file-list') . ':</label>
				<input type="checkbox" name="eeAllowFrontManage" value="YES" id="eeAllowFrontManage"';
				
				if(get_option('eeSFL-' . $eeSFL->eeListID . '-AllowFrontManage') == 'YES') { $eeOutput .= ' checked="checked"'; }
				
				$eeOutput .= ' /> <p>' . __('Use with Caution', 'ee-simple-file-list') . '</p>
								
				<div class="eeNote">' . __('Allows file deletion on the front-side of the website', 'ee-simple-file-list') . '</div>
				
				<br class=eeClearFix />';
				
				if($eeSFLF) {
					
					$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
					include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_ListSettings.php');
				}
				
				if($eeSFLS) {
					
					$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
					include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-pagination-settings.php');
				}
					
			}
			
		$eeOutput .= '<input type="submit" name="submit" id="submit2" value="' . __('SAVE', 'ee-simple-file-list') . '" class="eeAlignRight" />
		
		</fieldset>
		
	</form>
	
</div>';
	
?>