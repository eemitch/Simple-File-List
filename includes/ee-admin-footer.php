<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails
	
$eeOutput .= '<div id="eeAdminFooter">
	
	<fieldset><p id="eeFooterImportant" class="eeHide">' . __('IMPORTANT: Allowing the public to upload files to your web server comes with risk.', 'ee-simple-file-list') . ' ' .  
	__('Please go to Upload Settings and ensure that you only use the file types that you absolutely need.', 'ee-simple-file-list') . ' ' .  
	__('Open each file submitted carefully.', 'ee-simple-file-list') . '</p>';
		
	$eeOutput .= '<p class="eeRight">
	<a class="button" href="https://simplefilelist.com/documentation/" target="_blank">' . __('Plugin Documentation', 'ee-simple-file-list') . '</a></p>
			<a href="https://simplefilelist.com/?pr=free" target="_blank">' . __('Plugin Website', 'ee-simple-file-list') . '</a> | 
					<a href="https://simplefilelist.com/give-feedback/?pr=free" target="_blank">' . __('Give Feedback', 'ee-simple-file-list') . '</a> | 
						<strong><a href="#" id="eeFooterImportantLink">' . __('Caution', 'ee-simple-file-list') . '</a></strong>';
	
	$eeOutput .= '<br />
	
	' . __('Plugin Version', 'ee-simple-file-list') . ': ' . eeSFL_FREE_Version . ' | DB: ' . eeSFL_FREE_DB_Version;
	
	$eeOutput .= '
		
	</fieldset>
</div>';

	
?>