<?php // Simple File List Script: ee-upload-settings.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98' ); // Exit if nonce fails

$eeSFL_FREE_Log['RunTime'][] = 'Loading Uploader Settings Page ...';
	
// Check for POST and Nonce
if(@$_POST['eePost'] AND check_admin_referer( 'ee-simple-file-list-upload-settings', 'ee-simple-file-list-upload-settings-nonce')) {
		
	// Upload Form Visibility
	if($_POST['eeAllowUploads'] == 'YES') { 
		
		$eeSFL_Settings['AllowUploads'] = 'YES';
	
	} elseif($_POST['eeAllowUploads'] == 'USER') { // Only logged in users
		 
		 $eeSFL_Settings['AllowUploads'] = 'USER';
		 
	} elseif($_POST['eeAllowUploads'] == 'ADMIN') { // Only logged in users
		 
		 $eeSFL_Settings['AllowUploads'] = 'ADMIN';
		 
	} else { 
		$eeSFL_Settings['AllowUploads'] = 'NO';
	}
	
	// File Number Limit
	$eeSFL_Settings['UploadLimit'] = filter_var(@$_POST['eeUploadLimit'], FILTER_VALIDATE_INT);
	if(!$eeSFL_Settings['UploadLimit'] OR $eeSFL_Settings['UploadLimit'] > 999 ) { $eeSFL_Settings['UploadLimit'] = $eeSFL_FREE->eeDefaultUploadLimit; }
	
	// Maximum File Size
	if(@$_POST['eeUploadMaxFileSize']) {
		
		$eeSFL_UploadMaxFileSize = filter_var($_POST['eeUploadMaxFileSize'], FILTER_VALIDATE_INT);
		
		// Can't be more than the system allows.
		if(!$eeSFL_Settings['UploadMaxFileSize'] OR $eeSFL_Settings['UploadMaxFileSize'] > $eeSFL_FREE_Env['the_max_upload_size']) { 
			$eeSFL_Settings['UploadMaxFileSize'] = $eeSFL_FREE_Env['the_max_upload_size'];
		} else {
			$eeSFL_Settings['UploadMaxFileSize'] = $eeSFL_UploadMaxFileSize;
		}
		
	} else {
		$eeSFL_UploadMaxFileSize = 1;
	}
	
	// File Formats
	if(@$_POST['eeFileFormats']) { // Strip all but what we need for the comma list of file extensions
		
		$eeFileFormatsIN = preg_replace("/[^a-z0-9,]/i", "", $_POST['eeFileFormats']);
		$eeFileFormatsIN = explode(',', $eeFileFormatsIN);
		$eeFileFormatsOK = '';
		foreach( $eeFileFormatsIN as $eeKey => $eeValue){
			$eeValue = trim($eeValue);
			if(in_array($eeValue, $eeSFL_FREE->eeForbiddenTypes)) {
				$eeSFL_FREE_Log['errors'][] = 'This file type is not allowed: ' . $eeValue;
			} elseif($eeValue) {
				$eeFileFormatsOK .= $eeValue . ',';
			}
		}
		$eeSFL_Settings['FileFormats'] = substr($eeFileFormatsOK, 0, -1);
	}
	

	// YES/NO Checkboxes
	$eeCheckboxes = array(
		'AllowOverwrite'
		,'ShowUploadLimits'
		,'GetUploaderInfo'
	);
	foreach( $eeCheckboxes as $eeTerm ) {
		$eeSFL_Settings[$eeTerm] = eeSFL_FREE_ProcessCheckboxInput($eeTerm);
	}
	
	// Update DB
	if( update_option('eeSFL_Settings_1', $eeSFL_Settings) ) {
		$eeSFL_Confirm = __('Settings Saved', 'ee-simple-file-list');
		$eeSFL_FREE_Log['RunTime'][] = $eeSFL_Confirm;
	} else {
		$eeSFL_FREE_Log['RunTime'][] = '!!! The database was not updated.';
	}
}

// Settings Display =========================================
	
$eeOutput .= '<div class="eeSFL_Admin">';
	
if(@$eeSFL_FREE_Log['errors']) { 
	$eeOutput .=  eeSFL_FREE_ResultsDisplay($eeSFL_FREE_Log['errors'], 'notice-error');
} elseif(@$eeSFL_Confirm) { 
	$eeOutput .=  eeSFL_FREE_ResultsDisplay($eeSFL_Confirm, 'notice-success');
}
	
$eeOutput .= '<form action="' . admin_url() . '?page=' . $eeSFL_FREE->eePluginSlug . '&tab=settings&subtab=uploader_settings" method="post" id="eeSFL_Settings">

	<p class="eeSettingsRight"><a class="eeInstructionsLink" href="https://simplefilelist.com/upload-settings/" target="_blank">' . __('Instructions', 'ee-simple-file-list') . '</a>
	
	<input type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" class="button eeSFL_Save" /></p>
			
	<h2>' . __('Upload Settings', 'ee-simple-file-list') . '</h2>

	<input type="hidden" name="eePost" value="TRUE" />';	
	
	$eeOutput .= wp_nonce_field( 'ee-simple-file-list-upload-settings', 'ee-simple-file-list-upload-settings-nonce', TRUE, FALSE);
	
	$eeOutput .= '
	
	<div class="eeSFL_AdminHalfLeft">
	
	<fieldset class="eeSFL_SettingsBlock">
	
		<h3>' . __('File Upload Restrictions', 'ee-simple-file-list') . '</h3>
		
		<label for="eeAllowUploads">' . __('Allow File Upload', 'ee-simple-file-list') . '</label>
		
		<select name="eeAllowUploads" id="eeAllowUploads">
		
			<option value="YES" style="background-color:#FFFF00;"';

			if($eeSFL_Settings['AllowUploads'] == 'YES') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Anyone Can Upload', 'ee-simple-file-list') . ' !!!</option>
			
			<option value="USER"';

			if($eeSFL_Settings['AllowUploads'] == 'USER') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Only Logged in Users Can Upload', 'ee-simple-file-list') . '</option>
			
			<option value="ADMIN"';

			if($eeSFL_Settings['AllowUploads'] == 'ADMIN') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Only Logged in Admins Can Upload', 'ee-simple-file-list') . '</option>
			
			<option value="NO"';

			if($eeSFL_Settings['AllowUploads'] == 'NO') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Hide the Front Side Uploader Completely', 'ee-simple-file-list') . '</option>
		
		</select>
		<div class="eeNote">' . __('Allow anyone to upload, only logged-in users, administrators or nobody.', 'ee-simple-file-list') . ' <strong>' . __('Please use "Anyone Can Upload" with Caution', 'simple-file-list') . '</strong></div>
		
		
		<label for="eeAllowOverwrite">' . __('Allow Overwriting', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeAllowOverwrite" value="YES" id="eeAllowOverwrite"';
		
		if( @$eeSFL_Settings['AllowOverwrite'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /> <p>' . __('Do not number files, overwrite instead.', 'ee-simple-file-list') . '</p>
		
		<div class="eeNote">' . __('Existing files with the same name will be overwritten.', 'ee-simple-file-list') . ' ' .  
			__('Use with caution!', 'ee-simple-file-list') . '</div>
			
		
		
		<label for="eeGetUploaderInfo">' . __('Get File Owner Information', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeGetUploaderInfo" value="YES" id="eeGetUploaderInfo"';
		
		if( $eeSFL_Settings['GetUploaderInfo'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /> <p>' . __('Get name, email and description', 'ee-simple-file-list') . '</p>
		
		<div class="eeNote">' . __('Display a form which must be filled out before a file is uploaded; Name, Email, Description', 'ee-simple-file-list') . '<br />
		' . __('If the user is logged in, the name and email will automatically be captured regardless. Descriptions are always optional.', 'ee-simple-file-list') . '<br />
			' . __('This user information can be displayed along with the file.', 'ee-simple-file-list') . '</div>
		
		
		
	</fieldset>
			
	</div>		
	
	
	
			
	<div class="eeSFL_AdminHalfRight">		
			
	<fieldset class="eeSFL_SettingsBlock">
	
	<h3>' . __('Upload Job Limits', 'ee-simple-file-list') . '</h3>
	
	<label for="eeUploadLimit">' . __('Upload Limit', 'ee-simple-file-list') . '</label>
		<input type="number" min="1" max="100" step="1" name="eeUploadLimit" value="' . $eeSFL_Settings['UploadLimit'] . '" class="eeAdminInput" id="eeUploadLimit" />
		<div class="eeNote">' . __('The maximum number of files that may be uploaded per submission.', 'ee-simple-file-list') . '</div>';
	
	// Maximum File Size
	if(!$eeSFL_Settings['UploadMaxFileSize']) { $eeSFL_Settings['UploadMaxFileSize'] = $eeSFL_FREE_Env['the_max_upload_size']; }
	
	$eeOutput .= '<label for="eeUploadMaxFileSize">' . __('Maximum File Size', 'ee-simple-file-list') . ' (MB):</label>
	
	<input type="number" min="1" max="' . $eeSFL_FREE_Env['the_max_upload_size']. '" step="1" name="eeUploadMaxFileSize" value="' . $eeSFL_Settings['UploadMaxFileSize'] . '" class="eeAdminInput" id="eeUploadMaxFileSize" />
		<div class="eeNote">' . __('Your hosting limits the maximum file upload size to', 'ee-simple-file-list') . ' <strong>' . $eeSFL_FREE_Env['the_max_upload_size']. ' MB</strong>.</div>';
	
	
	// File Formats Allowed
	$eeOutput .= '<label for="eeFormats">' . __('Allowed File Types', 'ee-simple-file-list') . ':</label><textarea name="eeFileFormats" class="eeAdminInput" id="eeFormats" cols="64" rows="3" >' . $eeSFL_Settings['FileFormats'] . '</textarea>
		<div class="eeNote">' . __('Only use the file types you absolutely need, such as', 'ee-simple-file-list') . ' jpg, jpeg, png, pdf, mp4, etc</div>
		
		
	<label for="eeShowUploadLimits">' . __('Show Upload Limits', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeShowUploadLimits" value="YES" id="eeShowUploadLimits"';
		
		if( $eeSFL_Settings['ShowUploadLimits'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /> <p>' . __('Display upload restrictions on the front-end.', 'ee-simple-file-list') . '</p>
		
		<div class="eeNote">' . __('Show the user file size, number and file type restrictions.', 'ee-simple-file-list') . '</div>
		
	</fieldset>
	
	</div>
		
	<input type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" class="button eeSFL_Save" />
	
	</form>
	
</div>';

?>