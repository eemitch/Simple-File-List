<?php  // Simple File List Script: ee-upload-form.php | Author: Mitchell Bennis | support@simplefilelist.com

// Security	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_Uploaded = FALSE; // Show Confirmation

// Check for an upload job, then run notification routine.
if(isset($_POST['eeSFL_Upload'])) {
	
	$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = 'Processing Upload Job...';
	
	eeSFL_BASE_ProcessUpload();
	
	if( is_admin() ) {
		eeSFL_BASE_UploadCompletedAdmin(); // Action Hook: eeSFL_UploadCompletedAdmin  <-- Admin side
	} else {
		eeSFL_BASE_UploadCompleted(); // Action Hook: eeSFL_UploadCompleted <-- Front side
	}
	
	if($eeSFL_BASE->eeListSettings['UploadConfirm'] == 'YES' OR is_admin() ) { $eeSFL_Uploaded = TRUE; }
	
}


?>