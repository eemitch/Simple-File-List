// Simple File List Script: ee-footer.js | Author: Mitchell Bennis | support@simplefilelist.com

// Used in front-side and back-side file list display

console.log('ee-footer.js Loaded');

// Upon page load completion...
jQuery(document).ready(function($) {	

	console.log('eeSFL Document Ready');
	
	window.addEventListener('touchstart', function() {
		eeSFL_BASE_isTouchscreen = true;
	});
	
	
	jQuery('#eeSFL_Modal_Manage_Close').on('click', function() {
		jQuery('#eeSFL_Modal_Manage').hide();
	});	
	
	
	
	
		

}); // END Ready Function




// Copy File URL to Clipboard
function eeSFL_BASE_CopyLinkToClipboard(eeSFL_FileURL) {
	
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


String.prototype.eeStripSlashes = function(){
    return this.replace(/\\(.)/mg, "$1");
}





/*

function eeSFL_BASE_EditRename(eeSFL_FileID) {
	
	var eeName1 = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileLink').text(); // Current File Name
	var eeName2 = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' input.eeFileNameNew').val(); // New File Name
	
	if(eeName1 != eeName2) { // If no match, we rename
		
		eeSFL_BASE_FileAction(eeSFL_FileID, 'Rename');
	}
	
	jQuery('#eeSFL_EditFileWrap_' + eeSFL_FileID).slideUp();
	jQuery('#eeSFL_EditFile_' + eeSFL_FileID).text('Edit');
}

function eeSFL_BASE_EditDesc(eeSFL_FileID) {
	
	var eeDesc1 = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' span.eeSFL_SavedDesc').text(); // Current Desc
	var eeDesc2 = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' input.eeSFL_NewFileDesc').val(); // New Desc
	
	if(eeDesc1 != eeDesc2) { // If no match, we update  && !eeRenaming
		
		jQuery('#eeSFL_EditFileWrap_' + eeSFL_FileID).slideUp();
		eeSFL_BASE_FileAction(eeSFL_FileID, 'UpdateDesc');
	}
}
*/




// AJAX Post to File Engine
function eeSFL_BASE_FileAction(eeSFL_FileID, eeSFL_Action) {
	
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
		// var eeSFL_FileNameNew = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' input.eeFileNameNew').val();
		var eeSFL_FileNameNew = jQuery('#eeSFL_Modal_Manage input.eeFileNameNew').val();
		
		// Sanitize to match reality
		eeSFL_FileNameNew = eeSFL_FileNameNew.replace(/ /g, '-'); // Deal with spaces
		eeSFL_FileNameNew = eeSFL_FileNameNew.replace(/--/g, '-'); // Deal with double dash
		
		if(eeSFL_FileName.indexOf('.') == -1) { // It's Not a File
			
			eeSFL_FileNameNew = eeSFL_FileNameNew.replace(/\./g, '_'); // Replace dots
		
		} else { 
			
			if(eeSFL_FileNameNew.indexOf('.') == -1) { // Disallow removing extension
				return false;
			}
			
			// Remove dots from name-part
			var eeArray = eeSFL_FileNameNew.split('.');
			
			if(eeArray.length > 2) {
				
				console.log('Problem Filename: ' + eeSFL_FileNameNew);
				
				var eeExt = eeArray.pop();
				var eeFileNameNew = '';
				
				for(i = 0; i < eeArray.length; i++) {
					eeFileNameNew += eeArray[i] + '_';
				}
				eeFileNameNew = eeFileNameNew.substring(0, eeFileNameNew.length - 1); // Strip last dash
				
				eeSFL_FileNameNew = eeFileNameNew + '.' + eeExt;
			}
		}
	
		var eeFormData = {
			'action': 'simplefilelist_edit_job',
			'eeFileName': eeSFL_FileName,
			'eeFileAction': eeSFL_Action + '|' + eeSFL_FileNameNew,
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
					
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' .eeFileNameNew').val(eeSFL_FileNameNew);
					
					jQuery('div.eeSFL_FileRenameEntry').hide();
					
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text(eeSFL_FileNameNew);
					
					// Make a New Link
					var eeNewLink = '<a class="eeSFL_FileName" href="/' + eeSFL_FileListDir + eeSFL_FileNameNew + '">' + eeSFL_FileNameNew + '</a>';
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileLink').html(eeNewLink);
					
					// Update the Open link
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' a.eeSFL_FileOpen').attr('href', '/' + eeSFL_FileListDir + eeSFL_FileNameNew);
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' a.eeSFL_FileDownload').attr('href', '/' + eeSFL_FileListDir + eeSFL_FileNameNew);
					
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
