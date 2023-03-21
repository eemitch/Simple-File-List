// File Management JavaScript


// Delete Click Handler
function eeSFL_DeleteFile(eeSFL_FileID) {
	
	event.preventDefault(); // Don't follow the link
	
	console.log('Deleting File: ' + eeSFL_FileID);
	
	// Get the File Name
    var eeSFL_FileName = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' .eeSFL_RealFileName').text();
    
    console.log(eeSFL_FileName);
	
	if( confirm( eesfl_vars['eeConfirmDeleteText'] + "\r\n\r\n" + eeSFL_FileName ) ) {
	
		eeSFL_EditFileAction(eeSFL_FileID, 'Delete');
	
	}
}







// Edit Button Handler
function eeSFL_OpenEditModal(eeSFL_FileID) {
	
	event.preventDefault(); // Don't follow the link
	
	console.log('Editing File: ' + eeSFL_FileID);
	
	jQuery('#eeSFL_Modal_Manage').show();
	
	// Clear these because the last chosen file info might show here
	jQuery('#eeSFL_FileNiceNameNew').val('');
	jQuery('#eeSFL_FileDescriptionNew').text('');
	jQuery('#eeSFL_FileDescriptionNew').val('');
	
	// Pre-Populate the Modal
	jQuery('#eeSFL_Modal_Manage .eeSFL_Modal_Manage_FileID').text(eeSFL_FileID);
	
	eeSFL_FileNameOld = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text();
	jQuery('#eeSFL_FileNameNew').val(eeSFL_FileNameOld);
	
	eeSFL_FileNiceNameOld = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileNiceName').text();
	eeSFL_FileNiceNameOld = eeSFL_FileNiceNameOld.eeSFL_StripSlashes();
	jQuery('#eeSFL_FileNiceNameNew').val(eeSFL_FileNiceNameOld);
	
	eeSFL_FileDescriptionOld = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' .eeSFL_FileDesc').text();
	jQuery('#eeSFL_FileDescriptionNew').val(eeSFL_FileDescriptionOld);
	
	eeSFL_FileSize = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileSize').text();
	jQuery('#eeSFL_FileSize').text(eeSFL_FileSize);
	
	eeSFL_FileDateAdded = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileDateAdded').text();
	jQuery('#eeSFL_FileDateAdded').text(eeSFL_FileDateAdded);
	
	eeSFL_FileDateChanged = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileDateChanged').text();
	jQuery('#eeSFL_FileDateChanged').text(eeSFL_FileDateChanged);
	
}





// Modal Form Has Been Saved
function eeSFL_FileEditSaved() {
	
	eeSFL_FileID = jQuery('#eeSFL_Modal_Manage .eeSFL_Modal_Manage_FileID').text();
	
	// Get Modal Form Inputs and Set Variables
	eeSFL_FileNameNew = jQuery('#eeSFL_FileNameNew').val();
	
	if(eeSFL_FileNameNew.length < 1) {
		
		eeSFL_FileNameNew = false;
		
	} else if(eeSFL_FileNameOld == eeSFL_FileNameNew) { 
		
		eeSFL_FileNameNew = false;
		
	} else {
		
		if(eeSFL_FileNameNew.indexOf('.') == -1) { // It's Not a File
			
			eeSFL_FileNameNew = '';
		
		} else { 
			
			// Sanitize File Name
			eeSFL_FileNameNew = eeSFL_FileNameNew.replace(/ /g, '-'); // Deal with spaces
			eeSFL_FileNameNew = eeSFL_FileNameNew.replace(/--/g, '-'); // Deal with double dash
			
			// Disallow removing extension
			if(eeSFL_FileNameNew.indexOf('.') == -1) { eeSFL_FileNameNew = false; }
			
			// Remove dots from name-part
			var eeArray = eeSFL_FileNameNew.split('.');
			
			if(eeArray.length > 2) {
				
				var eeExt = eeArray.pop();
				var eeFileNameNew = '';
				
				for(i = 0; i < eeArray.length; i++) {
					eeFileNameNew += eeArray[i] + '-'; // Rebuild using dashes
				}
				eeFileNameNew = eeFileNameNew.substring(0, eeFileNameNew.length - 1); // Strip last dash
				
				eeSFL_FileNameNew = eeFileNameNew + '.' + eeExt;
			}
		}
	}
	
	eeSFL_FileNiceNameNew = jQuery('#eeSFL_FileNiceNameNew').val();
	if(eeSFL_FileNiceNameOld != eeSFL_FileNiceNameNew) { 
		if(eeSFL_FileNiceNameNew.length < 1 ) { eeSFL_FileNiceNameNew = ''; } // We'll trim later to remove this completely
	} else {
		eeSFL_FileNiceNameNew = false;
	}
	
	eeSFL_FileDescriptionNew = jQuery('#eeSFL_FileDescriptionNew').val();
	if(eeSFL_FileDescriptionOld != eeSFL_FileDescriptionNew) { 
		if(eeSFL_FileDescriptionNew.length < 1 ) { eeSFL_FileDescriptionNew = ''; } // We'll trim later to remove this completely
	} else {
		eeSFL_FileDescriptionNew = false;
	}
	
	if( eeSFL_FileNameNew !== false || eeSFL_FileNiceNameNew !== false || eeSFL_FileDescriptionNew !== false ) {
	
		console.log('Saving Edits to File: ' + eeSFL_FileID);
		
		if(eeSFL_FileNameNew == false) { eeSFL_FileNameNew = ''; } // Otherwise a file could not be named "false"
		
		eeSFL_EditFileAction(eeSFL_FileID, 'Edit');
	
	} else {
		
		console.log('Nothing Has Changed');
	}
	
	// Hide the Modal Overlay
	jQuery('#eeSFL_Modal_Manage').hide('slow');
}






function eeSFL_EditFileAction(eeSFL_FileID, eeSFL_FileAction) {
	
	event.preventDefault(); // Don't follow link
	
	console.log(eeSFL_FileAction + ' -> ' + eeSFL_FileID);
	
	// The File Action Engine
	var eeActionEngine = eesfl_vars.ajaxurl;
	var eeFormData = false;
	var eeSFL_ActionNonce = jQuery('#eeSFL_ActionNonce').text(); // Get the Nonce


	if(eeSFL_FileAction == 'Edit') {
		
		eeFormData = {
			'action': 'simplefilelist_edit_job',
			'eeFileAction': 'Edit',
			'eeFileName': eeSFL_FileNameOld,
			'eeFileNameNew': eeSFL_FileNameNew,
			'eeFileNiceNameNew': eeSFL_FileNiceNameNew,
			'eeFileDescNew': eeSFL_FileDescriptionNew,
			'eeSecurity': eeSFL_ActionNonce
		};
		
	} else if(eeSFL_FileAction == 'Delete') {
		
		// Get the File Name
		var eeSFL_FileName = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text();
		
		eeFormData = {
			'action': 'simplefilelist_edit_job',
			'eeFileName': eeSFL_FileName,
			'eeFileAction': 'Delete',
			'eeSecurity': eeSFL_ActionNonce
		};
	
	}
	
	console.log(eeFormData);
	
	
	// AJAX
	console.log('Calling: ' + eeActionEngine);

	jQuery.post(eeActionEngine, eeFormData, function(response) {
		
		if(response == 'SUCCESS') { // :-)
			
			if(eeSFL_FileAction == 'Edit') {
				
				// Update the Display
				
				var eeFileNameDisplay = eeSFL_FileNameOld;
				var eeFileNameActual = eeSFL_FileNameOld;

				
				if(eeSFL_FileNameNew) {
					
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text(eeSFL_FileNameNew);
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' a.eeSFL_FileOpen').attr('href', '/' + eeSFL_FileListDir + eeSFL_FileNameNew);
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' a.eeSFL_FileDownload').attr('href', '/' + eeSFL_FileListDir + eeSFL_FileNameNew);
					
					eeFileNameDisplay = eeSFL_FileNameNew;
					eeFileNameActual = eeSFL_FileNameNew;
				}
				
				
				if( eeSFL_FileNiceNameNew.length >= 1 ) {
					
					console.log('Changed Nice Name');
					
					eeFileNameDisplay = eeSFL_FileNiceNameNew;
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileNiceName').text(eeSFL_FileNiceNameNew);
				
				} else {
					
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileNiceName').text(eeFileNameActual);
					eeFileNameDisplay = eeFileNameActual;
					
				}
				
				jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' a.eeSFL_FileName').attr('href', '/' + eeSFL_FileListDir + eeFileNameActual);
				jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' a.eeSFL_FileName').text(eeFileNameDisplay);
				
				
				if(eeSFL_FileDescriptionNew) {
					
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' p.eeSFL_FileDesc').removeClass('eeHide');
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' p.eeSFL_FileDesc').text(eeSFL_FileDescriptionNew);
					eeSFL_FileDescriptionOld = ''; 
				
				} else if(eeSFL_FileDescriptionNew == '') {
					
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' p.eeSFL_FileDesc').addClass('eeHide');
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' p.eeSFL_FileDesc').text('');
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' .eeSFL_FileDesc').text('');
					eeSFL_FileDescriptionOld = '';
				}
				
				eeSFL_FileDescriptionOld = '';
				eeSFL_FileNiceNameOld = '';
				
				
				
			} else if(eeSFL_FileAction == 'Delete') {
				
				jQuery('#eeSFL_FileID-' + eeSFL_FileID).hide('slow');
				
			}
		
		} else { // NOT SUCCESS :-(
			
			alert(response);
		}
		
		console.log(response);
		
		
	});
}


