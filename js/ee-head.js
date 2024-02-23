/* Simple File List Javascript | Mitchell Bennis | Element Engage, LLC | mitch@elementengage.com */

var eeSFL_isTouchscreen = false;
var eeSFL_ListID = 1;
var eeSFL_FileID = 0;
var eeSFL_CheckEmail = '';
var eeSFL_FileDateAdded = '';
var eeSFL_FileDateChanged = '';
var eeSFL_OriginalFileNameOnly = ''; // Holder for original file name
var eeSFL_OriginalFileExtension = ''; // Holder for original file extension
var eeSFL_FileNameInput = '';
var eeSFL_SubFolder = ''; 

// File Sanitizer RegEx - These must match the values in ee-class.php as close as possible
const eeSFL_RegEx_Remove = /[^\w\-. \u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+|[~@^:;<>?]+/g;
const eeSFL_RegEx_Replace = /[.\s]+/g;


// File Name Sanitizer
function eeSFL_SanitizeFileName(eeFileName) {
	
	let eeNewFileNameOnly = eeSFL_GetFileNameWithoutExtension(eeFileName);
	eeNewFileNameOnly = eeNewFileNameOnly.replace(eeSFL_RegEx_Replace, '-');
	eeSanitized = eeNewFileNameOnly.replace(eeSFL_RegEx_Remove, '');
	
	if(eeSFL_OriginalFileExtension.length >= 1) {
		return eeSanitized + '.' + eeSFL_OriginalFileExtension; // Reattach original extension
	} else {
		return eeSanitized;
	}
	
}



// Dynamically remove/replace special chars within the new file name input
function eeSFL_SanitizeInputDynamically() {
	
	// console.log('SFL - Running eeSFL_SanitizeInputDynamically()');

	const eeInput = document.getElementById('eeSFL_FileNameNew');

	eeInput.addEventListener('input', function() {
		
		// console.log('Editing: ' + eeInput.value);

		const eeCursorPosition = eeInput.selectionStart;
		const eeInputSaved = eeInput.value;
		let eeNewFileNameOnly = eeSFL_GetFileNameWithoutExtension(eeInput.value);
		let eeNewFileName = '';
		let eeDotCount = (eeInput.value.match(/\./g) || []).length;
		
		// If the name is missing, reset to the original
		if(eeNewFileNameOnly.length < 1) { 
			this.value = eeSFL_OriginalFileNameOnly + '.' + eeSFL_OriginalFileExtension;
			return;
		} // Set in Open Edit Modal

		// Regex
		eeNewFileNameOnly = eeNewFileNameOnly.replace(eeSFL_RegEx_Remove, '');
		eeNewFileNameOnly = eeNewFileNameOnly.replace(eeSFL_RegEx_Replace, '-');

		if(eeSFL_OriginalFileExtension.length >= 1) { // It's a file - Set when edit modal is opened
			
			console.log('--> Editing File: ' + eeSFL_OriginalFileNameOnly + '.' + eeSFL_OriginalFileExtension);
			
			if(eeDotCount === 0) { 
				console.log('Removing the extension is not allowed.');
				this.value = eeSFL_OriginalFileNameOnly + '.' + eeSFL_OriginalFileExtension;
			
			
			} else if(eeNewFileNameOnly.length < 1) {
			
				console.log('Removing the name is not allowed.');
				this.value = eeSFL_OriginalFileNameOnly + '.' + eeSFL_OriginalFileExtension;
			
			
			} else {
				this.value = eeNewFileNameOnly + '.' + eeSFL_OriginalFileExtension;
				console.log('New File Name = ' + eeNewFileNameOnly + '.' + eeSFL_OriginalFileExtension);
			}
			
			

		} else { // It's a folder, so no extension
			
			console.log('Editing Folder: ' + eeSFL_OriginalFileNameOnly);
			if(eeDotCount >= 1) {
				this.value = eeSFL_OriginalFileNameOnly;
			} else {
				this.value = eeNewFileNameOnly;
				console.log('New Folder Name: ' + eeNewFileNameOnly);
			}
			
			
			
			
		}

		// Restore the cursor position
		eeInput.setSelectionRange(eeCursorPosition, eeCursorPosition);

	});
}



// Function to attach the blur event listener to the input
function eeSFL_AttachBlurEventToInput() {
	
	const eeInput = document.getElementById('eeSFL_FileNameNew'); // Adjust the ID as necessary
	const originalFileName = eeInput.value; // Store the original file name when the page loads or when the input is first populated

	eeInput.addEventListener('blur', function() {
		if (this.value.trim() === '' || this.value[0] === '.') {
			// If the file name or whole input is empty, repopulate it with the original name
			this.value = eeSFL_OriginalFileNameOnly + '.' + eeSFL_OriginalFileExtension;
		}
	});
}



// Get the file name only
function eeSFL_GetFileNameWithoutExtension(eeFileName) {
	
	// Split the filename into parts by the dot character
	const eeParts = eeFileName.split('.');
	
	// Remove the last part (extension) if there's more than one part
	if (eeParts.length > 1) {
		eeParts.pop();
	}
	
	// Rejoin the remaining parts. This handles filenames with multiple dots correctly.
	return eeParts.join('.');
}



// Get the file's extension, if there is one.
function eeSFL_GetFileExtension(eeFileName) {
	
	// Split the filename into parts by the dot character
	const eeParts = eeFileName.split('.');
	
	// Return the last part (extension) if there's more than one part, otherwise return an empty string
	return eeParts.length > 1 ? eeParts.pop() : '';
}





// Scroll down to #eeSFL_FileListTop
function eeSFL_ScrollToIt() {
	
	jQuery('html, body').animate({ scrollTop: jQuery('#eeSFL_FileListTop').offset().top }, 1000);
	
	return false;
	
}
