<?php // Simple File List Script: ee-email-settings.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = 'Loading Email Settings Page ...';

// Check for POST and Nonce
if(@$_POST['eePost'] AND check_admin_referer( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce')) {
	
	// YES/NO Checkboxes
	$eeCheckboxes = array(
		'Notify'
	);
	foreach( $eeCheckboxes as $eeTerm){
		$eeSFL_BASE->eeListSettings[$eeTerm] = eeSFL_BASE_ProcessCheckboxInput($eeTerm);
	}
	
	$eeDelivery = array('To', 'Cc', 'Bcc');
	
	foreach( $eeDelivery as $eeField ) {
		
		if( strpos($_POST['eeNotify' . $eeField], '@') ) {
			
			$eeAddresses = $eeSFL_BASE->eeSFL_SanitizeEmailString($_POST['eeNotify' . $eeField]);
			$eeSFL_BASE->eeListSettings['Notify' . $eeField] = $eeAddresses;
		
		} elseif(!$_POST['eeNotify' . $eeField]) {
			
			$eeSFL_BASE->eeListSettings['Notify' . $eeField] = '';
		
		}
	}
	
	// Message Options
	$eeTextInputs = array(
		'NotifyFrom'
		,'NotifyFromName'
		,'NotifySubject'
	);
	foreach( $eeTextInputs as $eeTerm){
		$eeSFL_BASE->eeListSettings[$eeTerm] = eeSFL_BASE_ProcessTextInput($eeTerm);
	}
	
	$eeSFL_BASE->eeListSettings['NotifyMessage'] = eeSFL_BASE_ProcessTextInput('NotifyMessage', 'textarea');
	
	// Update DB
	update_option('eeSFL_Settings_1', $eeSFL_BASE->eeListSettings );
	
	$eeConfirm = __('Notification Settings Saved', 'ee-simple-file-list-pro');
}



// Settings Display =========================================
	
if( count($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors']) ) { 
	$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeSFL_BASE->eeLog[eeSFL_BASE_Go]['errors'], 'notice-error');
} elseif($eeConfirm) { 
	$eeOutput .=  eeSFL_BASE_ResultsDisplay($eeConfirm, 'notice-success');
}

// Begin the Form	
$eeOutput .= '

<form action="' . admin_url() . '?page=' . eeSFL_BASE_PluginSlug . '&tab=settings&subtab=email_settings" method="post" id="eeSFL_Settings">
<input type="hidden" name="eePost" value="TRUE" />';	
$eeOutput .= wp_nonce_field( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce', TRUE, FALSE);

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
	
	if(strlen($eeSFL_BASE->eeListSettings['NotifyTo']) < 5) {
		$eeSFL_BASE->eeListSettings['NotifyTo'] = get_option('admin_email');
	}
	if(strlen($eeSFL_BASE->eeListSettings['NotifyFrom']) < 5) {
		$eeSFL_BASE->eeListSettings['NotifyFrom'] = get_option('admin_email');
	}
	
	$eeOutput .= '
	
	<fieldset>
	<legend>' . __('Enable Notifications', 'ee-simple-file-list') . '</legend>
	<div><label>' . __('Enable', 'ee-simple-file-list') . '<input type="checkbox" name="eeNotify" value="YES" id="eeNotify"'; 
	if(@$eeSFL_BASE->eeListSettings['Notify'] == 'YES') { $eeOutput .= ' checked'; }
	$eeOutput .= ' /></label></div>
	
	<div class="eeNote">' . __('Send an email notification when a file is uploaded on the front-side of the website.', 'ee-simple-file-list') . '</div>
	
	</fieldset>
	
</div>
	
	

<div class="eeColumns">		
	
	<!-- Left Column -->
	
	<div class="eeColLeft">
	
		<div class="eeSettingsTile">
		
		<h2>' . __('Notice Recipients', 'ee-simple-file-list') . '</h2>
		
		<fieldset>
		
		<div><label class="eeBlock">' . __('Notice Email', 'ee-simple-file-list') . '
		<input type="text" name="eeNotifyTo" value="' . $eeSFL_BASE->eeListSettings['NotifyTo'] . '" id="eeNotifyTo" /></label></div>
		<div class="eeNote">' . __('Send an email here whenever a file is uploaded.', 'ee-simple-file-list') . '</div>
		
		
		<div><label class="eeBlock">' . __('Copy to Email', 'ee-simple-file-list') . '<br />
		<input type="text" name="eeNotifyCc" value="' . $eeSFL_BASE->eeListSettings['NotifyCc'] . '" id="eeNotifyCc" /></label></div>
		<div class="eeNote">' . __('Copy all notice emails here.', 'ee-simple-file-list') . '</div>
		
		<div><label class="eeBlock">' . __('Blind Copy to Email', 'ee-simple-file-list') . '<br />
		<input class="eeFullWidth" type="text" name="eeNotifyBcc" value="' . $eeSFL_BASE->eeListSettings['NotifyBcc'] . '" id="eeNotifyBcc" /></label></div>
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
		<input class="eeFullWidth" type="text" name="eeNotifyFromName" value="' . stripslashes($eeSFL_BASE->eeListSettings['NotifyFromName']) . '" id="eeNotifyFromName" /></label></div>
		<div class="eeNote">' . __('The visible name in the From field.', 'ee-simple-file-list') . '</div>	
		
		<div><label class="eeBlock">' . __('Reply Address', 'ee-simple-file-list') . '<br />
		<input class="eeFullWidth" type="email" name="eeNotifyFrom" value="' . $eeSFL_BASE->eeListSettings['NotifyFrom'] . '" id="eeNotifyFrom" /></label></div>
		<div class="eeNote">' . __('The notification message\'s reply-to address.', 'ee-simple-file-list') . '</div>
		
		</fieldset>
		
		</div>
		
	</div>
	
</div>



<div class="eeSettingsTile">
		
<fieldset>
		
<h2>' . __('Message Details', 'ee-simple-file-list') . '</h2>';

if(!@$eeSFL_BASE->eeListSettings['NotifyMessage']) { $eeSFL_BASE->eeListSettings['NotifyMessage'] = $eeSFL_BASE->eeNotifyMessageDefault; }
	
$eeOutput .= '

<div><label class="eeBlock">' . __('Message Subject', 'ee-simple-file-list') . '<br />
<input class="eeFullWidth" type="text" name="eeNotifySubject" value="' . stripslashes($eeSFL_BASE->eeListSettings['NotifySubject']) . '" id="eeNotifySubject" /></label></div>
		
<div class="eeNote">' . __('The notification message subject line.', 'ee-simple-file-list') . '</div>

<div><label class="eeBlock">' . __('Message Body', 'ee-simple-file-list') . '<br />
<textarea class="eeFullWidth" name="eeNotifyMessage" id="eeNotifyMessage" cols="64" rows="12" >' . stripslashes($eeSFL_BASE->eeListSettings['NotifyMessage']) . '</textarea></label></div>
	
<div class="eeNote">' . __('This is the text for all file upload notification messages.', 'ee-simple-file-list') . ' ' . __('To insert links to the files, use this shortcode:', 'ee-simple-file-list') . ' [file-list]' . ' '  . __('To insert a link pointing to the file list page, use this shortcode:', 'ee-simple-file-list') . ' [web-page]</div>

</fieldset>

</div>

<div class="eeColInline eeSettingsTile">
				
	<input class="button" type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" />
			
</div>

	
</form>

';
	
?>