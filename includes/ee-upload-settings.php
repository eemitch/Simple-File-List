<?php // Simple File List Script: ee-upload-settings.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98' ); // Exit if nonce fails

$eeSFL_FREE_Log['SFL'][] = 'Loading Uploader Settings Page ...';
	
// Check for POST and Nonce
if(@$_POST['eePost'] AND check_admin_referer( 'ee-simple-file-list-upload-settings', 'ee-simple-file-list-upload-settings-nonce')) {
	
	// Get all the settings
	$eeSettings = get_option('eeSFL-Settings');
		
	if($_POST['eeAllowUploads'] == 'YES') { 
		
		$eeSettings[1]['AllowUploads'] = 'YES';
	
	} elseif($_POST['eeAllowUploads'] == 'USER') { // Only logged in users
		 
		 $eeSettings[1]['AllowUploads'] = 'USER';
		 
	} elseif($_POST['eeAllowUploads'] == 'ADMIN') { // Only logged in users
		 
		 $eeSettings[1]['AllowUploads'] = 'ADMIN';
		 
	} else { 
		$eeSettings[1]['AllowUploads'] = 'NO';
	}
	
	// File Number Limit
	$eeSettings[1]['UploadLimit'] = filter_var(@$_POST['eeUploadLimit'], FILTER_VALIDATE_INT);
	if(!$eeSettings[1]['UploadLimit'] OR $eeSettings[1]['UploadLimit'] > 999 ) { $eeSettings[1]['UploadLimit'] = $eeSFL_FREE->eeDefaultUploadLimit; }
	
	// Maximum File Size
	if(@$_POST['eeUploadMaxFileSize']) {
		
		$eeSFL_UploadMaxFileSize = filter_var($_POST['eeUploadMaxFileSize'], FILTER_VALIDATE_INT);
		
		// Can't be more than the system allows.
		if(!$eeSFL_FREE_Config['UploadMaxFileSize'] OR $eeSFL_FREE_Config['UploadMaxFileSize'] > $eeSFL_FREE_Env['the_max_upload_size']) { 
			$eeSettings[1]['UploadMaxFileSize'] = $eeSFL_FREE_Env['the_max_upload_size'];
		} else {
			$eeSettings[1]['UploadMaxFileSize'] = $eeSFL_UploadMaxFileSize;
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
		$eeSettings[1]['FileFormats'] = substr($eeFileFormatsOK, 0, -1);
	}
	

	// YES/NO Checkboxes
	$eeCheckboxes = array(
		'AllowOverwrite'
	);
	foreach( $eeCheckboxes as $eeTerm ) {
		$eeSettings[1][$eeTerm] = eeSFL_FREE_ProcessCheckboxInput($eeTerm);
	}
	
	// Update DB
	update_option('eeSFL-Settings', $eeSettings );
	
	// Update the array with new values
	$eeSFL_FREE_Config = $eeSettings[1];
	
	$eeSFL_Confirm = __('Uploader Settings Saved', 'ee-simple-file-list');
	$eeSFL_FREE_Log['SFL'][] = $eeSFL_Confirm;

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
	
	$eeOutput .= '<fieldset>';
	
	$eeOutput .= '<h4>' . __('List Location', 'ee-simple-file-list') . ':<br />
		' . ABSPATH . $eeSFL_FREE_Config['FileListDir'] . '</h4>';
			
	$eeOutput .= '
		
		<label for="eeAllowUploads">' . __('Allow File Upload', 'ee-simple-file-list') . '</label>
		
		<select name="eeAllowUploads" id="eeAllowUploads">
		
			<option value="YES" style="background-color:#FFFF00;"';

			if($eeSFL_FREE_Config['AllowUploads'] == 'YES') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Anyone Can Upload', 'ee-simple-file-list') . ' !!!</option>
			
			<option value="USER"';

			if($eeSFL_FREE_Config['AllowUploads'] == 'USER') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Only Logged in Users Can Upload', 'ee-simple-file-list') . '</option>
			
			<option value="ADMIN"';

			if($eeSFL_FREE_Config['AllowUploads'] == 'ADMIN') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Only Logged in Admins Can Upload', 'ee-simple-file-list') . '</option>
			
			<option value="NO"';

			if($eeSFL_FREE_Config['AllowUploads'] == 'NO') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Hide the Front Side Uploader Completely', 'ee-simple-file-list') . '</option>
		
		</select>
		<div class="eeNote">' . __('Allow anyone to upload, only logged-in users, administrators or nobody.', 'ee-simple-file-list') . ' <strong>' . __('Please use "Anyone Can Upload" with Caution', 'simple-file-list') . '</strong></div>
		
		<br class="eeClearFix" />
		
		<label for="eeUploadLimit">' . __('Upload Limit', 'ee-simple-file-list') . '</label>

	<input type="number" min="1" max="100" step="1" name="eeUploadLimit" value="' . $eeSFL_FREE_Config['UploadLimit'] . '" class="eeAdminInput" id="eeUploadLimit" />
		<div class="eeNote">' . __('The maximum number of files that may be uploaded per submission.', 'ee-simple-file-list') . '</div>
		
	<br class="eeClearFix" />';
		
			
	// Maximum File Size
	if(!$eeSFL_FREE_Config['UploadMaxFileSize']) { $eeSFL_FREE_Config['UploadMaxFileSize'] = $eeSFL_FREE_Env['the_max_upload_size']; }
	
	$eeOutput .= '<label for="eeUploadMaxFileSize">' . __('Maximum File Size', 'ee-simple-file-list') . ' (MB):</label>
	
	<input type="number" min="1" max="' . $eeSFL_FREE_Env['the_max_upload_size']. '" step="1" name="eeUploadMaxFileSize" value="' . $eeSFL_FREE_Config['UploadMaxFileSize'] . '" class="eeAdminInput" id="eeUploadMaxFileSize" />
		<div class="eeNote">' . __('Your hosting limits the maximum file upload size to', 'ee-simple-file-list') . ' <strong>' . $eeSFL_FREE_Env['the_max_upload_size']. ' MB</strong>.</div>
	
	<br class="eeClearFix" />';
	
	
	// File Formats Allowed
	$eeOutput .= '<label for="eeFormats">' . __('Allowed File Types', 'ee-simple-file-list') . ':</label><textarea name="eeFileFormats" class="eeAdminInput" id="eeFormats" cols="64" rows="3" >' . $eeSFL_FREE_Config['FileFormats'] . '</textarea>
		<div class="eeNote">' . __('Only use the file types you absolutely need, such as', 'ee-simple-file-list') . ' jpg, jpeg, png, pdf, mp4, etc</div>';

	
	// Overwrite or not
	$eeOutput .= '<br class="eeClearFix" />
		
		<label for="eeAllowOverwrite">' . __('Allow Overwriting', 'ee-simple-file-list') . ':</label>
	<input type="checkbox" name="eeAllowOverwrite" value="YES" id="eeAllowOverwrite"';
	
	if( @$eeSFL_FREE_Config['AllowOverwrite'] == 'YES') { $eeOutput .= ' checked="checked"'; }
	
	$eeOutput .= ' /> <p>' . __('Do not number files, overwrite instead.', 'ee-simple-file-list') . '</p>
	
	<div class="eeNote">' . __('Existing files with the same name will be overwritten.', 'ee-simple-file-list') . ' ' .  
		__('Use with caution!', 'ee-simple-file-list') . '</div>
	
	
	<br class="eeClearFix" />
		
		<input type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" class="button eeSFL_Save" />
		
		</fieldset>
	
	</form>
	
</div>';

?>