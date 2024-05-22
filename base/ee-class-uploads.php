<?php
// Simple File List Pro - Copyright 2024
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code are not advised and may not be supported.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

class eeSFL_Uploads {
	
	protected $eeSFL;
	public function __construct(eeSFL_MainClass $eeSFL) { $this->eeSFL = $eeSFL; }
	// Usage: $this->eeSFL->eeListID
	
	// Save the original file names for an upload job
	public $eeUploadedFiles = array(); 
	
	
	// The Upload Form Display
	public function eeSFL_UploadForm() {
		
		global $eeSFL_Messaging, $eeSFL_Pro;
		
		$eeOutput = '';
		$eeCurrentFolder = FALSE;
		$eeListID = $this->eeSFL->eeListID;
		
		if(defined('eeSFL_Pro')) { // Pro Only
			// Check for a Sub-Folder
			if(isset($_REQUEST['eeFolder']) AND $this->eeSFL->eeListRun == 1) { // Adjust the path based on REQUEST arg
				$eeCurrentFolder = sanitize_text_field(urldecode($_REQUEST['eeFolder'])) . '/'; 
			} elseif( !empty($this->eeSFL->eeShortcodeFolder) ) {
				$eeCurrentFolder = str_replace('&#34;', '', $this->eeSFL->eeShortcodeFolder) . '/'; // Fix for uploading to draft status page
			} else {
				$eeCurrentFolder = FALSE;
			}
		}
		
		// User Messaging
		$eeOutput .= $eeSFL_Messaging->eeSFL_ResultsNotification();
			
		$eeOutput .= '
		
		<!-- Simple File List Uploader -->	
		<form action="' . $this->eeSFL->eeSFL_GetThisURL() . '" method="POST" enctype="multipart/form-data" name="eeSFL_UploadForm" id="eeSFL_UploadForm">
		
		<input type="hidden" name="MAX_FILE_SIZE" value="' . (($this->eeSFL->eeListSettings['UploadMaxFileSize']*1024)*1024) . '" />
		<input type="hidden" name="ee" value="1" />
		<input type="hidden" name="eeSFL_Upload" value="TRUE" />
		<input type="hidden" name="eeListID" value="' . $eeListID . '" />
		<input type="hidden" name="eeSFL_FileCount" value="" id="eeSFL_FileCount" />
		<input type="hidden" name="eeSFL_FileList" value="" id="eeSFL_FileList" />';
		
		if($this->eeSFL->eeEnvironment['wpUserID'] > 0) { $eeOutput .= '
		<input type="hidden" name="eeSFL_FileOwner" value="' . $this->eeSFL->eeEnvironment['wpUserID'] . '" id="eeSFL_FileOwner" />'; }
		
		if($eeCurrentFolder) { $eeOutput .= '
		<input type="hidden" name="eeSFL_UploadFolder" value="' . urlencode($eeCurrentFolder) . '" id="eeSFL_UploadFolder" />'; }
		
		$eeOutput .= wp_nonce_field(eeSFL_Nonce, 'eeSFL_UploadNonce', TRUE, FALSE);
		$eeOutput .= wp_nonce_field(eeSFL_Nonce, 'eeSFL_PostUploadNonce', TRUE, FALSE);
		
		$eeOutput .= '
		
		<h2 class="eeSFL_UploadFilesTitle">' . __('Upload Files', 'ee-simple-file-list') . '</h2>
		
		<div class="eeClearFix" id="eeSFL_FileDropZone" ondrop="eeSFL_DropHandler(event);" ondragover="eeSFL_DragOverHandler(event);">';
			
		$eeName = ''; $eeEmail = '';
		
		$wpUserObj = wp_get_current_user();
		
		if(!empty($wpUserObj->user_email)) {
			$eeName = $wpUserObj->first_name . ' ' . $wpUserObj->last_name;
			$eeEmail = $wpUserObj->user_email;
		}
		
		$eeOutput .= '
		
		<div id="eeUploadInfoForm" class="eeClearFix">';
			
		if(!$eeEmail AND $this->eeSFL->eeListSettings['GetUploaderInfo'] == 'YES') {
			
			$eeOutput .= '
			
			<label for="eeSFL_Name">' . __('Name', 'ee-simple-file-list') . ':</label>
			<input type="text" name="eeSFL_Name" value="" id="eeSFL_Name" size="64" maxlength="64" /> 
			
			<label for="eeSFL_Email">' . __('Email', 'ee-simple-file-list') . ':</label>
			<input type="text" name="eeSFL_Email" value="" id="eeSFL_Email" size="64" maxlength="128" />';
			
		}
		
		if($this->eeSFL->eeListSettings['GetUploaderDesc'] == 'YES' OR is_admin() ) {
			
			$eeOutput .= '<label for="eeSFL_FileDesc">' . __('Description', 'ee-simple-file-list') . '</label>
			
			<textarea placeholder="' . __('Add a description (optional)', 'ee-simple-file-list') . '" name="eeSFL_FileDesc" id="eeSFL_FileDesc" rows="5" cols="64" maxlength="5012"></textarea>';
			
		}
			
		$eeOutput .= '</div>
		
		<input type="file" name="eeSFL_FileInput" id="eeSFL_FileInput" onchange="eeSFL_FileInputHandler(event)" multiple />
		
		<p id="eeSFL_FilesDrug"></p>
		
		<script>	
		var eeSFL_ListID = "' . $eeListID . '";
		var eeSFL_FileUploadDir = "' . esc_js(urlencode($eeCurrentFolder)) . '";
		var eeSFL_FileLimit = ' . esc_js($this->eeSFL->eeListSettings['UploadLimit']) . ';
		var eeSFL_UploadMaxFileSize = ' . esc_js((($this->eeSFL->eeListSettings['UploadMaxFileSize']*1024)*1024)) . ';
		var eeSFL_FileFormats = "' . esc_js(str_replace(' ' , '', $this->eeSFL->eeListSettings['FileFormats'])) . '";
		var eeSFL_UploadEngineURL = "' . admin_url( 'admin-ajax.php') . '";			
		</script>
		
		<span id="eeSFL_UploadProgress"><em class="eeHide">' . __('Processing the Upload', 'ee-simple-file-list') . '</em></span>
		
		<div id="eeSFL_FileUploadQueue"></div>
		
		<button type="button" class="button eeSFL_Action" name="eeSFL_UploadGo" id="eeSFL_UploadGo" data-id="0" data-action="upload">' . __('Upload', 'ee-simple-file-list') . '</button>';
		
		// if($eeEmail AND !is_admin()) { $eeOutput .= '<p>' . __('Submitter:', 'ee-simple-file-list') . ' ' . $eeName . ' (' . $eeEmail . ')</p>'; }
		
		if($this->eeSFL->eeListSettings['ShowUploadLimits'] == 'YES') {
		
			$eeOutput .= '<p class="sfl_instuctions">' . __('File Limit', 'ee-simple-file-list') . ': ' . $this->eeSFL->eeListSettings['UploadLimit'] . ' ' . __('files', 'ee-simple-file-list') . '<br />
			
			' . __('Size Limit', 'ee-simple-file-list') . ': ' . esc_textarea($this->eeSFL->eeListSettings['UploadMaxFileSize']) . ' MB
			
			' . __('per file', 'ee-simple-file-list') . '.<br />
			
			' . __('Types Allowed', 'ee-simple-file-list') . ': ' . esc_textarea(str_replace(',', ', ', $this->eeSFL->eeListSettings['FileFormats']))  . '<br />
			
			' . __('Drag-and-drop files here or use the Browse button.', 'ee-simple-file-list') . '</p>';
		
		}
		
		$eeOutput .= '
		</div>
		</form>';
		
		return $eeOutput;
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
			
			$eeOutput .= '<textarea placeholder="' . __('Add an optional description', 'ee-simple-file-list') . '" name="eeSFL_Comments" id="eeSFL_Comments" rows="5" cols="64" maxlength="5012"></textarea>';
			
			if($eeEmail) { $eeOutput .= '<p>' . __('Submitter:', 'ee-simple-file-list') . ' ' . $eeName . ' (' . $eeEmail . ')</p>'; }
			
			if($eeEmail) {
				$eeOutput .= '<input type="hidden" id="eeSFL_Name" name="eeSFL_Name" value="' . $eeName . '" />
					<input type="hidden" id="eeSFL_Email" name="eeSFL_Email" value="' . $eeEmail . '" />';
			}
			
			$eeOutput .= '</div>';
			
		return $eeOutput;
	
	}
	
	
	
	
	
	// Check for an Upload Job
	public function eeSFL_UploadCheck($eeListRun) {
		
		if($eeListRun > 1 ) { return; }
		
		$eeListID = 1;
		$eeMessages = array('Upload Job Complete');
		
		$eeUploaded = FALSE; // Show Confirmation

		// Check for an upload job, then run notification routine.
		if(isset($_POST['eeSFL_Upload'])) {
		
			if(isset($_POST['eeListID'])) {
				$eeListID = preg_replace("/[^0-9]/i", '', $_POST['eeListID']);
			}
			
			if( $eeListID >= 1 ) { $this->eeSFL_ProcessUploadJob($eeListID); $this->eeSFL->eeListID = $eeListID; }
			
			$eeMessages[] = 'List ID: ' . $eeListID;
			
			// Add Custom Hook
			if( is_admin() ) { 
				$eeMessages[] = 'Back-End Upload Complete';
				do_action('eeSFL_Admin_Hook_Uploaded', $eeMessages);
			} else { 
				$eeMessages[] = 'Front-End Upload Complete';
				do_action('eeSFL_Hook_Uploaded', $eeMessages);
			}
			
			do_action('eeSFL_UploadCompletedAdmin');
			do_action('eeSFL_UploadCompleted');
			
			if($this->eeSFL->eeListSettings['UploadConfirm'] == 'YES' OR is_admin() ) { $eeUploaded = TRUE; }
		}
		
		return $eeUploaded;
	}
	
	
	
	
	
	
	// Process an Upload Job, Update the DB as Needed and Return the Results in a Nice Message
	public function eeSFL_ProcessUploadJob($eeListID) {
		
		global $eeSFL_Environment, $eeSFL_Messaging, $eeSFL_Thumbs, $eeSFL_Pro, $eeSFL_Tasks, $eeSFLA;
		
		if(!check_admin_referer(eeSFL_Nonce, 'eeSFL_PostUploadNonce')) { return 'WP Nonce Failure: ' . basename(__FILE__); }
		
		$eeTime = $this->eeSFL->eeSFL_NOW();
		$eeGo = eeSFL_Go;
		$eeUploadFolder = FALSE;
		
		$this->eeSFL->eeLog['notice'][] = $eeTime . ' - Processing the Upload Job...';
		
		// Get a list of the original file names that were uploaded. JSON STRING
		$eeFileListString = stripslashes($_POST['eeSFL_FileList']); // ["Sunset2.jpg","Sunset.jpg","Boats.jpg"]
		$eeFileListArray = json_decode($eeFileListString);
		
		if(!is_array($eeFileListArray)) { 
				
			$this->eeSFL->eeLog['error'][] = 'Upload String Not a JSON Array.';
			return FALSE;
		}
		
		
		// Get the File Count
		$eeFileCount = count($eeFileListArray);
		
		
		if( isset($_POST['eeSFL_UploadFolder']) ) { // Pro
			$eeUploadFolder = esc_textarea(sanitize_text_field( urldecode($_POST['eeSFL_UploadFolder']) ));
		}
		
			
		$this->eeSFL->eeLog['notice'][] = $eeTime . ' - ' . $eeFileCount . ' Files Uploaded';
		
		$eeUploadJob = ''; // This will be the well-formed message we return
		
		// Semantics
		if($eeFileCount > 1) { 
			$eeUploadJob .= $eeFileCount . ' ' . __('Files Uploaded', 'ee-simple-file-list');	
		} else {
			$eeUploadJob .= __('File Uploaded', 'ee-simple-file-list');
		}
		$eeUploadJob .= ":" . PHP_EOL . PHP_EOL;
		
		// Get the existing array
		if(empty($this->eeSFL->eeAllFiles)) {
			$this->eeSFL->eeAllFiles = get_option('eeSFL_FileList_' . $eeListID);
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
				if( is_file(ABSPATH . $this->eeSFL->eeListSettings['FileListDir'] . $eeFile) ) { 
					
					$this->eeSFL->eeLog['notice'][] = $eeTime . ' - Creating File Array: ' . $eeFile;
						
					$eeFound = FALSE;
					
					if($this->eeSFL->eeListSettings['AllowOverwrite'] == 'YES') { // Look for existing file array
						
						foreach( $this->eeSFL->eeAllFiles as $eeKey => $eeThisFileArray ) {
							$eeFound = FALSE;
							if($eeThisFileArray['FilePath'] == $eeFile) { $eeFound = TRUE; break; }
						}
						
						if($eeFound) {
							$eeNewFileArray = $this->eeSFL->eeSFL_BuildFileArray($eeFile, $eeThisFileArray);
						} else {
							$eeNewFileArray = $this->eeSFL->eeSFL_BuildFileArray($eeFile); // Path relative to FileListDir
						}
					} else { // Build a new file array
						$eeNewFileArray = $this->eeSFL->eeSFL_BuildFileArray($eeFile); // Path relative to FileListDir
					}
					
					
					// Use Original as the Nice Name
					if($eeFileOriginal AND $this->eeSFL->eeListSettings['PreserveName'] == 'YES') {
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
					
					$this->eeSFL->eeLog['notice'][] = $eeTime . ' ——> Done';
					// $this->eeSFL->eeLog['notice'][] = $eeNewFileArray;
					
					$eeNewFileArray = array_filter($eeNewFileArray); // Remove empty elements
					
					// To add or modify
					if($eeFound) { 
						$this->eeSFL->eeAllFiles[$eeKey] = $eeNewFileArray; // Updating current file array
					} else {
						$this->eeSFL->eeAllFiles[] = $eeNewFileArray; // Append this file array to the big one
					}
					
					// If in a folder, update the folder dates
					if($eeUploadFolder) {
							
						$eePathPieces = explode('/', $eeUploadFolder);
						$eePartPaths = '';
						if(is_array($eePathPieces)) {
							foreach( $eePathPieces as $eePart ) {
								if($eePart) {
									$eePartPaths .= $eePart . '/';
									$this->eeSFL->eeSFL_UpdateFileDetail($eePartPaths, 'FileDateChanged', date("Y-m-d H:i:s") );
								}
							}
						}
					}
					
					
					// If in a folder, update the folder dates
					if(isset($eeSFL_Pro) AND $eeUploadFolder) {
							
						$eePathPieces = explode('/', $eeUploadFolder);
						$eePartPaths = '';
						if(is_array($eePathPieces)) {
							foreach( $eePathPieces as $eePart ) {
								if($eePart) {
									$eePartPaths .= $eePart . '/';
									$this->eeSFL->eeSFL_UpdateFileDetail($eePartPaths, 'FileDateChanged', date("Y-m-d H:i:s") );
								}
							}
						}
					}
					
					
					// Create thumbnail if needed
					if(isset($eeSFL_Tasks) AND $this->eeSFL->eeListSettings['ShowFileThumb'] == 'YES') {
						
						if(( $this->eeSFL->eeListSettings['GeneratePDFThumbs'] == 'YES' AND $eeNewFileArray['FileExt'] == 'pdf' ) 
						
						OR ( $this->eeSFL->eeListSettings['GenerateVideoThumbs'] == 'YES' AND in_array($eeNewFileArray['FileExt'], $eeSFL_Thumbs->eeDynamicVideoThumbFormats) )
						
						) {
									
							// Start the background function: eeSFL_Background_GenerateThumbs()
							if(is_array($eeSFL_Tasks)) {
								$eeSFL_Tasks[$this->eeSFL->eeListID]['GenerateThumbs'] = 'YES'; 
								update_option('eeSFL_Tasks', $eeSFL_Tasks);
							}
						}
					}
					
					
					// Notification Info
					if(isset($eeSFLA)) {
						$eeFileURL = $this->eeSFL->eeEnvironment['wpSiteURL'] . 'ee-get-file/?list=' . $this->eeSFL->eeListID . '&file=' . $eeFile;
					} else {
						$eeFileURL = $this->eeSFL->eeListSettings['FileListURL'] . $eeFile;
					}
					
					$eeUploadJob .=  $eeFile . " (" . $this->eeSFL->eeSFL_FormatFileSize($eeNewFileArray['FileSize']) . ")" . PHP_EOL;
					$eeUploadJob .=  $eeFileURL . PHP_EOL . PHP_EOL;
				}
				
				// Add to our Upload Results Array
				$this->eeUploadedFiles[] = $eeFile;
			}
			
			// Add the Description
			if(!empty($eeNewFileArray['FileDescription'])) {
				$eeUploadJob .= $eeNewFileArray['FileDescription'] . PHP_EOL . PHP_EOL;
			}				
			
			$this->eeSFL->eeSFL_SortFiles($this->eeSFL->eeListSettings['SortBy'], $this->eeSFL->eeListSettings['SortOrder']);
			
			// Adjust the counts and sizes.
			if(isset($eeSFL_Pro) AND $eeUploadFolder) { $eeSFL_Pro->eeSFL_UpdateFolderSizesAndCounts(); }
			
			// Save the new array
			update_option('eeSFL_FileList_' . $eeListID, $this->eeSFL->eeAllFiles);
				
			$this->eeSFL->eeLog['messages'][] = __('File Upload Complete', 'ee-simple-file-list');
			
			if( is_admin() ) {
				
				return TRUE;
			
			} else  {
				
				// Upload Email Notice
				if($this->eeSFL->eeListSettings['Notify'] == 'YES') {
					
					// Send the Email Notification
					$eeSFL_Messaging->eeSFL_UploadEmail($eeUploadJob);
					$_POST = array();
					return TRUE;
					
				} else {
					$_POST = array();
					return TRUE; // No notice wanted
				}
			}
			
			
		} else {
			// No Files Found
		}
	
	}


	// --------------------------------------------------------------------------



	// File Upload Engine
	public function eeSFL_FileUploader() {
		
		global $eeSFL, $eeSFL_Environment, $eeSFL_Pro, $eeSFL_Tasks, $eeSFL_Thumbs, $eeSFLA;
		
		// WP Security
		if( !check_ajax_referer( eeSFL_Nonce, 'eeSecurity' ) ) { return 'WP Nonce Failure: ' . basename(__FILE__); }
			
		if(isset($_POST['eeSFL_ID'])) { $eeListID = preg_replace("/[^0-9]/i", '', $_POST['eeSFL_ID']); } else { $eeListID = 1; }
		// $eeGo = eeSFL_Go;
		// $eeTime = $this->eeSFL->eeSFL_NOW();
		
		// The FILE object
		if(empty($_FILES)) { return 'The File Object is Empty'; } 
		
		// Get this List's Settings
		$this->eeSFL->eeSFL_GetSettings($eeListID);	
		$eeFileUploadDir = $this->eeSFL->eeListSettings['FileListDir'];
		
		$eeReferer = wp_get_referer(); // Front-side protections
		if( !strpos($eeReferer, '/wp-admin/') ) { $eeIsAdmin = TRUE; } else { $eeIsAdmin = FALSE; }
		
		if( !$eeIsAdmin ) { // Front-End Checks
		
			if($eeSFLA) { $this->eeSFL->eeListSettings['AllowUploads'] = $eeSFLA->eeSFLA_UploadsFirewall(); }
			
			// Who should be uploading?
			switch ($this->eeSFL->eeListSettings['AllowUploads']) {
				case 'YES':
					break; // Allow it, even if it's dangerous.
				case 'USER':
					// Allow it if logged in at all
					if( $eeSFL->eeEnvironment['wpUserID'] ) { break; } else { return __('Requires User Privileges to Upload', 'ee-simple-file-list'); }
				case 'ADMIN':
					// Allow it if admin only.
					if(current_user_can('manage_options')) { break; } else { return __('Requires Admin Privileges to Upload', 'ee-simple-file-list'); }
					break;
				default: // Don't allow at all
					return __('Uploading is Not Allowed', 'ee-simple-file-list');
			}
		}
		
		
		// Sub-Folder - Relative to FileListDir
		if(!empty($_POST['eeSFL_FileUploadDir'])) {
			$eeFileUploadDir .= sanitize_text_field( urldecode($_POST['eeSFL_FileUploadDir']) );	
		}
		
	
		// Check size
		$eeFileSize = filter_var($_FILES['file']['size'], FILTER_VALIDATE_INT);
		$eeUploadMaxFileSize = $this->eeSFL->eeListSettings['UploadMaxFileSize']*1024*1024; // Convert MB to B
		
		if($eeFileSize > $eeUploadMaxFileSize) {
			return __('File size is too large.', 'ee-simple-file-list');
		}
		
		// Go...
		if(is_dir(ABSPATH . $eeFileUploadDir)) {
							
			// Temp file
			$eeTempFile = $_FILES['file']['tmp_name'];
			
			// Clean up messy names
			$eeFileName = $eeSFL_Environment->eeSFL_SanitizeFileName($_FILES['file']['name']);
			
			// Check if it already exists
			if($this->eeSFL->eeListSettings['AllowOverwrite'] == 'NO') { 
				$eeSFL_FileName = $eeSFL_Environment->eeSFL_CheckForDuplicateFile($eeFileUploadDir . $eeFileName);
			}
			
			$this->eeSFL->eeSFL_DetectUpwardTraversal($eeFileUploadDir . $eeFileName); // Die if foolishness
			
			$eePathParts = pathinfo($eeFileName);
			$eeFileNameAlone = $eePathParts['filename'];
			$eeExtension = strtolower($eePathParts['extension']); // We need to do this here and in eeSFL_ProcessUpload()
			
			// Format Check
			$eeFileFormatsArray = array_map('trim', explode(',', $this->eeSFL->eeListSettings['FileFormats']));
			
			if(!in_array($eeExtension, $eeFileFormatsArray) OR in_array($eeExtension, $this->eeSFL->eeForbiddenTypes)) {
				return __('File type not allowed', 'ee-simple-file-list') . ': (' . $eeExtension . ')';	
			}
			
			// Assemble FilePath
			$eeTargetFile = $eeFileUploadDir . $eeFileNameAlone . '.' . $eeExtension;
			
			// Check if the name has changed
			if($_FILES['file']['name'] != $eeFileName) {
				
				// Set a transient with the new name so we can get it in ProcessUpload() after the form is submitted
				$eeOldFilePath = 'eeSFL-Renamed-' . str_replace($this->eeSFL->eeListSettings['FileListDir'], '', $eeFileUploadDir . $_FILES['file']['name']); // Strip the FileListDir
				$eeOldFilePath = esc_sql(urlencode($eeOldFilePath));
				$eeOldFilePath = preg_replace('/\++/', '+', $eeOldFilePath);
				$eeNewFilePath = str_replace($this->eeSFL->eeListSettings['FileListDir'], '', $eeTargetFile); // Strip the FileListDir
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
					if( in_array($eeExtension, $eeSFL_Thumbs->eeDynamicImageThumbFormats) ) {
						
						$eeString = implode('...', getimagesize($eeTarget) );
						
						if(!strpos($eeString, 'width=') OR !strpos($eeString, 'height=')) { // Make sure it's really an image
							
							unlink($eeTarget);
							
							return __('Corrupt Image Not Accepted.', 'ee-simple-file-list');
						}
					}
					
					// Update the File Date
					$eeDate = esc_textarea(sanitize_text_field($_POST['eeSFL_FileDate']));
					$eeDate = strtotime($eeDate);
					if($eeDate) {
						touch($eeTarget, $eeDate);  // Do nothing if bad date
					}
					
					// Build Image thumbs right away right away. We'll set other types to use the background job within eeSFL_ProcessUpload()
					if($this->eeSFL->eeListSettings['ShowFileThumb'] == 'YES') {
						if( in_array($eeExtension, $eeSFL_Thumbs->eeDynamicImageThumbFormats) ) {
				
							$eeTargetFile = str_replace($this->eeSFL->eeListSettings['FileListDir'], '', $eeTargetFile); // Strip the FileListDir
							$eeSFL_Thumbs->eeSFL_CheckThumbnail($eeTargetFile, $this->eeSFL->eeListSettings);
						}
					}
					
					return 'SUCCESS';
				}
				 
			} else {
				return 'Cannot save the uploaded file: ' . $eeTargetFile;
			}
			
		} else {
			return 'Upload Path Not Found: ' . $eeFileUploadDir;
		}
	}


}
?>