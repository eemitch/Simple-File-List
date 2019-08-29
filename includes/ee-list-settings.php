<?php // Simple File List - ee-list-settings.php - mitchellbennis@gmail.com
	
	// tab=list_settings
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('That is Noncense! (' . basename(__FILE__) . ')' ); // Exit if nonce fails

$eeSFL_Log[] = 'Loading List Settings Page ...';

// Check for POST and Nonce
if(@$_POST['eePost'] AND check_admin_referer( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce')) {
	
	// Get all the settings
	$eeSettings = get_option('eeSFL-Settings');
	
	$eeID = $eeSFL_Config['ID'];
	
	if($_POST['eeShowList'] == 'YES') { $eeSettings[$eeID]['ShowList'] = 'YES'; } 
		elseif($_POST['eeShowList'] == 'USER') { $eeSettings[$eeID]['ShowList'] = 'USER'; } // Show only to logged in users
		 elseif($_POST['eeShowList'] == 'ADMIN') { $eeSettings[$eeID]['ShowList'] = 'ADMIN'; } // Show only to logged in Admins
			else { $eeSettings[$eeID]['ShowList'] = 'NO'; }
			
	if($eeSFL_Config['ShowList'] != 'NO') { // Only update if showing the list
		
		// YES/NO Checkboxes
		$eeCheckboxes = array(
			'ShowFileThumb'
			,'ShowFileDate'
			,'ShowFileSize'
			,'ShowFileDescription'
			,'ShowSubmitterName'
			,'ShowSubmitterEmail'
			,'ShowSubmitterDesc'
		);
		
		foreach( $eeCheckboxes as $eeTerm){ // "ee" is added in the function
			
			$eeSettings[$eeID][$eeTerm] = eeSFL_ProcessCheckboxInput($eeTerm);
		}
		
		// Sort by Select Box	
		if(@$_POST['eeSortBy']) { $eeSettings[$eeID]['SortBy'] = filter_var($_POST['eeSortBy'], FILTER_SANITIZE_STRING); }
			elseif(@$_POST['eeSortBy'] == 'NO') { $eeSettings[$eeID]['SortBy'] = 'Name'; }
		
		// Asc/Desc Checkbox
		if(@$_POST['eeSortOrder'] == 'Descending') { $eeSettings[$eeID]['SortOrder'] = 'Descending'; }
			elseif($_POST['eeSortBy'] AND !@$_POST['eeSortOrder']) { $eeSettings[$eeID]['SortOrder'] = 'Ascending'; }
		
		// Expiration
		if( is_numeric($_POST['eeExpireTime']) AND $_POST['eeExpireTime'] <= 24 ) { $eeSettings[$eeID]['ExpireTime'] = $_POST['eeExpireTime']; }
	}
	
	
	// Update DB
	update_option('eeSFL-Settings', $eeSettings );
	
	// Update the array with new values
	$eeSFL_Config = $eeSettings[$eeID];
	
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
$eeOutput .= '

<form action="' . $_SERVER['PHP_SELF'] . '?page=' . $eeSFL->eePluginSlug . '&tab=settings&subtab=list_settings" method="post" id="eeSFL_Settings">
		
		<input type="hidden" name="eePost" value="TRUE" />';	
		
		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce' );
		
		$eeOutput .= '<fieldset>
		
			<h1>' . __('File List Settings', 'ee-simple-file-list') . '</h1>
			
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
			
				$eeOutput .= '<h3>' . __('Information to Show', 'ee-simple-file-list') . '</h3>
				
				<label class="eeNoClear" for="eeShowFileThumb">' . __('Show Thumbnail', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowFileThumb" value="YES" id="eeShowFileThumb"'; 
				if($eeSFL_Config['ShowFileThumb'] == 'YES') { $eeOutput .= ' checked'; }
				$eeOutput .= ' />
				
				<label class="eeNoClear" for="eeShowFileDate">' . __('Show File Date', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowFileDate" value="YES" id="eeShowFileDate"'; 
				if($eeSFL_Config['ShowFileDate'] == 'YES') { $eeOutput .= ' checked'; }
				$eeOutput .= ' /> 
				
				<label class="eeNoClear" for="eeShowFileSize">' . __('Show File Size', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowFileSize" value="YES" id="eeShowFileSize"'; 
				if($eeSFL_Config['ShowFileSize'] == 'YES') { $eeOutput .= ' checked'; }
				$eeOutput .= ' />
				
				<label class="eeNoClear" for="eeShowFileDescription">' . __('Show Description', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowFileDescription" value="YES" id="eeShowFileDescription"'; 
				if($eeSFL_Config['ShowFileDescription'] == 'YES') { $eeOutput .= ' checked'; }
				$eeOutput .= ' />
				
				<div class="eeNote">' . __('Limit the file information to display on the front-side file list.', 'ee-simple-file-list') . '</div>
				
				<br class="eeClearFix" />';
				
				if($eeSFL_Config['GetUploaderInfo'] == 'YES') {
					
					$eeOutput .= '
					
					<h2>Submitter Information</h2>
					
					<label class="eeNoClear" for="eeShowSubmitterName">' . __('Show Submitter\'s Name', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowSubmitterName" value="YES" id="eeShowSubmitterName"'; 
					if(@$eeSFL_Config['ShowSubmitterName'] == 'YES') { $eeOutput .= ' checked'; }
					$eeOutput .= ' />
					
					<label class="eeNoClear" for="eeShowSubmitterEmail">' . __('Show Submitter\'s Email', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowSubmitterEmail" value="YES" id="eeShowSubmitterEmail"'; 
					if(@$eeSFL_Config['ShowSubmitterEmail'] == 'YES') { $eeOutput .= ' checked'; }
					$eeOutput .= ' />
					
					<label class="eeNoClear" for="eeShowSubmitterDesc">' . __('Show Submitter\'s Comment', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowSubmitterDesc" value="YES" id="eeShowSubmitterDesc"'; 
					if(@$eeSFL_Config['ShowSubmitterDesc'] == 'YES') { $eeOutput .= ' checked'; }
					$eeOutput .= ' />
					
					<div class="eeNote">' . __('Show who uploaded the file, add a link to their email address, and show their comments.', 'ee-simple-file-list') . '</div>';
				}
				
				$eeOutput .= '
					
				<br class="eeClearFix" />
				
				<h2>' . __('File Sorting and Order', 'ee-simple-file-list') . '</h2>	
				
				<label for="eeSortList">' . __('Sort By', 'ee-simple-file-list') . ':</label>
				
				<select name="eeSortBy" id="eeSortList">
				
						<option value="Name"';
						
						if($eeSFL_Config['SortBy'] == 'Name') { $eeOutput .=  ' selected'; }
						
						$eeOutput .= '>' . __('File Name', 'ee-simple-file-list') . '</option>
						<option value="Date"';
						
						if($eeSFL_Config['SortBy'] == 'Date') { $eeOutput .=  ' selected'; }
						
						$eeOutput .= '>' . __('File Date', 'ee-simple-file-list') . '</option>
						<option value="Size"';
						
						if($eeSFL_Config['SortBy'] == 'Size') { $eeOutput .=  ' selected'; }
						
						$eeOutput .= '>' . __('File Size', 'ee-simple-file-list') . '</option>
						<option value="Random"';
						
						if($eeSFL_Config['SortBy'] == 'Random') { $eeOutput .=  ' selected'; }
						
						$eeOutput .= '>' . __('Random', 'ee-simple-file-list') . '</option>
					</select> 
				<div class="eeNote">' . __('Sort the list by name, date, file size, or randomly.', 'ee-simple-file-list') . '</div>
					
				<br class="eeClearFix" />
					
				<label for="eeSortOrder">' . __('Reverse Order', 'ee-simple-file-list') . ':</label>
				<input type="checkbox" name="eeSortOrder" value="Descending" id="eeSortOrder"';
				
				if( $eeSFL_Config['SortOrder'] == 'Descending') { $eeOutput .= ' checked="checked"'; }
				
				$eeOutput .= ' /> <p>&darr; ' . __('Descending', 'ee-simple-file-list') . '</p>
				
				<div class="eeNote">' . __('Check this box to reverse the default sort order.', 'ee-simple-file-list') . '<br />
					' . __('The list is sorted Ascending by default', 'ee-simple-file-list') . ': A to Z, ' . __('Small to Large', 'ee-simple-file-list') . ', ' . __('Old to New', 'ee-simple-file-list') . '</div>
					
				<br class="eeClearFix" />
				
				<h2>' . __('File List Performance', 'ee-simple-file-list') . '</h2>	
				
				<label for="eeExpireTime">' . __('Re-Scan Interval', 'ee-simple-file-list') . ':</label>
				<input type="range" id="eeExpireTime" name="eeExpireTime" min="0" max="24" step="1" value="' . $eeSFL_Config['ExpireTime'] . '" /> 
					<p><span id="eeExpireTimeValue">' . $eeSFL_Config['ExpireTime'] . '</span> ' . __('Hours', 'ee-simple-file-list') . '</p>
					<div class="eeNote">' . __('Choose how often the file list on your disc drive is re-scanned. Set to zero to re-scan on each list page load.', 'ee-simple-file-list') . '<br />
					<em>' . __('If you use FTP or another method to upload files to your list, set the interval low.', 'ee-simple-file-list') . '</em></div>';	
					
			}
			
		$eeOutput .= '<input type="submit" name="submit" id="submit2" value="' . __('SAVE', 'ee-simple-file-list') . '" class="eeAlignRight" />
		
		</fieldset>
		
	</form>
	
</div>';
	
?>