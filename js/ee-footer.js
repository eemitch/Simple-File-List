// Simple File List Script: ee-footer.js | Author: Mitchell Bennis | support@simplefilelist.com

// Used in front-side and back-side file list display

// Upon page load completion...
jQuery(document).ready(function($) {	

	console.log('eeSFL Document Ready');
	
	window.addEventListener('touchstart', function() {
		eeSFL_BASE_isTouchscreen = true;
	});
	
	
	jQuery('#eeSFL_Modal_Manage_Close').on('click', function() {
		jQuery('#eeSFL_Modal_Manage').hide();
	});	
	
	
	
	
	
	
	
	
	// Look for Media Files and Add Player
	jQuery( '.eeFiles tr' ).each(function( index ) {
		
		// Get the name of this row's ID
		var eeThisID = '#' + jQuery(this).attr('id'); // TO DO - Change id to class to class in SFL 5/6
		var eeFileName = jQuery( eeThisID + " span.eeSFL_RealFileName" ).text(); // Get the File Name
		var eeFileLink = jQuery( eeThisID + " a.eeSFL_FileName" ).attr('href'); // Get the File Link
		var eeExt = eeFileName.split('.').pop(); // Get the File Extension
		console.log( index + ": " + eeFileName );
		
		var eeAudioPlayer = '<audio controls><source src="' + eeFileLink + '" type="audio/mpeg">Not Supported</audio>';
		
		if(eeExt == 'mp3') {
			console.log('MP3 File Found: ' + eeFileName);
			console.log('Updating the Display...');
			
			// Change the Open Link to Play
			jQuery(eeThisID + ' .eeSFL_FileOpen').text('Play');
			
			jQuery( eeThisID + " td.eeSFL_FileName" ).append('<div>' + eeAudioPlayer +  '</div>'); // TO DO - Update this for SFL 5/6
			
			
		}
		
		
		
		
		
		
	});
	
		

}); // END Ready Function




// SFL FUNCTIONS ---------------------------

// Strip Slashes
String.prototype.eeStripSlashes = function(){
    return this.replace(/\\(.)/mg, "$1");
}


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










