<?php // Simple File List Script: ee-get-pro.php | Author: Mitchell Bennis | support@simplefilelist.com
	
defined( 'ABSPATH' ) or die( 'No direct access is allowed' );
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_ThisEmail = get_option('admin_email');
if(!$eeSFL_ThisEmail) { $eeSFL_ThisEmail = 'mail@' . $eeSFL_ThisDomain; }

// The Content
$eeOutput .= '<section class="eeSFL_Settings">

<div class="eeSettingsTile">

<article class="eeGoPro">
	
	<h1>' . __('Upgrade to Simple File List Pro', 'ee-simple-file-list') . '</h1>
	
	<a href="' . $eeSFL_BASE->eeEnvironment['pluginURL'] . 'images/SFL-Pro-Admin-List.jpg" target="_blank">
		<img src="' . $eeSFL_BASE->eeEnvironment['pluginURL'] . 'images/SFL-Pro-Admin-List.jpg" width="663" height="721" class="eeFloatRight" alt="Screenshot of back-end file list" />
	</a>
	
	<p>' . __('The Pro version adds new features and is further extendable.', 'simple-file-list') . '</p>
	<p>' . __('Cost is just once per domain. This includes all first-level sub-domains, plus a separate staging domain. There are no recurring fees.', 'ee-simple-file-list') . '</p>
	
	<h2>Pro Features</h2>
	
	<ul>
		<li>' . __('Create folders and unlimited levels of sub-folders.', 'ee-simple-file-list') . '</li>
		<li>' . __('Use the shortcode to display specific folders:', 'ee-simple-file-list') . '<br />
		<code>[eeSFL folder="Folder-A"]</code><br />
		<code>[eeSFL folder="Folder-A/Folder-B"]</code></li>
		<li>' . __('Display different folders in different places on your site.', 'ee-simple-file-list') . '
		<li>' . __('Breadcrumb navigation indicates where you are.', 'ee-simple-file-list') . '</li>
		<li>' . __('You can even show multiple folders on the same page', 'ee-simple-file-list') . '<a target="_blank" href="https://demo.simple-file-list.com/show-multiple-folders-on-a-single-page/">*</a></li>
		<li>' . __('Front-side users cannot navigate above the folder you specify.', 'ee-simple-file-list') . '</li>
		<li>' . __('Sort folders first or sort along with the files.', 'ee-simple-file-list') . '</li>
		<li>' . __('Display folder sizes and the count of items within.', 'ee-simple-file-list') . '</li>
		<li>' . __('Optionally define a custom directory for your file list.', 'ee-simple-file-list') . '*</li>
		<li>' . __('Bulk file editing allows you to download, move, delete or add descriptions to many files or folders at once.', 'ee-simple-file-list') . '</li>
		<li>' . __('Edit file dates. Change the date added or the modification date of any file or folder.', 'ee-simple-file-list') . '</li>
		<li>' . __('Allow front-end users to download entire folders, or multiple files or folders at once as a zip file.', 'ee-simple-file-list') . '</li>
		<li>' . __('Use the Shortcode Builder to create custom snippets for secondary file list location.', 'ee-simple-file-list') . '</li>
		<li>' . __('A Tools Tab allows you to reset settings, the file list array and delete orphaned thumbnails.', 'ee-simple-file-list') . '</li>
		<li>' . __('Updating Pro to newer versions works just like the free plugin.', 'ee-simple-file-list') . '</li>
	</ul>
	
	<h2>' . __('Pro is More Extendable', 'ee-simple-file-list') . '</h2>
	
	<p>' . __('Pro extensions to give you even more features:', 'ee-simple-file-list') . '</p>
	
	<ul>
		<li><a href="https://simplefilelist.com/file-access-manager/?pr=free" target="_blank">' . __('File Access Manager', 'ee-simple-file-list') . '</a><br />' . 
		__('Create additional file lists, each with its own directory, settings and access restrictions.', 'ee-simple-file-list') . ' ' . 
		__( 'Limit list or file access by WordPress user or role.', 'ee-simple-file-list') . '</li>
		<li><a href="https://simplefilelist.com/add-search-pagination/?pr=free" target="_blank">' . __('Search and Pagination', 'ee-simple-file-list') . '</a><br />' . 
		__('Search for files by name, description, date range or file owner.', 'ee-simple-file-list') . ' ' . 
		__('Add pagination to break up large file lists into smaller sections.', 'ee-simple-file-list') . '</li>
		<li><a href="https://simplefilelist.com/send-files-by-email/?pr=free" target="_blank">' . __('Send Files by Email', 'ee-simple-file-list') . '</a><br />' . 
		__('Send an email with links to your files. Send to multiple recipients and CC more.', 'ee-simple-file-list') . '</li>
	</ul>

	<p><a class="button eeGet" target="_blank" href="https://demo.simple-file-list.com/">' . __('Try the Pro Demo', 'ee-simple-file-list') . '</a> 
	<a class="button eeGet" target="_blank" href="https://get.simplefilelist.com/index.php?eeDomain=' . urlencode( $eeSFL_BASE->eeSFL_GetThisURL(FALSE) ) . '&eeExtension=ee-simple-file-list-pro&eeEmail=' . urlencode($eeSFL_ThisEmail) . '">' . __('Upgrade to Pro Now', 'ee-simple-file-list') . '</a></p>
	
</article>

</div>

</section>';	

?>