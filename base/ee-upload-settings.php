<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Loaded: Upload Settings';
	
// Check for POST and Nonce
if(!empty($_POST['eePost']) AND check_admin_referer('eeSFL_Nonce', eeSFL_Nonce)) {
	
	if(!$eeSFLA) {
		
		if($_POST['eeAllowUploads'] == 'YES') { 
			
			$eeSFL->eeListSettings['AllowUploads'] = 'YES';
		
		} elseif($_POST['eeAllowUploads'] == 'USER') { // Only logged in users
			 
			 $eeSFL->eeListSettings['AllowUploads'] = 'USER';
			 
		} elseif($_POST['eeAllowUploads'] == 'ADMIN') { // Only logged in users
			 
			 $eeSFL->eeListSettings['AllowUploads'] = 'ADMIN';
			 
		} else { 
			$eeSFL->eeListSettings['AllowUploads'] = 'NO';
		}
	}
	
	// File Number Limit
	$eeSFL->eeListSettings['UploadLimit'] = filter_var(@$_POST['eeUploadLimit'], FILTER_VALIDATE_INT);
	if(!$eeSFL->eeListSettings['UploadLimit'] OR $eeSFL->eeListSettings['UploadLimit'] > 999 ) { $eeSFL->eeListSettings['UploadLimit'] = $eeSFL->eeDefaultUploadLimit; }
	
	// Maximum File Size
	if(isset($_POST['eeUploadMaxFileSize'])) {
		
		$eeSFL_UploadMaxFileSize = filter_var($_POST['eeUploadMaxFileSize'], FILTER_VALIDATE_INT);
		
		// Can't be more than the system allows.
		if(!$eeSFL->eeListSettings['UploadMaxFileSize'] OR $eeSFL->eeListSettings['UploadMaxFileSize'] > $eeSFL->eeEnvironment['php_actual_max_upload_size']) { 
			$eeSFL->eeListSettings['UploadMaxFileSize'] = $eeSFL->eeEnvironment['php_actual_max_upload_size'];
		} else {
			$eeSFL->eeListSettings['UploadMaxFileSize'] = $eeSFL_UploadMaxFileSize;
		}
	} 
	
	if(!$eeSFL->eeListSettings['UploadMaxFileSize'] OR !is_numeric($eeSFL->eeListSettings['UploadMaxFileSize'] )) {
		$eeSFL_UploadMaxFileSize = 1;
	}
	
	// File Formats
	if(@$_POST['eeFileFormats']) { // Strip all but what we need for the comma list of file extensions
		
		$eeFileFormatsIN = preg_replace("/[^a-z0-9,]/i", "", $_POST['eeFileFormats']);
		$eeFileFormatsIN = explode(',', $eeFileFormatsIN);
		$eeFileFormatsOK = '';
		foreach( $eeFileFormatsIN as $eeKey => $eeValue){
			$eeValue = trim($eeValue);
			if(in_array($eeValue, $eeSFL->eeForbiddenTypes)) {
				$eeSFL->eeLog['errors'][] = __('This file type is not allowed', 'ee-simple-file-list') . ': ' . $eeValue;
			} elseif($eeValue) {
				$eeFileFormatsOK .= $eeValue . ',';
			}
		}
		$eeSFL->eeListSettings['FileFormats'] = substr($eeFileFormatsOK, 0, -1);
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
		$eeSFL->eeListSettings[$eeTerm] = eeSFL_ProcessCheckboxInput($eeTerm);
	}
	
	// Show Abore or Below List
	if(@$_POST['eeUploadPosition'] == 'Above') { $eeSFL->eeListSettings['UploadPosition'] = 'Above'; }
		else { $eeSFL->eeListSettings['UploadPosition'] = 'Below'; }
	
	
	// Update DB
	update_option('eeSFL_Settings_' . $eeSFL->eeListID, $eeSFL->eeListSettings );
	
	$eeSFL->eeLog['messages'][] = __('Upload Settings Saved', 'ee-simple-file-list');
}

// Settings Display =========================================
	
// User Messaging
$eeOutput .= $eeSFL_Messaging->eeSFL_ResultsNotification();
	
$eeOutput .= '

<form action="' . $eeSFL->eeSFL_GetThisURL() . '" method="post" id="eeSFL_Settings">
<input type="hidden" name="eePost" value="TRUE" />';	
$eeOutput .= wp_nonce_field('eeSFL_Nonce', eeSFL_Nonce, TRUE, FALSE);
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
	
	<div class="eeColLeft"><div class="eeSettingsTile">
		
		<h2>' . __('File Upload Restrictions', 'ee-simple-file-list') . '</h2>';
	
		if(!$eeSFLA) {
		
		$eeOutput .= '

		<fieldset>
		<legend>' . __('Who Can Upload Files', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Restrict To', 'ee-simple-file-list') . '
		<select name="eeAllowUploads" id="eeAllowUploads">
		
			<option value="YES" style="background-color:#FFFF00;"';

			if($eeSFL->eeListSettings['AllowUploads'] == 'YES') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Anyone Can Upload', 'ee-simple-file-list') . ' !!!</option>
			
			<option value="USER"';

			if($eeSFL->eeListSettings['AllowUploads'] == 'USER') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Only Logged in Users Can Upload', 'ee-simple-file-list') . '</option>
			
			<option value="ADMIN"';

			if($eeSFL->eeListSettings['AllowUploads'] == 'ADMIN') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Only Logged in Admins Can Upload', 'ee-simple-file-list') . '</option>
			
			<option value="NO"';

			if($eeSFL->eeListSettings['AllowUploads'] == 'NO') { $eeOutput .= ' selected'; }
			
			$eeOutput .= '>' . __('Hide the Front Side Uploader Completely', 'ee-simple-file-list') . '</option>
		
		</select></label></div>
		
		<div class="eeNote">' . __('Allow anyone to upload, only logged-in users, administrators or nobody.', 'ee-simple-file-list') . ' <strong>' . __('Please use "Anyone Can Upload" with Caution', 'simple-file-list') . '</strong></div>
		
		</fieldset>';
		
		} else {
			
			$eeOutput .= '<p>' . __('This setting has moved to the List Access Settings tab.', 'ee-simple-file-list') . '</p> 
			<a class="button" href="' . $eeSFL->eeSFL_GetThisURL(FALSE) . '?page=ee-simple-file-list-pro&tab=settings&subtab=list_access&eeListID=' . $eeSFL->eeListID . '">' . __('Go There', 'ee-simple-file-list') . '</a>';
		}
		
		$eeOutput .= '</div>
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Allow File Overwrite', 'ee-simple-file-list') . '</h2>

		<fieldset>
		<legend>' . __('Overwrite or Save as New', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Overwrite', 'ee-simple-file-list') . '
		<input type="checkbox" name="eeAllowOverwrite" value="YES" id="eeAllowOverwrite"';
		
		if( @$eeSFL->eeListSettings['AllowOverwrite'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></label></div>
		
		<div class="eeNote">' . __('Do not save new copies of files with the same name. Existing files will be overwritten.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
			
		</div>
		
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Upload Submitter Information', 'ee-simple-file-list') . '</h2>

		<fieldset>
		
		<legend>' . __('Upload Description', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Input', 'ee-simple-file-list') . '
		<input type="checkbox" name="eeGetUploaderDesc" value="YES" id="eeGetUploaderDesc"';
		
		if( $eeSFL->eeListSettings['GetUploaderDesc'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></label></div>
		
		<div class="eeNote">' . __('Display an input allowing the submitter to add a text description of the file upload.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		
		<fieldset>
		
		<legend>' . __('Submitter Information', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Require', 'ee-simple-file-list') . '
		<input type="checkbox" name="eeGetUploaderInfo" value="YES" id="eeGetUploaderInfo"';
		
		if( $eeSFL->eeListSettings['GetUploaderInfo'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
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
		<input type="number" min="1" max="100" step="1" name="eeUploadLimit" value="' . $eeSFL->eeListSettings['UploadLimit'] . '" id="eeUploadLimit" /></label></div>
		
		<div class="eeNote">' . __('The maximum number of files that may be uploaded per submission.', 'ee-simple-file-list') . '</div>
		
		</fieldset>';
	
		// Maximum File Size
		if(!$eeSFL->eeListSettings['UploadMaxFileSize']) { $eeSFL->eeListSettings['UploadMaxFileSize'] = $eeSFL->eeEnvironment['php_actual_max_upload_size']; }
	
		$eeOutput .= '
		
		<fieldset>
		<legend>' . __('Maximum File Size', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Size', 'ee-simple-file-list') . ' (MB)
		<input type="number" min="1" max="' . $eeSFL->eeEnvironment['php_actual_max_upload_size']. '" step="1" name="eeUploadMaxFileSize" value="' . $eeSFL->eeListSettings['UploadMaxFileSize'] . '" id="eeUploadMaxFileSize" /></label></div>
		
		<div class="eeNote">' . __('Your hosting limits the maximum file upload size to', 'ee-simple-file-list') . ' <strong>' . $eeSFL->eeEnvironment['php_actual_max_upload_size']. ' MB</strong>.</div>
	
		</fieldset>
		
		</div>
		
		
		<div class="eeSettingsTile">
		
		<h2>' . __('Allowed File Types', 'ee-simple-file-list') . '</h2>
	
		<fieldset>
		<legend>File Extensions</legend>
		<div><label>' . __('File Types', 'ee-simple-file-list') . '<br />
		<textarea name="eeFileFormats" id="eeFormats" cols="32" rows="3" >' . esc_textarea($eeSFL->eeListSettings['FileFormats']) . '</textarea></label></div>
		
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
			if($eeSFL->eeListSettings['UploadPosition'] == 'Above') { $eeOutput .= ' selected="selected"'; }
			$eeOutput .= '>' . __('Above', 'ee-simple-file-list') . '</option>
			
			<option value="Below"';
			if($eeSFL->eeListSettings['UploadPosition'] == 'Below') { $eeOutput .= ' selected="selected"'; }
			$eeOutput .= '>' . __('Below', 'ee-simple-file-list') . '</option>
		</select></label></div>
		
		<div class="eeNote">' . __('Choose to show the upload form either above or below the file list.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		<fieldset>
		<legend>' . __('Confirmation', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Results', 'ee-simple-file-list') . '
		<input type="checkbox" name="eeUploadConfirm" value="YES" id="eeUploadConfirm"';
		
		if( $eeSFL->eeListSettings['UploadConfirm'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /></label></div>
		
		<div class="eeNote">' . __('Show a resulting list of the files uploaded, or proceed directly back to the file list.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		<fieldset>
		<legend>' . __('Show Upload Limits', 'ee-simple-file-list') . '</legend>
		<div><label>' . __('Show Limits', 'ee-simple-file-list') . '
		<input type="checkbox" name="eeShowUploadLimits" value="YES" id="eeShowUploadLimits"';
		
		if( $eeSFL->eeListSettings['ShowUploadLimits'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
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