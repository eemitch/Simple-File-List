<?php // Simple File List Script: ee-class-uploads.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Class' ) ) exit('ERROR 98 - ee-class-uploads'); // Exit if nonce fails

class eeSFL_BASE_UploadClass {
	
	public $eeUploadedFiles = array(); // Save the original file names for an upload job
	
	
	public function eeSFL_UploadForm() {
		
		global $eeSFL_BASE;
		$eeOutput = '';
		
		// Detect Which SFL
		if(is_object($eeSFL_BASE)) {
			
			$eeObject = $eeSFL_BASE;
			$eeListID = 1;
			$eeCurrentFolder = '';
		
		} else {
			
			global $eeSFL; $eeObject = $eeSFL;
			$eeListID = $eeSFL->eeListID;
		
			// Check for a Sub-Folder
			if(isset($_REQUEST['eeFolder']) AND $eeObject->eeListRun == 1) { // Adjust the path based on REQUEST arg
				$eeCurrentFolder = sanitize_text_field(urldecode($_REQUEST['eeFolder'])) . '/'; 
			} elseif( !empty($eeObject->eeShortcodeFolder) ) {
				$eeCurrentFolder = str_replace('&#34;', '', $eeObject->eeShortcodeFolder) . '/'; // Fix for uploading to draft status page
			} else {
				$eeCurrentFolder = FALSE;
			}
		}
		
		// User Messaging
		$eeOutput .= $eeObject->eeSFL_ResultsNotification();
			
		$eeOutput .= '
		
		<!-- Simple File List Uploader -->
				
		<form action="' . $eeObject->eeSFL_GetThisURL() . '" method="POST" enctype="multipart/form-data" name="eeSFL_UploadForm" id="eeSFL_UploadForm">
		
		<input type="hidden" name="MAX_FILE_SIZE" value="' .(($eeObject->eeListSettings['UploadMaxFileSize']*1024)*1024) . '" />
		<input type="hidden" name="ee" value="1" />
		<input type="hidden" name="eeSFL_Upload" value="TRUE" />
		<input type="hidden" name="eeListID" value="' . $eeListID . '" />
		<input type="hidden" name="eeSFL_FileCount" value="" id="eeSFL_FileCount" />
		<input type="hidden" name="eeSFL_FileList" value="" id="eeSFL_FileList" />';
		
		if($eeObject->eeEnvironment['wpUserID'] > 0) { $eeOutput .= '
		<input type="hidden" name="eeSFL_FileOwner" value="' . $eeObject->eeEnvironment['wpUserID'] . '" id="eeSFL_FileOwner" />'; }
		
		if($eeCurrentFolder) { $eeOutput .= '
		<input type="hidden" name="eeSFL_UploadFolder" value="' . urlencode($eeCurrentFolder) . '" id="eeSFL_UploadFolder" />'; }
		
		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-upload-form', 'ee-simple-file-list-upload-form-nonce', TRUE, FALSE);
		
		$eeOutput .= '
		
		<h2 class="eeSFL_UploadFilesTitle">' . __('Upload Files', 'ee-simple-file-list-pro') . '</h2>
		
		<div class="eeClearFix" id="eeSFL_FileDropZone" ondrop="eeSFL_DropHandler(event);" ondragover="eeSFL_DragOverHandler(event);">';
			
		$eeName = ''; $eeEmail = '';
		
		$wpUserObj = wp_get_current_user();
		
		if($wpUserObj) {
			$eeName = $wpUserObj->first_name . ' ' . $wpUserObj->last_name;
			$eeEmail = $wpUserObj->user_email;
		}
		
		$eeOutput .= '
		
		<div id="eeUploadInfoForm" class="eeClearFix">';
			
		if(!$eeEmail AND $eeObject->eeListSettings['GetUploaderInfo'] == 'YES') {
			
			$eeOutput .= '
			
			<label for="eeSFL_Name">' . __('Name', 'ee-simple-file-list-pro') . ':</label>
			<input type="text" name="eeSFL_Name" value="" id="eeSFL_Name" size="64" maxlength="64" /> 
			
			<label for="eeSFL_Email">' . __('Email', 'ee-simple-file-list-pro') . ':</label>
			<input type="text" name="eeSFL_Email" value="" id="eeSFL_Email" size="64" maxlength="128" />';
			
		} else {
			
			$eeOutput .= '
				
			<input type="hidden" id="eeSFL_Name" name="eeSFL_Name" value="' . $eeName . '" />
			<input type="hidden" id="eeSFL_Email" name="eeSFL_Email" value="' . $eeEmail . '" />';
		}
		
		if($eeObject->eeListSettings['GetUploaderDesc'] == 'YES' OR is_admin() ) {
			
			$eeOutput .= '<label for="eeSFL_FileDesc">' . __('Description', 'ee-simple-file-list-pro') . '</label>
			
			<textarea placeholder="' . __('Add a description (optional)', 'ee-simple-file-list-pro') . '" name="eeSFL_FileDesc" id="eeSFL_FileDesc" rows="5" cols="64" maxlength="5012"></textarea>';
			
		}
			
		$eeOutput .= '</div>
		
		<input type="file" name="eeSFL_FileInput" id="eeSFL_FileInput" onchange="eeSFL_FileInputHandler(event)" multiple />
		
		<p id="eeSFL_FilesDrug"></p>
		
		<script>
				
		var eeSFL_ListID = "' . $eeListID . '";
		var eeSFL_FileUploadDir = "' . urlencode($eeCurrentFolder) . '";
		var eeSFL_FileLimit = ' . $eeObject->eeListSettings['UploadLimit'] . ';
		var eeSFL_UploadMaxFileSize = ' . (($eeObject->eeListSettings['UploadMaxFileSize']*1024)*1024) . ';
		var eeSFL_FileFormats = "' . str_replace(' ' , '', $eeObject->eeListSettings['FileFormats']) . '";
		var eeSFL_Nonce = "' . wp_create_nonce('ee-simple-file-list-upload') . '";
		var eeSFL_UploadEngineURL = "' . admin_url( 'admin-ajax.php') . '";
					
		</script>
		
		<span id="eeSFL_UploadProgress"><em class="eeHide">' . __('Processing the Upload', 'ee-simple-file-list-pro') . '</em></span>
		
		<div id="eeSFL_FileUploadQueue"></div>
		
		<button type="button" class="button" name="eeSFL_UploadGo" id="eeSFL_UploadGo" onclick="eeSFL_UploadProcessor(eeSFL_FileObjects);">' . __('Upload', 'ee-simple-file-list-pro') . '</button>';
		
		// if($eeEmail AND !$eeAdmin) { $eeOutput .= '<p>' . __('Submitter:', 'ee-simple-file-list-pro') . ' ' . $eeName . ' (' . $eeEmail . ')</p>'; }
		
		if($eeObject->eeListSettings['ShowUploadLimits'] == 'YES') {
		
			$eeOutput .= '<p class="sfl_instuctions">' . __('File Limit', 'ee-simple-file-list-pro') . ': ' . $eeObject->eeListSettings['UploadLimit'] . ' ' . __('files', 'ee-simple-file-list-pro') . '<br />
			
			' . __('Size Limit', 'ee-simple-file-list-pro') . ': ' . $eeObject->eeListSettings['UploadMaxFileSize'] . ' MB
			
			' . __('per file', 'ee-simple-file-list-pro') . '.<br />
			
			' . __('Types Allowed', 'ee-simple-file-list-pro') . ': ' . str_replace(',', ', ', $eeObject->eeListSettings['FileFormats'])  . '<br />
			
			' . __('Drag-and-drop files here or use the Browse button.', 'ee-simple-file-list-pro') . '</p>';
		
		}
		
		$eeOutput .= '
		
		</div>
		
		</form>';
		
		return $eeOutput;
	}
	
	
	
	
	
	// Check for an Upload Job
	public function eeSFL_UploadCheck($eeListRun) {
		
		if($eeListRun > 1 ) { return; }
		
		global $eeSFL_BASE;
		$eeListID = 1;
		
		$eeUploaded = FALSE; // Show Confirmation

		// Check for an upload job, then run notification routine.
		if(isset($_POST['eeSFL_Upload'])) {
		
			// Detect Which SFL
			if(is_object($eeSFL_BASE)) { $eeObject = $eeSFL_BASE; } else { global $eeSFL; $eeObject = $eeSFL; }
			
			if(isset($_POST['eeListID'])) {
				$eeListID = preg_replace("/[^0-9]/i", '', $_POST['eeListID']);
			}
			
			if( $eeListID >=1 ) { $this->eeSFL_ProcessUploadJob($eeListID); $eeObject->eeListID = $eeListID; }
			
			if(is_object($eeSFL_BASE)) {
				
				if( is_admin() ) { eeSFL_BASE_UploadCompletedAdmin(); // Action Hook: eeSFL_UploadCompletedAdmin  <-- Admin side
					} else { eeSFL_BASE_UploadCompleted(); } // Action Hook: eeSFL_UploadCompleted <-- Front side
				
			} else {
				
				if( is_admin() ) { eeSFL_UploadCompletedAdmin(); // Action Hook: eeSFL_UploadCompletedAdmin  <-- Admin side
					} else { eeSFL_UploadCompleted(); } // Action Hook: eeSFL_UploadCompleted <-- Front side
			}
			
			if($eeObject->eeListSettings['UploadConfirm'] == 'YES' OR is_admin() ) { $eeUploaded = TRUE; }
			
		}
		
		return $eeUploaded;
	}
	
	
	
	
	
	
	// Process an Upload Job, Update the DB as Needed and Return the Results in a Nice Message
	public function eeSFL_ProcessUploadJob($eeListID) {
	
		global $eeSFL_BASE;
		
		// Detect Which SFL
		if(is_object($eeSFL_BASE)) { 
			$eeObject = $eeSFL_BASE;
			$eeGo = eeSFL_BASE_Go;
			$eeTime = eeSFL_BASE_noticeTimer();
		} else { 
			global $eeSFL, $eeSFLF, $eeSFLA, $eeSFL_Tasks;
			$eeObject = $eeSFL;
			$eeGo = eeSFL_Go;
			$eeTime = eeSFL_noticeTimer();
		}
		
		$eeUploadFolder = FALSE;
		
		$eeObject->eeLog[$eeGo]['notice'][] = $eeTime . ' - Processing the Upload Job...';
		
		// Get a list of the original file names that were uploaded. JSON STRING
		$eeFileListString = stripslashes($_POST['eeSFL_FileList']); // ["Sunset2.jpg","Sunset.jpg","Boats.jpg"]
		$eeFileListArray = json_decode($eeFileListString);
		
		if(!is_array($eeFileListArray)) { 
				
			$eeObject->eeLog[$eeGo]['error'][] = 'Upload String Not a JSON Array.';
			return FALSE;
		}
		
		
		// Get the File Count
		$eeFileCount = count($eeFileListArray);
		
		
		if( isset($_POST['eeSFL_UploadFolder']) ) { // Pro
			$eeUploadFolder = esc_textarea(sanitize_text_field( urldecode($_POST['eeSFL_UploadFolder']) ));
		}
		
			
		$eeObject->eeLog[$eeGo]['notice'][] = $eeTime . ' - ' . $eeFileCount . ' Files Uploaded';
		
		// Check for Form Nonce
		if(check_admin_referer( 'ee-simple-file-list-upload-form', 'ee-simple-file-list-upload-form-nonce')) {
			
			$eeUploadJob = ''; // This will be the well-formed message we return
			
			// Semantics
			if($eeFileCount > 1) { 
				$eeUploadJob .= $eeFileCount . ' ' . __('Files Uploaded', 'ee-simple-file-list');	
			} else {
				$eeUploadJob .= __('File Uploaded', 'ee-simple-file-list');
			}
			$eeUploadJob .= ":" . PHP_EOL . PHP_EOL;
			
			// Get the existing array
			if(empty($eeObject->eeAllFiles)) {
				$eeObject->eeAllFiles = get_option('eeSFL_FileList_' . $eeListID);
			}
			
			// Loop through the uploaded files, original names.
			if(count($eeFileListArray)) {
				
				foreach($eeFileListArray as $eeKey => $eeFile) { 
					
					$eeFile = sanitize_text_field($eeFile);
					$eeFile = urlencode($eeUploadFolder . $eeFile); // Tack on any sub-folder of FileListDir
					
					// Check if Name was Sanitized
					$eeFileOriginal = FALSE; // Transient is named using the original file name and has a value of the sanitized name
					$eeFileSanitized = get_transient('eeSFL-Renamed-' . $eeFile); // Name, will include the sub-folder
					
					if($eeFileSanitized) {
						
						$eeFileOriginal = $eeFile;
						$eeFileSanitized = urldecode($eeFileSanitized); // The sanitized name
						delete_transient('eeSFL-Renamed-' . $eeFile); // Thank you
						$eeFile = $eeFileSanitized;
					
					} else {
						$eeFile = urldecode($eeFile);
					}
					
					// Check to be sure the file is there
					if( is_file(ABSPATH . $eeObject->eeListSettings['FileListDir'] . $eeFile) ) { 
						
						$eeObject->eeLog[$eeGo]['notice'][] = $eeTime . ' - Creating File Array: ' . $eeFile;
							
						$eeFound = FALSE;
						
						if($eeObject->eeListSettings['AllowOverwrite'] == 'YES') { // Look for existing file array
							
							foreach( $eeObject->eeAllFiles as $eeKey => $eeThisFileArray ) {
								$eeFound = FALSE;
								if($eeThisFileArray['FilePath'] == $eeFile) { $eeFound = TRUE; break; }
							}
							
							if($eeFound) {
								$eeNewFileArray = $eeObject->eeSFL_BuildFileArray($eeFile, $eeThisFileArray);
							} else {
								$eeNewFileArray = $eeObject->eeSFL_BuildFileArray($eeFile); // Path relative to FileListDir
							}
						} else { // Build a new file array
							$eeNewFileArray = $eeObject->eeSFL_BuildFileArray($eeFile); // Path relative to FileListDir
						}
						
						
						// Use Original as the Nice Name
						if($eeFileOriginal AND $eeObject->eeListSettings['PreserveName'] == 'YES') {
							$eeNewFileArray['FileNiceName'] = basename(urldecode($eeFileOriginal)); // The original name
						}
						
						
						// Save Owner Info
						$eeID = get_current_user_id();
						
						if( !is_admin() ) { // Front-end only
							
							if($eeID === 0) {
								
								$eeNewFileArray['FileOwner'] = '0'; // Public
							
								if( isset($_POST['eeSFL_Name'])) {
										
									$eeString = esc_textarea(sanitize_text_field($_POST['eeSFL_Name']));
									
									if($eeString) {
										
										$eeNewFileArray['SubmitterName'] = $eeString; // Who uploaded the file
									}
								}
								
								if( isset($_POST['eeSFL_Email'])) {
									
									$eeString = filter_var( sanitize_email($_POST['eeSFL_Email']), FILTER_VALIDATE_EMAIL);
									
									if($eeString) {
										
										$eeNewFileArray['SubmitterEmail'] = $eeString; // Their email
									}
								}
							
							} else {
								$eeNewFileArray['FileOwner'] = $eeID;
							}
						} else {
							$eeNewFileArray['FileOwner'] = $eeID;
						}
						
						
						
						if( isset($_POST['eeSFL_FileDesc'])) {
							
							$eeString = esc_textarea(sanitize_text_field($_POST['eeSFL_FileDesc']));
							
							if($eeString) {
								
								$eeNewFileArray['FileDescription'] = $eeString; // A short description of the file
								$eeNewFileArray['SubmitterComments'] = $eeString; // What they said
							}
						}
						
						$eeObject->eeLog[$eeGo]['notice'][] = $eeTime . ' ——> Done';
						// $eeObject->eeLog[$eeGo]['notice'][] = $eeNewFileArray;
						
						$eeNewFileArray = array_filter($eeNewFileArray); // Remove empty elements
						
						// To add or modify
						if($eeFound) { 
							$eeObject->eeAllFiles[$eeKey] = $eeNewFileArray; // Updating current file array
						} else {
							$eeObject->eeAllFiles[] = $eeNewFileArray; // Append this file array to the big one
						}
						
						// If in a folder, update the folder dates
						if($eeUploadFolder) {
								
							$eePathPieces = explode('/', $eeUploadFolder);
							$eePartPaths = '';
							if(is_array($eePathPieces)) {
								foreach( $eePathPieces as $eePart ) {
									if($eePart) {
										$eePartPaths .= $eePart . '/';
										$eeObject->eeSFL_UpdateFileDetail($eePartPaths, 'FileDateChanged', date("Y-m-d H:i:s") );
									}
								}
							}
						}
						
						
						// If in a folder, update the folder dates
						if(isset($eeSFLF) AND $eeUploadFolder) {
								
							$eePathPieces = explode('/', $eeUploadFolder);
							$eePartPaths = '';
							if(is_array($eePathPieces)) {
								foreach( $eePathPieces as $eePart ) {
									if($eePart) {
										$eePartPaths .= $eePart . '/';
										$eeObject->eeSFL_UpdateFileDetail($eePartPaths, 'FileDateChanged', date("Y-m-d H:i:s") );
									}
								}
							}
						}
						
						
						// Create thumbnail if needed
						if(isset($eeSFL_Tasks) AND $eeObject->eeListSettings['ShowFileThumb'] == 'YES') {
							
							if(( $eeObject->eeListSettings['GeneratePDFThumbs'] == 'YES' AND $eeNewFileArray['FileExt'] == 'pdf' ) 
							
							OR ( $eeObject->eeListSettings['GenerateVideoThumbs'] == 'YES' AND in_array($eeNewFileArray['FileExt'], $eeObject->eeDynamicVideoThumbFormats) )
							
							) {
										
								// Start the background function: eeSFL_Background_GenerateThumbs()
								if(is_array($eeSFL_Tasks)) {
									$eeSFL_Tasks[$eeObject->eeListID]['GenerateThumbs'] = 'YES'; 
									update_option('eeSFL_Tasks', $eeSFL_Tasks);
								}
							}
						}
						
						
						// Notification Info
						if(isset($eeSFLA)) {
							$eeFileURL = $eeObject->eeEnvironment['wpSiteURL'] . 'ee-get-file/?list=' . $eeSFL->eeListID . '&file=' . $eeFile;
						} else {
							$eeFileURL = $eeObject->eeListSettings['FileListURL'] . $eeFile;
						}
						
						$eeUploadJob .=  $eeFile . " (" . $eeObject->eeSFL_FormatFileSize($eeNewFileArray['FileSize']) . ")" . PHP_EOL;
						$eeUploadJob .=  $eeFileURL . PHP_EOL . PHP_EOL;
					}
					
					// Add to our Upload Results Array
					$this->eeUploadedFiles[] = $eeFile;
				}
				
				// Add the Description
				if(!empty($eeNewFileArray['FileDescription'])) {
					$eeUploadJob .= $eeNewFileArray['FileDescription'] . PHP_EOL . PHP_EOL;
				}				
				
				$eeObject->eeSFL_SortFiles($eeObject->eeListSettings['SortBy'], $eeObject->eeListSettings['SortOrder']);
				
				// If uploading into a folder, increment the counts and sizes.
				if(isset($eeSFLF) AND $eeUploadFolder) { $eeSFLF->eeSFLF_UpdateFolderSizes(); }
				
				// Save the new array
				update_option('eeSFL_FileList_' . $eeListID, $eeObject->eeAllFiles);
					
				$eeObject->eeLog[$eeGo]['messages'][] = __('File Upload Complete', 'ee-simple-file-list');
				
				if( is_admin() ) {
					
					return TRUE;
				
				} else  {
					
					// Upload Email Notice
					if($eeObject->eeListSettings['Notify'] == 'YES') {
						
						// Send the Email Notification
						$eeObject->eeSFL_NotificationEmail($eeUploadJob);
						return TRUE;
						
					} else {
						return TRUE; // No notice wanted
					}
				}
				
				
			} else {
				wp_die('ERROR 98 - ProcessUpload');
			}
		
		} else {
			$eeObject->eeLog[$eeGo]['errors'][] = 'No Files to Process';
			return FALSE;
		}
	}


	// --------------------------------------------------------------------------



	// File Upload Engine
	public function eeSFL_FileUploader() {
		
		global $eeSFL_BASE;
		
		// return print_r($_POST, FALSE);
		
		// Detect Which SFL
		if(is_object($eeSFL_BASE)) { 
			$eeObject = $eeSFL_BASE;
			$eeListID = 1;
			$eeGo = eeSFL_BASE_Go;
			$eeTime = eeSFL_BASE_noticeTimer();
		} else { 
			global $eeSFL, $eeSFLF, $eeSFLA, $eeSFL_Tasks;
			$eeObject = $eeSFL;
			if(isset($_POST['eeSFL_ID'])) { $eeListID = preg_replace("/[^0-9]/i", '', $_POST['eeSFL_ID']); } else { $eeListID = 1; };
			$eeGo = eeSFL_Go;
			$eeTime = eeSFL_noticeTimer();
		}
		
		// The FILE object
		if(empty($_FILES)) { return 'The File Object is Empty'; }
		
		if( !is_admin() ) { // Front-side protections
		
			// Who should be uploading?
			switch ($eeObject->eeListSettings['AllowUploads']) {
			    case 'YES':
			        break; // Allow it, even if it's dangerous.
			    case 'USER':
			        // Allow it if logged in at all
			        if( get_current_user_id() ) { break; } else { return 'ERROR 97'; }
			    case 'ADMIN':
			        // Allow it if admin only.
			        if(current_user_can('manage_options')) { break; } else { return 'ERROR 97'; }
			        break;
				default: // Don't allow at all
					return 'ERROR 97';
			}
		} 
		
		// Get this List's Settings
		$eeObject->eeSFL_GetSettings($eeListID);	
		$eeFileUploadDir = $eeObject->eeListSettings['FileListDir'];
		
		
		// Sub-Folder - Relative to FileListDir
		if(!empty($_POST['eeSFL_FileUploadDir'])) {
			$eeFileUploadDir .= sanitize_text_field( urldecode($_POST['eeSFL_FileUploadDir']) );	
		}
		
	
		// Check size
		$eeFileSize = filter_var($_FILES['file']['size'], FILTER_VALIDATE_INT);
		$eeUploadMaxFileSize = $eeObject->eeListSettings['UploadMaxFileSize']*1024*1024; // Convert MB to B
		
		if($eeFileSize > $eeUploadMaxFileSize) {
			return __('File size is too large.', 'ee-simple-file-list');
		}
		
		// Go...
		if(is_dir(ABSPATH . $eeFileUploadDir)) {
				
			if(wp_verify_nonce(@$_POST['ee-simple-file-list-upload'], 'ee-simple-file-list-upload')) {
				
				// Temp file
				$eeTempFile = $_FILES['file']['tmp_name'];
				
				// Clean up messy names
				$eeFileName = $eeObject->eeSFL_SanitizeFileName($_FILES['file']['name']);
				
				// Check if it already exists
				if($eeObject->eeListSettings['AllowOverwrite'] == 'NO') { 
					$eeSFL_FileName = $eeObject->eeSFL_CheckForDuplicateFile($eeFileUploadDir . $eeFileName);
				}
				
				$eeObject->eeSFL_DetectUpwardTraversal($eeFileUploadDir . $eeFileName); // Die if foolishness
				
				$eePathParts = pathinfo($eeFileName);
				$eeFileNameAlone = $eePathParts['filename'];
				$eeExtension = strtolower($eePathParts['extension']); // We need to do this here and in eeSFL_ProcessUpload()
				
				// Format Check
				$eeFileFormatsArray = array_map('trim', explode(',', $eeObject->eeListSettings['FileFormats']));
				
				if(!in_array($eeExtension, $eeFileFormatsArray) OR in_array($eeExtension, $eeObject->eeForbiddenTypes)) {
					return __('File type not allowed', 'ee-simple-file-list') . ': (' . $eeExtension . ')';	
				}
				
				// Assemble FilePath
				$eeTargetFile = $eeFileUploadDir . $eeFileNameAlone . '.' . $eeExtension;
				
				// Check if the name has changed
				if($_FILES['file']['name'] != $eeFileName) {
					
					// Set a transient with the new name so we can get it in ProcessUpload() after the form is submitted
					$eeOldFilePath = 'eeSFL-Renamed-' . str_replace($eeObject->eeListSettings['FileListDir'], '', $eeFileUploadDir . $_FILES['file']['name']); // Strip the FileListDir
					$eeOldFilePath = esc_sql(urlencode($eeOldFilePath));
					$eeNewFilePath = str_replace($eeObject->eeListSettings['FileListDir'], '', $eeTargetFile); // Strip the FileListDir
					set_transient($eeOldFilePath, $eeNewFilePath, 900); // Expires in 15 minutes
				}
				
				$eeTarget = ABSPATH . $eeTargetFile;
				
				// return $eeTarget;
				
				// Save the file
				if( move_uploaded_file($eeTempFile, $eeTarget) ) {
					
					if(!is_file($eeTarget)) {
						return 'Error - File System Error.'; // No good.
					} else {
						
						// Check for corrupt images
						if( in_array($eeExtension, $eeObject->eeDynamicImageThumbFormats) ) {
							
							$eeString = implode('...', getimagesize($eeTarget) );
							
							if(!strpos($eeString, 'width=') OR !strpos($eeString, 'height=')) { // Make sure it's really an image
								
								unlink($eeTarget);
								
								return 'ERROR 99';
							}
						}
						
						// Update the File Date
						$eeDate = esc_textarea(sanitize_text_field($_POST['eeSFL_FileDate']));
						$eeDate = strtotime($eeDate);
						if($eeDate) {
							touch($eeTarget, $eeDate);  // Do nothing if bad date
						}
						
						// Build Image thumbs right away right away. We'll set other types to use the background job within eeSFL_ProcessUpload()
						if($eeObject->eeListSettings['ShowFileThumb'] == 'YES') {
							if( in_array($eeExtension, $eeObject->eeDynamicImageThumbFormats) ) {
					
								$eeTargetFile = str_replace($eeObject->eeListSettings['FileListDir'], '', $eeTargetFile); // Strip the FileListDir
								$eeObject->eeSFL_CheckThumbnail($eeTargetFile, $eeObject->eeListSettings);
							}
						}
						
						return 'SUCCESS';
					}
					 
				} else {
					return 'Cannot save the uploaded file: ' . $eeTargetFile;
				}
			
			} else {
				
				return 'ERROR 98 - FileUploader';
			}
			
		} else {
			return 'Upload Path Not Found: ' . $eeFileUploadDir;
		}
	}




	// Get Actual Max Upload Size
	public function eeSFL_ActualUploadMax() {
		
		$eeEnv = array();
		
		$eeEnv['upload_max_filesize'] = substr(ini_get('upload_max_filesize'), 0, -1); // PHP Limit (Strip off the "M")
		$eeEnv['post_max_size'] = substr(ini_get('post_max_size'), 0, -1); // PHP Limit (Strip off the "M")
		
		// Check which is smaller, upload size or post size.
		if ($eeEnv['upload_max_filesize'] <= $eeEnv['post_max_size']) { 
			return $eeEnv['upload_max_filesize'];
		} else {
			return $eeEnv['post_max_size'];
		}
	}

}
?>