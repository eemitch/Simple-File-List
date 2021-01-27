// Simple File List Script: ee-footer.js | Author: Mitchell Bennis | support@simplefilelist.com

// Used in front-side and back-side file list display

console.log('ee-footer.js Loaded');

// Upon page load completion...
jQuery(document).ready(function($) {	

	console.log('eeSFL Document Ready');
	
	window.addEventListener('touchstart', function() {
		eeSFL_FREE_isTouchscreen = true;
	});	

}); // END Ready Function




// Copy File URL to Clipboard
function eeSFL_FREE_CopyLinkToClipboard(eeSFL_FileURL) {
	
	var eeTemp = jQuery('<input name="eeTemp" value="' + eeSFL_FileURL + '" type="url" class="" id="eeTemp" />'); // Create a temporary input
	jQuery("body").append(eeTemp); // Add it to the bottom of the page
	
	var eeTempInput = jQuery('#eeTemp');
	eeTempInput.focus();
	eeTempInput.select(); // Select the temp input
	// eeTempInput.setSelectionRange(0, 99999); /* For mobile devices <<<------------ TO DO */
	document.execCommand("copy"); // Copy to clipboard
	eeTemp.remove(); // Remove the temp input
    
    alert(eesfl_vars['eeCopyLinkText'] + "\r\n" + eeSFL_FileURL); // Alert the user
}




function eeSFL_FREE_EditFile(eeSFL_FileID) {
	
	event.preventDefault(); // Don't follow the link
	
	if( jQuery('#eeSFL_EditFileWrap_' + eeSFL_FileID).is(':visible') ) {
		
		jQuery('#eeSFL_EditFileWrap_' + eeSFL_FileID).slideUp();
		jQuery('#eeSFL_EditFile_' + eeSFL_FileID).text(eesfl_vars['eeEditText']);
	
	} else {
		
		jQuery('#eeSFL_EditFileWrap_' + eeSFL_FileID).slideDown();
		jQuery('#eeSFL_EditFile_' + eeSFL_FileID).text( eesfl_vars['eeCancelText'] );
	}
}




function eeSFL_FREE_EditRename(eeSFL_FileID) {
	
	var eeName1 = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileLink').text(); // Current File Name
	var eeName2 = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' input.eeNewFileName').val(); // New File Name
	
	if(eeName1 != eeName2) { // If no match, we rename
		
		eeSFL_FREE_FileAction(eeSFL_FileID, 'Rename');
	}
	
	jQuery('#eeSFL_EditFileWrap_' + eeSFL_FileID).slideUp();
	jQuery('#eeSFL_EditFile_' + eeSFL_FileID).text('Edit');
}

function eeSFL_FREE_EditDesc(eeSFL_FileID) {
	
	var eeDesc1 = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' span.eeSFL_SavedDesc').text(); // Current Desc
	var eeDesc2 = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' input.eeSFL_NewFileDesc').val(); // New Desc
	
	if(eeDesc1 != eeDesc2) { // If no match, we update  && !eeRenaming
		
		jQuery('#eeSFL_EditFileWrap_' + eeSFL_FileID).slideUp();
		eeSFL_FREE_FileAction(eeSFL_FileID, 'UpdateDesc');
	}
}





// Triggered when you click the Delete link
function eeSFL_FREE_Delete(eeSFL_FileID) {
	
	event.preventDefault(); // Don't follow the link
	
	console.log('Deleting File ID #' + eeSFL_FileID);
	
	// Get the File Name
    var eeSFL_FileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' .eeSFL_RealFileName').text();
    
    console.log(eeSFL_FileName);
	
	if( confirm( eesfl_vars['eeConfirmDeleteText'] + "\r\n\r\n" + eeSFL_FileName ) ) {
	
		eeSFL_FREE_FileAction(eeSFL_FileID, 'Delete');
	
	}

}




// AJAX Post to File Engine
function eeSFL_FREE_FileAction(eeSFL_FileID, eeSFL_Action) {
	
	event.preventDefault(); // Don't follow link
	
	console.log(eeSFL_Action + ' -> ' + eeSFL_FileID);
	
	// The File Action Engine
	var eeActionEngine = eesfl_vars.ajaxurl;
	
	// Current File Name
	var eeSFL_FileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text(); 
	
	// AJAX --------------
	
	// Get the Nonce
	var eeSFL_ActionNonce = jQuery('#eeSFL_ActionNonce').text();
	
	if(eeSFL_Action == 'Rename') {
		
		// Get the new name
		var eeSFL_NewFileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' input.eeNewFileName').val();
		
		// Sanitize to match reality
		eeSFL_NewFileName = eeSFL_NewFileName.replace(/ /g, '-'); // Deal with spaces
		eeSFL_NewFileName = eeSFL_NewFileName.replace(/--/g, '-'); // Deal with double dash
		
		if(eeSFL_FileName.indexOf('.') == -1) { // It's Not a File
			
			eeSFL_NewFileName = eeSFL_NewFileName.replace(/\./g, '_'); // Replace dots
		
		} else { 
			
			if(eeSFL_NewFileName.indexOf('.') == -1) { // Disallow removing extension
				return false;
			}
			
			// Remove dots from name-part
			var eeArray = eeSFL_NewFileName.split('.');
			
			if(eeArray.length > 2) {
				
				console.log('Problem Filename: ' + eeSFL_NewFileName);
				
				var eeExt = eeArray.pop();
				var eeNewFileName = '';
				
				for(i = 0; i < eeArray.length; i++) {
					eeNewFileName += eeArray[i] + '_';
				}
				eeNewFileName = eeNewFileName.substring(0, eeNewFileName.length - 1); // Strip last dash
				
				eeSFL_NewFileName = eeNewFileName + '.' + eeExt;
			}
		}
	
		var eeFormData = {
			'action': 'simplefilelist_edit_job',
			'eeFileName': eeSFL_FileName,
			'eeFileAction': eeSFL_Action + '|' + eeSFL_NewFileName,
			'eeSecurity': eeSFL_ActionNonce
		};
	
	} else if(eeSFL_Action == 'Delete') {
		
		// Get the File Name
		var eeSFL_FileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' .eeSFL_RealFileName').text();
		
		var eeFormData = {
			'action': 'simplefilelist_edit_job',
			'eeFileName': eeSFL_FileName,
			'eeFileAction': eeSFL_Action,
			'eeSecurity': eeSFL_ActionNonce
		};
	
	} else if(eeSFL_Action == 'UpdateDesc') {
		
		var eeSFL_FileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text(); // Name
		
		var eeSFL_FileDesc = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' input.eeSFL_NewFileDesc').val(); // Desc
		
		var eeFormData = {
			'action': 'simplefilelist_edit_job',
			'eeFileAction': eeSFL_Action,
			'eeFileName': eeSFL_FileName,
			'eeFileDesc': eeSFL_FileDesc,
			'eeSecurity': eeSFL_ActionNonce
		};	
	}
	
	if(eeSFL_Action && eeFormData) {

		console.log('Calling: ' + eeActionEngine);

		jQuery.post(eeActionEngine, eeFormData, function(response) {
			
			if(response == 'SUCCESS') {
				
				if(eeSFL_Action == 'Rename') {
					
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' .eeNewFileName').val(eeSFL_NewFileName);
					
					jQuery('div.eeSFL_FileRenameEntry').hide();
					
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text(eeSFL_NewFileName);
					
					// Make a New Link
					var eeNewLink = '<a class="eeSFL_FileName" href="/' + eeSFL_FileListDir + eeSFL_NewFileName + '">' + eeSFL_NewFileName + '</a>';
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileLink').html(eeNewLink);
					
					// Update the Open link
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' a.eeSFL_FileOpen').attr('href', '/' + eeSFL_FileListDir + eeSFL_NewFileName);
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' a.eeSFL_FileDownload').attr('href', '/' + eeSFL_FileListDir + eeSFL_NewFileName);
					
				} else if (eeSFL_Action == 'Delete') {
					
					jQuery('#eeSFL_RowID-' + eeSFL_FileID).hide('slow');
					
				} else if(eeSFL_Action == 'UpdateDesc') {
					
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileDesc').show();
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileDesc').html(eeSFL_FileDesc);
					
				}
			
			} else {
				alert(response);
			}
		});
	}
}
