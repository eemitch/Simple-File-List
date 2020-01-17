<?php // Simple File List Script: ee-upload-settings.php | Author: Mitchell Bennis | support@simplefilelist.com | Revised: 12.22.2019
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_Log[] = 'Loading Uploader Settings Page ...';
	
// Check for POST and Nonce
if(@$_POST['eePost'] AND check_admin_referer( 'ee-simple-file-list-upload-settings', 'ee-simple-file-list-upload-settings-nonce')) {
	
	// Get all the settings
	$eeSettings = get_option('eeSFL-Settings');
	
	$eeID = $eeSFL_Config['ID'];
	
	if($_POST['eeAllowUploads'] == 'YES') { 
		
		$eeSettings[$eeID]['AllowUploads'] = 'YES';
	
	} elseif($_POST['eeAllowUploads'] == 'USER') { // Only logged in users
		 
		 $eeSettings[$eeID]['AllowUploads'] = 'USER';
		 
	} elseif($_POST['eeAllowUploads'] == 'ADMIN') { // Only logged in users
		 
		 $eeSettings[$eeID]['AllowUploads'] = 'ADMIN';
		 
	} else { 
		$eeSettings[$eeID]['AllowUploads'] = 'NO';
	}
	
	// Get Uploader Info
	if($eeSettings[$eeID]['AllowUploads'] != 'NO') { // Only update if showing these
		
		// YES/NO Checkboxes
		$eeSettings[$eeID]['GetUploaderInfo'] = eeSFL_ProcessCheckboxInput('GetUploaderInfo');
		
		// File Number Limit
		$eeSettings[$eeID]['UploadLimit'] = filter_var(@$_POST['eeUploadLimit'], FILTER_VALIDATE_INT);
		if(!$eeSettings[$eeID]['UploadLimit'] ) { $eeSettings[$eeID]['UploadLimit'] = $eeSFL->eeDefaultUploadLimit; }
		
		// Maximum File Size
		if(@$_POST['eeUploadMaxFileSize']) {
			
			$eeSFL_UploadMaxFileSize = (int) $_POST['eeUploadMaxFileSize'];
			
			// Can't be more than the system allows.
			if(!$eeSFL_Config['UploadMaxFileSize'] OR $eeSFL_Config['UploadMaxFileSize'] > $eeSFL_Env['the_max_upload_size']) { 
				$eeSettings[$eeID]['UploadMaxFileSize'] = $eeSFL_Env['the_max_upload_size'];
			} else {
				$eeSettings[$eeID]['UploadMaxFileSize'] = $eeSFL_UploadMaxFileSize;
			}
			
		} else {
			$eeSFL_UploadMaxFileSize = 1;
		}
		
		// File Formats
		if(@$_POST['eeFileFormats']) { // Strip all but what we need for the comma list of file extensions
			$eeSettings[$eeID]['FileFormats'] = preg_replace("/[^a-z0-9,]/i", "", $_POST['eeFileFormats']);
		}
	}
	
	// Update DB
	update_option('eeSFL-Settings', $eeSettings );
	
	// Update the array with new values
	$eeSFL_Config = $eeSettings[$eeID];
	
	$eeSFL_Confirm = __('Uploader Settings Saved', 'ee-simple-file-list');
	$eeSFL_Log[] = $eeSFL_Confirm;
	
	if($eeSFL_DevMode) {
		$eeSFL_Log[] = $_POST;
	}
}

// Settings Display =========================================
	
$eeOutput .= '<div class="eeSFL_Admin">';
	
if(@$eeSFL_Log['errors']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['errors'], 'notice-error');
} elseif(@$eeSFL_Confirm) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Confirm, 'notice-success');
}
	
$eeOutput .= '<form action="' . $_SERVER['PHP_SELF'] . '?page=' . $eeSFL->eePluginSlug . '&tab=settings&subtab=uploader_settings" method="post" id="eeSFL_Settings">

		<p class="eeSettingsRight"><a class="eeInstructionsLink" href="https://simplefilelist.com/upload-settings/" target="_blank">' . __('Instructions', 'ee-simple-file-list') . '</a>
		<input type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" class="button eeSFL_Save" /></p>
			
		<h2>' . __('Upload Settings', 'ee-simple-file-list') . '</h2>

		<input type="hidden" name="eePost" value="TRUE" />
		<input type="hidden" name="eeListID" value="' . $eeSFL->eeListID . '" />';	
		
		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-upload-settings', 'ee-simple-file-list-upload-settings-nonce', TRUE, FALSE);
		
		$eeOutput .= '<fieldset>
			
			<label for="eeAllowUploads">' . __('Allow File Upload', 'ee-simple-file-list') . '</label>
			
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
				__('Allow anyone to upload, only logged-in users, administrators or nobody.', 'ee-simple-file-list') . '</div>
					
			<br class="eeClearFix" />';
			
			
			if($eeSFL_Config['AllowUploads'] != 'NO') {
				
				// The File List Folder
				$eeOutput .= '
				
				<label for="eeUploadLimit">' . __('Upload Limit', 'ee-simple-file-list') . '</label>
		
				<input type="number" min="1" max="100" step="1" name="eeUploadLimit" value="' . $eeSFL_Config['UploadLimit'] . '" class="eeAdminInput" id="eeUploadLimit" />
					<div class="eeNote">' . __('The maximum number of files that may be uploaded per submission.', 'ee-simple-file-list') . '</div>
					
				<br class="eeClearFix" />';
					
				// Maximum File Size
				if(!$eeSFL_Config['UploadMaxFileSize']) { $eeSFL_Config['UploadMaxFileSize'] = $eeSFL_Env['the_max_upload_size']; }
				
				$eeOutput .= '<label for="eeUploadMaxFileSize">' . __('Maximum File Size', 'ee-simple-file-list') . ' (MB):</label>
				
				<input type="number" min="1" max="' . $eeSFL_Env['the_max_upload_size']. '" step="1" name="eeUploadMaxFileSize" value="' . $eeSFL_Config['UploadMaxFileSize'] . '" class="eeAdminInput" id="eeUploadMaxFileSize" />
					<div class="eeNote">' . __('Your hosting limits the maximum file upload size to', 'ee-simple-file-list') . ' <strong>' . $eeSFL_Env['the_max_upload_size']. ' MB</strong>.</div>
				
				<br class="eeClearFix" />';
				
				
				// File Formats Allowed
				$eeOutput .= '<label for="eeFormats">' . __('Allowed File Types', 'ee-simple-file-list') . ':</label><textarea name="eeFileFormats" class="eeAdminInput" id="eeFormats" cols="64" rows="3" >' . $eeSFL_Config['FileFormats'] . '</textarea>
					<div class="eeNote">' . __('Only use the file types you absolutely need, such as', 'ee-simple-file-list') . ' jpg, jpeg, png, pdf, mp4, etc</div>';
					
			}
			
			$eeOutput .= '<br class="eeClearFix" />
			
			<input type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" class="button eeSFL_Save" />
			
			</fieldset>
	
	</form>
	
</div>';

?>