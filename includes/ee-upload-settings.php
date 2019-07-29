<?php // Simple File List Uploader Settings - Mitchell Bennis | Element Engage, LLC | mitch@elementengage.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('That is Noncense! (' . basename(__FILE__) . ')' ); // Exit if nonce fails

$eeSFL_Log[] = 'Loading Uploader Settings Page ...';
	
// Check for POST and Nonce
if(@$_POST['eePost'] AND check_admin_referer( 'ee-simple-file-list-upload-settings', 'ee-simple-file-list-upload-settings-nonce')) {
		
	if($_POST['eeAllowUploads'] == 'YES') { 
		
		$eeSFL_Config['AllowUploads'] = 'YES';
	
	} elseif($_POST['eeAllowUploads'] == 'USER') { // Only logged in users
		 
		 $eeSFL_Config['AllowUploads'] = 'USER';
		 
	} elseif($_POST['eeAllowUploads'] == 'ADMIN') { // Only logged in users
		 
		 $eeSFL_Config['AllowUploads'] = 'ADMIN';
		 
	} else { 
		$eeSFL_Config['AllowUploads'] = 'NO';
	}
	
	// Get Uploader Info
	if($eeSFL_Config['AllowUploads'] != 'NO') { // Only update if allowing uploads
			
		if(@$_POST['eeGetUploaderInfo'] == 'YES') { $eeSFL_Config['GetUploaderInfo'] = 'YES'; } 
			else { $eeSFL_Config['GetUploaderInfo'] = 'NO'; }
		
		// File Number Limit
		$eeSFL_Config['UploadLimit'] = filter_var(@$_POST['eeUploadLimit'], FILTER_VALIDATE_INT);
		if(! $eeSFL_Config['UploadLimit'] ) { $eeSFL_Config['UploadLimit'] = $eeSFL->eeDefaultUploadLimit; }
		
		// Maximum File Size
		if(@$_POST['eeUploadMaxFileSize']) {
			
			$eeSFL_UploadMaxFileSize = (int) $_POST['eeUploadMaxFileSize'];
			
			// Can't be more than the system allows.
			if(! $eeSFL_Config['UploadMaxFileSize'] OR $eeSFL_Config['UploadMaxFileSize'] > $eeSFL_Env['the_max_upload_size']) { 
				$eeSFL_Config['UploadMaxFileSize'] = $eeSFL_Env['the_max_upload_size'];
			}
			
		} else {
			$eeSFL_UploadMaxFileSize = 1;
		}
		
		
		// File Formats
		if(@$_POST['eeFileFormats']) { // Strip all but what we need for the comma list of file extensions
			$eeSFL_Config['FileFormats'] = preg_replace("/[^a-z0-9 ,]/i", "", $_POST['eeFileFormats']);
		}
		
		// Custom Upload Folder
		$eeSFL_LastFileListDir = $eeSFL_Config['FileListDir'];
		
		if(@$_POST['eeFileListDir']) {
			
			$eeSFL_FileListDir = filter_var($_POST['eeFileListDir'], FILTER_SANITIZE_STRING);
			
			// Get rid of leading slash
			if(strpos($eeSFL_FileListDir, '/') === 0) {
				$eeSFL_FileListDir = substr($eeSFL_FileListDir, 1);
			}
			
			$eeSFL_DirCheck = eeSFL_FileListDirCheck(ABSPATH . $eeSFL_Config['FileListDir']);
			$eeSFL_Log[] = $eeSFL_DirCheck;	
			
			if(@$eeSFL_DirCheck['Error']) {
				$eeSFL_Log['errors'][] = $eeSFL_DirCheck;
				$eeSFL_Log['errors'][] = __('Cannot create the file directory. Reverting to default.', 'ee-simple-file-list');
				$eeSFL_Config['FileListDir'] = $eeSFL_Env['FileListDefaultDir'];
			}
		
		} else {
			
			$eeSFL_Config['FileListDir'] = $eeSFL_Env['FileListDefaultDir'];
		}
		
		
		$eeSFL_To = @$_POST['eeNotify'];
			
		if(strpos($eeSFL_To, ',')) { // Multiple Addresses
		
			$eeSFL_Addresses = explode(',', $eeSFL_To); // Make array
			
			$eeSFL_AddressesString = '';
			
			foreach($eeSFL_Addresses as $add){
				
				$add = trim($add);
				
				if(filter_var($add, FILTER_VALIDATE_EMAIL)) {
			
					$eeSFL_AddressesString .= $add . ',';
				} else {
					$eeSFL_Log['errors'][] = $add . ' - ' . __('This is not a valid email address.', 'ee-simple-file-list');
				}
			}
			
			$eeSFL_Config['Notify'] = substr($eeSFL_AddressesString, 0, -1); // Remove last comma
			
		
		} elseif(filter_var(@$_POST['eeNotify'], FILTER_SANITIZE_EMAIL)) { // Only one address
			
			$add = $_POST['eeNotify'];
			
			if(filter_var($add, FILTER_VALIDATE_EMAIL)) {
				$eeSFL_Config['Notify'] = $add;
			} else {
				$eeSFL_Log['errors'][] = $add . ' - ' . __('This is not a valid email address.', 'ee-simple-file-list');
			}
			
		} else {
			
			$eeSFL_Config['Notify'] = ''; // Anything but a good email gets null.
		}
	
	
	}
	
	// Get all the settings
	$eeSettings = get_option('eeSFL-Settings');
	
	// Update this sub-array
	$eeSettings[$eeSFL->eeListID] = $eeSFL_Config;
	
	// Update DB
	update_option('eeSFL-Settings', $eeSettings );
	
	
		
	$eeSFL_Confirm = __('Uploader Settings Saved', 'ee-simple-file-list');
	$eeSFL_Log[] = $eeSFL_Confirm;
	
	if($eeSFL_DevMode) {
		$eeSFL_Log[] = $_POST;
	}
	
}

// Settings Display =========================================
	
$eeOutput .= '<div class="eeSFL_Admin">';




	
if(@$eeSFL_Log['errors']) { 
	
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['errors'], 'error');
	
} elseif(@$eeSFL_Confirm) { 
	
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Confirm, 'updated');
}





	
$eeOutput .= '<form action="' . $_SERVER['PHP_SELF'] . '?page=' . $eeSFL_Page . '&tab=list_settings&subtab=uploader_settings" method="post" id="eeSFL_Settings">
		<input type="hidden" name="eePost" value="TRUE" />
		<input type="hidden" name="eeListID" value="' . $eeSFL->eeListID . '" />';	
		
		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-upload-settings', 'ee-simple-file-list-upload-settings-nonce' );
		
		$eeOutput .= '<fieldset>
			
			<h2>' . __('File Upload Settings', 'ee-simple-file-list') . '</h2>
			
			<label for="eeAllowUploads">' . __('File Uploader', 'ee-simple-file-list') . '</label>
			
			<select name="eeAllowUploads" id="eeAllowUploads">
			
				<option value="YES"';

				if($eeSFL_Config['AllowUploads'] == 'YES') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Anyone Can Upload', 'ee-simple-file-list') . '</option>
				
				<option value="USER"';

				if($eeSFL_Config['AllowUploads'] == 'USER') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Only Logged in Users Can Upload', 'ee-simple-file-list') . '</option>
				
				<option value="ADMIN"';

				if($eeSFL_Config['AllowUploads'] == 'ADMIN') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Only Logged in Admins Can Upload', 'ee-simple-file-list') . '</option>
				
				<option value="NO"';

				if($eeSFL_Config['AllowUploads'] == 'NO') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Hide the Front Side Uploader Completely', 'ee-simple-file-list') . '</option>
			
			</select>';
			
			
			$eeOutput .= '<div class="eeNote">' . 
				__('Allow anyone to upload, only logged in users, administrators or nobody.', 'ee-simple-file-list') . '</div>
					
			<br class="eeClearFix" />';
			
			
			if($eeSFL_Config['AllowUploads'] != 'NO') {
				
				// Uploader Engine
				
				$eeOutput .= '
				
				<label for="eeUploadLimit">' . __('Upload Limit', 'ee-simple-file-list') . '</label>
		
				<input type="number" min="1" max="100" step="1" name="eeUploadLimit" value="' . $eeSFL_Config['UploadLimit'] . '" class="eeAdminInput" id="eeUploadLimit" />
					<div class="eeNote">' . __('The maximum number of files that may be uploaded per submission.', 'ee-simple-file-list') . '</div>
					
				<br class="eeClearFix" />';
					
					
				
				// The File List Folder
				$eeOutput .= '<label for="eeFileListDir">' . __('Upload Directory', 'ee-simple-file-list') . ':</label><input type="text" name="eeFileListDir" value="';
			
				if( $eeSFL_Config['FileListDir'] ) { 
					
					$eeDir = str_replace(ABSPATH, '', $eeSFL_Config['FileListDir']); // Strip ABSPATH for saving
					$eeOutput .= $eeDir;
				
				} else { 
					
					$eeDir = str_replace(ABSPATH, '', $eeSFL_Env['FileListDefaultDir']);
					$eeOutput .= $eeDir;
				}
			
				$eeOutput .= '" class="eeAdminInput" id="eeFileListDir" size="64" />
					<div class="eeNote">' . __('This is relative to your Wordpress home folder.', 'ee-simple-file-list') . ' <em>wp-content/uploads/simple-file-list/</em> ' . __('is the default', 'ee-simple-file-list') . '.<br />
						' . __('This will create the directory if it does not yet exist.', 'ee-simple-file-list') . '
					</div>
				
				<br class="eeClearFix" />';
					
				
				
				
				// Maximum File Size
				if(!$eeSFL_Config['UploadMaxFileSize']) { $eeSFL_Config['UploadMaxFileSize'] = $eeSFL_Env['the_max_upload_size']; }
				
				$eeOutput .= '<label for="eeUploadMaxFileSize">' . __('Maximum File Size', 'ee-simple-file-list') . ' (MB):</label><input type="number" min="1" max="' . $eeSFL_Env['the_max_upload_size']. '" step="1" name="eeUploadMaxFileSize" value="' . $eeSFL_Config['UploadMaxFileSize'] . '" class="eeAdminInput" id="eeUploadMaxFileSize" />
					<div class="eeNote">' . __('Your hosting limits the maximum file upload size to', 'ee-simple-file-list') . ' <strong>' . $eeSFL_Env['the_max_upload_size']. ' MB</strong>.</div>
				
				<br class="eeClearFix" />';
				
				
				
				
				// Get Uploader Info
				$eeOutput .= '<span>' . __('Get Uploader\'s Information?', 'ee-simple-file-list') . '</span><label for="eeGetUploaderInfoYes" class="eeRadioLabel">' . __('Yes', 'ee-simple-file-list') . '</label><input type="radio" name="eeGetUploaderInfo" value="YES" id="eeGetUploaderInfoYes"';
				
				if($eeSFL_Config['GetUploaderInfo'] == 'YES') { $eeOutput .= ' checked'; }
				
				$eeOutput .= '/>
					<label for="eeFormNo" class="eeRadioLabel">' . __('No', 'ee-simple-file-list') . '</label><input type="radio" name="eeGetUploaderInfo" value="NO" id="eeFormNo"';
					
				if($eeSFL_Config['GetUploaderInfo'] != 'YES') { $eeOutput .= ' checked'; }
				
				$eeOutput .= ' />
					<br class="eeClearFix" />
					<div class="eeNote">' . __('Displays a form which must be filled out', 'ee-simple-file-list') . '; ' . __('Name, Email, with optional text Notes.', 'ee-simple-file-list') . '<br />
						' . __('Submissions are sent to the Notice Email.', 'ee-simple-file-list') . '</div>
				<br class="eeClearFix" />';
				
				
				
				
				
				// File Formats Allowed
				$eeOutput .= '<label for="eeFormats">' . __('Allowed File Types', 'ee-simple-file-list') . ':</label><textarea name="eeFileFormats" class="eeAdminInput" id="eeFormats" cols="64" rows="3" />' . $eeSFL_Config['FileFormats'] . '</textarea>
					<div class="eeNote">' . __('Only use the file types you absolutely need, such as', 'ee-simple-file-list') . ' jpg, jpeg, png, pdf, mp4, etc</div>';
					
				
				// Upload Notification
				$eeOutput .= '<label for="eeNotify">' . __('Notice Email', 'ee-simple-file-list') . ':</label><input type="text" name="eeNotify" value="' . $eeSFL_Config['Notify'] . '" class="eeAdminInput" id="eeNotify" size="64" />
						<div class="eeNote">' . __('You will get an email whenever a file is uploaded.', 'ee-simple-file-list') . ' ' .  __('Separate multiple addresses with a comma.', 'ee-simple-file-list') . '</div>';
			}
			
			$eeOutput .= '<br class="eeClearFix" />
			
			<input type="submit" name="submit" id="submit2" value="' . __('SAVE', 'ee-simple-file-list') . '" class="eeAlignRight" />
			
			</fieldset>
	
	</form>
	
</div>';
	
	
?>