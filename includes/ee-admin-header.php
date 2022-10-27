<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails
	
// Please Buy the Pro Version
$eeUpSell = '<div id="eeGoProBanner" class="eeClearFix">
	
<a href="https://get.simplefilelist.com/index.php?eeExtension=ee-simple-file-list-pro&pr=free" class="button" target="_blank">' . __('Upgrade Now', 'ee-simple-file-list') . ' &rarr;</a>

<small><a href="https://demo.simple-file-list.com/?pr=free" class="eeFloatRight" target="_blank">Try the Demo</a></small>
	
<p><strong>' . __('Upgrade to Pro', 'ee-simple-file-list') . '</strong> - ' . 
__('Add sub-folder support, bulk file editing, directory location customization and add extensions for searching and more flexible user and role file restrictions.', 'ee-simple-file-list') . ' ' . __('The low cost is just once per domain. No recurring fees.', 'ee-simple-file-list') . '</p>
	
</div>';

// Begin Output
$eeOutput = '
<!-- BEGIN SFL ADMIN -->

<div class="wrap">
<main class="eeSFL_Admin">
	
	<header class="eeClearFix">
	
		<div class="eeShortCodeOps">
			
			 <p>' . __('Place this shortcode on a page, post or widget.', 'ee-simple-file-list') . '<br />
			 
			 <label>Shortcode: <input type="text" name="eeSFL_ShortCode" value="[eeSFL]" id="eeSFL_ShortCode" size="8"></label> <button id="eeCopytoClipboard" class="button">' . __('Copy', 'ee-simple-file-list') . '</button></p>
			
		</div>
		
		<div>
		
			<a href="https://get.simplefilelist.com/index.php" target="_blank" />
			<img src="' . $eeSFL_BASE->eeEnvironment['pluginURL'] . '/images/icon-128x128.png" alt="Simple File List ' . __('Logo', 'ee-simple-file-list') . '" title="Simple File List" /></a>
			
		</div>
		
		<div>
			<p class="heading">Simple File List</p>
			<p class="eeTagLine">' . __('Easy File Sharing for WordPress', 'ee-simple-file-list') . '</p>
			<p class="eeHeaderLinks"> 
			<a href="https://simplefilelist.com/" target="_blank">Website</a>
			<a href="https://simplefilelist.com/documentation/" target="_blank">' . __('Documentation', 'ee-simple-file-list') . '</a> 
			<a href="https://simplefilelist.com/get-support/" target="_blank">' . __('Get Support', 'ee-simple-file-list') . '</a> 
			<a href="https://get.simplefilelist.com/index.php" target="_blank">' . __('Upgrade to Pro', 'ee-simple-file-list') . '</a></p>
		</div>
	
	</header>

';

// if($eeSFL_BASE_DevMode) { $eeOutput .= '<p class="eeAlert">' . __('DEVELOPMENT MODE ON', 'ee-simple-file-list') . '</p>'; }
	
?>