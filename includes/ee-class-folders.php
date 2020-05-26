<?php // Simple File List | Folder Extension | Script: eeSFLF_class.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFLF_Nonce, 'eeSFLF_Include' )) exit('ERROR 98'); // Exit if nonce fails
	
class eeSFLF_class {
	
	// Property declarations
	public $eeSFLF_ListOfFolders = array(); // List of all folders
	public $eeSFLF_FileScanArray = array(); // Temporary holder
	
	
	// Get File AND Folders
	public function eeSFLF_IndexCompleteFileListDirectory($eeDir) {
		
		global $eeSFL_Config;
		
		if(is_dir(ABSPATH . $eeDir) === FALSE) { return FALSE; }
		
		$eeArray = new DirectoryIterator(ABSPATH . $eeDir);
		   
		foreach ($eeArray as $eeItem) {
		    
		    if ( !$eeItem->isDot() ) { // Don't go up
		        
		        if(strpos($eeItem, '.') !== 0 AND basename($eeItem) != 'index.html') { // No hidden items
		        
			        // Catch and correct spaces in items found here
			        if( strpos($eeItem, ' ') AND strpos($eeItem, ' ') !== 0 ) {
				        
				        $eeNewItem = str_replace(' ', '-', $eeItem);
				        
				        if(rename(ABSPATH . $eeDir . '/' . $eeItem, ABSPATH . $eeDir . '/' . $eeNewItem)) {
					        $eeItem = $eeNewItem;
				        }
				    }
			        
			        if (is_dir(ABSPATH . $eeItem)) {
				        
			            $this->eeSFLF_FileScanArray[] =  $eeDir . '/';
			            
			            $this->eeSFLF_IndexCompleteFileListDirectory($eeDir . '/' . $eeItem);
			            
			        } else {
			            
			            $eeString = $eeDir . '/' . $eeItem; // ->getFilename()
			            
			            if(strpos($eeItem, '.')) {
				            $this->eeSFLF_FileScanArray[] =  $eeString;
			            } else {
				            $this->eeSFLF_IndexCompleteFileListDirectory($eeDir . '/' . $eeItem);
				            $this->eeSFLF_FileScanArray[] =  $eeString . '/';
			            }
			        }
		        }
		    }
		}
		
		// Strip the FileListDir from the path
		$eeArray = array();
		
		foreach( $this->eeSFLF_FileScanArray as $eeValue) {
			
			
			$eeArray[] = str_replace($eeSFL_Config['FileListDir'] . '/', '', $eeValue); 
		}
		
		$this->eeSFLF_FileScanArray = $eeArray;	
	}
	



	// Get just files and folder from here and down
	public function eeSFLF_GetItemsBelow($eeFileArray, $eeListFolder) {
		
		global $eeSFL_Config;
		$eeArray = array();
		
		foreach( $eeFileArray as $eeKey => $eeFile) {
			
			if( strpos($eeFile['FilePath'], $eeListFolder) === 0 AND $eeFile['FileExt'] != 'folder' ) {
				
				$eeArray[] = $eeFile;
			}
		}
		
		return $eeArray;
	}
    
    
    
    
    // Sort Folders First
	public function eeSFLF_SortFoldersFirst($eeFiles, $eeSortBy, $eeSortOrder, $eeListFolder = FALSE) {
			
		global $eeSFL_Log, $eeSFL;
		
		$eeJustFolders = array();
		$eeJustFiles = array();
		
		if(is_array($eeFiles) AND @count($eeFiles) > 1) {
			
			foreach( $eeFiles as $eeKey => $eeFileArray) {
			
				if($eeFileArray['FileExt'] == 'folder') {
					
					$eeJustFolders[ $eeFileArray['FilePath'] ] = $eeFileArray;
				
				} else {
					
					$eeJustFiles[ $eeFileArray['FilePath'] ] = $eeFileArray;
				}
			}
			
			$eeFiles = array_merge($eeJustFolders, $eeJustFiles);
		}
		
		return $eeFiles;
	}
	
	
	
	
	
	// File Move Display
	public function eeSFLF_MoveToFolderDisplay($eeFileArray, $eeSFLF_ListFolder = FALSE) {
		
		global $eeSFL_Log;
		$eeArray = array();
		$eeFolders = $this->eeSFLF_ListOfFolders; // Get our list of folders
		
		if(count($eeFolders)) {
			
			$eeListFolderParent = '';
			$eeOptions = array();
			
			$eeOutput = '<p class="eeSFLF_moveFileDisplay"><select name="eeSFLF_moveFile" class="eeSFLF_moveFile">
							<option value="">Move to...</option>';
					
			if($eeSFLF_ListFolder) {
				$eeOutput .= '<option value="/">Home Folder</option>' . "\n";
			}
							
			if($eeFileArray['FileExt'] == 'folder') {
				
				if($eeSFLF_ListFolder) {
				
					// Get the immediate parent of this location
					$eeArray = explode('/', $eeSFLF_ListFolder);
					array_pop($eeArray); // Remove This folder
					$eeListFolderParent = implode('/', $eeArray) . '/';
					
				} else {
					
					$eeSFLF_ListFolder = $eeFileArray['FilePath'];
				}
			
				$eeArray = array();
				
				foreach( $eeFolders as $eeKey => $eeFolder ) {
			
					if( $eeFolder == $eeSFLF_ListFolder . '/') {
						
						// THIS FOLDER --- Can't Move Here
						// $eeSFL_Log['select'][] = 'THIS: ' . $eeFolder . ' == ' . $eeSFLF_ListFolder . '/';
					
					} elseif( $eeFolder == $eeListFolderParent ) {
						
						// PARENT FOLDER --- Can't Move Here
						// $eeSFL_Log['select'][] = 'PARENT: ' . $eeFolder . ' == ' . $eeListFolderParent;
					
					} elseif( strpos($eeFolder, $eeFileArray['FilePath']) === 0 ) {
						
						// SUB-FOLDER --- Can't Move Here
						// $eeSFL_Log['select'][] = 'SUB: ' . $eeSFLF_ListFolder . ' within ' . $eeFolder;
						
					} else { // We can move here...
						
						// $eeSFL_Log['select'][] = 'GOOD: ' . $eeFolder . ' == ' . $eeSFLF_ListFolder . '/';
						
						$eeArray[] = '<option value="' . $eeFolder . '">' . substr($eeFolder, 0, -1) . '</option>';
					}
				}
			
			} else { // File
				
				foreach( $eeFolders as $eeKey => $eeFolder ) {
				
					if($eeSFLF_ListFolder . '/' != $eeFolder) { // Can't move to self
							
						$eeArray[] = '<option value="' . $eeFolder . '">' . substr($eeFolder, 0, -1) . '</option>';
					}
				}
			}
			
			
			foreach( $eeArray as $eeKey => $eeValue) {
				
				$eeOutput .= $eeValue;
			}
			
			$eeOutput .= '</select><button onclick="">Move</button></p>'; // We add the onclick function dynamically
			
			return $eeOutput;
		}
		
		return FALSE;
	}
	
	
	
		
	
	// Returns the folder name as an argument in the visible link.
	// This will let the page know to open that folder.
	public function eeSFLF_GetFolderURL($eeSFL_File, $eeSFLF_ShortcodeFolder) {
		
		global $eeSFL_ID, $eeSFL_Admin;
		
		// The current address
		$eeURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		if(@$_GET) {
			
			$eeURL = remove_query_arg('eePage', $eeURL); // Pagination used
			
			// Strip these as needed...
			if(@$_GET['eeFolder']) {
				$eeURL = remove_query_arg('eeFolder', $eeURL);
			}
			$eeURL = eeSFL_AppendProperUrlOp($eeURL);
			
			$eeURL .= 'eeFolder=' . substr($eeSFL_File, 0, -1);
			
		} else {

			$eeURL .= '?eeFolder=' . substr($eeSFL_File, 0, -1);
			
		}
		
		// Detect front-side folder click
		if($eeSFLF_ShortcodeFolder AND !strpos($eeURL, 'eeFront')) {
			$eeURL .= '&eeFront=1';
		}
		
		return $eeURL; // . '&eeListID=' . $eeSFL_ID;
	}
	
	

	
	
	public function eeSFLF_FunctionBar($eeSFLF_ListFolder, $eeSFLF_ShortcodeFolder) {
		
		global $eeSFL_Config, $eeSFL_Log, $eeSFL_ListRun;
		
		$eeAdmin = is_admin(); // Where is we?
		
		$eeShowCreate = FALSE;
		$eeForm = FALSE;
		$eeTrail = FALSE;
		
		// Condition for display
		if($eeAdmin) { $eeShowCreate = TRUE; } 
		if($eeSFL_Config['AllowFrontManage'] == 'YES' AND $eeSFL_ListRun == 1) { $eeShowCreate = TRUE; }
		if(@$_POST['eeSFLS_Searching']) { $eeShowCreate = FALSE; }
		if(@$_POST['eeSFL_Upload']) { $eeShowCreate = FALSE; }
		
		// NOTE - Folder creation does work for multiple list runs per page. But Moving does not, so creation is disabled for now.
		
		
		// Create New Folder Form
		if($eeShowCreate) {
			
			// Fix for when this is off but manage is on  TO DO --> Make this less sucky
			if(!$eeAdmin AND $eeSFL_Config['ShowFileActions'] != 'YES') {
				return FALSE;
			}
			
			// Create New Folder
			$eeFormURI = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_STRING);
				
			$eeForm = '
			
			<form class="eeSFLF_CreateFolder" action="' . $eeFormURI . '" method="post">';
			
			$eeForm .= wp_nonce_field( 'ee-simple-file-list-create-folder', 'ee-simple-file-list-create-folder-nonce', TRUE, FALSE);
			
			$eeForm .= '
			
				<input class="button" type="submit" value="' . __('Create Folder', 'ee-simple-file-list') . '" />
					<input class="" type="text" name="eeSFLF_NewFolderName" value="" size="64" />
			</form>';
		
		}
		
		
		
		// Breadcrumb Display
		if(($eeAdmin OR $eeSFL_Config['ShowBreadCrumb'] == 'YES') AND !@$_POST['eeSFL_Upload']) {
		
			// exit('$eeSFLF_ListFolder = ' . $eeSFLF_ListFolder);
			
			// Always within a folder on the back-side, but only within if within sub-folder of shortcode folder
			if( @$_GET['eeFolder'] ) {  // OR strpos($eeSFLF_ListFolder, '/')
				
				// exit('BANG');
				
				$eeSFLF_OmitFolderArray = FALSE;
				
				if($eeSFLF_ShortcodeFolder) {
					$eeSFLF_OmitFolderArray = explode('/', $eeSFLF_ShortcodeFolder);
				}
				
				if(strpos($eeSFLF_ListFolder, '/')) {
					$eeSFLF_FolderArray = explode('/', $eeSFLF_ListFolder);
				} else {
					$eeSFLF_FolderArray = array($eeSFLF_ListFolder);
				}
				
				
				
				// TO DO - $eeURL
				$eeRequestURI =  filter_var( $_SERVER['REQUEST_URI'], FILTER_SANITIZE_STRING );
				
				// Strip these arguments
				if(@$_GET['eeFront'] == 1) {
					$eeRequestURI = remove_query_arg('eeFront', $eeRequestURI);
					$eeRequestURI = urldecode($eeRequestURI);
				} 
				$eeSFLF_BaseURL = str_replace('eeFolder=' . $eeSFLF_ListFolder, '', $eeRequestURI);
				
				$eeTrail .= '<div class="eeSFL_FolderBreadcrumb"><strong>';
				
				if(!$eeSFLF_ShortcodeFolder) {
					$eeTrail .= '<a href="' . $eeSFLF_BaseURL . '">' . __('Home', 'ee-simple-file-list') . '</a> / '; // Never show the Home folder if a shortcode folder was used
				}
				
				$count = count($eeSFLF_FolderArray);
				
				if($count >= 1) {
					
					$path = '';
					$i = 0;
					
					foreach( $eeSFLF_FolderArray as $key => $folder) {
						
						if($i) { $path .= '/'; }
						$path .= $folder;
						
						$i++;
						
						if($eeSFLF_ShortcodeFolder) { // Shortcode defined folder. Omit folders up to here
							
							// wp_die('Bang');
							
							if($i != $count) {
							
								if($i == 1) { $folder = __('Home', 'ee-simple-file-list'); } // We are within a sub-folder, change base folder name to "Home"
								
								$eeTrail .= '<a href="' . $eeSFLF_BaseURL;
								
								if($i > 1) { $eeTrail .= 'eeFolder=' . urlencode($path); }
								
								if(@$_GET['eeFront'] == 1 AND $folder != 'Home') { $eeTrail .= '&eeFront=1'; }
								
								$eeTrail .= '">' . $folder . '</a> / ';
							
							} elseif($count != count($eeSFLF_OmitFolderArray)) { // This is the current sub-folder
								
								$eeTrail .= $folder . ' / ';
							
							} elseif($count == count($eeSFLF_OmitFolderArray)) { // We are in the base folder, change name to "Home"
								
								$eeTrail .= __('Home', 'ee-simple-file-list') . ' / ';
							}
						
						} else { // Back-side or front-side showing home folder
							
							if($i != $count) {
								
								$eeTrail .= '<a href="' . $eeSFLF_BaseURL . 'eeFolder=' . urlencode($path) . '">' . $folder . '</a> / ';
							
							} else {
								
								$eeTrail .= $folder . ' / ';
							}
						}
					}
				}
				
				$eeTrail .= '</strong></div>';
			}
		}
		
		if($eeForm OR $eeTrail) {
		
			$eeOutput = '<div class="eeSFLF_FunctionBar">';
			$eeOutput .= $eeForm;
			$eeOutput .= $eeTrail;
			$eeOutput .= '<br />';
			$eeOutput .= '</div>';
			return $eeOutput;
		}
		
		return FALSE;	
	}
	
	
	
	
	public function eeSFLF_CreateFolder($eeSFLF_NewFolderName, $eeSFLF_ListFolder = FALSE) {
		
		global $eeSFL, $eeSFL_ID, $eeSFL_Log, $eeSFL_Config, $eeSFL_Env;
		
		// Sanitize
		$eeSFLF_NewFolderName = strip_tags($eeSFLF_NewFolderName); 
	    $eeSFLF_NewFolderName = preg_replace('/[\r\n\t ]+/', ' ', $eeSFLF_NewFolderName);
	    $eeSFLF_NewFolderName = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $eeSFLF_NewFolderName);
	    $eeSFLF_NewFolderName = html_entity_decode( $eeSFLF_NewFolderName, ENT_QUOTES, "utf-8" );
	    $eeSFLF_NewFolderName = htmlentities($eeSFLF_NewFolderName, ENT_QUOTES, "utf-8");
	    $eeSFLF_NewFolderName = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $eeSFLF_NewFolderName);
	    $eeSFLF_NewFolderName = str_replace(' ', '-', $eeSFLF_NewFolderName);
	    $eeSFLF_NewFolderName = rawurlencode($eeSFLF_NewFolderName);
	    $eeSFLF_NewFolderName = str_replace('%', '-', $eeSFLF_NewFolderName);
	    $eeSFLF_NewFolderName = str_replace('.', '-', $eeSFLF_NewFolderName);
		
		eeSFL_DetectUpwardTraversal($eeSFL_Config['FileListDir'] . $eeSFLF_NewFolderName);
		
		if(strlen($eeSFLF_NewFolderName) <= 255) {
			
			if(!is_dir(ABSPATH . $eeSFL_Config['FileListDir'] . $eeSFLF_ListFolder . '/' . $eeSFLF_NewFolderName)) {
				
				if(!mkdir(ABSPATH . $eeSFL_Config['FileListDir'] . $eeSFLF_ListFolder . '/' . $eeSFLF_NewFolderName)) {
					$eeSFL_Log['errors'] = 'Could Not Create the New Folder';
				} else {
					
					$eeIndexFile = ABSPATH . $eeSFL_Config['FileListDir'] . $eeSFLF_ListFolder . '/' . $eeSFLF_NewFolderName . '/index.html'; // Disallow direct file indexing.
		
					$eeHandle = @fopen($eeIndexFile, "a+");
				
					fclose($eeHandle);
					
					$eeFiles = $eeSFL->eeSFL_UpdateFileListArray($eeSFL_ID);
					
					// Is creator person logged-in?
					if( is_numeric(@$eeSFL_Env['wpUserID']) ) {
						$eeSFL->eeSFL_UpdateFileDetail($eeSFL_ID, $eeSFLF_NewFolderName . '/', 'FileOwner', $eeSFL_Env['wpUserID']);
					}
										
					return $eeFiles;
				}
			} else {
				$eeSFL_Log['errors'] = $eeSFLF_ListFolder . '/' . $eeSFLF_NewFolderName . ' Already Exists!';
				
				return FALSE;
			}
		} else {
			$eeSFL_Log['errors'] = 'Bad New Folder Name';
			
			return FALSE;
		}
	}
	
	
	
	// Delete Folder and Contents
	public function eeSFLF_DeleteFolder($eeSFL_Dir) { // Requires full path
	
		global $eeSFL_Log;
		
		if(is_dir($eeSFL_Dir) === TRUE) {
	        
	        // Get an array of the files in the folder
	        $eeFiles = array_diff( scandir($eeSFL_Dir), array('.', '..') );
	        
	        $eeSFL_Log[] = $eeFiles;
	
	        // Loop through and run this function on the contents
	        foreach($eeFiles as $eeFile) {
	            
	            $this->eeSFLF_DeleteFolder(realpath($eeSFL_Dir) . '/' . $eeFile);
	        }
	
	        // Delete the folder
	        if(rmdir($eeSFL_Dir)) {
		        return TRUE;
	        } else {
		        return FALSE;
	        }
	    
	    // Delete files in the folder
	    } elseif (is_file($eeSFL_Dir) === TRUE) {
	        
	        unlink($eeSFL_Dir);
	    
	    } else {
		    
		    $eeSFL_Log[] = 'Not a File or Folder: ' . $eeSFL_Dir;
	
			return FALSE;
	    }
	}
	
	
	
	public function eeSFLF_GetFolderSize($eeSFL_Dir) {
		
		$dirSize = 0;
		$count = 0;
		$dirArray = scandir(ABSPATH . $eeSFL_Dir);
		
		foreach($dirArray as $key => $filename) {
		    
		    if($filename != ".." && $filename != ".") {
		       
		       	if(is_dir(ABSPATH . $eeSFL_Dir . "/" . $filename)) {
		          
		          	$new_foldersize = $this->eeSFLF_GetFolderSize($eeSFL_Dir . "/" . $filename);
				  	if(is_numeric($new_foldersize)) { $dirSize = $dirSize + $new_foldersize; }
		        
		        } elseif(is_file(ABSPATH . $eeSFL_Dir . "/" . $filename)) {
		          
		          	$dirSize = $dirSize + filesize(ABSPATH . $eeSFL_Dir . "/" . $filename);
				  	$count++;
		        }
		   }
		}
		
		return $dirSize;
	}

	
}	
	
?>