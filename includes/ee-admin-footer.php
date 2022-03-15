<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' )) exit('ERROR 98'); // Exit if nonce fails

$eeLocale = get_locale();

if(isset($_POST['eeLangOptionSubmit'])) {
	
	if($_POST['eeLangOption'] == 'en_US') {
		update_option('eeSFL_Lang', 'en_US');
	} else {
		update_option('eeSFL_Lang', $eeLocale);
	}
}

$eeLocaleSetting = get_option('eeSFL_Lang');
	
$eeOutput .= '

<footer class="eeClearFix">';

	if($eeLocale != 'en_US') {
	
		$eeOutput .= '
		
		<form id="eeSFL_LangOption" action="' . $eeURL . '" method="post">	
		
			<p>' . __('Use English on the Back-End', 'ee-simple-file-list') . '</p>
				
			
			<input type="radio" id="eeLangOptionNative" name="eeLangOption" value="' . $eeLocale . '"';
			
			if(!$eeLocaleSetting OR $eeLocaleSetting != 'en_US') { $eeOutput .= ' checked="checked"'; }
			
			$eeOutput .= '> <label for="eeLangOptionNative">' . $eeLocale . '</label> | <input type="radio" id="eeLangOptionEnglish" name="eeLangOption" value="en_US"';
			
			if($eeLocaleSetting == 'en_US') { $eeOutput .= ' checked="checked"'; }
			
			$eeOutput .= '> <label for="eeLangOptionEnglish">en_US</label> 
			
			<input class="button" type="submit" name="eeLangOptionSubmit" value="' . __('Save', 'ee-simple-file-list') . '" />
			
		</form>';
	
	}
	
	$eeOutput .= '<p id="eeFooterImportant" class="eeHide">' . __('IMPORTANT: Allowing the public to upload files to your web server comes with risk.', 'ee-simple-file-list') . ' ' .  
	__('Please go to Upload Settings and ensure that you only use the file types that you absolutely need.', 'ee-simple-file-list') . ' ' .  
	__('Open each file submitted carefully.', 'ee-simple-file-list') . '</p>
		
	<a href="https://simplefilelist.com/documentation/" target="_blank">' . __('Plugin Documentation', 'ee-simple-file-list') . '</a>
	<a href="https://simplefilelist.com/?pr=free" target="_blank">' . __('Plugin Website', 'ee-simple-file-list') . '</a>
	<a href="https://simplefilelist.com/give-feedback/?pr=free" target="_blank">' . __('Give Feedback', 'ee-simple-file-list') . '</a>
	<a class="eeCaution" href="#" id="eeFooterImportantLink">' . __('Caution', 'ee-simple-file-list') . '</a>
	
	<br class="eeClear" />
	
	<p class="ee-plugin-version"><a href="https://wordpress.org/plugins/simple-file-list/">Simple File List ' . __('Version', 'ee-simple-file-list') . ' ' . eeSFL_BASE_Version . '</a>' .  
	' &rarr; <a href="https://get.simplefilelist.com/" target="_blank">Upgrade</a>';
	
	$eeOutput .= '</p>
	
</footer>
</main><!-- END .eeSFL_Admin -->
</div><!-- END .wrap -->

<!-- END SFL ADMIN -->


';

	
?>