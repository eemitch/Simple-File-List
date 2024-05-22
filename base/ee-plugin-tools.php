<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

$eeSFL->eeLog['notice'][] = $eeSFL->eeSFL_NOW() . 'Loading Admin Tools ...';

// Reset File Array
if(isset($_POST['reset-files'])) {
		
	if(check_admin_referer('eeSFL_Nonce', eeSFL_Nonce) == 1) {
		
		// Delete the File List Array
		if($eeSFL->eeListID) {
			delete_option('eeSFL_FileList_' . $eeSFL->eeListID);
			delete_transient('eeSFL_FileList_' . $eeSFL->eeListID);
			
			$eeSFL->eeSFL_UpdateFileListArray($eeSFL->eeListID);
			
			$eeSFL->eeLog['messages'][] = __('The File List Array Has Been Reset', 'ee-simple-file-list') . ' (' . $eeSFL->eeListID . ')';
		}
	}
}

// Reset File List Settings
if(isset($_POST['reset-settings'])) {	
	
	if(check_admin_referer('eeSFL_Nonce', eeSFL_Nonce)) {
		
		// Save the Name and Dir
		$eeSFL->eeListSettings = get_option('eeSFL_Settings_' . $eeSFL->eeListID);
		$eeListTitle = $eeSFL->eeListSettings['ListTitle'];
		$eeFileListDir = $eeSFL->eeListSettings['FileListDir'];
			
		// Reset List Settings
		if($eeSFL->eeListSettings) {
			
			// Rest this list
			$eeSFL->eeListSettings = $eeSFL->eeDefaultListSettings;
			
			// Set these
			$eeSFL->eeListSettings['NotifyTo'] = get_option('admin_email'); 
			$eeSFL->eeListSettings['NotifyMessage'] = $eeSFL_Messaging->eeNotifyMessageDefault; 
			$eeSFL->eeListSettings['ListTitle'] = $eeListTitle;
			$eeSFL->eeListSettings['FileListDir'] = $eeFileListDir;
			
			if($eeSFLS) {
				$eeSFL->eeListSettings = array_merge($eeSFL->eeListSettings, $eeSFLS->eeSFLS_SettingsDefault);
			}
			
			if($eeSFLA) {
				$eeSFL->eeListSettings = array_merge($eeSFL->eeListSettings, $eeSFLA->eeDefaultAccessSettings);
			}
			
			update_option('eeSFL_Settings_' . $eeSFL->eeListID, $eeSFL->eeListSettings);
			delete_transient('eeSFL_FileList_' . $eeSFL->eeListID);
			
			$eeSFL->eeLog['messages'][] = __('The File List Settings Have Been Reset', 'ee-simple-file-list');
		}
	}
 }
 
 
 // Delete Orphaned Thumbnails
 if(isset($_POST['orphaned-thumbs'])) {
	
	if(check_admin_referer('eeSFL_Nonce', eeSFL_Nonce) == 1) {
		
		// Delete Orphaned Thumbs
		if($eeSFL->eeListID) {
			
			// Get the file list dir contents
			$eeSFL_Environment->eeSFL_ScanAndSanitize();
			
			$eeExtensionsToCheck = array_merge($eeSFL_Thumbs->eeDynamicImageThumbFormats, $eeSFL_Thumbs->eeDynamicVideoThumbFormats);
			$eeExtensionsToCheck[] = 'pdf';
			sort($eeExtensionsToCheck);
			
			$eeOrphanesArray = array();
			
			if(!empty($eeSFL->eeFileScanArray)) {
				
				// Home Dir
				if ($eeHandle = opendir(ABSPATH . $eeSFL->eeListSettings['FileListDir'] . '.thumbnails')) {
				
				    while (false !== ($eeFile = readdir($eeHandle))) {
				        
				        if(strlen($eeFile) > 2) {
					        
					        // Check for source file
					        $eeFileToCheck = str_replace('thumb_', '', $eeFile);
					        
					        $eeFound = FALSE;
					        
					        foreach( $eeExtensionsToCheck as $eeExt) {
						        	
					        	$eeFileToTry = str_replace('.jpg', '.' . $eeExt, $eeFileToCheck);
					        	
					        	if(is_file(ABSPATH . $eeSFL->eeListSettings['FileListDir'] . $eeFileToTry)) {
						        	
						        	$eeFound = TRUE;
					        	}
					        	
					        }
					        
					        if($eeFound === FALSE) {
						        $eeOrphanesArray[] = $eeSFL->eeListSettings['FileListDir'] . '.thumbnails/' . $eeFile;
					        }
						}
				    }
				
				    closedir($eeHandle);
				}
				
				// Folders
				foreach( $eeSFL->eeFileScanArray as $eeKey => $eeValue) {
					
					if(substr($eeValue, -1) == '/') { // Folder
						
						if ($eeHandle = @opendir(ABSPATH . $eeSFL->eeListSettings['FileListDir'] . $eeValue . '.thumbnails')) {
						
						    while (false !== ($eeFile = readdir($eeHandle))) {
				        
						        if(strlen($eeFile) > 2) {
							        
							        // Check for source file
							        $eeFileToCheck = str_replace('thumb_', '', $eeFile);
							        
							        $eeFound = FALSE;
							        
							        foreach( $eeExtensionsToCheck as $eeExt) {
								        	
							        	$eeFileToTry = str_replace('.jpg', '.' . $eeExt, $eeFileToCheck);
							        	
							        	if(is_file(ABSPATH . $eeSFL->eeListSettings['FileListDir'] . $eeValue  . $eeFileToTry)) {
								        	
								        	$eeFound = TRUE;
								        	
								        	break;
							        	}
							        	
							        }
							        
							        if($eeFound === FALSE) {
								        $eeOrphanesArray[] = $eeSFL->eeListSettings['FileListDir'] . $eeValue  . '.thumbnails/' . $eeFile;
							        }
								}
						    }
						
						    closedir($eeHandle);
						}
					}
				}
				
				foreach( $eeOrphanesArray as $eeKey => $eeValue) {
					
					unlink(ABSPATH . $eeValue);
				}
			
				$eeSFL->eeLog['messages'][] = count($eeOrphanesArray) . ' ' . __('Orphaned thumbnail files have been deleted', 'ee-simple-file-list');
			}
		}
	}
}




// User Messaging
$eeOutput .= $eeSFL_Messaging->eeSFL_ResultsNotification();
	
$eeOutput .= '

<div class="eeColInline eeSettingsTile">
				
	<div class="eeColHalfLeft">
	
		<h1>' .  __('List Tools', 'ee-simple-file-list');
		
		if($eeSFLA) { $eeOutput .= ' &rarr; ' . $eeSFL->eeListSettings['ListTitle']; }
		
		$eeOutput .= '</h1>

	</div>
	
	<div class="eeColHalfRight">
	
		<a class="" href="https://simplefilelist.com/the-tools-tab/" target="_blank">' . __('Instructions', 'ee-simple-file-list') . '</a>
	
	</div>

</div>

<div class="eeColumns">	

<div class="eeColLeft">


<div class="eeSettingsTile">
<form class="eeSFL_ToolBox" action="' . $eeSFL->eeSFL_GetThisURL() . '" method="post">

<h2>' . __('Reset File List Array', 'ee-simple-file-list') . '</h2>

<fieldset>

<legend>' . __('Reset the file list array back to default if it becomes corrupted.', 'ee-simple-file-list') . '</legend>

<div class="eeNote">' . __('Use caution. Descriptions, nice names and other information will be lost.', 'ee-simple-file-list') . '</div>

<input type="hidden" name="reset-files" value="1" />
<input type="hidden" name="eeListID" value="' . $eeSFL->eeListID . '" />';

$eeOutput .= wp_nonce_field('eeSFL_Nonce', eeSFL_Nonce, TRUE, FALSE);	

$eeOutput .= '<input class="button" type="submit" name="eeSubmit" value="' . __('Reset', 'ee-simple-file-list') . '" />

</fieldset>
	
</form>
</div>
</div>



<div class="eeColRight">

<div class="eeSettingsTile">
<form class="eeSFL_ToolBox" action="' . $eeSFL->eeSFL_GetThisURL() . '" method="post">
	
<h2>' . __('Reset List Settings', 'ee-simple-file-list') . '</h2>
	
<fieldset>
	
<legend>' . __('Reset the file list settings back to default.', 'ee-simple-file-list') . '</legend>
		
<div class="eeNote">' . __('Reset the list settings back to the default. No file information will be lost.', 'ee-simple-file-list') . '</div>
	
<input type="hidden" name="reset-settings" value="1" />
<input type="hidden" name="eeListID" value="' . $eeSFL->eeListID . '" />';
		
$eeOutput .= wp_nonce_field('eeSFL_Nonce', eeSFL_Nonce, TRUE, FALSE);	
		
$eeOutput .= '<input class="button" type="submit" name="eeSubmit" value="' . __('Reset', 'ee-simple-file-list') . '" />
	
</fieldset>
	
</form>
</div>



<div class="eeSettingsTile">
<form class="eeSFL_ToolBox" action="' . $eeSFL->eeSFL_GetThisURL() . '" method="post">
	
<h2>' . __('Orphaned Thumbnails', 'ee-simple-file-list') . '</h2>
	
<fieldset>

<legend>' . __('Delete thumbnails which have no associated file.', 'ee-simple-file-list') . '</legend>

<div class="eeNote">' . __('Removes only dynamically created thumbnail files for images, videos and PDFs.', 'ee-simple-file-list') . '</div>
	
<input type="hidden" name="orphaned-thumbs" value="1" />
<input type="hidden" name="eeListID" value="' . $eeSFL->eeListID . '" />';
		
$eeOutput .= wp_nonce_field('eeSFL_Nonce', eeSFL_Nonce, TRUE, FALSE);	
		
$eeOutput .= '<input class="button" type="submit" name="eeSubmit" value="' . __('Delete', 'ee-simple-file-list') . '" />
		
</fieldset>
	
</form>
</div>



</div>

</div>';

			
?>