/* Simple File List Javascript | Mitchell Bennis | Element Engage, LLC | mitch@elementengage.com */

console.log('eeSFL Admin JS Loaded');

var eeBaseShortcode = 'eeSFL';

var eeSFL_FileID = 0;

// Shortcode Builder
var eeAttsObject = new Object();
var eeOption = '';
var eeValue = '';
var eeNewOption = '';
var eeNewSetTo = '';
var eeAttsArray = '';
var eeArray1 = new Array;
var eeArray2 = new Array;



jQuery(function() {
   
   // Copy the Shortcode to the clipboard
   jQuery('#eeCopytoClipboard').on('click', function() {  
	
		var eeShortCode = jQuery('#eeSFL_ShortCode').val();
		jQuery('#eeSFL_ShortCode').focus();
		jQuery('#eeSFL_ShortCode').select();
		jQuery('#eeSFL_ShortCode').css('background-color', '#d4ffdd');
		document.execCommand('copy');
    
   });
	
});





// Upon page load completion...
jQuery(document).ready(function() {
	
	console.log('eeSFL Admin Document Ready');
	
	// Admin side uploader view control
	jQuery('#uploadFilesDiv').hide();
	jQuery('#eeSFL_UploadFilesButtonSwap').hide();
	
	jQuery('#eeSFL_ShortCode').val('[' + eeBaseShortcode + ']');
	jQuery('input[name="eeShortcode"]').val('[' + eeBaseShortcode + ']');
	
	jQuery('#eeSFL_UploadFilesButton').on('click', function( event ) { 
		
		event.preventDefault();
		
		if ( jQuery('#uploadFilesDiv').is(':visible') ) { // Canceling
		
			jQuery('#uploadFilesDiv').slideUp();
			var eeString1 = jQuery(this).text(); // Showing Cancel
			var eeString2 = jQuery('#eeSFL_UploadFilesButtonSwap').text();
			jQuery(this).text(eeString2);
			jQuery('#eeSFL_UploadFilesButtonSwap').text(eeString1);
			
		} else { // Showing
			
			jQuery('#uploadFilesDiv').slideDown();
			jQuery('.is-dismissible').slideUp();
			var eeString1 = jQuery(this).text(); // Showing Upload
			var eeString2 = jQuery('#eeSFL_UploadFilesButtonSwap').text(); // Cancel
			jQuery(this).text(eeString2);
			jQuery('#eeSFL_UploadFilesButtonSwap').text(eeString1);
			
		}
	});
	
	
	
	jQuery('#eeSFL_ReScanButton').on('click', function( event ) { 
		
		event.preventDefault();
		
		console.log('Re-scanning...');
		
		let eeSFL_ThisURL = document.location;
		
		document.location = eeSFL_ThisURL + '&eeSFL_Scan=true'; 
	});
	
	

	// Admin side file deletion
	// jQuery('.eeDeleteCheckedButton').hide();
	
	
	

	jQuery('#eeFooterImportantLink').on('click', function() {
		
		var eeImportant = jQuery('#eeFooterImportant').text();
		
		alert(eeImportant);
	});	
	
		


}); // END Ready Function