/* Simple File List Javascript | Mitchell Bennis | Element Engage, LLC | mitch@elementengage.com */

console.log('eeSFL Frontside Footer JS Loaded');


// Upon page load completion...
jQuery(document).ready(function($) {	

	console.log('eeSFL Document Ready');
	
	// File List Table Sorting
	jQuery('.eeFiles th.eeSFL_Sortable').click(function(){
	    
	    var table = jQuery(this).parents('table').eq(0)
	    var rows = table.find('tr:gt(0)').toArray().sort(eeSFL_comparer(jQuery(this).index()))
	    this.asc = !this.asc
	    if (!this.asc){rows = rows.reverse()}
	    for (var i = 0; i < rows.length; i++){table.append(rows[i])}
	})
	
	function eeSFL_comparer(index) {
	    return function(a, b) {
	        var valA = eeSFL_getCellValue(a, index), valB = eeSFL_getCellValue(b, index)
	        var eeReturn = $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
	        
	        if($.isNumeric(eeReturn)) {
		        eeReturn = eeSFL_GetFileSize(eeReturn, 1024);
	        }
	        
	        return eeReturn;   
	    }
	}
	
	function eeSFL_getCellValue(row, index){
		return jQuery(row).children('td').eq(index).text();
	}
	

}); // END Ready Function








// Triggered when you click the Delete link
function eeSFL_Delete(eeSFL_FileID) {
	
	event.preventDefault(); // Don't follow the link
	
	console.log('Deleting File ID #' + eeSFL_FileID);
	
	// Get the File Name
    var eeSFL_FileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileLink').text();
	
	if( confirm("Confirm Delete...\r\n" + eeSFL_FileName) ) {
	
		eeSFL_FileAction(eeSFL_FileID, 'Delete');
	
	}

}









// Triggered when you click on the Rename link
function eeSFL_Rename(eeSFL_FileID) {
   
   event.preventDefault(); // Don't follow the link
   
   // Cancel previous if needed
   if(jQuery('div.eeSFL_FileRenameEntry').is(':visible')) {
	   eeSFL_CancelRename();
   }
   
   // Get the File Name
   var eeSFL_FileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileLink').text();
   
   console.log('Renaming "' + eeSFL_FileName + '" to something else');
   
   // Hide the <a> tag
   jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileLink').hide();
   
   // Show the Form Instead
   jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' div.eeSFL_FileRenameEntry').show();
}




// AJAX Post to File Engine
function eeSFL_FileAction(eeSFL_FileID, eeSFL_Action) {
	
	event.preventDefault(); // Don't follow link
	
	// console.log(eeSFL_Action + ' -> ' + eeSFL_FileID);
	
	// The File Action Engine
	var eeActionEngine = eeSFL_PluginURL + '/ee-file-engine.php';
	
	
	// AJAX --------------
	
	// Get the Nonce
	var eeSFL_ActionNonce = jQuery('#eeSFL_ActionNonce').text();
	
	if(eeSFL_Action == 'Rename') {
		
		var eeSFL_OldFileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' span.eeOldFileName').text();
		var eeSFL_NewFileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' input.eeNewFileName').val();
	
		var eeFormData = {
			'eeSFL_ID': eeSFL_ListID,
			'eeFilePath': eeSFL_FileListPath + eeSFL_OldFileName,
			'eeFileAction': eeSFL_Action + '|' + eeSFL_FileListPath + eeSFL_NewFileName,
			'eeSecurity': eeSFL_ActionNonce
		};
	
	} else if(eeSFL_Action == 'Delete') {
		
		// Get the File Name
		var eeSFL_FileName = jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileLink').text();
		
		var eeFormData = {
			'eeSFL_ID': eeSFL_ListID,
			'eeFilePath': eeSFL_FileListPath + eeSFL_FileName,
			'eeFileAction': eeSFL_Action,
			'eeSecurity': eeSFL_ActionNonce
		};
	}
	
	if(eeFormData) {

		console.log('Calling: ' + eeActionEngine);

		jQuery.post(eeActionEngine, eeFormData, function(response) {
			
			if(response == 'SUCCESS') {
				
				if(eeSFL_Action == 'Rename') {
					
					jQuery('div.eeSFL_FileRenameEntry').hide();
					
					// Make a New Link
					var eeNewLink = '<a class="eeSFL_FileName" href="' + eeSFL_NewFileName + '">' + eeSFL_NewFileName + '</a>';
					
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileLink').html(eeNewLink);
					jQuery('#eeSFL_RowID-' + eeSFL_FileID + ' p.eeSFL_FileLink').show();
					
				} else if (eeSFL_Action == 'Delete') {
					jQuery('#eeSFL_RowID-' + eeSFL_FileID).hide('slow');
				}
			
			} else {
				alert(response);
			}
		});
	}
}





function eeSFL_CancelRename() {
	event.preventDefault(); // Don't do it
	jQuery('div.eeSFL_FileRenameEntry').hide();
	jQuery('td.eeSFL_FileName p').show();
}




