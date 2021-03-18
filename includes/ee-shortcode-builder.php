<?php // Simple File List Script: ee-shortcode-builder.php | Author: Mitchell Bennis | support@simplefilelist.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeInclude' ) ) exit('ERROR 98'); // Exit if nonce fails

$eeSFL_FREE_Log['RunTime'][] = 'Loaded: ee-plugin-instructions';

// All the shortcodes we have
$eeSFL_ShortcodeArray = array(
	'ShowList' => array('showlist', __('File List', 'ee-simple-file-list') , 'YES|ADMIN|USER|NO'), 
	'AllowUploads' => array('allowuploads',  __('Uploader', 'ee-simple-file-list'), 'YES|ADMIN|USER|NO'),
	'ShowFileThumb' => array('showthumb', __('File Thumbnails', 'ee-simple-file-list'), 'YES|NO'), 
	'ShowFileDate' => array('showdate', __('File Date', 'ee-simple-file-list'), 'YES|NO'), 
	'ShowFileSize' => array('showsize', __('File Size', 'ee-simple-file-list'), 'YES|NO'), 
	'ShowHeader' => array('showheader', __('Table Header', 'ee-simple-file-list'), 'YES|NO'), 
	'ShowFileActions' => array('showactions', __('File Actions', 'ee-simple-file-list'), 'YES|NO'),
	'SortBy' => array('sortby', __('Sort By', 'ee-simple-file-list'), 'Name|Date|Size|Random'),
	'SortOrder' => array('sortorder', __('Sort Order', 'ee-simple-file-list'), 'Descending|Ascending')
);

$eeChecked = '';
$eeCheckmark = ' (' . __('Default', 'ee-simple-file-list') . ')';

$eeOutput .= '

<article class="eeSupp">

<article class="eeSupp">

	<a class="button eeRight" href="https://simplefilelist.com/shortcode/" target="_blank">' . __('Shortcode Documentation', 'ee-simple-file-list') . '</a>

	<h2>' . __('Create Shortcode', 'ee-simple-file-list') . '</h2>

	<p>' . __('Simply place this bit of shortcode in any post, page or widget that you would like the plugin to appear.', 'ee-simple-file-list') . ' ' . 
		__('Both the file list and uploader will be displayed using the plugin settings.', 'ee-simple-file-list') . ' ' . 
			__('Optionally, use the Shortcode Builder below to create a custom shortcode.', 'ee-simple-file-list') . '</p>

	<button id="eeCopytoClipboard" class="button eeButton eeRight">' . __('COPY', 'ee-simple-file-list') . '</button>

	<p><textarea id="eeSFL_ShortCode" rows="3" cols="64" ></textarea></p>
	
	<br class="eeClearFix" />

	<div id="eeShortcodeControls">

		<form id="eeCreatePostwithShortcode" action="' . $_SERVER['PHP_SELF'] . '" method="POST" >
	
			<input type="hidden" name="eeShortcode" value="" />
	
			<select name="eeCreatePostType">
				<option value="Page">' . __('Create Page with Shortcode', 'ee-simple-file-list') . '</option>
				<option value="Post">' . __('Create Post with Shortcode', 'ee-simple-file-list') . '</option>
			</select>
	
			<input type="submit" name="eeGo" value="' . __('Go', 'ee-simple-file-list') . '" class="button" />
		
		</form>
		
	</div>

</article>

<article class="eeSupp" id="eeShortcodeBuilder">

	<h3>' . __('Shortcode Builder', 'ee-simple-file-list') . '</h3>

	<p>' . __('Use this Shortcode Builder to create a customized shortcode that over-rides your default settings.', 'ee-simple-file-list') . '</p>

	<script>
	
		var eeSFL_DefaultSettings = new Object;
		
		';
		
		foreach($eeSFL_ShortcodeArray as $eeSetting => $eeSettingSet) { // Loop through shortcodes
			
			if(is_array($eeSettingSet)) {
				
				// Pass to javascript
				$eeOutput .= 'eeSFL_DefaultSettings["' . $eeSettingSet[0] . '"] = "' . $eeSFL_Settings[$eeSetting] . '"' . PHP_EOL;
			}
		}
	
	$eeOutput .= '
	
	</script>

	<form class="eeShortcodeBuilderTop">
	
		';
		
		$eeOutput .= '<label class="eeClearfix">' . __('Show File List', 'ee-simple-file-list') . '<br />
			<select name="eeSFL_1_ShowList" id="eeShortcodeBuilder_showlist" onchange="eeSFL_FREE_ShortcodeBuilder(\'showlist\', \'select\')">
				<option value="YES" ';
				if($eeSFL_Settings['ShowList'] == 'YES') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show to Everyone', 'ee-simple-file-list') . $eeChecked . '</option>
				
				<option value="USER" ';
				if($eeSFL_Settings['ShowList'] == 'ADMIN') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show Only to Logged-in Users', 'ee-simple-file-list') . $eeChecked . '</option>
				<option value="ADMIN" ';

				if($eeSFL_Settings['ShowList'] == 'USER') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show Only to Logged-in Administrators', 'ee-simple-file-list') . $eeChecked . '</option>
				<option value="NO" ';
				
				if($eeSFL_Settings['ShowList'] == 'NO') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Hide the List', 'ee-simple-file-list') . $eeChecked . '</option>
			</select>
		</label>
		
		
		<label class="eeClearfix">' . __('Show Uploader', 'ee-simple-file-list') . '<br />
			<select name="eeSFL_1_AllowUploads" id="eeShortcodeBuilder_allowuploads" onchange="eeSFL_FREE_ShortcodeBuilder(\'allowuploads\', \'select\')">
				<option value="YES" ';
				if($eeSFL_Settings['AllowUploads'] == 'YES') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show to Everyone', 'ee-simple-file-list') . $eeChecked . '</option>
				
				<option value="USER" ';
				if($eeSFL_Settings['AllowUploads'] == 'ADMIN') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show Only to Logged-in Users', 'ee-simple-file-list') . $eeChecked . '</option>
				<option value="ADMIN" ';

				if($eeSFL_Settings['AllowUploads'] == 'USER') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Show Only to Logged-in Administrators', 'ee-simple-file-list') . $eeChecked . '</option>
				<option value="NO" ';
				
				if($eeSFL_Settings['AllowUploads'] == 'NO') { $eeOutput .= ' selected="selected" '; $eeChecked = $eeCheckmark; } else { $eeChecked = FALSE; }
				$eeOutput .= '>' . __('Hide the Uploader', 'ee-simple-file-list') . $eeChecked . '</option>
			</select>
		</label>';
	
	$eeOutput .= '</form>
	
	<br class="eeClearFix" />

	<div class="eeShortcodeBuilderBottom">';	
	
	function eeSFL_FREE_ShortcodeOptionButton($eeSetting, $eeValue, $eeLabel) {

		$eeOutput = '
		
		<p onclick="eeSFL_FREE_ShortcodeBuilder(\'' . $eeSetting . '\', \'toggle\')" class="" id="' . $eeSetting . '">' . $eeLabel . "</p>
		
		";
		
		return $eeOutput;
	}
	
	foreach($eeSFL_ShortcodeArray as $eeSetting => $eeSettingSet) { // Loop through shortcodes
			
		if(is_array($eeSettingSet) AND $eeSettingSet[2] == 'YES|NO') {
			
			if($eeSFL_Settings[$eeSetting]) {
			
				$eeSettingNewValue = ($eeSFL_Settings[$eeSetting] == 'YES' ? 'NO' : 'YES'); // Alternate
				if($eeSFL_Settings[$eeSetting] == 'YES') { $eeSettingShowValue = __('Hide', 'ee-simple-file-list'); } else { $eeSettingShowValue = __('Show', 'ee-simple-file-list'); }
				
				$eeOutput .= eeSFL_FREE_ShortcodeOptionButton($eeSettingSet[0], $eeSettingNewValue, '<b>' . $eeSettingShowValue . '</b>' . $eeSettingSet[1]);
				
			}
		}
	}
	
	$eeOutput .= '<br class="eeClearFix" />
	
	</div>';
	

$eeOutput .= '</article>

</article>';	
	
?>