<?php // Simple File List Script: ee-plugin-author.php | Author: Mitchell Bennis | support@simplefilelist.com
	
defined( 'ABSPATH' ) or die( 'No direct access is allowed' );
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_BASE->eeLog[eeSFL_BASE_Go]['notice'][] = 'Loaded: ee-plugin-author';
	
// Plugin Contributors Array - Format: Name|URL|DESCRIPTION Example: Thnaks to <a href="URL">NAME</a> DESCRIPTION
// Values here are inserted below
$eeContributors = array('dmhendricks|https://github.com/dmhendricks/file-icon-vectors| for the awesome file type icons');  // else it's FALSE;

// $eePageSlug = $_GET['page'];
	
// The Content
$eeOutput .= '<section class="eeSFL_Settings">

<div class="eeSettingsTile">

<article>

	<img class="eeFloatRight" src="' . $eeSFL_BASE->eeEnvironment['pluginURL'] . 'images/Mitchell-Bennis-Head-Shot.jpg" alt="Mitchell Bennis" />
	
	<h1>' . __('Thank You', 'ee-simple-file-list') . '</h1>

	<p>' . __('Thank you for using my plugin. I am proud of this work and am committed to supporting it.', 'ee-simple-file-list') . ' ' . __('The goal is to keep it simple, yet make it do what you need it to do.', 'ee-simple-file-list') . ' ' . __('Tell me about the features that you want.', 'ee-simple-file-list') . ' </p>

	
	<p><a href="http://mitchellbennis.com/" target="_blank">Mitchell Bennis</a><br />
	Cokato, Minnesota, USA</p>'; // That's me!
		
		$eeOutput .= '<p>' . __('Contact Me', 'ee-simple-file-list') . ': <a href="https://simplefilelist.com/support/">' . __('Feedback or Questions', 'ee-simple-file-list') . '</a></p>';
	
	if(is_array($eeContributors)) {
		
		$eeOutput .= '<hr />
		
		<h3>' . __('Contributors', 'ee-simple-file-list') . '</h3>
		
		<p>';
		
		// Contributors Output
		foreach( $eeContributors as $eeValue){
			
			$eeArray = explode('|', $eeValue);
			$eeOutput .= __('Thanks to', 'ee-simple-file-list') . ' <a href="' . $eeArray[1] . '" target="_blank">' . @$eeArray[0] . ' </a>' . @$eeArray[2] . '<br />';
		}
		
		$eeOutput .= '</p>';
	}
		
	$eeOutput .= '</article>
	
	</div>
	
	</section>';

?>