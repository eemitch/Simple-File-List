// File Management JavaScript


// Delete Click Handler
function eeSFL_DeleteFile(eeSFL_FileID) {
	
	event.preventDefault(); // Don't follow the link
	
	console.log('Deleting File: ' + eeSFL_FileID);
	
	// Use eeSFL_RealFilePath if Searching
	var eeSFL_RealFilePath = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_RealFilePath').text();
	if(eeSFL_RealFilePath) { eeSFL_SubFolder = eeSFL_RealFilePath; }
	
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
	
	console.log('Editing File ID#: ' + eeSFL_FileID);
	
	// Use eeSFL_RealFilePath if Searching
	var eeSFL_RealFilePath = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_RealFilePath').text();
	if(eeSFL_RealFilePath) { eeSFL_SubFolder = eeSFL_RealFilePath; }
	
	var eeSFL_RealFileName = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text();
	jQuery('#eeSFL_Modal_EditFile .eeSFL_ModalFilePath').text(eeSFL_RealFileName);
	
	// Get the Name and Extension
	eeSFL_OriginalFileNameOnly = eeSFL_GetFileNameWithoutExtension(eeSFL_RealFileName);
	eeSFL_OriginalFileExtension = eeSFL_GetFileExtension(eeSFL_RealFileName);
	console.log('Name: ' + eeSFL_OriginalFileNameOnly);
	console.log('Type: ' + eeSFL_OriginalFileExtension);
	
	// Open the Edit Modal
	jQuery('#eeSFL_Modal_EditFile').show();
	
	// Clear these because the last chosen file info might show here
	jQuery('#eeSFL_FileNiceNameNew').val('');
	jQuery('#eeSFL_FileDescriptionNew').text('');
	jQuery('#eeSFL_FileDescriptionNew').val('');
	
	// Pre-Populate the Modal
	jQuery('#eeSFL_Modal_FileID').text(eeSFL_FileID);
	
	eeSFL_FileNameOld = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text();
	jQuery('#eeSFL_FileNameNew').val(eeSFL_FileNameOld);
	
	eeSFL_FileNiceNameOld = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileNiceName').text();
	eeSFL_FileNiceNameOld = eeSFL_FileNiceNameOld.eeSFL_StripSlashes();
	jQuery('#eeSFL_FileNiceNameNew').val(eeSFL_FileNiceNameOld);
	
	eeSFL_FileDescriptionOld = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' .eeSFL_FileDesc').text();
	jQuery('#eeSFL_FileDescriptionNew').val(eeSFL_FileDescriptionOld);
	
	eeSFL_FileSize = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileSize').text();
	jQuery('#eeSFL_FileSize').text(eeSFL_FileSize);
	
	// Dates - Icky
	
	eeSFL_FileDateAdded = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileDateAddedNice').text();
	jQuery('#eeSFL_FileDateAdded').text(eeSFL_FileDateAdded);
	eeSFL_FileDateAdded = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileDateAdded').text();
	
	eeArrayDateAdded = eeSFL_FileDateAdded.split('-');
	eeSFL_FileDateAddedYearOld = eeArrayDateAdded[0];
	eeSFL_FileDateAddedMonthOld = eeArrayDateAdded[1];
	eeSFL_FileDateAddedDayOld = eeArrayDateAdded[2];
	jQuery('#eeSFL_FileDateAddedYearNew').val(eeSFL_FileDateAddedYearOld);
	jQuery('#eeSFL_FileDateAddedMonthNew').val(eeSFL_FileDateAddedMonthOld);
	jQuery('#eeSFL_FileDateAddedDayNew').val(eeSFL_FileDateAddedDayOld);
	
	
	eeSFL_FileDateChanged = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileDateChangedNice').text();
	jQuery('#eeSFL_FileDateChanged').text(eeSFL_FileDateChanged);
	eeSFL_FileDateChanged = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileDateChanged').text();
	
	eeArrayDateChanged = eeSFL_FileDateChanged.split('-');
	eeSFL_FileDateChangedYearOld = eeArrayDateChanged[0];
	eeSFL_FileDateChangedMonthOld = eeArrayDateChanged[1];
	eeSFL_FileDateChangedDayOld = eeArrayDateChanged[2];
	jQuery('#eeSFL_FileDateChangedYearNew').val(eeSFL_FileDateChangedYearOld);
	jQuery('#eeSFL_FileDateChangedMonthNew').val(eeSFL_FileDateChangedMonthOld);
	jQuery('#eeSFL_FileDateChangedDayNew').val(eeSFL_FileDateChangedDayOld);
	
	eeSFL_AttachBlurEventToInput();
		
}





// Modal Form Has Been Saved
function eeSFL_FileEditSaved() {
	
	var eeChanged = false;
	
	eeSFL_FileID = jQuery('#eeSFL_Modal_FileID').text();
	
	// Get Modal Form Inputs and Set Variables
	eeSFL_FileNameNew = jQuery('#eeSFL_FileNameNew').val();
	
	if(eeSFL_FileNameNew.length < 1) {
		
		eeSFL_FileNameNew = false;
		
	} else if(eeSFL_FileNameOld == eeSFL_FileNameNew) { 
		
		eeSFL_FileNameNew = false;
		
	} else {
		
		eeSFL_FileNameNew = eeSFL_SanitizeFileName(eeSFL_FileNameNew);
		
		eeChanged = true;
	}
	
	eeSFL_FileNiceNameNew = jQuery('#eeSFL_FileNiceNameNew').val();
	if(eeSFL_FileNiceNameOld != eeSFL_FileNiceNameNew) { // We'll trim later to remove this completely
		eeChanged = true; 
	}
	
	eeSFL_FileDescriptionNew = jQuery('#eeSFL_FileDescriptionNew').val();
	if(eeSFL_FileDescriptionOld != eeSFL_FileDescriptionNew) { 
		eeChanged = true;
	}
	
	// Dates - What a PAIN! (Needs Improvement)
	
	eeSFL_FileDateAddedYearNew = jQuery('#eeSFL_FileDateAddedYearNew').val();
	if(eeSFL_FileDateAddedYearOld == eeSFL_FileDateAddedYearNew) { eeSFL_FileDateAddedYearNew = false; } else { eeChanged = true; }
	
	eeSFL_FileDateAddedMonthNew = jQuery('#eeSFL_FileDateAddedMonthNew').val();
	if(eeSFL_FileDateAddedMonthOld == eeSFL_FileDateAddedMonthNew) { eeSFL_FileDateAddedMonthNew = false; } else { eeChanged = true; }
	
	eeSFL_FileDateAddedDayNew = jQuery('#eeSFL_FileDateAddedDayNew').val();
	if(eeSFL_FileDateAddedDayOld == eeSFL_FileDateAddedDayNew) { eeSFL_FileDateAddedDayNew = false; } else { eeChanged = true; }
	
	if(eeSFL_FileDateAddedYearNew != false || eeSFL_FileDateAddedMonthNew != false || eeSFL_FileDateAddedDayNew != false) {
		if(eeSFL_FileDateAddedYearNew == false) { eeSFL_FileDateAddedYear = eeSFL_FileDateAddedYearOld; } else { eeSFL_FileDateAddedYear = eeSFL_FileDateAddedYearNew; }
		if(eeSFL_FileDateAddedMonthNew == false) { eeSFL_FileDateAddedMonth = eeSFL_FileDateAddedMonthOld; } else { eeSFL_FileDateAddedMonth = eeSFL_FileDateAddedMonthNew; }
		if(eeSFL_FileDateAddedDayNew == false) { eeSFL_FileDateAddedDay = eeSFL_FileDateAddedDayOld; } else { eeSFL_FileDateAddedDay = eeSFL_FileDateAddedDayNew; }
		eeSFL_FileDateAddedNew = eeSFL_FileDateAddedYear + '-' + eeSFL_FileDateAddedMonth + '-' + eeSFL_FileDateAddedDay;
	} else {
		eeSFL_FileDateAddedNew = '';
	}
	
	eeSFL_FileDateChangedYearNew = jQuery('#eeSFL_FileDateChangedYearNew').val();
	if(eeSFL_FileDateChangedYearOld == eeSFL_FileDateChangedYearNew) { eeSFL_FileDateChangedYearNew = false; } else { eeChanged = true; }
	
	eeSFL_FileDateChangedMonthNew = jQuery('#eeSFL_FileDateChangedMonthNew').val();
	if(eeSFL_FileDateChangedMonthOld == eeSFL_FileDateChangedMonthNew) { eeSFL_FileDateChangedMonthNew = false; } else { eeChanged = true; }
	
	eeSFL_FileDateChangedDayNew = jQuery('#eeSFL_FileDateChangedDayNew').val();
	if(eeSFL_FileDateChangedDayOld == eeSFL_FileDateChangedDayNew) { eeSFL_FileDateChangedDayNew = false; } else { eeChanged = true; }
	
	if(eeSFL_FileDateChangedYearNew != false || eeSFL_FileDateChangedMonthNew != false || eeSFL_FileDateChangedDayNew != false) {
		if(eeSFL_FileDateChangedYearNew == false) { eeSFL_FileDateChangedYear = eeSFL_FileDateChangedYearOld; } else { eeSFL_FileDateChangedYear = eeSFL_FileDateChangedYearNew; }
		if(eeSFL_FileDateChangedMonthNew == false) { eeSFL_FileDateChangedMonth = eeSFL_FileDateChangedMonthOld; } else { eeSFL_FileDateChangedMonth = eeSFL_FileDateChangedMonthNew; }
		if(eeSFL_FileDateChangedDayNew == false) { eeSFL_FileDateChangedDay = eeSFL_FileDateChangedDayOld; } else { eeSFL_FileDateChangedDay = eeSFL_FileDateChangedDayNew; }
		eeSFL_FileDateChangedNew = eeSFL_FileDateChangedYear + '-' + eeSFL_FileDateChangedMonth + '-' + eeSFL_FileDateChangedDay;
	} else {
		eeSFL_FileDateChangedNew = '';
	}
	
	
	if( eeChanged === true ) {
	
		console.log('Saving Edits to File: ' + eeSFL_FileID);
		
		if(eeSFL_FileNameNew == false) { eeSFL_FileNameNew = ''; } // Otherwise a file could not be named "false"
		
		eeSFL_EditFileAction(eeSFL_FileID, 'Edit');
	
	} else {
		
		console.log('Nothing Has Changed');
	}
	
	// Hide the Modal Overlay
	jQuery('.eeSFL_Modal').hide();
}






function eeSFL_EditFileAction(eeSFL_FileID, eeSFL_FileAction) {
	
	event.preventDefault(); // Don't follow link
	
	eeSFL_ListID = jQuery('#eeSFL_ID').text();
	
	console.log('List ID = ' + eeSFL_ListID);
	console.log(eeSFL_FileAction + ' -> ' + eeSFL_FileID);
	
	// The File Action Engine
	var eeActionEngine = eesfl_vars.ajaxurl;
	var eeFormData = false;
	var eeSFL_ActionNonce = jQuery('#eeSFL_ActionNonce').text(); // Get the Nonce


	if(eeSFL_FileAction == 'Edit') {
		
		eeFormData = {
			'action': 'simplefilelist_edit_job',
			'eeSFL_ID': eeSFL_ListID,
			'eeFileName': eeSFL_FileNameOld,
			'eeSubFolder': eeSFL_SubFolder,
			'eeFileNameNew': eeSFL_FileNameNew,
			'eeFileNiceNameNew': eeSFL_FileNiceNameNew,
			'eeFileDescNew': eeSFL_FileDescriptionNew,
			'eeFileDateAdded': eeSFL_FileDateAddedNew,
			'eeFileDateChanged': eeSFL_FileDateChangedNew,
			'eeFileAction': 'Edit',
			'eeSecurity': eeSFL_ActionNonce
		};
		
	} else if(eeSFL_FileAction == 'Delete') {
		
		// Get the File Name
		var eeSFL_FileName = jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text();
		
		eeFormData = {
			'action': 'simplefilelist_edit_job',
			'eeSFL_ID': eeSFL_ListID,
			'eeFileName': eeSFL_FileName,
			'eeSubFolder': eeSFL_SubFolder,
			'eeFileAction': 'Delete',
			'eeSecurity': eeSFL_ActionNonce
		};
	
	}
	
	console.log(eeFormData);
	
	
	// AJAX
	console.log('Calling: ' + eeActionEngine);

	jQuery.post(eeActionEngine, eeFormData, function(response) {
		
		if(response.indexOf('SUCCESS') == 0) { // :-)
			
			if(eeSFL_FileAction == 'Edit') {
				
				// Update the Display
				console.log('Updating the Display for File ID# ' + eeSFL_FileID);
				
				var eeFileNameDisplay = eeSFL_FileNameOld;
				var eeFileNameActual = eeSFL_FileNameOld;

				
				if(eeSFL_FileNameNew) {
					
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text(eeSFL_FileNameNew);
					
					if( eeSFL_FileNameOld.indexOf('.') == -1 ) { // Folder
						
						console.log('Folder: ' + eeSFL_FileNameOld);
						console.log('Renamed To: ' + eeSFL_FileNameNew);
						
						if(eeSFL_SubFolder == '/') { eeSFL_SubFolder = ''; }
						
						var eeSFL_URL = new URL(eeSFL_ThisURL);
						var eeSFL_Parameter = eeSFL_URL.searchParams;
						
						eeSFL_Parameter.set('eeFolder', eeSFL_SubFolder + eeSFL_FileNameNew);
						
						eeSFL_URL.search = eeSFL_Parameter.toString();
						
						var eeSFL_NewURL = eeSFL_URL.toString();
						
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' a.eeSFL_FileName').attr('href', eeSFL_NewURL);
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' td.eeSFL_Thumbnail a').attr('href', eeSFL_NewURL);
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' a.eeSFL_FileOpen').attr('href', eeSFL_NewURL);
						
					} else {
						
						console.log('File: ' + eeSFL_FileNameOld);
						console.log('Renamed To: ' + eeSFL_FileNameNew);
						
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' a.eeSFL_FileName').attr('href', '/' + eeSFL_FileListDir + eeSFL_FileNameNew);
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' td.eeSFL_Thumbnail a').attr('href', '/' + eeSFL_FileListDir + eeSFL_FileNameNew);
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' a.eeSFL_FileOpen').attr('href', '/' + eeSFL_FileListDir + eeSFL_FileNameNew);
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' a.eeSFL_FileDownload').attr('href', '/' + eeSFL_FileListDir + eeSFL_FileNameNew);
						
					}
					
					eeFileNameDisplay = eeSFL_FileNameNew;
					eeFileNameActual = eeSFL_FileNameNew;
					
				}
				
				// Nice Name
				if(eeSFL_FileNiceNameNew != eeSFL_FileNiceNameOld) {
					
					if(eeSFL_FileNiceNameNew.length === 0) { // Removing the Nice Name
						
						eeFileNameDisplay = eeFileNameActual;
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileNiceName').text('');
						
					} else { // Add/Change Nice Name
						
						eeFileNameDisplay = eeSFL_FileNiceNameNew;
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileNiceName').text(eeSFL_FileNiceNameNew);
					}
				
				} else if(eeSFL_FileNiceNameOld.length >= 1) {
					eeFileNameDisplay = eeSFL_FileNiceNameOld;
				}
				
				// Set the Display Name
				jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' a.eeSFL_FileName').text(eeFileNameDisplay);
				
				
				
				// Description
				// console.log('eeSFL_FileDescriptionOld = ' + eeSFL_FileDescriptionOld);
				// console.log('eeSFL_FileDescriptionNew = ' + eeSFL_FileDescriptionNew);
				
				if(eeSFL_FileDescriptionNew != eeSFL_FileDescriptionOld) {
				
					if(eeSFL_FileDescriptionNew.length === 0) { // Removing the description
						
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' p.eeSFL_FileDesc').addClass('eeHide');
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' p.eeSFL_FileDesc').text('');
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' .eeSFL_FileDesc').text('');
					
					} else { // Add/Change Description
						
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' p.eeSFL_FileDesc').removeClass('eeHide');
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' p.eeSFL_FileDesc').text(eeSFL_FileDescriptionNew);
						
					}
				}
				
				// Check for a formatted date in the response
				if(response.indexOf('|') > 1) {
					
					var eeArray = response.split('|');
					var eeArray2 = eeArray[1].split('=');
					if(eeArray2[0] == 'Date') { 
						jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileDateDisplayed').text(eeArray2[1]);
					}
					
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileDateAddedNice').text('');
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileDateAdded').text(eeSFL_FileDateAddedNew);
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileDateChangedNice').text('');
					jQuery('#eeSFL_FileID-' + eeSFL_FileID + ' span.eeSFL_FileDateChanged').text(eeSFL_FileDateChangedNew);
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


