<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');
	
$eeOutput .= '

<footer class="eeClearFix">';
	
	// Usage Reports OptIn
	if(isset($_POST['eeOptInSubmit']) AND check_admin_referer('eeSFL_NonceReporting', eeSFL_Nonce)) {
		if($_POST['eeSFL_ReportsOptIn'] == 'YES') { 
			update_option('eeSFL_ReportsOptIn', 'YES');
			$eeOptIn = 'YES'; 
		} else {
			delete_option('eeSFL_ReportsOptIn');
			delete_transient('eeSFL_ReportsOptInSent');
			$eeOptIn = FALSE;
		}
	} else {
		$eeOptIn = get_option('eeSFL_ReportsOptIn');
	}
	
	$eeOutput .= '<div class="eeSFL_FooterFormsContainer">
	
	<form class="eeSFL_FooterForm" id="eeSFL_FooterTools" action="' . $eeSFL->eeSFL_GetThisURL() . '" method="post">
		<fieldset>	
		<h3>' . eeSFL_PluginName . '</h3>
			<p>';
				$eeOutput .= wp_nonce_field('eeSFL_FooterTools', eeSFL_Nonce);
				$eeOutput .= __('Version', 'ee-simple-file-list') . ': ' . eeSFL_ThisPluginVersion;
				if($eeSFLS) { $eeOutput .= ' | Search: ' . eeSFLS_Version; }
				if($eeSFLA) { $eeOutput .= ' | Access: ' . eeSFLA_Version; }
				if($eeSFLE) { $eeOutput .= ' | Email: ' . eeSFLE_Version; }
				$eeOutput .= '
				<br />
				' . __('Run Time', 'ee-simple-file-list') . ': ' . $eeSFL->eeSFL_NOW() . __('Total', 'ee-simple-file-list');
				$eeOutput .= '
			</p>
		</fieldset>
	</form>
	
	<!-- SFL Reporting Opt-In -->
	<form class="eeSFL_FooterForm" id="eeSFL_ReportsOptIn" action="' . $eeSFL->eeSFL_GetThisURL() . '" method="post">
		<fieldset>';
			$eeOutput .= wp_nonce_field('eeSFL_NonceReporting', eeSFL_Nonce);
			$eeOutput .= '
			<h4>' . __('Please Help', 'ee-simple-file-list') . '</h4>
			<p>' . __('Please help improve Simple File List by allowing general usage reports to be sent.', 'ee-simple-file-list') . '
			</p>
			<p><input type="radio" id="eeSFL_ReportsOptInYES" name="eeSFL_ReportsOptIn" value="YES"';
			if($eeOptIn == 'YES') { $eeOutput .= ' checked="checked"'; }
			$eeOutput .= '> <label for="eeSFL_ReportsOptInYES">' . __('YES', 'ee-simple-file-list') . '</label>   
			<input type="radio" id="eeSFL_ReportsOptInNO" name="eeSFL_ReportsOptIn" value="NO"';
			if(!$eeOptIn) { $eeOutput .= ' checked="checked"'; }
			$eeOutput .= '> <label for="eeSFL_ReportsOptInNO">' . __('NO', 'ee-simple-file-list') . '</label></p>
			<p><input class="button" type="submit" name="eeOptInSubmit" value="' . __('Confirm', 'ee-simple-file-list') . '" />
			</p>
			<p><a href="">' . __('More Information', 'ee-simple-file-list') . '</a></p>
		</fieldset>
	</form>';	
		
	if( get_locale() != 'en_US' ) {
		$eeOutput .= '<!-- SFL Language Selector -->
		<form class="eeSFL_FooterForm" id="eeSFL_LangOption" action="' . $eeSFL->eeSFL_GetThisURL() . '" method="post">	
			<fieldset>';
				$eeLocale = get_locale();
				$eeLocaleSetting = get_option('eeSFL_Lang');
				$eeOutput .= wp_nonce_field('eeSFL_NonceLang', eeSFL_Nonce);
				$eeOutput .= '
				<p>' . __('Use English on the Back-End', 'ee-simple-file-list') . '</p>	
				<input type="radio" id="eeLangOptionNative" name="eeLangOption" value="' . $eeLocale . '"';
				if(!$eeLocaleSetting OR $eeLocaleSetting != 'en_US') { $eeOutput .= ' checked="checked"'; }
				$eeOutput .= '> <label for="eeLangOptionNative">' . $eeLocale . '</label> | <input type="radio" id="eeLangOptionEnglish" name="eeLangOption" value="en_US"';
				if($eeLocaleSetting == 'en_US') { $eeOutput .= ' checked="checked"'; }
				$eeOutput .= '> <label for="eeLangOptionEnglish">en_US</label> 
				<input class="button" type="submit" name="eeLangOptionSubmit" value="' . __('Save', 'ee-simple-file-list') . '" />
			</fieldset>
		</form>';
	}	
		
	$eeOutput .= '</div>
	
	<div id="eeSFL_FooterActions">
		<p id="eeFooterImportant" class="eeHide">' . __('IMPORTANT: Allowing the public to upload files to your web server comes with risk.', 'ee-simple-file-list') . ' ' .  
		__('Please go to Upload Settings and ensure that you only use the file types that you absolutely need.', 'ee-simple-file-list') . ' ' .  
		__('Open each file submitted carefully.', 'ee-simple-file-list') . '</p>
			
		<a href="https://simplefilelist.com/documentation/" target="_blank">' . __('Plugin Documentation', 'ee-simple-file-list') . '</a>
		<a href="https://simplefilelist.com/" target="_blank">' . __('Plugin Website', 'ee-simple-file-list') . '</a>
		<a href="https://simplefilelist.com/give-feedback/" target="_blank">' . __('Give Feedback', 'ee-simple-file-list') . '</a>
		<a class="eeCaution" href="#" id="eeFooterImportantLink">' . __('Caution', 'ee-simple-file-list') . '</a>
	</div>';

$eeOutput .= '</footer>';

require_once(eeSFL_PluginDir . 'base/ee-alert-modal.php');

$eeOutput .= '</main><!-- END .eeSFL_Admin -->
</div><!-- END .wrap -->

<!-- END SFL ADMIN -->

';

$_POST = array();

?>