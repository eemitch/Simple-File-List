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










