<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98 - Extension Settings'); // Exit if nonce fails

$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Loaded: List Settings';

// Check for POST and Nonce
if(isset($_POST['eePost']) AND check_admin_referer( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce')) {
	
	// Media Player Settings Process
	if($eeSFLM) {
		$eeSFLM->eeSFLM_SettingsProcess('_BASE', eeSFL_BASE_Go, 1);
	}
	
	// Update DB
	// update_option('eeSFL_Settings_1', $eeSFL_BASE->eeListSettings );
	
	$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['messages'][] = __('Extension Settings Saved', 'ee-simple-file-list');
}

// Settings Display =========================================
	
$eeOutput .= '<div class="eeSFL_Admin">';
	
// User Messaging
$eeOutput .= $eeSFL_BASE->eeSFL_ResultsNotification();

// Begin the Page	
$eeOutput .= '<form action="' . $eeSFL_BASE->eeSFL_GetThisURL() . '" method="post" id="eeSFL_Settings">';

$eeOutput .= wp_nonce_field( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce', TRUE, FALSE);

$eeOutput .= '<input type="hidden" name="eePost" value="TRUE" />
<input type="hidden" name="eeListID" value="1" />

<div class="eeColInline eeSettingsTile">
				
	<div class="eeColHalfLeft">
	
		<h1>' . __('Extension Settings', 'ee-simple-file-list-pro') . '</h1>
		<a class="" href="https://simplefilelist.com/extensions/" target="_blank">' . __('Information', 'ee-simple-file-list') . '</a>
	
	</div>
	
	<div class="eeColHalfRight">
	
		<input class="button" type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" />
	
	</div>

</div>

<div class="eeColumns">		
		
<!-- Left Column -->
	
<div class="eeColLeft">';

// Media Player
if($eeSFLM) { $eeOutput .= $eeSFLM->eeSFLM_SettingsInputsDisplay(); } 
	else {
		$eeSFL_Nonce = wp_create_nonce('eeSFL');
		include_once($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-plugin-extension-SFLM.php');
}

$eeOutput .= '</div>
	
<!-- Right Column -->

<div class="eeColRight">';

// Go Pro
$eeSFL_Nonce = wp_create_nonce('eeSFL');
include_once($eeSFL_BASE->eeEnvironment['pluginDir'] . 'includes/ee-plugin-extension-SFL-PRO.php');

$eeOutput .= '

</div>
</div>

<div class="eeColInline eeSettingsTile">
				
	<input class="button" type="submit" name="submit" value="' . __('SAVE', 'ee-simple-file-list') . '" />
			
</div>
		
</form>';		
	
?>