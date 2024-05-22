<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');

$eeOutput .= '<!-- BEGIN SFL Modals -->

<span class="eeHide" id="eeSFL_Modal_FileID"></span>';

if( defined('eeSFL_Pro') AND $eeSFLE) { require_once(WP_PLUGIN_DIR . '/ee-simple-file-list-email/includes/ee-list-display-email-1.php'); } // PRO

if(is_admin() OR $eeSFL->eeListSettings['AllowFrontManage'] == 'YES') {

	$eeOutput .= '
	<span class="eeHide" id="eeSFL_EditNonce">' . wp_create_nonce(eeSFL_Nonce) . '</span>
	
	<div class="eeSFL_Modal" id="eeSFL_Modal_EditFile">
	<div class="eeSFL_ModalBackground"></div>
	<div class="eeSFL_ModalBody">
	
		<button class="eeSFL_ModalClose">&times;</button>
		
		<h1>' . __('Edit Item', 'ee-simple-file-list') . '</h1>
		
		<p class="eeSFL_ModalFilePath eeHide"></p>
		
		<p class="eeSFL_ModalFileDetails">' . 
		__('Added', 'ee-simple-file-list') . ': <span id="eeSFL_FileDateAdded" >???</span> | ' . 
		__('Changed', 'ee-simple-file-list') . ': <span id="eeSFL_FileDateChanged" >???</span> | ' . 
		__('Size', 'ee-simple-file-list') . ': <span id="eeSFL_FileSize">???</span>
		</p>
		
		<label for="eeSFL_FileNameNew">' . __('Item Name', 'ee-simple-file-list') . '</label>
		<input type="text" id="eeSFL_FileNameNew" name="eeSFL_FileNameNew" value="??" size="64" />
		<small class="eeSFL_ModalNote">' . __('Change the name.', 'ee-simple-file-list') . ' ' . __('Some characters are not allowed. These will be automatically replaced.', 'ee-simple-file-list') . '</small>';
			
		$eeOutput .= '<label for="eeSFL_FileNiceNameNew">' . __('File Nice Name', 'ee-simple-file-list') . '</label>
		<input type="text" id="eeSFL_FileNiceNameNew" name="eeSFL_FileNiceNameNew" value="" size="64" />
		<small class="eeSFL_ModalNote">' . __('Enter a name that will be shown in place of the real file name.', 'ee-simple-file-list') . ' ' . __('You may use special characters not allowed in the file name.', 'ee-simple-file-list') . '</small>';
		
		$eeOutput .= '<label for="eeSFL_FileDescriptionNew">' . __('Item Description', 'ee-simple-file-list') . '</label>
		<textarea cols="64" rows="3" id="eeSFL_FileDescriptionNew" name="eeSFL_FileDescriptionNew"></textarea>
		<small class="eeSFL_ModalNote">' . __('Add a description.', 'ee-simple-file-list') . ' ' . __('Use this field to describe this item and apply keywords for searching.', 'ee-simple-file-list') . '</small>
		
		<h4>' . __('Item Date Added', 'ee-simple-file-list') . '</h4>
		
		<div class="eeSFL_DateNew">
		<label>' . __('Year', 'ee-simple-file-list') . '<input min="1970" max="' . date('Y') . '" type="number" name="eeSFL_FileDateAddedYearNew" value="" id="eeSFL_FileDateAddedYearNew" /></label>
		<label>' . __('Month', 'ee-simple-file-list') . '<input min="1" max="12" type="number" name="eeSFL_FileDateAddedMonthNew" value="" id="eeSFL_FileDateAddedMonthNew" /></label>
		<label>' . __('Day', 'ee-simple-file-list') . '<input min="1" max="31" type="number" name="eeSFL_FileDateAddedDayNew" value="" id="eeSFL_FileDateAddedDayNew" /></label>
		</div>
		<small class="eeSFL_ModalNote">' . __('Change the date added to the list.', 'ee-simple-file-list') . '</small>
		
		<h4>' . __('Item Date Changed', 'ee-simple-file-list') . '</h4>
		
		<div class="eeSFL_DateNew">
		<label>' . __('Year', 'ee-simple-file-list') . '<input min="1970" max="' . date('Y') . '" type="number" name="eeSFL_FileDateChangedYearNew" value="" id="eeSFL_FileDateChangedYearNew" /></label>
		<label>' . __('Month', 'ee-simple-file-list') . '<input min="1" max="12" type="number" name="eeSFL_FileDateChangedMonthNew" value="" id="eeSFL_FileDateChangedMonthNew" /></label>
		<label>' . __('Day', 'ee-simple-file-list') . '<input min="1" max="31" type="number" name="eeSFL_FileDateChangedDayNew" value="" id="eeSFL_FileDateChangedDayNew" /></label>
		</div>
		<small class="eeSFL_ModalNote">' . __('Change date the file was last modified.', 'ee-simple-file-list') . '</small>
		
		<button class="button eeSFL_Action" data-id="0" data-action="edit-save">' . __('Save', 'ee-simple-file-list') . '</button>

	</div>
	</div>';
	
	if( defined('eeSFL_Pro') ) { require_once(eeSFL_PluginDir . 'pro/ee-list-display-move-modal.php'); } // PRO
	
	if( defined('eeSFL_Pro') AND $eeSFLA ) { require_once(WP_PLUGIN_DIR . '/' . eeSFLA_PluginSlug . '/includes/ee-list-display-access-2.php'); } // ACCESS
}

$eeOutput .= '

<!-- End SFL Modals -->';

?>