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
	
	<a href="' . $eeSFL_FREE_Env['pluginURL'] . 'images/Folder-Demo.jpg" target="_blank">
		<img src="' . $eeSFL_FREE_Env['pluginURL'] . 'images/Folder-Demo.jpg" width="400" height="566" class="eeRight" alt="Screenshot of front-end file list" /></a>

	<h2>' . __('Upgrade to Simple File List Pro', 'ee-simple-file-list') . '</h2>
	
	<p>' . __('The Pro version adds features not available in the free version.', 'ee-simple-file-list') . ' ' . __('The Pro version is also extendable, allowing you to add more specific functionality as needed.', 'simple-file-list') . '</p>
	
	<h3>' . __('Create Unlimited Folders', 'ee-simple-file-list') . '</h3>
	
	<ul>
		<li>' . __('Create folders and unlimited levels of sub-folders.', 'ee-simple-file-list') . '</li>
		<li>' . __('Use this shortcode attribute to display specific folders: ', 'ee-simple-file-list') . '
			<br />[eeSFL showfolder="folder-name"]<br />
			[eeSFL showfolder="folder-name/another-folder/another"]</li>
		<li>' . __('Display different folders in different places on your site.', 'ee-simple-file-list') . '
		<li>' . __('Breadcrumb navigation indicates where you are.', 'ee-simple-file-list') . '</li>
		<li>' . __('You can even show several different folders on the same page and within widgets.', 'ee-simple-file-list') . '</li>
		<li>' . __('Front-side users cannot navigate above the folder you specify.', 'ee-simple-file-list') . '</li>
		<li>' . __('Choose to sort folders first or sort along with the files.', 'ee-simple-file-list') . '</li>
		<li>' . __('Choose to display folder sizes or the count of items within.', 'ee-simple-file-list') . '</li>
		
	</ul>
	
	<a href="' . $eeSFL_FREE_Env['pluginURL'] . 'images/SFL-Pro-Admin.jpg" target="_blank">
		<img src="' . $eeSFL_FREE_Env['pluginURL'] . 'images/SFL-Pro-Admin.jpg" width="400" height="342" class="eeRight" alt="Screenshot of back-end file list" /></a>
	
	<h3>' . __('More Pro Features', 'ee-simple-file-list') . '</h3>
	
	<ul>
		
		<li>' . __('Edit the modification date of any file or folder.', 'ee-simple-file-list') . '</li>
		<li>' . __('Bulk file editing allows you to download, move, delete or add descriptions to many files or folders at once.', 'ee-simple-file-list') . '</li>
		<li>' . __('Download multiple files or folders at once as a zip file.', 'ee-simple-file-list') . '</li>
		<li>' . __('Allow front-end users to download a folder as a zip file.', 'ee-simple-file-list') . '</li>
		<li>' . __('Share files by sending emails containing file links to others.', 'ee-simple-file-list') . '</li>
		<li>' . __('Optionally define a custom directory for your file list.', 'ee-simple-file-list') . '</li>
		<li>' . __('The Tools Tab allows you to reset settings, the file list array and more.', 'ee-simple-file-list') . '</li>
		
	</ul>
	
	<h3>' . __('Pro is Extendable', 'ee-simple-file-list') . '</h3>
	
	<ul>
		
		<li>' . __('Optionally add extensions to add further functionality.', 'ee-simple-file-list') . '
			<ul>
				<li><a href="https://simplefilelist.com/file-access-manager/?pr=free" target="_blank">' . __('File Access Manager', 'ee-simple-file-list') . '</a></li>
				<li><a href="https://simplefilelist.com/add-search-pagination/?pr=free" target="_blank">' . __('Search and Pagination', 'ee-simple-file-list') . '</a></li>
			</ul>
		</li>
		<li>' . __('Updating to newer versions is just like the free plugin.', 'ee-simple-file-list') . '</li>
		<li><a target="_blank" href="https://simplefilelist.com/simple-file-list-demo/?pr=free">' . __('Try the Demo', 'ee-simple-file-list') . '</a></li>
	</ul>
	
	<p class="eeCentered">' . $eeSFL_Button . '</p>
	
	</article>';	

?>