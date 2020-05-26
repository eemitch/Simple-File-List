<?php // Simple File List Script: ee-get-extensions.php | Author: Mitchell Bennis | support@simplefilelist.com
	
defined( 'ABSPATH' ) or die( 'No direct access is allowed' );
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_Log[] = 'Loaded: ee-get-simple-file-list-folders';
$eeSFL_Button = '';

$eeSFL_ThisDomain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

$eeSFL_ThisEmail = get_option('admin_email');
if(!$eeSFL_ThisEmail) {
	$eeSFL_ThisEmail = 'mail@' . $eeSFL_ThisDomain;
}

if(filter_var($eeSFL_ThisDomain, FILTER_VALIDATE_URL)) {

	// Build the query URL
	$eeOrderURL = 'https://get.simplefilelist.com/index.php?eeDomain=' . urlencode( $eeSFL_ThisDomain ); // Add this domain name, with protocal
	$eeOrderURL .= '&eeEmail=' . urlencode($eeSFL_ThisEmail); // The notification email
}

// The Content
$eeOutput .= '<article class="eeSupp eeExtensions">

	<h2>' . __('Add Feature Extensions', 'ee-simple-file-list-pro') . '</h2>
	
	<p>' . __('Extensions add extended feature support to the free version of Simple File List. They are designed to improve the management of larger, more complex, file lists.', 'ee-simple-file-list-pro') . '</p>
	
	<hr />';
	
	
if(!$eeSFLS) {
		
$eeSFL_Button = '<a class="button eeGet" target="_blank" href="' . $eeOrderURL . '&eeExtension=ee-simple-file-list-pro-search">' . __('Add Search &amp; Pagination Now', 'ee-simple-file-list-pro') . '</a>';

$eeOutput .= '<h3>Search &amp; Pagination</h3>
	
	<img src="' . $eeSFL_Env['pluginURL'] . 'support/images/Search-Demo.jpg" width="400" height="301" class="eeRight" />
	
	<p>' . __('Adds searching and pagination functionality to Simple File List. It is designed to make very large file lists more manageable.', 'ee-simple-file-list-pro') . '</p>
	
	<ul>
		<li>' . __('Adds a search bar above the file list.', 'ee-simple-file-list-pro') . '</li>
		<li>' . __('Search by name and/or a date range (if the date column is shown).', 'ee-simple-file-list-pro') . '</li>
		<li>' . __('Searches within sub-folder. (But not above the current folder)', 'ee-simple-file-list-pro') . '</li>
		<li>' . __('Pagination breaks up large file lists into smaller pages.', 'ee-simple-file-list-pro') . '</li>
		<li>' . __('Define the number of files per page in the settings.', 'ee-simple-file-list-pro') . '</li>
		<li>' . __('Show or hide the search bar and/or pagination in the settings.', 'ee-simple-file-list-pro') . '</li>
		<li>' . __('Use a shortcode to place a search form anywhere on your website.', 'ee-simple-file-list-pro') . '</li>
		<li>' . __('Updating to newer versions is just like other Wordpress plugins.', 'ee-simple-file-list-pro') . '</li>
		<li><a target="_blank" href="https://simplefilelist.com/add-search-pagination/">' . __('See the Demo', 'ee-simple-file-list-pro') . '</a></li>
	</ul>
	
	<p class="eeCentered">' . $eeSFL_Button . '</p>
	
	<br class="clearing" />';
	
}

$eeOutput .= '</article>';
	
$eeSFL_Log[] = '$eeOrderURL ...';
$eeSFL_Log[] = urldecode($eeOrderURL);	

?>