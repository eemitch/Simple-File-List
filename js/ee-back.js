// Simple File List Pro - Copyright 2024
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code are not advised and may not be supported.

var eeBaseShortcode = 'eeSFL';
var eeSFL_FileID = 0;


// var eeAttsObject = new Object();
// var eeOption = '';
// var eeValue = '';
// var eeNewOption = '';
// var eeNewSetTo = '';
// var eeAttsArray = '';
// var eeArray1 = new Array;
// var eeArray2 = new Array;



// Shortcode Builder
function eeShortcodeBuilder(eeNewOption, eeInputType) {
	
	// Get the Value
	if(eeInputType == 'select') {
		
		eeNewSetTo = jQuery('#eeShortcodeBuilder_' + eeNewOption).val();
	
	} else if(eeInputType == 'toggle' && jQuery('#' + eeNewOption + ' b').text() == eesfl_vars['eeShowText']) {
		
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
		    
		    if(eeOption == 'showfolder') {
			    eeOption = 'folder';
		    }
		    
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



// Upon page load completion...
jQuery(document).ready(function() {
	
	// console.log('eeSFL Admin Document Ready');
	
	// Admin side uploader view control
	jQuery('#uploadFilesDiv').hide();
	jQuery('#eeSFL_UploadFilesButtonSwap').hide();
	
	if(eeSFL_ListID > 1) {
		eeBaseShortcode = eeBaseShortcode + ' list=\'' + eeSFL_ListID + '\'';
	}
	
	// Shortcode Snippet Display
	if(eeSFL_SubFolder && eeSFL_SubFolder != '/') { eeBaseShortcode += ' folder=\'' + eeSFL_SubFolder.slice(0, -1) + '\''; }
	
	jQuery('#eeSFL_ShortCode').val('[' + eeBaseShortcode + ']');
	
	
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
			var eeString1 = jQuery(this).text(); // Showing Upload
			var eeString2 = jQuery('#eeSFL_UploadFilesButtonSwap').text(); // Cancel
			jQuery(this).text(eeString2);
			jQuery('#eeSFL_UploadFilesButtonSwap').text(eeString1);
			
		}
	});
	
	
	// Select or deselect all the checkboxes
	jQuery('#eeSFL_SelectAll').on('click', function() {
	   
	   var eeState = jQuery('#eeSFL_SelectAll').prop("checked"); 
	   
	   if(eeState) {
		   jQuery('.eeDeleteFile').prop( "checked", true );
	   } else {
		   jQuery('.eeDeleteFile').prop( "checked", false );  
	   }
	});
	
	
	
	jQuery('#eeSFL_ConfirmDismiss').on('click', function() {  
	
	   console.log('Confirmed');
	   
	   var eeData = {
			'action': 'simplefilelist_confirm',
			'confirm': 'toggle'
		};

		jQuery.post(eesfl_vars.ajaxurl, eeData, function(response) {
			jQuery('.is-dismissible').slideUp();
		});
   });
   
   
   
   
   	// Copy the Shortcode to the clipboard
   	jQuery('#eeCopytoClipboard').on('click', function() {  
	
		var eeShortCode = jQuery('#eeSFL_ShortCode').val();
		jQuery('#eeSFL_ShortCode').focus();
		jQuery('#eeSFL_ShortCode').select();
		document.execCommand('copy');
		
		eeSFL_AlertModal('<h1>' + eesfl_vars['eeCopyShortcodeHeading'] + '</h1><p>' + eesfl_vars['eeCopyShortcodeText'] + '</p>');
    
   	});
	
	
	
	
	// Background Scan Checkbox
	jQuery('#eeUseCache').on('change', function( event ) {
		
		if ( jQuery('#eeUseCache').val() == 'HOUR' || jQuery('#eeUseCache').val() == 'DAY' ) { // Re-enable the checkbox
		
			jQuery('#eeUseCacheCron').removeAttr('disabled');
			
		} else { // Showing
			
			jQuery('#eeUseCacheCron').removeAttr('checked'); // Uncheck
			jQuery('#eeUseCacheCron').attr('disabled', 'disabled'); // Disable it
			
		}
	});
	
	

	jQuery('#eeFooterImportantLink').on('click', function() {
		
		var eeImportant = jQuery('#eeFooterImportant').text();
		
		alert(eeImportant);
	});
	
		


}); // END Ready Function