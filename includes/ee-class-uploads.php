<?php // Simple File List Script: ee-class-uploads.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Class' ) ) exit('ERROR 98 - ee-class-uploads'); // Exit if nonce fails

class eeSFL_BASE_UploadClass {
	
	public $eeUploadedFiles = array(); // Save the original file names for an upload job
	
	
	
	// Process an Upload Job, Update the DB as Needed and Return the Results in a Nice Message
	public function eeSFL_ProcessUploadJob() {
	
		global $eeSFL_BASE;
		
		$eeGo = eeSFL_BASE_Go;
		$eeSFL_UploadFolder = FALSE;
		
		echo '<pre>'; print_r($_POST); echo '</pre>'; exit;
		
		$eeSFL_BASE->eeLog[$eeGo]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Processing the Upload Job...';
		
		// Get a list of the original file names that were uploaded. JSON STRING
		$eeFileListString = stripslashes($_POST['eeSFL_FileList']); // ["Sunset2.jpg","Sunset.jpg","Boats.jpg"]
		$eeFileListArray = json_decode($eeFileListString);
		
		if(!is_array($eeFileListArray)) { 
				
			$eeSFL_BASE->eeLog[$eeGo]['error'][] = 'Upload String Not a JSON Array.';
			return FALSE;
		}
		
		
		// Get the File Count
		$eeFileCount = count($eeFileListArray);
		
		
/*
		if( isset($_POST['eeSFL_UploadFolder']) ) { // Pro
			
			$eeSFL_UploadFolder = esc_textarea(sanitize_text_field( urldecode($_POST['eeSFL_UploadFolder']) ));
		
		}
*/
		
		
			
		$eeSFL_BASE->eeLog[$eeGo]['notice'][] = $eeFileCount . ' Files Uploaded';
		
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
			if(empty($eeSFL_BASE->eeAllFiles)) {
				$eeSFL_BASE->eeAllFiles = get_option('eeSFL_FileList_1');
			}
			
			// Loop through the uploaded files
			if(count($eeFileListArray)) {
							
				$eeFound = FALSE;
				
				foreach($eeFileListArray as $eeKey => $eeFile) { 
					
					$eeFile = sanitize_text_field($eeFile);
					
					$eeFilePath = urlencode($eeSFL_UploadFolder . $eeFile); // Tack on any sub-folder of FileListDir
					$eeFileNew = get_transient('eeSFL-Renamed-' . $eeFilePath); // Sanitized name, will include the sub-folder
					
					if($eeFileNew) {
						
						$eeFile = urldecode($eeFileNew); // The actual name;
						delete_transient('eeSFL-Renamed-' . $eeFileNew); // Thank you
						
						// If sanitized, use original as the Nice Name
						if($eeSFL_BASE->eeListSettings['PreserveName']) {
							
							$eeFileNiceName = FALSE;
							
							$eeFileNiceName = urldecode(basename($eeFile)); // The original name
							
							if($eeFileNiceName) { $eeNewFileArray['FileNiceName'] = $eeFileNiceName; }
						}
					}
					
					
					
					if( is_file(ABSPATH . $eeSFL_BASE->eeListSettings['FileListDir'] . $eeFile) ) { // Check to be sure the file is there
						
						$eeSFL_BASE->eeLog[$eeGo]['notice'][] = eeSFL_BASE_noticeTimer() . ' - Creating File Array: ' . $eeFile;
						
						if($eeSFL_BASE->eeListSettings['AllowOverwrite'] == 'YES') { // Look for existing file array
							
							foreach( $eeSFL_BASE->eeAllFiles as $eeKey => $eeThisFileArray ) {
								$eeFound = FALSE;
								if($eeThisFileArray['FilePath'] == $eeFile) { $eeFound = TRUE; break; }
							}
							
							if($eeFound) {
								$eeNewFileArray = $eeSFL_BASE->eeSFL_BuildFileArray($eeFile, $eeThisFileArray);
							} else {
								$eeNewFileArray = $eeSFL_BASE->eeSFL_BuildFileArray($eeFile); // Path relative to FileListDir
							}
						} else { // Build a new file array
							$eeNewFileArray = $eeSFL_BASE->eeSFL_BuildFileArray($eeFile); // Path relative to FileListDir
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
						
						$eeSFL_BASE->eeLog[$eeGo]['notice'][] = '——> Done';
						$eeSFL_BASE->eeLog[$eeGo]['notice'][] = $eeNewFileArray;
						
						$eeNewFileArray = array_filter($eeNewFileArray); // Remove empty elements
						
						// To add or modify
						if($eeFound) { 
							$eeSFL_BASE->eeAllFiles[$eeKey] = $eeNewFileArray; // Updating current file array
						} else {
							$eeSFL_BASE->eeAllFiles[] = $eeNewFileArray; // Append this file array to the big one
						}
						
						// If in a folder, update the folder dates
						if($eeSFL_UploadFolder) {
								
							$eePathPieces = explode('/', $eeSFL_UploadFolder);
							$eePartPaths = '';
							if(is_array($eePathPieces)) {
								foreach( $eePathPieces as $eePart ) {
									if($eePart) {
										$eePartPaths .= $eePart . '/';
										$eeSFL_BASE->eeSFL_UpdateFileDetail($eePartPaths, 'FileDateAdded', date("Y-m-d H:i:s") );
									}
								}
							}
						}
						
						// Notification Info
						$eeFileURL = $eeSFL_BASE->eeListSettings['FileListURL'] . $eeFile;
						
						$eeUploadJob .=  $eeFile . " (" . eeSFL_BASE_FormatFileSize($eeNewFileArray['FileSize']) . ")" . PHP_EOL;
						$eeUploadJob .=  $eeFileURL . PHP_EOL . PHP_EOL;
					}
					
					// Add to our Upload Results Array
					$eeSFL_BASE->eeEnvironment['UploadedFiles'][] = $eeFile;
				}
				
				echo '<pre>'; print_r($eeFileListArray); echo '</pre>';
				
				echo '<pre>'; print_r($eeSFL_BASE->eeEnvironment['UploadedFiles']); echo '</pre>'; 
				
				echo '<pre>'; print_r($eeSFL_BASE->eeLog[$eeGo]); echo '</pre>'; exit;
				
				// Add the Description
				if(!empty($eeNewFileArray['FileDescription'])) {
					$eeUploadJob .= $eeNewFileArray['FileDescription'] . PHP_EOL . PHP_EOL;
				}				
				
				$eeSFL_BASE->eeSFL_SortFiles($eeSFL_BASE->eeListSettings['SortBy'], $eeSFL_BASE->eeListSettings['SortOrder']);
				
				// Save the new array
				update_option('eeSFL_FileList_1', $eeSFL_BASE->eeAllFiles);
					
				$eeSFL_BASE->eeLog[$eeGo]['messages'][] = __('File Upload Complete', 'ee-simple-file-list');
				
				if( is_admin() ) {
					
					return TRUE;
				
				} else  {
					
					// Upload Email Notice
					if($eeSFL_BASE->eeListSettings['Notify'] == 'YES') {
						
						// Send the Email Notification
						$eeSFL_BASE->eeSFL_NotificationEmail($eeUploadJob);
						return TRUE;
						
					} else {
						return TRUE; // No notice wanted
					}
				}
				
				
			} else {
				wp_die('ERROR 98 - ProcessUpload');
			}
		
		} else {
			$eeSFL_BASE->eeLog[$eeGo]['errors'][] = 'No Files to Process';
			return FALSE;
		}
	}






	// File Upload Engine
	function eeSFL_FileUploader() {
		
		global $eeSFL_BASE;
		
		// The FILE object
		if(empty($_FILES)) { return 'The File Object is Empty'; }
		
		if( !is_admin() ) { // Front-side protections
		
			// Who should be uploading?
			switch ($eeSFL_BASE->eeListSettings['AllowUploads']) {
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
		$eeSFL_BASE->eeSFL_GetSettings(1);	
		$eeSFL_FileUploadDir = $eeSFL_BASE->eeListSettings['FileListDir'];
	
		// Check size
		$eeSFL_FileSize = filter_var($_FILES['file']['size'], FILTER_VALIDATE_INT);
		$eeSFL_UploadMaxFileSize = $eeSFL_BASE->eeListSettings['UploadMaxFileSize']*1024*1024; // Convert MB to B
		
		if($eeSFL_FileSize > $eeSFL_UploadMaxFileSize) {
			return "File size is too large.";
		}
		
		// Go...
		if(is_dir(ABSPATH . $eeSFL_FileUploadDir)) {
				
			if(wp_verify_nonce(@$_POST['ee-simple-file-list-upload'], 'ee-simple-file-list-upload')) {
				
				// Temp file
				$eeTempFile = $_FILES['file']['tmp_name'];
				
				// Clean up messy names
				$eeSFL_FileName = eeSFL_BASE_SanitizeFileName($_FILES['file']['name']);
				
				// Check if it already exists
				if($eeSFL_BASE->eeListSettings['AllowOverwrite'] == 'NO') { 
					$eeSFL_FileName = eeSFL_BASE_CheckForDuplicateFile($eeSFL_FileUploadDir . $eeSFL_FileName);
				}
				
				eeSFL_BASE_DetectUpwardTraversal($eeSFL_FileUploadDir . $eeSFL_FileName); // Die if foolishness
				
				$eeSFL_PathParts = pathinfo($eeSFL_FileName);
				$eeSFL_FileNameAlone = $eeSFL_PathParts['filename'];
				$eeSFL_Extension = strtolower($eeSFL_PathParts['extension']); // We need to do this here and in eeSFL_ProcessUpload()
				
				// Format Check
				$eeSFL_FileFormatsArray = array_map('trim', explode(',', $eeSFL_BASE->eeListSettings['FileFormats']));
				
				if(!in_array($eeSFL_Extension, $eeSFL_FileFormatsArray) OR in_array($eeSFL_Extension, $eeSFL_BASE->eeForbiddenTypes)) {
					return 'File type not allowed: (' . $eeSFL_Extension . ')';	
				}
				
				// Assemble FilePath
				$eeSFL_TargetFile = $eeSFL_FileUploadDir . $eeSFL_FileNameAlone . '.' . $eeSFL_Extension;
				
				// Check if the name has changed
				if($_FILES['file']['name'] != $eeSFL_FileName) {
					
					// Set a transient with the new name so we can get it in ProcessUpload() after the form is submitted
					$eeOldFilePath = 'eeSFL-Renamed-' . str_replace($eeSFL_BASE->eeListSettings['FileListDir'], '', $eeSFL_FileUploadDir . $_FILES['file']['name']); // Strip the FileListDir
					$eeOldFilePath = esc_sql(urlencode($eeOldFilePath));
					$eeNewFilePath = str_replace($eeSFL_BASE->eeListSettings['FileListDir'], '', $eeSFL_TargetFile); // Strip the FileListDir
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
						if( in_array($eeSFL_Extension, $eeSFL_BASE->eeDynamicImageThumbFormats) ) {
							
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
						if($eeSFL_BASE->eeListSettings['ShowFileThumb'] == 'YES') {
							if( in_array($eeSFL_Extension, $eeSFL_BASE->eeDynamicImageThumbFormats) ) {
					
								$eeSFL_TargetFile = str_replace($eeSFL_BASE->eeListSettings['FileListDir'], '', $eeSFL_TargetFile); // Strip the FileListDir
								$eeSFL_BASE->eeSFL_CheckThumbnail($eeSFL_TargetFile, $eeSFL_BASE->eeListSettings);
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



}
?>