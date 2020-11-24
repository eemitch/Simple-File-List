<?php // Simple File List Script: ee-class.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Class' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_FREE_Log['SFL'][] = 'Loaded: ee-class';

class eeSFL_FREE_MainClass {
			
	// Basics
	public $eePluginName = 'Simple File List';
	public $eePluginNameSlug = 'simple-file-list';
	public $eePluginSlug = 'ee-simple-file-list';
	public $eePluginMenuTitle = 'File List';
	public $eePluginWebPage = 'https://simplefilelist.com';
	public $eeAllFilesSorted = array();
	public $eeDefaultUploadLimit = 99;
	public $eeFileThumbSize = 64;
	// public $eeSFL_FileScanArray = array(); // Temporary holder
	private $eeExpireTime = 1; // Hours until the disk is rescanned;
    
    // File Types
    public $eeDynamicImageThumbFormats = array('gif', 'jpg', 'jpeg', 'png');
    public $eeDynamicVideoThumbFormats = array('avi', 'flv', 'm4v', 'mov', 'mp4', 'wmv');
    public $eeDefaultThumbFormats = array('3gp', 'ai', 'aif', 'aiff', 'apk', 'avi', 'bmp', 'cr2', 'dmg', 'doc', 'docx', 
    	'eps', 'flv', 'gz', 'indd', 'iso', 'jpeg', 'jpg', 'm4v', 'mov', 'mp3', 'mp4', 'mpeg', 'mpg', 'pdf', 'png', 
		'pps', 'ppsx', 'ppt', 'pptx', 'psd', 'tar', 'tgz', 'tif', 'tiff', 'txt', 'wav', 'wma', 'wmv', 'xls', 'xlsx', 'zip');
	public $eeOpenableFileFormats = array('aif', 'aiff', 'avi', 'bmp', 'flv', 'jpeg', 'jpg', 'gif', 'm4v', 'mov', 'mp3', 'mp4', 'mpeg', 'mpg', 'pdf', 'png', 
		'txt', 'wav', 'wma', 'wmv', 'htm', 'html');
    
    public $eeExcludedFileNames = array('error_log', 'index.html');
    public $eeForbiddenTypes = array('php', 'exe', 'js', 'com', 'wsh', 'vbs');
    
    private $eeExcludedFiles = array('index.html');
    
    public $eeNotifyMessageDefault = 'Greetings,' . PHP_EOL . PHP_EOL . 
    	'You should know that a file has been uploaded to your website.' . PHP_EOL . PHP_EOL . 
    		
    		'[file-list]' . PHP_EOL . PHP_EOL . 
    		
    		'File List: [web-page]' . PHP_EOL . PHP_EOL;
    
    
    // The Default List Definition
    public $DefaultListSettings = array( // An array of file list settings

		// List Settings
		'ListTitle' => 'Simple File List', // List Title (Not currently used)
		'FileListDir' => 'wp-content/uploads/simple-file-list/', // List Directory Name (relative to ABSPATH)
		'ExpireTime' => 'NO', // To cache or not to cache
		'ShowList' => 'YES', // Show the File List (YES, ADMIN, USER, NO)
		'AdminRole' => 5, // Who can access settings, based on WP role (5 = Admin ... 1 = Subscriber)
		'ShowFileThumb' => 'YES', // Display the File Thumbnail Column (YES or NO)
		'ShowFileDate' => 'YES', // Display the File Date Column (YES or NO)
		'ShowFileSize' => 'YES', // Display the File Size Column (YES or NO)
		'LabelThumb' => 'Thumbnail', // Label for the thumbnail column
		'LabelName' => 'Name', // Label for the file name column
		'LabelDate' => 'Date', // Label for the file date column
		'LabelSize' => 'Size', // Label for the file size column
		'SortBy' => 'Date', // Sort By (Name, Date, Size, Random)
		'SortOrder' => 'Descending', // Descending or Ascending
		
		// Upload Settings
		'AllowUploads' => 'USER', // Allow File Uploads (YES, ADMIN, USER, NO)
		'UploadLimit' => 10, // Limit Files Per Upload Job (Quantity)
		'UploadMaxFileSize' => 1, // Maximum Size per File (MB)
		'FileFormats' => 'gif, jpg, jpeg, png, tif, pdf, wav, wmv, wma, avi, mov, mp4, m4v, mp3, zip', // Allowed Formats
		'AllowOverwrite' => 'NO', // Number new files with same name, or just overwrite.
		
		// Display Settings
		'PreserveSpaces' => 'NO', // Replace ugly hyphens with spaces
		'ShowFileDescription' => 'YES', // Display the File Description (YES or NO)
		'ShowFileActions' => 'YES', // Display the File Action Links Section (below each file name) (YES or NO)
		'ShowFileExtension' => 'YES', // Show the file extension, or not.
		'ShowHeader' => 'YES', // Show the File List's Table Header (YES or NO)
		'ShowUploadLimits' => 'YES', // Show the upload limitations text.
		'GetUploaderInfo' => 'NO', // Show the Info Form
		'ShowSubmitterInfo' => 'NO', // Show who uploaded the file (name linked to their email)
		'AllowFrontSend' => 'NO', // Allow users to email file links (YES or NO)
		'AllowFrontManage' => 'NO', // Allow front-side users to manage files (YES or NO)
		
		// Notifications
		'Notify' => 'YES', // Send Notifications (YES or NO)
		'NotifyTo' => '', // Send Notification Email Here (Defaults to WP Admin Email)
		'NotifyCc' => '', // Send Copies of Notification Emails Here
		'NotifyBcc' => '', // Send Blind Copies of Notification Emails Here
		'NotifyFrom' => '', // The sender email (reply-to) (Defaults to WP Admin Email)
		'NotifyFromName' => '', // The nice name of the sender
		'NotifySubject' => '', // The subject line
		'NotifyMessage' => '', // The notice message's body
		
	);
    
    
    // Get Environment
    public function eeSFL_GetEnv() {
	    
	    $eeSFL_Env = array();
	    
	    // Detect OS
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		    $eeSFL_Env['eeOS'] = 'WINDOWS';
		} else {
		    $eeSFL_Env['eeOS'] = 'LINUX';
		}
		
		// Detect Web Server
		if(!function_exists('apache_get_version')) {
		    $eeSFL_Env['eeWebServer'] = $_SERVER["SERVER_SOFTWARE"];
		} else {
			$eeSFL_Env['eeWebServer'] = 'Apache';
		}
		
		$eeSFL_Env['wpSiteURL'] = get_site_url() . '/'; // This Wordpress Website
		$eeSFL_Env['wpPluginsURL'] = plugins_url() . '/'; // The Wordpress Plugins Location
		
		$eeSFL_Env['pluginURL'] = plugins_url() . '/' . $this->eePluginNameSlug . '/';
		$eeSFL_Env['pluginDir'] = WP_PLUGIN_DIR . '/' . $this->eePluginNameSlug . '/';
		
		$wpUploadArray = wp_upload_dir();
		$wpUploadDir = $wpUploadArray['basedir'];
		$eeSFL_Env['wpUploadDir'] = $wpUploadDir . '/'; // The Wordpress Uploads Location
		$eeSFL_Env['wpUploadURL'] = $wpUploadArray['baseurl'] . '/';

		$eeSFL_Env['FileListDefaultDir'] = str_replace(ABSPATH, '', $eeSFL_Env['wpUploadDir'] . 'simple-file-list/'); // The default file list location
		
		$eeSFL_Env['upload_max_upload_size'] = substr(ini_get('upload_max_filesize'), 0, -1); // PHP Limit (Strip off the "M")
		$eeSFL_Env['post_max_size'] = substr(ini_get('post_max_size'), 0, -1); // PHP Limit (Strip off the "M")
		
		// Check which is smaller, upload size or post size.
		if ($eeSFL_Env['upload_max_upload_size'] <= $eeSFL_Env['post_max_size']) { 
			$eeSFL_Env['the_max_upload_size'] = $eeSFL_Env['upload_max_upload_size'];
		} else {
			$eeSFL_Env['the_max_upload_size'] = $eeSFL_Env['post_max_size'];
		}
		
		$eeSFL_Env['supported'] = get_option('eeSFL_Supported'); // Server technologies available (i.e. FFMPEG)
		
		$eeSFL_Env['wpUserID'] = get_current_user_id();
		
		return $eeSFL_Env;
    }
    
    

    // Get Settings for Specified List
    public function eeSFL_GetSettings() {
	    
	    global $eeSFL_FREE_Log, $eeSFL_FREE_Env;
		
		$eeSFL_FREE_Log['SFL'][] = 'Getting List 1 Settings...'; // One is the only number that you ever will...
	    
	    // Getting the settings array
	    $eeSettings = get_option('eeSFL_Settings_1');
	    
	    if(is_array($eeSettings)) {
			
			// Check for Change in Environment
			if( isset($eeSettings['UploadMaxFileSize']) ) {
				if($eeSFL_FREE_Env['the_max_upload_size'] < $eeSettings['UploadMaxFileSize']) {
					$eeSettings['UploadMaxFileSize'] = $eeSFL_FREE_Env['the_max_upload_size']; // If Env is lower, set to that.
				}
			} else {
				$eeSettings['UploadMaxFileSize'] = $eeSFL_FREE_Env['the_max_upload_size']; // The best we can do
			}
			
			$eeSettings['FileListURL'] = $eeSFL_FREE_Env['wpSiteURL'] . $eeSettings['FileListDir']; // The Full URL
			
			// The whole point...	
			return $eeSettings; // Associative array
		
		} else {
			
			$eeSFL_FREE_Log['SFL'][] = '!!! MISSING SETTINGS, BACK TO DEFAULT...';
			update_option('eeSFL_Settings_1', $this->DefaultListSettings); // The settings are gone, so reset to defaults.
			return $this->DefaultListSettings;
		}
	}
	    
    
    // Default File List Definition
    public $eeSFL_Files = array(
	    
		0 => array( // The File ID (We copy this to the array on-the-fly when sorting)
			'FileList' => 1, // The ID of the File List, contained in the above array.
		    'FilePath' => '', // Path to file, relative to the list root
		    'FileExt' => '', // The file extension
			'FileSize' => '', // The size of the file
			'FileDateAdded' => '', // Date the file was added to the list
			'FileDateChanged' => '', // Last date the file was renamed or otherwise changed
			'FileDescription' => '', // A short description of the file
			
			'SubmitterName' => '', // Who uploaded the file
			'SubmitterEmail' => '', // Their email
			'SubmitterComments' => '', // What they said
			
			'FileUserGroup' => '', // (Coming Later)
			'FileOwner' => '' // (Coming Later)
		)
    );
    
    
    
    public function eeSFL_UpdateFileDetail($eeFile, $eeDetail, $eeValue = FALSE) {
	    
	    if($eeValue) {
	    
		    // Get the current file array
			$eeFileArray = get_option('eeSFL_FileList_1');
			
			foreach( $eeFileArray as $eeKey => $eeThisFileArray ) {
		
				if($eeFile == $eeThisFileArray['FilePath']) { // Look for this file
					
					$eeFileArray[$eeKey][$eeDetail] = $eeValue;
				}
			}
			
			// Save the updated array
			$eeFileArray = update_option('eeSFL_FileList_1', $eeFileArray);
			
			return $eeFileArray;
		
		} else {
			return FALSE;
		}
	}

    
    
    // Scan the real files and create or update array as needed.
    public function eeSFL_UpdateFileListArray() {
	    
	    global $eeSFL_FREE_Log, $eeSFL_Settings_1, $eeSFL_FREE_Env;
	    
	    $eeSFL_FREE_Log['SFL'][] = 'Scanning File List...';
	    
	    $eeFilesArray = get_option('eeSFL_FileList_1'); // Get the File List Array
	    
	    $eeFilePathsArray = $this->eeSFL_IndexFileListDir($eeSFL_Settings_1['FileListDir']); // Get the real files
	    
	    if ( !is_array($eeFilesArray) OR !@count($eeFilesArray) ) { // Creating New
			
			$eeSFL_FREE_Log['SFL'][] = 'No List Found! Creating from scratch...';
			
			$eeFilesArray = array();
			
			foreach( $eeFilePathsArray as $eeKey => $eeFile) {
				
				// $eeFilesArray[$eeKey]['FileList'] = $eeSFL_ID;
				$eeFilesArray[$eeKey]['FilePath'] = $eeFile;
				$eePathParts = pathinfo($eeFile);
				$eeFileNameAlone = $eePathParts['filename'];
				$eeExtension = strtolower(@$eePathParts['extension']);
				$eeFilesArray[$eeKey]['FileExt'] = $eeExtension;
				$eeFilesArray[$eeKey]['FileSize'] = @filesize(ABSPATH . $eeSFL_Settings_1['FileListDir'] . $eeFile);
				$eeFilesArray[$eeKey]['FileDateAdded'] = date("Y-m-d H:i:s", filemtime(ABSPATH . $eeSFL_Settings_1['FileListDir'] . $eeFile));
				$eeFilesArray[$eeKey]['FileDateChanged'] = $eeFilesArray[$eeKey]['FileDateAdded'];
				
			}
		
		} else { // Update file info
			
			$eeSFL_FREE_Log['SFL'][] = 'Updating existing list...';
			
			if(!$eeFilesArray) { return FALSE; } // No files found
			
			$eeFileArrayNew = $eeFilesArray;
			
			// Check to be sure files are there...
			foreach( $eeFilesArray as $eeKey => $eeFileSet) {
				
				// Check if file is there
				$eeFile = ABSPATH . $eeSFL_Settings_1['FileListDir'] . $eeFileSet['FilePath'];
				
				if( is_file($eeFile) ) { // Update file particulars
					
					// Update file size
					$eeFileArrayNew[$eeKey]['FileSize'] = @filesize($eeFile);
					
					// Update modification date
					$eeFileArrayNew[$eeKey]['FileDateChanged'] = date("Y-m-d H:i:s", @filemtime($eeFile));
					
				} else { // Get rid of it
					
					$eeSFL_FREE_Log['SFL'][] = 'Removing: ' . $eeFile;
					
					unset($eeFileArrayNew[$eeKey]);
				}
				
				// If no file path
				if(strlen($eeFileSet['FilePath']) === 0) {
					unset($eeFileArrayNew[$eeKey]);
				}
			}

			
			// Check if any new files have been added
			foreach( $eeFilePathsArray as $eeKey => $eeFile) {
				
				$eeSFL_FREE_Log['SFL'][] = 'Checking...';
				
				$eeMatch = FALSE;
				
				// Look for this file in our array
				foreach( $eeFilesArray as $eeKey2 => $eeArray ) {
					
					if($eeFile == $eeArray['FilePath']) {
						
						$eeMatch = TRUE;
						
						break; // Matched this file
					}
				}
				
				if($eeMatch === FALSE) { // New File Found
					
					$eeSFL_FREE_Log['SFL'][] = 'Added: ' . $eeFile;
					
					$eePathParts = pathinfo($eeFile);
					$eeDirName = $eePathParts['dirname'];
					$eeFileName = $eePathParts['basename'];
					$eeFileNameAlone = $eePathParts['filename'];
					$eeExtension = strtolower(@$eePathParts['extension']); // Throws a notice on folders
					$eeKey .= '_new'; // Make sure the key is unique
					// $eeFileArrayNew[$eeKey]['FileList'] = $eeSFL_ID;
					$eeFileArrayNew[$eeKey]['FilePath'] = $eeFile;
					$eeFileArrayNew[$eeKey]['FileExt'] = $eeExtension;
					$eeFileArrayNew[$eeKey]['FileSize'] = 0;
					$eeFileArrayNew[$eeKey]['FileDateAdded'] = date("Y-m-d H:i:s", @filemtime(ABSPATH . $eeSFL_Settings_1['FileListDir'] . $eeFile));
					$eeFileArrayNew[$eeKey]['FileDateChanged'] = $eeFileArrayNew[$eeKey]['FileDateAdded'];
				}
			}
			
			$eeFilesArray = $eeFileArrayNew;
		}
		
		
		if( count($eeFilesArray) ) {
	    
		    $eeSFL_FREE_Log['SFL'][] = count($eeFilesArray) . ' New Files Found';
		    
		    // Reset the Keys
		    $eeFilesArray = array_values($eeFilesArray); 
		    
		    // Sort
		    $eeFilesArray = $this->eeSFL_SortFiles($eeFilesArray, $eeSFL_Settings_1['SortBy'], $eeSFL_Settings_1['SortOrder']);
		    
		    // Update the DB
		    update_option('eeSFL_FileList_1', $eeFilesArray);
		    
		    foreach($eeFilesArray as $eeKey => $eeFile) {
		    	
		    	// Check and create thumbnail if needed...
				$eeExt = $eeFile['FileExt'];
				if( in_array($eeExt, $this->eeDynamicImageThumbFormats) OR in_array($eeExt, $this->eeDynamicVideoThumbFormats) ) {
					
					$eeSFL_FREE_Log['SFL'][] = 'Checking thumbnail...';
					
					$this->eeSFL_CheckThumbnail($eeFile['FilePath']);
				}
		    }
		    
		    // Set the transient
		    if(@$eeSFL_Settings_1['ExpireTime'] >= 1) {
			
				$eeExpiresIn = $this->eeExpireTime * HOUR_IN_SECONDS;
				
				$eeSFL_FREE_Log['SFL'][] = 'Setting transient to expire in ' . $eeSFL_Settings_1['ExpireTime'] . ' hours.';
				
				set_transient('eeSFL_FileList-1', 'Good', $eeExpiresIn);
			}
		

		    // Check for FFmpeg here
			if(trim(@shell_exec('type -P ffmpeg'))) {
				update_option('eeSFL_Supported', 'ffMpeg');
			} else {
				$eeSFL_FREE_Log['SFL'][] = 'FFMPEG is not supported';
			}
			
			return $eeFilesArray;
		}
	    
	    return FALSE;
    }
	
	
	
	
	// Get All the Files
	private function eeSFL_IndexFileListDir($eeFileListDir) {
	    
	    global $eeSFL_FREE_Log;
	    
	    $eeFilesArray = array();
	    
	    if(!is_dir(ABSPATH . $eeFileListDir)) {
		    
		    $eeSFL_FREE_Log['errors'][] = 'The File List is Gone. Re-Creating...';
		    
		    eeSFL_FREE_FileListDirCheck($eeFileListDir);
		    return $eeFilesArray;
	    }
		    
	    $eeSFL_FREE_Log['SFL'][] = 'Getting files from: ' . $eeFileListDir; 
	    
	    $eeFileNameOnlyArray = scandir(ABSPATH . $eeFileListDir);
	    
	    foreach($eeFileNameOnlyArray as $eeValue) {
	    	
	    	if(strpos($eeValue, '.') !== 0 ) { // Not hidden
		    	
		    	if(is_file(ABSPATH . $eeFileListDir . $eeValue)) { // Is a regular file
		    	
			    	if(!in_array($eeValue, $this->eeExcludedFiles) )  { // Not excluded
				    	
				    	// Catch and correct spaces in items found
				    	if( strpos($eeValue, ' ') AND strpos($eeValue, ' ') !== 0 ) {
			        
					        $eeNewItem = str_replace(' ', '-', $eeValue);
					        
					        if(rename(ABSPATH . $eeFileListDir . $eeValue, ABSPATH . $eeFileListDir . $eeNewItem)) {
						        $eeValue = $eeNewItem;
					        }
					    }
				    	
				    	$eeFilesArray[] = $eeValue; // Add the path
			    	}
		    	}
	    	}
	    }
	    
	    if(!count($eeFilesArray)) {
		    $eeSFL_FREE_Log['SFL'][] = 'No Files Found';
	    }

		return $eeFilesArray;
	}
	
	
	
	// Check thumbnail
	public function eeSFL_CheckThumbnail($eeFilePath) { // File Path relative to FileListDir
		
		global $eeSFL_FREE_Log, $eeSFL_Settings_1, $eeSFL_FREE_Env;
		
		$eeExt = FALSE;
		$eeScreenshot = FALSE;
		$eeThumbURL = FALSE;
		
		$eePathParts = pathinfo($eeFilePath);
		$eeBaseName = $eePathParts['basename'];
		$eeFileNameOnly = $eePathParts['filename'];
		$eeExt = strtolower(@$eePathParts['extension']);
		
		$eeFileFullPath = ABSPATH . $eeSFL_Settings_1['FileListDir'] . $eeFilePath;
		$eeFileFullPath = str_replace('//', '/', $eeFileFullPath);
		
		$eeThumbsPATH = ABSPATH . $eeSFL_Settings_1['FileListDir'] . '.thumbnails/';
		
		// Is there already a thumb?
		if(is_file($eeThumbsPATH . 'thumb_' . $eeFileNameOnly . '.jpg')) { return TRUE; } // Checked Okay
		
		// Else...
		
		// Video Files
		if(in_array($eeExt, $this->eeDynamicVideoThumbFormats)) { // Check for FFMPEG
			
			if($eeSFL_FREE_Env['supported']) {
				
				// FFmpeg won't create the thumbs dir, so we need to do it here if needed.
				if(!is_dir($eeThumbsPATH)) { mkdir($eeThumbsPATH); }
				
				$eeExt = 'png'; // Set the extension
				$eeScreenshot = $eeThumbsPATH . 'eeScreenshot_' . $eeFileNameOnly . '.' . $eeExt; // Create a temporary file
				
				// Create a full-sized image at the one-second mark
				$eeCommand = 'ffmpeg -i ' . $eeFileFullPath . ' -ss 00:00:01.000 -vframes 1 ' . $eeScreenshot;
				
				$eeFFmpeg = trim(shell_exec($eeCommand));
				
				$eeSFL_FREE_Log['SFL'][] = $eeCommand;
				
				if(is_file($eeScreenshot)) { // <<<------------------------ TO DO - Resize down to $this->eeFileThumbSize
					
					$this->eeSFL_CreateThumbnailImage($eeScreenshot);
					
					unlink($eeScreenshot); // Delete the screeshot file
					
					return TRUE;
				
				} else {
					
					// FFmpeg FAILED !!!
					$eeSFL_FREE_Log['SFL'][] = 'FFmpeg could not read ' . $eeFileNameOnly . '. Using the default thumbnail.';
					
					// Create a default thumb, as if there was no FFmpeg <<<--------- TO DO - Try to improve the command above / How can we do this on the fly in the list?
					$eeFrom = $eeSFL_FREE_Env['pluginDir'] . 'images/thumbnails/!default_video.jpg';
					$eeTo = $eeThumbsPATH . 'thumb_' . $eeFileNameOnly . '.jpg';
					
					if(!copy($eeFrom, $eeTo)) {
						$eeSFL_FREE_Log['SFL'][] = 'FFmpeg could not create the default thumbnail.';
						return FALSE;
					}
					
					return TRUE;
				}
					
			} else {
				$eeSFL_FREE_Log['SFL'][] = 'FFmpeg Not Installed';
			}
		}
		
		// Image Files
		if(in_array($eeExt, $this->eeDynamicImageThumbFormats)) { // Just for known image files... 
		
			// Make sure it's really an image
			if(!@getimagesize($eeFileFullPath)) {
				
				$eeSFL_FREE_Log['errors'][] = 'Corrupt File Deleted: ' . basename($eeFileFullPath);
				
				unlink($eeFileFullPath);
				
				return FALSE;
			}
			
			// Generate an Image Thumbnail
			$this->eeSFL_CreateThumbnailImage($eeFileFullPath);
				
			return TRUE;
		}
	}
	
	
	
	
	// Create thumbnail image
	private function eeSFL_CreateThumbnailImage($eeFileFullPath) { // Requires full path 
		
		global $eeSFL_FREE_Log, $eeSFL_Settings_1, $eeSFL_FREE_Env;
		
		$eeFileSize = filesize($eeFileFullPath);
		
		$eeFilePath = str_replace(ABSPATH . $eeSFL_Settings_1['FileListDir'], '', $eeFileFullPath); // Strip path thru FileListDir
		
		$eePathParts = pathinfo($eeFilePath);
		
		if( !is_array($eePathParts) ) { 
	        $eeSFL_FREE_Log['errors'][] = 'No Path Parts';
	        $eeSFL_FREE_Log['errors'][] = $eeFileFullPath;
	        return FALSE;
	    }
		
		$eeFileNameOnly = $eePathParts['filename'];
		$eeFileExt = $eePathParts['extension'];
		$eeDir = $eePathParts['dirname']; // Get this path location
		if($eeDir) { $eeDir .= '/'; } // Add the slash
		$eeDir = str_replace('.thumbnails/', '', $eeDir); // Remove if .thumbnails/ is part of the path (video thumbs)

		// Dynamicly created thumbnails are here
		$eeThumbsURL = $eeSFL_FREE_Env['wpSiteURL'] . '/' . $eeSFL_Settings_1['FileListURL'] . $eeDir . '.thumbnails/'; 
		$eeThumbsPATH = ABSPATH . $eeSFL_Settings_1['FileListDir'] . $eeDir . '.thumbnails/'; // Path to them
		
		if(!is_dir($eeThumbsPATH)) {
			if(!mkdir($eeThumbsPATH)) {
				$eeSFL_Log['errors'][] = 'Cannot Create the Thumbnails Folder';
				return; 
			}
		}
        
        $eeSizeCheck = @getimagesize($eeFileFullPath);
        
        $eeSizeCheck['memory-limit'] = preg_replace("/[^0-9]/", "", ini_get('memory_limit') ) * 1048576;
        $eeSizeCheck['memory-usage'] = memory_get_usage();
        
        $eeImageMemoryNeeded = ($eeSizeCheck[0] * $eeSizeCheck[1] * $eeSizeCheck['bits']) / 8;
        
        $eeImageSizeLimit = ( $eeSizeCheck['memory-limit'] - $eeSizeCheck['memory-usage'] ) * .2;
        
        if($eeImageMemoryNeeded >  $eeImageSizeLimit) { // It's too big for Wordpress
			
			$eeDefaultThumbIcon = $eeSFL_FREE_Env['pluginDir'] . 'images/thumbnails/!default_image.jpg';
			$eeNewThumb = $eeThumbsPATH . 'thumb_' . $eeFileNameOnly . '.jpg';
		
			copy($eeDefaultThumbIcon, $eeNewThumb); // Use our default image file icon
		
		} else { // Create thumbnail
			
			// Thank Wordpress for this easyness.
			$eeFileImage = wp_get_image_editor($eeFileFullPath); // Try to open the file
	        
	        if (!is_wp_error($eeFileImage)) { // Image File Opened
	            
	            $eeFileImage->resize($this->eeFileThumbSize, $this->eeFileThumbSize, TRUE); // Create the thumbnail
	            
	            $eeFileNameOnly = str_replace('eeScreenshot_', '', $eeFileNameOnly); // Strip the temp term from video screenshots
			
				$eeSFL_FREE_Log['SFL'][] = $eeThumbsPATH . 'thumb_' . $eeFileNameOnly . '.jpg'; 
	            
	            $eeFileImage->save($eeThumbsPATH . 'thumb_' . $eeFileNameOnly . '.jpg'); // Save the file
	        
	        } else { // Cannot open
		        
		        $eeSFL_Log['SFL'][] = '!!! Bad Image File Deleted: ' . basename($eeFileFullPath);   
	        }
		}
	}
	
	
	
	
	// Move the sort item to the array key and then sort. Preserve the key (File ID) in a new element
	public function eeSFL_SortFiles($eeFiles, $eeSortBy, $eeSortOrder) {
		
		global $eeSFL_FREE_Log, $eeSFL_Settings_1;
		
		if(is_array($eeFiles)) {
			if( count($eeFiles) <= 1 ) { return $eeFiles; } // No point if none or one
		} else {
			return FALSE;
		}
		
		$eeSFL_FREE_Log['SFL'][] = 'Sorting by...';
		$eeSFL_FREE_Log['SFL'][] = $eeSortBy . ' > ' . $eeSortOrder;
		
		$eeFilesSorted = array();
			
		if($eeSortBy == 'Date') { // Files by Date
			
			foreach($eeFiles as $eeKey => $eeFileArray) {
				
				if(@$eeFileArray['FileDateChanged']) {
					
					$eeFilesSorted[ $eeFileArray['FileDateChanged'] . ' ' . $eeKey ] = $eeFileArray; // Add the file key to preserve files with same date or size.
					$eeFilesSorted[ $eeFileArray['FileDateChanged'] . ' ' . $eeKey ]['FileID'] = $eeKey; // Save the ID in new element
				
				} elseif($eeFileArray['FileDateAdded']) {
					
					$eeFilesSorted[ $eeFileArray['FileDateAdded'] . ' ' . $eeKey ] = $eeFileArray;
					$eeFilesSorted[ $eeFileArray['FileDateAdded'] . ' ' . $eeKey ]['FileID'] = $eeKey;
				}
			}
			
		} elseif($eeSortBy == 'Size') { // Files by Size
			
			foreach($eeFiles as $eeKey => $eeFileArray) {
				
				// Add the file key to preserve files with same size.
				$eeFilesSorted[ $eeFileArray['FileSize'] . '.' . $eeKey ] = $eeFileArray;
				$eeFilesSorted[ $eeFileArray['FileSize'] . '.' . $eeKey ]['FileID'] = $eeKey;
			}
	
		} elseif($eeSortBy == 'Name') { // Alpha
			
			foreach($eeFiles as $eeKey => $eeFileArray) {
				
				$eeFilePathLowerCase = strtolower($eeFileArray['FilePath']); // Make lower case so name sorting works properly
				$eeFilesSorted[ $eeFilePathLowerCase ] = $eeFileArray; // These keys shall always be unique
				$eeFilesSorted[ $eeFilePathLowerCase ]['FileID'] = $eeKey;
			}
		
		} else { // Random
			
			foreach($eeFiles as $eeKey => $eeFileArray) {
				
				$eeFilesSorted[ $eeFileArray['FilePath'] ] = $eeFileArray;
				$eeFilesSorted[ $eeFileArray['FilePath'] ]['FileID'] = $eeKey;
			}
			
			$eeKeys = array_keys($eeFilesSorted);

	        shuffle($eeKeys);
	
	        foreach($eeKeys as $eeKey) {
	            $eeNewArray[$eeKey] = $eeFilesSorted[$eeKey];
	        }
	
	        $eeFilesSorted = $eeNewArray;
			
			return $eeFilesSorted;
		}
			
		// Sort by the key
		ksort($eeFilesSorted); 
		
		// If Descending
		if($eeSortOrder == 'Descending') {
			$eeFilesSorted = array_reverse($eeFilesSorted);
		}
		
		// Reindex the array keys
		$eeFilesSorted = array_values($eeFilesSorted);

		return $eeFilesSorted;
	}
	
	
	
	public function eeSFL_SendFilesEmail() {
		
		$eePOST = $_POST;
		
		global $eeSFL_Settings_1, $eeSFL_FREE_Env;
		$eeFiles = '';
		
		// Process a raw input of email addresses
		$eeFrom = sanitize_email(@$eePOST['eeSFL_SendFrom']); // 1 Required
		if(!$eeFrom) { return FALSE; }
		
		$eeTo = eeSFL_FREE_ProcessEmailString(@$eePOST['eeSFL_SendTo']); // Required
		if(!$eeTo) { return FALSE; }
		
		// Optional Inputs
		$eeCc = eeSFL_FREE_ProcessEmailString(@$eePOST['eeSFL_SendCc']);
		
		// Email Headers
		$eeHeaders = eeSFL_FREE_ReturnHeaderString($eeFrom, $eeCc);
		
		// Subject
		$eeSubject = sanitize_text_field(@$eePOST['eeSFL_SendSubject']);
		if(!$eeSubject) { $eeSubject = __('File Notification', 'ee-simple-file-list'); }
		
		// Message
		$eeMessage = sanitize_text_field(@$eePOST['eeSFL_SendMessage']);
		
		if( is_array($eePOST['eeSFL_SendTheseFiles']) ) { // The files array checkboxes
			
			foreach( $eePOST['eeSFL_SendTheseFiles'] as $eeFile) {
				$eeFiles .= '-> ' . $eeSFL_Settings_1['FileListURL'] . sanitize_text_field( urldecode($eeFile) ) . PHP_EOL;
			}
		}
		
		// Footer
		if(@$eeSFL_Settings_1['SendFooter']) {
			$eeFooter = $eeSFL_Settings_1['SendFooter'];
		} else {
			if( is_admin() ) {
				$eeURL = $eeSFL_FREE_Env['wpSiteURL']; // The main URL
			} else {
				$eeURL = eeSFL_GetThisURL();
			}
			if($eeURL) {
				$eeFooter = 'Sent from the file list at ' . $eeURL;
			} else {
				$eeFooter = 'Powered by Simple File List';
			}
			
		}
		
		// The Body
		$eeMessage .= PHP_EOL .  PHP_EOL . $eeFiles . PHP_EOL .  PHP_EOL . $eeFooter; // with Custom Footer
		
		// TO DO -- Allow files to be attached if less than X MB
		$eeAttached = array();
		
		// Send the Message
		wp_mail($eeTo, $eeSubject, $eeMessage, $eeHeaders, $eeAttached);
	}
	
	
	
	
	// Send the notification email
	public function eeSFL_NotificationEmail($eeSFL_UploadJob) {
		
		global $eeSFL_FREE_Log, $eeSFL_Settings_1, $eeSFL_FREE_Env;
		
		if($eeSFL_UploadJob) {
		
			$eeSFL_FREE_Log['SFL'][] = 'Sending Notification Email...';
			
			// Build the Message Body
			$eeSFL_Body = $eeSFL_Settings_1['NotifyMessage']; // Get the template
			$eeSFL_Body = str_replace('[file-list]', $eeSFL_UploadJob, $eeSFL_Body); // Add files
			$eeSFL_Body = str_replace('[web-page]', get_permalink(), $eeSFL_Body); // Add location
			
			// Get Form Input?
			if(@$_POST['eeSFL_Email']) {
				
				$eeSFL_Body .= PHP_EOL . PHP_EOL . __('Uploader Information', 'ee-simple-file-list') . PHP_EOL;
				
				$eeSFL_Name = substr(sanitize_text_field(@$_POST['eeSFL_Name']), 0, 64);
				$eeSFL_Name = strip_tags($eeSFL_Name);
				if($eeSFL_Name) { 
					$eeSFL_Body .= __('Uploaded By', 'ee-simple-file-list') . ': ' . ucwords($eeSFL_Name) . " - ";
				}
				
				$eeSFL_Email = filter_var(sanitize_email(@$_POST['eeSFL_Email']), FILTER_VALIDATE_EMAIL);
				$eeSFL_Body .= strtolower($eeSFL_Email) . PHP_EOL;
				$eeSFL_ReplyTo = $eeSFL_Name . ' <' . $eeSFL_Email . '>';
				
				$eeSFL_Comments = substr(sanitize_text_field(@$_POST['eeSFL_Comments']), 0, 5012);
				$eeSFL_Comments = strip_tags($eeSFL_Comments);
				if($eeSFL_Comments) {
					$eeSFL_Body .= $eeSFL_Comments . PHP_EOL . PHP_EOL;
				}
			}
			
			// Show if no extensions installed
			if( @count($eeSFL_FREE_Env['installed']) < 1 OR strlen($eeSFL_Body) < 3) { // Or if no content
				
				$eeSFL_Body .= PHP_EOL . PHP_EOL . "----------------------------------"  . 
				PHP_EOL . "Powered by Simple File List - simplefilelist.com";
			}
		
			if($eeSFL_Settings_1['NotifyFrom']) {
				$eeSFL_NotifyFrom = $eeSFL_Settings_1['NotifyFrom'];
			} else {
				$eeSFL_NotifyFrom = get_option('admin_email');
			}
			
			if($eeSFL_Settings_1['NotifyFromName']) {
				$eeSFL_AdminName = $eeSFL_Settings_1['NotifyFromName'];
			} else {
				$eeSFL_AdminName = $this->eePluginName;
			}
			
			$eeTo = $eeSFL_Settings_1['NotifyTo'];
			
			$eeSFL_Headers = "From: " . stripslashes( $eeSFL_Settings_1['NotifyFromName'] ) . " <$eeSFL_NotifyFrom>" . PHP_EOL . 
				"Return-Path: $eeSFL_NotifyFrom" . PHP_EOL . "Reply-To: $eeSFL_NotifyFrom";
			
			if($eeSFL_Settings_1['NotifyCc']) {
				$eeSFL_Headers .= PHP_EOL . "CC:" . $eeSFL_Settings_1['NotifyCc'];
			}
				
			if($eeSFL_Settings_1['NotifyBcc']) {
				$eeSFL_Headers .= PHP_EOL . "BCC:" . $eeSFL_Settings_1['NotifyBcc'];
			}
			
			if($eeSFL_Settings_1['NotifySubject']) {
				$eeSFL_Subject = stripslashes( $eeSFL_Settings_1['NotifySubject'] );
			} else {
				$eeSFL_Subject = __('File Upload Notice', 'ee-simple-file-list');
			}
				
			if(strpos($eeTo, '@') ) {
				
				if(wp_mail($eeTo, $eeSFL_Subject, $eeSFL_Body, $eeSFL_Headers)) { // SEND IT
					
					$eeSFL_FREE_Log['SFL'][] = 'Notification Email Sent';
					return 'SUCCESS';
					
				} else {
					
					$eeSFL_FREE_Log['errors'][] = 'Notification Email FAILED';
				}
			}
		
		}
	}
	
	
	
	
	
	// Sanitize Email Addresses
	public function eeSFL_SanitizeEmailString($eeAddresses) { // Can be one or more addresses, comma deliniated
		
		global $eeSFL_FREE_Log;
		$eeAddressSanitized = '';
		
		if(strpos($eeAddresses, ',')) { // Multiple Addresses
		
			$eeSFL_Addresses = explode(',', $eeAddresses);
			
			$eeSFL_AddressesString = '';
			
			foreach($eeSFL_Addresses as $add){
				
				$add = trim($add);
				
				if(filter_var(sanitize_email($add), FILTER_VALIDATE_EMAIL)) {
			
					$eeSFL_AddressesString .= $add . ',';
					
				} else {
					$eeSFL_FREE_Log['errors'][] = $add . ' - ' . __('This is not a valid email address.', 'ee-simple-file-list');
				}
			}
			
			$eeAddressSanitized = substr($eeSFL_AddressesString, 0, -1); // Remove last comma
			
		
		} elseif(filter_var(sanitize_email($eeAddresses), FILTER_SANITIZE_EMAIL)) { // Only one address
			
			$add = $eeAddresses;
			
			if(filter_var(sanitize_email($add), FILTER_VALIDATE_EMAIL)) {
				
				$eeAddressSanitized = $add;
				
			} else {
				
				$eeSFL_FREE_Log['errors'][] = $add . ' - ' . __('This is not a valid email address.', 'ee-simple-file-list');
			}
			
		} else {
			
			$eeAddressSanitized = ''; // Anything but a good email gets null.
		}
		
		return $eeAddressSanitized;
	}
	
	
	
	
	
	
	// Upload Info Form Display
	public function eeSFL_UploadInfoForm() {
		
		$eeName = '';
		$eeEmail = '';
		
		$wpUserObj = wp_get_current_user();
		
		if($wpUserObj) {
			$eeName = $wpUserObj->first_name . ' ' . $wpUserObj->last_name;
			$eeEmail = $wpUserObj->user_email;
		}
		
		$eeOutput = '<div id="eeUploadInfoForm">';
		
			if(!$eeEmail) {
				
				$eeOutput .= '<label for="eeSFL_Name">' . __('Name', 'ee-simple-file-list') . ':</label>
			
				<input type="text" name="eeSFL_Name" value="" id="eeSFL_Name" size="64" maxlength="64" /> 
			
				<label for="eeSFL_Email">' . __('Email', 'ee-simple-file-list') . ':</label>
				<input type="text" name="eeSFL_Email" value="" id="eeSFL_Email" size="64" maxlength="128" />';
			
			} else {
				
				$eeOutput .= '<input type="hidden" id="eeSFL_Name" name="eeSFL_Name" value="' . $eeName . '" />
								<input type="hidden" id="eeSFL_Email" name="eeSFL_Email" value="' . $eeEmail . '" />';
				
			}
			
			$eeOutput .= '<label for="eeSFL_Comments">' . __('Description', 'ee-simple-file-list') . ':</label>
			<textarea name="eeSFL_Comments" id="eeSFL_Comments" rows="5" cols="64" maxlength="5012"></textarea></div>';
			
		return $eeOutput;
	
	}
		
	
	
	public function eeSFL_WriteLogData($eeSFL_ThisLog) {
		
		$eeDate = date('Y-m-d:h:m');
		
		$eeLogNow = get_option('eeSFL_Log'); // Stored as an array
		
		// Log Size Management
		$eeSizeCheck = serialize($eeLogNow);
		if(strlen($eeSizeCheck) > 65535) { // Using TEXT Limit, even tho options are LONGTEXT.
			$eeLogNow = array(); // Clear
		}
		
		$eeLogNow[$eeDate][] = $eeSFL_ThisLog;
		
		update_option('eeSFL_FREE_Log', $eeLogNow, FALSE);
		
		return TRUE;
	}

	
} // END Class 

?>