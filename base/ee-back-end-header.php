<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

if(defined('eeSFL_Base')) {
	
	$eeUpSell = '<div id="eeGoProBanner" class="eeClearFix">
	<a href="https://get.simplefilelist.com/index.php?eeProduct=ee-simple-file-list-pro" class="button" target="_blank">' . __('Upgrade Now', 'ee-simple-file-list') . ' &rarr;</a>
	<small><a href="https://demo.simple-file-list.com/?pr=free" class="eeFloatRight" target="_blank">Try the Demo</a></small>
	<p><strong>' . __('Upgrade to Pro', 'ee-simple-file-list') . '</strong> - ' . 
	__('Add sub-folder support and extensions to include even more capabilities.', 'ee-simple-file-list') . ' ' . __('The cost is just once per domain. No recurring fees.', 'ee-simple-file-list') . '</p>
		
	</div>';
}


$eeOutput = '
<!-- BEGIN SFL ADMIN -->

<div class="wrap eeSFL">
<main class="eeSFL_Admin">
	
	<header class="eeClearFix">';
		
		$eeOutput .= '
		
		<div id="eeSFL_HeaderMeta">
			<a href="https://get.simplefilelist.com/index.php" target="_blank">
				<img src="' . $eeSFL->eeEnvironment['pluginURL'] . '/images/icon-128x128.png" alt="Simple File List ' . __('Logo', 'ee-simple-file-list') . '" title="Simple File List" /></a>
			<div>
				<p class="heading">' . eeSFL_PluginName . '</p>
				<p class="eeTagLine">' . __('Easy File Sharing for WordPress', 'ee-simple-file-list') . '</p>
				<p class="eeHeaderLinks">';
				
				if(defined('eeSFL_Pro')) { 
					$eeOutput .= '<a href="https://account.simplefilelist.com/" target="_blank">' . __('My Account', 'ee-simple-file-list') . '</a>';
				} else {
					$eeOutput .= '<a href="https://get.simplefilelist.com/" target="_blank">' . __('Upgrade to the Pro Version', 'ee-simple-file-list') . '</a>';
				}
				
				$eeOutput .= '
					<a href="https://simplefilelist.com/documentation/" target="_blank">' . __('Documentation', 'ee-simple-file-list') . '</a> 
					<a href="https://simplefilelist.com/get-support/" target="_blank">' . __('Get Support', 'ee-simple-file-list') . '</a>
				</p>
			</div>';
			
			if(eeSFL_DevMode) {
				$eeOutput .= '<p class="eeAlert" id="eeSFL_DevMode">' . __('DEVELOPMENT MODE ON', 'ee-simple-file-list') . '</p>';
			}
			
		$eeOutput .= '</div>
				
		<div id="eeSFL_HeaderTools">';
	
		if($eeSFLA) { require_once(WP_PLUGIN_DIR . '/' . eeSFLA_PluginSlug . '/includes/eeSFLA_ListNavigation.php'); }
		
		if(!isset($_GET['tab'])) { $eeShowShortcodeBlock = TRUE; } else { $eeShowShortcodeBlock = FALSE; }
		if(isset($_GET['tab'])) { if($_GET['tab'] == 'list') { $eeShowShortcodeBlock = TRUE; } } 
		
		if($eeShowShortcodeBlock) { // Only show this on the list tab
		
			$eeOutput .= '
			
			<div class="eeShortCodeOps">
			
			<div class="eeFlex">
			 
				<input class="eeFlex3" type="text" name="eeSFL_ShortCode" value="[eeSFL';
			 
				if($eeSFLA) { $eeOutput .= ' list=\'' . $eeSFL->eeListID . '\''; }
				if(isset($_GET['eeFolder'])) { 
					$eeShortcodeFolder = esc_js(sanitize_text_field($_GET['eeFolder']));
					$eeOutput .= ' showfolder=\'' . $eeShortcodeFolder . '\'';
				}
				 
				$eeOutput .= ']" id="eeSFL_ShortCode"><button id="eeCopytoClipboard" class="button eeFlex1">' . __('Copy', 'ee-simple-file-list') . '</button>
			
			</div>
			
			<p><small>' . __('Place this shortcode on a page, post or widget.', 'ee-simple-file-list') . '</small></p>
			
			</div>';
		}
		
	$eeOutput .= '
	
	</div>
	
	</header>

';

if(defined('eeSFL_Pro')) { require_once(eeSFL_PluginDir . 'pro/ee-plugin-reg-checks.php'); }

$eeOutput .= $eeSFL_Messaging->eeSFL_ResultsNotification();
	
?>