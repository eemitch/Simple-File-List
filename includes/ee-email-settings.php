<?php // Simple File List Script: ee-email-settings.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_FREE_Log['RunTime'][] = 'Loading Email Settings Page ...';

// Check for POST and Nonce
if(@$_POST['eePost'] AND check_admin_referer( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce')) {
	
	// YES/NO Checkboxes
	$eeCheckboxes = array(
		'Notify'
	);
	foreach( $eeCheckboxes as $eeTerm){
		$eeSFL_Settings[$eeTerm] = eeSFL_FREE_ProcessCheckboxInput($eeTerm);
	}
	
	$eeDelivery = array('To', 'Cc', 'Bcc');
	
	foreach( $eeDelivery as $eeField ) {
		
		if( strpos($_POST['eeNotify' . $eeField], '@') ) {
			
			$eeAddresses = $eeSFL_FREE->eeSFL_SanitizeEmailString($_POST['eeNotify' . $eeField]);
			$eeSFL_Settings['Notify' . $eeField] = $eeAddresses;
		}
	}
	
	// Message Options
	$eeTextInputs = array(
		'NotifyFrom'
		,'NotifyFromName'
		,'NotifySubject'
	);
	foreach( $eeTextInputs as $eeTerm){
		$eeSFL_Settings[$eeTerm] = eeSFL_FREE_ProcessTextInput($eeTerm);
	}
	
	$eeSFL_Settings['NotifyMessage'] = eeSFL_FREE_ProcessTextInput('NotifyMessage', 'textarea');
	
	// Update DB
	if( update_option('eeSFL_Settings_1', $eeSFL_Settings) ) {
		$eeSFL_Confirm = __('Settings Saved', 'ee-simple-file-list');
		$eeSFL_FREE_Log['RunTime'][] = 'Settings Saved';
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

// Begin the Form	
$eeOutput .= '

<form action="' . admin_url() . '?page=' . $eeSFL_FREE->eePluginSlug . '&tab=settings&subtab=email_settings" method="post" id="eeSFL_Settings">

	<p class="eeSettingsRight"><a class="eeInstructionsLink" href="https://simplefilelist.com/notification-settings/" target="_blank">' . __('Instructions', 'ee-simple-file-list') . '</a>
		<input type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" class="button eeSFL_Save" /></p>
		
	<input type="hidden" name="eePost" value="TRUE" />
	
	<h2>' . __('Notifications', 'ee-simple-file-list') . '</h2>
	
	<br class="eeClearFix" />';	
	
	$eeOutput .= wp_nonce_field( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce', TRUE, FALSE);
	
	if(strlen($eeSFL_Settings['NotifyTo']) < 5) {
		$eeSFL_Settings['NotifyTo'] = get_option('admin_email');
	}
	if(strlen($eeSFL_Settings['NotifyFrom']) < 5) {
		$eeSFL_Settings['NotifyFrom'] = get_option('admin_email');
	}
	
	$eeOutput .= '<fieldset class="eeSFL_SettingsFull">
	
	<h3>' . __('Enable Notifications', 'ee-simple-file-list') . '</h3>
	
	<label for="eeNotify">' . __('Enable Notifications', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeNotify" value="YES" id="eeNotify"'; 
	if(@$eeSFL_Settings['Notify'] == 'YES') { $eeOutput .= ' checked'; }
	$eeOutput .= ' /> <div class="eeNote">' . __('Send an email notification when a file is uploaded on the front-side of the website.', 'ee-simple-file-list') . '</div>
	
	</fieldset>
	
	<fieldset class="eeSFL_SettingsFull">
	
	<h3>' . __('Notice Recipients', 'ee-simple-file-list') . '</h3>
	
	<label for="eeNotifyTo">' . __('Notice Email', 'ee-simple-file-list') . ':</label>
			<input type="text" name="eeNotifyTo" value="' . @$eeSFL_Settings['NotifyTo'] . '" class="eeAdminInput" id="eeNotifyTo" size="64" />
				<div class="eeNote">' . __('Send an email whenever a file is uploaded.', 'ee-simple-file-list') . ' ' .  __('Separate multiple addresses with a comma.', 'ee-simple-file-list') . '</div>
				
	<hr />
	
	<label for="eeNotifyCc">' . __('Copy to Email', 'ee-simple-file-list') . ':</label>
	<input type="text" name="eeNotifyCc" value="' . @$eeSFL_Settings['NotifyCc'] . '" class="eeAdminInput" id="eeNotifyCc" size="64" />
		<div class="eeNote">' . __('Copy notice emails here.', 'ee-simple-file-list') . '</div>
	
	
	<label for="eeNotifyBcc">' . __('Blind Copy to Email', 'ee-simple-file-list') . ':</label>
	<input type="text" name="eeNotifyBcc" value="' . @$eeSFL_Settings['NotifyBcc'] . '" class="eeAdminInput" id="eeNotifyBcc" size="64" />
		<div class="eeNote">' . __('Blind copy notice emails here.', 'ee-simple-file-list') . '</div>
	
	</fieldset>
	
	
	<fieldset class="eeSFL_SettingsFull">	
	
	<h3>' . __('Message Details', 'ee-simple-file-list') . '</h3>
	
	<label for="eeNotifyFrom">' . __('Sender Email', 'ee-simple-file-list') . ':</label>
	<input type="email" name="eeNotifyFrom" value="' . @$eeSFL_Settings['NotifyFrom'] . '" class="eeAdminInput" id="eeNotifyFrom" size="64" />
		<div class="eeNote">' . __('The notification message\'s reply-to address.', 'ee-simple-file-list') . '</div>
	
	
	<label for="eeNotifyFromName">' . __('Sender Name', 'ee-simple-file-list') . ':</label>
	<input type="text" name="eeNotifyFromName" value="' . stripslashes(@$eeSFL_Settings['NotifyFromName']) . '" class="eeAdminInput" id="eeNotifyFromName" size="64" />
		<div class="eeNote">' . __('The visible name in the From field.', 'ee-simple-file-list') . '</div>
	
	
	<label for="eeNotifySubject">' . __('Notification Subject', 'ee-simple-file-list') . ':</label>
	<input type="text" name="eeNotifySubject" value="' . stripslashes(@$eeSFL_Settings['NotifySubject']) . '" class="eeAdminInput" id="eeNotifySubject" size="64" />
		<div class="eeNote">' . __('The notification email subject line.', 'ee-simple-file-list') . '</div>';
		
	
	if(!@$eeSFL_Settings['NotifyMessage']) { $eeSFL_Settings['NotifyMessage'] = $eeSFL_FREE->eeNotifyMessageDefault; }
	
	$eeOutput .= '<label for="eeNotifyMessage">' . __('Message Text', 'ee-simple-file-list') . ':</label>
	<textarea name="eeNotifyMessage" class="eeAdminInput" id="eeNotifyMessage" cols="64" rows="12" >' . stripslashes($eeSFL_Settings['NotifyMessage']) . '</textarea>
		<div class="eeNote">' . __('This will be the text for the file upload notification messages.', 'ee-simple-file-list') . '<br />
			' . __('To insert file information and link, use this shortcode:', 'ee-simple-file-list') . ' [file-list]<br />
			' . __('To insert a link pointing to the file list, use this shortcode:', 'ee-simple-file-list') . ' [web-page]</div>
	
	';
	
	
	$eeOutput .= '
	
	</fieldset>
	
	<input type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" class="button eeSFL_Save" />
	
</form>
	
</div>';
	
?>