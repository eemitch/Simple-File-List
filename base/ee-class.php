<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

class eeSFL_MainClass {
			
	// Properties of SFL
	public $eeEnvironment = array(); // Environment Details
	public $eeFileScanArray = array(); // Contains All Items
	public $eeFileListDefaultDir = 'simple-file-list/';
    public $eeForbiddenTypes = array('php','phar','pl','py','com','cgi','asp','exe','js','phtml', 'wsh','vbs');
    public $eeLocaleSetting = ''; // For back-end language
    public $eeListRun = 1; // Count of lists per page
	public $eeURL = ''; // What's in the address bar
    
    // The Log
    public $eeLog = array(
	    'Log' => '0.000s | 0 MB '. eeSFL_PluginName . ' is Loading...',
	    'Version' => 'Version: ' . eeSFL_ThisPluginVersion,
	    'errors' => array(),
	    'warnings' => array(),
		'issues' => array(), 
	    'messages' => array(),
	    'notice' => array()
    );
	
    
    // List Settings ---------------------------
    
    // The List ID
    public $eeListID = 1; // SFLA May Create Additional Lists
    
    // Settings for the Current List
    public $eeListSettings = array();
    
    // List Template
    public $eeDefaultListSettings = array( // An array of file list settings arrays
			
		// List Settings
		'ListTitle' => 'Main File List', // List Title (Not currently used)
		'FileListDir' => eeSFL_FileListDirDefault, // List Directory Name (relative to ABSPATH)
		'UseCache' => 'OFF', // Re-Scan Interval: Each, Hour, Day, OFF
		'UseCacheCron' => 'NO', // Use the Wordpress Cron-like System, or not
		'ShowList' => 'YES', // Show the File List (YES, ADMIN, USER, NO)
		'ShowListStyle' => 'TABLE', // TABLE, TILES or FLEX
		'ShowListTheme' => 'LIGHT', // LIGHT, DRK or NONE
		'AdminRole' => 5, // Who can access settings, based on WP role (5 = Admin ... 1 = Subscriber)
		'ShowFileThumb' => 'YES', // Display the File Thumbnail Column (YES or NO)
		'ShowFileDate' => 'YES', // Display the File Date Column (YES or NO)
		'ShowFileDateAs' => 'Changed', // Which date to show: Added or Changed
		'ShowFileSize' => 'YES', // Display the File Size Column (YES or NO)
		'LabelThumb' => 'Thumb', // Label for the thumbnail column
		'LabelName' => 'Name', // Label for the file name column
		'LabelDate' => 'Date', // Label for the file date column
		'LabelSize' => 'Size', // Label for the file size column
		'LabelDesc' => 'Description', // Label for the file description
		'LabelOwner' => 'Submitter', // Label for the file owner
		'SortBy' => 'Name', // Sort By (Name, Added, Changed, Size, Random)
		'SortOrder' => 'Ascending', // Descending or Ascending
		'ShowFileDateAs' => 'Changed', // Which Date shows in the Display
		'MaxSize' => 131072, // (25GB) The maximum size of the list
		
		// Display Settings
		'GenerateImgThumbs' => 'YES', // Create thumbnail images for images if possible.
		'GeneratePDFThumbs' => 'NO', // Create thumbnail images for PDFs if possible.
		'GenerateVideoThumbs' => 'NO', // Create thumbnail images for videos if possible.
		'PreserveName' => 'YES', // Show the original file name if it had to be sanitized.
		'ShowFileDesc' => 'YES', // Display the File Description (YES or NO)
		'ShowFileActions' => 'YES', // Display the File Action Links Section (below each file name) (YES or NO)
		'ShowFileOpen' => 'YES', // Show this operation
		'ShowFileDownload' => 'YES', // Show this operation
		'ShowFileCopyLink' => 'YES', // Show this operation
		'ShowFileExtension' => 'YES', // Show the file extension, or not.
		'ShowHeader' => 'YES', // Show the File List's Table Header (YES or NO)
		'ShowUploadLimits' => 'YES', // Show the upload limitations text.
		'ShowSubmitterInfo' => 'NO', // Show who uploaded the file (name linked to their email)
		'AllowFrontManage' => 'NO', // Allow front-side users to manage files (YES or NO)
		'SmoothScroll' => 'YES', // Use the awesome and cool JavaScript smooth scroller after an upload or folder click
		
		// Audio/Video
		'AudioEnabled' => 'YES', // Show AV Controls
		'AudioHeight' => 20, // Height of the Audio Player Bar
		
		// Folders
		'AllowFolderDownload' => 'NO', // Allow front-end users to download a folder as a ZIP file
		'AllowBulkFileDownload' => 'NO', // Allow front-end users to download more than one file at a time
		'ShowBreadCrumb' => 'YES', // Navigation above the list
		'FoldersFirst' => 'YES', // Group folders together at the top
		'ShowFolderSize' => 'YES', // Calculate the size of each folder
		
		// Upload Settings
		'AllowUploads' => 'USER', // Allow File Uploads (YES, ADMIN, USER, NO)
		'UploadLimit' => 10, // Limit Files Per Upload Job (Quantity)
		'UploadMaxFileSize' => 8, // Maximum Size per File (MB)
		'FileFormats' => 'jpg, jpeg, png, tif, pdf, mov, mp4, mp3, zip', // Allowed Formats
		'AllowOverwrite' => 'NO', // Number new files with same name, or just overwrite.
		'UploadConfirm' => 'YES', // Show the upload confirmation screen, or go right back to the list.
		'UploadPosition' => 'Above', // Above or Below the list
		'GetUploaderDesc' => 'NO', // Show the Description Form
		'GetUploaderInfo' => 'NO', // Show the User Info Form
		
		// Notifications
		'Notify' => 'NO', // Send Notifications (YES or NO)
		'NotifyTo' => '', // Send Notification Email Here (Defaults to WP Admin Email)
		'NotifyCc' => '', // Send Copies of Notification Emails Here
		'NotifyBcc' => '', // Send Blind Copies of Notification Emails Here
		'NotifyFrom' => '', // The sender email (reply-to) (Defaults to WP Admin Email)
		'NotifyFromName' => 'Simple File List', // The nice name of the sender
		'NotifySubject' => 'File Upload Notice', // The subject line
		'NotifyMessage' => '', // The notice message's body
		
		// Extensions will add to this as needed
	);
	
	
	
	// Get Settings for Specified List
    public function eeSFL_GetSettings($eeListID) {
	    
	    if(!is_numeric($eeListID)) { $eeListID = 1; }

	    // Getting the settings array
	    $this->eeListSettings = get_option('eeSFL_Settings_' . $eeListID);
	    
	    if(!is_array($this->eeListSettings)) {
			$this->eeLog['warnings'][] = 'No Settings Found. Restoring the defaults ...';
			update_option('eeSFL_Settings_' . $eeListID, $this->eeDefaultListSettings); // The settings are gone, so reset to defaults.
			$this->eeListSettings = $this->eeDefaultListSettings;
		}
		
		// Add the Full URL
	    $this->eeListSettings['FileListURL'] = get_option('siteurl') . '/' . $this->eeListSettings['FileListDir']; 
			
		ksort($this->eeListSettings);
			
		return $this->eeListSettings;
	}
	

	// FILES ----------------
	
	// All Files and Folders for a Given List (Big)
	public $eeAllFiles = array(); 
	
	// Files and Folders to Display (Small)
	public $eeDisplayFiles = array();
	
	// Original and Sanitized Names
	public $eeSanitizedFiles = array(); 
	
	// The path defined within the shortcode
	public $eeShortcodeFolder = FALSE; 
	
	// The Current Sub-Folder We are Within
	public $eeCurrentFolder = FALSE; // FALSE = Home Folder. String = Relative to FileListDir
	
	// The Current File to Consider
	public $eeFileArray = array();
	public $eeIsFile = FALSE;
	public $eeIsFolder = FALSE;
	public $eeFilePath = FALSE;
	public $eeFileURL = FALSE;
	public $eeFileThumbURL = FALSE;
	public $eeFileName = FALSE;
	public $eeFileExt = FALSE;
	public $eeFileMIME = FALSE;
	public $eeRealFileName = FALSE;
	public $eeFileNiceName = FALSE;
	public $eeFileDescription = FALSE;
	public $eeFileDateAdded = FALSE;
	public $eeFileDateChanged = FALSE;
	public $eeFileDate = FALSE;
	public $eeFileSize = FALSE;
	public $eeFileOwner = FALSE;
	public $eeFileSubmitterEmail = FALSE;
	public $eeFileSubmitterName = FALSE;
	public $eeFileSubmitterComments = FALSE;
	
	public $eeAccessUsers = array(); // SFLA
	public $eeAccessRole = FALSE;
	
	// Total Counts
	public $eeFileCount = 0;
	public $eeFolderCount = 0;
	public $eeItemCount = 0;
	    
    
    // File Array Template
    public $eeFileTemplate = array(
	    
		0 => array( // The File ID (We copy this to the array on-the-fly when sorting)
			'FilePath' => '', // Path to file, relative to the FileListDir
		    'FileExt' => '', // The file extension
		    'FileMIME' => '', // The file's MIME type
			'FileSize' => 0, // The size of the file
			'FileDateAdded' => '', // Date the file was added to the list
			'FileDateChanged' => '', // Last date the file was renamed or otherwise changed
			'FileDescription' => '', // A short description of the file
			'FileNiceName' => '', // A name that will replace the actual file name
			'FileOwner' => '', // The logged-in user who added the file
			'SubmitterName' => '', // The full name of who added the file
			'SubmitterEmail' => '', // Their email
			'SubmitterComments' => '', // What they said
			
			'AccessUsers' => array(), // SFLA - Array of User IDs
			'AccessRole' => '' // SFLA - Role, ID (Min) or String Name (Match)
		)
    );
	
	
	
	// Count All Files and Folders
	public function eeSFL_CountFiles($eeFolder = FALSE) {
		
		$this->eeFileCount = 0;
		
		if( is_array($this->eeAllFiles) ) {
			
			foreach( $this->eeAllFiles as $eeKey => $eeFileArray ) {
				
				if( !strpos($eeFileArray['FilePath'], '/') AND $eeFileArray['FileExt'] != 'folder') { 
					$this->eeFileCount++;
				}
			}
			// $this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Counted ' . $this->eeFileCount . ' Total Files';
		}
	}
	
	
	
	// File Editor Engine
	public function eeSFL_ItemEditor() {
		
		global $eeSFL_Environment, $eeSFL_Thumbs, $eeSFL_Pro;
		
		$eeFileNameNew = FALSE;
		$eeFileNiceNameNew = FALSE;
		$eeFileDescriptionNew = FALSE;
		$eeFileAction = FALSE;
		$eeSubFolder = FALSE;
		$eeAdditionalData = FALSE; // Used to send data back to JS
		$eeMessages = array();
		$eeIsFile = '';
		$eeSlash = '';
		
		// WP Security
		if( !check_ajax_referer( eeSFL_Nonce, 'eeSecurity' ) ) { return 'WP Nonce Failure: ' . basename(__FILE__); }
		
		// The List ID
		if( $_POST['eeSFL_ID'] ) { $this->eeListID = filter_var($_POST['eeSFL_ID'], FILTER_VALIDATE_INT); } else { return "Missing ID"; }
		
		if($_POST['eeFileMimeType'] == 'directory') { $eeSlash = '/'; }
		
		$this->eeSFL_GetSettings($this->eeListID); // Populate the right settings
		
		$eeReferer = wp_get_referer(); // Front-side protections
		if( !strpos($eeReferer, '/wp-admin/') ) { $eeIsAdmin = TRUE; } else { $eeIsAdmin = FALSE; }
		
		// Front-End Access
		if(!$eeIsAdmin AND $eeSFLA) { $this->eeListSettings['AllowFrontManage'] = $eeSFLA->eeSFLA_ManangementFirewall(); }
		
		// Check if we should be doing this
		if($eeIsAdmin OR $this->eeListSettings['AllowFrontManage'] == 'YES') {
			
			$this->eeAllFiles = get_option('eeSFL_FileList_' . $this->eeListID); // Get the full list
			
			// The Action
			if( strlen($_POST['eeFileAction']) ) { $eeFileAction = sanitize_text_field($_POST['eeFileAction']); } 
			if( !$eeFileAction ) { return "Missing the Action"; }
			
			// The Current File Name
			if( strlen($_POST['eeFileName']) ) { $eeFileName = esc_textarea(sanitize_text_field($_POST['eeFileName'])); }
			
			if(!$eeFileName) { return "Missing the File Name"; }
			
			// Ignore these file types
			$eeParts = pathinfo($eeFileName);
			if(in_array($eeParts['extension'], $this->eeForbiddenTypes)) {
				return "Forbidden File Type";
			}
			
			// Are we in a Folder?
			if( $_POST['eeSubFolder'] ) { 
				$eeSubFolder = sanitize_text_field( urldecode( $_POST['eeSubFolder'] )); 
			}
			if(!$eeSubFolder OR $eeSubFolder == '/') { $eeSubFolder = FALSE; }
			
			// Delete the File
			if($eeFileAction == 'Delete') {
				
				$eeMessages[] = 'Deleting File';
				
				$this->eeSFL_DetectUpwardTraversal($this->eeListSettings['FileListDir'] . $eeFileName); // Die if foolishness
						
				// Remove the item from the array
				
				$eeFilePath = ABSPATH . $this->eeListSettings['FileListDir'] . $eeSubFolder . $eeFileName;
				
				$eeMessages[] = $this->eeListSettings['FileListDir'] . $eeFileName;
				
				if( is_file($eeFilePath) ) { // Gotta be a File - Looking for the dot rather than using is_file() for better speed
					
					if(unlink($eeFilePath)) {
						
						foreach( $this->eeAllFiles as $eeKey => $eeThisFileArray) {
							if($eeThisFileArray['FilePath'] == $eeSubFolder . $eeFileName) {
								unset($this->eeAllFiles[$eeKey]);
								break;
							}
						}
						
						// Reduce the parent folder's item count
						foreach( $this->eeAllFiles as $eeKey => $eeThisFileArray) {
							if($eeThisFileArray['FilePath'] == $eeSubFolder) {
								$this->eeAllFiles[$eeKey]['ItemCount'] = $eeThisFileArray['ItemCount'] - 1;
								break;
							}
						}
						
						update_option('eeSFL_FileList_' . $this->eeListID, $this->eeAllFiles);
						
						$eeSFL_Thumbs->eeSFL_UpdateThumbnail($eeSubFolder . $eeFileName, FALSE, $this->eeListID); // Delete the thumb
						
						// Add Custom Hook
						$eeMessages[] = 'File Deleted';
						do_action('eeSFL_Hook_Deleted', $eeMessages);
						
						return 'SUCCESS';
						
					} else {
						return __('File Delete Failed', 'ee-simple-file-list') . ':' . $eeFileName;
					}
				
				} elseif( is_dir($eeFilePath) ) {
					
					// Delete Folder
					if( $eeSFL_Pro->eeSFL_DeleteFolder($eeFilePath) ) {
						
						// Remove from the array
						$eeFilePath = $eeSubFolder . $eeFileName . '/';
						
						foreach( $this->eeAllFiles as $eeKey => $eeThisFileArray ) {
							if( strpos($eeThisFileArray['FilePath'], $eeFilePath) === 0 ) { // Look for each one with this leading path
								unset($this->eeAllFiles[$eeKey]);
							}
						}
						
						// Update the counts and sizes in the array.
						$eeSFL_Pro->eeSFL_UpdateFolderSizesAndCounts();
					
						update_option('eeSFL_FileList_' . $this->eeListID, $this->eeAllFiles);
						
						// Add Custom Hook
						$eeMessages[] = 'Folder Deleted';
						do_action('eeSFL_Hook_Deleted', $eeMessages);
					
						return 'SUCCESS';
						
					} else {
						
						return __('Folder Delete Failed', 'ee-simple-file-list') . ':' . $eeFilePath;
					}
	
				} else {
					
					return __('Unknown Item', 'ee-simple-file-list') . ':' . $eeFilePath;
				}	
			
			} elseif($eeFileAction == 'Edit') { // ------------------------------------------------ EDIT
				
				$eeMessages[] = 'Editing File';
				$eeMessages[] = $this->eeListSettings['FileListDir'] . $eeFileName;
				
				
				// The Nice Name - Might be empty
				if($_POST['eeFileNiceNameNew'] != 'false') {
					$eeFileNiceNameNew = trim(esc_textarea(sanitize_text_field($_POST['eeFileNiceNameNew'])));
					
					if(strlen($eeFileNiceNameNew) < 1) { $eeFileNiceNameNew = ''; } 
					$this->eeSFL_UpdateFileDetail($eeSubFolder . $eeFileName . $eeSlash, 'FileNiceName', $eeFileNiceNameNew);
					
					$eeMessages[] = 'Nice Name: ' . $eeFileNiceNameNew;
				}
				
				
				
				// The Description - Might be empty
				if($_POST['eeFileDescNew'] != 'false') {
				
					$eeFileDescriptionNew = trim(esc_textarea(sanitize_text_field($_POST['eeFileDescNew'])));
					
					if(strlen($eeFileDescriptionNew) < 1) { $eeFileDescriptionNew = ''; }
					// if(!strpos($eeFileName, '.')) { $eeFileName .= '/'; } // Need to Add this Because It's a Folder
					
					$this->eeSFL_UpdateFileDetail($eeSubFolder . $eeFileName . $eeSlash, 'FileDescription', $eeFileDescriptionNew);
					
					$eeMessages[] = 'Description: ' . $eeFileDescriptionNew;
				}
				
				
				// Edit the Date Added
				$eeDate = preg_replace("/[^0-9-]/", "", $_POST['eeFileDateAdded']);
				
				if(strlen($eeDate) > 1) {
				
					// Check to be sure it's a good date 
					$eeArray = explode('-', $eeDate);
					if( !checkdate( $eeArray[1], $eeArray[2], $eeArray[0]) ) {
						
						return 'Bad Date, Indiana! ' . $eeArray[1] . ', ' . $eeArray[2] . ', ' . $eeArray[0];
					
					} else {
						
						// Update the Database
						$this->eeSFL_UpdateFileDetail($eeSubFolder. $eeFileName, 'FileDateAdded', $eeDate . ' 00:00:00' );
						
						if($this->eeListSettings['ShowFileDateAs'] == 'Added') { $eeAdditionalData = '|Date=' . date_i18n( get_option('date_format'), strtotime( $eeDate ) );}
					}
				}
				
				
				// Edit the Date Changed
				$eeDate = preg_replace("/[^0-9-]/", "", $_POST['eeFileDateChanged']);
				
				if(strlen($eeDate) > 1) {
				
					// Check to be sure it's a good date 
					$eeArray = explode('-', $eeDate);
					if( !checkdate( $eeArray[1], $eeArray[2], $eeArray[0]) ) {
						return 'Bad Date, Indiana! ' . $eeArray[1] . ', ' . $eeArray[2] . ', ' . $eeArray[0];
					}
					
					// Update the File
					$eeDateTime = strtotime($eeDate);
					$eeFilePath = ABSPATH . $this->eeListSettings['FileListDir'] . $eeSubFolder . $eeFileName;
					if(is_readable($eeFilePath) AND $eeDateTime) {
						
						// Touch the file
						touch($eeFilePath, $eeDateTime);
						
						// Update the Database
						$this->eeSFL_UpdateFileDetail($eeSubFolder. $eeFileName, 'FileDateChanged', $eeDate . ' 00:00:00' );
						
						if($this->eeListSettings['ShowFileDateAs'] == 'Changed') { $eeAdditionalData = '|Date=' . date_i18n( get_option('date_format'), strtotime( $eeDate ) );}
					}
				}
			
				// New File Name? - Rename Last
				if( strlen($_POST['eeFileNameNew']) >= 1 ) { 
					
					$eeFileNameNew = sanitize_text_field($_POST['eeFileNameNew']);
					$eeFileNameNew = urldecode( $eeFileNameNew );
					$eeFileNameNew = $eeSFL_Environment->eeSFL_SanitizeFileName($eeFileNameNew);
					
					if( strlen($eeFileNameNew) >= 1 ) {
					
						if(strpos($eeFileName, '.') === FALSE) { // Folder
							$eeIsFile = FALSE;
							$eeFileNameNew = str_replace('.', '_', $eeFileNameNew); // Prevent adding an extension
						} else {
							$eeIsFile = TRUE;
							$eePathParts = pathinfo($eeFileName);
							$eeOldExtension = strtolower($eePathParts['extension']); // Prevent changing file extension
							$eePathParts = pathinfo($eeFileNameNew);
							$eeNewExtension = strtolower($eePathParts['extension']);
							if($eeOldExtension != $eeNewExtension) { 
								return __('Changing the File Extension is Not Allowed', 'ee-simple-file-list');
							}	
						}
					
						// Die if foolishness
						$this->eeSFL_DetectUpwardTraversal($this->eeListSettings['FileListDir'] . $eeSubFolder . $eeFileNameNew ); 
						
						// Build Full Paths
						$eeFilePathNew = ABSPATH . $this->eeListSettings['FileListDir'] . $eeSubFolder . $eeFileNameNew;
						$eeFilePathOld = ABSPATH . $this->eeListSettings['FileListDir'] . $eeSubFolder . $eeFileName;
						if($eeIsFile === FALSE) { $eeFilePathOld .= '/'; } // Folder
						
						// Be Sure that the Source File is Found
						if(!file_exists($eeFilePathOld)) {
							return __('Source File Not Found', 'ee-simple-file-list') . ': ' . $eeFilePathOld;
						}
						
						// Check for Existing Destination Item with Same Name
						if(file_exists($eeFilePathNew)) {
							return __('Cannot Change the Name. Item with Same Name Found.', 'ee-simple-file-list');
						}
						
						// Rename File On Disk
						if( !rename($eeFilePathOld, $eeFilePathNew) ) {
							return __('The Rename Function Failed', 'ee-simple-file-list');
						
						} else {
							
							$this->eeSFL_UpdateFileDetail($eeSubFolder. $eeFileName, 'FilePath', $eeSubFolder . $eeFileNameNew);
							
						}
						
						$eeMessages[] = 'Renamed to';
						$eeMessages[] = $this->eeListSettings['FileListDir'] . $eeFileNameNew;
					
					} else {
						return __('Invalid New File Name', 'ee-simple-file-list');
					}
				}
		
				// $this->eeSFL_WriteLogData();
				
				// Custom Hook
				do_action('eeSFL_Hook_Edited', $eeMessages);
				
				return 'SUCCESS' . $eeAdditionalData;
				
			} else { // End Editing
				
				return; // Nothing to do	
			}
		}
		
		// We should not be doing this
		return;
	}
	
	
	
	// Scan the real files and create or update array as needed.
	public function eeSFL_UpdateFileListArray() {
		
		global $eeSFL_Environment, $eeSFL_Upload, $eeSFL_Thumbs, $eeSFL_Pro, $eeSFL_Tasks, $eeSFLA;
		
		$this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Re-Indexing the File List ...';
		
		if(empty($this->eeListSettings)) {
			$this->eeListSettings = get_option('eeSFL_Settings_' . $this->eeListID);
		}
		
		// Double-check the Disk Directory
		if( !$eeSFL_Environment->eeSFL_FileListDirCheck($this->eeListSettings['FileListDir']) ) { return FALSE; }
		
		// Check where ZIPs to be downloaded are kept temporarily
		if(defined('eeSFL_Pro')) { $this->eeLog['notice'][] = eeSFL_TempDirCheck(); }
		
		// Get the File List Array
		$this->eeAllFiles = get_option('eeSFL_FileList_' . $this->eeListID);
		if(!is_array($this->eeAllFiles)) { $this->eeAllFiles = array(); }
		
		// List the actual files on the disk and fill $this->eeFileScanArray
		$eeSFL_Environment->eeSFL_ScanAndSanitize();
		
		if(!count($this->eeFileScanArray)) {
			$this->eeLog['notice'][] = $this->eeSFL_NOW() . 'No Files Found';
			return FALSE;	    
		}
		
		// No List in the DB, Creating New...
		if( !count($this->eeAllFiles) ) { 
			
			$this->eeLog['notice'][] = $this->eeSFL_NOW() . 'No List Found! Creating from scratch...';
			
			if(count($this->eeFileScanArray)) {
				
				$eeHasFolders = FALSE;
				
				foreach( $this->eeFileScanArray as $eeKey => $eeFilePath) {
					
					// Add the new item
					$eeNewArray = $this->eeSFL_BuildFileArray($eeFilePath); // Path relative to FileListDir
					
					if( isset($eeNewArray['FilePath']) ) {
						
						if( isset($this->eeSanitizedFiles[$eeFilePath]) AND $this->eeListSettings['PreserveName'] == 'YES' ) {
							$eeNewArray['FileNiceName'] = basename($this->eeSanitizedFiles[$eeFilePath]);
						}
						
						$this->eeAllFiles[] = $eeNewArray;
					}
				}
			}
			
			$this->eeLog['notice'][] = $this->eeSFL_NOW() . '' .  count($this->eeFileScanArray) . ' Items Added';

		
		} else { // Update file info
			
			// Check to be sure each file is there...
			foreach( $this->eeAllFiles as $eeKey => $eeFileSet) {
				
				if( isset($eeFileSet['FilePath']) ) {
				
					// Build full path
					$eeFile = ABSPATH . $this->eeListSettings['FileListDir'] . $eeFileSet['FilePath'];
					
					if( is_file($eeFile) ) { // Update file size
						
						// Update file size
						$this->eeAllFiles[$eeKey]['FileSize'] = filesize($eeFile);
						
					} elseif( $eeSFL_Pro AND is_dir($eeFile) ) {
							
						if($this->eeListSettings['ShowFolderSize'] == 'YES') { // How Big?
							$this->eeAllFiles[$eeKey]['FileSize'] = $eeSFL_Pro->eeSFL_GetFolderSize( $this->eeListSettings['FileListDir'] . $eeFileSet['FilePath'] );
						}
						
						$eeFile .= '/'; // Need trailing dot to get actual folder mod time.
					
					} else { // Get rid of it
						
						$this->eeLog['notice'][] = $this->eeSFL_NOW() . 'File Not Found: ' . basename($eeFile);
						
						unset($this->eeAllFiles[$eeKey]);
						
						// Custom Hook
						array_unshift($eeFileSet, 'File Not Found');
						do_action('eeSFL_Hook_Removed', $eeFileSet);
						
						continue;
					}
					
					// MIME Type
					$this->eeAllFiles[$eeKey]['FileMIME'] = mime_content_type($eeFile); // MIME Type
						
					// Update modification date
					$this->eeAllFiles[$eeKey]['FileDateChanged'] = date("Y-m-d H:i:s", filemtime($eeFile));
					
					// Merge-in Default File Attributes
					$this->eeAllFiles[$eeKey] = array_merge($this->eeFileTemplate[0], $this->eeAllFiles[$eeKey]);
				
				} else {
					unset($this->eeAllFiles[$eeKey]); // If no FilePath, get rid of it.
				}
			}
			
			
			if(count($this->eeFileScanArray)) {
				
				// Check if any new files have been added
				foreach( $this->eeFileScanArray as $eeKey => $eeFile ) {
					
					$eeFound = FALSE;
					
					// Look for this file in our array
					foreach( $this->eeAllFiles as $eeKey2 => $eeFileArray ) {
						
						if($eeFile == $eeFileArray['FilePath']) { $eeFound = TRUE; break; } // Found this file, on to the next.
					}
					
					if($eeFound === FALSE) { // New Item Found
						
						$this->eeLog['notice'][] = $this->eeSFL_NOW() . 'New Item Found: ' . $eeFile;
						
						// Build a new file array
						$eeNewArray = $this->eeSFL_BuildFileArray($eeFile); // Path relative to FileListDir
						
						if( isset($eeNewArray['FilePath']) ) {
							
							if( isset($this->eeSanitizedFiles[$eeFile]) ) {
								$eeNewArray['FileNiceName'] = basename($this->eeSanitizedFiles[$eeFile]);
							}
							
							$this->eeAllFiles[] = $eeNewArray;
							
							// Custom Hook
							array_unshift($eeNewArray, 'New File Found');
							do_action('eeSFL_Hook_Added', $eeNewArray);
						}
					}
				}
			}
		} // END Update file Info
		
		
		// Finish Up
		if(count($this->eeAllFiles)) {
			
			// Sort - Passing a reference to the file array
			$this->eeSFL_SortFiles($this->eeListSettings['SortBy'], $this->eeListSettings['SortOrder']);
			
			// Folder Sizes and Counts
			if(defined('eeSFL_Pro')) { $eeSFL_Pro->eeSFL_UpdateFolderSizesAndCounts(); }
			
			// Remove Duplicates
			$this->eeAllFiles = array_map("unserialize", array_unique(array_map("serialize", $this->eeAllFiles)));
			
			// Remove empty array keys to reduce array size
			foreach( $this->eeAllFiles as $eeFileID => $eeArray) {
				
				foreach( $eeArray as $eeName => $eeValue) {
					
					if( empty($eeValue) AND $eeValue !== 0 ) {
						unset( $this->eeAllFiles[$eeFileID][$eeName] );
					}
				}
			}
			
			// Re-Check Server Capabilities
			$eeSFL_Environment->eeSFL_CheckSupported(); 
			
			// Check Thumbnails ...
			if(defined('eeSFL_Pro')) { // PRO Conditionals
				
				$eeSFL_Pro->eeSFL_CheckThumbnailsConditions();
				
			} else { // Check Now
				
				foreach( $this->eeAllFiles as $eeKey => $eeFile ) {
				
					if(is_string($eeFile['FilePath'])) {
						$eeSFL_Thumbs->eeSFL_CheckThumbnail($eeFile['FilePath'], $this->eeListSettings);
					}
				}
			}
			
			// Update the DB
			update_option('eeSFL_FileList_' . $this->eeListID, $this->eeAllFiles);
			
			if($eeSFLA) { eeSFLA_FileViewerCheck(); }
			
			// Add Custom Hook
			$eeMessages[] = 'Disk Scan Completed';
			$eeMessages[] = $this->eeAllFiles;
			do_action('eeSFL_Hook_Scanned', $eeMessages);
			
			$this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Re-Index Completed';
			
			return TRUE;
		
		} else {
			
			$this->eeAllFiles = array('' => ''); // No files found :-(
			
			return FALSE;
		}
	}
	
	

    
    
    public $eeArchiveFileTypes= array('zip'); // Types we can unzip
    
    public function eeSFL_ReturnFileActions($eeFileID, $eeFileArray) {
			
		global $eeSFL_Pro, $eeSFLA, $eeSFLE, $eeSFL_Thumbs;
		
		$eeOutput = '
		
		<small class="eeSFL_ListFileActions">';
			
		// Open Action
		if(is_admin() OR $this->eeListSettings['ShowFileOpen'] == 'YES') {
		
			if(in_array($this->eeFileExt, $eeSFL_Thumbs->eeOpenableFileFormats)) {
				
				$eeOutput .= '
				<a class="eeSFL_FileOpen" href="' . $this->eeFileURL . '" ';
				
				if($this->eeIsFile) { $eeOutput .= 'target="_blank"'; }
				
				$eeOutput .= '>' . __('Open', 'ee-simple-file-list') . '</a>';
			}
		}
		
		if($this->eeIsFile) {
			
			// File Download
			if(is_admin() OR $this->eeListSettings['ShowFileDownload'] == 'YES') {
			
				$eeOutput .= '
				<a class="eeSFL_FileDownload" href="' . $this->eeFileURL;
				
				// Extension Check
				if($eeSFLA) {  // File Access Manager
					if($this->eeListSettings['Mode'] != 'NORMAL') { $eeOutput .= '&mode=download"'; } 
						else { $eeOutput .= '" download="' . basename($this->eeFileURL) . '"'; } // Basic Download link
				} else { 
					$eeOutput .= '" download="' . basename($this->eeFileURL) . '"'; // Basic Download link
				}
				$eeOutput .= '>' . __('Download', 'ee-simple-file-list') . '</a>';
				
			}
			
			// Copy Link Action
			if(is_admin() OR $this->eeListSettings['ShowFileCopyLink'] == 'YES') {
				
				$eeOutput .= '
				<a href="#" class="eeSFL_Action eeSFL_CopyLinkToClipboard" data-action="copy-link" data-id="' . $eeFileID . '">' . __('Copy Link', 'ee-simple-file-list') . '</a>';														
														
			}
		}
		
		// Front-End Manage or Admin
		if( (is_admin() OR $this->eeListSettings['AllowFrontManage'] == 'YES') AND $this->eeListRun == 1) {							
			$eeOutput .= '
			<a href="#" class="eeSFL_Action" data-action="edit" data-id="' . $eeFileID . '">' . __('Edit', 'ee-simple-file-list') . '</a>
			<a href="#" class="eeSFL_Action" data-action="delete" data-id="' . $eeFileID . '">' . __('Delete', 'ee-simple-file-list') . '</a>';	
		}
		
		if(defined('eeSFL_Pro')) {
			$eeSFL_Include = wp_create_nonce(eeSFL_Include);
			require(eeSFL_PluginDir . 'pro/ee-file-actions.php');
		}
		
		// File Details to Pass to the Editor
		$eeArray = explode(' ', $this->eeFileDateAdded);
		$eeDateAdded = $eeArray[0];
		$eeArray = explode(' ', $this->eeFileDateChanged);
		$eeDateChanged = $eeArray[0];
				
		$eeOutput .= '
		
		<span class="eeHide eeSFL_FileSize">' . $this->eeFileSize . '</span>
		<span class="eeHide eeSFL_FileDateAdded">' . $eeDateAdded . '</span>
		<span class="eeHide eeSFL_FileDateAddedNice">' . date_i18n( get_option('date_format'), strtotime( $eeDateAdded ) ) . '</span>
		<span class="eeHide eeSFL_FileDateChanged">' . $eeDateChanged . '</span>
		<span class="eeHide eeSFL_FileDateChangedNice">' . date_i18n( get_option('date_format'), strtotime( $eeDateChanged ) ) . '</span>
		
		</small>'; // Close File List Actions Links
		
		return $eeOutput;
	    
    }
    
	
	
	
	
	
	// Update the details of an item - Accepts a referenced array or list ID
	public function eeSFL_UpdateFileDetail($eeFilePath, $eeDetail, $eeNewInfo) {
		
		global $eeSFL_Pro, $eeSFL_Thumbs;
		
		if($eeDetail == 'FilePath') {
		
			$eeUpdateChildren = FALSE; // Update sub-items or not
			$isFolder = substr($eeFilePath, -1) == '/' ? TRUE : FALSE;
			if($isFolder && substr($eeFilePath, -1) != '/') { $eeFilePath .= '/'; }
			if($isFolder) { $eeUpdateChildren = TRUE; }
			
			foreach($this->eeAllFiles as $eeKey => $eeFileArray) {
				
				// Update the specified detail for the exact path match
				if($eeFileArray['FilePath'] == $eeFilePath) {
					$this->eeAllFiles[$eeKey][$eeDetail] = $eeNewInfo;
				}
		
				// If folder, update all children paths
				if($eeUpdateChildren && strpos($eeFileArray['FilePath'], $eeFilePath) === 0) {
					$eeFilePathNew = str_replace($eeFilePath, $eeNewInfo, $eeFileArray['FilePath']);
					$this->eeAllFiles[$eeKey]['FilePath'] = $eeFilePathNew;
					$eeSFL_Thumbs->eeSFL_UpdateThumbnail($eeFileArray['FilePath'], $eeFilePathNew);
				}
			}
			
			$eeSFL_Thumbs->eeSFL_UpdateThumbnail($eeFilePath, $eeNewInfo);
			
		} else {
			
			foreach($this->eeAllFiles as $eeKey => $eeFileArray) {
				
				// Update the specified detail for the exact path match
				if($eeFileArray['FilePath'] == $eeFilePath) {
					$this->eeAllFiles[$eeKey][$eeDetail] = $eeNewInfo;
				}
			}
		}
	
		// Update the Database
		update_option('eeSFL_FileList_' . $this->eeListID, $this->eeAllFiles);
		
		return TRUE;
	}

    
    
    public $eeUsingCustomThumbs = FALSE;
    
    // Prepare to Display the Item in the List
    public function eeSFL_ProcessFileArray($eeFileArray, $eeHideName = FALSE, $eeHideType = FALSE) {
	    
	    global $eeSFL_Thumbs;
		
		
		// $this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Processing File Array...';
	    
	    if( is_array($eeFileArray) ) {

			// Assign values to our properties
			
			// The File Info
			$this->eeFilePath = $eeFileArray['FilePath']; // Path relative to FileListDir
			$this->eeFileName = basename($eeFileArray['FilePath']); // This name might change
			$this->eeRealFileName = $this->eeFileName; // Never changed
			$this->eeFileExt = $eeFileArray['FileExt']; // Just the name
			$this->eeFileURL = $this->eeEnvironment['wpSiteURL'] . $this->eeListSettings['FileListDir'] . $this->eeFilePath; // Clickable URL
			$this->eeFileSize = $this->eeSFL_FormatFileSize($eeFileArray['FileSize']); // Formatted Size
			$this->eeFileDateAdded = $eeFileArray['FileDateAdded'];
			$this->eeFileDateChanged = $eeFileArray['FileDateChanged'];
			if(isset($eeFileArray['FileMIME'])) {
				$this->eeFileMIME = $eeFileArray['FileMIME'];
			} else {
				$this->eeFileMIME = 'no/mime';
			}
			
			// Reset These
			$this->eeIsFile = FALSE;
			$this->eeIsFolder = FALSE;
			$this->eeFileNiceName = FALSE;
			$this->eeFileDescription = FALSE;
			$this->eeFileOwner = FALSE;
			$this->eeFileSubmitterEmail = FALSE;
			$this->eeFileSubmitterName = FALSE;
			$this->eeFileSubmitterComments = FALSE;
			$this->eeAccessUsers = FALSE;
			$this->eeAccessRole = FALSE;
			$this->eeFileThumbURL = FALSE;
			
			
			// Skip names hidden via shortcode
			if($eeHideName) { // Expecting a comma delimited string of file names
				$eeArray = explode(',', $eeHideName);
				foreach( $eeArray as $eeKey => $eeValue ) {
					if( $eeValue . '/' == $this->eeFilePath ) { return FALSE; } // Folder
					if($eeValue == $this->eeFilePath) { return FALSE; } // File
				}
			}
			
			
			// Must Be a File
			if( strpos($eeFileArray['FilePath'], '.') ) { // This is a File
				
				// $this->eeLog['Files'][] = 'File: ' . $this->eeFileName;
				
				$this->eeIsFile = TRUE;
			
				$this->eeFileCount++; // Bump the file count
				
				// Skip types hidden via shortcode
				if($eeHideType) { // Expecting a comma delimited string of extensions
					if(strpos($eeHideType, $this->eeFileExt) OR strpos($eeHideType, $this->eeFileExt) === 0 ) { 
						return FALSE;
					}
				}
				
				// Thumbnail
				$eeThumbSet = FALSE;
				$eeHasCreatedThumb = FALSE;
				if($this->eeUsingCustomThumbs) {
					$eePathParts = pathinfo($this->eeFilePath);
					$eeFileNamePart = $eePathParts['filename'] . '-thumb';
					// $this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Checking for: ' . $eeFileNamePart . '.jpg';
					if(is_readable(eeSFL_CustomThumbsDir . $eeFileNamePart . '.jpg')) {
						// $this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Found';
						$this->eeFileThumbURL = eeSFL_CustomThumbsURL . $eeFileNamePart . '.jpg';
						$eeThumbSet = TRUE;
					}
				} 
				
				if(!$this->eeFileThumbURL) {
					
					if( in_array($this->eeFileExt,  $eeSFL_Thumbs->eeDynamicImageThumbFormats) AND $this->eeListSettings['GenerateImgThumbs'] == 'YES' ) { $eeHasCreatedThumb = TRUE; }
					if( in_array($this->eeFileExt,  $eeSFL_Thumbs->eeDynamicVideoThumbFormats) AND isset($this->eeEnvironment['thumbsVIDEO']) AND $this->eeListSettings['GenerateVideoThumbs'] == 'YES' ) { $eeHasCreatedThumb = TRUE; }
					if( $this->eeFileExt == 'pdf' AND isset($this->eeEnvironment['thumbsPDF']) AND $this->eeListSettings['GeneratePDFThumbs'] == 'YES' ) { $eeHasCreatedThumb = TRUE; }
					
					if($eeHasCreatedThumb) { // Images use .jpg files
		
						$eePathParts = pathinfo($this->eeFilePath);
						
						if($eePathParts['dirname'] AND $eePathParts['dirname'] != '.') { $eeFolder = $eePathParts['dirname'] . '/'; } else { $eeFolder = ''; }
						
						$eeFileThumbPath = ABSPATH . $this->eeListSettings['FileListDir'] . $eeFolder . '.thumbnails/thumb_' . $eePathParts['filename'] . '.jpg';
						
						if( is_readable($eeFileThumbPath) ) {
							$eeFileThumbURL = $this->eeListSettings['FileListURL'];
							if($eePathParts['dirname']) { $eeFileThumbURL .= $eePathParts['dirname'] . '/'; }
							$this->eeFileThumbURL = $eeFileThumbURL . '.thumbnails/thumb_' . $eePathParts['filename'] . '.jpg';
							$eeThumbSet = TRUE;
						}
					}
				}
				
				// We supply the thumbnail based on file type
				if(!$eeThumbSet) {
						
					// Optional Custom Thumbnail
					if($this->eeUsingCustomThumbs) {
						
						// Based on File Type
						// $this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Checking for: ' . $this->eeFileExt . '.jpg';
						if(is_readable(eeSFL_CustomThumbsDir . $this->eeFileExt . '.jpg')) {
							// $this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Found';
							$this->eeFileThumbURL = eeSFL_CustomThumbsURL . $this->eeFileExt . '.jpg';
							$eeThumbSet = TRUE;
						}
						
						// Based on File Name
						$eeFileNamePart = basename($this->eeFilePath) . '-thumb';
						if(is_readable(eeSFL_CustomThumbsDir . $eeFileNamePart . '.jpg')) {
							// $this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Custom Thumbnail Found: ' . $eeFileNamePart . '.jpg';
							$this->eeFileThumbURL = eeSFL_CustomThumbsURL . $eeFileNamePart . '.jpg';
							$eeThumbSet = TRUE;
						}
					}
					
					if(!$eeThumbSet) {
						if(is_readable(eeSFL_PluginDir . 'images/thumbnails/' . $this->eeFileExt . '.svg')) {
							// $this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Using File Type Icon';
							$this->eeFileThumbURL = eeSFL_PluginURL . 'images/thumbnails/' . $this->eeFileExt . '.svg';
						} else {
							$this->eeFileThumbURL = eeSFL_PluginURL . 'images/thumbnails/!default.svg';
						}
					}
				}
				
			} elseif( defined('eeSFL_Pro') AND $eeFileArray['FileExt'] == 'folder' ) { // This is a Folder
				
				global $eeSFL_Pro;
				if($this->eeListRun > 1) { return FALSE; } // Only the first list shows folders
				$this->eeIsFolder = TRUE;
				$this->eeFileURL = $eeSFL_Pro->eeSFL_GetFolderURL($this->eeFilePath);
				if( strpos($this->eeFileURL, 'eeListID') ) { $this->eeFileURL = remove_query_arg('eeListID', $this->eeFileURL); } // Reset
				$this->eeFileURL .= '&eeListID=' . $this->eeListID; // Add true
				if( strpos($this->eeFileURL, 'ee=1') === FALSE ) { $this->eeFileURL .= '&ee=1';  } // Smooth scroll after file nav
				
				// Optional Custom Thumbnail
				if($this->eeUsingCustomThumbs) {
					$eePathParts = pathinfo($this->eeFilePath);
					$eeFileNamePart = $eePathParts['filename'] . '-thumb';
					// $this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Checking for: ' . $eeFileNamePart . '.jpg';
					if(is_readable(eeSFL_CustomThumbsDir . $eeFileNamePart . '.jpg')) {
						// $this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Found';
						$this->eeFileThumbURL = eeSFL_CustomThumbsURL . $eeFileNamePart . '.jpg';
						$eeThumbSet = TRUE;
					}
				}
				
				if(!$this->eeFileThumbURL) { $this->eeFileThumbURL = $this->eeEnvironment['pluginURL'] . 'images/thumbnails/folder.svg'; }
				$this->eeItemCount = $eeFileArray['ItemCount']; // Files and folders within
				$this->eeFolderCount++; // Bump the folder count
				
			} else { // Unknown Thing
				return FALSE;	
			}
				
			// File Nice Name
			if( isset($eeFileArray['FileNiceName']) ) {
				if( strlen($eeFileArray['FileNiceName']) >= 1 ) {
					$this->eeFileNiceName = $eeFileArray['FileNiceName'];
					$this->eeFileName = $eeFileArray['FileNiceName'];
				}
			}
				
			if($this->eeFileNiceName === FALSE) { // Strip the Extension?
				if(!is_admin() AND $this->eeListSettings['ShowFileExtension'] == 'NO' AND $this->eeIsFile) {
					$eePathParts = pathinfo($this->eeRealFileName);
					$this->eeFileName = $eePathParts['filename'];
				}
			}
				
			// File Description
			if( isset($eeFileArray['FileDescription']) ) {
				$this->eeFileDescription = $eeFileArray['FileDescription'];
			} elseif( isset($eeFileArray['SubmitterComments']) ) { 
				$this->eeFileDescription = $eeFileArray['SubmitterComments']; // Show the submitter comment if no desc
				// $this->eeFileSubmitterComments = $eeFileArray['SubmitterComments']; // Use on back-end
			}
				
			// File Dates and the Display Date
			if($this->eeListSettings['ShowFileDateAs'] == 'Changed') {
				$this->eeFileDate = date_i18n( get_option('date_format'), strtotime( $this->eeFileDateChanged ) );
			} else {
				$this->eeFileDate = date_i18n( get_option('date_format'), strtotime( $this->eeFileDateAdded ) );
			}
				
			// Submitter Info
			if(isset($eeFileArray['FileOwner'])) { // User or Public
				if( is_numeric($eeFileArray['FileOwner']) ) {
					$this->eeFileOwner = $eeFileArray['FileOwner']; // The User ID
					$wpUserData = get_userdata($this->eeFileOwner);
					if(!empty($wpUserData->user_email)) {
						$this->eeFileSubmitterEmail = $wpUserData->user_email;
						$this->eeFileSubmitterName = $wpUserData->first_name . ' ' . $wpUserData->last_name;
					}
				}
			
			} elseif( isset($eeFileArray['SubmitterName']) AND isset($eeFileArray['SubmitterEmail']) ) {
				$this->eeFileSubmitterName = $eeFileArray['SubmitterName'];
				$this->eeFileSubmitterEmail = $eeFileArray['SubmitterEmail'];
			}
			
			global $eeSFLA;
			if($eeSFLA) {
				if($this->eeListSettings['Mode'] != 'NORMAL' AND $this->eeIsFile) {
					$this->eeFileURL = $this->eeEnvironment['wpSiteURL'] . 'ee-get-file/?list=' . $this->eeListID . '&file=' . $eeFileArray['FilePath'];
				}
				if(isset($eeFileArray['AccessUsers'])) { $this->eeAccessUsers = $eeFileArray['AccessUsers']; }
				if(isset($eeFileArray['AccessRole'])) { $this->eeAccessRole = $eeFileArray['AccessRole']; }
			}
		}
		
		$eeMessages = array($eeFileArray);
		do_action('eeSFL_Hook_Listed', $eeMessages);
		
		// $this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Complete.';
	    
	    return TRUE; // Properties have been updated
	}
    
    
    
	
	// Build a New File/Folder Array (for an upload or new file found)
	public function eeSFL_BuildFileArray($eeFilePath, $eeFileArray = FALSE) { // Path relative to ABSPATH
		
		$this->eeLog['notice'][] = $this->eeSFL_NOW() . 'eeSFL_BuildFileArray() for ' . $eeFilePath;
		
		$eePathParts = pathinfo($eeFilePath);
		
		if( is_readable(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFilePath) ) {
			
			if( !is_array($eeFileArray) ) {
				$eeFileArray = $this->eeFileTemplate[0]; // Get the file array template
			}
			
			$eeFileArray['FilePath'] = $eeFilePath; // Path to file, relative to the list root
			
			if(isset($eePathParts['extension'])) { 
				$eeExt = strtolower($eePathParts['extension']);
			} else { 
				$eeExt = 'folder';
				$eeFileArray['ItemCount'] = '0';
			}
			$eeFileArray['FileExt'] = $eeExt; // The file extension 
			
			if(function_exists('mime_content_type')) {
				$eeFileArray['FileMIME'] = mime_content_type(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFilePath); // MIME Type
			} else {
				$eeFileArray['FileMIME'] = 'no/mime';
			}
			
			$eeFileArray['FileSize'] = filesize(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFilePath);
			
			if(empty($eeFileArray['FileDateAdded'])) {
				$eeFileArray['FileDateAdded'] = date("Y-m-d H:i:s");
			}
			
			$eeFileArray['FileDateChanged'] = date("Y-m-d H:i:s", filemtime(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFilePath));
			
			if( strlen($eeFileArray['FilePath']) ) { // 02/21 - If FilePath is empty, sort doesn't work? But why would that be empty.
				return $eeFileArray;
			} else {
				$this->eeLog['warning'][] = $this->eeSFL_NOW() . '$eeFileArray[\'FilePath\'] is Empty';

			}
		} else {
			$this->eeLog['warning'][] = $this->eeSFL_NOW() . 'Item Not Readable';
			$this->eeLog['warning'][] = $this->eeSFL_NOW() . ' ' . ABSPATH . $this->eeListSettings['FileListDir'] . $eeFilePath;
			
		}
		
		return FALSE;
	}
	
	
	
	
	// Move the sort item to the array key and then sort. Preserve the key (File ID) in a new element
	public function eeSFL_SortFiles($eeSortBy, $eeSortOrder) {
		
		global $eeSFL_Pro;
		
		if(empty($this->eeAllFiles)) { return; }
		
		$this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Sorting the File Array';
		
		if($eeSortBy == 'Random') {
			return shuffle($this->eeAllFiles);
		} elseif($eeSortBy == 'Size') {
			$eeSort = 'FileSize';
		} elseif($eeSortBy == 'Added') {
			$eeSort = 'FileDateAdded';
		} elseif($eeSortBy == 'Changed') {
			$eeSort = 'FileDateChanged';
		} else {
			$eeSort = 'FilePath'; // Name
		}
		
		if($eeSortOrder == 'Descending') { $eeOrder = SORT_DESC; } else { $eeOrder = SORT_ASC; }
		$eeArray1 = array_column($this->eeAllFiles, $eeSort);
		$eeArray2 = array_column($this->eeAllFiles, 'FileExt');
		
		// Sort Multi-Dimensional Array
		if( count($eeArray1) == count($eeArray2) ) {
			array_multisort($eeArray1, $eeOrder, SORT_NATURAL|SORT_FLAG_CASE, $eeArray2, SORT_ASC, $this->eeAllFiles);
		}
		
		// Sort Folders First?
		if(defined('eeSFL_Pro')) { $eeSFL_Pro->eeSFL_SortFoldersFirst(); }
		
		$this->eeLog['notice'][] = $this->eeSFL_NOW() . 'Files Sorted: ' . $eeSortBy . ' (' . $eeSortOrder . ')';
		
		return TRUE;
	}
	
	
	
	
	
	// Sanitize and Validate Email Addresses
	public function eeSFL_SanitizeEmailString($eeAddresses) { // Can be one or more addresses, comma delineated
		
		$eeAddressSanitized = '';
		
		// Check the input string for malicious activities
		$eeAddresses2 = strip_tags($eeAddresses);
		$eeAddresses2 = preg_replace('/[^a-zA-Z0-9\-._@,+]/', '', $eeAddresses2);
		if ($eeAddresses != $eeAddresses2) { 
			$this->eeLog['errors'][] = __('Invalid Input', 'ee-simple-file-list');
			return ''; // Funny business going on
		}
		
		// Always work as if there are multiple addresses
		$eeSFL_Addresses = explode(',', $eeAddresses);
		$eeSFL_AddressesString = '';
		
		foreach ($eeSFL_Addresses as $add) {
			
			$add = sanitize_email(trim($add));
			
			if (filter_var($add, FILTER_VALIDATE_EMAIL)) {
				$eeSFL_AddressesString .= $add . ',';
			} else {
				$this->eeLog['errors'][] = __('This is not a valid email address.', 'ee-simple-file-list');
			}
		}
		
		// Remove last comma
		$eeAddressSanitized = rtrim($eeSFL_AddressesString, ',');
		
		return $eeAddressSanitized;
	}
	
	
	
	
	// Return the general size of a file in a nice format.
	public function eeSFL_FormatFileSize($eeFileSizeBytes) {  
	    
	    $bytes = $eeFileSizeBytes;
	    $kilobyte = 1024;
	    $megabyte = $kilobyte * 1024;
	    $gigabyte = $megabyte * 1024;
	    $terabyte = $gigabyte * 1024;
	    $precision = 2;
	   
	    if (($bytes >= 0) && ($bytes < $kilobyte)) {
	        return $bytes . ' B';
	 
	    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
	        return round($bytes / $kilobyte, $precision) . ' KB';
	 
	    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
	        return round($bytes / $megabyte, $precision) . ' MB';
	 
	    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
	        return round($bytes / $gigabyte, $precision) . ' GB';
	 
	    } elseif ($bytes >= $terabyte) {
	        return round($bytes / $terabyte, $precision) . ' TB';
	    } else {
	        return $bytes . ' B';
	    }
	}
	
	
	
	
	// Detect upward path traversal
	function eeSFL_DetectUpwardTraversal($eeInputFilePath) { // Relative to ABSPATH
		
		// Testing Shenanigans
		// $eeInputFilePath .= '/../testing/';
		// $eeInputFilePath .= '/%2E%2E/%2E%2E/testing/';
		// $eeInputFilePath .= '/%252E%252E/%252E%252E/testing/';
		// $eeInputFilePath .= '/.\%2E/.\%2E/testing/';
		// $eeInputFilePath .= '/\u002E\u002E/\u002E\u002E/testing/';
		
		if($eeInputFilePath) {
			
			// Decode URL-encoded characters
			$eeWorkingPath = urldecode($eeInputFilePath);
		
			// Convert all directory separators to '/'
			$eeWorkingPath = str_replace('\\', '/', $eeWorkingPath);
		
			// Normalize the path: replace double or multiple slashes with a single slash
			$eeWorkingPath = preg_replace('~/+~', '/', $eeWorkingPath);
		
			if (strpos($eeWorkingPath, '..') === FALSE) { // No '..' found, continue checks
				
				// Construct the full path and resolve to a real path
				$eeWorkingPath = ABSPATH . trim(dirname($eeWorkingPath), '/');
				$eeRealPath = realpath($eeWorkingPath);
			
				// Ensure paths are valid
				if($eeRealPath !== FALSE) {
					
					$eeRealPath = str_replace('\\', '/', $eeRealPath); // Normalize the real path
					
					// Check if the real path starts with the intended base directory (ABSPATH)
					if (strpos($eeRealPath, str_replace('\\', '/', ABSPATH)) === 0) {
						
						// If all checks passed, no traversal detected
						$this->eeLog['notice'][] = 'Traversal check passed.';
						return TRUE;
					
					} else {
						$this->eeLog['errors'][] = 'This path fails to begin with ABSPATH.';
					}
				} else {
					$this->eeLog['errors'][] = 'The Real Path is Does Not Exist.';
				}
			} else {
				$this->eeLog['errors'][] = 'Potential directory traversal detected.';
			}
		} else {
			$this->eeLog['errors'][] = 'The input file path is empty.';
		}
		
		// FAILURE
		$this->eeLog['errors'][] = 'Directory Traversal Check Failure';
		$this->eeLog['errors'][] = 'InputPath: ' . $eeInputFilePath;
		$this->eeLog['errors'][] = 'WorkingPath: ' . $eeWorkingPath;
		$this->eeLog['errors'][] = 'RealPath: ' . $eeRealPath;
		$eeMessage = __('Directory Traversal Check Failure', 'ee-simple-file-list') . 
			PHP_EOL . PHP_EOL . print_r($this->eeLog['errors'], TRUE);
			
		$this->eeSFL_WriteLogData();
		
		wp_die($eeMessage);
	}

	
	
	
	
	// Get the current URL
	public function eeSFL_GetThisURL($eeIncludeQuery = TRUE) {
		
		// Find what is contained in the address bar?
		// Example: https://mywebsite.com/wordpress/wp-admin/admin.php?page=ee-simple-file-list-pro&eeFolder=WTEA_Curriculum&eeListID=1&ee=1
		
		$eeProtocol = ''; $eeHost = ''; $eePage = ''; $eeArguments = '';
		
		// If HTTP_HOST is empty, use site_url()
		if( empty($_SERVER['HTTP_HOST']) ) {
			
			$eeHost = site_url(); // This will contain the path to the WP core files, including protocol and trailing slash
		
		} else {
			
			$eeProtocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://"; // Protocol
			$eeHost = $_SERVER['HTTP_HOST']; // Host
			$eeHost = $eeProtocol . $eeHost;
		}
		
		// Reassemble the URL
		$eeURL = $eeHost . $eePage;
		
		if(strpos($_SERVER['REQUEST_URI'], '?') >= 1 ) { // Sub-Page with arguments
		
			$eeRequest = explode('?', $_SERVER['REQUEST_URI']);
			if(!empty($eeRequest[0])) { $eePage = $eeRequest[0]; }
			if(!empty($eeRequest[1])) { $eeArguments = $eeRequest[1]; }
			
		} elseif(strpos($_SERVER['REQUEST_URI'], '?') === 0) { // No sub-page, just arguments
			
			if(!empty($eeRequest[0])) { $eeArguments = $_SERVER['REQUEST_URI']; }
			
		} elseif( !empty($_SERVER['REQUEST_URI']) ) { // No arguments
			
			$eePage = esc_js(sanitize_text_field($_SERVER['REQUEST_URI']));
		}
		
		$eeURL = $eeHost . $eePage;
		
		
		// Sanitize and Escape Arguments
		if($eeArguments) {
			
			$eeArgumentsArray = explode('&', $eeArguments);
			
			foreach($eeArgumentsArray as $eeArg => $eeValue) {
				
				$eeArgumentsArray[$eeArg] = esc_js(sanitize_text_field(urldecode($eeValue)));
			}
			
			$eeArguments = implode('&', $eeArgumentsArray);
		}
		
		if($eeIncludeQuery === TRUE) { 
			$eeURL .= '?' . $eeArguments;
			$eeURL = remove_query_arg('eeReScan', $eeURL); // Don't want this
		}
	
		return $eeURL;
	}

		
	
	
	
	public $eeSFL_StartTime = 0;
	public $eeSFL_StartMemory = 0;
	
	// Get Elapsed Time
	public function eeSFL_NOW() {
		
		$eeTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]; // Time Right Now
		
		$eeTime = $eeTime - $this->eeSFL_StartTime; // Actual Time Elapsed
		
		$eeTime = number_format($eeTime, 3); // Format to 0.000
		
		$eeMemory = $this->eeSFL_FormatFileSize(memory_get_usage() - $this->eeSFL_StartMemory);
		
		return $eeTime . ' S | ' . $eeMemory . ' -> ';
	}	
	
	
	
	public function eeSFL_WriteLogData() {
		
		// Get the Current Log
		if(defined('eeSFL_Pro')) { // Pro Only
			
			$eeAllLogs = get_option('eeSFL_TheLog'); // Stored as an array
			if(!$eeAllLogs) { 
				$eeAllLogs = array();
			}
			
			// Log Size Management
			$eeSizeCheck = serialize($eeAllLogs);
			if(strlen($eeSizeCheck) > 131070) { // Using TEXT Limit, even tho options are LONGTEXT.
				$eeAllLogs = array(); // Clear
			}
		}
		
		// Include POST and GET
		if( count($_REQUEST) ) { $this->eeLog['REQUEST'] = $_REQUEST; }
		
		// $this->eeLog['warnings'][] = 'WARNING 1';
		// $this->eeLog['warnings'][] = 'WARNING 2';
		// $this->eeLog['errors'][] = 'ERROR 1';
		// $this->eeLog['errors'][] = 'ERROR 2';
		
		$this->eeLog[] = 'We waited for ' . $this->eeSFL_StartTime . ' seconds';
		$this->eeLog[] = 'Then ran for ' . $this->eeSFL_NOW();
		
		// Save to DB
		if(defined('eeSFL_Pro')) { // Pro Only
			$this->eeLog = array_filter($this->eeLog); // Remove empty items
			$eeAllLogs[eeSFL_Go] = $this->eeLog;
			update_option('eeSFL_TheLog', $eeAllLogs, FALSE); // Save to the database
		}
				
		// DEVELOPMENT MODE DISPLAY
		if(eeSFL_DevMode) {
			
			$eeOutput = '<hr /><pre>Runtime Log ' . print_r($this->eeLog, TRUE) . '</pre><hr />';
			$eeOutput .= '<pre>Environment ' . print_r($this->eeEnvironment, TRUE) . '</pre><hr />';
			$eeOutput .= '<pre>Settings ' . print_r($this->eeListSettings, TRUE) . '</pre><hr />';
			if(count($this->eeDisplayFiles)) { $eeOutput .= '<pre>Files ' . print_r($this->eeDisplayFiles, TRUE) . '</pre><hr />'; } // Items that were displayed
			return $eeOutput;
		}
		
		eeSFL_OptInReportGenerator();
	}

	
} // END Class 

?>