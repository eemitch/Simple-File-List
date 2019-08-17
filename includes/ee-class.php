<?php // Simple File List - ee-class.php - mitchellbennis@gmail.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Class' ) ) exit('That is Noncense! (' . basename(__FILE__) . ')' ); // Exit if nonce fails

$eeSFL_Log[] = 'Loaded: ee-class';


// Plugin Setup
class eeSFL_MainClass { // Plugin Configuration --> Environment, User, Settings
			
	// Basics
	public $eePluginName = 'Simple File List';
	public $eePluginNameSlug = 'simple-file-list-4'; // TO DO - Change this before release !!!
	public $eePluginSlug = 'ee-simple-file-list';
	public $eePluginWebPage = 'http://simplefilelist.com';
	public $eeAddOnsURL = 'https://get.simplefilelist.com/index.php';
	
	public $eeListID = 1;
	public $eeDefaultUploadLimit = 99;
	public $eeFileThumbSize = 64;
    
    public $eeDynamicImageThumbFormats = array('gif', 'jpg', 'jpeg', 'png');
    public $eeDynamicVideoThumbFormats = array('avi', 'flv', 'm4v', 'mov', 'mp4', 'wmv');
    public $eeExcludedFileNames = array('error_log', 'index.html');
    private $eeForbiddenTypes = array('.php', '.exe', '.js', '.com', '.wsh', '.vbs');
    
    private $eeExcludedFiles = array('index.html');
    
    // The Default List Definition (Unsorted)
    // Extensions Add as Needed
    public $DefaultListSettings = array( // An array of file list settings arrays
		
		1 => array(
			'ListTitle' => 'Simple File List', // List Title
			'FileListDir' => 'wp-content/uploads/simple-file-list', // List Directory Name (relative to ABSPATH)
			'ShowList' => 'YES', // Show the File List (YES, ADMIN, USER, NO)
			'ShowFileNiceName' => 'YES', // Display the File's Nice Name (YES or NO)
			'ShowFileThumb' => 'YES', // Display the File Thumbnail Column (YES or NO)
			'ShowFileDate' => 'YES', // Display the File Date Column (YES or NO)
			'ShowFileSize' => 'YES', // Display the File Size Column (YES or NO)
			'ShowFileDescription' => 'YES', // Display the File Description (YES or NO)
			'ShowFileActions' => 'YES', // Display the File Action Links Section (below each file name) (YES or NO)
			'ShowHeader' => 'YES', // Show the File List's Table Header (YES or NO)
			'SortBy' => 'NAME', // Sort By (NAME, DATE, SIZE, RANDOM)
			'SortOrder' => 'Descending', // Descending or Ascending
			'AllowFrontManage' => 'NO', // Allow front-side users to manage files (YES or NO)
			'AllowUploads' => 'YES', // Allow File Uploads (YES, ADMIN, USER, NO)
			'UploadLimit' => 10, // Limit Files Per Upload Job (Quantity)
			'UploadMaxFileSize' => 1, // Maximum Size per File (MB)
			'FileFormats' => 'gif, jpg, jpeg, png, tif, pdf, wav, wmv, wma, avi, mov, mp4, m4v, mp3, zip', // Allowed Formats
			'GetUploaderInfo' => 'YES', // Show the Info Form
			'Notify' => '', // Send Upload Nitification Email Here
			'Updated' => '00-00-00 00:00:00' // Time/Date of Last File Upload
		)
	);
    
    
    
    // Get Environment
    public function eeSFL_GetEnv() {
	    
	    $eeSFL_Env = array();
	    
	    // This Wordpress Website
		$eeSFL_Env['wpSiteURL'] = get_site_url() . '/';
		
		// The Wordpress Plugins Location
		$eeSFL_Env['wpPluginsURL'] = plugins_url() . '/';
		$eeSFL_Env['pluginURL'] = plugins_url() . '/' . $this->eePluginNameSlug;
		
		// The Wordpress Uploads Location
		$wpUploadArray = wp_upload_dir();
		$wpUploadDir = $wpUploadArray['basedir'];
		$eeSFL_Env['wpUploadDir'] = $wpUploadDir . '/';
		$eeSFL_Env['wpUploadURL'] = $wpUploadArray['baseurl'] . '/';
		
		// SFL DEFAULT
		$eeSFL_Env['FileListDefaultDir'] = str_replace(ABSPATH, '', $eeSFL_Env['wpUploadDir'] . 'simple-file-list/');
		// $eeSFL_Env['FileListDefaultURL'] = str_replace($eeSFL_Env['wpSiteURL'], '', $eeSFL_Env['wpPluginsURL'] . 'simple-file-list/');
		
		// PHP Limits
		$eeSFL_Env['upload_max_upload_size'] = substr(ini_get('upload_max_filesize'), 0, -1); // Strip off the "M".
		$eeSFL_Env['post_max_size'] = substr(ini_get('post_max_size'), 0, -1); // Strip off the "M".
		
		// Check which is smaller, upload size or post size.
		if ($eeSFL_Env['upload_max_upload_size'] <= $eeSFL_Env['post_max_size']) { 
			$eeSFL_Env['the_max_upload_size'] = $eeSFL_Env['upload_max_upload_size'];
		} else {
			$eeSFL_Env['the_max_upload_size'] = $eeSFL_Env['post_max_size'];
		}
		
		$eeSFL_Env['supported'] = get_option('eeSFL_Supported');
		
		return $eeSFL_Env;
    }
    
    
    
   
    // Get Data
    public function eeSFL_Config($eeSFL_Env, $eeID = 1) {
	    
	    global $eeSFL_Log;
	    
	    $eeSFL_Config = array();
	    
	    // Getting the settings array
	    $eeArray = get_option('eeSFL-Settings');
	    
	    if(is_array($eeArray)) {
	    
			// Get sub-array for this list ID
			$eeSFL_Config = $eeArray[$eeID];
			$eeSFL_Config['eeID'] = $eeID;  // The List ID
			
			// Check Environment
			if($eeSFL_Env['the_max_upload_size'] < $eeSFL_Config['UploadMaxFileSize']) {
				$eeSFL_Config['UploadMaxFileSize'] = $eeSFL_Env['the_max_upload_size']; // If Env is lower, set to that.
			}
			
			$eeSFL_Config['FileListBaseDir'] = $eeSFL_Config['FileListDir']; // Before folders are added
			$eeSFL_Config['FileListURL'] = $eeSFL_Env['wpSiteURL'] . $eeSFL_Config['FileListDir']; // The Full URL
					
			// Get the files for this List ID
			// $eeSFL_Config['eeSFL_Files'] = get_transient('eeSFL-' . $eeSFL->eeListID . '-Files');
			
			// echo '<pre>'; print_r($eeSFL_Env); echo '</pre>';
			// echo '<pre>'; print_r($eeSFL_Config); echo '</pre>'; exit;
			
			$eeSFL_Log['Env'] = $eeSFL_Env;
			$eeSFL_Log['Config'] = $eeSFL_Config;
			
			// The whole point...	
			return $eeSFL_Config; // Associative array
		
		} else {
			return $this->DefaultListSettings;
		}
		
	}
	
	
	
    
    
    // Default File List Definition
    public $eeSFL_Files = array(
	    
		0 => array(
			'FileList' => 1, // The ID of the File List, contained in the above array.
		    'FilePath' => '/Example-File.jpg', // Path to file, relative to the list root
		    'FileNiceName' => 'Example File', // The name displayed
		    'FileExt' => 'jpg', // The file extension
			'FileDateAdded' => '', // Date the file was added to the list
			'FileDateChanged' => '', // Last date the file was renamed or otherwise changed
			'FileSize' => '', // The size of the file
			'FileDescription' => '', // A short descriotion of the file
			'FileOwner' => '' // (Coming Later)
		)
    );
	
	
	// Create an array: $eeArray( array('filepath/name', 'date', 'size' , 'etc...') )
	public function eeSFL_createFileListArray($eeID, $eeFileListDir, $eeForce = FALSE) {
		
		global $eeSFL_Log, $eeSFL_Config;
		$eeArray = array();
		
		if($eeForce) {
			delete_transient('eeSFL-FileList-' . $eeID);
		} else {
			$eeArray = get_transient('eeSFL-FileList-' . $eeID); // From the database
			if(!$eeArray) { $eeArray = array(); } // Ensure it's not FALSE
		}
		
		// We store our array of file info in a transient
		if( !count($eeArray) ) {
			
			$eeSFL_Log[] = 'Creating new file list transient for List # ' . $eeID . ' at ' . $eeFileListDir;
			
			$eeFileArray = $this->eeSFL_IndexFileListDir($eeFileListDir);
			
			foreach( $eeFileArray as $eeKey => $eeFile) {
				
				$eeFileTime = date("Y-m-d H:i:s", filemtime(ABSPATH . $eeFile));
				$eeFileSize = @filesize(ABSPATH . $eeFile);
				
				// Get just the folder/file
				// $ee1 = strlen(ABSPATH . $eeFileListDir);
				// $ee2 = strlen($eeValue);
				// $eeStart = $ee2 - ($ee2-$ee1) -1;
				// $eeFile = substr($eeValue, $eeStart);
				
				// Check forbidden types
				foreach( $this->eeForbiddenTypes as $eeValue2) {
					if(strpos($eeFile, $eeValue2)) {
						$eeFile = FALSE;
					}
				}
				
				if($eeFile) {
					
					// Remove the base file list directory string
					$eeFile = str_replace($eeSFL_Config['FileListBaseDir'], '', $eeFile);
					
					// Add line to array
					$eeArray[] = $eeFile . '|' . $eeFileTime . '|' .  $eeFileSize;
					
					// Get file extension
					$eeExt = substr(strrchr($eeFile,'.'), 1); 
					
					// Check and create thumbnail if needed...
					if(in_array($eeExt, $this->eeDynamicImageThumbFormats) OR in_array($eeExt, $this->eeDynamicImageThumbFormats)) {
						$this->eeSFL_CheckThumbnail($eeFileListDir . $eeFile);
					}
				}
			}
			
			if(@count($eeArray) AND set_transient('eeSFL-FileList-' . $eeID, $eeArray, 6 * HOUR_IN_SECONDS) ) {
				$eeSFL_Log['eeSFL']['transient set'] = $eeArray;
			} else {
				$eeSFL_Log['errors'][] = 'File Transient Could Not Be Set.';
				return FALSE;
			}
		
			$eeArray = array_unique($eeArray); // Remove duplicates
			
			// Check for FFmpeg here
			if(trim(@shell_exec('type -P ffmpeg'))) {
				update_option('eeSFL_Supported', 'ffMpeg');
			}
		
		}
		
		// echo '<pre>'; print_r($eeSFL_Log); echo '</pre>'; exit;
			
		return $eeArray;
	}
	
	
	
	
	// Get All the Files
	private function eeSFL_IndexFileListDir($eeFileListDir) {
	    
	    global $eeSFLF, $eeSFL_Log;
	    
	    $eeFileArray = array();
	    
	    if($eeSFLF) {
		    
		    $eeSFL_Log[] = 'Getting files and folders for searching...';
		    
		    $eeFileArray = $eeSFLF->eeSFLF_IndexCompleteFileListDirectory($eeFileListDir);
		    
	    } else {
		    
		    $eeSFL_Log[] = 'Getting files from: ' . $eeFileListDir; 
		    
		    
		    $eeFileNameOnlyArray = scandir(ABSPATH . $eeFileListDir);
		    
		    foreach($eeFileNameOnlyArray as $eeValue) {
		    	
		    	if(strpos($eeValue, '.') !== 0 AND is_file(ABSPATH . $eeFileListDir . $eeValue) ) {
			    	
			    	if(!in_array($eeValue, $this->eeExcludedFiles) )  {
				    	$eeFileArray[] = $eeFileListDir . $eeValue; // Add the path
			    	}
		    	}
		    }
	    }
	    
	    
	    if(!count($eeFileArray)) {
		    $eeSFL_Log['errors'][] = 'No Files Found';
	    }
		
		// $eeSFL_Log['fileArray'][] = $eeFileArray;

		return $eeFileArray;
	}
	
	
	
	
	
	
	
	
	public function eeSFL_CheckThumbnail($eeFilePath) {
		
		global $eeSFL_Log, $eeSFL_Config, $eeSFL_Env;
		
		$eeFileFullPath = ABSPATH . $eeFilePath;
		$eeFileFullPath = str_replace('//', '/', $eeFileFullPath);
		
		$eeSFL_Log['checking thumb'][] = $eeFilePath;
		
		// Config
		$eeExt = FALSE;
		$eeScreenshot = FALSE;
		$eeThumbURL = FALSE;
		
		// Get File Info
		$eePathParts = pathinfo($eeFileFullPath);
		$eeFileName = basename($eeFileFullPath);
		$eeDirName = $eePathParts['dirname'];
		$eeBaseName = $eePathParts['basename'];
		$eeExt = strtolower(@$eePathParts['extension']);
		$eeFileNameOnly = $eePathParts['filename'];
		
		// Is there already a thumb?
		if(is_file(ABSPATH . '/' . $eeSFL_Config['FileListDir'] . '/.thumbnails/thumb_' . $eeFileNameOnly . '.jpg')) {
			
			// exit(ABSPATH . '/' . $eeSFL_Config['FileListDir'] . '/.thumbnails/thumb_' . $eeFileNameOnly . '.jpg');
			
			return TRUE;
		}
		
		// FFmpeg Support
		if(in_array($eeExt, $this->eeDynamicVideoThumbFormats)) { // Check for FFMPEG
			
			if($eeSFL_Env['supported']) {
				
				$eeExt = 'png'; // Set the extension
				$eeScreenshot = $eeThumbsPATH . 'eeScreenshot_' . $eeFileNameOnly . '.' . $eeExt; // Create a temporary file
				
				// Create a full-sized image at the one-second mark
				$eeCommand = 'ffmpeg -i ' . $eeFileFullPath . ' -ss 00:00:01.000 -vframes 1 ' . $eeScreenshot;
				
				$eeFFmpeg = trim(shell_exec($eeCommand));
					
				if(is_file($eeScreenshot)) { // <<<------------------------ TO DO - Resize down to $this->eeFileThumbSize
					
					$this->eeSFL_CreateThumbnailImage($eeFileFullPath);
					
					// unlink($eeScreenshot); // Delete the screeshot file
					
					return TRUE;
				
				} else {
					$eeSFL_Log['errors'][] = __('FFmpeg Error - File Not Created', 'ee-simple-file-list');
					$eeSFL_Log['errors'][] = $eeFileName;
					$eeSFL_Log['errors'][] = $eeVideoThumb;
				}	
			} else {
				$eeSFL_Log[] = 'FFmpeg Not Installed';
			}
		}
		
		
		if(in_array($eeExt, $this->eeDynamicImageThumbFormats)) { // Just for known image files... 
		
			// Generate an Image Thumbnail
			if(!$eeExt OR strpos($eeExt, '.') === 0) { // It's a Folder or Hidden
			
				$eeExt = 'folder';
			
			} else {
		
				$this->eeSFL_CreateThumbnailImage($eeFilePath);
				
				return TRUE;
			}
		}
	}
	
	
	
	private function eeSFL_CreateThumbnailImage($eeFilePath) {
		
		global $eeSFL_Log, $eeSFL_Config, $eeSFL_Env;
		
		$eeFileFullPath = ABSPATH . $eeFilePath;
		
		// Dynamicly created thumbnails are here
		$eeThumbsURL = $eeSFL_Env['wpSiteURL'] . '/' . $eeSFL_Config['FileListURL'] . '.thumbnails/'; 
		$eeThumbsPATH = ABSPATH . $eeSFL_Config['FileListDir'] . '.thumbnails/'; // Path to them
        
        $eePathParts = pathinfo($eeFileFullPath);
        
        // echo '<pre>'; print_r($eeThumbsPATH); echo '</pre>'; exit;
        
        if( !is_array($eePathParts) ) { 
	        $eeSFL_Log['errors'][] = 'No Path Parts';
	        $eeSFL_Log['errors'][] = $eeFileFullPath;
	        return FALSE;
	    }
        
		$eeFileNameOnly = $eePathParts['filename'];
		
        // Thank Wordpress for this easyness.
		$eeFileImage = wp_get_image_editor($eeFileFullPath); // Try to open the file
    
        if (!is_wp_error($eeFileImage)) { // Image File Opened
            
            $eeFileImage->resize($this->eeFileThumbSize, $this->eeFileThumbSize, TRUE); // Create the thumbnail
            
            $eeFileNameOnly = str_replace('eeScreenshot_', '', $eeFileNameOnly); // Strip the temp term from video screenshots
		
			$eeSFL_Log['creating thumb'][] = $eeThumbsPATH . 'thumb_' . $eeFileNameOnly . '.jpg'; 
            
            $eeFileImage->save($eeThumbsPATH . 'thumb_' . $eeFileNameOnly . '.jpg'); // Save the file
        
        } else { // Cannot open
	     
	        $eeSFL_Log[] = 'Not an Image: ' . $eeFileFullPath;   
        }
	}
	
	
	
	
	
	
	
	public function eeSFL_SortFiles($eeFiles, $eeSortBy, $eeSortOrder) {
		
		$eeFilesSorted = array();
		
		if(count($eeFiles)) {
			
			// Files by Name
			if($eeSortBy == 'Date') { // Files by Date
				
				// echo '<p>Sorting by date...</p>';
				
				foreach($eeFiles as $eeFileInfo) {
					
					$eeArray = explode('|', $eeFileInfo);
					
					$eeFilesSorted[ $eeArray[1] ] = $eeFileInfo; // Associative Array
				}
				
			} elseif($eeSortBy == 'Size') { // Files by Size
				
				foreach($eeFiles as $eeFileInfo) {
					
					$eeArray = explode('|', $eeFileInfo);
					
					$eeFilesSorted[ $eeArray[2] ] = $eeFileInfo; // Associative Array
				}
		
			} elseif($eeSortBy == 'Name') { // Alpha
				
				natcasesort($eeFiles);
				$eeFilesSorted = $eeFiles;
			
			} else { // Random
				
				$eeFilesSorted = shuffle($eeFiles);
				
				return $eeFilesSorted;
			}
			
			ksort($eeFilesSorted); // Sort by the key
			
			// If Descending
			if($eeSortOrder == 'Descending') {
				$eeFilesSorted = array_reverse($eeFilesSorted);
			}
			
			$eeFilesSorted = array_values($eeFilesSorted); // Reindex the array keys
		}
		
		return $eeFilesSorted;
	}
	

	
	
	
	
	// Send a system notification email, via ee-email-engine.php 
	public function eeSFL_AjaxEmail($eeSFL_UploadJob, $eeSFL_Notify) {
		
		global $eeSFL_Log;
		
		// Email Notifications
		$eeSFL_Message = '';
		$eeSFL_Body = '';
		
		// Notifications via AJAX Engine
		if($_POST AND !@$eeSFL_UploadJob['Error'] AND !is_admin() ) {
		
			$eeSFL_Log['notice'][] = 'Sending Notification to ' . $eeSFL_Notify;
			
			// Create nonce to be checked on the other side
			$eeSFL_EmailNonce = wp_create_nonce('ee-simple-file-list-email');
			
			// Build the Message Body
			
			$eeSFL_Body = __('Greetings', 'ee-simple-file-list') . ",\n\n";
				
			$eeSFL_Body .= $eeSFL_UploadJob['Message'] . "\n\n";
			
			// Get Form Input?
			if(@$_POST['eeSFL_Email']) {
				
				$eeSFL_Name = substr(filter_var(@$_POST['eeSFL_Name'], FILTER_SANITIZE_STRING), 0, 64);
				$eeSFL_Name = strip_tags($eeSFL_Name);
				$eeSFL_Body .= __('Uploaded By', 'ee-simple-file-list') . ': ' . ucwords($eeSFL_Name) . " - ";
				
				$eeSFL_Email = substr(filter_var(@$_POST['eeSFL_Email'], FILTER_VALIDATE_EMAIL), 0, 128);
				$eeSFL_Body .= strtolower($eeSFL_Email) . "\n\n";
				$eeSFL_ReplyTo = $eeSFL_Name . ' <' . $eeSFL_Email . '>';
				
				$eeSFL_Notes = substr(filter_var(@$_POST['eeSFL_Notes'], FILTER_SANITIZE_STRING), 0, 5012);
				$eeSFL_Notes = strip_tags($eeSFL_Notes);
				$eeSFL_Body .= $eeSFL_Notes . "\n\n";
		
			}
			
			$eeSFL_Body .= "\n\n----------------------\n\nVia: Simple File List, " . __('located at', 'ee-simple-file-list') . ' ' . get_permalink();
			
			// Send the message to the Email Engine via Ajax
			
			$eeOutput = '<script type="text/javascript">
				
				console.log("Simple File List - Upload Notification");
				
				function eeSFL_Notification() {
				
					var eeSFL_Url = "' . plugin_dir_url( __FILE__ ) . 'ee-email-engine.php' . '";
					var eeSFL_Xhr = new XMLHttpRequest();
					var eeSFL_FormData = new FormData();
					    
					console.log("Calling Email Engine: " + eeSFL_Url);
					    
					eeSFL_Xhr.open("POST", eeSFL_Url, true);
					    
				    eeSFL_Xhr.onreadystatechange = function() {
				        
				        if (eeSFL_Xhr.readyState == 4) {
			            
			            	// Every thing ok
				            console.log("RESPONSE: " + eeSFL_Xhr.responseText);
				            
				            if(eeSFL_Xhr.responseText == "SENT") {
					            
				            	console.log("Message Sent");
								
					        } else {
						    	
						    	console.log("XHR Status: " + eeSFL_Xhr.status);
						    	console.log("XHR State: " + eeSFL_Xhr.readyState);
						    	
						    	var n = eeSFL_Xhr.responseText.search("<"); // Error condition
						    	
						    	if(n === 0) {
							    	console.log("Error Returned: " + eeSFL_Xhr.responseText);
							    }
							    return false;
					        }
				        
				        } else {
					    	console.log("XHR Status: " + eeSFL_Xhr.status);
					    	console.log("XHR State: " + eeSFL_Xhr.readyState);
					    	return false;
				        }
				    };';
				    
				    // Security First
				    $eeSFL_Timestamp = time();
				    $eeSFL_TimestampMD5 = md5('eeSFL_' . $eeSFL_Timestamp);
				    
				    $eeSFL_Body = json_encode($eeSFL_Body);
				    
				    $eeOutput .= '
				    
				    eeSFL_FormData.append("eeSFL_Timestamp", "' . $eeSFL_Timestamp . '");
				     
				    eeSFL_FormData.append("eeSFL_Token", "' . $eeSFL_TimestampMD5 . '");
				    
				    eeSFL_FormData.append("eeSFL_Notify", "' . $eeSFL_Notify . '");
				    
	 			    eeSFL_FormData.append("eeSFL_Body", ' . $eeSFL_Body . ');
				        
				    eeSFL_Xhr.send(eeSFL_FormData);
				    
				}
				
				';
				
				$eeOutput .= 'eeSFL_Notification();'; // Run the above function right now
				
				$eeOutput .= '
				
			</script>'; // Ends $eeOutput
			
			// $eeSFL_Log['notice'][] = $eeSFL_Body;
			
			return $eeOutput;
		
		} else {
			$eeSFL_Log[] = 'Upload Notification Missing Input';
		}
		
		
	}
	
	
	// Upload Info Form Display
	public function eeSFL_UploadInfoForm() {
		
		$eeOutput = '<div id="eeUploadInfoForm"><h4>' . __('Your Information', 'ee-simple-file-list') . '</h4>
			
			<label for="eeSFL_Name">' . __('Name', 'ee-simple-file-list') . ':</label>
			<input placeholder="(required)" required type="text" name="eeSFL_Name" value="" id="eeSFL_Name" size="64" maxlength="64" /> 
			
			<label for="eeSFL_Email">' . __('Email', 'ee-simple-file-list') . ':</label>
			<input placeholder="(required)" required type="email" name="eeSFL_Email" value="" id="eeSFL_Email" size="64" maxlength="128" />
			
			<label for="eeSFL_Notes">' . __('Comments', 'ee-simple-file-list') . ':</label>
			<textarea name="eeSFL_Notes" id="eeSFL_Notes" rows="5" cols="64" maxlength="5012"></textarea></div>';
			
		return $eeOutput;
	
	}
	
	
	// The form submission results bar at the top of the admin pages
	public function eeSFL_ResultsDisplay($eeSFL_Results, $eeResultType) { // error, updated, etc...
		
		$eeReturn = '<div class="';
		
		if(is_admin()) {
			$eeReturn .= $eeResultType;
		} else {
			$eeReturn .= 'eeResult';
		}
		
		$eeReturn .= '"><p>';
		$eeReturn .= eeSFL_MessageDisplay($eeSFL_Results); // Parse the message array
		$eeReturn .= '</p></div>';
		
		return $eeReturn;
	}
	
	
	
	// Problem Display / Error reporting
	public function eeSFL_MessageDisplay($eeSFL_Message) {
		
		$eeReturn = '';
		
		$eeAdmin = is_admin();
		
		if(is_array($eeSFL_Message)) {
			
			if(!$eeAdmin) { $eeReturn .= '<div id="eeMessageDisplay">'; }
			
			$eeReturn .= '<ul>'; // Loop through $eeSFL_Log['messages'] array
			foreach($eeSFL_Message as $key => $value) { 
				if(is_array($value)) {
					foreach ($value as $value2) {
						$eeReturn .= "<li>$value2</li>\n";
					}
				} else {
					$eeReturn .= "<li>$value</li>\n";
				}
			}
			$eeReturn .= "</ul>\n";
			
			if(!$eeAdmin) { $eeReturn .= '</div>'; }
			
			return $eeReturn;
			
		} else {
			return $eeSFL_Message;
		}
	}
	
	
	
	
	public function eeSFL_WriteLogData($eeSFL_ThisLog) {
		
		$eeDate = date('Y-m-d:h:m');
		
		$eeLogNow = get_option('eeSFL-Log'); // Stored as an array
		
		// Log Size Management
		$eeSizeCheck = serialize($eeLogNow);
		if(strlen($eeSizeCheck) > 16777215) { // Using MEDIUMTEXT Limit, even tho options are LONGTEXT.
			$eeLogNow = array(); // Clear
		}
		
		$eeLogNow[$eeDate][] = $eeSFL_ThisLog;
		
		update_option('eeSFL-Log', $eeLogNow);
		
		return TRUE;
	}

	
} // END Class ?>