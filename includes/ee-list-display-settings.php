<?php // Simple File List - ee-list-display-settings.php - mitchellbennis@gmail.com
	
	// tab=display_settings
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('That is Noncense! (' . basename(__FILE__) . ')' ); // Exit if nonce fails

$eeSFL_Log[] = 'Loading List Settings Page ...';

// Check for POST and Nonce
if(@$_POST['eePost'] AND check_admin_referer( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce')) {
	
	// Get all the settings
	$eeSettings = get_option('eeSFL-Settings');
	
	$eeID = $eeSFL_Config['ID'];

	// YES/NO Checkboxes
	$eeCheckboxes = array(
		'AllowFrontManage'
		,'ShowFileActions'
		,'ShowHeader'
		,'AllowFrontSend'
	);
	foreach( $eeCheckboxes as $eeTerm){
		$eeSettings[$eeID][$eeTerm] = eeSFL_ProcessCheckboxInput($eeTerm);
	}
	
	// Update DB
	update_option('eeSFL-Settings', $eeSettings );
	
	// Update the array with new values
	$eeSFL_Config = $eeSettings[$eeID];
	
	// Extension Processing
	if($eeSFLF) {
		if(!@$eeSFLF_ListFolder) { // If not already set up
			$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
			include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_ListSettingsProcess.php');
		}
	}
	
	if($eeSFLS) {
		$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
		include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-pagination-settings-process.php');
	}
	
	$eeSFL_Confirm = __('List Settings Saved', 'ee-simple-file-list');
}

// Settings Display =========================================
	
$eeOutput .= '<div class="eeSFL_Admin">';
	
if(@$eeSFL_Log['errors']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['errors'], 'error'); // TO DO - Make this a Function
} elseif(@$eeSFL_Confirm) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Confirm, 'updated');
}

// Begin the Form	
$eeOutput .= '

<form action="' . $_SERVER['PHP_SELF'] . '?page=' . $eeSFL->eePluginSlug . '&tab=settings&subtab=display_settings" method="post" id="eeSFL_Settings">
		
		<input type="hidden" name="eePost" value="TRUE" />';	
		
		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce' );
		
		
		$eeOutput .= '<fieldset>
		
		<h1>' . __('File List Options', 'ee-simple-file-list') . '</h1>
		
		<label for="eeShowListHeader">' . __('Show Header', 'ee-simple-file-list') . ':</label> <p>Show the file list\'s table header</p>
		<input type="checkbox" name="eeShowHeader" value="YES" id="eeShowListHeader"';
		
		if( $eeSFL_Config['ShowHeader'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' />
		
		<div class="eeNote">' . __('Show file list\'s table header or not.', 'ee-simple-file-list') . '</div>
		
		<br class="eeClearFix" />
		
		
		
		<label for="eeShowFileActions">' . __('Show File Actions', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeShowFileActions" value="YES" id="eeShowFileActions"';
		
		if( $eeSFL_Config['ShowFileActions'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /> <p>' . __('Open | Download', 'ee-simple-file-list') . '</p>
		
		<div class="eeNote">' . __('Show file action links below each file name on the front-side list', 'ee-simple-file-list') . '</div>
		
		<br class="eeClearFix" />
		
		
		
		<label for="eeAllowFrontSend">' . __('Allow File Sending', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeAllowFrontSend" value="YES" id="eeAllowFrontSend"';
		
		if( $eeSFL_Config['AllowFrontSend'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /> <p>' . __('Send via Email', 'ee-simple-file-list') . '</p>
		
		<div class="eeNote">' . __('Allow front-side users to email links to files.', 'ee-simple-file-list') . '</div>
		
		<br class="eeClearFix" />
		
		
		
		<h2>' . __('Front-Side File Management', 'ee-simple-file-list') . '</h2>
		
		<label for="eeAllowFrontManage">' . __('Allow', 'ee-simple-file-list') . ':</label>
		<input type="checkbox" name="eeAllowFrontManage" value="YES" id="eeAllowFrontManage"';
		
		if( $eeSFL_Config['AllowFrontManage'] == 'YES') { $eeOutput .= ' checked="checked"'; }
		
		$eeOutput .= ' /> <p>' . __('Use with Caution', 'ee-simple-file-list') . '</p>
						
		<div class="eeNote">' . __('Show file manager links on the front side of the site.', 'ee-simple-file-list') . '</div>
		
		<br class="eeClearFix" />';
		
		
		if($eeSFLF) {
			
			$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
			include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_ListSettings.php');
		}
		
		if($eeSFLS) {
			
			$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
			include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-pagination-settings.php');
		}
			
		$eeOutput .= '<input type="submit" name="submit" id="submit2" value="' . __('SAVE', 'ee-simple-file-list') . '" class="eeAlignRight" />
		
		</fieldset>
		
	</form>
	
</div>';
	
?>