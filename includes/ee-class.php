<?php // Simple File List Script: ee-class.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Class' ) ) exit('ERROR 98'); // Exit if nonce fails

class eeSFL_BASE_MainClass {
			
	// Basics
	// public $eePluginName = 'Simple File List';
	// public $eePluginNameSlug = 'simple-file-list';
	// public $eePluginSlug = 'ee-simple-file-list';
	// public $eePluginMenuTitle = 'File List';
	// public $eePluginWebPage = 'https://simplefilelist.com';
	// public $eeAllFilesSorted = array();
	// public $eeUseCache = 1; // Hours
    
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
    
    // private $eeExcludedFiles = array('index.html');
    
    public $eeNotifyMessageDefault = 'Greetings,' . PHP_EOL . PHP_EOL . 
    	'You should know that a file has been uploaded to your website.' . PHP_EOL . PHP_EOL . 
    		
    		'[file-list]' . PHP_EOL . PHP_EOL . 
    		
    		'File List: [web-page]' . PHP_EOL . PHP_EOL;
    		
    
    
    // Default Vaules
	public $eeDefaultUploadLimit = 99;
	public $eeFileThumbSize = 256;
    public $eeListRun = 1; // Count of lists per page
    public $eeUploadFormRun = FALSE; // Check if uploader form has run or not
    
    // The Log - Written to wp_option -> eeSFL-Log
    public $eeLog = array(eeSFL_BASE_Go => array(
	    
	    'Log' => '0.000s | 0 MB Simple File List is Loading...',
	    'Version' => 'SFL FREE: ' . eeSFL_BASE_Version,
	    'errors' => array(),
	    'warnings' => array(),
	    'messages' => array(),
	    'notice' => array()
    ));
    
    
    // Settings for the Current List
    public $eeListSettings = array();
    
    // The Default List Definition
    public $DefaultListSettings = array( // An array of file list settings

		// List Settings
		'ListTitle' => 'Simple File List', // List Title (Not currently used)
		'FileListDir' => 'wp-content/uploads/simple-file-list/', // List Directory Name (relative to ABSPATH)
		'ShowList' => 'YES', // Show the File List (YES, ADMIN, USER, NO)
		'ShowListStyle' => 'Table', // Table, Tiles, or Flex
		'ShowListTheme' => 'Light', // Light, Dark or None
		'AdminRole' => 5, // Who can access settings, based on WP role (5 = Admin ... 1 = Subscriber)
		'ShowFileThumb' => 'YES', // Display the File Thumbnail (YES or NO)
		'ShowFileDate' => 'YES', // Display the File Date (YES or NO)
		'ShowFileDateAs' => 'Added', // Which date to show: added or modified
		'ShowFileSize' => 'YES', // Display the File Size (YES or NO)
		'ShowFileDesc' => 'YES', // Display the File Description (YES or NO)
		'LabelThumb' => 'Thumb', // Label for the thumbnail
		'LabelName' => 'Name', // Label for the file name
		'LabelDate' => 'Date', // Label for the file date
		'LabelSize' => 'Size', // Label for the file size
		'LabelDesc' => 'Description', // Label for the file description
		'LabelOwner' => 'Submitter', // Label for the file owner
		'SortBy' => 'DateChanged', // Sort By (Name, Date, DateChanged, Size, Random) -- DateChanged added in 4.3
		'SortOrder' => 'Descending', // Descending or Ascending
		
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
		'ShowSubmitterInfo' => 'NO', // Show who uploaded the file (name linked to their email)
		'AllowFrontManage' => 'NO', // Allow front-side users to manage files (YES or NO)
		'SmoothScroll' => 'YES', // Use the awesome and cool JavaScript smooth scroller after an upload
		
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
		
	);
	
	
	
	// Get Settings for Specified List
    public function eeSFL_GetSettings($eeListID) {
	    
	    if(is_numeric($eeListID)) {

		    // Getting the settings array
		    $this->eeListSettings = get_option('eeSFL_Settings_' . $eeListID);
		    
		    if(!is_array($this->eeListSettings)) {
				
				$this->eeLog[eeSFL_BASE_Go]['warnings'][] = 'No Settings Found. Restoring the defaults ...';
				update_option('eeSFL_Settings_' . $this->eeListID, $this->eeDefaultListSettings); // The settings are gone, so reset to defaults.
				$this->eeListSettings = $this->eeDefaultListSettings;
			}
			    
		    $this->eeListSettings['FileListURL'] = $this->eeEnvironment['wpSiteURL'] . $this->eeListSettings['FileListDir']; // The Full URL
				
			ksort($this->eeListSettings);
				
			return $this->eeListSettings;
		
		} else {
			
			$this->eeListSettings = array();
		}
	}
	
    
    
    // Environment Details
	public $eeEnvironment = array(); 
	
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
		
		$eeEnv['pluginURL'] = plugins_url() . '/' . eeSFL_BASE_PluginDir . '/';
		$eeEnv['pluginDir'] = WP_PLUGIN_DIR . '/' . eeSFL_BASE_PluginDir . '/';
		
		$wpUploadArray = wp_upload_dir();
		$wpUploadDir = $wpUploadArray['basedir'];
		$eeEnv['wpUploadDir'] = $wpUploadDir . '/'; // The Wordpress Uploads Location
		$eeEnv['wpUploadURL'] = $wpUploadArray['baseurl'] . '/';

		$eeEnv['FileListDefaultDir'] = str_replace(ABSPATH, '', $eeEnv['wpUploadDir'] . eeSFL_BASE_FileListDefaultDir); // The default file list location
		
		$eeEnv['php_version'] = phpversion(); // PHP Version
		
		$eeEnv['php_max_execution_time'] = ini_get('max_execution_time');
		
		$eeEnv['php_memory_limit'] = ini_get('memory_limit');
		
		$eeEnv['the_max_upload_size'] = eeSFL_BASE_ActualUploadMax();
		
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
		
		$this->eeEnvironment = $eeEnv;
    }
	
	
	public $eeAllFiles = array();
	
	public $eeIsFile = FALSE;
	public $eeFilePath = FALSE;
	public $eeFileURL = FALSE;
	public $eeFileName = FALSE;
	public $eeFileExt = FALSE;
	public $eeRealFileName = FALSE;
	public $eeFileNiceName = FALSE;
	public $eeFileDescription = FALSE;
	public $eeFileThumbURL = FALSE;
	public $eeFileDateAdded = FALSE;
	public $eeFileDateChanged = FALSE;
	public $eeFileDate = FALSE;
	public $eeFileSize = FALSE;
	public $eeFileOwner = FALSE;
	public $eeFileSubmitterEmail = FALSE;
	public $eeFileSubmitterName = FALSE;
	public $eeFileSubmitterComments = FALSE;
	public $eeFileCount = 0;
	
	
	 // Default File List Definition
    public $eeFileTemplate = array(
	    
		0 => array( // The File ID (We copy this to the array on-the-fly when sorting)
			// 'FileList' => 1, // The ID of the File List, contained in the above array.
		    'FilePath' => '', // Path to file, relative to the list root
		    'FileExt' => '', // The file extension
		    'FileMIME' => '', // The MIME Type
			'FileSize' => '', // The size of the file
			'FileDateAdded' => '', // Date the file was added to the list
			'FileDateChanged' => '', // Last date the file was renamed or otherwise changed
			'FileDescription' => '', // A short description of the file
			'FileNiceName' => '', // A name that will replace the actual file name
			'FileOwner' => '', // The logged-in user who added the file
			'SubmitterName' => '', // Who uploaded the file
			'SubmitterEmail' => '', // Their email
			'SubmitterComments' => '', // What they said
		)
    );
    
    
    
    
    public function eeSFL_ReturnFileActions($eeFileID) {;
		
		$eeAdmin = is_admin();
		
		$eeOutput = '
		
		<small class="eeSFL_ListFileActions">';
			
		// Open Action
		if($eeAdmin OR $this->eeListSettings['ShowFileOpen'] == 'YES') {
		
			if(in_array($this->eeFileExt, $this->eeOpenableFileFormats)) {
				$eeOutput .= '
				<a class="eeSFL_FileOpen" href="' . $this->eeFileURL . '" target="_blank">' . __('Open', 'ee-simple-file-list') . '</a>';
			}
		}
		
		// Download Action
		if($eeAdmin OR $this->eeListSettings['ShowFileDownload'] == 'YES') {
		
			$eeOutput .= '
			<a class="eeSFL_FileDownload" href="' . $this->eeFileURL . '" download="' . basename($this->eeFileURL) . '">' . __('Download', 'ee-simple-file-list') . '</a>';
		
		}
		
		// Copy Link Action
		if($eeAdmin OR $this->eeListSettings['ShowFileCopyLink'] == 'YES') {
			
			$eeOutput .= '
			<a class="eeSFL_CopyLinkToClipboard" onclick="eeSFL_BASE_CopyLinkToClipboard(\''  . $this->eeFileURL .   '\')" href="#">' . __('Copy Link', 'ee-simple-file-list') . '</a>';														
		
		}
		
		// Front-End Manage or Admin
		if( ($eeAdmin OR $this->eeListSettings['AllowFrontManage'] == 'YES') AND $this->eeListRun == 1) {							
			
			// <span class="eeSFL_FileManageLinks">
			
			$eeOutput .= '
			<a href="#" onclick="eeSFL_BASE_OpenEditModal(' . $eeFileID . ')">' . __('Edit', 'ee-simple-file-list') . '</a>
			<a href="#" onclick="eeSFL_BASE_DeleteFile(' . $eeFileID . ')">' . __('Delete', 'ee-simple-file-list') . '</a>';
			
			if($eeAdmin) {
			
				$eeOutput .= '
				 <a class="eeDisabledAction" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Move', 'ee-simple-file-list') . '</a>
				 <a class="eeDisabledAction" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Users', 'ee-simple-file-list') . '</a>
				 <a class="eeDisabledAction" href="' . admin_url() . 'admin.php?page=ee-simple-file-list&tab=pro" >' . __('Send', 'ee-simple-file-list') . '</a>';
			}
			
			// $eeOutput .= '<span>';
		
		}
				
		$eeOutput .= '
		
		</small>'; // Close File List Actions Links
		
		// File Details to Pass to the Editor
		$eeOutput .= '
		
		<span class="eeHide eeSFL_FileSize">' . $this->eeFileSize . '</span>
		<span class="eeHide eeSFL_FileDateAdded">' . $this->eeFileDateAdded . '</span>
		<span class="eeHide eeSFL_FileDateChanged">' . $this->eeFileDateChanged . '</span>';
		
		return $eeOutput;
	    
    }
    
    
    
    public function eeSFL_ProcessFileArray($eeFileArray, $eeHideName = FALSE, $eeHideType = FALSE) {
	    
	    if( is_admin() ) { $eeAdmin = TRUE; } else { $eeAdmin = FALSE; }
	    
	    if( is_array($eeFileArray) ) {
			
			// Deny Folder Listing
			if(strpos($eeFileArray['FilePath'], '/')) { return FALSE; }

			// Assign values to our properties
			
			// The File Name
			$this->eeFilePath = $eeFileArray['FilePath']; // Path relative to FileListDir
			$this->eeFileName = basename($eeFileArray['FilePath']); // This name might change
			$this->eeRealFileName = $this->eeFileName; // Never changed
			$this->eeFileExt = basename($eeFileArray['FileExt']); // Just the name
			$this->eeFileURL = $this->eeEnvironment['wpSiteURL'] . $this->eeListSettings['FileListDir'] . $this->eeFilePath; // Clickable URL
			$this->eeFileSize = eeSFL_BASE_FormatFileSize($eeFileArray['FileSize']); // Formatted Size
			
			// Reset These
			$this->eeFileNiceName = FALSE;
			$this->eeFileDescription = FALSE;
			$this->eeSubmitterComments = FALSE;
			$this->eeFileOwner = FALSE;
			$this->eeFileSubmitterEmail = FALSE;
			$this->eeFileSubmitterName = FALSE;
			$this->eeFileSubmitterComments = FALSE;
			
			// Must Be a File
			if(strpos($this->eeFilePath, '.')) { // Skip folders and hidden files
			
				// Skip names hidden via shortcode
				if($eeHideName) { // Expecting a comma deleimited string of file names
					$eeArray = explode(',', $eeHideName);
					foreach( $eeArray as $eeKey => $eeValue ) {
						if( strtolower($eeFileName) ==  $eeValue . '.' . $this->eeFileExt ) {  return FALSE; } // Without extension
						if($eeValue == strtolower($this->eeFileName)) { return FALSE; } // With extension
					}
				}
				
				// Skip types hidden via shortcode
				if($eeHideType) { // Expecting a comma deleimited string of extensions
					if(strpos($eeHideType, $this->eeFileExt) OR strpos($eeHideType, $this->eeFileExt) === 0 ) { 
						return FALSE;
					}
				}
				
				$this->eeIsFile = TRUE;
				
				// Thumbnail
				$eeHasCreatedThumb = FALSE;
				if( in_array($this->eeFileExt,  $this->eeDynamicImageThumbFormats) AND $this->eeListSettings['GenerateImgThumbs'] == 'YES' ) { $eeHasCreatedThumb = TRUE; }
				if( in_array($this->eeFileExt,  $this->eeDynamicVideoThumbFormats) AND isset($this->eeEnvironment['ffMpeg']) AND $this->eeListSettings['GenerateVideoThumbs'] == 'YES' ) { $eeHasCreatedThumb = TRUE; }
				if( $this->eeFileExt == 'pdf' AND isset($this->eeEnvironment['ImkGs']) AND $this->eeListSettings['GeneratePDFThumbs'] == 'YES' ) { $eeHasCreatedThumb = TRUE; }
				
				if($eeHasCreatedThumb) { // Images use .jpg files
	
					$eePathParts = pathinfo($this->eeFilePath);
					$eeFileThumbURL = $this->eeListSettings['FileListURL'];
					if($eePathParts['dirname']) { $eeFileThumbURL .= $eePathParts['dirname'] . '/'; }
					$this->eeFileThumbURL = $eeFileThumbURL . '.thumbnails/thumb_' . $eePathParts['filename'] . '.jpg';
	
				} else { // Others use our awesome .svg files
					
					if( !in_array($this->eeFileExt, $this->eeDefaultThumbFormats) ) { $eeDefaultThumb = '!default.svg'; } // What the heck is this? 
						else { $eeDefaultThumb = $this->eeFileExt . '.svg'; } // Use our sweet icon
					
					$this->eeFileThumbURL = $this->eeEnvironment['pluginURL'] . 'images/thumbnails/' . $eeDefaultThumb;
				}
				
				
				
				// File Nice Name
				if( isset($eeFileArray['FileNiceName']) ) {
					if( strlen($eeFileArray['FileNiceName']) >= 1 ) {
						$this->eeFileNiceName = $eeFileArray['FileNiceName'];
						$this->eeFileName = $eeFileArray['FileNiceName'];
					}
				}
				
				if($this->eeFileNiceName === FALSE) {
					
					// Strip the Extension?
					if(!$eeAdmin AND $this->eeListSettings['ShowFileExtension'] == 'NO') {
						$eePathParts = pathinfo($this->eeRealFileName);
						$this->eeFileName = $eePathParts['filename'];
					}
					
					// Replace hyphens with spaces?
					if(!$eeAdmin AND $this->eeListSettings['PreserveSpaces'] == 'YES') {
						$this->eeFileName = eeSFL_BASE_PreserveSpaces($this->eeRealFileName); 
					}
				}
				
				if( isset($eeFileArray['FileDescription']) ) {
					$this->eeFileDescription = $eeFileArray['FileDescription'];
				}
				
				// File Description
				if( isset($eeFileArray['SubmitterComments']) ) { 
					if(!$this->eeFileDescription) {
						$this->eeFileDescription = $eeFileArray['SubmitterComments']; // Show the submitter comment if no desc
						$this->eeFileSubmitterComments = $eeFileArray['SubmitterComments']; // Use on back-end
					}
				}
				
				
				
				// File Dates and the Display Date
				if($this->eeListSettings['ShowFileDateAs'] == 'Changed') {
					$this->eeFileDateChanged = date_i18n( get_option('date_format'), strtotime( $eeFileArray['FileDateChanged'] ) ); // The mod date
					$this->eeFileDate = $this->eeFileDateChanged;
				} else {
					$this->eeFileDateAdded = date_i18n( get_option('date_format'), strtotime( $eeFileArray['FileDateAdded'] ) );
					$this->eeFileDate = $this->eeFileDateAdded;
				}
				
				
				
				// Submitter Info
				if( isset($eeFileArray['FileOwner']) ) { 
					if(is_numeric($eeFileArray['FileOwner'])) {
						$this->eeFileOwner = $eeFileArray['FileOwner']; // The User ID
						$wpUserData = get_userdata($this->eeFileOwner);
						if($wpUserData->user_email) {
							$this->eeFileSubmitterEmail = $wpUserData->user_email;
							$this->eeFileSubmitterName = $wpUserData->first_name . ' ' . $wpUserData->last_name;
						}
					}
				
				} elseif(isset($eeFileArray['SubmitterName'])) {
					
					$this->eeFileSubmitterName = $eeFileArray['SubmitterName'];
					$this->eeFileSubmitterEmail = $eeFileArray['SubmitterEmail'];
					
				}
				
				$this->eeFileCount++; // Bump the file count
			
			} else {
				return FALSE; // Not an item we want to display
			}
		}
	    
	    return TRUE;
	}
	
	
	
	// Build a New File/Folder Array (for an upload or new file found)
	public function eeSFL_BuildFileArray($eeFilePath) { // Path relative to ABSPATH
		
		$eePathParts = pathinfo($eeFilePath);
		
		if( is_readable(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFilePath) ) {
		
			$eeFileArray = $this->eeSFL_Files[0]; // Get the file array template
			$eeFileArray['FilePath'] = $eeFilePath; // Path to file, relative to the list root
			
			if(isset($eePathParts['extension'])) { $eeExt = strtolower($eePathParts['extension']); } else { $eeExt = 'folder'; }
			$eeFileArray['FileExt'] = $eeExt; // The file extension 
			
			if(function_exists('mime_content_type')) {
				$eeFileArray['FileMIME'] = mime_content_type(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFilePath); // MIME Type
			}
			
			$eeFileArray['FileSize'] = filesize(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFilePath);
			
			$eeFileArray['FileDateAdded'] = date("Y-m-d H:i:s");
			$eeFileArray['FileDateChanged'] = date("Y-m-d H:i:s", filemtime(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFilePath));
			
			if( strlen($eeFileArray['FilePath']) ) { // 02/21 - If FilePath is empty, sort doesn't work? But why would that be empty.
				return $eeFileArray;
			}
		
		}
		
		return FALSE;
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
	    
	    $this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Calling Method: eeSFL_UpdateFileListArray()';
	    $this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Scanning File List...';
	    
	    // Get the File List Array
	    $eeFilesDBArray = get_option('eeSFL_FileList_1');
	    if(!is_array($eeFilesDBArray)) { $eeFilesDBArray = array(); }
	    
	    // List the actual files on the disk
	    $eeFilePathsArray = $this->eeSFL_IndexFileListDir($this->eeListSettings['FileListDir']);
	    
	    if(!count($eeFilePathsArray)) {
		    $this->eeLog[eeSFL_BASE_Go]['notice'][] = 'No Files Found';
		    return FALSE; // Quit and leave DB alone
	    }
	    
	    // Create an array we'll fill with files
	    $eeFileArrayWorking = array();
	    
	    // No List in the DB, Creating New...
	    if( !count($eeFilesDBArray) ) {
			
			$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'No List Found! Creating from scratch...';
			
			foreach( $eeFilePathsArray as $eeKey => $eeFile) {
				
				$eePathParts = pathinfo($eeFile);
				
				// Add it to the array
				$eeFileArrayWorking[] = array(
					'FilePath' => $eeFile
					,'FileExt' => strtolower($eePathParts['extension'])
					,'FileSize' => filesize(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFile)
					,'FileDateAdded' => date("Y-m-d H:i:s")
					,'FileDateChanged' => date("Y-m-d H:i:s", filemtime(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFile))
				);
				
				if(function_exists('mime_content_type')) {
					$eeFileArrayWorking[]['FileMIME'] = mime_content_type(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFile); // MIME Type
				}				
			}
		
		} else { // Update file info
			
			$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Updating Existing List...';
			
			$eeFileArrayWorking = $eeFilesDBArray; // Fill it up with current files
			
			// Check to be sure each file is there...
			foreach( $eeFileArrayWorking as $eeKey => $eeFileSet) {
				
				// Build full path
				$eeFileFullPath = ABSPATH . $this->eeListSettings['FileListDir'] . $eeFileSet['FilePath'];
				
				if( !is_file($eeFileFullPath) ) { // Get rid of it
					
					$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Removing: ' . $eeFileFullPath;
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
				
				$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Checking File: ' . $eeFile;
				
				$eeFound = FALSE;
				
				// Look for this file in our array
				foreach( $eeFileArrayWorking as $eeKey2 => $eeThisFileArray ) {
					
					if($eeFile == $eeThisFileArray['FilePath']) { // Found this file, on to the next.
						
						$eeFileArrayWorking[$eeKey2] = array_merge($this->eeFileTemplate[0], $eeThisFileArray); // Add all of the string keys
						$eeFound = TRUE;
						break;
					}
				}
				
				if($eeFound === FALSE) {
					
					$this->eeLog[eeSFL_BASE_Go]['notice'][] = '!!! New File Found: ' . $eeFile;
					
					$eePathParts = pathinfo($eeFile);
					
					// Add it to the array
					$eeFileArrayWorking[] = array(
						'FilePath' => $eeFile
						,'FileExt' => strtolower($eePathParts['extension'])
						,'FileSize' => filesize(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFile)
						,'FileDateAdded' => date("Y-m-d H:i:s")
						,'FileDateChanged' => date("Y-m-d H:i:s", filemtime(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFile))
					);
				
					if(function_exists('mime_content_type')) {
						$eeFileArrayWorking['FileMIME'][] = mime_content_type(ABSPATH . $this->eeListSettings['FileListDir'] . $eeFile); // MIME Type
					}
				}
			}
		}
		
		
		// Sort...
		if(count($eeFileArrayWorking)) {
			
			// Sort
		    $eeFileArrayWorking = $this->eeSFL_SortFiles($eeFileArrayWorking, $this->eeListSettings['SortBy'], $this->eeListSettings['SortOrder']);

			
			// Update the DB
		    update_option('eeSFL_FileList_1', $eeFileArrayWorking);
		    
		    // Check for and create thumbnail if needed...
		    if( $this->eeListSettings['ShowFileThumb'] == 'YES' ) {
			    
			    // Check for supported technologies
				eeSFL_BASE_CheckSupported();
						
				$eeSFL_Log['notice'][] = 'Checking Thumbnails ...';
		    
				// Check for and create thumbnail if needed...
			    foreach($eeFileArrayWorking as $eeKey => $eeFile) {
			    	
			    	if( ($eeFile['FileExt'] == 'pdf' AND $this->eeListSettings['GeneratePDFThumbs'] == 'YES')
			    		OR (in_array($eeFile['FileExt'], $this->eeDynamicImageThumbFormats) AND $this->eeListSettings['GenerateImgThumbs'] == 'YES')
							OR (in_array($eeFile['FileExt'], $this->eeDynamicVideoThumbFormats) AND $this->eeListSettings['GenerateVideoThumbs'] == 'YES') ) {
						
								$this->eeSFL_CheckThumbnail($eeFile['FilePath'], $this->eeListSettings);	
					}
			    }
			    
		    } else {
			    $eeSFL_Log['notice'][] = 'Not Showing Thumbnails';
			    
		    }
		    
		    // Check for Enviroment Changes
		    $eeActual = eeSFL_BASE_ActualUploadMax();
			if( $this->eeListSettings['UploadMaxFileSize'] > $eeActual ) { 
				$this->eeListSettings['UploadMaxFileSize'] = $eeActual;
				update_option('eeSFL_Settings_' . $eeSFL_ID, $this->eeListSettings); // Set to Actual Max
			} 
		
		}
		    
		return $eeFileArrayWorking; // Return empty or not
    }
	
	
	
	
	// Get All the Files
	private function eeSFL_IndexFileListDir($eeFileListDir) {
	    
	    $eeFilesArray = array();
	    
	    if(!is_dir(ABSPATH . $eeFileListDir)) {
		    
		    $this->eeLog[eeSFL_BASE_Go]['errors'][] = 'The directory is Gone :-0  Re-Creating...';
		    
		    eeSFL_BASE_FileListDirCheck($eeFileListDir);
		    return $eeFilesArray;
	    }
		    
	    $this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Getting files from: ' . $eeFileListDir; 
	    
	    $eeFileNameOnlyArray = scandir(ABSPATH . $eeFileListDir);
	    
	    foreach($eeFileNameOnlyArray as $eeValue) {
	    	
	    	$eePathParts = pathinfo($eeValue);
			if(isset($eePathParts['extension'])) {
				if( in_array($eePathParts['extension'], $this->eeForbiddenTypes) ) { $eeValue = '.forbidden'; }
			}
	    	
	    	if(strpos($eeValue, '.') !== 0 ) { // Not hidden
		    	
		    	if(is_file(ABSPATH . $eeFileListDir . $eeValue)) { // Is a regular file
		    	
			    	if(!in_array($eeValue, $this->eeExcludedFileNames) )  { // Not excluded
				    	
				    	$eeNewItem = eeSFL_BASE_SanitizeFileName($eeValue);
			            if($eeNewItem != $eeValue) {
				            
				            $this->eeLog[eeSFL_BASE_Go]['Trouble'][] = 'OLD --> BAD File Name: ' . $eeValue;
				            
				            if(rename(ABSPATH . $eeFileListDir . $eeValue, ABSPATH . $eeFileListDir . $eeNewItem)) {
					        	
					        	$eeValue = $eeNewItem;
								$this->eeLog[eeSFL_BASE_Go]['Trouble'][] = 'NEW --> File Name Sanitized: ' . $eeValue;
				        	}
			            }
			            
				    	$eeFilesArray[] = $eeValue; // Add the path
			    	}
		    	}
	    	}
	    }
	    
	    if(!count($eeFilesArray)) {
		    $this->eeLog[eeSFL_BASE_Go]['notice'][] = 'No Files Found';
	    }

		return $eeFilesArray;
	}
	
	
	
	// Move, Rename or Delete a thumbnail - Expects path relative to FileListDir
	public function eeSFL_UpdateThumbnail($eeFileFrom, $eeFileTo) {
		
		$this->eeListSettings = $this->eeSFL_GetSettings(1); // Get this list
		
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
				
				$eeThumbFrom = ABSPATH . $this->eeListSettings['FileListDir'];
				
				if($eePathPartsFrom['dirname'] != '.') { $eeThumbFrom .= $eePathPartsFrom['dirname']; }
				
				$eeThumbFrom .= '/.thumbnails/thumb_' . basename($eeFileFrom);
				
				if( is_file($eeThumbFrom) ) {
					
					if(!$eeFileTo) { // Delete the thumb
						
						if(unlink($eeThumbFrom)) {
							
							$eeSFL_Log['notice'][] = 'Deleted Thumbnail For: ' . basename($eeFileFrom);
							
							return;
						}
					
					} else { // Move / Rename
						
						$eePathPartsTo = pathinfo($eeFileTo);
						
						$eeThumbTo = ABSPATH . $this->eeListSettings['FileListDir'] . $eePathPartsTo['dirname'] . '/.thumbnails/thumb_' . basename($eeFileTo);
						
						if(rename($eeThumbFrom, $eeThumbTo)) { // Do nothing on failure
						
							$eeSFL_Log['notice'][] = 'Thumbnail Updated For: ' . basename($eeFileFrom);
							
							return;
						}
					}
				}
			}
		}
	}
	
	
	
	
	
	// Check Thumbnail and Create if Needed
	public function eeSFL_CheckThumbnail($eeFilePath) { // Expects FilePath relative to FileListDir & the List's Settings Array
		
		$eePathParts = pathinfo($eeFilePath);
		$eeFileNameOnly = $eePathParts['filename'];
		$eeFileExt = $eePathParts['extension'];
		$eeFileSubPath = $eePathParts['dirname'] . '/';
		$eeFileFullPath = ABSPATH . $this->eeListSettings['FileListDir'] . $eeFilePath;
		$eeThumbsPath = ABSPATH . $this->eeListSettings['FileListDir'] . $eeFileSubPath . '.thumbnails/';
		$eeThumbFileToCheck = 'thumb_' . $eeFileNameOnly . '.jpg';
		
		// Check for the .thumbnails directory
		if( !is_dir($eeThumbsPath) ) {
			if( !mkdir($eeThumbsPath) ) { 
				$this->eeLog[eeSFL_BASE_Go]['notice'][] = '!!!! Cannot create the .thumbnails directory: ' . $eeThumbsPath;
				return FALSE;
			}
		}
		
		// Is there already a thumb?
		if(is_file($eeThumbsPath . $eeThumbFileToCheck)) {
			return TRUE; // Checked Okay
		}
		
		
		
		// Else We Generate ...
		$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Missing: thumb_' . $eeFileNameOnly . '.jpg';
		
		// Image Files
		if(in_array($eeFileExt, $this->eeDynamicImageThumbFormats) AND $this->eeListSettings['GenerateImgThumbs'] == 'YES') { // Just for known image files... 
			
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
				$this->eeLog[eeSFL_BASE_Go]['errors'][] = '!!!! ' . __('Corrupt Image File Deleted', 'ee-simple-file-list') . ': ' . basename($eeFileFullPath);
				return FALSE;
			}
		}
		
		
		// Video Files
		if(in_array($eeFileExt, $this->eeDynamicVideoThumbFormats) AND $this->eeListSettings['GenerateVideoThumbs'] == 'YES' AND isset($this->eeEnvironment['ffMpeg']) ) {
			
			$this->eeSFL_CreateVideoThumbnail($eeFileFullPath); // Create a temp image, then a thumb from that using $this->eeSFL_CreateThumbnailImage()
		}
		
		
		// PDF Files
		if($eeFileExt == 'pdf' AND $this->eeListSettings['GeneratePDFThumbs'] == 'YES' AND isset($this->eeEnvironment['ImkGs']) ) {
			
			if($this->eeSFL_CreatePDFThumbnail($eeFileFullPath)) {
				return TRUE;
			}
		}
	}
	
	
	
	
	// Create Image Thumbnail
	private function eeSFL_CreateThumbnailImage($eeInputFileCompletePath) { // Expects Full Path
		
		if(!is_file($eeInputFileCompletePath)) {
			$this->eeLog[eeSFL_BASE_Go]['notice'][] = '!!!! Source File Not Found';
			return FALSE;
		}
		
		$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Creating Thumbnail Image for ' . basename($eeInputFileCompletePath);
		
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
        
        if($eeImageMemoryNeeded > $eeImageSizeLimit) { // It's too big for WordPress
			
			if( strpos($eeFileNameOnly, 'temp_') === 0 ) { // These are PDF thumbs
				$eeDefaultThumbIcon = $this->eeEnvironment['pluginDir'] . 'images/thumbnails/!default_pdf.jpg';
			} else {
				$eeDefaultThumbIcon = $this->eeEnvironment['pluginDir'] . 'images/thumbnails/!default_image.jpg';
			}
			
			$eeFileNameOnly = str_replace('temp_', '', $eeFileNameOnly); // Strip the temp term if needed
			$eeNewThumb = $eeThumbsPath . 'thumb_' . $eeFileNameOnly . '.jpg';
		
			copy($eeDefaultThumbIcon, $eeNewThumb); // Use our default image file icon
			
			$this->eeLog[eeSFL_BASE_Go]['warnings'][] = 'Image was too large. Default thumbnail will be used for: ' . basename($eeInputFileCompletePath);
			
			return TRUE;
		
		} else { // Create thumbnail

			// Thank WordPress for this easyness.
			$eeFileImage = wp_get_image_editor($eeInputFileCompletePath); // Try to open the file
	        
	        if (!is_wp_error($eeFileImage)) { // Image File Opened
	            
	            $eeFileImage->resize($this->eeFileThumbSize, $this->eeFileThumbSize, TRUE); // Create the thumbnail
	            
	            $eeFileNameOnly = str_replace('temp_', '', $eeFileNameOnly); // Strip the temp term 
	            
	            $eeFileImage->save($eeThumbsPath . 'thumb_' . $eeFileNameOnly . '.jpg'); // Save the file
			
				$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Thumbnail Created.';
	            
	            return TRUE;
	        
	        } else { // Cannot open
		        
		        $this->eeLog[eeSFL_BASE_Go]['warnings'][] = 'Bad Image File Deleted: ' . basename($eeInputFileCompletePath);
		        
		        return FALSE;
	        }
		}
		
		return FALSE;
	}
	
	
	
	
	private function eeSFL_CreateVideoThumbnail($eeFileFullPath) { // Expects Full Path
		
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
				$this->eeLog[eeSFL_BASE_Go]['warnings'][] = 'ffMpeg could not create a screenshot for ' . basename($eeScreenshot);
				return FALSE;
			}
		}
		
		$this->eeLog[eeSFL_BASE_Go]['notice'][] = '!!!! There is no .thumbnails directory: ' . $eeThumbsPath;
		
		return FALSE;
	}
	
	
		
	
	// Generate PDF Thumbnails
	private function eeSFL_CreatePDFThumbnail($eeFileFullPath) { // Expects Full Path
		
		$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Generating PDF Thumbnail...';
		
		$eePathParts = pathinfo($eeFileFullPath);
		$eeFileNameOnly = $eePathParts['filename'];
		$eeFileExt = $eePathParts['extension'];
		$eeCompleteDir = $eePathParts['dirname'] . '/';
		$eeThumbsPath = $eeCompleteDir . '.thumbnails/';
		$eeTempFile = 'temp_' . $eeFileNameOnly . '.jpg'; // The converted pdf file - A temporary file
		$eeTempFileFullPath = $eeThumbsPath . $eeTempFile;
		
		if($eeFileExt != 'pdf') { return FALSE; }
		
		if( isset($this->eeEnvironment['ImkGs']) ) {
		
			// $this->eeLog[eeSFL_BASE_Go]['notice'][] = 'ImageMagik & GhostScript is Installed';
			
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
				
				$this->eeLog[eeSFL_BASE_Go]['GhostScript'][] = $eeCommand;
				$this->eeLog[eeSFL_BASE_Go]['GhostScript'][] = $eeCommandOutput;
				$this->eeLog[eeSFL_BASE_Go]['GhostScript'][] = $eeReturnVal;
				
				if($eeReturnVal === 0) { // Zero == No Errors
					
					// The command. AVOID LINE BREAKS
					$eeCommand = 'gs -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=' . $eeQuality . ' -dQFactor=' . $eeQFactor . ' -r' . $eeResolution . ' -dFirstPage=1 -dLastPage=1 -sOutputFile=' . $eeTempFileFullPath . ' ' . $eeFileFullPath;

					// Run the Command. Drum roll please
					exec( $eeCommand, $eeCommandOutput, $eeReturnVal );
					
					$this->eeLog[eeSFL_BASE_Go]['GhostScript'][] = $eeCommand;
					$this->eeLog[eeSFL_BASE_Go]['GhostScript'][] = $eeCommandOutput;
					$this->eeLog[eeSFL_BASE_Go]['GhostScript'][] = $eeReturnVal;
				
				} else {
					
					$this->eeLog[eeSFL_BASE_Go]['warnings'][] = __('FILE NOT READABLE', 'ee-simple-file-list') . ': ' . basename($eeFileFullPath);
					$this->eeLog[eeSFL_BASE_Go]['notice'][] = '!!!! PDF NOT READABLE: ' . basename($eeFileFullPath);
					return FALSE;
				}
			}
				
			// Confirm the file is there
			if(is_readable($eeTempFileFullPath)) {
				
				if($this->eeSFL_CreateThumbnailImage($eeTempFileFullPath)) {
					
					$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Created the PDF Thumbnail for ' . basename($eeFileFullPath);
					
					unlink($eeTempFileFullPath); // Delete the temp PNG file
					
					return TRUE;
					
				} else {
					
					$this->eeLog[eeSFL_BASE_Go]['notice'][] = '!!!! FAILED to Create the PDF Thumbnail for ' . basename($eeFileFullPath);
					
					unlink($eeTempFileFullPath);
					
					return FALSE;
				}
			
			} elseif(is_file($eeTempFileFullPath)) {
				
				unlink($eeTempFileFullPath); // Delete the corrupt temp file;
				
				return FALSE;
			
			} else {
				
				$this->eeLog[eeSFL_BASE_Go]['notice'][] = '!!!! PDF to PNG FAILED for ' . basename($eeFileFullPath);
				
				return FALSE;
			}		
		}
		
		return FALSE;
	}
	
	
	
	
	
	
	
	// Move the sort item to the array key and then sort. Preserve the key (File ID) in a new element
	public function eeSFL_SortFiles($eeFiles, $eeSortBy, $eeSortOrder) {
		
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
		
		$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Sorting by...';
		$this->eeLog[eeSFL_BASE_Go]['notice'][] = $eeSortBy . ' > ' . $eeSortOrder;
		
		$eeFilesSorted = array();
			
		if($eeSortBy == 'Date') { // Files by Date Added (the Original)
			
			foreach($eeFiles as $eeKey => $eeFileArray) {
				
				$eeFilesSorted[ $eeFileArray['FileDateAdded'] . ' ' . $eeKey ] = $eeFileArray;
			}
			
		} elseif($eeSortBy == 'DateChanged') { // Files by Date Modified (By Customer Request)
			
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
				
				// Use Nice Name if Available
				if( strlen($eeFileArray['FileNiceName']) ) {
					$eeFileLowerCase = strtolower($eeFileArray['FileNiceName']); // Make lower case so name sorting works properly
					$eeFileLowerCase = eeSFL_BASE_SanitizeFileName($eeFileLowerCase); // Get rid of troublsome chars
				} else {
					$eeFileLowerCase = strtolower($eeFileArray['FilePath']); // Make lower case so name sorting works properly
				}
				
				$eeFilesSorted[ $eeFileLowerCase ] = $eeFileArray; // These keys shall always be unique
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
		
		// echo '<pre>'; print_r($eeFilesSorted); echo '</pre>'; exit;
		
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
		
		if($eeSFL_UploadJob) {
		
			$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Sending Notification Email...';
			
			// Build the Message Body
			$eeSFL_Body = $this->eeListSettings['NotifyMessage']; // Get the template
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
				
				$eeSFL_FileDesc = substr(sanitize_text_field(@$_POST['eeSFL_FileDesc']), 0, 5012);
				$eeSFL_FileDesc = strip_tags($eeSFL_FileDesc);
				if($eeSFL_FileDesc) {
					$eeSFL_Body .= $eeSFL_FileDesc . PHP_EOL . PHP_EOL;
				}
			}
			
			// Show if no extensions installed
			if( @count($this->eeEnvironment['installed']) < 1 OR strlen($eeSFL_Body) < 3) { // Or if no content
				
				$eeSFL_Body .= PHP_EOL . PHP_EOL . "----------------------------------"  . 
				PHP_EOL . "Powered by Simple File List - simplefilelist.com";
			}
		
			if($this->eeListSettings['NotifyFrom']) {
				$eeSFL_NotifyFrom = $this->eeListSettings['NotifyFrom'];
			} else {
				$eeSFL_NotifyFrom = get_option('admin_email');
			}
			
			if($this->eeListSettings['NotifyFromName']) {
				$eeSFL_AdminName = $this->eeListSettings['NotifyFromName'];
			} else {
				$eeSFL_AdminName = $this->eePluginName;
			}
			
			$eeTo = $this->eeListSettings['NotifyTo'];
			
			$eeSFL_Headers = "From: " . stripslashes( $this->eeListSettings['NotifyFromName'] ) . " <$eeSFL_NotifyFrom>" . PHP_EOL . 
				"Return-Path: $eeSFL_NotifyFrom" . PHP_EOL . "Reply-To: $eeSFL_NotifyFrom";
			
			if($this->eeListSettings['NotifyCc']) {
				$eeSFL_Headers .= PHP_EOL . "CC:" . $this->eeListSettings['NotifyCc'];
			}
				
			if($this->eeListSettings['NotifyBcc']) {
				$eeSFL_Headers .= PHP_EOL . "BCC:" . $this->eeListSettings['NotifyBcc'];
			}
			
			if($this->eeListSettings['NotifySubject']) {
				$eeSFL_Subject = stripslashes( $this->eeListSettings['NotifySubject'] );
			} else {
				$eeSFL_Subject = __('File Upload Notice', 'ee-simple-file-list');
			}
				
			if(strpos($eeTo, '@') ) {
				
				if(wp_mail($eeTo, $eeSFL_Subject, $eeSFL_Body, $eeSFL_Headers)) { // SEND IT
					
					$this->eeLog[eeSFL_BASE_Go]['notice'][] = 'Notification Email Sent';
					return 'SUCCESS';
					
				} else {
					
					$this->eeLog[eeSFL_BASE_Go]['errors'][] = 'Notification Email FAILED';
				}
			}
		
		}
	}
	
	
	
	
	
	// Sanitize Email Addresses
	public function eeSFL_SanitizeEmailString($eeAddresses) { // Can be one or more addresses, comma deliniated
		
		$eeAddressSanitized = '';
		
		if(strpos($eeAddresses, ',')) { // Multiple Addresses
		
			$eeSFL_Addresses = explode(',', $eeAddresses);
			
			$eeSFL_AddressesString = '';
			
			foreach($eeSFL_Addresses as $add){
				
				$add = trim($add);
				
				if(filter_var(sanitize_email($add), FILTER_VALIDATE_EMAIL)) {
			
					$eeSFL_AddressesString .= $add . ',';
					
				} else {
					$this->eeLog[eeSFL_BASE_Go]['errors'][] = $add . ' - ' . __('This is not a valid email address.', 'ee-simple-file-list');
				}
			}
			
			$eeAddressSanitized = substr($eeSFL_AddressesString, 0, -1); // Remove last comma
			
		
		} elseif(filter_var(sanitize_email($eeAddresses), FILTER_SANITIZE_EMAIL)) { // Only one address
			
			$add = $eeAddresses;
			
			if(filter_var(sanitize_email($add), FILTER_VALIDATE_EMAIL)) {
				
				$eeAddressSanitized = $add;
				
			} else {
				
				$this->eeLog[eeSFL_BASE_Go]['errors'][] = $add . ' - ' . __('This is not a valid email address.', 'ee-simple-file-list');
			}
			
		} else {
			
			$eeAddressSanitized = ''; // Anything but a good email gets null.
		}
		
		return $eeAddressSanitized;
	}

		
	
	
	public function eeSFL_WriteLogData() {
		
		global $eeSFL_BASE, $eeSFL_StartTime, $eeSFL_Tasks;
		
		// Get the current logs
		$eeAllLogs = get_option('eeSFL_TheLog'); // Stored as an array
		if(!$eeAllLogs) { $eeAllLogs = array(); } // First run
		
		// Log Size Management
		$eeSizeCheck = serialize($eeAllLogs);
		if(strlen($eeSizeCheck) > 131070) { // Using TEXT Limit, even tho options are LONGTEXT.
			$eeAllLogs = array(); // Clear
		}
		
		if( count($_REQUEST) ) {
			$this->eeLog['REQUEST'] = $_REQUEST;
		}
		
		$this->eeLog['Tasks'] = $eeSFL_Tasks;
		
		$eeTime = eeSFL_BASE_noticeTimer();
		$this->eeLog[] = 'We waited for ' . $eeSFL_StartTime . ' seconds';
		$this->eeLog[] = 'Then ran for ' . $eeTime;
		
		// Remove empty items
		$this->eeLog = array_filter($this->eeLog);
		
		update_option('eeSFL_TheLog', $this->eeLog, FALSE); // Save to the database
		
		// Add development info for display
		if(eeSFL_BASE_DevMode AND current_user_can('administrator') ) {
			
			$eeOutput = '<hr /><pre>Runtime Log ' . print_r($this->eeLog, TRUE) . '</pre><hr />';
			
			if(@$_REQUEST) { $eeOutput .= '<pre>REQUEST ' . print_r($_REQUEST, TRUE) . '</pre><hr />'; }
			
			$eeOutput .= '<pre>Environment ' . print_r($this->eeEnvironment, TRUE) . '</pre><hr />';
			
			$eeOutput .= '<pre>Settings ' . print_r($this->eeListSettings, TRUE) . '</pre><hr />';
			
			if(count($this->eeAllFiles)) { $eeOutput .= '<pre>Files ' . print_r($this->eeAllFiles, TRUE) . '</pre><hr />'; } // Items that were displayed
		
			return $eeOutput;
		}
	}

	
} // END Class 

?>