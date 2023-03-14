<?php // Simple File List Script: ee-class-uploads.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Class' ) ) exit('ERROR 98 - ee-class-uploads'); // Exit if nonce fails

class eeSFL_BASE_UploadClass {
	
	public $eeUploadedFiles = array(); // Save the original file names for an upload job
	
	
	// Process an Upload Job, Update the DB as Needed and Return the Results in a Nice Message
	public function eeSFL_ProcessUploadJob() {
	
		global $eeSFL_BASE;
		
		// Detect Which SFL
		if(is_object($eeSFL_BASE)) { 
			$eeObject = $eeSFL_BASE;
			$eeListID = 1;
			$eeGo = eeSFL_BASE_Go;
			$eeTime = eeSFL_BASE_noticeTimer();
		} else { 
			global $eeSFL, $eeSFLF, $eeSFLA, $eeSFL_Tasks;
			$eeObject = $eeSFL;
			$eeListID = $eeSFL->eeListID;
			$eeGo = eeSFL_Go;
			$eeTime = eeSFL_noticeTimer();
		}
		
		$eeSFL_UploadFolder = FALSE;
		
		// echo '<pre>'; print_r($_POST); echo '</pre>'; exit;
		
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
			$eeSFL_UploadFolder = esc_textarea(sanitize_text_field( urldecode($_POST['eeSFL_UploadFolder']) ));
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
							
				// $eeFound = FALSE;
				
				foreach($eeFileListArray as $eeKey => $eeFile) { 
					
					$eeFile = sanitize_text_field($eeFile);
					$eeFile = urlencode($eeSFL_UploadFolder . $eeFile); // Tack on any sub-folder of FileListDir
					
					// Check if Name was Sanitized
					$eeFileOriginal = FALSE; // Transient is named using the original file name and has a value of the sanitized name
					$eeFileSanitized = get_transient('eeSFL-Renamed-' . $eeFile); // Name, will include the sub-folder
					
					if($eeFileSanitized) {
						
						$eeFileOriginal = $eeFile;
						$eeFileSanitized = urldecode($eeFileSanitized); // The sanitized name
						delete_transient('eeSFL-Renamed-' . $eeFile); // Thank you
						$eeFile = $eeFileSanitized;
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
							$eeNewFileArray['FileNiceName'] = urldecode(basename($eeFileOriginal)); // The original name
						}
						
						
						// Save Owner Info
						if( !is_admin() ) { // Front-end only
							
							$eeID = get_current_user_id();
							
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
						if($eeSFL_UploadFolder) {
								
							$eePathPieces = explode('/', $eeSFL_UploadFolder);
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
						if(isset($eeSFLF) AND $eeSFL_UploadFolder) {
								
							$eePathPieces = explode('/', $eeSFL_UploadFolder);
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
				if(isset($eeSFLF) AND $eeSFL_UploadFolder) { $eeSFLF->eeSFLF_UpdateFolderSizes(); }
				
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






	// File Upload Engine
	function eeSFL_FileUploader() {
		
		global $eeSFL_BASE;
		
		// Detect Which SFL
		if(is_object($eeSFL_BASE)) { $eeObject = $eeSFL_BASE; $eeListID = 1; $eeGo = eeSFL_BASE_Go; } 
			else { global $eeSFL; $eeObject = $eeSFL; $eeListID = $eeSFL->eeListID; $eeGo = eeSFL_Go; }
		
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
		$eeObject->eeSFL_GetSettings(1);	
		$eeSFL_FileUploadDir = $eeObject->eeListSettings['FileListDir'];
	
		// Check size
		$eeSFL_FileSize = filter_var($_FILES['file']['size'], FILTER_VALIDATE_INT);
		$eeSFL_UploadMaxFileSize = $eeObject->eeListSettings['UploadMaxFileSize']*1024*1024; // Convert MB to B
		
		if($eeSFL_FileSize > $eeSFL_UploadMaxFileSize) {
			return "File size is too large.";
		}
		
		// Go...
		if(is_dir(ABSPATH . $eeSFL_FileUploadDir)) {
				
			if(wp_verify_nonce(@$_POST['ee-simple-file-list-upload'], 'ee-simple-file-list-upload')) {
				
				// Temp file
				$eeTempFile = $_FILES['file']['tmp_name'];
				
				// Clean up messy names
				$eeSFL_FileName = $eeObject->eeSFL_SanitizeFileName($_FILES['file']['name']);
				
				// Check if it already exists
				if($eeObject->eeListSettings['AllowOverwrite'] == 'NO') { 
					$eeSFL_FileName = $eeObject->eeSFL_CheckForDuplicateFile($eeSFL_FileUploadDir . $eeSFL_FileName);
				}
				
				$eeObject->eeSFL_DetectUpwardTraversal($eeSFL_FileUploadDir . $eeSFL_FileName); // Die if foolishness
				
				$eeSFL_PathParts = pathinfo($eeSFL_FileName);
				$eeSFL_FileNameAlone = $eeSFL_PathParts['filename'];
				$eeSFL_Extension = strtolower($eeSFL_PathParts['extension']); // We need to do this here and in eeSFL_ProcessUpload()
				
				// Format Check
				$eeSFL_FileFormatsArray = array_map('trim', explode(',', $eeObject->eeListSettings['FileFormats']));
				
				if(!in_array($eeSFL_Extension, $eeSFL_FileFormatsArray) OR in_array($eeSFL_Extension, $eeObject->eeForbiddenTypes)) {
					return 'File type not allowed: (' . $eeSFL_Extension . ')';	
				}
				
				// Assemble FilePath
				$eeSFL_TargetFile = $eeSFL_FileUploadDir . $eeSFL_FileNameAlone . '.' . $eeSFL_Extension;
				
				// Check if the name has changed
				if($_FILES['file']['name'] != $eeSFL_FileName) {
					
					// Set a transient with the new name so we can get it in ProcessUpload() after the form is submitted
					$eeOldFilePath = 'eeSFL-Renamed-' . str_replace($eeObject->eeListSettings['FileListDir'], '', $eeSFL_FileUploadDir . $_FILES['file']['name']); // Strip the FileListDir
					$eeOldFilePath = esc_sql(urlencode($eeOldFilePath));
					$eeNewFilePath = str_replace($eeObject->eeListSettings['FileListDir'], '', $eeSFL_TargetFile); // Strip the FileListDir
					set_transient($eeOldFilePath, $eeNewFilePath, 900); // Expires in 15 minutes
				}
				
				$eeTarget = ABSPATH . $eeSFL_TargetFile;
				
				// return $eeTarget;
				
				// Save the file
				if( move_uploaded_file($eeTempFile, $eeTarget) ) {
					
					if(!is_file($eeTarget)) {
						return 'Error - File System Error.'; // No good.
					} else {
						
						// Check for corrupt images
						if( in_array($eeSFL_Extension, $eeObject->eeDynamicImageThumbFormats) ) {
							
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
							if( in_array($eeSFL_Extension, $eeObject->eeDynamicImageThumbFormats) ) {
					
								$eeSFL_TargetFile = str_replace($eeObject->eeListSettings['FileListDir'], '', $eeSFL_TargetFile); // Strip the FileListDir
								$eeObject->eeSFL_CheckThumbnail($eeSFL_TargetFile, $eeObject->eeListSettings);
							}
						}
						
						return 'SUCCESS';
					}
					 
				} else {
					return 'Cannot save the uploaded file: ' . $eeSFL_TargetFile;
				}
			
			} else {
				
				return 'ERROR 98 - FileUploader';
			}
			
		} else {
			return 'Upload Path Not Found: ' . $eeSFL_FileUploadDir;
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