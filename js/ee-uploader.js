// Simple File List - Multi-File Uploader - mitch@elementengage.com

// alert(eeSFL_FileFormats);

console.log("Upload JS Loaded - ver 3.0");

// var eeUploadFiles = document.querySelector("#eeSFL_FileInput");
// var eeSFL_Files = "";
var eeSFL_FileSet = new Array(); // Names
var eeSFL_FileObjects = new Array(); // File objects
var eeSFL_FileCount = 0; // How many to upload
var eeSFL_Uploaded = 0; // How many have uploaded
var eeSFL_Error = false; // Bad things have happened
var eeSFL_FormatsArray = eeSFL_FileFormats.split(","); // An array of the things.

jQuery(document).ready(function() {

	jQuery( "#eeSFL_UploadingNow" ).hide(); // Hide the spinner
	
	// File Queue Information
	document.getElementById("eeSFL_FileInput").addEventListener("change", function(){
	    
	    console.log("File Added");
	    
	    if(this.files.length > eeSFL_FileLimit) {
		    
		    alert(this.files.length + " files selected. The maximum allowed is " + eeSFL_FileLimit);
		    
		    eeSFL_Error = false;
		    eeSFL_File = false;
		    jQuery("#eeSFL_FileInput").val("");
		    return false;
		       
		}
	    
	    for(var i = 0; i < this.files.length; i++){
	        
	        var eeSFL_File =  this.files[i];
	        
	        console.group("File # " + i);
	        console.log("Name: " + eeSFL_File.name);
	        
	        // Validation
	        
	        // Size
	        console.log("Size: " + eeSFL_File.size);
	        
	        if(eeSFL_File.size > eeSFL_UploadMaxFileSize) {
		        eeSFL_Error = eeSFL_File.name + " is too large to upload.";
	        }
	        
	        // Type
	        var eeSFL_Extension = eeSFL_File.name.split(".").pop();
	        eeSFL_Extension = eeSFL_Extension.toLowerCase();
	        
	        if(eeSFL_FormatsArray.indexOf(eeSFL_Extension) == -1) {
		        eeSFL_Error = "This file type (" + eeSFL_Extension + ") is not allowed.";
	        }
	        
	        console.log("Extension: " + eeSFL_Extension);
	        console.log("Type: " + eeSFL_File.type);
	        
	        // Modified date
	        // console.log("Date: " + eeSFL_File.lastModified);
	        
	        console.groupEnd();
	        
	        if(!eeSFL_Error) { // If no errors
	        	
				eeSFL_FileObjects.push(eeSFL_File); // Add object
				eeSFL_FileSet.push(eeSFL_File.name); // Add name   
				
	        } else {
		        
		        alert(eeSFL_Error); // Alert the user.
		        
		        eeSFL_Error = false;
		        eeSFL_File = false;
		        jQuery("#eeSFL_FileInput").val("");
		        return false;
	        }
	        
	    }
	    
	    eeSFL_FileCount = eeSFL_FileObjects.length; // Reset based on set
	    var eeSFL_FileQstring = JSON.stringify(eeSFL_FileSet);
	            
        jQuery("#eeSFL_FileList").val(eeSFL_FileQstring); // Set the hidden inputs
		jQuery("#eeSFL_FileCount").val(eeSFL_FileCount); // The number of files
        
        console.log("#eeSFL_FileList  Set: " + eeSFL_FileQstring);
		console.log("#eeSFL_FileCount Set: " + eeSFL_FileCount);
	        
	    console.log("Files: " + eeSFL_FileSet);
	    console.log("Count: " + eeSFL_FileCount);
	    
	}, false);

}); // END Ready Function





// The Upload Queue Processor
function eeUploadProcessor(eeSFL_FileObjects) {
	
	eeSFL_FileCount = eeSFL_FileObjects.length;
	
	if(eeSFL_FileCount) {
		
		// Remove button and replace with spinner
	    jQuery("#eeSFL_UploadGo" ).fadeOut( function(){ jQuery( "#eeSFL_UploadingNow" ).fadeIn(); } );
		// jQuery( "#eeUploadingNow" ).fadeIn();
	
		console.log("Uploading " + eeSFL_FileCount + " files...");
		
		for (var i = 0; i < eeSFL_FileCount; i++) { // Loop through and upload the files
			
			console.log("Processing File: " + eeSFL_FileObjects[i].name);
						            
            eeUploadFile(eeSFL_FileObjects[i]); // Upload the file using the function below...
		}
	}		
}




// File Upload AJAX Call
function eeUploadFile(eeSFL_File) { // Pass in file object
    
    var eeXhr = new XMLHttpRequest();
    
    if(eeXhr.upload) { // Upload progress
	    
	    console.log('Upload in progress ...');
	    
	    eeXhr.upload.addEventListener("progress", function(e) {
		    
			var percent = parseInt(100 - (e.loaded / e.total * 100)); // Percent remaining
			console.log('Upload Progress: ' + percent + "%" );
			
			// Progress Bar
			alert('STOP - Upload Progress Display');
			
			
			
		}, false);
	}
	
	
	
	
	
	
    
    var eeFormData = new FormData();
    
    console.log("Uploading: " + eeSFL_File.name);
    console.log("Calling Engine: " + eeSFL_UploadEngineURL);
    
    eeXhr.open("POST", eeSFL_UploadEngineURL, true); // URL set in ee-upload-form.php
    
    eeXhr.onreadystatechange = function() {
        
        if (eeXhr.readyState == 4) { // && eeXhr.status == 200 <-- Windows returns 404?
        
        	eeSFL_Uploaded ++;
            
            console.log("File Uploaded (" + eeSFL_Uploaded + " of " + eeSFL_FileCount + ")");
            
			// Every thing ok, file uploaded
            console.log("RESPONSE: " + eeXhr.responseText); // handle response.
            
            // Submit the Form
            if(eeSFL_Uploaded == eeSFL_FileCount) {
	            
	            if(eeXhr.responseText == "SUCCESS") {
	            
	            	console.log("--->>> SUBMITTING FORM ...");
	            	
	            	document.forms.eeSFL_UploadForm.submit(); // SUCCESS - Process the Form <<<----- FORM SUBMIT
					
		        } else {
			    	console.log("XHR Status: " + eeXhr.status);
			    	console.log("XHR State: " + eeXhr.readyState);
			    	
			    	var n = eeXhr.responseText.search("<"); // Error condition
			    	if(n === 0) {
				    	alert("Upload Error: " + eeSFL_File.name);
				    	jQuery( "#eeUploadingNow" ).fadeOut();
				    }
				    return false;
		        }
	        }
        
        } else {
	    	console.log("XHR Status: " + eeXhr.status);
	    	console.log("XHR State: " + eeXhr.readyState);
	    	return false;
        }
    };
    
    // Pass to the Upload Engine...
    eeFormData.append("file", eeSFL_File);
    
    // These values are set in ee-upload-form.php
    eeFormData.append("eeSFL_ID", eeSFL_ListID);
    eeFormData.append("eeSFL_FileListDir", eeSFL_FileListDir);
    eeFormData.append("eeSFL_Timestamp", eeSFL_TimeStamp); 
    eeFormData.append("eeSFL_Token", eeSFL_TimeStampMD5);
        
    // Send the AJAX request...
    eeXhr.send(eeFormData);
}


console.log("Waiting for files...");

// Populate the action attribute in the form
var eeSFL_CurrentURL = document.location.href;
jQuery("#eeSFL_UploadForm").attr("action", eeSFL_CurrentURL);


