<?php // Simple File List Script: ee-class.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Class' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_FREE_Log['RunTime'][] = 'Loaded: ee-class';

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
	public $eeUseCache = 1; // Hours
    
    // File Types
    public $eeDynamicImageThumbFormats = array('gif', 'jpg', 'jpeg', 'png', 'tif', 'tiff');
    public $eeDynamicVideoThumbFormats = array('avi', 'flv', 'm4v', 'mov', 'mp4', 'webm', 'wmv');
    public $eeDefaultThumbFormats = array('3gp', 'ai', 'aif', 'aiff', 'apk', 'avi', 'bmp', 'cr2', 'dmg', 'doc', 'docx', 
    	'eps', 'flv', 'gz', 'indd', 'iso', 'jpeg', 'jpg', 'm4v', 'mov', 'mp3', 'mp4', 'mpeg', 'mpg', 'pdf', 'png', 
		'pps', 'ppsx', 'ppt', 'pptx', 'psd', 'tar', 'tgz', 'tif', 'tiff', 'txt', 'wav', 'wma', 'wmv', 'xls', 'xlsx', 'zip');
	public $eeOpenableFileFormats = array('aif', 'aiff', 'avi', 'bmp', 'flv', 'jpeg', 'jpg', 'gif', 'm4v', 'mov', 'mp3', 'mp4', 'mpeg', 'mpg', 'pdf', 'png', 
		'txt', 'wav', 'wma', 'wmv', 'htm', 'html');
    
    public $eeExcludedFileNames = array('error_log', 'index.html');
    public $eeForbiddenTypes = array('php','phar','pl','py','com','cgi','asp','exe','js','phtml', 'wsh','vbs');
    
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
		'ShowList' => 'YES', // Show the File List (YES, ADMIN, USER, NO)
		'AdminRole' => 5, // Who can access settings, based on WP role (5 = Admin ... 1 = Subscriber)
		'ShowFileThumb' => 'YES', // Display the File Thumbnail Column (YES or NO)
		'ShowFileDate' => 'YES', // Display the File Date Column (YES or NO)
		'ShowFileSize' => 'YES', // Display the File Size Column (YES or NO)
		'LabelThumb' => 'Thumb', // Label for the thumbnail column
		'LabelName' => 'Name', // Label for the file name column
		'LabelDate' => 'Date', // Label for the file date column
		'LabelSize' => 'Size', // Label for the file size column
		'SortBy' => 'DateMod', // Sort By (Name, Date, DateMod, Size, Random) -- DateMod added in 4.3
		'SortOrder' => 'Descending', // Descending or Ascending
		
		// Upload Settings
		'AllowUploads' => 'USER', // Allow File Uploads (YES, ADMIN, USER, NO)
		'UploadLimit' => 10, // Limit Files Per Upload Job (Quantity)
		'UploadMaxFileSize' => 8, // Maximum Size per File (MB)
		'FileFormats' => 'jpg, jpeg, png, tif, pdf, mov, mp4, mp3, zip', // Allowed Formats
		'AllowOverwrite' => 'NO', // Number new files with same name, or just overwrite.
		
		// Display Settings
		'GenerateImgThumbs' => 'YES', // Create thumbnail images for images if possible.
		'GeneratePDFThumbs' => 'YES', // Create thumbnail images for PDFs if possible.
		'GenerateVideoThumbs' => 'YES', // Create thumbnail images for videos if possible.
		'PreserveSpaces' => 'NO', // Replace ugly hyphens with spaces
		'ShowFileDescription' => 'YES', // Display the File Description (YES or NO)
		'ShowFileActions' => 'YES', // Display the File Action Links Section (below each file name) (YES or NO)
		'ShowFileOpen' => 'YES', // Show this operation
		'ShowFileDownload' => 'YES', // Show this operation
		'ShowFileCopyLink' => 'YES', // Show this operation
		'ShowFileExtension' => 'YES', // Show the file extension, or not.
		'ShowHeader' => 'YES', // Show the File List's Table Header (YES or NO)
		'ShowUploadLimits' => 'YES', // Show the upload limitations text.
		'GetUploaderInfo' => 'NO', // Show the Info Form
		'ShowSubmitterInfo' => 'NO', // Show who uploaded the file (name linked to their email)
		'AllowFrontManage' => 'NO', // Allow front-side users to manage files (YES or NO)
		'SmoothScroll' => 'YES', // Use the awesome and cool JavaScript smooth scroller after an upload
		
		// Notifications
		'Notify' => 'NO', // Send Notifications (YES or NO)
		'NotifyTo' => '', // Send Notification Email Here (Defaults to WP Admin Email)
		'NotifyCc' => '', // Send Copies of Notification Emails Here
		'NotifyBcc' => '', // Send Blind Copies of Notification Emails Here
		'NotifyFrom' => '', // The sender email (reply-to) (Defaults to WP Admin Email)
		'NotifyFromName' => 'Simple File List', // The nice name of the sender
		'NotifySubject' => 'File Upload Notice', // The subject line
		'NotifyMessage' => '', // The notice message's body
		
	);
	
	
	
	
	 // Default File List Definition
    public $eeSFL_Files = array(
	    
		0 => array( // The File ID (We copy this to the array on-the-fly when sorting)
			'FileList' => 1, // The ID of the File List, contained in the above array.
		    'FilePath' => '', // Path to file, relative to the list root
		    'FileExt' => '', // The file extension
		    'FileMIME' => '', // The MIME Type
			'FileSize' => '', // The size of the file
			'FileDateAdded' => '', // Date the file was added to the list
			'FileDateChanged' => '', // Last date the file was renamed or otherwise changed
			'FileDescription' => '', // A short description of the file
			'SubmitterName' => '', // Who uploaded the file
			'SubmitterEmail' => '', // Their email
			'SubmitterComments' => '', // What they said
		)
    );
	
	
	
	// Build a New File/Folder Array (for an upload or new file found)
	public function eeSFL_BuildFileArray($eeFilePath) { // Path relative to ABSPATH
		
		global $eeSFL_Settings;
		
		$eePathParts = pathinfo($eeFilePath);
		
		if( is_readable(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFilePath) ) {
		
			$eeFileArray = $this->eeSFL_Files[0]; // Get the file array template
			$eeFileArray['FilePath'] = $eeFilePath; // Path to file, relative to the list root
			
			if(isset($eePathParts['extension'])) { $eeExt = strtolower($eePathParts['extension']); } else { $eeExt = 'folder'; }
			$eeFileArray['FileExt'] = $eeExt; // The file extension 
			
			if(function_exists('mime_content_type')) {
				$eeFileArray['FileMIME'] = mime_content_type(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFilePath); // MIME Type
			}
			
			$eeFileArray['FileSize'] = filesize(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFilePath);
			
			$eeFileArray['FileDateAdded'] = date("Y-m-d H:i:s");
			$eeFileArray['FileDateChanged'] = date("Y-m-d H:i:s", filemtime(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFilePath));
			
			if( strlen($eeFileArray['FilePath']) ) { // 02/21 - If FilePath is empty, sort doesn't work? But why would that be empty.
				return $eeFileArray;
			}
		
		}
		
		return FALSE;
	}
	
    
    
    // Get Environment
    public function eeSFL_GetEnv() {
	    
	    $eeEnv = array();
	    
	    // Detect OS
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		    $eeEnv['eeOS'] = 'WINDOWS';
		} else {
		    $eeEnv['eeOS'] = 'LINUX';
		}
		
		// Detect Web Server
		if(!function_exists('apache_get_version')) {
		    $eeEnv['eeWebServer'] = $_SERVER["SERVER_SOFTWARE"];
		} else {
			$eeEnv['eeWebServer'] = 'Apache';
		}
		
		$eeEnv['wpSiteURL'] = get_option('siteurl') . '/'; // This Wordpress Website
		$eeEnv['wpPluginsURL'] = plugins_url() . '/'; // The Wordpress Plugins Location
		
		$eeEnv['pluginURL'] = plugins_url() . '/' . $this->eePluginNameSlug . '/';
		$eeEnv['pluginDir'] = WP_PLUGIN_DIR . '/' . $this->eePluginNameSlug . '/';
		
		$wpUploadArray = wp_upload_dir();
		$wpUploadDir = $wpUploadArray['basedir'];
		$eeEnv['wpUploadDir'] = $wpUploadDir . '/'; // The Wordpress Uploads Location
		$eeEnv['wpUploadURL'] = $wpUploadArray['baseurl'] . '/';

		$eeEnv['FileListDefaultDir'] = str_replace(ABSPATH, '', $eeEnv['wpUploadDir'] . 'simple-file-list/'); // The default file list location
		
		$eeEnv['the_max_upload_size'] = eeSFL_FREE_ActualUploadMax();
	
		$eeEnv['supported'] = get_option('eeSFL_Supported'); // Server technologies available (i.e. FFMPEG)
		
		$eeEnv['wpUserID'] = get_current_user_id();
		
		$eeEnv['UploadedFiles'] = array(); // Holder for upload job
		
		// Check Server technologies available (i.e. ffMpeg)
		$eeSupported = get_option('eeSFL_Supported');
		
		if(is_array($eeSupported)) {
			
			if( in_array('ImageMagick', $eeSupported) AND in_array('GhostScript', $eeSupported) ) { 
				$eeEnv['ImkGs'] = 'YES';
			}
			if( in_array('ffMpeg', $eeSupported) ) {
				$eeEnv['ffMpeg'] = 'YES';
			}
		}
		
		ksort($eeEnv);
		
		return $eeEnv;
    }
    
    

    // Get Settings for Specified List
    public function eeSFL_GetSettings() {
	    
	    global $eeSFL_FREE_Log, $eeSFL_FREE_Env;
		
		$eeSFL_FREE_Log['RunTime'][] = 'Getting List 1 Settings...'; // One is the only number that you ever will...
	    
	    // Getting the settings array
	    $eeSettings = get_option('eeSFL_Settings_1');
	    
	    if(is_array($eeSettings)) {
			
			$eeSettings['FileListURL'] = $eeSFL_FREE_Env['wpSiteURL'] . $eeSettings['FileListDir']; // The Full URL
			
			// The whole point...	
			return $eeSettings; // Associative array
		
		} else {
			
			$eeSFL_FREE_Log['RunTime'][] = '!!! MISSING SETTINGS, BACK TO DEFAULT...';
			update_option('eeSFL_Settings_1', $this->DefaultListSettings); // The settings are gone, so reset to defaults.
			return $this->DefaultListSettings;
		}
	}
    
    
    
    public function eeSFL_UpdateFileDetail($eeFile, $eeDetail, $eeValue = FALSE) {
	    
	    if($eeValue !== FALSE) {
	    
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
	    
	    global $eeSFL_FREE_Log, $eeSFL_Settings, $eeSFL_FREE_Env;
	    
	    $eeSFL_FREE_Log['RunTime'][] = 'Calling Method: eeSFL_UpdateFileListArray()';
	    $eeSFL_FREE_Log['RunTime'][] = 'Scanning File List...';
	    
	    // Get the File List Array
	    $eeFilesDBArray = get_option('eeSFL_FileList_1');
	    if(!is_array($eeFilesDBArray)) { $eeFilesDBArray = array(); }
	    
	    // List the actual files on the disk
	    $eeFilePathsArray = $this->eeSFL_IndexFileListDir($eeSFL_Settings['FileListDir']);
	    
	    if(!count($eeFilePathsArray)) {
		    $eeSFL_FREE_Log['RunTime'][] = 'No Files Found';
		    return FALSE; // Quit and leave DB alone
	    }
	    
	    // Create an array we'll fill with files
	    $eeFileArrayWorking = array();
	    
	    // No List in the DB, Creating New...
	    if( !count($eeFilesDBArray) ) {
			
			$eeSFL_FREE_Log['RunTime'][] = 'No List Found! Creating from scratch...';
			
			foreach( $eeFilePathsArray as $eeKey => $eeFile) {
				
				$eePathParts = pathinfo($eeFile);
				
				// Add it to the array
				$eeFileArrayWorking[] = array(
					'FilePath' => $eeFile
					,'FileExt' => strtolower($eePathParts['extension'])
					,'FileSize' => filesize(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFile)
					,'FileDateAdded' => date("Y-m-d H:i:s")
					,'FileDateChanged' => date("Y-m-d H:i:s", filemtime(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFile))
				);
				
				if(function_exists('mime_content_type')) {
					$eeFileArrayWorking[]['FileMIME'] = mime_content_type(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFile); // MIME Type
				}				
			}
		
		} else { // Update file info
			
			$eeSFL_FREE_Log['RunTime'][] = 'Updating Existing List...';
			
			$eeFileArrayWorking = $eeFilesDBArray; // Fill it up with current files
			
			// Check to be sure each file is there...
			foreach( $eeFileArrayWorking as $eeKey => $eeFileSet) {
				
				// Build full path
				$eeFileFullPath = ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFileSet['FilePath'];
				
				if( !is_file($eeFileFullPath) ) { // Get rid of it
					
					$eeSFL_FREE_Log['RunTime'][] = 'Removing: ' . $eeFileFullPath;
					unset($eeFileArrayWorking[$eeKey]);
				
				} else {
				
					// Update file size
					$eeFileArrayWorking[$eeKey]['FileSize'] = filesize($eeFileFullPath);
						
					// Update modification date
					$eeFileArrayWorking[$eeKey]['FileDateChanged'] = date("Y-m-d H:i:s", @filemtime($eeFileFullPath));
				}
			}

			
			// Check if any new files have been added
			foreach( $eeFilePathsArray as $eeKey => $eeFile) {
				
				$eeSFL_FREE_Log['RunTime'][] = 'Checking File: ' . $eeFile;
				
				$eeFound = FALSE;
				
				// Look for this file in our array
				foreach( $eeFileArrayWorking as $eeKey2 => $eeThisFileArray ) {
					
					if($eeFile == $eeThisFileArray['FilePath']) { $eeFound = TRUE; break; } // Found this file, on to the next.
				}
				
				if($eeFound === FALSE) {
					
					$eeSFL_FREE_Log['RunTime'][] = '!!! New File Found: ' . $eeFile;
					
					$eePathParts = pathinfo($eeFile);
					
					// Add it to the array
					$eeFileArrayWorking[] = array(
						'FilePath' => $eeFile
						,'FileExt' => strtolower($eePathParts['extension'])
						,'FileSize' => filesize(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFile)
						,'FileDateAdded' => date("Y-m-d H:i:s")
						,'FileDateChanged' => date("Y-m-d H:i:s", filemtime(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFile))
					);
				
					if(function_exists('mime_content_type')) {
						$eeFileArrayWorking['FileMIME'][] = mime_content_type(ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFile); // MIME Type
					}
				}
			}
		}
		
		
		// Sort...
		if(count($eeFileArrayWorking)) {
			
			// Sort
		    $eeFileArrayWorking = $this->eeSFL_SortFiles($eeFileArrayWorking, $eeSFL_Settings['SortBy'], $eeSFL_Settings['SortOrder']);

			
			// Update the DB
		    update_option('eeSFL_FileList_1', $eeFileArrayWorking);
		    
		    // Check for and create thumbnail if needed...
		    if( $eeSFL_Settings['ShowFileThumb'] == 'YES' ) {
			    
			    // Check for supported technologies
				eeSFL_FREE_CheckSupported();
						
				$eeSFL_Log['RunTime'][] = 'Checking Thumbnails ...';
		    
				// Check for and create thumbnail if needed...
			    foreach($eeFileArrayWorking as $eeKey => $eeFile) {
			    	
			    	if( ($eeFile['FileExt'] == 'pdf' AND $eeSFL_Settings['GeneratePDFThumbs'] == 'YES')
			    		OR (in_array($eeFile['FileExt'], $this->eeDynamicImageThumbFormats) AND $eeSFL_Settings['GenerateImgThumbs'] == 'YES')
							OR (in_array($eeFile['FileExt'], $this->eeDynamicVideoThumbFormats) AND $eeSFL_Settings['GenerateVideoThumbs'] == 'YES') ) {
						
								$this->eeSFL_CheckThumbnail($eeFile['FilePath'], $eeSFL_Settings);	
					}
			    }
			    
		    } else {
			    $eeSFL_Log['RunTime'][] = 'Not Showing Thumbnails';
			    
		    }
		    
		    // Check for Enviroment Changes
		    $eeActual = eeSFL_FREE_ActualUploadMax();
			if( $eeSFL_Settings['UploadMaxFileSize'] > $eeActual ) { 
				$eeSFL_Settings['UploadMaxFileSize'] = $eeActual;
				update_option('eeSFL_Settings_' . $eeSFL_ID, $eeSFL_Settings); // Set to Actual Max
			}
		    
		    return $eeFileArrayWorking; 
		
		} else {
			return FALSE;
		}
    }
	
	
	
	
	// Get All the Files
	private function eeSFL_IndexFileListDir($eeFileListDir) {
	    
	    global $eeSFL_FREE, $eeSFL_FREE_Log;
	    
	    $eeFilesArray = array();
	    
	    if(!is_dir(ABSPATH . $eeFileListDir)) {
		    
		    $eeSFL_FREE_Log['errors'][] = 'The directory is Gone :-0  Re-Creating...';
		    
		    eeSFL_FREE_FileListDirCheck($eeFileListDir);
		    return $eeFilesArray;
	    }
		    
	    $eeSFL_FREE_Log['RunTime'][] = 'Getting files from: ' . $eeFileListDir; 
	    
	    $eeFileNameOnlyArray = scandir(ABSPATH . $eeFileListDir);
	    
	    foreach($eeFileNameOnlyArray as $eeValue) {
	    	
	    	$eePathParts = pathinfo($eeValue);
			if(isset($eePathParts['extension'])) {
				if( in_array($eePathParts['extension'], $this->eeForbiddenTypes) ) { $eeValue = '.forbidden'; }
			}
	    	
	    	if(strpos($eeValue, '.') !== 0 ) { // Not hidden
		    	
		    	if(is_file(ABSPATH . $eeFileListDir . $eeValue)) { // Is a regular file
		    	
			    	if(!in_array($eeValue, $this->eeExcludedFiles) )  { // Not excluded
				    	
				    	$eeNewItem = eeSFL_FREE_SanitizeFileName($eeValue);
			            if($eeNewItem != $eeValue) {
				            
				            $eeSFL_FREE_Log['Trouble'][] = 'OLD --> BAD File Name: ' . $eeValue;
				            
				            if(rename(ABSPATH . $eeFileListDir . $eeValue, ABSPATH . $eeFileListDir . $eeNewItem)) {
					        	
					        	$eeValue = $eeNewItem;
								$eeSFL_FREE_Log['Trouble'][] = 'NEW --> File Name Sanitized: ' . $eeValue;
				        	}
			            }
			            
				    	$eeFilesArray[] = $eeValue; // Add the path
			    	}
		    	}
	    	}
	    }
	    
	    if(!count($eeFilesArray)) {
		    $eeSFL_FREE_Log['RunTime'][] = 'No Files Found';
	    }

		return $eeFilesArray;
	}
	
	
	
	// Move, Rename or Delete a thumbnail - Expects path relative to FileListDir
	public function eeSFL_UpdateThumbnail($eeFileFrom, $eeFileTo, $eeSFL_ID = 1) {
		
		global $eeSFL_FREE, $eeSFL_Log;
		
		$eeSFL_Settings = $eeSFL_FREE->eeSFL_GetSettings($eeSFL_ID); // Get this list
		
		$eePathPartsFrom = pathinfo($eeFileFrom);
		
		if(isset($eePathPartsFrom['extension'])) { // Files only
			
			if($eePathPartsFrom['extension'] = 'pdf' 
				OR in_array($eePathPartsFrom['extension'], $this->eeDynamicImageThumbFormats) 
					OR in_array($eePathPartsFrom['extension'], $this->eeDynamicVideoThumbFormats) ) {
				
				// All thumbs are JPGs
				if($eePathPartsFrom['extension'] != 'jpg') { 
					$eeFileFrom = str_replace('.' . $eePathPartsFrom['extension'], '.jpg', $eeFileFrom);
					$eeFileTo = str_replace('.' . $eePathPartsFrom['extension'], '.jpg', $eeFileTo);
				}
				
				$eeThumbFrom = ABSPATH . $eeSFL_Settings['FileListDir'];
				
				if($eePathPartsFrom['dirname'] != '.') { $eeThumbFrom .= $eePathPartsFrom['dirname']; }
				
				$eeThumbFrom .= '/.thumbnails/thumb_' . basename($eeFileFrom);
				
				if( is_file($eeThumbFrom) ) {
					
					if(!$eeFileTo) { // Delete the thumb
						
						if(unlink($eeThumbFrom)) {
							
							$eeSFL_Log['RunTime'][] = 'Deleted Thumbnail For: ' . basename($eeFileFrom);
							
							return;
						}
					
					} else { // Move / Rename
						
						$eePathPartsTo = pathinfo($eeFileTo);
						
						$eeThumbTo = ABSPATH . $eeSFL_Settings['FileListDir'] . $eePathPartsTo['dirname'] . '/.thumbnails/thumb_' . basename($eeFileTo);
						
						if(rename($eeThumbFrom, $eeThumbTo)) { // Do nothing on failure
						
							$eeSFL_Log['RunTime'][] = 'Thumbnail Updated For: ' . basename($eeFileFrom);
							
							return;
						}
					}
				}
			}
		}
	}
	
	
	
	
	
	// Check Thumbnail and Create if Needed
	public function eeSFL_CheckThumbnail($eeFilePath, $eeSFL_Settings) { // Expects FilePath relative to FileListDir & the List's Settings Array
		
		global $eeSFL_FREE_Log, $eeSFL_FREE_Env;
		
		$eePathParts = pathinfo($eeFilePath);
		$eeFileNameOnly = $eePathParts['filename'];
		$eeFileExt = $eePathParts['extension'];
		$eeFileSubPath = $eePathParts['dirname'] . '/';
		$eeFileFullPath = ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFilePath;
		$eeThumbsPath = ABSPATH . $eeSFL_Settings['FileListDir'] . $eeFileSubPath . '.thumbnails/';
		$eeThumbFileToCheck = 'thumb_' . $eeFileNameOnly . '.jpg';
		
		// Check for the .thumbnails directory
		if( !is_dir($eeThumbsPath) ) {
			if( !mkdir($eeThumbsPath) ) { 
				$eeSFL_FREE_Log['RunTime'][] = '!!!! Cannot create the .thumbnails directory: ' . $eeThumbsPath;
				return FALSE;
			}
		}
		
		// Is there already a thumb?
		if(is_file($eeThumbsPath . $eeThumbFileToCheck)) {
			return TRUE; // Checked Okay
		}
		
		
		
		// Else We Generate ...
		$eeSFL_FREE_Log['RunTime'][] = 'Missing: thumb_' . $eeFileNameOnly . '.jpg';
		
		// Image Files
		if(in_array($eeFileExt, $this->eeDynamicImageThumbFormats) AND $eeSFL_Settings['GenerateImgThumbs'] == 'YES') { // Just for known image files... 
			
			// Make sure it's really an image
			if( getimagesize($eeFileFullPath) ) {
				
				if(strpos($eeFileFullPath, '.')) {
					if( $this->eeSFL_CreateThumbnailImage($eeFileFullPath) ) {
						return TRUE;
					} else {
						return FALSE;
					}
				}
				
			} else { // Not an image, be gone with you!
				
				unlink($eeFileFullPath);
				$eeSFL_FREE_Log['errors'][] = '!!!! ' . __('Corrupt Image File Deleted', 'ee-simple-file-list') . ': ' . basename($eeFileFullPath);
				return FALSE;
			}
		}
		
		
		// Video Files
		if(in_array($eeFileExt, $this->eeDynamicVideoThumbFormats) AND $eeSFL_Settings['GenerateVideoThumbs'] == 'YES' AND isset($eeSFL_FREE_Env['ffMpeg']) ) {
			
			$this->eeSFL_CreateVideoThumbnail($eeFileFullPath); // Create a temp image, then a thumb from that using $this->eeSFL_CreateThumbnailImage()
		}
		
		
		// PDF Files
		if($eeFileExt == 'pdf' AND $eeSFL_Settings['GeneratePDFThumbs'] == 'YES' AND isset($eeSFL_FREE_Env['ImkGs']) ) {
			
			if($this->eeSFL_CreatePDFThumbnail($eeFileFullPath)) {
				return TRUE;
			}
		}
	}
	
	
	
	
	// Create Image Thumbnail
	private function eeSFL_CreateThumbnailImage($eeInputFileCompletePath) { // Expects Full Path
		
		// exit($eeInputFileCompletePath);
		
		global $eeSFL_FREE_Log, $eeSFL_FREE_Env;
		
		if(!is_file($eeInputFileCompletePath)) {
			$eeSFL_FREE_Log['RunTime'][] = '!!!! Source File Not Found';
			return FALSE;
		}
		
		$eeSFL_FREE_Log['RunTime'][] = 'Creating Thumbnail Image for ' . basename($eeInputFileCompletePath);
		
		// All The Path Parts
		$eePathParts = pathinfo($eeInputFileCompletePath);
		$eeFileNameOnly = $eePathParts['filename'];
		$eeFileExt = $eePathParts['extension'];
		
		// Sub-Directory Path
		$eeCompleteDir = $eePathParts['dirname'] . '/';
		
		// The Destination
		// PDF and Video temp files are created in the .thumbnails dir - Strip that part of the path so it's not doubled.
		if(!strpos($eeCompleteDir, '.thumbnails/')) {
			$eeThumbsPath = $eeCompleteDir . '.thumbnails/';
		} else {
			$eeThumbsPath = $eeCompleteDir;
		}
		
		
		
		// The Source
		$eeImageMemoryNeeded = 0;
		$eeImageSizeLimit = 0;
		$eeFileSize = filesize($eeInputFileCompletePath);
		$eeSizeCheck = getimagesize($eeInputFileCompletePath);
        $eeSizeCheck['memory-limit'] = preg_replace("/[^0-9]/", "", ini_get('memory_limit') ) * 1048576;
	    $eeSizeCheck['memory-usage'] = memory_get_usage();
		
		if(isset($eeSizeCheck['bits'])) {
	        $eeImageMemoryNeeded = ($eeSizeCheck[0] * $eeSizeCheck[1] * $eeSizeCheck['bits']) / 8;
	        $eeImageSizeLimit = ( $eeSizeCheck['memory-limit'] - $eeSizeCheck['memory-usage'] ) * .2;
        }
        
        if($eeImageMemoryNeeded > $eeImageSizeLimit) { // It's too big for Wordpress
			
			if( strpos($eeFileNameOnly, 'temp_') === 0 ) { // These are PDF thumbs
				$eeDefaultThumbIcon = $eeSFL_FREE_Env['pluginDir'] . 'images/thumbnails/!default_pdf.jpg';
			} else {
				$eeDefaultThumbIcon = $eeSFL_FREE_Env['pluginDir'] . 'images/thumbnails/!default_image.jpg';
			}
			
			$eeFileNameOnly = str_replace('temp_', '', $eeFileNameOnly); // Strip the temp term if needed
			$eeNewThumb = $eeThumbsPath . 'thumb_' . $eeFileNameOnly . '.jpg';
		
			copy($eeDefaultThumbIcon, $eeNewThumb); // Use our default image file icon
			
			$eeSFL_FREE_Log['warnings'][] = 'Image was too large. Default thumbnail will be used for: ' . basename($eeInputFileCompletePath);
			
			return TRUE;
		
		} else { // Create thumbnail

			// Thank Wordpress for this easyness.
			$eeFileImage = wp_get_image_editor($eeInputFileCompletePath); // Try to open the file
	        
	        if (!is_wp_error($eeFileImage)) { // Image File Opened
	            
	            $eeFileImage->resize($this->eeFileThumbSize, $this->eeFileThumbSize, TRUE); // Create the thumbnail
	            
	            $eeFileNameOnly = str_replace('temp_', '', $eeFileNameOnly); // Strip the temp term 
	            
	            $eeFileImage->save($eeThumbsPath . 'thumb_' . $eeFileNameOnly . '.jpg'); // Save the file
			
				$eeSFL_FREE_Log['RunTime'][] = 'Thumbnail Created.';
	            
	            return TRUE;
	        
	        } else { // Cannot open
		        
		        $eeSFL_FREE_Log['warnings'][] = 'Bad Image File Deleted: ' . basename($eeInputFileCompletePath);
		        
		        return FALSE;
	        }
		}
		
		return FALSE;
	}
	
	
	
	
	private function eeSFL_CreateVideoThumbnail($eeFileFullPath) { // Expects Full Path
		
		global $eeSFL_FREE_Log, $eeSFL_FREE_Env;
		
		// All The Path Parts
		$eePathParts = pathinfo($eeFileFullPath);
		$eeFileNameOnly = $eePathParts['filename'];
		$eeFileExt = $eePathParts['extension'];
		$eeCompleteDir = $eePathParts['dirname'] . '/';
		$eeThumbsPath = $eeCompleteDir . '.thumbnails/';
		
		if(is_dir($eeThumbsPath)) {
			
			// Create a temporary file
			$eeScreenshot = $eeThumbsPath . 'temp_' . $eeFileNameOnly . '.png';
			
			// Create a full-sized image at the one-second mark
			$eeCommand = 'ffmpeg -i ' . $eeFileFullPath . ' -ss 00:00:01.000 -vframes 1 ' . $eeScreenshot;
			
			$eeffMpeg = trim(shell_exec($eeCommand));
			
			if(is_file($eeScreenshot)) { // Resize down to $this->eeFileThumbSize
				
				if( $this->eeSFL_CreateThumbnailImage($eeScreenshot) ) {
					unlink($eeScreenshot); // Delete the screeshot file
					return TRUE;
				} else {
					unlink($eeScreenshot); // Delete the screeshot file anyway
					return FALSE;
				}
			
			} else {
				
				// ffMpeg FAILED !!!
				$eeSFL_FREE_Log['warnings'][] = 'ffMpeg could not create a screenshot for ' . basename($eeScreenshot);
				return FALSE;
			}
		}
		
		$eeSFL_FREE_Log['RunTime'][] = '!!!! There is no .thumbnails directory: ' . $eeThumbsPath;
		
		return FALSE;
	}
	
	
		
	
	// Generate PDF Thumbnails
	private function eeSFL_CreatePDFThumbnail($eeFileFullPath) { // Expects Full Path
		
		global $eeSFL_FREE_Env, $eeSFL_FREE_Log;
		
		$eeSFL_FREE_Log['RunTime'][] = 'Generating PDF Thumbnail...';
		
		$eePathParts = pathinfo($eeFileFullPath);
		$eeFileNameOnly = $eePathParts['filename'];
		$eeFileExt = $eePathParts['extension'];
		$eeCompleteDir = $eePathParts['dirname'] . '/';
		$eeThumbsPath = $eeCompleteDir . '.thumbnails/';
		$eeTempFile = 'temp_' . $eeFileNameOnly . '.jpg'; // The converted pdf file - A temporary file
		$eeTempFileFullPath = $eeThumbsPath . $eeTempFile;
		
		if($eeFileExt != 'pdf') { return FALSE; }
		
		if( isset($eeSFL_FREE_Env['ImkGs']) ) {
		
			// $eeSFL_FREE_Log['RunTime'][] = 'ImageMagik & GhostScript is Installed';
			
			// Check Size and set image resolution higher for smaller sizes.
			$eeFileSize = filesize($eeFileFullPath);
			if($eeFileSize >= 8388608) { // Greater than 8 MB
				$eeResolution = '72';
				$eeBits = '2';
				$eeQuality = '60';
				$eeQFactor = '.25';
			} elseif($eeFileSize < 8388608 AND $eeFileSize > 2097152) { // Less than 8MB but larger than 2 MB 
				$eeResolution = '150';
				$eeBits = '2';
				$eeQuality = '75';
				$eeQFactor = '.5';
			} else { // Less than 2 MB
				$eeResolution = '300';
				$eeBits = '4';
				$eeQuality = '90';
				$eeQFactor = '.75';
			}
			
			// GhostScript Operations
			if( !is_readable($eeTempFileFullPath) ) { // Might be there already.
			
				// Check PDF Validity
				$eeCommand = 'gs -dNOPAUSE -dBATCH -sDEVICE=nullpage ' . $eeFileFullPath;
				
				// Run the Command. Drum roll please
				exec( $eeCommand, $eeCommandOutput, $eeReturnVal );
				
				$eeSFL_FREE_Log['GhostScript'][] = $eeCommand;
				$eeSFL_FREE_Log['GhostScript'][] = $eeCommandOutput;
				$eeSFL_FREE_Log['GhostScript'][] = $eeReturnVal;
				
				if($eeReturnVal === 0) { // Zero == No Errors
					
					// The command. AVOID LINE BREAKS
					$eeCommand = 'gs -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=' . $eeQuality . ' -dQFactor=' . $eeQFactor . ' -r' . $eeResolution . ' -dFirstPage=1 -dLastPage=1 -sOutputFile=' . $eeTempFileFullPath . ' ' . $eeFileFullPath;

					// Run the Command. Drum roll please
					exec( $eeCommand, $eeCommandOutput, $eeReturnVal );
					
					$eeSFL_FREE_Log['GhostScript'][] = $eeCommand;
					$eeSFL_FREE_Log['GhostScript'][] = $eeCommandOutput;
					$eeSFL_FREE_Log['GhostScript'][] = $eeReturnVal;
				
				} else {
					
					$eeSFL_FREE_Log['warnings'][] = __('FILE NOT READABLE', 'ee-simple-file-list') . ': ' . basename($eeFileFullPath);
					$eeSFL_FREE_Log['RunTime'][] = '!!!! PDF NOT READABLE: ' . basename($eeFileFullPath);
					return FALSE;
				}
			}
				
			// Confirm the file is there
			if(is_readable($eeTempFileFullPath)) {
				
				if($this->eeSFL_CreateThumbnailImage($eeTempFileFullPath)) {
					
					$eeSFL_FREE_Log['RunTime'][] = 'Created the PDF Thumbnail for ' . basename($eeFileFullPath);
					
					unlink($eeTempFileFullPath); // Delete the temp PNG file
					
					return TRUE;
					
				} else {
					
					$eeSFL_FREE_Log['RunTime'][] = '!!!! FAILED to Create the PDF Thumbnail for ' . basename($eeFileFullPath);
					
					unlink($eeTempFileFullPath);
					
					return FALSE;
				}
			
			} elseif(is_file($eeTempFileFullPath)) {
				
				unlink($eeTempFileFullPath); // Delete the corrupt temp file;
				
				return FALSE;
			
			} else {
				
				$eeSFL_FREE_Log['RunTime'][] = '!!!! PDF to PNG FAILED for ' . basename($eeFileFullPath);
				
				return FALSE;
			}		
		}
		
		return FALSE;
	}
	
	
	
	
	
	
	
	// Move the sort item to the array key and then sort. Preserve the key (File ID) in a new element
	public function eeSFL_SortFiles($eeFiles, $eeSortBy, $eeSortOrder) {
		
		global $eeSFL_FREE_Log, $eeSFL_Settings;
		
		// echo '<pre>'; print_r($eeFiles); echo '</pre>'; exit;
		
		if(is_array($eeFiles)) {
			
			if( count($eeFiles) <= 1 ) { return $eeFiles; } // No point if none or one
			
			// Clean the Array
			foreach( $eeFiles as $eeKey => $eeFileArray) {
				if( !isset($eeFileArray['FilePath']) ) { unset($eeFiles[$eeKey]); } // Get rid of bad arrays.
			}
			
		} else {
			return FALSE;
		}
		
		$eeSFL_FREE_Log['RunTime'][] = 'Sorting by...';
		$eeSFL_FREE_Log['RunTime'][] = $eeSortBy . ' > ' . $eeSortOrder;
		
		$eeFilesSorted = array();
			
		if($eeSortBy == 'Date') { // Files by Date Added (the Original)
			
			foreach($eeFiles as $eeKey => $eeFileArray) {
				
				$eeFilesSorted[ $eeFileArray['FileDateAdded'] . ' ' . $eeKey ] = $eeFileArray;
			}
			
		} elseif($eeSortBy == 'DateMod') { // Files by Date Modified (By Customer Request)
			
			foreach($eeFiles as $eeKey => $eeFileArray) {
					
				$eeFilesSorted[ $eeFileArray['FileDateChanged'] . ' ' . $eeKey ] = $eeFileArray; // Add the file key to preserve files with same date or size.
			}
			
		} elseif($eeSortBy == 'Size') { // Files by Size
			
			foreach($eeFiles as $eeKey => $eeFileArray) {
				
				// Add the file key to preserve files with same size.
				$eeFilesSorted[ $eeFileArray['FileSize'] . '.' . $eeKey ] = $eeFileArray;
			}
	
		} elseif($eeSortBy == 'Name') { // Alpha
			
			foreach($eeFiles as $eeKey => $eeFileArray) {
				
				$eeFilePathLowerCase = strtolower($eeFileArray['FilePath']); // Make lower case so name sorting works properly
				$eeFilesSorted[ $eeFilePathLowerCase ] = $eeFileArray; // These keys shall always be unique
			}
		
		} else { // Random
			
			foreach($eeFiles as $eeKey => $eeFileArray) {
				
				$eeFilesSorted[ $eeFileArray['FilePath'] ] = $eeFileArray;
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
	
	
	
	
	// Send the notification email
	public function eeSFL_NotificationEmail($eeSFL_UploadJob) {
		
		global $eeSFL_FREE_Log, $eeSFL_Settings, $eeSFL_FREE_Env;
		
		if($eeSFL_UploadJob) {
		
			$eeSFL_FREE_Log['RunTime'][] = 'Sending Notification Email...';
			
			// Build the Message Body
			$eeSFL_Body = $eeSFL_Settings['NotifyMessage']; // Get the template
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
		
			if($eeSFL_Settings['NotifyFrom']) {
				$eeSFL_NotifyFrom = $eeSFL_Settings['NotifyFrom'];
			} else {
				$eeSFL_NotifyFrom = get_option('admin_email');
			}
			
			if($eeSFL_Settings['NotifyFromName']) {
				$eeSFL_AdminName = $eeSFL_Settings['NotifyFromName'];
			} else {
				$eeSFL_AdminName = $this->eePluginName;
			}
			
			$eeTo = $eeSFL_Settings['NotifyTo'];
			
			$eeSFL_Headers = "From: " . stripslashes( $eeSFL_Settings['NotifyFromName'] ) . " <$eeSFL_NotifyFrom>" . PHP_EOL . 
				"Return-Path: $eeSFL_NotifyFrom" . PHP_EOL . "Reply-To: $eeSFL_NotifyFrom";
			
			if($eeSFL_Settings['NotifyCc']) {
				$eeSFL_Headers .= PHP_EOL . "CC:" . $eeSFL_Settings['NotifyCc'];
			}
				
			if($eeSFL_Settings['NotifyBcc']) {
				$eeSFL_Headers .= PHP_EOL . "BCC:" . $eeSFL_Settings['NotifyBcc'];
			}
			
			if($eeSFL_Settings['NotifySubject']) {
				$eeSFL_Subject = stripslashes( $eeSFL_Settings['NotifySubject'] );
			} else {
				$eeSFL_Subject = __('File Upload Notice', 'ee-simple-file-list');
			}
				
			if(strpos($eeTo, '@') ) {
				
				if(wp_mail($eeTo, $eeSFL_Subject, $eeSFL_Body, $eeSFL_Headers)) { // SEND IT
					
					$eeSFL_FREE_Log['RunTime'][] = 'Notification Email Sent';
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
			}
			
			$eeOutput .= '<label for="eeSFL_Comments">' . __('Description', 'ee-simple-file-list') . ':</label>';
			
			$eeOutput .= '<textarea placeholder="' . __('Add a description (optional)', 'ee-simple-file-list') . '" name="eeSFL_Comments" id="eeSFL_Comments" rows="5" cols="64" maxlength="5012"></textarea>';
			
			if($eeEmail) { $eeOutput .= '<p>' . __('Submitter:', 'ee-simple-file-list') . ' ' . $eeName . ' (' . $eeEmail . ')</p>'; }
			
			if($eeEmail) {
				$eeOutput .= '<input type="hidden" id="eeSFL_Name" name="eeSFL_Name" value="' . $eeName . '" />
					<input type="hidden" id="eeSFL_Email" name="eeSFL_Email" value="' . $eeEmail . '" />';
			}
			
			$eeOutput .= '</div>';
			
		return $eeOutput;
	
	}
		
	
	
	public function eeSFL_WriteLogData() {
		
		global $eeSFL_FREE_Log, $eeSFL_FREE_DevMode, $eeSFL_FREE_Env, $eeSFL_Settings, $eeSFL_Files;
		
		$eeDate = date('Y-m-d:h:m');
		
		$eeLogNow = get_option('eeSFL_FREE_Log'); // Stored as an array
		
		// Log Size Management
		$eeSizeCheck = serialize($eeLogNow);
		if(strlen($eeSizeCheck) > 65535) { // Using TEXT Limit, even tho options are LONGTEXT.
			$eeLogNow = array(); // Clear
		}
		
		$eeLogNow[$eeDate][] = $eeSFL_FREE_Log;
		
		update_option('eeSFL_FREE_Log', $eeLogNow, FALSE);
		
		// Add development info for display
		if($eeSFL_FREE_DevMode AND current_user_can('administrator') ) {
			
			$eeOutput = '<hr /><pre>Runtime Log ' . print_r($eeSFL_FREE_Log, TRUE) . '</pre><hr />';
			
			if(@$_REQUEST) { $eeOutput .= '<pre>REQUEST ' . print_r($_REQUEST, TRUE) . '</pre><hr />'; }
			
			$eeOutput .= '<pre>Environment ' . print_r($eeSFL_FREE_Env, TRUE) . '</pre><hr />';
			
			if( isset($eeSFL_Settings['NotifyMessage']) ) {
				$eeSFL_Settings['NotifyMessage'] = substr($eeSFL_Settings['NotifyMessage'], 0, 9) . ' ...' ; // For Readability
			}
			$eeOutput .= '<pre>Settings ' . print_r($eeSFL_Settings, TRUE) . '</pre><hr />';
			
			if(isset($eeSFL_Files)) { $eeOutput .= '<pre>Files ' . print_r($eeSFL_Files, TRUE) . '</pre><hr />'; } // Items that were displayed
		
			return $eeOutput;
		}
	}

	
} // END Class 

?>