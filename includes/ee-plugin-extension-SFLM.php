<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL' )) exit('ERROR 98 - SFLM Info'); // Exit if nonce fails

$eeOutput .= '<div class="eeSettingsTile">
		
<h2>' . __('Add Media Player', 'ee-simple-file-list') . '</h2>
	
<!-- <img src="" width="400" height="500" class="eeFloatRight" alt="Screen Shot" /> -->

<p>' . __('This free extension adds audio and video media players to your file list.', 'ee-simple-file-list') . ' ' . __('Show playback inline or within a pop-up box.', 'ee-simple-file-list') . '</p>

<ul>
	<li>' . __('Add audio and video players to Simple File List.', 'ee-simple-file-list') . '</li>
	<li>' . __('Display an inline audio player below the file name to allow your users to play audio files right on the list.', 'ee-simple-file-list') . '</li>
	<li>' . __('Video files open in an overlay, rather than directly within a new tab.', 'ee-simple-file-list') . '</li>
	<li>' . __('The audio player can be enabled/disabled in the settings.', 'ee-simple-file-list') . '</li>
	<li>' . __('The height of the audio player can be defined in the settings.', 'ee-simple-file-list') . '</li>
	<li>' . __('The file type MIME is auto-detected and passed to the browser, letting it play whatever it can.', 'ee-simple-file-list') . '</li>
</ul>

<br class="eeClear" />

<p class="eeCentered"><a class="button" target="_blank" href="">' . __('Get Media Player', 'ee-simple-file-list') . '</a>  
<a class="button" target="_blank" href="https://simplefilelist.com/add-media-player/">' . __('More Information', 'ee-simple-file-list-pro') . '</a></p>

</div>';

?>