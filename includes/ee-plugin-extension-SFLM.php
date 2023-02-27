<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL' )) exit('ERROR 98 - SFLM Info'); // Exit if nonce fails

$eeOutput .= '<div class="eeSettingsTile">
		
<h2>' . __('Add Media Player', 'ee-simple-file-list') . '</h2>
	
<img src="' . $eeSFL_BASE->eeEnvironment['pluginURL'] . 'images/Media-Player.jpg" width="400" height="500" class="eeFloatRight" alt="Screen Shot" />

<p>' . __('This FREE extension adds audio and video media players to your file list.', 'ee-simple-file-list') . ' ' . __('Show playback inline or within a pop-up box.', 'ee-simple-file-list') . '</p>

<ul>
	<li>' . __('Adds an HTML5 audio player next to each MP3 file.', 'ee-simple-file-list') . '</li>
</ul>

<br class="eeClear" />

<p class="eeCentered"><a class="button" target="_blank" href="">' . __('Get Media Player', 'ee-simple-file-list') . '</a>  
<a class="button" target="_blank" href="https://simplefilelist.com/add-media-player/?ee=1">' . __('More Information', 'ee-simple-file-list-pro') . '</a></p>

</div>';

?>