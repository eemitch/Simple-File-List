<?php // Simple File List Script: ee-get-pro.php | Author: Mitchell Bennis | support@simplefilelist.com
	
defined( 'ABSPATH' ) or die( 'No direct access is allowed' );
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

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

$eeSFL_Button = '<a class="button eeGet" target="_blank" href="' . $eeOrderURL . '&eeExtension=ee-simple-file-list-pro">' . __('Upgrade to Pro Now', 'ee-simple-file-list') . '</a>';

// The Content
$eeOutput .= '<article class="eeSupp eeExtensions">

	<h2>' . __('Upgrade to Simple File List Pro', 'ee-simple-file-list') . '</h2>
	
	<img src="' . $eeSFL_Env['pluginURL'] . 'images/Folder-Demo.jpg" width="400" height="331" class="eeRight" />
	
	<p>' . __('The Pro version allows folder listing, navigation and management capabilities among other features not available in the free version.', 'ee-simple-file-list') . ' ' . __('The Pro version is also extendable, with extensions for search and pagination and file access management.', 'simple-file-list') . '</p>
	
	<ul>
		<li>' . __('Create folders and unlimited levels of sub-folders.', 'ee-simple-file-list') . '</li>
		<li>' . __('Use a shortcode attribute to display specific folders.', 'ee-simple-file-list') . '</li>
		<li>' . __('Display different folders in different places on your site.', 'ee-simple-file-list') . '<br />
			' . __('You can even show several different folders on the same page and within widgets.', 'ee-simple-file-list') . '<br />
			' . __('Front-side users cannot navigate above the folder you specify.', 'ee-simple-file-list') . '</li>
		<li>' . __('Breadcrumb navigation indicates where you are.', 'ee-simple-file-list') . '</li>
		<li>' . __('Easily move files or entire folders.', 'ee-simple-file-list') . '</li>
		<li>' . __('Easily rename any folder.', 'ee-simple-file-list') . '</li>
		<li>' . __('Easily delete any folder, along with all contents.', 'ee-simple-file-list') . '</li>
		<li>' . __('Choose to sort folders first or sort along with the files.', 'ee-simple-file-list') . '</li>
		<li>' . __('Optionally display folder sizes.', 'ee-simple-file-list') . '</li>
		<li>' . __('Optionally define a custom directory for your file list.', 'ee-simple-file-list') . '</li>
		<li>' . __('Updating to newer versions is just like the free plugin.', 'ee-simple-file-list') . '</li>
		<li><a target="_blank" href="https://simplefilelist.com/how-to-organize-your-files-into-folders/">' . __('How to Organize Your Files into Folders', 'ee-simple-file-list') . '</a></li>
		<li><a target="_blank" href="https://simplefilelist.com/add-folder-support/">' . __('See the Demo', 'ee-simple-file-list') . '</a></li>
	</ul>
	
	<p class="eeCentered">' . $eeSFL_Button . '</p>
	
	</article>';
	
$eeSFL_Log[] = '$eeOrderURL ...';
$eeSFL_Log[] = urldecode($eeOrderURL);	

?>