<?php
// Simple File List Pro - Copyright 2024
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code are not advised and may not be supported.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

class eeSFL_Environment {
	
	protected $eeSFL;
	public function __construct(eeSFL_MainClass $eeSFL) { $this->eeSFL = $eeSFL; }
	// Usage: $this->eeSFL->eeListID
		
		
		
	// Get the WordPress Root Directory
	public function eeSFL_GetRootPath() {
		
		if(!defined('eeSFL_ABSPATH')) {
			
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Getting the Root Path ...';
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'ABSPATH = ' . ABSPATH;
			
			$eeUploadDir = wp_upload_dir();
			$eeUploadPath = $eeUploadDir['basedir'];
		
			while ( ! file_exists( $eeUploadPath . '/wp-config.php' ) ) {
				$eeUploadPath = dirname( $eeUploadPath );
			}
		
			$eeRootPath = $eeUploadPath . '/';
			
			define('eeSFL_ABSPATH', $eeRootPath);
			
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'eeSFL_ABSPATH = ' . $eeRootPath;
		
			return $eeRootPath;
		
		} else {
			return FALSE;
		}
	}
	
	
	
	
	// Get Environment
	public function eeSFL_ScanEnvironment() {
		
		$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Scanning the Environment...';
		
		global $eeSFL_Upload;
		$eeArray = array();
		
		// Detect the Operating System
		$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'PHP_OS: ' . PHP_OS;
		$eeOS = strtoupper(PHP_OS);
		
		// Using explicit comparison to avoid false positives
		if (strpos($eeOS, 'WIN') === 0) {
			$eeArray['eeOS'] = 'WINDOWS';
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . ' Windows Detected';
		} elseif (strpos($eeOS, 'DARWIN') === 0) { // Ensure we're checking for the full string 'DARWIN'
			$eeArray['eeOS'] = 'MACOS';
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . ' macOS Detected';
		} elseif (strpos($eeOS, 'LINUX')) {
			$eeArray['eeOS'] = 'LINUX';
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . ' Linux Detected';
		} elseif (strpos($eeOS, 'BSD')) {
			$eeArray['eeOS'] = 'BSD';
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . ' BSD Detected';
		}

		
		// Detect Web Server
		$serverSoftware = $_SERVER["SERVER_SOFTWARE"];
		if (strpos($serverSoftware, 'Apache') !== false) {
			$eeArray['eeWebServer'] = 'Apache';
		} elseif (strpos($serverSoftware, 'nginx') !== false) {
			$eeArray['eeWebServer'] = 'Nginx';
		} elseif (strpos($serverSoftware, 'IIS') !== false) {
			$eeArray['eeWebServer'] = 'IIS';
		} elseif (strpos($serverSoftware, 'Lighttpd') !== false) {
			$eeArray['eeWebServer'] = 'Lighttpd';
		} else {
			$eeArray['eeWebServer'] = 'Unknown';
		}
		$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . $eeArray['eeWebServer'] . ' Detected';

		
		// PHP
		$eeArray['php_version'] = phpversion(); // PHP Version
		if(version_compare($eeArray['php_version'], '7.4', '<') OR version_compare($eeArray['php_version'], '9', '>')) {
			$this->eeSFL->eeLog['issues'][] = __('Not Supported', 'ee-simple-file-list') . ': PHP ' . $eeArray['php_version'];
		}
		
		// PHP Actual Upload Max Size
		$eeArray['upload_max_filesize'] = substr(ini_get('upload_max_filesize'), 0, -1); // PHP Limit (Strip off the "M")
		$eeArray['post_max_size'] = substr(ini_get('post_max_size'), 0, -1); // PHP Limit (Strip off the "M")
		if ($eeArray['upload_max_filesize'] <= $eeArray['post_max_size']) { // Check which is smaller, upload size or post size.
			$eeArray['php_actual_max_upload_size'] = $eeArray['upload_max_filesize'];
		} else {
			$eeArray['php_actual_max_upload_size'] = $eeArray['post_max_size'];
			$this->eeSFL->eeLog['issues'][] = 'PHP ' . __('Error', 'ee-simple-file-list');
			$this->eeSFL->eeLog['issues'][] = ' "post_max_size" ' . __('is less than', 'ee-simple-file-list') . ' "upload_max_filesize"';
			$this->eeSFL->eeLog['issues'][] = __('Upload Size Limit', 'ee-simple-file-list') . ': ' . $eeArray['php_actual_max_upload_size']; 
		}
		
		// WordPress Details
		$eeArray['wpUserID'] = get_current_user_id();
		$eeArray['wpSiteURL'] = get_option('siteurl') . '/'; // This Wordpress Website
		$eeArray['wpPluginsURL'] = plugins_url() . '/'; // The Wordpress Plugins Location
		$eeArray['pluginURL'] = plugins_url() . '/' . eeSFL_PluginSlug . '/';
		$eeArray['pluginDir'] = WP_PLUGIN_DIR . '/' . eeSFL_PluginSlug . '/';
		$wpUploadArray = wp_upload_dir(); // WordPress Upload Dir
		$eeArray['wpUploadDir'] = $wpUploadArray['basedir'] . '/'; // The Wordpress Uploads Location
		$eeArray['wpUploadURL'] = $wpUploadArray['baseurl'] . '/';
				
		
		// Get Server Technologies Available
		$eeSupported = get_option('eeSFL_Supported'); // Updated Each Re-Scan
		
		// Check for exec() and shell_exec() availability
		$execAvailable = function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))));
		$shellExecAvailable = function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))));
		
		if ($execAvailable && $shellExecAvailable && is_array($eeSupported)) {
			
			if (in_array('ImageMagick', $eeSupported) && in_array('GhostScript', $eeSupported)) {
				$eeArray['thumbsPDF'] = 'YES';
				$this->eeSFL->eeLog['environment'][] = $this->eeSFL->eeSFL_NOW() . ' ImageMagick and GhostScript Available for PDF Thumbnails';
			} else {
				$this->eeSFL->eeLog['environment'][] = $this->eeSFL->eeSFL_NOW() . ' ImageMagick or GhostScript Not Available';
			}
			
			if (in_array('ffMpeg', $eeSupported)) {
				$eeArray['thumbsVIDEO'] = 'YES';
				$this->eeSFL->eeLog['environment'][] = $this->eeSFL->eeSFL_NOW() . ' ffMpeg Available for Video Thumbnails';
			} else {
				$this->eeSFL->eeLog['environment'][] = $this->eeSFL->eeSFL_NOW() . ' ffMpeg Not Available';
			}
		} else {
			$this->eeSFL->eeLog['issues'][] = $this->eeSFL->eeSFL_NOW() . ' PHP exec() and/or shell_exec() functions are not available.';
			$this->eeSFL->eeLog['issues'][] = $this->eeSFL->eeSFL_NOW() . ' PDF and Video Thumbs Cannot Be Created.';
			$this->eeSFL->eeListSettings['GeneratePDFThumbs'] = 'NO';
			$this->eeSFL->eeListSettings['GenerateVideoThumbs'] = 'NO';
		}

		
		$this->eeSFL->eeEnvironment = $eeArray; // Our Environment Array
	}
	
	
	
	
	
	// Check What Tech This Server Supports
	public function eeSFL_CheckSupported() {
		
		// Check if SFL Plugin Dir is Readable
		$eeTestUrl = plugins_url(eeSFL_PluginURL . 'base/icon-128x128.png');
		$eeResponse = wp_remote_get($eeTestUrl);
		if (is_wp_error($eeResponse)) {
			$eeErrorMessage = $eeResponse->get_error_message();
			$this->eeSFL->eeLog['issues'][] = $this->eeSFL->eeSFL_NOW() . '! The SFL Plugin Directory Has a Problem...';
			$this->eeSFL->eeLog['issues'][] = $this->eeSFL->eeSFL_NOW() . $eeErrorMessage;
		} else {
			$eeResponseCode = wp_remote_retrieve_response_code($eeResponse);
			if ($eeResponseCode != 200) {
				$this->eeSFL->eeLog['issues'][] = $this->eeSFL->eeSFL_NOW() . '! The SFL Plugin Directory is Not Reachable by URL';
				$this->eeSFL->eeLog['issues'][] = $this->eeSFL->eeSFL_NOW() . '! Response Code: ' . $eeResponseCode;
			}
		}

		
		// Check File List Dir Permissions
		$eePermissions = substr(sprintf('%o', fileperms(ABSPATH . $this->eeSFL->eeListSettings['FileListDir'])), -4);
		if ($eePermissions !== '0755' && $eePermissions !== '0775') {
			$this->eeSFL->eeLog['issues'][] = $this->eeSFL->eeSFL_NOW() . ' ! Uncommon permissions in the File List Directory: ' . $eePermissions;
		}

		
		// Check for supported technologies
		$eeSupported = array();
	
		// Check for ffMpeg
		if(function_exists('shell_exec')) {
			
			if(shell_exec('ffmpeg -version')) {
				$eeSupported[] = 'ffMpeg';
				$this->eeSFL->eeLog['Supported'][] = 'Supported: ffMpeg';
			}
		}
		
		if($this->eeSFL->eeEnvironment['eeOS'] != 'WINDOWS') {
			
			// Check for ImageMagick
			$phpExt = 'imagick'; 
			if(extension_loaded($phpExt)) {
				$eeSupported[] = 'ImageMagick';
				$this->eeSFL->eeLog['Supported'][] = 'Supported: ImageMagick';
			}
			
			// Check for GhostScript
			if($this->eeSFL->eeEnvironment['eeOS'] == 'LINUX') { // TO DO - Make it work for IIS
			
				if(function_exists('shell_exec')) {
				
					$phpExt = 'gs'; // <<<---- This will be different for Windows
					if(shell_exec($phpExt . ' --version') >= 1.0) { // <<<---- This will be different for Windows too
						$eeSupported[] = 'GhostScript';
						$this->eeSFL->eeLog['Supported'][] = 'Supported: GhostScript';
					}
				}
			}
		}
		
		// echo '<pre>'; print_r($eeSupported); echo '</pre>'; exit;
		
		if(count($eeSupported)) {
			update_option('eeSFL_Supported', $eeSupported);
		}
		
		
		// Check Upload Max
		$eeActual = $this->eeSFL_ActualUploadMax();
		if( $this->eeSFL->eeListSettings['UploadMaxFileSize'] > $eeActual ) { 
			$this->eeSFL->eeListSettings['UploadMaxFileSize'] = $eeActual;
			update_option('eeSFL_Settings_' . $this->eeSFL->eeListID, $this->eeSFL->eeListSettings); // Set to Actual Max
		}
		
		return TRUE;
	}
	
	
	
	
	public function eeSFL_ScanAndSanitize($eeScanDir = FALSE) { // Relative to ABSPATH
		
		// If $eeScanDir is given, those files found will be returned
		// If $eeScanDir is FALSE, $eeSFL->eeFileScanArray will be filled.
		
		
		$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Scanning the Disk...';
	
		if(!$eeScanDir) { $eeScanDir = $this->eeSFL->eeListSettings['FileListDir']; } // wp-content/uploads/simple-file-list/
		
		if(is_dir(ABSPATH . $eeScanDir) === FALSE) { return FALSE; } // Bail if empty
		
		// Do the Scan Man
		$eeFilesFound = array();
		$eeIterator = new RecursiveDirectoryIterator(ABSPATH . $eeScanDir, RecursiveDirectoryIterator::SKIP_DOTS);
		$eeArray = new RecursiveIteratorIterator($eeIterator, RecursiveIteratorIterator::SELF_FIRST);
			
		foreach ($eeArray as $eeItem) { // Loop thru and take a look
			
			// Get the basic parts; path, filename, ext
			$eeThisItemName = $eeItem->getFilename(); // Get the file name
			$eeThisItemExt = $eeItem->getExtension(); // Get the extension
			$eeThisItemPath = $eeItem->getPath() . '/'; // up/to/the/file/
			
			if(strpos($eeThisItemPath, '.thumbnails/')) { continue; } // Skip Thumbs Dir
			
			// File or Folder?
			if($eeItem->isDir() AND defined('eeSFL_Pro')) { $eeIsDir = TRUE; } // Pro Only
				elseif($eeItem->isFile()) { $eeIsDir = FALSE; } 
					else { continue; }
			
			// Remove ABSPATH so we remain relative to it
			$eeThisItemPath = str_replace(ABSPATH . $this->eeSFL->eeListSettings['FileListDir'], '', $eeThisItemPath);
			
			if(strpos($eeThisItemPath, '.') === 0 OR strpos($eeThisItemName, '.') === 0) { continue; } // Skip hidden items
			if($eeThisItemName == '__MACOSX') { continue; } // Skip This
			if($eeThisItemName == 'index.html') { continue; } // Skip index.html files
		   
			// Skip Forbidden Types
			if($eeThisItemExt) {
				if( in_array($eeThisItemExt, $this->eeSFL->eeForbiddenTypes) ) { 
					if(isset($_GET['eeSFL_ArchivePath']) OR isset($_GET['eeReScan'])) { // Back-End Messaging
						$this->eeSFL->eeLog['warnings'][] = 'File Skipped. Type Forbidden: ' . $eeThisItemName;
					}
					continue;
				}
			}
			
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'FOUND: ' . $eeThisItemName;
			
			// Sanitize the Name
			$eeNewItemName = $this->eeSFL_SanitizeFileName($eeThisItemName);
			
			// Only If the Name Was Changed
			if($eeNewItemName != $eeThisItemName) {
			
				$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'PROBLEM NAME: ' . $eeThisItemName;
				
				// Prevent over-writing another item that has just been sanitized
				// Rename using a sequential numbering system
				if( in_array($eeThisItemPath . $eeNewItemName, $eeFilesFound) ) {
					
					$eePathParts = pathinfo($eeNewItemName);
					$eeNameOnly = $eePathParts['filename'];
					$eeExtOnly = $eePathParts['extension'];
					
					for ($i = 1; $i <= 10000; $i++) { // How many before we overwrite? Ten thousand seems like a lot.
						
						if($eeIsDir) { // Add a sequential suffix
							$eeNewItemName = $eeNameOnly . '_' . $i; // Folders
						} else {
							$eeNewItemName = $eeNameOnly . '_' . $i . '.' . $eeExtOnly; // Files
						}
						
						// Maybe this name is already there too?
						if(!in_array($eeThisItemPath . $eeNewItemName, $eeFilesFound)) { break; } // If not, we're done.
						
					}
					
					// Rename the Item on the Disk
					if(rename(ABSPATH . $eeThisItemPath . $eeThisItemName, ABSPATH . $eeThisItemPath . $eeNewItemName)) {
						$this->eeSFL->eeSanitizedFiles[$eeThisItemPath . $eeNewItemName] = $eeThisItemPath . $eeThisItemName;
						$eeThisItemName = $eeNewItemName;
						$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'SANITIZED: ' . $eeNewItemName;
					} else {
						$this->eeSFL->eeLog['warnings'][] = $this->eeSFL->eeSFL_NOW() . 'RENAME FAILED: ' . $eeThisItemName;
					}
				}
			}
			
				
			// Add to the Array
			if($eeIsDir) { $eeThisItemName .= '/' ; } // Save folders with a trailing slash
			$eeFilesFound[] = $eeThisItemPath . $eeThisItemName;
			
				
			// Ensure there is a blank index.html file to prevent directory browsing
			if(!is_file(ABSPATH . $eeScanDir . 'index.html')) {
				
				$eeHandle = fopen(ABSPATH . $eeScanDir . 'index.html', "a+");
				
				if($eeHandle) {
					fclose($eeHandle);
				} else {
					$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Unable to create blank index file in ' . $eeScanDir;
				}
			}
		}
		
		if($eeScanDir == $this->eeSFL->eeListSettings['FileListDir']) { // Scanning All
			$this->eeSFL->eeFileScanArray = $eeFilesFound; // Fill $eeSFL->eeFileScanArray
			return TRUE;
		} else { // Scanning a Specific Dir
			return $eeFilesFound; // Just return the files found
		}	
	}
	
	
	
	// Make sure the file name is acceptable
	public function eeSFL_SanitizeFileName($eeFileName) {
		
		// Get the name and extension
		$eePathParts = pathinfo($eeFileName);
		
		// Let WordPress sanitize
		$eeFileNameOnly = sanitize_file_name($eePathParts['filename']);
		
		// Get rid of dots in the name
		$eeFileNameOnly = str_replace('.', '_', $eeFileNameOnly); // Get rid of dots
		
		if($eeFileNameOnly) {
		
			if(isset($eePathParts['extension'])) { // It's a File
		
				$eeNewFileName = $eeFileNameOnly . '.' . strtolower($eePathParts['extension']);
				return $eeNewFileName;
			}
			
			// It's a Folder
			return $eeFileNameOnly;
		
		} else {
			$this->eeSFL->eeLog['warning'][] = 'File Name Sanitization Left No Name for ' . addslashes($eeFileName);
		}
		
		return FALSE;
	}
	
	
	
	
	// Check if a file already exists, then number it so file will not be over-written.
	public function eeSFL_CheckForDuplicateFile($eeSFL_FilePathAdded) { // Path from ABSPATH
		
		$eePathInfo = pathinfo($eeSFL_FilePathAdded);
		$eeFileName = $eePathInfo['basename'];
		$eeNameOnly = $eePathInfo['filename'];
		$eeExtension = strtolower($eePathInfo['extension']);
		$eeDir = dirname($eeSFL_FilePathAdded) . '/';
		$eeFolderPath = str_replace($this->eeSFL->eeListSettings['FileListDir'], '', $eeDir);
		$eeCopyLimit = 1000; // File copies limit
		
		if(empty($this->eeSFL->eeAllFiles)) {
			$this->eeSFL->eeAllFiles = get_option('eeSFL_FileList_' . $this->eeSFL->eeListID);
		}
		
		foreach($this->eeSFL->eeAllFiles as $eeFileArray) { // Loop through file array and look for a match.
			
			if( $eeFolderPath . $eeFileName == $eeFileArray['FilePath'] ) { // Duplicate found
			
				$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Duplicate Item Found: ' . $eeFolderPath . $eeFileName;
				
				if( is_file(ABSPATH . $eeSFL_FilePathAdded) ) { // Confirm the file is really there
					
					for ($i = 1; $i <= $eeCopyLimit; $i++) { // Look for existing copies
						
						$eeFileName = $eeNameOnly . '_' . $i . '.' . $eeExtension; // Indicate the copy number
						
						if(!is_file(ABSPATH . $eeDir . $eeFileName)) { break; } // We're done.
					}							
				}
			}
		}
		
		return 	$eeFileName; // Return the new file name
	}
	
	
	
	
	
	// Return the size of a file in a nice format.
	// Accepts a path or file size in bytes
	public function eeSFL_GetFileSize($eeSFL_File) {  
		
		if( is_numeric($eeSFL_File) ) {
			$bytes = $eeSFL_File;
		} elseif(is_file(eeSFL_ABSPATH . $eeSFL_File)) {
			$bytes = filesize(eeSFL_ABSPATH . $eeSFL_File);	
		} else {
			return FALSE;
		}
			
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
	
	
	
	
	// Sanitize & Validate the File List Directory Path String
	private function eeSFL_ValidateFileListDir($eeDir) {
		
		global $eeSFL_Environment;
		
		if(!$eeDir) { 
			$this->eeSFL->eeLog['errors'][] = __('No Path Found', 'ee-simple-file-list');
			return FALSE;
		}
		
		// Replace Backslashes with Slashes
		$eeDir = str_replace('\\', '/', $eeDir);
		
		// Check if eeDir has a leading slash. We don't want that.
		if($eeDir[0] == '/') {  $eeDir = substr($eeDir, 1); }
	
		// Check if eeDir has a trailing slash. We do want that.
		$eeChar = substr($eeDir, -1);
		if($eeChar != '/') {  $eeDir .= '/'; }
		
		// Sanitize each foldername in the path
		if( substr_count($eeDir, '/') > 1 ) {
			
			// Traversal Prevention
			$eeCheck = str_replace('../', '', $eeDir );
			
			if($eeCheck != $eeDir) { 
				
				$this->eeSFL->eeLog['errors'][] = __('Path is Not Direct', 'ee-simple-file-list');
				$this->eeSFL->eeLog['errors'][] = $eeDir;
				return FALSE;
			}
			
			$eePieces = explode('/', $eeDir); 		
			$eeDir = ''; // Reset for rebuilding
			
			foreach( $eePieces as $eePiece ) { // Rebuild as we sanitize each level
				
				$eePiece = $eeSFL_Environment->eeSFL_SanitizeFileName($eePiece);
				
				if($eePiece AND strlen($eePiece) <= 255) {
					
					$eeDir .= $eePiece . '/';
				}
			}
		
		} else {
			
			$eeCheck = substr($eeDir, 0, -1); // Exclude the trailing slash
			$eeDir = $eeSFL_Environment->eeSFL_SanitizeFileName( $eeCheck ) . '/'; // Add it back
		}
		
		if($eeDir) {
			
			// This are locations NOT Allowed
			if(strpos($eeDir, '.') === 0   
			OR strpos($eeDir, 'p-admin/')
			OR strpos($eeDir, 'p-base/') 
			OR strpos($eeDir, 'p-content/themes/')  
			OR $eeDir == '/' OR $eeDir == '-/' OR $eeDir == '--/'
			OR $eeDir == 'wp-content/' 
			OR $eeDir == 'wp-content/plugins/' 
			OR $eeDir == 'wp-content/uploads/' ) {
				
				$this->eeSFL->eeLog['errors'][] = 'This File List Location is Not Allowed';
				$this->eeSFL->eeLog['errors'][] = $eeDir;
				
				return FALSE;
			}
			
			return $eeDir; // Good to go
		
		}
		
		$this->eeSFL->eeLog['errors'][] = __('Path Failed Validation', 'ee-simple-file-list');
		return FALSE;
	}
	
	
	
	
	
	// Check for the Upload Folder, Create if Needed
	public function eeSFL_FileListDirCheck($eeFileListDir) {
		
		$eeCopyMaunalFile = FALSE;
		
		if(!$eeFileListDir OR substr($eeFileListDir, 0, 1) == '/' OR strpos($eeFileListDir, '../') ) { 
			
			$this->eeSFL->eeLog['errors'][] = __('Bad Directory Given', 'ee-simple-file-list') . ': ' . $eeFileListDir;
			
			return FALSE;
		}
			
		$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Checking: ' . $eeFileListDir;
			
		if( !is_dir(ABSPATH . $eeFileListDir) ) { // Directory Changed or New Install
		
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'New Install or Directory Change...';
			
			if(!is_writable( ABSPATH . $eeFileListDir ) ) {
				
				$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'No Directory Found. Creating ...';
				
				if ($this->eeSFL->eeEnvironment['eeOS'] == 'WINDOWS') {
					
					if( !mkdir(ABSPATH . $eeFileListDir) ) {
						
						$this->eeSFL->eeLog['errors'][] = __('Cannot Create Windows Directory', 'ee-simple-file-list') . ': ' . $eeFileListDir;
					}
				
				} elseif($this->eeSFL->eeEnvironment['eeOS'] == 'LINUX') {
					
					if( !mkdir(ABSPATH . $eeFileListDir , 0755) ) { // Linux - Need to set permissions
						
						$this->eeSFL->eeLog['errors'][] = __('Cannot Create Linux Directory', 'ee-simple-file-list') . ': ' . $eeFileListDir;
					}
				} else {
					
					$this->eeSFL->eeLog['errors'][] = __('ERROR: Could not detect operating system', 'ee-simple-file-list');
					return FALSE;
				}
				
				if(!is_writable( ABSPATH . $eeFileListDir )) {
					$this->eeSFL->eeLog['errors'][] = __('Cannot create the upload directory', 'ee-simple-file-list') . ': ' . $eeFileListDir;
					$this->eeSFL->eeLog['errors'][] = __('Please check directory permissions', 'ee-simple-file-list');
				
					return FALSE;
				
				} else {
					
					$eeCopyMaunalFile = TRUE;
					
					$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'The File List Dir Has Been Created!';
					$this->eeSFL->eeLog['notice'][] = $eeFileListDir;
				}
			
			} else {
				$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'FileListDir Looks Good';
			}
			
		} 
		
		// Check index.html, create if needed.	
		if( strlen($eeFileListDir) >= 2 ) {	
			
			$eeFile = ABSPATH . $eeFileListDir . 'index.html'; // Disallow direct file indexing.
			
			if(!is_file($eeFile)) {
				
				if($eeHandle = fopen($eeFile, "a+")) {
					
					if(!is_readable($eeFile)) {
						
						$this->eeSFL->eeLog['warnings'][] = __('WARNING! Could not write file', 'ee-simple-file-list') . ': index.html';
						$this->eeSFL->eeLog['warnings'][] = __('Please upload a blank index file to this location to prevent unauthorized access.', 'ee-simple-file-list');
						$this->eeSFL->eeLog['warnings'][] = ABSPATH . '/' . $eeFileListDir;
						
					} else {
						
						// Write nice content to the file
						$eeString = file_get_contents( $this->eeSFL->eeEnvironment['pluginDir'] . 'images/ee-index-template.html' );
						fwrite($eeHandle, $eeString);
						fclose($eeHandle);
					}
				}
			}
			
			if($eeCopyMaunalFile === TRUE) {
				
				// Copy the Manual to the new directory, so there's at least one file.
				$eeCopyFrom = $this->eeSFL->eeEnvironment['pluginDir'] . 'Simple-File-List.pdf';
				$eeCopyTo = ABSPATH . '/' . $eeFileListDir . 'Simple-File-List.pdf';
				copy($eeCopyFrom, $eeCopyTo);
			}
		}
		
		return TRUE; // Looks Good
		
	}
	
	
	
	// Protect file list directories form direct URL access (hotlinking)
	public function eeSFL_LimitDirAccess($eeMode) {
		
		global $eeSFL;
		
		// Delete and Start Over
		$eeFile = ABSPATH . $eeSFL->eeListSettings['FileListDir'] . '.htaccess';
		if( is_readable($eeFile) ) { unlink($eeFile); }
		
		if($eeMode == 'YES' OR $eeMode == 'NORMAL') { // Not Needed
			return TRUE;
		}
		
		// Apache / LightSpeed
		if(stripos($eeSFL->eeEnvironment['eeWebServer'], 'Apache') === 0 OR stripos($eeSFL->eeEnvironment['eeWebServer'], 'LiteSpeed') === 0 ) { // Write .htaccess
				
			// Check index.html, create if needed.	
			$eeFile = ABSPATH . $eeSFL->eeListSettings['FileListDir'] . '.htaccess';
			
			if($handle = fopen($eeFile, "w+")) {
				
				if(!is_readable($eeFile)) {
					
					$eeSFL->eeLog['errors'][] = __('ERROR: Could not create', 'ee-simple-file-list') . ' .htaccess';
					return FALSE;
				
				} else {
					
					// Write nice content to the file
					$eeString = file_get_contents( plugin_dir_path(__FILE__) . '/../images/htaccess-template.txt' );
					
					if(!$eeString) {
						$eeSFL->eeLog['errors'][] = __('WARNING: Could not write', 'ee-simple-file-list') . ' .htaccess';
						return FALSE;
					}
					
					// Assign our file types
					$eeFormats = str_replace(',', '|', $eeSFL->eeListSettings['FileFormats']);
					$eeFormats = str_replace(' ', '', $eeFormats);
					$eeString = str_replace('FILE_TYPES', $eeFormats, $eeString);
					
					// Create .htaccess file
					if(!fwrite($handle, $eeString)) {
						$eeSFL->eeLog['errors'][] = __('WARNING: Could not write', 'ee-simple-file-list') . ' .htaccess';
					} else {
						$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'eeSFLA --> .htaccess file has been set';
					}
					fclose($handle);
				}
			}
		
		// NGINX
		} elseif( stripos($eeSFL->eeEnvironment['eeWebServer'], 'nginx') === 0 ) {
			
			$eeSFL->eeLog['messages'][] = '<strong>' . __('IMPORTANT', 'ee-simple-file-list') . ' - ' . 'Nginx ' . __('Web Server Detected', 'ee-simple-file-list') . '</strong><br />' . 
				__('To prevent direct URL access to your files you must manually update your server configuration.', 'ee-simple-file-list') . '<br />
					<a href="https://kinsta.com/blog/hotlinking/#nginx" target="_blank">' . __('Learn More', 'ee-simple-file-list') . '</a>';
			
		// Microsoft IIS
		} elseif( stripos($eeSFL->eeEnvironment['eeWebServer'], 'iis') OR stripos($eeSFL->eeEnvironment['eeWebServer'], 'iis') === 0  ) {
			
			$eeSFL->eeLog['messages'][] = '<strong>' . __('IMPORTANT', 'ee-simple-file-list') . ' - ' . 'IIS ' . __('Web Server Detected', 'ee-simple-file-list') . '</strong><br />' . 
				__('To prevent direct URL access to your files you must manually update your server configuration.', 'ee-simple-file-list') . '<br />
					<a href="https://medium.com/@ankittyagi366/how-to-prevent-hotlinking-in-iis-server-asp-net-framework-asp-net-core-application-21900edd80d7" target="_blank">' . __('Learn More', 'ee-simple-file-list') . '</a>';
		
		// Something Else
		} else {
			$eeSFL->eeLog['messages'][] = '<strong>' . __('IMPORTANT', 'ee-simple-file-list') . ' - ' . __('Unknown Web Server', 'ee-simple-file-list') . '</strong><br />' . 
				__('To prevent direct URL access to your files you must manually update your server configuration.', 'ee-simple-file-list');
		}
	}
	
	
	
	
	// Get Actual Max Upload Size
	public function eeSFL_ActualUploadMax() {
		
		$eeArray = array();
		
		$eeArray['upload_max_filesize'] = substr(ini_get('upload_max_filesize'), 0, -1); // PHP Limit (Strip off the "M")
		$eeArray['post_max_size'] = substr(ini_get('post_max_size'), 0, -1); // PHP Limit (Strip off the "M")
		
		// Check which is smaller, upload size or post size.
		if ($eeArray['upload_max_filesize'] <= $eeArray['post_max_size']) { 
			return $eeArray['upload_max_filesize'];
		} else {
			return $eeArray['post_max_size'];
		}
	}
	
		
		
}