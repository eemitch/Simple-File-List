<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails
	
$eeOutput .= '<div id="eeAdminFooter">
	
	<fieldset><p id="eeFooterImportant" class="eeHide">' . __('IMPORTANT: Allowing the public to upload files to your web server comes with risk.', 'ee-simple-file-list-pro') . ' ' .  
	__('Please go to Upload Settings and ensure that you only use the file types that you absolutely need.', 'ee-simple-file-list-pro') . ' ' .  
	__('Open each file submitted carefully.', 'ee-simple-file-list-pro') . '</p>';
		
	$eeOutput .= '<p class="eeRight">
	<a class="button" href="https://simplefilelist.com/docs/" target="_blank">' . __('Plugin Documentation', 'ee-simple-file-list-pro') . '</a>
		<a target="_blank" class="button" href="https://wordpress.org/support/plugin/simple-file-list/reviews/">' . __('Review Plugin', 'ee-simple-file-list-pro') . '</a></p>
		
		<strong>' . $eeSFL->eePluginName . '</strong> | <a href="' . $eeSFL->eePluginWebPage . '" target="_blank">' . __('Plugin Website', 'ee-simple-file-list-pro') . '</a> | 
					<a href="https://simplefilelist.com/give-feedback/" target="_blank">' . __('Give Feedback', 'ee-simple-file-list-pro') . '</a> | 
						<a href="#" id="eeFooterImportantLink">' . __('Caution', 'ee-simple-file-list-pro') . '</a>';
	
	$eeOutput .= '<br />
	
	' . __('Plugin Version', 'ee-simple-file-list-pro') . ': ' . eeSFL_Version . ' | DB: ' . eeSFL_DB_Version . ' | CB: ' . eeSFL_Cache_Version;
	
	if( @defined('eeSFLF_Version') ) { $eeOutput .= '<br />
		
		' . __('Folder Extension', 'ee-simple-file-list-pro') . ': ' . eeSFLF_Version;
	}
	
	if( @defined('eeSFLS_Version') ) { $eeOutput .= '<br />
		
		' . __('Search Extension', 'ee-simple-file-list-pro') . ': ' . eeSFLS_Version;
	}
	
	if( @defined('eeSFLA_Version') ) { $eeOutput .= '<br />
		
		' . __('Access Extension', 'ee-simple-file-list-pro') . ': ' . eeSFLA_Version;
	}
	
	$eeOutput .= '
		
	</fieldset>
</div>';

	
?>