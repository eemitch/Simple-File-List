=== Simple File List ===
Contributors: eemitch
Donate link: http://simplefilelist.com
Tags: file list, file sharing, file upload form, upload files, exchange files, host files, zip files, dropbox, ftp
Requires at least: 4.0
Requires PHP: 7
Tested up to: 6.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple File List gives your WordPress website a list of your files which allows your users to open and download them.

== Description ==

Simple File List is a free plugin that is great for when you need to provide a list of files, either publicly available or private to logged-in users. Place a file list anywhere on your site using a simple shortcode, allowing your front-end users to open, download and optionally edit them. Users can also upload files if you choose.

Simple File List is also a good alternative for organizations using clumsy FTP or Dropbox for larger files. Simply provide your clients with a link to their file list.

<img src="https://ps.w.org/simple-file-list/assets/screenshot-1.png" alt="Screenshot" />

## Features

* Displays a file list, file uploader or both using simple shortcode: [eeSFL]
* Manage your files and the list settings from the Admin List on the back-end.
* Choose from three file list styles: table, tiles or flex.
* Choose from a light or dark theme, or choose no theme and provide the styling of your theme.
* Complete settings for the file list style and display, file upload restrictions, and upload notifications. 
* Both the front-end list and file upload form can be shown to users based on their role; Everyone, Only Logged-in User, Only Admins or Nobody (OFF).
* Collect the users name, email and description of the file(s) uploaded. This can optionally be shown in the file list.
* Files can be assigned descriptions, which can be added from the Admin list or user uploads.
* Optionally allow your front-end users full control over renaming, moving, sending, deleting and editing descriptions.

## This Plugin is Great For:

* Posting official documents.
* Sharing files within an organization.
* Sharing files with business clients or a community.
* Enabling distance learning by allowing schools to share class materials with students. 
* When you need a list of archived files, such as videos, PDF files, or music files.
* When you need a simple front-side uploader so people can send you files.
* Exchanging files when the sizes get too large for email attachments.

## File List Features

* Limit access to only Admins or logged-in users, or hide the list and only show the uploader.
* Add and manage your files from the Admin List on the WordPress back-end.
* Show details like file dates, size and a thumbnail for PDFs, images and videos.
* Add descriptions to files and optionally show them in your list.
* Sort file by name, date modified, date added or file size ... ascending or descending.
* Files are kept separate from the media library.


## File Uploader Features =

* Simple reliable uploader, works on mobile devices too.
* Drag and drop zone, plus upload progress bar
* Allow uploading to only Admins or logged-in users, or turn it off completely.
* Limit the types of files users can upload.
* Limit number of files uploaded per submission.
* Limit the maximum upload file size.
* Get an email notice each time a file is uploaded.
* Option to gather the uploader's name, email and file description.


##Internationalized

* cz_CZ - Czech (Czech Republic)
* da_DK - Danish (Denmark)
* de_DE - German (Germany)
* es_ES - Spanish (Spain)
* es_MX - Spanish (Mexico)
* fr_CA - French (Canada)
* fr_FR - French (France)
* fr_BE - French (Belgium)
* it_IT - Italian (Italy)
* nl_NL - Dutch (Netherlands)
* pt_BR - Portuguese (Brazil)
* pt_PT - Portuguese (Portugal)
* sv_SE - Swedish (Sweden)


###Plus

* Simple lightweight design, easy to style and customize.
* Committed and responsive support from the developer.

###Try the Demo

Try out the free demo:

[Simple File List Demo](https://free.simple-file-list.com)


##Upgrade to the PRO Version to Add Sub-Folder Support

* Create unlimited levels of sub-folders.
* Use a shortcode attribute to display specific folders.
**[eeSFL folder="folderA/folderB"]**
* Display different folders in different places on your site.
* You can even show several different folders on the same page and within widgets.
* Front-side users cannot navigate above the folder you specify.
* Breadcrumb navigation indicates where you are.
* Easily move files and folders as needed.
* Rename folders and add descriptions, which can be shown in the file list.
* Quickly delete any folder, along with all contents.
* Choose to sort folders first or sort along with the files.
* Optionally display folder sizes.
* Optionally define a custom file list directory.

[Get Simple File List Pro](htts://get.simplefilelist.com/)


##PRO Extensions

##File Access Manager

* Lock down your files to prevent direct access. Only allow specific users to see the file you want them to.
* Limit file access to only logged-in users. Specify the minimum role or specify a matched role.
* Create a file list for a specific WordPress user or a group of users.
* For each list mode you can separately control permissions for file uploading and front-side file management.

##Create Five Types Lists

**Normal Mode**
Do not restrict access. Files are viewable by anyone who can reach the file list page. Files may also be linked-to from outside of your website.

**Limited Mode**
Restrict file list access to all except WordPress users matching a specified role or with a minimum role or higher.

**Group Mode**
Restrict file list access to a specified group of WordPress users.

**User Mode**
Restrict file list access to a specific WordPress user.

**Restricted Mode**
Restrict access to all of the files by default. Grant access to specific files to specific users or roles.

##Search & Pagination

* Adds searching and pagination functionality.
* Designed to make very large file lists more manageable.
* Adds a search bar above the file list.
* Search by file name and/or date, if this column is displayed.
* Searches within sub-folders. (But not above the current folder)
* Pagination breaks up large file lists into smaller pages.
* Define the number of files per page in the settings.
* Show or hide the search bar and/or pagination in the settings.
* Updating to newer versions is just like other WordPress plugins.
* Shortcode attributes to control search visibility and pagination functionality.
**[eeSFL search="YES/NO" paged="YES/NO" filecount="25"]**
* Use a shortcode to place a search form anywhere on your website.
**[eeSFLS permalink='file-list-url']**

[More Information](htts://simplefilelist.com/) | [Try the Demo](https://demo.simple-file-list.com/add-search-and-pagination/)


== Installation ==

Just like most other WordPress plugins...

1. To install, simply use the amazing WordPress plugin installer, or upload the plugin zip file to your WordPress website, and activate it.
1. A new main menu item will appear: **File List**  Click on this.
1. Then click on **Settings** tab and configure the features you want for your file list.
1. Upload some files by clicking on the **Upload Files** button
1. To add the file list to your website, simply add this shortcode: **[eeSFL]**
1. Over-ride the settings using the shortcode attributes listed above.


== Frequently Asked Questions ==

= Q: Who is Simple File List for? =

A: Anyone who exchanges files with clients, customers or within an organization.

= Q: Are the file uploaded to the Media Library? =

A: No, files are uploaded to a special folder inside your general WordPress uploads folder, or anywhere you specify.

= Q: Can I limit the access to my file to only logged-in users? =

Yes, you can limit to Admins or anyone who is logged in. If you need further users access control, consider the <a href="https://simplefilelist.com/file-access-manager/">File Access Manager</a> extension*.

= Q: Can people who upload overwrite existing files? =

A: No, by default a file will not be overwritten. If a file is uploaded having the same name as one already present, a series number is appended to the name ( filename_(2).ext ). If you don’t want this, uncheck the box on the Upload Settings tab. 

= Q: Can I place different lists in different places? =

A: Yes, you can put place your file lists on different posts, pages and widgets. If you upgrade to the <a href="https://simplefilelist.com">Pro</a>version, you can even place different folders on your site.

= Q: Are the files in the list searchable by Google and other search engines? =

A: Only if you place a list on the front-side of your website which is viewable to the general public. If you choose USER, ADMIN or NO, in the List Settings these files will not be indexed by search engines.

= Q: Can I customize the appearance of the list and uploader? =

A: Yes, the CSS is easily over-ridden, making it easy for anyone with CSS knowledge to customize the page design. Check the Instructions page within the plugin's admin panel for more info.

= Q: Can I create custom behavior after an upload completes? =

A: Yes, you can hook into the actions "eeSFL_UploadCompleted" and "eeSFL_UploadCompletedAdmin" and do pretty much anything you want upon upload completion.

= Q: What is the maximum upload file size? =

A: This is a setting that you choose in the file configuration. The initial default is 8 MB. The absolute maximum size will depend on your hosting setup. which is automatically detected.

= Q: What if I have trouble or need assistance? Will you help? =

A: Yes! I enjoy helping people. Please contact me with any issues using the <a href="https://wordpress.org/support/plugin/simple-file-list/">WordPress Forum</a> or via <a href="https://simplefilelist.com/get-support/">simplefilelist.com</a>

= Q: Why did you develop this plugin? =

A: I got frustrated with the difficulties of getting files back and forth between myself and my non-technical clients once they become too large for email, and having an archive for them to return to was needed. Training these people to use FTP or Dropbox was a challenge. I wanted something simple that I could use on my own website, so I created a simple index.php page that solved my problem. I later realized that others could benefit from this functionality, so I decided to port it to my favorite website platform; WordPress. I also hoped that the donations would pay for a large home and a private jet, but that has not happened yet :-(  Regardless, I still enjoy giving to the community and helping others.




== Upgrade Notice ==

* 6.0.4 - Improvements and Bug Fixes


== Screenshots ==

1. Front-side display, both upload and list activated.
2. Back-side display, both upload and list are always activated.
3. Back-side settings page.


== Changelog ==

= 6.0.4 = 
* Added additional shortcode attributes: style and theme
* Bug fix where eeSFL_ScrollToIt was not defined.
* Bug fix where the file link within the notice email was broken.

= 6.0.3 =
* Bug Fix where upload form appeared even if turned off.
* Bug fix where PHP Warning was thrown on missing array key.

= 6.0.2 =
* Major UI and code base improvements.
* NEW -> Added ability to give a file a "Nice Name". This displays in place of the real file and can contain characters that are not allowed in file names.
* NEW -> Added option to choose from three responsive file list styles: Table, Tiles or Flex
* NEW -> Added option to choose from three theme options: Light, Dark or None.
* NEW -> Added option to allow the upload form to appear either above or below the file list.
* NEW -> Added to choose the date type displayed. This shows the selected date type (added or modified) despite the sort settings.
* NEW -> Added inputs for customizing the description and file submitter label text.
* NEW -> Added an option to bypass the post-upload results page and go straight to the file list.
* NEW -> Added shortcode display with copy-to-clipboard button to admin UI (top-right)
* NEW -> Added ability to override the locale setting in order to display English on the back-end list and settings tabs.
* Redesigned the File Edit dialog to open in a modal dialog box rather than inline with the file. All changes can be saved at once now.
* Improved the upload form to display a list of the files to be uploaded. You can also remove individual files if needed.
* Separated the 'Get Uploader Info' form settings. Now you can get the description and/or the submitter's information, rather than only both.
* The file list table is now responsive, shifting to a vertical block display on small screens.
* Removed the Shortcode Builder. If you need to over-ride existing list settings, refer to: https://simplefilelist.com/shortcode/ for a list of attributes.
* Now Requires PHP 7 +


= 4.4.9 =
* Added ability to create video thumbnails for the WebM format.
* Fixed where a double-slash in the item's path would cause Error 99.
* Bug fix where the site's URL was missing (unpredictably) from file and folder link URLs on the Admin side.

= 4.4.8 =
* Bug fix where warning was being thrown if the server did not offer any thumbnail generation helpers.
* Now sanitizing the names of externally added files the same as if using the plugin upload form.

= 4.4.7 =
* Added automatic thumbnail generation for TIFF (.tif & .tiff) image file types.
* Bug fix where disallowed file types were being listed if added outside the plugin.
* Updated documentation.

= 4.4.6 = 
* Added new option settings to allow you to individually show or hide the Open, Download and/or Copy Link actions on the front-end.
* Added a new option setting to Enable or Disable the front-side Smooth Scrolling effect.
* Language updates

= 4.4.5 =
* Bug fix for undefined function.

= 4.4.4 =
* Added a fix to prevent the "All in One SEO" plugin from parsing Simple File List shortcode, which was breaking front-end features.
* Added a check to see if the PHP function shell_exec() is available.

= 4.4.3 =
* Removed the mostly unnecessary free version cache feature. External uploads, like FTP, will now appear immediately.
* Minor code and style improvements.

= 4.4.2 =
* Bug fix where generated thumbnails were not getting updated when the main files were renamed or deleted.
* Bug fix where file names using multiple spaces was causing the post-upload processor to fail.
* Bug fix where the displayed max upload size was not accurate in some cases.

= 4.4.1 =
* Added ability to generate thumbnails for PDF files. (by popular demand)
* Added options for generating image, PDF and video thumbnails.
* jQuery code updates

= 4.3.6 =
* Added a "Copy Link" file operation. This copies the file's URL to your clipboard.
* Bug fix where file description was not removable.
* Bug fix where fatal error was thrown on settings form submit.
* Bug fix where a duplicate file array was created even if over-writing was allowed.
* Language fixes.

= 4.3.5 =
* Added smooth-scrolling down to the file list section after upload. (by popular demand)
* Original file modification date is now preserved for file uploads. (by popular demand)
* Sorting options now available for Date Added and Date Changed.
* Redesigned the Edit Details dialog.
* Reorganized the List Settings tabs.
* Simplified Re-Scan Interval by changing to an ON/OFF setting. If ON, File List Cache expires in one hour, else the disk is scanned on each page load.
* Admins can now always add file descriptions when uploading on the back-end.
* Admins now see all file details on the back-end, regardless of front-side settings.
* Improved and added translations.
* Many many under-the-hood efficiency and security improvements.

= 4.2.15 =
* Bug fix to prevent SPAM abuse.

= 4.2.13 =
* Extensive under-the-hood changes so this version and the Pro version can run at the same time.
* Now deleting corrupt image files (rather than jut warning) to prevent images disguised as malware being listed.
* Removed support for extensions and added path to free upgrade to Pro for all extension owners.
Pages using the "showfolder" shortcode attribute will show "ERROR 95" where the file list would be.
* Removed support for files sending.
* Added MIDI file type icon

= 4.2.12 =
* Moved the "Delete" button from inside the File Edit dialog up to the File Actions links. (By popular demand)
* Fixed a pesky bug where uploading a file with a name that required changing did not show in the confirmation list and wrongly applied meta data to the existing file.
* Now updating the file modification date if file meta data is added or changed.
* Process logging improvements.
* Bug fix where file name formatting was reflected in the downloaded file.

= 4.2.11 =
* Fixed a bug where the file uploader's info was not being collected.
* Fixed a bug where the "Get Submitter Information" setting was getting cleared when saving Upload Settings. 
* French and German language improvement.
* Code improvements.

= 4.2.10 =
* Fixed a bug where list settings were not working if WordPress is in a sub-folder.
* Fixed a Javascript warning shown when the uploader was not being shown.
* Removed the Search and Pagination extension integration.
* Various minor changes.


= 4.2.8 =
* Fixed several security issues.
* Updated input sanitization to use WordPress functions.
* Under-the-hood code improvements.


= 4.2.7 =
* Speed and server load improvements.
* Fixed a couple of bugs with sorting.
* Fixed a bug where descriptions were not sticking to folders.
* Improved the French translations, added French-Belgium translations.


= 4.2.6 =
* Various security improvements
* Code cleanup and Ajax Improvements

= 4.2.3 =
* Disallowed changing the file type when renaming, as this presented a security risk for untrusted users.
* Add sorting shortcodes [eeSFL sortby="Name, Date, Size, or Random" sortorder="Descending or Ascending"]
* Fixed where clicking the Open link for a folder was opening in a new tab.
* Improved the upload info form's validation. Name and email are now required if getting uploader info.
* Fixed an issue where both renaming a file and adding/editing the description caused the description to be applied to the wrong file.


= 4.2.2 =
* Fixed a bug where some had a bad experience upon updating.

= 4.2.1 =
* Improved flexibility when using multiple shortcodes per page.
* Now auto-detecting a logged-in user and auto-populating the name and email fields on the upload form.
* Added option to allow uploaded files to over-write existing files, rather than numbering new files with the same name.
* No longer hiding upload settings if the uploader is set to not display.
* Fixed a site crash after adding a very very large image file.
* CSS Improvements
* Various bug fixes and improvements

= 4.1.3 =
* Added shortcodes for Search & Pagination customization
** [eeSFL paged="(YES/NO)" filecount="25" search="(YES/NO)"]

= 4.1.2 =
* Fixed missing thumbnails when ffMpeg is installed, but fails to read the source video format.

= 4.1.1 =

* Added new admin-side action hook: eeSFL_UploadCompletedAdmin
* Video thumbnails were not being created via ffMpeg
* Thumbnail images were not following proper path
* Addressed Error 99 issue on Windows installations
* Various bug fixes and code improvements

= 4.1.0 =

* Improved the uploader with a drag-and-drop zone, plus a file upload progress bar.
* Added file descriptions with option to show on front-side or not.
* Added saving of upload info form inputs which can optionally be displayed as the file description on the front-side.
* Created a new after-upload view which shows only the file(s) uploaded.
* Added ability to customize the file lists heading labels.
* Added ability to show or hide the file extension.
* Added ability to preserve spaces in file names, which are normally replaced with hyphens.
* Expanded configuration options for the upload notification emails (from, from name, to, cc, bcc, subject, and body text)
* Added ability to restrict access to the plugin’s list and upload settings by WordPress user role
* New Shortcode: hidename="this-file.docx, that-folder"  Hide specific file names in the list. Accepts a comma list of file names.
* New Shortcode: hidetype="psd, zip, folder"  Hide specific file types in the list. Accepts a comma list of file extensions.
* Implemented a database-based file lists to improve performance and allow for meta data tracking.
* Added UploadDir status transient, instead of checking each time.
* Added option to set list re-scan interval every 0 to 24 hours. Zero will work like old versions, accessing the file system on each load.
* Moved settings to a single array in wp_options, instead of each separately, reducing code and database hits significantly.
* Rewrote the single-level file directory listing method, using scandir() instead of the old opendir() method.
* Improved styling, with much improved table display on small screens
* Changed Rename and Delete links to use new AJAX routines. Removed the delete checkbox form and created File Edit accordion below each file.
* Expanded settings, separating general list settings from list display options.

= 3.2.17 =

* Bug fix where email notices were failing to send to multiple addresses.

= 3.2.16 =

* File URL bug fix

= 3.2.15 =

* Improved Search & Pagination integration

= 3.2.14 =

* Translation updates and a minor change related to extension updating

= 3.2.13 =

* Improvements and new features for Folder Support

= 3.2.12 =

* Fixed the admin-side ugly arrow issue when file names create broken paths.
* Changed "Add Folder Support" to "Add Features".
* Now fully supports the new Search & Pagination extension.

= 3.2.11 =

* Further improved thumbnail load times.
* Revamped the file download process.

= 3.2.10 =

* Front-side-delete bug fixes.
* Improved thumbnail loading speed.
* Unseen fixes, improvements and progress.

= 3.2.9 =

* Minor bug fix

= 3.2.8 =

* Further security improvements to the file downloader.

= 3.2.7 =

* Improved file upload process.
* Added support for optional search & pagination (coming soon)
* Patched a security issue with the file downloader engine.

= 3.2.6 =

* Bug fix to allow files with uppercase extensions to upload properly.

= 3.2.5 =

* Fixed broken link within the email notice if the file name had spaces.
* Fixed a security issue with the file downloader and deletion.

= 3.2.4 =

* Addressed a bug causing extension updating failure
* Improved process logging for better support

= 3.2.3 =

* NEW Shortcode Builder: Dynamically create custom file list shortcode. Folder support ready.
* Now you can automatically create a new draft post/page with your shortcode.
* Fixed a bug where the default thumbnail SVG was not appearing for unaccounted-for file types
* Minor Admin UI changes

= 3.2.2 = 

* Fixed an issue where jQuery was not being loaded on some themes.
* Fixed an issue where File Thumbs were being turned off upon plugin update
* Fixed a few other minor bugs

= 3.2.1 =

* Fixed erroneous error message on multi-file uploads.
* Readme.txt content updated

= 3.2 =

* Added file renaming functionality
* Added new shortcode options: showthumb, showsize, showdate, showactions and showheader to allow customization of the table appearance.
* Added Front-Side file deletion and show/hide table header options
* Added ability to either show or hide the File Actions links ( Open | Download ) to the List Settings
* Improved file name sanitizing to account for non-english characters
* Added custom action hook, eeSFL_UploadCompleted, to allow developers to customize post-upload behavior
* Fixed a bug where file downloading used the wrong MIME type
* Fixed broken upload notifications
* The plugin now supports Folders with purchase of a plugin extension. Check out the Folders tab

= 3.1.2 =

* Bug Fixes
* Fixed an error in the shortcode instructions
* Improved support form
* Merged uploader and list containers

= 3.1.1 =

* Thumbnails - Optionally show thumbnails for videos and image files, or file type icons for documents.
* ffMpeg Support - Allows automatic thumbnail creation for videos.
* File List Table Sorting - Built-in file list table sorting. Click on a column heading to sort it.
* Forced Download Link - Allows for forced downloading of a file, rather than relying on the default browser action.
* Choose File Types - Fully customize the types of files you want users to be able to upload.
* Date Format - The file date format now uses the format selected in your WordPress General Settings.
* Custom Upload Folder - Specify whatever folder you like for users to upload files.
* Additional Viewing Restrictions - Limit viewing the list and/or uploading only to Admins.
* Shortcode Attributes - Hide the list or the uploader on a per page/post basis using [eeSFL showlist="NO" allowuploads="NO"]
* Improved Admin Dashboard / Simplified Menu
* Extensive under-the-hood updates and improvements

= 3.0.6 =
* Minor Bug Fix

= 3.0.5 =
* Minor Bug Fix

= 3.0.4 = 

* Added ability to hide the size or date columns
* Improved the list loading speed for large lists
* Improved file uploader process and speed
* Added ability to limit the number of files uploaded
* Improved upload email notifications
* Added ability to allow uploading to only logged-in users
* Added ability to show the list to only logged-in users
* Improved compatibility with password protected directories
* Revamped and greatly improved Admin Panel
* Improved usage instructions and added integrated email support form
* Added Internationalization (l18n), with several default translations

= 2.0.8 =

* Fixed activation bug *
* Improved directory creation failure fallback
* Fixed bug where no uploader would appear in the admin area if "None" was selected for the front-side

= 2.0.7 = 

* Added multi-file uploading option
* Added optional front-side user input form
* Added configurable upload directory option
* Added File List sorting options
* Improved HTML display and design
* Improved email notifications
* Added ability to send notifications to more than one address
* Many minor bug fixes and basic improvements

= 1.0.4 =

* Added settings option to add the username of the uploader to the file name.
* Improved date format
* Added support for the Table Sorter plugin by Farhan Noor, so the file list may be sortable by name or date
* Minor bug fixes

= 1.0.3 = 

* HTML Bug Fix
* Improved error reporting

= 1.0.2 =

* Fixed issue where uploading failed from Admin page
* Improved error reporting


= 1.0.1 =

* First Update - Minor Changes

= 1.0 =

* FIRST RELEASE VERSION - Previous versions were non-WordPress.
