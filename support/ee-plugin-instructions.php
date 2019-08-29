<?php // Plugin Shortcode Builer with Links to Documentation
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit; // Exit if nonce fails

$eeSFL_Log[] = 'Loaded: ee-plugin-instructions';

// All the shortcodes we have
$eeSFL_ShortcodeArray = array(
	'ShowList' => array('showlist', 'File List', 'YES|ADMIN|USER|NO'), 
	'AllowUploads' => array('allowuploads',  'Uploader', 'YES|ADMIN|USER|NO'),
	'ShowThumb' => array('showthumb', 'File Thumbnails', 'YES|NO'), 
	'ShowFileDate' => array('showdate', 'File Date', 'YES|NO'), 
	'ShowFileSize' => array('showsize', 'File Size', 'YES|NO'), 
	'ShowListHeader' => array('showheader', 'Table Header', 'YES|NO'), 
	'ShowFileActions' => array('showactions', 'File Actions', 'YES|NO')
);

if($eeSFLF) { 
	$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
	include(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_ShortcodeBuilder.php');
	$eeSFL_ShortcodeAttributes[] = 'showfolder';
}

$eeChecked = '';
$eeCheckmark = '<small> (' . __('Default', 'ee-simple-file-list') . ')</small>';

$eeOutput .= '<article class="eeSupp">

<article class="eeSupp">



<h2>' . __('Shortcode Usage', 'ee-simple-file-list') . '</h2>

<p class="eeNote">' . __('Simply place this bit of shortcode in any post, page or widget that you would like the plugin to appear.', 'ee-simple-file-list') . 
	' ' . 
		__('By default, both the file list and uploader will be displayed. To change this use Shortcode Builder below or refer to the documentation', 'ee-simple-file-list') . '</p>

<p><textarea id="eeSFL_ShortCode" rows="3" cols="64" /></textarea></p>

<div id="eeShortcodeControls">

<form id="eeCreatePostwithShortcode" action="' . $_SERVER['PHP_SELF'] . '" method="POST" >
	
	<input type="hidden" name="eeShortcode" value="" />
	
	<p><select name="eeCreatePostType">
		<option value="Page">' . __('Create Page with Shortcode', 'ee-simple-file-list') . '</option>
		<option value="Post">' . __('Create Post with Shortcode', 'ee-simple-file-list') . '</option>
	</select>
	
	<input type="submit" name="eeGo" value="Go" /></p>
</form>

<button id="eeCopytoClipboard" class="button eeRight">' . __('COPY', 'ee-simple-file-list') . '</button>

<br class="eeClearFix" />
</div>

<h3>' . __('Shortcode Builder', 'ee-simple-file-list') . '</h3>

<p>' . __('Use the Shortcode Builder to create a customized list.', 'ee-simple-file-list') . '</p>

<article id="eeShortcodeBuilder">

	<script>
	
		var eeSFL_DefaultSettings = new Object;
		
		';
		
		foreach($eeSFL_ShortcodeArray as $eeSetting => $eeSettingSet) { // Loop through shortcodes
			
			if(is_array($eeSettingSet)) {
				
				// Get current setting
				$eeSettingString = 'eeSFL_' . $eeSetting;
				$eeSettingCurrentValue = ${'eeSFL_' . $eeSetting};
				
				if($eeSettingString AND $eeSettingCurrentValue) {
				
					// Pass to javascript
					$eeOutput .= 'eeSFL_DefaultSettings["' . $eeSettingSet[0] . '"] = "' . $eeSettingCurrentValue . '"' . PHP_EOL;
				}
			}
		}
	
	$eeOutput .= '
	
	</script>

	<form class="eeShortcodeBuilderTop">
	
		';
		
		if(function_exists('eeSFLF_FolderSelect')) { $eeOutput .= eeSFLF_FolderSelect($eeSFL_FileListDir); }
		
		$eeOutput .= '<label class="eeClearfix">' . __('Show File List', 'ee-simple-file-list') . '<br />
			<select name="eeSFL_' . $eeSFL->eeListID . '_ShowList" id="eeShortcodeBuilder_showlist" onchange="eeShortcodeBuilder(\'showlist\', \'select\')">
				<option value="YES" ';
				if($eeSFL_ShowList == 'YES') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show to Everyone', 'ee-simple-file-list') . $eeChecked . '</option>
				
				<option value="USER" ';
				if($eeSFL_ShowList == 'ADMIN') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show Only to Logged-in Users', 'ee-simple-file-list') . $eeChecked . '</option>
				<option value="ADMIN" ';

				if($eeSFL_ShowList == 'USER') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show Only to Logged-in Administrators', 'ee-simple-file-list') . $eeChecked . '</option>
				<option value="NO" ';
				
				if($eeSFL_ShowList == 'NO') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Hide the List', 'ee-simple-file-list') . $eeChecked . '</option>
			</select>
		</label>
		
		
		<label class="eeClearfix">' . __('Show Uploader', 'ee-simple-file-list') . '<br />
			<select name="eeSFL_' . $eeSFL->eeListID . '_AllowUploads" id="eeShortcodeBuilder_allowuploads" onchange="eeShortcodeBuilder(\'allowuploads\', \'select\')">
				<option value="YES" ';
				if($eeSFL_AllowUploads == 'YES') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show to Everyone', 'ee-simple-file-list') . $eeChecked . '</option>
				
				<option value="USER" ';
				if($eeSFL_AllowUploads == 'ADMIN') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show Only to Logged-in Users', 'ee-simple-file-list') . $eeChecked . '</option>
				<option value="ADMIN" ';

				if($eeSFL_AllowUploads == 'USER') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show Only to Logged-in Administrators', 'ee-simple-file-list') . $eeChecked . '</option>
				<option value="NO" ';
				
				if($eeSFL_AllowUploads == 'NO') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Hide the Uploader', 'ee-simple-file-list') . $eeChecked . '</option>
			</select>
		</label>';
	
	$eeOutput .= '</form>
	
	<br class="eeClearFix" />

	<div class="eeShortcodeBuilderBottom">';
	
	
	function eeSFL_ShortcodeOptionButton($eeSetting, $eeValue, $eeLabel) {

		$eeOutput = '
		
		<p onclick="eeShortcodeBuilder(\'' . $eeSetting . '\', \'toggle\')" class="" id="' . $eeSetting . '">' . $eeLabel . "</p>
		
		";
		
		return $eeOutput;
	}
	
	foreach($eeSFL_ShortcodeArray as $eeSetting => $eeSettingSet) { // Loop through shortcodes
			
		if(is_array($eeSettingSet) AND $eeSettingSet[2] == 'YES|NO') {
			
			$eeSettingString = 'eeSFL_' . $eeSetting;
			$eeSettingCurrentValue = ${'eeSFL_' . $eeSetting};
			
			if($eeSettingString AND $eeSettingCurrentValue) {
			
				$eeSettingNewValue = ($eeSettingCurrentValue == 'YES' ? 'NO' : 'YES'); // Alternate
				if($eeSettingCurrentValue == 'YES') { $eeSettingShowValue ='Hide'; } else { $eeSettingShowValue ='Show'; }
				
				$eeOutput .= eeSFL_ShortcodeOptionButton($eeSettingSet[0], $eeSettingNewValue, '<b>' . $eeSettingShowValue . '</b>' . $eeSettingSet[1]);
				
			}
		}
	}
	
	$eeOutput .= '<br class="eeClearFix" />
	
	</div>';
	

$eeOutput .= '</article>

<p class="eeRight"><strong><a href="https://simplefilelist.com/shortcode/" target="_blank" >' . __('Shortcode Documentation', 'ee-simple-file-list') . '</a></strong></p>

';

$eeOutput .= '<br class="eeClearFix" />

</article>
<article class="eeSupp">

<a href="?page=' . $eeSFL_Page . '&tab=list_settings&subtab=list_settings" class="button eeRight">' . __('Go to File List Settings', 'ee-simple-file-list') . '</a>

<h2>' . __('File List Settings', 'ee-simple-file-list') . '</h2>

<p><strong><a href="https://simplefilelist.com/file-list-settings/" target="_blank" >' . __('List Settings Instructions', 'ee-simple-file-list') . '</a></strong></p>';

	
// Uploader Instructions

$eeOutput .= '</article>
<article class="eeSupp">

<a href="?page=' . $eeSFL_Page . '&tab=list_settings&subtab=uploader_settings" class="button eeRight">' . __('Go to Uploader Settings', 'ee-simple-file-list') . '</a>

<h2>' . __('Uploader Settings', 'ee-simple-file-list') . '</h2> 

<p><strong><a href="https://simplefilelist.com/upload-settings/" target="_blank" >' . __('Uploader Settings Instructions', 'ee-simple-file-list') . '</a></strong></p>';

$eeOutput .= '</article>

<article class="eeSupp">

<h2>' . __('How to Customize the Look', 'ee-simple-file-list') . '</h2>

<p>' . __('Simple File List styles can be over-written in your theme\'s CSS to achieve the look you need.', 'ee-simple-file-list') . ' '  . __('Please refer to the CSS styling documentation here:', 'ee-simple-file-list') . '</p>

<p><strong><a href="https://simplefilelist.com/how-to-style-the-list-and-uploader-appearance/" target="_blank" >CSS ' . __('Documentation', 'ee-simple-file-list') . '</a></strong></p>

</article>

</article>';	
	
?>