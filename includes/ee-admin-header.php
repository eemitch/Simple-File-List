<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails
	
// Please Buy the Pro Version
$eeUpSell = '<div id="eeGoProBanner">
	
<a href="https://get.simplefilelist.com/index.php?eeExtension=ee-simple-file-list-pro&pr=free" class="button" target="_blank">' . __('Upgrade Now', 'ee-simple-file-list') . ' &rarr;</a>

<small><a href="https://demo.simple-file-list.com/?pr=free" class="eeRight" target="_blank">Try the Demo</a></small>
	
<p><strong>' . __('Upgrade to Pro', 'ee-simple-file-list') . '</strong> - ' . __('Add sub-folder support, bulk file editing, send files by email, directory location customization and more.', 'ee-simple-file-list') . ' ' . __('Plus, add extensions for larger file lists and flexible user management.', 'ee-simple-file-list') . ' ' . __('The low cost is just once per domain.', 'ee-simple-file-list') . '</p>
	
</div>';

// Begin Output
$eeOutput = '
<!-- BEGIN SFL ADMIN -->

<div class="wrap">
<main class="eeSFL_Admin">';

if($eeSFL_BASE_DevMode) { $eeOutput .= '<p class="eeAlert">' . __('DEVELOPMENT MODE ON', 'ee-simple-file-list') . '</p>'; }
	
?>