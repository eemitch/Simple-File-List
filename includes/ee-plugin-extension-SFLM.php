<?php
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFLA' )) exit('ERROR 98 - SFLM Info'); // Exit if nonce fails

$eeOutput .= '<div class="eeSettingsTile">
		
<h2>' . __('Add Media Player', 'ee-simple-file-list') . '</h2>
	
<img src="' . $eeSFL_BASE->eeEnvironment['pluginURL'] . 'images/Search-Demo.jpg" width="400" height="301" class="eeFloatRight" alt="Screen Shot" />

<p>' . __('This FREE extension adds audio and video media players to your file list.', 'ee-simple-file-list') . ' ' . __('Show playback inline or within a pop-up box.', 'ee-simple-file-list') . '</p>

<ul>
	<li>' . __('Adds a search bar above the file list.', 'ee-simple-file-list-pro') . '</li>
	<li>' . __('Search by name and/or a date range.', 'ee-simple-file-list-pro') . '</li>
	<li>' . __('For front-side searching, it will only look in sub-folders of the defined folder.', 'ee-simple-file-list-pro') . '</li>
	<li>' . __('Supports wildcards for searching for file names, descriptions and file owner information.', 'ee-simple-file-list-pro') . '</li>
	<li>' . __('Pagination breaks up large file lists into smaller pages.', 'ee-simple-file-list-pro') . '</li>
	<li>' . __('Define the number of files per page in the settings.', 'ee-simple-file-list-pro') . '</li>
	<li>' . __('Show or hide the search bar and/or pagination in the settings.', 'ee-simple-file-list-pro') . '</li>
	<li>' . __('Use a shortcode to place a search form anywhere on your website.', 'ee-simple-file-list-pro') . '</li>
	<li>' . __('Use shortcode attributes to customize the functionality for different locations.', 'ee-simple-file-list-pro') . '</li>
</ul>

<p class="eeCentered"><a class="button" target="_blank" href="https://get.simplefilelist.com/index.php?eeDomain=' . $eeSFL->eeEnvironment['wpSiteURL'] . '&eeProduct=ee-simple-file-list-search&ee=1">' . __('Get This Extension', 'ee-simple-file-list-pro') . '</a>  
<a class="button" target="_blank" href="https://simplefilelist.com/add-search-pagination/?ee=1">' . __('More Information', 'ee-simple-file-list-pro') . '</a></p>

</div>';

?>