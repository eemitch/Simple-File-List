=== Simple File List 4 ===
Contributors: eemitch
Donate link: http://simplefilelist.com/donations/simple-file-list-project/
Tags: file sharing, file list, file uploader, upload files, share files, exchange files, host files, sort files, dropbox, ftp
Requires at least: 4.0
Tested up to: 5.2.3
Stable tag: 3.2.13
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple File List is a basic file list with optional front-side uploader. Easily exchanges files with clients, customers, or within a group setting.

== Description ==

Simple File List is a basic file list with optional front-side uploader. The plugin provides an easy-to-integrate basic user interface allowing visitors, clients, customers, or associates to see a listing of your files (separate from the media library) and optionally upload files if you choose.

The list and/or the uploader can be turned on and off for the front-side of the website. You can show just the list, just the uploader, or both. You can also restrict uploading and/or viewing of the file list to only Admins or logged-in users. For list management, there is a page in the administration area which always shows both. Here you can easily view, upload, rename and delete files as needed.

Simple File List is also an alternative to using FTP or Dropbox for larger files. There's no need to deal with Dropbox or FTP usernames and passwords anymore! Everything is on your Wordpress website. All you need is a VPS or dedicated web server.


= This Plugin is Great For =

* Exchanging files when the sizes get too large for email attachments
* Sharing files with a community of others
* When you need a list of files, separate from the media library
* When you need a list of archived files, such as videos, PDF files, or music files.
* When you need a down-and-dirty simple front-side uploader so people can send you files.

= List Features =

* Limit access to the list to only Admins or logged in users.
* Delete unwanted files from the Admin page, and optionally allow front-side users to delete files.
* Rename files on the Admin-side.
* Optionally show file date, size and a thumbnail for images and videos.
* List can be set to sort file by name, date or file size ... ascending or descending.
* File List Table Sorting. In addition to default sorting, click on a column heading to sort it.
* Optionally show thumbnails for videos and image files, or file type icons for documents.
* Show or hide columns for the thumbnail, file size or file date columns.
* The file date format uses the format selected in your Wordpress General Settings.

= Uploader Features =

* Simple reliable uploader, works on mobile devices too.
* Allow uploading to only Admins or logged-in users, or turn it off completely.
* Fully customize the types of files you want users to be able to upload.
* Limit number of files uploaded per submission.
* Limit the maximum upload file size.
* Get an email notice each time a file is uploaded.
* Option to gather the uploader's name, email and comments to add to the upload notification.
* Custom Upload Folder - Specify whatever folder you like for users to upload files.

= Shortcode Attributes =

* Over-ride the main list's settings on a per page/post/widget basis using shortcode attributes.
* Shortcode Builder allows you to create custom file list shortcodes.
* Creates a new draft post/page with the shortcode in place, then redirects them to the new page.
* List Visibility: showlist="YES / USER / ADMIN / NO"
* Uploader Visibility: allowuploads="YES / USER / ADMIN / NO"
* File Thumbnail Column: showthumb="YES / NO"
* File Size Column: showsize="YES / NO"
* File Date Column: showdate="YES / NO"
* File Actions (Open | Download): showactions="YES / NO"
* File List Appearance: showheader="YES / NO"

= Internationalized =

* de_DE - German (Germany)
* es_ES - Spanish (Spain)
* es_MX - Spanish (Mexico)
* fr_CA - French (Canada)
* fr_FR - French (France)
* it_IT - Italian (Italy)
* ja_JP - Japanese (Japan)
* nl_NL - Dutch (Netherlands)
* pt_BR - Portuguese (Brazil)
* pt_PT - Portuguese (Portugal)
* sv_SE - Swedish (Sweden)

= Plus =

* Simple lightweight design, easy to style and customize.
* Committed support from the developer, including internal support contact form.

= ADD-ON - Folder Support (Optional) =

* Create folders and unlimited levels of sub-folders.
* Use a shortcode attribute to display specific folders: showfolder="folderA/folderB"
* Display different folders in different places on your site.
* You can even show several different folders on the same page and within widgets.
* Front-side users cannot navigate above the folder you specify.
* Breadcrumb navigation indicates where you are.
* Easily move files from folder to folder.
* Easily rename any folder.
* Easily delete any folder, along with all contents.
* Choose to sort folders first or sort along with the files.
* Optionally display folder sizes.

= ADD-ON - Search & Pagination (Optional) =

* Adds searching and pagination functionality.
* Designed to make very large file lists more manageable.
* Adds a search bar above the file list.
* Search by file name and/or date, if this column is displayed.
* Searches within sub-folders. (But not above the current folder)
* Pagination breaks up large file lists into smaller pages.
* Define the number of files per page in the settings.
* Show or hide the search bar and/or pagination in the settings.
* Updating to newer versions is just like other Wordpress plugins.


== Installation ==

1. To install, simply upload the plugin zip file to your Wordpress website and activate it. Configure the simple settings in the new menu that will appear in your sidebar. Explanations of each feature accompany the inputs.
2. To add the plugin to your website, simply add this shortcode: [eeSFL]
3. Customize the shortcode using attributes listed above.


== Frequently Asked Questions ==

= Q: Who is Simple File List for? =

A: This script is for media companies, print houses, educational institutions, music sites, etc… Anyone who exchanges files with clients and customers, or within a group setting.

= Q: Are the file uploaded to the Media Library? =

A: No, files are uploaded to a special folder inside your general WordPress uploads folder, or anywhere you specify.

= Q: Can people who upload overwrite existing files? =

A: No, files cannot be overwritten. If a file is uploaded having the same name as one already present, a series number is appended to the name ( filename_(2).ext ) You can use the renaming functionality to change this.

= Q: Can I place different lists in different places? =

A: Yes, with the optional Folder Support extension you can put different file lists on different posts, pages and widgets.

= Q: Are the files in the list searchable by Google and other search engines? =

A: Only if you place a list on the front-side of your website which is viewable to the general public. If you choose USER, ADMIN or NO, in the List Settings these files will not be indexed by search engines.

= Q: Can I customize the appearance of the list and uploader? =

A: Yes, the CSS is easily over-ridden, making it easy for anyone with CSS knowledge to customize the page design. Check the Instructions page within the plugin's admin panel for more info.

= Q: Can I create custom behavior after an upload completes? =

A: Yes, you can hook into the action "eeSFL_UploadCompleted" and do pretty much anything you want upon upload completion.

= Q: What is the maximum upload file size? =

A: This is a setting that you choose in the file configuration. The initial default is 8 MB. The absolute maximum size will depend on your hosting setup. which is automatically detected.

= Q: What if I have trouble or need assistance? Will you help? =

A: Yes! I enjoy helping people. Please contact me with any issues using the built in support form.

= Q: Why did you develop this plugin? =

A: I got tired of the difficulties of getting files back and forth between myself and my non-technical clients once they become too large for email. Training these people to use FTP or Dropbox was a challenge. I wanted something simple that I could use on my own website, so I created a simple index.php page that solved my problem. I later realized that others could benefit from this functionality, so I decided to port it to my favorite website platform; Wordpress. I also hoped that the donations would pay for a large home and a private jet, but that has not happened yet :-(  Regardless, I still enjoy giving to the community and helping others.



== Upgrade Notice ==

3.2.13 - Improvements and new features for Folder Support


== Screenshots ==

1. Front-side display, both upload and list activated.
2. Back-side display, both upload and list are always activated.
3. Back-side settings page.


== Changelog ==

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
* FFmpeg Support - Allows automatic thumbnail creation for videos.
* File List Table Sorting - Built-in file list table sorting. Click on a column heading to sort it.
* Forced Download Link - Allows for forced downloading of a file, rather than relying on the default browser action.
* Choose File Types - Fully customize the types of files you want users to be able to upload.
* Date Format - The file date format now uses the format selected in your Wordpress General Settings.
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

* FIRST RELEASE VERSION - Previous versions were non-Wordpress.
