<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

class eeSFL_Thumbnails {
	
	protected $eeSFL;
	public function __construct(eeSFL_MainClass $eeSFL) { $this->eeSFL = $eeSFL; }
	// Usage: $this->eeSFL->eeListID

	public $eeDynamicImageThumbFormats = array('gif', 'jpg', 'jpeg', 'png', 'tif', 'tiff');
	public $eeDynamicVideoThumbFormats = array('avi', 'flv', 'm4v', 'mov', 'mp4', 'webm', 'wmv');
	public $eeDefaultThumbFormats = array('3gp', 'ai', 'aif', 'aiff', 'apk', 'avi', 'bmp', 'cr2', 'dmg', 'doc', 'docx', 
		'eps', 'flv', 'gz', 'indd', 'iso', 'jpeg', 'jpg', 'm4v', 'mov', 'mp3', 'mp4', 'mpeg', 'mpg', 'pdf', 'png', 
		'pps', 'ppsx', 'ppt', 'pptx', 'psd', 'tar', 'tgz', 'tif', 'tiff', 'txt', 'wav', 'wma', 'wmv', 'xls', 'xlsx', 'zip', 'folder');
	public $eeOpenableFileFormats = array('aif', 'aiff', 'avi', 'bmp', 'flv', 'jpeg', 'jpg', 'gif', 'm4v', 'mov', 'mp3', 'mp4', 'mpeg', 'mpg', 'pdf', 'png', 
		'txt', 'wav', 'wma', 'wmv', 'folder', 'htm', 'html');
	public $eeFolderIcon = '&#128193;'; // Used when thumbnails are off.
	public $eeDefaultUploadLimit = 10; // Ten Files
	public $eeFileThumbSize = 256; // Pixels Square
 	
	 
	 
	 
	
	// Move, Rename or Delete a thumbnail - Expects path relative to FileListDir
	public function eeSFL_UpdateThumbnail_NEW($eeFileFrom, $eeFileTo) {
		$eePathPartsFrom = pathinfo($eeFileFrom);
		
		// Check if the file is of a type that we generate thumbnails for
		if (isset($eePathPartsFrom['extension']) && $this->isThumbnailSupported($eePathPartsFrom['extension'])) {
			// Convert the original file path to its thumbnail path
			$eeThumbFrom = $this->convertToThumbnailPath($eeFileFrom, $eePathPartsFrom);
			
			if (is_file($eeThumbFrom)) {
				if (!$eeFileTo) { // If no destination is provided, delete the thumbnail
					if (unlink($eeThumbFrom)) {
						$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Deleted Thumbnail For: ' . basename($eeFileFrom);
					}
				} else { // Move/Rename the thumbnail
					$eeThumbTo = $this->convertToThumbnailPath($eeFileTo, pathinfo($eeFileTo), true);
					if (rename($eeThumbFrom, $eeThumbTo)) {
						$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Thumbnail Updated For: ' . basename($eeFileFrom);
					}
				}
			}
		}
	}
	
	// Helper function to check if we support thumbnail generation for the file type
	private function isThumbnailSupported($extension) {
		return $extension == 'pdf' || in_array($extension, $this->eeDynamicImageThumbFormats) || in_array($extension, $this->eeDynamicVideoThumbFormats);
	}
	
	// Helper function to convert a file path to its corresponding thumbnail path
	private function convertToThumbnailPath($filePath, $pathParts, $forDestination = false) {
		$thumbnailDir = ABSPATH . $this->eeSFL->eeListSettings['FileListDir'] . ($pathParts['dirname'] != '.' ? $pathParts['dirname'] : '') . '/.thumbnails/';
		if ($forDestination && !is_dir($thumbnailDir)) {
			mkdir($thumbnailDir, 0755, true); // Ensure the directory exists for the destination
		}
		return $thumbnailDir . 'thumb_' . basename($filePath, '.' . $pathParts['extension']) . '.jpg';
	}

	
	
	
	
	
	public function eeSFL_UpdateThumbnail($eeFileFrom, $eeFileTo) {
		
		$eePathPartsFrom = pathinfo($eeFileFrom);
		
		if(isset($eePathPartsFrom['extension'])) { // Files only
			
			if($eePathPartsFrom['extension'] == 'pdf' 
				OR in_array($eePathPartsFrom['extension'], $this->eeDynamicImageThumbFormats) 
					OR in_array($eePathPartsFrom['extension'], $this->eeDynamicVideoThumbFormats) ) {
				
				// All thumbs are JPGs
				if($eePathPartsFrom['extension'] != 'jpg') { 
					$eeFileFrom = str_replace('.' . $eePathPartsFrom['extension'], '.jpg', $eeFileFrom);
					$eeFileTo = str_replace('.' . $eePathPartsFrom['extension'], '.jpg', $eeFileTo);
				}
				
				$eeThumbFrom = ABSPATH . $this->eeSFL->eeListSettings['FileListDir'];
				
				if($eePathPartsFrom['dirname'] != '.') { $eeThumbFrom .= $eePathPartsFrom['dirname']; }
				
				$eeThumbFrom .= '/.thumbnails/thumb_' . basename($eeFileFrom);
				
				if( is_file($eeThumbFrom) ) {
					
					if(!$eeFileTo) { // Delete the thumb
						
						if(unlink($eeThumbFrom)) {
							
							$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Deleted Thumbnail For: ' . basename($eeFileFrom);
							
							return;
						}
					
					} else { // Move / Rename
						
						$eePathPartsTo = pathinfo($eeFileTo);
						
						$eeThumbTo = ABSPATH . $this->eeSFL->eeListSettings['FileListDir'] . $eePathPartsTo['dirname'] . '/.thumbnails';
						
						if(!is_dir($eeThumbTo)) { mkdir($eeThumbTo); }
						
						$eeThumbTo .= '/thumb_' . basename($eeFileTo);
						
						if(rename($eeThumbFrom, $eeThumbTo)) { // Do nothing on failure
						
							$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Thumbnail Updated For: ' . basename($eeFileFrom);
							
							return;
						}
					}
				}
			}
		}
	}
	
	
	
	// Check Thumbnail and Create if Needed
	public function eeSFL_CheckThumbnail($eeFilePath) { // Expects FilePath relative to FileListDir
		
		if( $this->eeSFL->eeListSettings['ShowFileThumb'] != 'YES' ) { return TRUE; }
		
		$eePathParts = pathinfo($eeFilePath);
		$eeFileNameOnly = $eePathParts['filename'];
		if(strpos($eeFileNameOnly, 'thumb_') === 0) { return false; } // Ignore thumbs.
		$eeFileExt = isset($eePathParts['extension']) ? $eePathParts['extension'] : '';
		$eeFileSubPath = $eePathParts['dirname'] !== '.' ? $eePathParts['dirname'] . '/' : '';
		$eeFileFullPath = ABSPATH . $this->eeSFL->eeListSettings['FileListDir'] . $eeFilePath;
		$eeThumbsPath = ABSPATH . $this->eeSFL->eeListSettings['FileListDir'] . $eeFileSubPath . '.thumbnails/';
		$eeThumbFileToCheck = 'thumb_' . $eeFileNameOnly . '.jpg';
	
	
		// Ensure the .thumbnails directory exists
		if(!is_dir($eeThumbsPath)) {
			if(!mkdir($eeThumbsPath, 0755, true)) { // Ensure recursive directory creation
				$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . ' !!!! Cannot create the .thumbnails directory: ' . $eeThumbsPath;
				return FALSE;
			}
		}
	
		// Check if the thumbnail already exists
		if(is_file($eeThumbsPath . $eeThumbFileToCheck)) {
			return TRUE; // Thumbnail already exists, no need to regenerate
		}
	
		// Determine if a thumbnail needs to be created based on file extension and settings
		$shouldGenerateThumb = FALSE;
		if(in_array($eeFileExt, $this->eeDynamicImageThumbFormats) && $this->eeSFL->eeListSettings['GenerateImgThumbs'] == 'YES') {
			$shouldGenerateThumb = TRUE;
		} elseif(in_array($eeFileExt, $this->eeDynamicVideoThumbFormats) && $this->eeSFL->eeListSettings['GenerateVideoThumbs'] == 'YES' && isset($this->eeEnvironment['thumbsVIDEO'])) {
			$shouldGenerateThumb = TRUE;
		} elseif($eeFileExt == 'pdf' && $this->eeSFL->eeListSettings['GeneratePDFThumbs'] == 'YES' && isset($this->eeEnvironment['thumbsPDF'])) {
			$shouldGenerateThumb = TRUE;
		}
	
		// Generate the thumbnail if needed
		if($shouldGenerateThumb) {
			// Depending on the file type, call the appropriate method to generate the thumbnail
			switch($eeFileExt) {
				case 'pdf':
					return $this->eeSFL_CreatePDFThumbnail($eeFileFullPath);
				case in_array($eeFileExt, $this->eeDynamicVideoThumbFormats):
					return $this->eeSFL_CreateVideoThumbnail($eeFileFullPath);
				default:
					return $this->eeSFL_CreateThumbnailImage($eeFileFullPath);
			}
		}
	
		return FALSE; // If the code reaches this point, no thumbnail was needed/created
	}
	
	
	
	
	
	// Create Image Thumbnail
	private function eeSFL_CreateThumbnailImage($eeInputFileCompletePath) {
		
		if (!is_file($eeInputFileCompletePath)) {
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . ' !!!! Source File Not Found for ' . basename($eeInputFileCompletePath);
			return FALSE;
		}
	
		$eePathParts = pathinfo($eeInputFileCompletePath);
		$eeFileNameOnly = $eePathParts['filename'];
		$eeFileExt = $eePathParts['extension'];
		$eeCompleteDir = rtrim($eePathParts['dirname'], '/') . '/'; // Ensure no double slashes
		$eeThumbsPath = strpos($eeCompleteDir, '/.thumbnails/') === FALSE ? $eeCompleteDir . '.thumbnails/' : $eeCompleteDir;
	
		$eeFileImage = wp_get_image_editor($eeInputFileCompletePath);  // Using WP's memory checker
		if (is_wp_error($eeFileImage)) {
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Error opening image file: ' . basename($eeInputFileCompletePath);
			return FALSE;
		}
	
		$eeFileImage->resize($this->eeFileThumbSize, $this->eeFileThumbSize, TRUE);
		$result = $eeFileImage->save($eeThumbsPath . 'thumb_' . $eeFileNameOnly . '.jpg');
	
		if (is_wp_error($result)) {
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Error saving thumbnail for: ' . basename($eeInputFileCompletePath);
			return FALSE;
		}
	
		$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Thumbnail created for ' . basename($eeInputFileCompletePath);
		return TRUE;
	}
	
	
	
	
	
	// Create Video Thumbnail
	private function eeSFL_CreateVideoThumbnail($eeFileFullPath) {
		
		// All The Path Parts
		$eePathParts = pathinfo($eeFileFullPath);
		$eeFileNameOnly = $eePathParts['filename'];
		
		// Create a temporary file in the general temp directory
		$eeScreenshot = eeSFL_TempDir . 'temp_' . $eeFileNameOnly . '.png';
	
		// Prepare the FFmpeg command for generating the screenshot
		$eeCommand = escapeshellcmd("ffmpeg -i " . escapeshellarg($eeFileFullPath) . " -ss 00:00:01.000 -vframes 1 " . escapeshellarg($eeScreenshot));
	
		// Execute the command and retrieve the output and return value
		exec($eeCommand, $eeOutput, $eeReturnValue);
	
		// Check if the screenshot was successfully created
		if($eeReturnValue === 0 && is_file($eeScreenshot)) {
			// Pass the screenshot to create thumbnail image without the need to delete it as it's in temp directory
			return $this->eeSFL_CreateThumbnailImage($eeScreenshot);
		} else {
			// Log FFmpeg failure
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'FFmpeg failed to create a screenshot for ' . basename($eeFileFullPath) . ' | Command: ' . $eeCommand . ' | Return value: ' . $eeReturnValue;
			return FALSE;
		}
	}
	
	
	
	private function eeSFL_CreateVideoThumbnail_OLD($eeFileFullPath) { // Expects Full Path
		
		// All The Path Parts
		$eePathParts = pathinfo($eeFileFullPath);
		$eeFileNameOnly = $eePathParts['filename'];
		$eeFileExt = $eePathParts['extension'];
		$eeCompleteDir = $eePathParts['dirname'] . '/';
		$eeThumbsPath = $eeCompleteDir . '.thumbnails/';
		
		if(is_dir($eeThumbsPath)) {
			
			// Create a temporary file
			$eeScreenshot = $eeThumbsPath . 'temp_' . $eeFileNameOnly . '.png';
			
			// Create a full-sized image at the one-second mark
			$eeCommand = 'ffmpeg -i ' . $eeFileFullPath . ' -ss 00:00:01.000 -vframes 1 ' . $eeScreenshot;
			
			$eeFFmpeg = trim(shell_exec($eeCommand));
			
			if(is_file($eeScreenshot)) { // Resize down to $this->eeFileThumbSize
				
				if( $this->eeSFL_CreateThumbnailImage($eeScreenshot) ) {
					unlink($eeScreenshot); // Delete the screeshot file
					return TRUE;
				} else {
					unlink($eeScreenshot); // Delete the screeshot file anyway
					return FALSE;
				}
			
			} else {
				
				// FFmpeg FAILED !!!
				$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'FFmpeg could not create a screenshot for ' . basename($eeScreenshot);
				return FALSE;
			}
		}
		
		$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . ' !!!! There is no .thumbnails directory: ' . $eeThumbsPath;
		
		return FALSE;
	}
	
	
	
	
	
	// Generate PDF Thumbnails
	private function eeSFL_CreatePDFThumbnail($eeFileFullPath) {
		
		$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Generating PDF Thumbnail...';
		
		$eePathParts = pathinfo($eeFileFullPath);
		$eeFileNameOnly = $eePathParts['filename'];
		$eeFileExt = $eePathParts['extension'];
		if ($eeFileExt !== 'pdf') { return FALSE; } // Ensure we're dealing with a PDF
		
		// Check Size and set image resolution higher for smaller sizes.
		$eeFileSize = filesize($eeFileFullPath);
		if($eeFileSize >= 8388608) { // Greater than 8 MB
			$eeResolution = '72';
			$eeQuality = '60';
			$eeQFactor = '.25';
		} elseif($eeFileSize < 8388608 AND $eeFileSize > 2097152) { // Less than 8MB but larger than 2 MB 
			$eeResolution = '150';
			$eeQuality = '75';
			$eeQFactor = '.5';
		} else { // Less than 2 MB
			$eeResolution = '300';
			$eeQuality = '90';
			$eeQFactor = '.75';
		}
		
		$eeTempFile = 'temp_' . $eeFileNameOnly . '.jpg'; // Temp file for the PDF thumbnail
		$eeTempFileFullPath = eeSFL_TempDir . $eeTempFile; // Use the general temp directory
	
		// The Ghostscript command for generating the thumbnail
		$eeCommand = 'gs -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=' . 
		escapeshellarg($eeQuality) . ' -dQFactor=' . 
		escapeshellarg($eeQFactor) . ' -r' . 
		escapeshellarg($eeResolution) . 
		' -dFirstPage=1 -dLastPage=1 -sOutputFile=' . 
		escapeshellarg($eeTempFileFullPath) . ' ' . 
		escapeshellarg($eeFileFullPath);
	
		// Execute the command
		exec($eeCommand, $eeOutput, $eeReturnValue);
	
		if ($eeReturnValue === 0) {
			// Successfully generated the thumbnail, so now we'll hand it off to be processed
			// as a standard image thumbnail by our existing image thumbnail generator
			return $this->eeSFL_CreateThumbnailImage($eeTempFileFullPath);
		} else {
			// Log failure details
			$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . " - Ghostscript failed to generate a thumbnail for $eeFileNameOnly.pdf, Return Value: $eeReturnValue";
			return FALSE;
		}
	}

	
	
	private function eeSFL_CreatePDFThumbnail_OLD($eeFileFullPath) { // Expects Full Path
		
		$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Generating PDF Thumbnail...';
		
		$eePathParts = pathinfo($eeFileFullPath);
		$eeFileNameOnly = $eePathParts['filename'];
		$eeFileExt = $eePathParts['extension'];
		$eeCompleteDir = $eePathParts['dirname'] . '/';
		$eeThumbsPath = $eeCompleteDir . '.thumbnails/';
		$eeTempFile = 'temp_' . $eeFileNameOnly . '.jpg'; // The converted pdf file - A temporary file
		$eeTempFileFullPath = eeSFL_TempDir . $eeTempFile;
		
		if($eeFileExt != 'pdf') { return FALSE; }
		
		if( isset($this->eeEnvironment['thumbsPDF']) ) {
		
			// $this->eeSFL->eeLog['notice'][] = 'ImageMagik & GhostScript is Installed';
			
			// Check Size and set image resolution higher for smaller sizes.
			$eeFileSize = filesize($eeFileFullPath);
			if($eeFileSize >= 8388608) { // Greater than 8 MB
				$eeResolution = '72';
				$eeBits = '2';
				$eeQuality = '60';
				$eeQFactor = '.25';
			} elseif($eeFileSize < 8388608 AND $eeFileSize > 2097152) { // Less than 8MB but larger than 2 MB 
				$eeResolution = '150';
				$eeBits = '2';
				$eeQuality = '75';
				$eeQFactor = '.5';
			} else { // Less than 2 MB
				$eeResolution = '300';
				$eeBits = '4';
				$eeQuality = '90';
				$eeQFactor = '.75';
			}
			
			// GhostScript Operations
			if( !is_readable($eeTempFileFullPath) ) { // Might be there already.
			
				// Check PDF Validity
				$eeCommand = 'gs -dNOPAUSE -dBATCH -sDEVICE=nullpage ' . $eeFileFullPath;
				
				// Run the Command. Drum roll please
				exec( $eeCommand, $eeCommandOutput, $eeReturnVal );
				
				if($eeReturnVal === 0) { // Zero == No Errors
					
					// The command. AVOID LINE BREAKS
					// $eeCommand = 'gs -dNOPAUSE -sDEVICE=png16m -dGraphicsAlphaBits=' . $eeBits . ' -dTextAlphaBits=' . $eeBits . ' -r' . $eeResolution . ' -dFirstPage=1 -dLastPage=1 -sOutputFile=' . $eeTempFileFullPath . ' ' . $eeFileFullPath;
					$eeCommand = 'gs -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=' . $eeQuality . ' -dQFactor=' . $eeQFactor . ' -r' . $eeResolution . ' -dFirstPage=1 -dLastPage=1 -sOutputFile=' . $eeTempFileFullPath . ' ' . $eeFileFullPath;
						
					// Run the Command. Drum roll please
					exec( $eeCommand, $eeCommandOutput, $eeReturnVal );
					
					// $this->eeSFL->eeLog['GhostScript'][] = $eeCommand;
					// $this->eeSFL->eeLog['GhostScript'][] = $eeCommandOutput;
					// $this->eeSFL->eeLog['GhostScript'][] = $eeReturnVal;
				
				} else {
				
					$this->eeSFL->eeLog['GhostScript'][] = $eeCommand;
					$this->eeSFL->eeLog['GhostScript'][] = $eeCommandOutput;
					$this->eeSFL->eeLog['GhostScript'][] = $eeReturnVal;
					
					$this->eeSFL->eeLog['notice'][] = 'FILE NOT READABLE: ' . basename($eeFileFullPath);
					$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . '!!!! PDF NOT READABLE: ' . basename($eeFileFullPath);
					return FALSE;
				}
			}
				
			// Confirm the file is there
			if(is_readable($eeTempFileFullPath)) {
				
				if($this->eeSFL_CreateThumbnailImage($eeTempFileFullPath)) {
					
					$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Created the PDF Thumbnail for ' . basename($eeFileFullPath);
										
					return TRUE;
					
				} else {
					
					$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . '!!!! FAILED to Create the PDF Thumbnail for ' . basename($eeFileFullPath);
					
					return FALSE;
				}
			
			} elseif(is_file($eeTempFileFullPath)) {
								
				return FALSE;
			
			} else {
				
				$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . '!!!! PDF to PNG FAILED for ' . basename($eeFileFullPath);
				
				return FALSE;
			}		
		}
		
		return FALSE;
	}

}

?>