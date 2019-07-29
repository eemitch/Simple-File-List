<?php  // Simple File List - ee-uploader.php - mitchellbennis@gmail.com

// Security	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('That is Noncense! (' . basename(__FILE__) . ')' ); // Exit if nonce fails
$eeSFL_UploadNonce = wp_create_nonce('ee-simple-file-list-upload'); // Checked in the upload engine.

$eeSFL_Log[] = 'Loaded: ee-uploader';

// Extension Check
if($eeSFLF) {
	if(!@$eeSFLF_ListFolder) { // If not already set up
		$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
		include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_PathSetup.php'); // Run Setup
	}
}

$eeSFL_Log[] = 'Uploading to...';
$eeSFL_Log[] = $eeSFL_Config['FileListDirName'];

// Check for an upload job, then run notification routine.
if(@$_POST['eeSFL_Upload']) { 
	
	$eeOutput .= eeSFL_ProcessUpload($eeSFL_Config['FileListURL'], $$eeSFL_Config['FileListDir'], $eeSFL_Config['Notify']);
	
	if(!$eeAdmin) {
		eeSFL_UploadCompleted(); // Action Hook: eeSFL_UploadCompleted
	}	
}




// File limit fallback
if(!$eeSFL_Config['UploadLimit']) { $eeSFL_Config['UploadLimit'] = $eeSFL->eeDefaultUploadLimit; }

// User Messaging	
if(@$eeSFL_Log['messages']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['messages'], 'updated'); // Add to the output
	$eeSFL_Log['messages'] = ''; // Clear
}	
if(@$eeSFL_Log['errors']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['errors'], 'error'); // Add to the output
	$eeSFL_Log['errors'] = ''; // Clear
}
	


if(@$eeSFL_Config['FileListDir']) {
	
	$eeOutput .= '
	
	<!-- Simple File List Uploader -->
			
		<form action="" method="POST" enctype="multipart/form-data" name="eeUploadForm" id="eeUploadForm">
		
		<input type="hidden" name="MAX_FILE_SIZE" value="' .(($eeSFL_Config['UploadMaxFileSize']*1024)*1024) . '" />
		<input type="hidden" name="eeSFL_Upload" value="TRUE" />
		<input type="hidden" name="eeFileCount" value="" id="eeFileCount" />
		<input type="hidden" name="eeFileList" value="" id="eeFileList" />';
		
		if($eeSFLF) {
			$eeOutput .= '<input type="hidden" name="eeSFLF_UploadFolder" value="' . urlencode($eeSFLF_ListFolder) . '" id="eeSFLF_UploadFolder" />';
		}
		if($eeAdmin) {
			$eeOutput .= '<a href="?page=ee-simple-file-list&tab=list_settings&subtab=uploader_settings" class="button eeRight">' . __('Upload Settings', 'ee-simple-file-list') . '</a>';
		}
		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-upload', 'ee-simple-file-list-upload-nonce', TRUE, FALSE);
	
		$eeOutput .= '<h2>' . __('Upload Files', 'ee-simple-file-list') . '</h2>';
		
		if($eeSFL_Config['GetUploaderInfo'] == 'YES' AND !$eeAdmin) { $eeOutput .= $eeSFL->eeSFL_UploadInfoForm(); }
		
		$eeSFL_FileFormats = str_replace(' ' , '', $eeSFL_Config['FileFormats']); // Strip spaces
	    
		$eeOutput .= '<input type="file" name="eeFileInput" id="eeFileInput" multiple />
		
		<br class="eeClearFix" />
		
		<script type="text/javascript">
		
			console.log("Simple File List - Multi-Uploader");
			
			var eeUploadFiles = document.querySelector("#eeFileInput");
			var eeFiles = "";
			var eeFileSet = new Array(); // Names
			var eeFileObjects = new Array(); // File objects
			var eeFileCount = 0; // How many to upload
			
			var eeFileLimit = ' . $eeSFL_Config['UploadLimit'] . '; // Maximum number of files allowed
			var eeUploaded = 0; // How many have uploaded
			var eeError = false; // Bad things have happened
			
			// Allowed file extentions
			var eeFormats = "' . $eeSFL_Config['FileFormats'] . '";
			var eeFormatsArray = eeFormats.split(","); // An array of the things.
			
			jQuery(document).ready(function() {
			
				jQuery( "#eeUploadingNow" ).hide(); // Hide the spinner
				
				// File Queue Information
				document.getElementById("eeFileInput").addEventListener("change", function(){
				    
				    console.log("File Added");
				    
				    if(this.files.length > eeFileLimit) {
					    
					    alert(this.files.length + " files selected. The maximum allowed is " + eeFileLimit);
					    
					    eeError = false;
					    eeFile = false;
					    jQuery("#eeFileInput").val("");
					    return false;
					       
					}
				    
				    for(var i = 0; i < this.files.length; i++){
				        
				        var eeFile =  this.files[i];
				        
				        console.group("File # " + i);
				        console.log("Name: " + eeFile.name);
				        
				        // Validation
				        
				        // Size
				        console.log("Size: " + eeFile.size);
				        
				        if(eeFile.size > ' . (($eeSFL_Config['UploadMaxFileSize']*1024)*1024) . ') {
					        eeError = eeFile.name + " is too large to upload.";
				        }
				        
				        // Type
				        var eeExtension = eeFile.name.split(".").pop();
				        eeExtension = eeExtension.toLowerCase();
				        
				        if(eeFormatsArray.indexOf(eeExtension) == -1) {
					        eeError = "This file type (" + eeExtension + ") is not allowed.";
				        }
				        
				        console.log("Extension: " + eeExtension);
				        console.log("Type: " + eeFile.type);
				        
				        // Modified date
				        // console.log("Date: " + eeFile.lastModified);
				        
				        console.groupEnd();
				        
				        if(!eeError) { // If no errors
				        	
							eeFileObjects.push(eeFile); // Add object
							eeFileSet.push(eeFile.name); // Add name   
							
				        } else {
					        
					        alert(eeError); // Alert the user.
					        
					        eeError = false;
					        eeFile = false;
					        jQuery("#eeFileInput").val("");
					        return false;
				        }
				        
				    }
				    
				    eeFileCount = eeFileObjects.length; // Reset based on set
				    var eeFileQstring = JSON.stringify(eeFileSet);
				            
		            jQuery("#eeFileList").val(eeFileQstring); // Set the hidden inputs
					jQuery("#eeFileCount").val(eeFileCount); // The number of files
		            
		            console.log("#eeFileList  Set: " + eeFileQstring);
					console.log("#eeFileCount Set: " + eeFileCount);
				        
				    console.log("Files: " + eeFileSet);
				    console.log("Count: " + eeFileCount);
				    
				}, false);
				
				
		
			}); // END Ready Function
			
			
			
			// The Upload Queue Processor
			function eeUploadProcessor(eeFileObjects) {
				
				eeFileCount = eeFileObjects.length;
				
				if(eeFileCount) {
					
					// Remove button and replace with spinner
				    jQuery("#eeUploadGo" ).fadeOut( function(){ jQuery( "#eeUploadingNow" ).fadeIn(); } );
					// jQuery( "#eeUploadingNow" ).fadeIn();
				
					console.log("Uploading " + eeFileCount + " files...");
					
					for (var i = 0; i < eeFileCount; i++) { // Loop through and upload the files
						
						console.log("Processing File: " + eeFileObjects[i].name);
									            
			            eeUploadFile(eeFileObjects[i]); // Upload the file using the function below...
					}
				}		
			}
			
			
			// File Upload AJAX Call
			function eeUploadFile(eeFile) { // Pass in file object
			    
			    var eeUrl = "' . plugin_dir_url( __FILE__ ) . '../ee-upload-engine.php' . '";
			    var eeXhr = new XMLHttpRequest();
			    var eeFd = new FormData();
			    
			    console.log("Calling Engine: " + eeUrl);
			    console.log("Uploading: " + eeFile.name);
			    
			    eeXhr.open("POST", eeUrl, true);
			    
			    eeXhr.onreadystatechange = function() {
			        
			        if (eeXhr.readyState == 4) { // && eeXhr.status == 200 <-- Windows returns 404?
		            
		            	eeUploaded ++;
			            
			            console.log("File Uploaded (" + eeUploaded + " of " + eeFileCount + ")");
			            
						// Every thing ok, file uploaded
			            console.log("RESPONSE: " + eeXhr.responseText); // handle response.
			            
			            // Submit the Form
			            if(eeUploaded == eeFileCount) {
				            
				            if(eeXhr.responseText == "SUCCESS") {
				            
				            	console.log("--->>> SUBMITTING FORM ...");
				            	
				            	document.forms.eeUploadForm.submit(); // SUCCESS - Process the Form <<<----- FORM SUBMIT
								
					        } else {
						    	console.log("XHR Status: " + eeXhr.status);
						    	console.log("XHR State: " + eeXhr.readyState);
						    	
						    	var n = eeXhr.responseText.search("<"); // Error condition
						    	if(n === 0) {
							    	alert("Upload Error: " + eeFile.name);
							    	jQuery( "#eeUploadingNow" ).fadeOut();
							    }
							    return false;
					        }
				        }
			        
			        } else {
				    	console.log("XHR Status: " + eeXhr.status);
				    	console.log("XHR State: " + eeXhr.readyState);
				    	return false;
			        }
			    };
			    
			    // Pass the file name to the Upload Engine
			    eeFd.append("file", eeFile);
			    
			    // Security
			    ';
			    
			    $eeSFL_Timestamp = time();
			    $eeSFL_TimestampMD5 = md5('unique_salt' . $eeSFL_Timestamp);
			    
			    $eeOutput .= 'eeFd.append("timestamp", "' . $eeSFL_Timestamp . '"); 
			    
			    eeFd.append("token", "' . $eeSFL_TimestampMD5 . '");  
			    
			    eeFd.append("eeSFL_ID", "' . $eeSFL->eeListID . '");
			    
			    eeFd.append("eeSFL_FileListDirName", "' . urlencode($eeSFL_Config['FileListDirName']) . '");
			        
			    // Send the AJAX request...
			    eeXhr.send(eeFd);
			}

			
			console.log("Waiting for files...");
			
			// Populate the action attribute in the form
			var eeCurrentURL = document.location.href;
			jQuery("#eeUploadForm").attr("action", eeCurrentURL);
			
		</script>
		
		<span id="eeUploadingNow"><img class="eeSFL_UploadingImg" src="' . $eeSFL_Env['wpPluginsURL'] . '/images/sending.gif" width="32" height="32" alt="Spinner Icon" />' . __('Uploading', 'ee-simple-file-list') . '</span>
		
		<button type="button" name="eeUploadGo" id="eeUploadGo" onclick="eeUploadProcessor(eeFileObjects);">' . __('Upload', 'ee-simple-file-list') . '</button>
		
		<p class="sfl_instuctions">' . __('File Limit', 'ee-simple-file-list') . ': ' . $eeSFL_Config['UploadLimit'] . ' ' . __('files', 'ee-simple-file-list') . '<br />
		
		' . __('Size Limit', 'ee-simple-file-list') . ': ' . $eeSFL_Config['UploadMaxFileSize'] . ' MB
		
		' . __('per file', 'ee-simple-file-list') . '.<br />' . __('To select multiple files, hold down the Control key while choosing files', 'ee-simple-file-list') . ' (' . __('Command key on Macs', 'ee-simple-file-list') . ')</p>
		
		<br class="eeClearFix" />
	
	</form>';
	
	
} else {
	$eeOutput .= __('No upload directory configured.', 'ee-simple-file-list');
	$eeSFL_Log['errors'] = 'No upload directory configured.';
}