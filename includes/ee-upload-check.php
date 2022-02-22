<?php  // Simple File List Script: ee-upload-form.php | Author: Mitchell Bennis | support@simplefilelist.com

// Security	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails


// Check for an upload job, then run notification routine.
if(isset($_POST['eeSFL_Upload'])) {
	
	$eeSFL_BASE_Log['RunTime'][] = 'Processing Upload Job...';
	
	$eeSFL_Uploaded = TRUE; // Show the results page
	
	eeSFL_BASE_ProcessUpload();
	
	if($eeAdmin) {
		eeSFL_BASE_UploadCompletedAdmin(); // Action Hook: eeSFL_UploadCompletedAdmin  <-- Admin side
	} else {
		eeSFL_BASE_UploadCompleted(); // Action Hook: eeSFL_UploadCompleted <-- Front side
	}
	
} else {
	$eeSFL_Uploaded = FALSE; // Show the regular list
}


?>