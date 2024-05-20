// Simple File List Pro - Copyright 2024
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code are not advised and may not be supported.

// Used in front-side and back-side file list display

console.log('ee-footer.js Loaded');

// Upon page load completion...
jQuery(document).ready(function() {
	
	// For Mobile Devices
	window.addEventListener('touchstart', function() {
		eeSFL_isTouchscreen = true;
	});
	
	// Dynamic File Name Sanitizer
	eeSFL_SanitizeInputDynamically();
	
	jQuery('.eeSFL_ModalClose').on('click', function() {
		jQuery('.eeSFL_Modal').hide();
	});
	
	// Look for Media Files and Add Player
	jQuery( '.eeSFL_Item' ).each(function( index ) {
		
		// Get the name of this row's ID
		var eeSFL_ThisID = jQuery(this).attr('id');
		
		if (eeSFL_ThisID !== undefined) { // Like in the header row
			
			var eeSFL_FileMIME = jQuery('#' + eeSFL_ThisID + " .eeSFL_FileMimeType" ).text(); // Get the File MIME Type
			
			if(eeSFL_FileMIME) {
			
				// Get File Info
				var eeSFL_ID = eeSFL_ThisID.replace( /^\D+/g, ''); // Get just the number
				var eeSFL_FileName = jQuery('#' + eeSFL_ThisID + " span.eeSFL_RealFileName" ).text(); // Get the File Name
				var eeSFL_FileLink = jQuery('#' + eeSFL_ThisID + " a.eeSFL_FileName" ).attr('href'); // Get the File Link
				var eeSFL_Ext = eeSFL_FileName.split('.'); // Get the File Extension
				
				// Detect Type
				var eeSFL_MediaType = eeSFL_FileMIME.split('/');
				var eeSFL_Player = eeSFL_MediaType[0].toUpperCase(); // audio or video
				
				// Setup for Playback
				if(eeSFL_Player == 'AUDIO' || eeSFL_Player == 'VIDEO') {
				
					// console.log(eeSFL_FileMIME + ' Media File Found: ' + eeSFL_FileName);
					
					// Change "Open" to "Play"
					jQuery('#' + eeSFL_ThisID + ' a.eeSFL_FileOpen').text(eesfl_vars.eePlayLabel); 
					jQuery('#' + eeSFL_ThisID + ' a.eeSFL_FileOpen').removeAttr('target');
					jQuery('#' + eeSFL_ThisID + ' a.eeSFL_FileOpen').addClass('eeSFL_Play' + eeSFL_Player);
					jQuery('#' + eeSFL_ThisID + ' a.eeSFL_FileOpen').attr('data-ee-id', eeSFL_ID);
					
					jQuery('#' + eeSFL_ThisID + ' a.eeSFL_FileName').addClass('eeSFL_Play' + eeSFL_Player); 
					jQuery('#' + eeSFL_ThisID + ' a.eeSFL_FileName').removeAttr('target');
					jQuery('#' + eeSFL_ThisID + ' a.eeSFL_FileName').attr('data-ee-id', eeSFL_ID);
					
					jQuery('#' + eeSFL_ThisID + ' .eeSFL_Thumbnail a').addClass('eeSFL_Play' + eeSFL_Player); 
					jQuery('#' + eeSFL_ThisID + ' .eeSFL_Thumbnail a').removeAttr('target');
					jQuery('#' + eeSFL_ThisID + ' .eeSFL_Thumbnail a').attr('data-ee-id', eeSFL_ID);
					
					
					
					// Inline Audio Player
					if(eeSFL_Player == 'AUDIO' && eesfl_vars.eeAudioEnabled == 'YES') {
								
						
						// Thumb, Name or Play, Go Ahead and Play
						jQuery('.eeSFL_PlayAUDIO').on('click', function() {
							
							event.preventDefault();
				
							var eeSFL_ThisID = jQuery(this).attr('data-ee-id');
							
							if(eeSFL_ThisID !== undefined) {
							
								document.getElementById('eeSFL_AudioPlayer' + eeSFL_ThisID).play();
							}
							
						});
						
						
						// Build and Add the Player
						var eeSFL_AudioPlayer = '<audio controls id="eeSFL_AudioPlayer' + eeSFL_ID + '" class="eeSFL_AudioPlayer" style="';
						
						if(eesfl_vars.eeAudioHeight >= 1) { eeSFL_AudioPlayer += 'height:' + eesfl_vars.eeAudioHeight + 'px;'; }
						
						eeSFL_AudioPlayer += '"><source src="' + eeSFL_FileLink + '" type="' + eeSFL_FileMIME + '">Not Supported</audio>';
						
						if(eeSFL_ShowListStyle == 'TABLE') {
							
							jQuery('#' + eeSFL_ThisID + " td.eeSFL_FileName" ).append('<div class="eeSFL_AudioPlayerWrap">' + eeSFL_AudioPlayer +  '</div>');
							
						} else if(eeSFL_ShowListStyle == 'TILES') {
							
							jQuery('#' + eeSFL_ThisID + " .eeSFL_FileDesc" ).append('<div class="eeSFL_AudioPlayerWrap">' + eeSFL_AudioPlayer +  '</div>');
							
						} else if(eeSFL_ShowListStyle == 'FLEX') {
							
							jQuery('#' + eeSFL_ThisID + " .eeSFL_FileLink" ).append('<div class="eeSFL_AudioPlayerWrap">' + eeSFL_AudioPlayer +  '</div>');
							
						}
					}
				}
			}
		}
	});
	
	
	// Thumb, Name or Play, Go Ahead and Play
	jQuery('.eeSFL_PlayAUDIO').on('click', function() {
		
		event.preventDefault();
	
		var eeSFL_ThisID = jQuery(this).attr('data-ee-id');
		
		if(eeSFL_ThisID !== undefined) {
		
			document.getElementById('eeSFL_AudioPlayer' + eeSFL_ThisID).play();
		}
		
	});
	
	
	// VIDEO
	// Produce the Video Player
	jQuery('.eeSFL_PlayVIDEO').on('click', function() {
			
		event.preventDefault();
		
		var eeSFL_ThisID = jQuery(this).attr('data-ee-id');
		var eeSFL_ThisURL = jQuery(this).attr('href');
		var eeSFL_FileMIME = jQuery("eeSFL_FileID-" + eeSFL_ThisID + " .eeSFL_FileMimeType").text();
		
		if(eeSFL_ThisID !== undefined) {
		
			// alert('PLAY');
			
			var eeSFL_VideoPlayer = '<div class="eeSFL_Modal" id="eeSFL_Video"><div class="eeSFL_ModalBackground"></div><div class="eeSFL_ModalBody">';
			
			eeSFL_VideoPlayer += '<button class="eeSFL_ModalClose">&times;</button>';
			
			eeSFL_VideoPlayer += '<video id="eeSFL_VideoPlayer" autoplay controls><source src="' + eeSFL_ThisURL + '" type="' + eeSFL_FileMIME + '">' + eesfl_vars.eeBrowserWarning + '</video>';
			
			eeSFL_VideoPlayer += '</div></div>';
			
			eeSFL_VideoPlayer += '<script>';
			eeSFL_VideoPlayer += "jQuery('.eeSFL_ModalClose').on('click', function() {  jQuery('#eeSFL_Video').trigger('pause');jQuery('#eeSFL_Video').remove(); });";
			eeSFL_VideoPlayer += "jQuery('.eeSFL_Modal').on('click', function() { jQuery('#eeSFL_Video').trigger('pause'); jQuery('#eeSFL_Video').remove(); });";
			
			eeSFL_VideoPlayer += '';
			eeSFL_VideoPlayer += '</script>';
			
			
			jQuery('.eeSFL').append(eeSFL_VideoPlayer);
	
			jQuery('#eeSFL_Video').show();
			
		}
		
	});

}); // END Ready Function


// Strip Slashes
String.prototype.eeSFL_StripSlashes = function(){
    return this.replace(/\\(.)/mg, "$1");
}


// Copy File URL to Clipboard
function eeSFL_CopyLinkToClipboard(eeSFL_FileURL) {
	
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





function eeSFL_EditFile(eeSFL_FileID) {
	
	event.preventDefault(); // Don't follow the link
	
	if( jQuery('#eeSFL_EditFileWrap_' + eeSFL_FileID).is(':visible') ) {
		
		jQuery('#eeSFL_EditFileWrap_' + eeSFL_FileID).slideUp();
		jQuery('#eeSFL_EditFile_' + eeSFL_FileID).text(eesfl_vars['eeEditText']);
	
	} else {
		
		jQuery('#eeSFL_EditFileWrap_' + eeSFL_FileID).slideDown();
		jQuery('#eeSFL_EditFile_' + eeSFL_FileID).text(eesfl_vars['eeCancelText']);
	}
}




// Triggered when you click the Delete link
function eeSFL_Delete(eeSFL_FileID) {
	
	event.preventDefault(); // Don't follow the link
	
	console.log('Deleting File ID #' + eeSFL_FileID);
	
	// Get the File Name
    var eeSFL_FileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' .eeSFL_RealFileName').text();
    
    console.log(eeSFL_FileName);
	
	if( confirm( eesfl_vars['eeConfirmDeleteText'] + "\r\n\r\n" + eeSFL_FileName ) ) {
	
		eeSFL_FileAction(eeSFL_FileID, 'Delete');
	
	}

}



function eeSFL_ValidateEmail(eeSFL_CheckEmail) {

	var eeSFL_EmailFormat = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	
	if (eeSFL_CheckEmail.match(eeSFL_EmailFormat)) {
    	return 'GOOD';
  	} else {
	  	return "BAD";
  	}
}



// File Size Formatting
function eeSFL_GetFileSize(bytes, si) {
    
    var thresh = si ? 1000 : 1024;
    
    if(Math.abs(bytes) < thresh) {
        return bytes + ' B';
    }
    
    var units = si
        ? ['kB','MB','GB','TB','PB','EB','ZB','YB']
        : ['KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];
    var u = -1;
    
    do {
        bytes /= thresh;
        ++u;
    } while(Math.abs(bytes) >= thresh && u < units.length - 1);
    
    return bytes.toFixed(1)+' '+units[u];
}
