/* Simple File List Javascript | Mitchell Bennis | Element Engage, LLC | mitch@elementengage.com */

// console.log('eeSFL Frontside Head JS Loaded');

// var eeSFL_FileFormats = '';
var eeSFL_isTouchscreen = false;
var eeSFL_FileID = false;
var eeSFL_ID = 1;
var eeSFL_CheckEmail = false;
var eeSFL_FileFormats = 'jpg, jpeg';



// Confirm or cancel folder delete
function eeSFLF_ConfirmFolderDelete() {
    
    var response = confirm("Are You Sure?\nAll Contents Will Be Deleted");
	
	if (response != true) {
	    
	    // Do No-thing, uuuu NOTHING!
	    event.preventDefault(); 
	}
}



// Open the Move Display
function eeSFLF_MoveFileDisplay(eeSFL_FileID) {
   
   event.preventDefault(); // Don't follow the link
   
   console.log('Showing file-move display for: ' + eeSFL_FileID);
   
   if(jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFLF_moveFileDisplay').is(':visible')) {
	   jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFLF_moveFileDisplay').slideUp();
	   jQuery('#eeSFLF_moveLink_' + eeSFL_FileID).text('Move');
   } else {
	   jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFLF_moveFileDisplay').slideDown();
	   jQuery('#eeSFLF_moveLink_' + eeSFL_FileID).text('Cancel Move');
	   jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFLF_moveFileDisplay button').attr('onclick', 'eeSFLF_MoveThisFile(' + eeSFL_FileID + ')');
   }   
}



// AJAX Post to File Moving Engine
function eeSFLF_MoveThisFile(eeSFL_FileID) {
	
	event.preventDefault(); // Don't submit
	
	// The Move Engine
	var eeSFLF_MoveEngine = eesfl_vars.ajaxurl;
	
	console.log('Calling: ' + eeSFLF_MoveEngine);
	
	// The List ID
	// var eeID = jQuery('#eeSFL_ID').val();
	
	// The File Name
	var eeFileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' span.eeSFL_RealFileName').text(); // Current File Name
	
	// Where are we going?
	var eeSFLF_fileDestination = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' select').val();
	
	// Where are we now?
	var eeSFLF_ListFolder = jQuery('#eeSFLF_ListFolder').text() + '/';
	
	// Get the Nonce
	var eeSFLF_MoveNonce = jQuery('#eeSFLF_MoveNonce').text();
	
	if(eeSFLF_fileDestination) {

		console.log('Moving File: ' + eeSFL_FileID + ' to ' + eeSFLF_fileDestination);
		
		var eeSFLF_FormData = {
			'eeID': eeSFL_ListID,
			'eeMoveFile': eeSFL_FileListDir + eeSFLF_ListFolder + eeFileName,
			'eeMoveTo': eeSFL_FileListDir + eeSFLF_fileDestination + eeFileName,
			'eeSecurity': eeSFLF_MoveNonce,
			'action': 'simplefilelistpro_move_job'
		};

		jQuery.post(eeSFLF_MoveEngine, eeSFLF_FormData, function(response) {
			if(response == 'SUCCESS') {
				jQuery('#eeSFL_RowID-' + eeSFL_FileID).hide('slow');
			} else {
				alert(response);
			}
		});
		
	
	} else {
		alert('Please Choose a Folder');
	}

}




function eeSFL_ValidateEmail(eeSFL_CheckEmail) {

	var eeSFL_EmailFormat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
	
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