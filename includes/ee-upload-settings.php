<?php // Simple File List Script: ee-upload-settings.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98' ); // Exit if nonce fails

$eeSFL_BASE_Log['RunTime'][] = 'Loading Uploader Settings Page ...';
	
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
	if(!$eeSFL_Settings['UploadLimit'] OR $eeSFL_Settings['UploadLimit'] > 999 ) { $eeSFL_Settings['UploadLimit'] = $eeSFL_BASE->eeDefaultUploadLimit; }
	
	// Maximum File Size
	if(@$_POST['eeUploadMaxFileSize']) {
		
		$eeSFL_UploadMaxFileSize = filter_var($_POST['eeUploadMaxFileSize'], FILTER_VALIDATE_INT);
		
		// Can't be more than the system allows.
		if(!$eeSFL_Settings['UploadMaxFileSize'] OR $eeSFL_Settings['UploadMaxFileSize'] > $eeSFL_BASE_Env['the_max_upload_size']) { 
			$eeSFL_Settings['UploadMaxFileSize'] = $eeSFL_BASE_Env['the_max_upload_size'];
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
			if(in_array($eeValue, $eeSFL_BASE->eeForbiddenTypes)) {
				$eeSFL_BASE_Log['errors'][] = 'This file type is not allowed: ' . $eeValue;
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
		,'GetUploaderDesc'
		,'UploadConfirm'
	);
	foreach( $eeCheckboxes as $eeTerm ) {
		$eeSFL_Settings[$eeTerm] = eeSFL_BASE_ProcessCheckboxInput($eeTerm);
	}
	
	// Show Abore or Below List
	if(@$_POST['eeUploadPosition'] == 'Above') { $eeSFL_Settings['UploadPosition'] = 'Above'; }
		else { $eeSFL_Settings['UploadPosition'] = 'Below'; }
	
	// Update DB
	if( update_option('eeSFL_Settings_1', $eeSFL_Settings) ) {
		$eeConfirm = __('Settings Saved', 'ee-simple-file-list');
		$eeSFL_BASE_Log['RunTime'][] = $eeConfirm;
	} else {
		$eeSFL_BASE_Log['RunTime'][] = '!!! The database was not updated.';
	}
}

// Settings Display =========================================
	
$eeOutput .= '<div class="eeSFL_Admin">';
	
if( count($eeSFL_BASE_Log['errors']) ) { 
	$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeSFL_BASE_Log['errors'], 'notice-error');
} elseif($eeConfirm) { 
	$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeConfirm, 'notice-success');
}
	
$eeOutput .= '

<form action="' . admin_url() . '?page=' . $eeSFL_BASE->eePluginSlug . '&tab=settings&subtab=uploader_settings" method="post" id="eeSFL_Settings">
<input type="hidden" name="eePost" value="TRUE" />';	
$eeOutput .= wp_nonce_field( 'ee-simple-file-list-upload-settings', 'ee-simple-file-list-upload-settings-nonce', TRUE, FALSE);
$eeOutput .= '

<div class="eeColInline eeSettingsTile">
				
	<div class="eeColHalfLeft">
	
		<h1>' . __('File Upload Settings', 'ee-simple-file-list') . '</h1>
		<a class="" href="https://simplefilelist.com/upload-settings/" target="_blank">' . __('Instructions', 'ee-simple-file-list') . '</a>
	
	</div>
	
	<div class="eeColHalfRight">
	
		<input class="button" type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" />
	
	</div>

</div>

<div class="eeColumns">		
		
	<!-- Left Column -->
	
	<div class="eeColLeft">
		
		<div class="eeSettingsTile">
		
		<h2>' . __('File Upload Restrictions', 'ee-simple-file-list') . '</h2>

		<fieldset>
		<legend>' . __('Who Can Upload Files', 'ee-simple-file-list-pro') . '</legend>
		<div><label>' . __('Restrict To', 'ee-simple-file-list') . '
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
		
		</select></label></div>
		
		<div class="eeNote">' . __('Allow anyone to upload, only logged-in users, administrators or nobody.', 'ee-simple-file-list') . ' <strong>' . __('Please use "Anyone Can Upload" with Caution', 'simple-file-list') . '</strong></div>
		
		</fieldset>
		
		</div>
		
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Allow File Overwrite', 'ee-simple-file-list') . '</h2>

		<fieldset>
		<legend>' . __('Overwrite or Save as New', 'ee-simple-file-list-pro') . '</legend>
		<div><label>' . __('Overwrite', 'ee-simple-file-list') . '
		<input type="checkbox" name="eeAllowOverwrite" value="YES" id="eeAllowOverwrite"';
		
		if( @$eeSFL_Settings['AllowOverwrite'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></label></div>
		
		<div class="eeNote">' . __('Do not save new copies of files with the same name. Existing files will be overwritten.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
			
		</div>
		
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Upload Submitter Information', 'ee-simple-file-list') . '</h2>

		<fieldset>
		
		<legend>' . __('Upload Description', 'ee-simple-file-list-pro') . '</legend>
		<div><label>' . __('Show Input', 'ee-simple-file-list') . '
		<input type="checkbox" name="eeGetUploaderDesc" value="YES" id="eeGetUploaderDesc"';
		
		if( $eeSFL_Settings['GetUploaderDesc'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></label></div>
		
		<div class="eeNote">' . __('Display an input allowing the submitter to add a text description of the file upload.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		
		<legend>' . __('Submitter Information', 'ee-simple-file-list-pro') . '</legend>
		<div><label>' . __('Require', 'ee-simple-file-list') . '
		<input type="checkbox" name="eeGetUploaderInfo" value="YES" id="eeGetUploaderInfo"';
		
		if( $eeSFL_Settings['GetUploaderInfo'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></label></div>
		
		<div class="eeNote">' . __('Display a form with name and email which is required to be filled out.', 'ee-simple-file-list') . '<br />
		' . __('If the user is logged in the form will not appear. The name and email address will be automatically captured from the user data.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		</div>
			
	</div>
	
	
	
	<!-- Right Column -->
	
	<div class="eeColRight">
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Upload Job Limits', 'ee-simple-file-list') . '</h2>
	
		<fieldset>
		<legend>' . __('Maximum Files Limit', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('File Limit', 'ee-simple-file-list') . '
		<input type="number" min="1" max="100" step="1" name="eeUploadLimit" value="' . $eeSFL_Settings['UploadLimit'] . '" id="eeUploadLimit" /></label></div>
		
		<div class="eeNote">' . __('The maximum number of files that may be uploaded per submission.', 'ee-simple-file-list') . '</div>
		
		</fieldset>';
	
		// Maximum File Size
		if(!$eeSFL_Settings['UploadMaxFileSize']) { $eeSFL_Settings['UploadMaxFileSize'] = $eeSFL_BASE_Env['the_max_upload_size']; }
	
		$eeOutput .= '
		
		<fieldset>
		<legend>' . __('Maximum File Size', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Size', 'ee-simple-file-list') . ' (MB)
		<input type="number" min="1" max="' . $eeSFL_BASE_Env['the_max_upload_size']. '" step="1" name="eeUploadMaxFileSize" value="' . $eeSFL_Settings['UploadMaxFileSize'] . '" id="eeUploadMaxFileSize" /></label></div>
		
		<div class="eeNote">' . __('Your hosting limits the maximum file upload size to', 'ee-simple-file-list') . ' <strong>' . $eeSFL_BASE_Env['the_max_upload_size']. ' MB</strong>.</div>
	
		</fieldset>
		
		</div>
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Allowed File Types', 'ee-simple-file-list') . '</h2>
	
		<fieldset>
		<legend>File Extensions</legend>
		<div><label>' . __('File Types', 'ee-simple-file-list') . '<br />
		<textarea name="eeFileFormats" id="eeFormats" cols="32" rows="3" >' . $eeSFL_Settings['FileFormats'] . '</textarea></label></div>
		
		<div class="eeNote">' . __('Only use the file types you absolutely need, such as', 'ee-simple-file-list') . ' jpg, jpeg, png, pdf, mp4, etc</div>
		
		</fieldset>
		
		</div>
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Display', 'ee-simple-file-list') . '</h2>
		
		<fieldset>
		<legend>' . __('Upload Form Position', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Position', 'ee-simple-file-list') . '<select name="eeUploadPosition" id="eeUploadPosition">
			<option value="">' . __('Choose Position', 'ee-simple-file-list') . '</option>
			
			<option value="Above"';
			if($eeSFL_Settings['UploadPosition'] == 'Above') { $eeOutput .= ' selected="selected"'; }
			$eeOutput .= '>' . __('Above', 'ee-simple-file-list') . '</option>
			
			<option value="Below"';
			if($eeSFL_Settings['UploadPosition'] == 'Below') { $eeOutput .= ' selected="selected"'; }
			$eeOutput .= '>' . __('Below', 'ee-simple-file-list') . '</option>
		</select></label></div>
		
		<div class="eeNote">' . __('Choose to show the upload form either above or below the file list.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		<fieldset>
		<legend>' . __('Confirmation', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Results', 'ee-simple-file-list') . '
		<input type="checkbox" name="eeUploadConfirm" value="YES" id="eeUploadConfirm"';
		
		if( $eeSFL_Settings['UploadConfirm'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></label></div>
		
		<div class="eeNote">' . __('Show a resulting list of the files uploaded, or proceed directly back to the file list.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		<fieldset>
		<legend>' . __('Show Upload Limits', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Limits', 'ee-simple-file-list') . '
		<input type="checkbox" name="eeShowUploadLimits" value="YES" id="eeShowUploadLimits"';
		
		if( $eeSFL_Settings['ShowUploadLimits'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></label></div>
		
		<div class="eeNote">' . __('Display upload limitations on the front-end, such as size and types allowed.', 'ee-simple-file-list') . '</div>
		
		</fieldset>  
	
		</div>
	
	</div>

</div>


<div class="eeColInline eeSettingsTile">
				
	<input class="button" type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" />
			
</div>

</form>';

?>