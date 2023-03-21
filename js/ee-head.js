/* Simple File List Javascript | Mitchell Bennis | Element Engage, LLC | mitch@elementengage.com */

var eeSFL_isTouchscreen = false;
var eeSFL_ListID = 1;
var eeSFL_FileID = false;
var eeSFL_CheckEmail = false;
var eeSFL_FileDateAdded = false;
var eeSFL_FileDateChanged = false;

function eeSFL_ScrollToIt() {
	
	jQuery('html, body').animate({ scrollTop: jQuery('#eeSFL_FileListTop').offset().top }, 1000);
	
	return false;
	
}