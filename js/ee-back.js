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



// Shortcode Builder
function eeSFL_FREE_ShortcodeBuilder(eeNewOption, eeInputType) {
	
	// Get the Value
	if(eeInputType == 'select') {
		
		eeNewSetTo = jQuery('#eeShortcodeBuilder_' + eeNewOption).val();
	
	} else if(eeInputType == 'toggle' && jQuery('#' + eeNewOption + ' b').text() == eesfl_vars['eeShowText'] ) {
		
		eeNewSetTo = 'YES';
		
	} else if(eeInputType == 'toggle' && jQuery('#' + eeNewOption + ' b').text() == eesfl_vars['eeHideText']) {
		
		eeNewSetTo = 'NO';
	}
	
	console.log('Input Type: ' + eeInputType);
	console.log('eeNewOption: ' + eeNewOption);
	console.log('eeNewSetTo: ' + eeNewSetTo);
	
	
	
	// Get the existing shortcode string
	var eeShortcode = jQuery('#eeSFL_ShortCode').val();
	console.log('Shortcode Before: ' + eeShortcode);
	
	// Get Current Atts
	var eeShortcode = eeShortcode.replace('[' + eeBaseShortcode, ''); // Trim down to just the atts
	var eeShortcodeAtts = eeShortcode.replace(']', '').trim();
	
	// Are there any?
	if(eeShortcodeAtts.length) {
		console.log('eeShortcodeAtts: ' + eeShortcodeAtts);
		eeAttsArray = eeShortcodeAtts.split(' '); // Put existing atts in the array
	}
	
	// Are there existing atts?
	if(eeAttsArray.length) {
		
		console.log('eeShortcodeAtts: ' + eeShortcodeAtts);
		
		eeArray1 = eeShortcodeAtts.split(' '); // Put existing atts in the array
		
		for (var eeKey in eeArray1) {
			
			console.log('eeKey: ' + eeKey);
		    
		    if (eeArray1.hasOwnProperty(eeKey)) {
		         
		         eeValue = eeArray1[eeKey];
		         
		         eeValue = eeValue.replace('"', ''); // Strip quotes
		         eeValue = eeValue.replace('"', ''); // Strip quotes
		         
		         console.log('eeValue: ' + eeValue);
		         
		         if(eeValue.length) {
			         
			         eeArray2 = eeValue.split('=');
			         
			         eeOption = eeArray2[0];
			         eeSetTo = eeArray2[1];
			         
			         console.log('eeOption: ' + eeOption );
			         console.log('eeSetTo: ' + eeSetTo );
			         
			         eeAttsObject[eeOption] = eeSetTo;
			         
			         eeOption = '';
			         eeSetTo = '';
		         }      
		    }
		}
		
		console.log(eeAttsObject);	
	}
	
	
	
	
	// See if new option is already in the shortcode
	var eeThisOption = eeAttsObject[eeNewOption];
	
	if(eeThisOption) {
		
		console.log('Existing Shortcode Argument');
		// console.log('eeThisOption Now: ' + eeThisOption);
		
		eeAttsObject[eeNewOption] = eeNewSetTo;
		
	} else {
		console.log('New Shortcode Argument');
		
		eeAttsObject[eeNewOption] = eeNewSetTo;
	}
	
	console.log('New Option: ' + eeNewOption);
	console.log('New Setting: ' + eeNewSetTo);
	
	
	// Update the Form Appearance
	console.log('Updating the Display');
	
	
	if(jQuery('#' + eeNewOption + ' b').text() == eesfl_vars['eeShowText']) {
		
		jQuery('#' + eeNewOption + ' b').text(eesfl_vars['eeHideText']);
	
	} else {
		
		jQuery('#' + eeNewOption + ' b').text(eesfl_vars['eeShowText']);
	}
	
	if(jQuery('#' + eeNewOption).hasClass('eeOn')) {
	}
	
	
	// Does this att match our default setting?
	var eeSettingDefault = eeSFL_DefaultSettings[ eeNewOption.toLowerCase() ];
	
	
	console.log('Setting Default: ' + eeSettingDefault);
	
	if(eeNewSetTo == eeSettingDefault || eeNewSetTo == 'remove') { // Remove the att
		
		delete eeAttsObject[eeNewOption];
		
		jQuery('#' + eeNewOption).removeClass('eeOn');
		
		console.log('Choice Matches Default. Removing...');
	
	} else {
		jQuery('#' + eeNewOption).addClass('eeOn');
	}
	
	
	
	
	// Build the new shortcode
	var eeNewShortcode = '[' + eeBaseShortcode;
	
	// Build Atts String
	for (var eeProperty in eeAttsObject) {
	    
	    if (eeAttsObject.hasOwnProperty(eeProperty)) {
		    
		    eeOption = eeProperty;
			
			console.log('eeOption: ' + eeOption);
		    
		    eeSetTo = eeAttsObject[eeOption];
		    
		    console.log('eeSetTo: ' + eeSetTo);
		    
		    // Build string
		    eeNewShortcode += ' ' + eeOption + '="' + eeSetTo + '"';
		}
	
	}
	
	eeNewShortcode += ']'; // Complete
	
	eeNewShortcode = eeNewShortcode.replace(/\s{2,}/g, ' '); // Remove double spaces
	
	// New Shortcode
	console.log('Shortcode After: ' + eeNewShortcode);
	
	// Set Box Value
	jQuery('#eeSFL_ShortCode').val(eeNewShortcode);
	
	// Update the Hidden Input
	jQuery('input[name="eeShortcode"]').val(eeNewShortcode);
	
} // End Shortcode Builder








jQuery(function() {
   
   // Copy the Shortcode to the clipboard
   jQuery('#eeCopytoClipboard').click(function() {  
	
	var eeShortCode = jQuery('#eeSFL_ShortCode').val();
    jQuery('#eeSFL_ShortCode').focus();
    jQuery('#eeSFL_ShortCode').select();
    document.execCommand('copy');
    
   });
   
   
   
   // Select or deselect all the checkboxes
   jQuery('#eeSFL_SelectAll').click(function() {
	   
	   var eeState = jQuery('#eeSFL_SelectAll').prop("checked"); 
	   
	   if(eeState) {
		   jQuery('.eeDeleteFile').prop( "checked", true );
	   } else {
		   jQuery('.eeDeleteFile').prop( "checked", false );  
	   }
	});
	
	
	// Show or Hide the Delete Files Button
	jQuery('input[type=checkbox]').click(function( event ) { 
		
		jQuery('.eeDeleteCheckedButton').fadeIn();
		
	});
	
});





// Upon page load completion...
jQuery(document).ready(function() {
	
	console.log('eeSFL Admin Document Ready');
	
	// Admin side uploader view control
	jQuery('#uploadFilesDiv').hide();
	
	if(eeSFL_ID > 1) {
		eeBaseShortcode = eeBaseShortcode + ' list="' + eeSFL_ID + '"';
	}
	
	jQuery('#eeSFL_ShortCode').val('[' + eeBaseShortcode + ']');
	jQuery('input[name="eeShortcode"]').val('[' + eeBaseShortcode + ']');
	
	jQuery('#eeSFL_UploadFilesButton').click(function( event ) { 
		
		event.preventDefault();
		
		if ( jQuery('#uploadFilesDiv').is(':visible') ) { // Canceling
		
			jQuery('#uploadFilesDiv').slideUp();
			var eeString1 = jQuery(this).text(); // Showing Cancel
			var eeString2 = jQuery('#eeSFL_UploadFilesButtonSwap').text();
			jQuery(this).text(eeString2);
			jQuery('#eeSFL_UploadFilesButtonSwap').text(eeString1);
			
		} else { // Showing
			
			jQuery('#uploadFilesDiv').slideDown();
			var eeString1 = jQuery(this).text(); // Showing Upload
			var eeString2 = jQuery('#eeSFL_UploadFilesButtonSwap').text(); // Cancel
			jQuery(this).text(eeString2);
			jQuery('#eeSFL_UploadFilesButtonSwap').text(eeString1);
			
		}
	});
	
	
	
	jQuery('#eeSFL_ReScanButton').click(function( event ) { 
		
		event.preventDefault();
		
		console.log('Re-scanning...');
		
		let eeSFL_ThisURL = document.location;
		
		document.location = eeSFL_ThisURL + '&eeSFL_Scan=true'; 
	});
	
	

	// Admin side file deletion
	jQuery('.eeDeleteCheckedButton').hide();
	
	
	

	jQuery('#eeFooterImportantLink').click(function() {
		
		var eeImportant = jQuery('#eeFooterImportant').text();
		
		alert(eeImportant);
	});
	
	
	
	// List Settings
	jQuery('#eeExpireTime').change(function( event ) {
	
		let eeNum = jQuery('#eeExpireTime').val();
		
		if(eeNum === 0) {
			eeNum = 'Refresh each time';
		}
		
		jQuery('#eeExpireTimeValue').html( eeNum );
	
	});	
	
		


}); // END Ready Function