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
    // public $filesTotalCount = 0;
    
    public $eeDynamicImageThumbFormats = array('gif', 'jpg', 'jpeg', 'png');
    public $eeDynamicVideoThumbFormats = array('mp4', 'mov', 'wmf', 'webm');
    public $eeExcludedFileNames = array('error_log', 'index.html');
    private $eeForbiddenTypes = array('.php', '.exe', '.js', '.com', '.wsh', '.vbs');
    
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
		
		// The Wordpress Uploads Location
		$wpUploadArray = wp_upload_dir();
		$wpUploadDir = $wpUploadArray['basedir'];
		$eeSFL_Env['wpUploadDir'] = $wpUploadDir . '/';
		$eeSFL_Env['wpUploadURL'] = $wpUploadArray['baseurl'] . '/';
		
		// SFL DEFAULT
		$eeSFL_Env['FileListDefaultDir'] = $eeSFL_Env['wpUploadDir'] . 'simple-file-list/';
		$eeSFL_Env['FileListDefaultURL'] = $eeSFL_Env['wpPluginsURL'] . 'simple-file-list/';
		
		// PHP Limits
		$eeSFL_Env['upload_max_upload_size'] = substr(ini_get('upload_max_filesize'), 0, -1); // Strip off the "M".
		$eeSFL_Env['post_max_size'] = substr(ini_get('post_max_size'), 0, -1); // Strip off the "M".
		
		// Check which is smaller, upload size or post size.
		if ($eeSFL_Env['upload_max_upload_size'] <= $eeSFL_Env['post_max_size']) { 
			$eeSFL_Env['the_max_upload_size'] = $eeSFL_Env['upload_max_upload_size'];
		} else {
			$eeSFL_Env['the_max_upload_size'] = $eeSFL_Env['post_max_size'];
		}
		
		return $eeSFL_Env;
    }
    
    
    
   
    // Get Data
    public function eeSFL_Config($eeSFL_Env, $eeID = 1) {
	    
	    global $eeSFL_Log;
	    
	    $eeSFL_Config = array();
	    
	    // Getting the settings array
	    $eeArray = get_option('eeSFL-Settings');
	    
		// Get sub-array for this list ID
		$eeSFL_Config['eeID'] = $eeID;  // The List ID
		$eeSFL_Config = array_merge($eeSFL_Config, $eeArray[$eeID]);
		
		// Check Environment
		if($eeSFL_Env['the_max_upload_size'] < $eeSFL_Config['UploadMaxFileSize']) {
			$eeSFL_Config['UploadMaxFileSize'] = $eeSFL_Env['the_max_upload_size']; // If Env is lower, set to that.
		}
		
		$eeSFL_Config['FileListDirName'] = $eeSFL_Config['FileListDir']; // Relative to the WP Root
		$eeSFL_Config['FileListDir'] = ABSPATH . $eeSFL_Config['FileListDir']; // The Full Path
		$eeSFL_Config['FileListURL'] = $eeSFL_Env['wpSiteURL'] . $eeSFL_Config['FileListDirName']; // The Full URL
				
		// Get the files for this List ID
		// $eeSFL_Config['eeSFL_Files'] = get_transient('eeSFL-' . $eeSFL->eeListID . '-Files');
		
		// echo '<pre>'; print_r($eeSFL_Env); echo '</pre>';
		// echo '<pre>'; print_r($eeSFL_Config); echo '</pre>'; exit;
		
		$eeSFL_Log['Env'] = $eeSFL_Env;
		$eeSFL_Log['Config'] = $eeSFL_Config;
		
		// The whole point...	
		return $eeSFL_Config; // Associative array
		
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
	public function eeSFL_createFileListArray($eeID, $eeDir, $eeForce = FALSE) {
		
		global $eeSFL_Log;
		$eeArray = array();
		
		if($eeForce) {
			delete_transient('eeSFL-FileList-' . $eeID);
		} else {
			$eeArray = get_transient('eeSFL-FileList-' . $eeID); // From the database
			if(!$eeArray) { $eeArray = array(); } // Ensure it's not FALSE
		}
		
		// We store our array of file info in a transient
		if( !count($eeArray) ) {
			
			$eeSFL_Log[] = 'Creating new search transient for List # ' . $eeID . ' at ' . $eeDir;
			
			$eeFileArray = $this->eeSFL_IndexFileListDir($eeDir);
			
			foreach( $eeFileArray as $eeKey => $eeValue) {
				
				$eeFileTime = @filemtime($eeValue);
				$eeFileSize = @eeSFL_GetFileSize($eeValue);
				
				// Get just the folder/file
				$ee1 = strlen(ABSPATH . $eeDir);
				$ee2 = strlen($eeValue);
				$eeStart = $ee2 - ($ee2-$ee1) -1;
				$eeFile = substr($eeValue, $eeStart);
				
				// Check forbidden types
				foreach( $this->eeForbiddenTypes as $eeValue2) {
					if(strpos($eeFile, $eeValue2)) {
						$eeFile = FALSE;
					}
				}
				
				if($eeFile) {
					$eeArray[] = $eeFile . '|' . date_i18n('Y-m-d', $eeFileTime) . '|' .  $eeFileSize;
				}
			}
			
			if(@count($eeArray) AND set_transient('eeSFL-FileList-' . $eeID, $eeArray, 6 * HOUR_IN_SECONDS) ) {
				$eeSFL_Log['eeSFL']['transient set'] = $eeArray;
			} else {
				$eeSFL_Log['errors'][] = 'File Transient Could Not Be Set.';
				return FALSE;
			}
		
			$eeArray = array_unique($eeArray); // Remove duplicates
		
		}
		
		// echo '<pre>'; print_r($eeSFL_Log); echo '</pre>'; exit;
			
		return $eeArray;
	}
	
	
	
	
	// Get All the Files
	private function eeSFL_IndexFileListDir($eeDir) {
	    
	    global $eeSFLF, $eeSFL_Log;
	    
	    $eeFileArray = array();
	    
	    $eeDir = ABSPATH . $eeDir;
	    
	    if($eeSFLF) {
		    
		    $eeSFL_Log[] = 'Getting files and folders for searching...';
		    
		    $eeFileArray = $eeSFLF->eeSFLF_IndexCompleteFileListDirectory($eeDir);
		    
	    } else {
		    
		    $eeSFL_Log[] = 'Getting files from: ' . $eeDir; 
		    
		    
		    $eeFileNameOnlyArray = scandir($eeDir);
		    
		    foreach($eeFileNameOnlyArray as $eeValue) {
		    	
		    	if(strpos($eeValue, '.') !== 0 AND is_file($eeDir . $eeValue) ) {
			    	$eeFileArray[] = $eeDir . $eeValue; // Add the path
		    	}
		    }
	    }
	    
	    if(!count($eeFileArray)) {
		    $eeSFL_Log['errors'][] = 'No Files Found';
	    }
		
		// $eeSFL_Log['fileArray'][] = $eeFileArray;

		return $eeFileArray;
	}
	
	
	
	
	
	
	
	
	public function eeSFL_SortFiles($eeSFL_FileListDir, $eeSFL_Files, $eeSFL_SortBy, $eeSFL_SortOrder) {
		
		if(count($eeSFL_Files)) {
			
			// Files by Name
			if($eeSFL_SortBy == 'Date') { // Files by Date
				
				$eeSFL_FilesByDate = array();
				foreach($eeSFL_Files as $eeSFL_File){
					$eeSFL_FileDate = filemtime($eeSFL_FileListDir . $eeSFL_File); // Get byte Date, yum.
					$eeSFL_FilesByDate[$eeSFL_File] = $eeSFL_FileDate; // Associative Array
				}
				
				// Sort order
				if($eeSFL_SortOrder == 'Descending') {
					arsort($eeSFL_FilesByDate);
				} else {
					asort($eeSFL_FilesByDate); // Sort by Date, ascending
				}
				
				$eeSFL_Files = array();
				
				foreach( $eeSFL_FilesByDate as $eeKey => $eeValue){
					$eeSFL_Files[] = $eeKey;
				}
				
			} elseif($eeSFL_SortBy == 'Size') { // Files by Size
				
				$eeSFL_FilesBySize = array();
				foreach($eeSFL_Files as $eeSFL_File){
					$eeSFL_FileSize = filesize($eeSFL_FileListDir . $eeSFL_File); // Get byte size, yum.
					$eeSFL_FilesBySize[$eeSFL_File] = $eeSFL_FileSize; // Associative Array
				}
				
				// Sort order
				if($eeSFL_SortOrder == 'Descending') {
					arsort($eeSFL_FilesBySize);
				} else {
					asort($eeSFL_FilesBySize); // Sort by Date, ascending
				}
				
				$eeSFL_Files = array();
				
				foreach( $eeSFL_FilesBySize as $eeKey => $eeValue){
					$eeSFL_Files[] = $eeKey;
				}
		
			} elseif($eeSFL_SortBy == 'Name') { // Alpha
				
				@natcasesort($eeSFL_Files);
				
				// Sort order
				if($eeSFL_SortOrder == 'Descending') {
					arsort($eeSFL_Files);
				}
			}
		}
		
		return $eeSFL_Files;
		
	}
	
	
	
	
	
	// Our Basic File List
	public function eeSFL_ListFiles($eeSFL_FileListDir, $eeSFL_ForceReIndex = FALSE) {
		
		global $eeSFL, $eeSFL_Log;
		
		$eeSFL_Files = FALSE;
		$eeSFL_Files = get_transient('eeSFL-' . $eeSFL->eeListID . '-Files');
		
		// Check Transient First
		if( is_array($eeSFL_Files) OR !$eeSFL_ForceReIndex === FALSE ) {
			$eeSFL_Log['transient'][] = $eeSFL_Files;
			return $eeSFL_Files;
		}

		// Else, Re-create the file list
		$eeSFL_Files = array();
		
		$eeSFL_Log[] = 'Recreating the File List Transient ...';
		$eeSFL_Log[] = $eeSFL_FileListDir;
		
		// List files in folder, add to array.
		if ($eeSFL_Handle = @opendir($eeSFL_FileListDir)) {
		
			while(false !== ($eeSFL_File = readdir($eeSFL_Handle))) {
				
				// Don't list excluded  or hidden files
				if(!@in_array($eeSFL_File, $this->eeExcludedFileNames) OR strpos($eeSFL_File, '.') === 0) {
					
					// Not allowed to list: .php, .exe, .js, .com, .wsh, .vbs
					$eeSFL_ForbiddenFormats = array('php', 'exe', 'js', 'com', 'wsh', 'vbs');
					
					$eeExt = substr(strrchr($eeSFL_File,'.'), 1); // Get the extension, lazy way
					
					if(!in_array($eeExt, $eeSFL_ForbiddenFormats)) {
					
						if(@is_file($eeSFL_FileListDir . '/' . $eeSFL_File)) { // Don't show directories.
							
							// Don't list the home dir
							if($eeSFL_File == 'wp-config.php') {
								$eeSFL_Log['errors'][] = 'File List Directory Error: Listing Root Directory';
								$eeSFL_Log['errors'][] = $eeSFL_FileListDir;
								$eeSFL_Log['errors'][] = 'List Terminated.';
								return FALSE;
							}
							
							// Get file info...
							$eeSFL_Files[] = $eeSFL_File; // Add the file to the array
						}
					}	
				}
			}
			@closedir($eeSFL_Handle);
			
		} else {
		
			$eeSFL_Log['errors'][] = "Can't read the files in the Uploads folder.";
			
			return FALSE;
		}
		
		// $eeSFL_Log[] = $eeSFL_Files;
		
		// Set Transient
		set_transient('eeSFL-' . $eeSFL->eeListID . '-Files', $eeSFL_Files, 14400); // Expires in 4 hours, or if there's an upload.
		
		$eeSFL_Log['transient'][] = $eeSFL_Files;
		
		return $eeSFL_Files;
		
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
	
	
	
	
	
	public function eeSFL_FileThumbnail($eeSFL_FileListDir, $eeSFL_UploadURL, $eeSFL_File, $eeSFL_FileThumbSize = 64) {
		
		global $eeSFL_Log, $eeSFL_UploadMaxFileSize;
		
		// Config
		$eeExt = FALSE;
		$eeScreenshot = FALSE;
		$eeThumbURL = FALSE;
		$eeThumbsURL = $eeSFL_UploadURL . '.thumbnails/'; // Dynamicly created thumbnails are here
		$eeThumbsPATH = $eeSFL_FileListDir . '.thumbnails/'; // Path to them
		
		// Get File Info
		$eePathParts = pathinfo($eeThumbsPATH . $eeSFL_File);
		$eeDirName = $eePathParts['dirname'];
		$eeBaseName = $eePathParts['basename'];
		$eeExt = strtolower(@$eePathParts['extension']);
		$eeFileName = $eePathParts['filename'];
		
		
		// Get Transient need ID
		
		
		
		
		// Else, we make or assign a default thumbnail image ...
		
		

		
		 
		
		
		// Is there already a thumb?
		if(is_file($eeThumbsPATH . 'thumb_' . $eeSFL_File)) {
			return $eeThumbsURL . 'thumb_' . $eeSFL_File;
		} else {
			$eePNG = str_replace($eeExt, 'png', $eeSFL_File); // Check for video thumb, which has a different extension.
			if(is_file($eeThumbsPATH . 'thumb_' . $eePNG)) {
				return $eeThumbsURL . 'thumb_' . $eePNG;
			}
		}
		
		
		// FFmpeg Support
		$eeVideoFormats = array('avi', 'flv', 'm4v', 'mov', 'mp4', 'wmv'); // Is this a video ?
		
		if(in_array($eeExt, $eeVideoFormats)) {
			
			if(trim(shell_exec('type -P ffmpeg'))) { // Check for FFMPEG
				
				$eeSFL_Log[] = 'FFmpeg Installed!';
				
				$eeExt = 'png'; // Set the extension
				$eeScreenshot = $eeThumbsPATH . 'eeScreenshot_' . $eeFileName . '.' . $eeExt; // Create a temporary file
				
				// Create a full-sized image at the one-second mark
				$eeCommand = 'ffmpeg -i ' . $eeSFL_FileListDir . $eeSFL_File . ' -ss 00:00:01.000 -vframes 1 ' . $eeScreenshot;
				
				$eeFFmpeg = trim(shell_exec($eeCommand));
					
				if(is_file($eeScreenshot)) {
					
					$eeSFL_File = basename($eeScreenshot); // It worked
				
					// Switch the path that the script below will use to look for the file used to make a thumb of.
					$eeSFL_FileListDir = $eeThumbsPATH;
				
				} else {
					$eeSFL_Log['errors'][] = __('FFmpeg Error - File Not Created', 'ee-simple-file-list');
					$eeSFL_Log['errors'][] = $eeSFL_File;
					$eeSFL_Log['errors'][] = $eeVideoThumb;
				}	
			} else {
				// $eeSFL_Log[] = 'FFmpeg Not Installed';
			}
		}
		
		
		// Generate a Thumbnail Image
		if(!$eeExt OR strpos($eeExt, '.') === 0) { // It's a Folder or Hidden
			
			$eeExt = 'folder';
			
		} else {
		
			// Dynamically Create Thumbnails --------------
			
			// Known image files
			$eeImageExts = array('gif', 'jpg', 'jpeg', 'png');
			
			if(in_array($eeExt, $eeImageExts)) { // Just for known image files... 					
					
				// Thank Wordpress for this easyness.
				$eeFileImage = wp_get_image_editor($eeSFL_FileListDir . $eeSFL_File); // Try to open the file
	        
		        if (!is_wp_error($eeFileImage)) { // Image File Opened
		            
		            $eeFileImage->resize($eeSFL_FileThumbSize, $eeSFL_FileThumbSize, TRUE); // Create the thumbnail
		            
		            if(strpos($eeSFL_File, 'eScreenshot_')) {
			            $eeSFL_File = str_replace('eeScreenshot_', '', $eeSFL_File); // Strip the temp term
		            }
		            
		            $eeFileImage->save($eeThumbsPATH . 'thumb_' . $eeSFL_File); // Save the file
		            
		            $eeThumbURL = $eeThumbsURL . 'thumb_' . $eeSFL_File; // Build full URL
		            
		            if($eeScreenshot) {
			            unlink($eeScreenshot); // Delete the screeshot file
		            }
		        
		        } else { // Cannot open
			     
			        // $eeLog[] = 'Not an Image: ' . $eeSFL_File;   
		        }
			}
		}		
		
		// Assign Default Thumbnail
		if($eeThumbURL) { 
			
			$eeSFL_Log[] = 'Thumbnail Image Created for ' . $eeSFL_File;
		
		} else {
			
			$eeSFL_Log['errors'][] = 'Failed to create thumbnail for: ' . $eeSFL_File;
		}
		
		// The Return
		return $eeThumbURL; // Full path to image file
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