<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL' )) exit('ERROR 98 - SFLM Info'); // Exit if nonce fails

$eeOutput .= '<div class="eeSettingsTile">
		
<h2>' . __('Upgrade to PRO', 'ee-simple-file-list') . '</h2>
	
<img src="' . $eeSFL_BASE->eeEnvironment['pluginURL'] . 'images/SFL-Pro-Admin-List.jpg" width="400" height="510" class="eeFloatRight" alt="Screen Shot" />

<p>' . __('Upgrade to Simple File List Pro and enjoy more features and functionality.', 'ee-simple-file-list') . ' ' . __('Pro is also extendable in more ways.', 'ee-simple-file-list') . '</p>

<ul>
	<li>' . __('Create folders and unlimited sub-folders.', 'ee-simple-file-list') . '</li>
</ul>

<br class="eeClear" />

<p class="eeCentered"><a class="button" target="_blank" href="https://get.simplefilelist.com/index.php?eeDomain=' . $eeSFL_BASE->eeEnvironment['wpSiteURL'] . '&eeProduct=ee-simple-file-list-pro&ee=1">' . __('Upgrade to Pro', 'ee-simple-file-list') . '</a>  
<a class="button" target="_blank" href="https://simplefilelist.com/?ee=1">' . __('More Information', 'ee-simple-file-list') . '</a></p>

</div>';

?>