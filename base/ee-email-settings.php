<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Loaded: Email Settings';

// echo '<pre>'; print_r($_POST); echo '</pre>'; exit;

// Check for POST and Nonce
if(!empty($_POST['eePost']) AND check_admin_referer('eeSFL_Nonce', eeSFL_Nonce)) {

	// YES/NO Checkboxes
	$eeCheckboxes = array(
		'Notify'
	);
	foreach( $eeCheckboxes as $eeTerm){
		$eeSFL->eeListSettings[$eeTerm] = eeSFL_ProcessCheckboxInput($eeTerm);
	}
	
	// Extension Check
	if($eeSFLA) {
		require_once(WP_PLUGIN_DIR . '/ee-simple-file-list-access/includes/eeSFLA_NoticeSettingsProcess.php');
	} 
	
	$eeDelivery = array('To', 'Cc', 'Bcc');
	
	foreach( $eeDelivery as $eeField ) {
		
		if( strpos($_POST['eeNotify' . $eeField], '@') ) {
			
			$eeAddresses = $eeSFL->eeSFL_SanitizeEmailString($_POST['eeNotify' . $eeField]);
			$eeSFL->eeListSettings['Notify' . $eeField] = $eeAddresses;
		
		} elseif(!$_POST['eeNotify' . $eeField]) {
			
			$eeSFL->eeListSettings['Notify' . $eeField] = '';
		
		}
	}
	
	// Message Options
	$eeTextInputs = array(
		'NotifyFrom'
		,'NotifyFromName'
		,'NotifySubject'
	);
	foreach( $eeTextInputs as $eeTerm ) {
		$eeSFL->eeListSettings[$eeTerm] = eeSFL_ProcessTextInput($eeTerm);
	}
	
	$eeSFL->eeListSettings['NotifyMessage'] = eeSFL_ProcessTextInput('NotifyMessage', 'textarea');
	
	if(!$eeSFL->eeListSettings['NotifyMessage']) {
		$eeSFL->eeListSettings['NotifyMessage'] = $eeSFL->eeNotifyMessageDefault;
	}
	
	// Update DB
	update_option('eeSFL_Settings_' . $eeSFL->eeListID, $eeSFL->eeListSettings );
	
	$eeSFL->eeLog['messages'][] = __('Notification Settings Saved', 'ee-simple-file-list');
}



// Settings Display =========================================
	
// User Messaging
$eeOutput .= $eeSFL_Messaging->eeSFL_ResultsNotification();

// Begin the Form	
$eeOutput .= '

<form action="' . $eeSFL->eeSFL_GetThisURL() . '" method="post" id="eeSFL_Settings">		
<input type="hidden" name="eePost" value="TRUE" />
<input type="hidden" name="eeListID" value="' . $eeSFL->eeListID . '" />';	
$eeOutput .= wp_nonce_field('eeSFL_Nonce', eeSFL_Nonce, TRUE, FALSE);

$eeOutput .= '

<div class="eeColInline eeSettingsTile">
				
	<div class="eeColHalfLeft">
	
		<h1>' . __('Notifications Settings', 'ee-simple-file-list') . '</h1>
		<a class="" href="https://simplefilelist.com/notification-settings/" target="_blank">' . __('Instructions', 'ee-simple-file-list') . '</a>
	
	</div>
	
	<div class="eeColHalfRight">
	
		<input class="button" type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" />
	
	</div>

</div>


<div class="eeSettingsTile">
	
	<h2>' . __('Notifications', 'ee-simple-file-list') . '</h2>';
	
	if(empty($eeSFL->eeListSettings['NotifyTo'])) {
		$eeSFL->eeListSettings['NotifyTo'] = get_option('admin_email');
	}
	if(empty($eeSFL->eeListSettings['NotifyFrom'])) {
		$eeSFL->eeListSettings['NotifyFrom'] = get_option('admin_email');
	}
	
	$eeOutput .= '
	
	<fieldset>
	<legend>' . __('Enable Notifications', 'ee-simple-file-list') . '</legend>
	<div><label>' . __('Enable', 'ee-simple-file-list') . '<input type="checkbox" name="eeNotify" value="YES" id="eeNotify"'; 
	if($eeSFL->eeListSettings['Notify'] == 'YES') { $eeOutput .= ' checked'; }
	$eeOutput .= ' /></label></div>
	
	<div class="eeNote">' . __('Send an email notification when a file is uploaded on the front-side of the website.', 'ee-simple-file-list') . '</div>
	
	</fieldset>
	
</div>




<div class="eeColumns">

	<!-- Left Column -->	
	
	<div class="eeColLeft">
	
		<div class="eeSettingsTile">
		
		<h2>' . __('Notice Recipients', 'ee-simple-file-list') . '</h2>
		
		<fieldset>';
		
		if($eeSFLA) {
			
			require_once(WP_PLUGIN_DIR . '/ee-simple-file-list-access/includes/eeSFLA_NoticeSettingsDisplay.php');
		
		} else {
			
			$eeOutput .= '
		
			<div><label class="eeBlock">' . __('Notice Email', 'ee-simple-file-list') . '
			<input type="text" name="eeNotifyTo" value="' . esc_textarea($eeSFL->eeListSettings['NotifyTo']) . '" id="eeNotifyTo" /></label></div>
			<div class="eeNote">' . __('Send an email here whenever a file is uploaded.', 'ee-simple-file-list') . '</div>';
		}
		
		
		// For All List Types
		$eeOutput .= '
		
		<div><label class="eeBlock">' . __('Copy to Email', 'ee-simple-file-list') . '<br />
		<input type="text" name="eeNotifyCc" value="' . esc_textarea($eeSFL->eeListSettings['NotifyCc']) . '" id="eeNotifyCc" /></label></div>
		<div class="eeNote">' . __('Copy all notice emails here.', 'ee-simple-file-list') . '</div>
		
		<div><label class="eeBlock">' . __('Blind Copy to Email', 'ee-simple-file-list') . '<br />
		<input class="eeFullWidth" type="text" name="eeNotifyBcc" value="' . esc_textarea($eeSFL->eeListSettings['NotifyBcc']) . '" id="eeNotifyBcc" /></label></div>
		<div class="eeNote">' . __('Blind copy all notice emails here.', 'ee-simple-file-list') . '</div>
		<div class="eeNote">* ' . __('Separate multiple addresses with a comma.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		</div>
		
	</div>
	
	
	<!-- Right Column -->
	
	<div class="eeColRight">
	
		<div class="eeSettingsTile">
		
		<h2>' . __('Message Details', 'ee-simple-file-list') . '</h2>
		
		<fieldset>	
		
		<div><label class="eeBlock">' . __('Your Name', 'ee-simple-file-list') . '<br />
		<input class="eeFullWidth" type="text" name="eeNotifyFromName" value="' . esc_textarea(stripslashes($eeSFL->eeListSettings['NotifyFromName'])) . '" id="eeNotifyFromName" /></label></div>
		<div class="eeNote">' . __('The visible name in the From field.', 'ee-simple-file-list') . '</div>	
		
		<div><label class="eeBlock">' . __('Reply Address', 'ee-simple-file-list') . '<br />
		<input class="eeFullWidth" type="email" name="eeNotifyFrom" value="' . esc_textarea($eeSFL->eeListSettings['NotifyFrom']) . '" id="eeNotifyFrom" /></label></div>
		<div class="eeNote">' . __('The notification message\'s reply-to address.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		</div>
		
	</div>

</div>
	
	
	
	
<div class="eeSettingsTile">
		
<fieldset>
		
<h2>' . __('Message Details', 'ee-simple-file-list') . '</h2>';

$eeOutput .= '

<div><label class="eeBlock">' . __('Message Subject', 'ee-simple-file-list') . '<br />
<input class="eeFullWidth" type="text" name="eeNotifySubject" value="' . esc_textarea(stripslashes($eeSFL->eeListSettings['NotifySubject'])) . '" id="eeNotifySubject" /></label></div>
		
<div class="eeNote">' . __('The notification message subject line.', 'ee-simple-file-list') . '</div>

<div><label class="eeBlock">' . __('Message Body', 'ee-simple-file-list') . '<br />
<textarea class="eeFullWidth" name="eeNotifyMessage" id="eeNotifyMessage" cols="64" rows="12" >' . esc_textarea(stripslashes($eeSFL->eeListSettings['NotifyMessage'])) . '</textarea></label></div>
	
<div class="eeNote">' . __('This is the text for all file upload notification messages.', 'ee-simple-file-list') . ' ' . __('To insert links to the files, use this shortcode:', 'ee-simple-file-list') . ' [file-list]' . ' '  . __('To insert a link pointing to the file list page, use this shortcode:', 'ee-simple-file-list') . ' [web-page]</div>

</fieldset>

</div>



<div class="eeColInline eeSettingsTile">
				
	<input class="button" type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" />
			
</div>
	

</form>

';
	
?>