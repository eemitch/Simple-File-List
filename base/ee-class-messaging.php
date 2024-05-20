<?php  
// Simple File List Pro - Copyright 2024 - See /license.txt
// Author: Mitchell Bennis | support@simplefilelist.com | https://simplefilelist.com
// Modifications to this code will not be supported. Get SFL Tweak Instead.
	
if(!defined('ABSPATH')) exit('<p>This is an <a href="https://simplefilelist.com">SFL</a> file.</p>');
if(!wp_verify_nonce( $eeSFL_Include, eeSFL_Include)) eeSFL_NonceError(__FILE__, 'Include');
	
class eeSFL_Messaging {
	
	protected $eeSFL;
	public function __construct(eeSFL_MainClass $eeSFL) { $this->eeSFL = $eeSFL; }
	// Usage: $this->eeSFL->eeListID
		
	
	
	
	// Display Messaging
	
	public function eeSFL_ResultsNotification() { // Display the results of an operation; success, warning or failure.
		
		$eeOutput = '';
		$eeLogParts = array('errors' => 'error', 'warnings' => 'warning', 'messages' => 'success');
		
		foreach($eeLogParts as $eePart => $eeType) {
			
			if(!empty($this->eeSFL->eeLog[$eePart])) {
			
				$eeOutput .= '<div class="';
				
				if( is_admin() ) {
					$eeOutput .=  'notice notice-' . $eeType . ' is-dismissible';
				} else {
					$eeOutput .= 'eeSFL_ResultsNotification eeSFL_ResultsNotification_' . $eePart;
				}
				
				$eeOutput .= '">
				<ul>';
				
				foreach($this->eeSFL->eeLog[$eePart] as $eeValue) { // We can go two-deep arrays
					
					if(is_array($eeValue)) {
						foreach ($eeValue as $eeValue2) {
							$eeOutput .= '
							<li>' . $eeValue2 . '</li>' . PHP_EOL;
						}
					} else {
						$eeOutput .= '
						<li>' . $eeValue . '</li>' . PHP_EOL;
					}
				}
				$eeOutput .= '
				</ul>
				</div>';
				
				$this->eeSFL->eeLog[$eePart] = array(); // Clear this part for the array
			}
		}
		
		return $eeOutput;
	}







	// Email Notifications
	
	public $eeNotifyMessageDefault = 'Greetings,' . PHP_EOL . PHP_EOL . 
		'You should know that a file has been uploaded to your website.' . PHP_EOL . PHP_EOL . 
			
			'[file-list]' . PHP_EOL . PHP_EOL . 
			
			'File List: [web-page]' . PHP_EOL . PHP_EOL;
	
	
	// Send the upload notification email
	public function eeSFL_UploadEmail($eeSFL_UploadJob) {
		
		global $eeSFLA;
		
		$this->eeSFL->eeLog['notice'][] = $this->eeSFL->eeSFL_NOW() . 'Class Method Called: eeSFL_UploadEmail()';
		
		$eeAdminEmail = $this->eeSFL->eeListSettings['NotifyTo'];
		
		if($eeSFL_UploadJob) {
			
			// Build the Message Body
			$eeSFL_Body = sanitize_text_field($this->eeSFL->eeListSettings['NotifyMessage']); // Get the template
			
			 // Add files
			$eeSFL_Body = str_replace('[file-list]', $eeSFL_UploadJob, $eeSFL_Body);
			$eeSFL_Body = str_replace('[web-page]', get_permalink(), $eeSFL_Body); // Add location
			
			// Get Form Input?
			if(isset($_POST['eeSFL_Email'])) {
				
				$eeSFL_Body .= PHP_EOL . PHP_EOL . __('Uploader Information', 'ee-simple-file-list') . PHP_EOL;
				
				if(isset($_POST['eeSFL_Name'])) {
					$eeSFL_Name = substr(sanitize_text_field($_POST['eeSFL_Name']), 0, 64);
					$eeSFL_Name = esc_textarea(strip_tags($eeSFL_Name));
						} else { $eeSFL_Name = eeSFL_PluginName; }
				$eeSFL_Body .= __('Uploaded By', 'ee-simple-file-list') . ': ' . ucwords($eeSFL_Name) . " - ";
				
				if(isset($_POST['eeSFL_Email'])) {
					$eeSFL_Email = filter_var(sanitize_email($_POST['eeSFL_Email']), FILTER_VALIDATE_EMAIL);
				} else {
					$eeSFL_Email = get_option('admin_email');
				}
				
				$eeSFL_Body .= strtolower($eeSFL_Email) . PHP_EOL;
				$eeSFL_ReplyTo = $eeSFL_Name . ' <' . $eeSFL_Email . '>';
				
				$eeSFL_Comments = esc_textarea(substr(sanitize_text_field(strip_tags($_POST['eeSFL_Comments'])), 0, 5012));
				if($eeSFL_Comments) { $eeSFL_Body .= PHP_EOL . $eeSFL_Comments . PHP_EOL . PHP_EOL; }
			}
		
			if($this->eeSFL->eeListSettings['NotifyFrom']) {
				$eeSFL_NotifyFrom = esc_textarea($this->eeSFL->eeListSettings['NotifyFrom']);
			} else {
				$eeSFL_NotifyFrom = get_option('admin_email');
			}
			
			if($this->eeSFL->eeListSettings['NotifyFromName']) {
				$eeSFL_AdminName = esc_textarea($this->eeSFL->eeListSettings['NotifyFromName']);
			} else {
				$eeSFL_AdminName = $this->eePluginName;
			}
			
			if($this->eeSFL->eeListSettings['NotifySubject']) {
				$eeSFL_Subject = esc_textarea($this->eeSFL->eeListSettings['NotifySubject']);
			} else {
				$eeSFL_Subject = __('File Upload Notice', 'ee-simple-file-list');
			}
			
			$eeSFL_Headers = "From: " . esc_textarea( $this->eeSFL->eeListSettings['NotifyFromName'] ) . " <$eeSFL_NotifyFrom>" . PHP_EOL . 
				"Return-Path: $eeSFL_NotifyFrom" . PHP_EOL . "Reply-To: $eeSFL_NotifyFrom";
			
			$eeSFL_HeadersCC = '';
			
			if($this->eeSFL->eeListSettings['NotifyCc']) {
				$eeSFL_HeadersCC .= PHP_EOL . "CC:" . esc_textarea($this->eeSFL->eeListSettings['NotifyCc']);
			}
				
			if($this->eeSFL->eeListSettings['NotifyBcc']) {
				$eeSFL_HeadersCC .= PHP_EOL . "BCC:" . esc_textarea($this->eeSFL->eeListSettings['NotifyBcc']);
				
				
				if($eeSFLA) {
					if($eeSFLA->eeSFLA_Settings['BCC'] == 'YES' AND $this->eeListID > 1) { // Append if needed
						$eeSFL_HeadersCC .= ',' . $eeAdminEmail;
					}
				}
				
				
			}
			
			if($eeSFLA) {
				if($eeSFLA->eeSFLA_Settings['BCC'] == 'YES') {
					$eeMainListSettings = get_option('eeSFL_Settings_1');
					if($this->eeSFL->eeListSettings['NotifyBcc']) {
						$eeSFL_HeadersCC .= ',' . $eeMainListSettings['NotifyTo']; // Add to existing BCC
					} else {
						$eeSFL_HeadersCC .= PHP_EOL . "BCC:" . $eeMainListSettings['NotifyTo']; // Set as BCC
					}
				}
			}
			
			
			// Must be an array
			if(is_array($this->eeSFL->eeListSettings['NotifyTo'])) { $eeTo = $this->eeSFL->eeListSettings['NotifyTo']; } 
				else { $eeTo = array($this->eeSFL->eeListSettings['NotifyTo']); }
			
			
			// eeSFLA
			if( !strpos($eeTo[0], '@') ) { // Not an actual email address
				
					if($eeSFLA) {
					
					$eeArray = array();
					
					if( $eeTo[0] == 'All' ) { // Send to all list users
						
						$eeUsers = $eeSFLA->eeSFLA_GetMinRoleUsers($this->eeSFL->eeListSettings['ListRole'], FALSE, $this->eeSFL->eeListSettings['ListMatchMode']);
						
						foreach( $eeUsers as $eeKey => $eeUser) { // Build array of addresses
							
							$eeArray[] = $eeUser['Email'];
						}
						
						$eeTo = $eeArray;
					
					} elseif( is_numeric($eeTo[0]) ) {
						
						foreach( $eeTo as $eeKey => $eeID ){
							
							$eeUserInfo = get_userdata($eeID);
							
							$eeArray[] = $eeUserInfo->user_email;
						}
						
						$eeTo = $eeArray;
						
					} else {
						
						$eeTo = array($eeAdminEmail); // Send to Admin
					}
				}
			}
			
			
			
			
			
			$eeTo = array_unique($eeTo); // Remove duplicates
			
			foreach( $eeTo as $eeKey => $eeThisTo) {
			
				if( strpos($eeThisTo, '@') ) {
					
					if( wp_mail($eeThisTo, $eeSFL_Subject, $eeSFL_Body, $eeSFL_Headers . $eeSFL_HeadersCC) ) { // SEND IT
						
						$eeSFL_HeadersCC = '';
						$eeSent = TRUE;
						
						$this->eeSFL->eeLog['notice'][] = 'Notification Email SENT';
						
					} else {
						
						$this->eeSFL->eeLog['errors'][] = 'Notification Email FAILED';
						$eeSent = FALSE;
					}
				}
			}
			
			if($eeSent) {
				return 'SUCCESS';
			}
		}
	}

}