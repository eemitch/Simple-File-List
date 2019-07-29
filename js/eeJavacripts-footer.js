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


