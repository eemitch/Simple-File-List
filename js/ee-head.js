/* Simple File List Javascript | Mitchell Bennis | Element Engage, LLC | mitch@elementengage.com */

// console.log('eeSFL Frontside Head JS Loaded');

var eeSFL_isTouchscreen = false;
var eeSFL_FileID = false;
var eeSFL_ID = 1;
var eeSFL_CheckEmail = false;
var eeSFL_FileFormats = 'jpg,jpeg';

function eeSFL_FREE_ValidateEmail(eeSFL_CheckEmail) {

	var eeSFL_EmailFormat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
	
	if (eeSFL_CheckEmail.match(eeSFL_EmailFormat)) {
    	return 'GOOD';
  	} else {
	  	return "BAD";
  	}
}



function eeSFL_FREE_ScrollToIt() {
	
	jQuery('html, body').animate({ scrollTop: jQuery('.eeSFL').offset().top }, 1000);
	
	return false;
	
}


// File Size Formatting
function eeSFL_FREE_GetFileSize(bytes, si) {
    
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