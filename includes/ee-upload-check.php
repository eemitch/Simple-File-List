<?php // Simple File List Script: ee-list-display.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

// Check for an upload job, then run notification routine.
if(@$_POST['eeSFL_Upload']) {
	
	$eeSFL_Uploaded = TRUE;
	
	eeSFL_FREE_ProcessUpload();
	
	if($eeAdmin) {
		eeSFL_FREE_UploadCompletedAdmin(); // Action Hook: eeSFL_UploadCompletedAdmin  <-- Admin side
	} else {
		eeSFL_FREE_UploadCompleted(); // Action Hook: eeSFL_UploadCompleted <-- Front side
	}	
} else {
	$eeSFL_Uploaded = FALSE;
}


?>