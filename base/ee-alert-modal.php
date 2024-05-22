<?php  
// Simple File List - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
defined( 'ABSPATH' ) or die( 'No direct access is allowed' );
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

$eeOutput .= '<!-- SFL Alert Modals -->
<span class="eeHide" id="eeSFL_Modal_FileID"></span>
<span class="eeHide" id="eeSFL_AlertNonce">' . wp_create_nonce(eeSFL_Nonce) . '</span>

<div class="eeSFL_Modal" id="eeSFL_AlertModal">
<div class="eeSFL_ModalBackground"></div>
<div class="eeSFL_ModalBody eeSFL_AlertModalBody">
	<button class="eeSFL_ModalClose">&times;</button>
	<div id="eeSFL_AlertMessage"></div>
</div>
</div>


<div class="eeSFL_Modal" id="eeSFL_ConfirmModal">
<div class="eeSFL_ModalBackground"></div>
<div class="eeSFL_ModalBody eeSFL_AlertModalBody">
	<div id="eeSFL_ConfirmMessage"></div>
	<div class="eSFL_ConfirmInputs">
		<button id="eeSFL_ConfirmYes">Yes</button>
		<button id="eeSFL_ConfirmNo">No</button>
	</div>
</div>
</div>
';
	
?>